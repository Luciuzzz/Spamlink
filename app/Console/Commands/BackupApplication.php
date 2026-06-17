<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class BackupApplication extends Command
{
    protected $signature = 'app:backup {--no-prune : Do not delete backups older than the configured retention window}';

    protected $description = 'Back up the database and application logs';

    public function handle(): int
    {
        $backupRoot = rtrim((string) config('backup.path'), DIRECTORY_SEPARATOR);
        $timestamp = now()->format('Y-m-d_His');
        $backupDir = $backupRoot.DIRECTORY_SEPARATOR.$timestamp;

        File::ensureDirectoryExists($backupDir);

        $manifest = [
            'created_at' => now()->toIso8601String(),
            'app_name' => config('app.name'),
            'app_env' => config('app.env'),
            'database_connection' => config('database.default'),
            'includes' => [
                'database' => false,
                'logs' => false,
            ],
        ];

        if ((bool) config('backup.include_database')) {
            $this->backupDatabase($backupDir);
            $manifest['includes']['database'] = true;
        }

        if ((bool) config('backup.include_logs')) {
            $this->backupLogs($backupDir);
            $manifest['includes']['logs'] = true;
        }

        File::put(
            $backupDir.DIRECTORY_SEPARATOR.'manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL
        );

        if (! $this->option('no-prune')) {
            $this->pruneOldBackups($backupRoot);
        }

        $this->info('Backup creado en: '.$backupDir);

        return self::SUCCESS;
    }

    protected function backupDatabase(string $backupDir): void
    {
        $connectionName = config('database.default');
        $driver = config("database.connections.$connectionName.driver");

        if ($driver === 'sqlite') {
            $this->backupSqliteDatabase($backupDir, $connectionName);

            return;
        }

        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            throw new \RuntimeException("No se puede respaldar la base de datos para el driver [$driver].");
        }

        $dumpFile = $backupDir.DIRECTORY_SEPARATOR.'database.sql';
        $database = (string) config("database.connections.$connectionName.database");
        $host = (string) config("database.connections.$connectionName.host");
        $port = (string) config("database.connections.$connectionName.port");
        $user = (string) config("database.connections.$connectionName.username");
        $password = (string) config("database.connections.$connectionName.password");
        $binary = $this->resolveDatabaseDumpBinary();

        $process = new Process([
            $binary,
            '--single-transaction',
            '--quick',
            '--routines',
            '--triggers',
            '--events',
            '--host='.$host,
            '--port='.$port,
            '--user='.$user,
            '--password='.$password,
            '--result-file='.$dumpFile,
            $database,
        ]);

        $process->setTimeout(null);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw new \RuntimeException(
                'No se pudo crear el backup de la base de datos. Revisa que '.$binary.' esté instalado y que las credenciales sean correctas.',
                previous: $e
            );
        }
    }

    protected function backupSqliteDatabase(string $backupDir, string $connectionName): void
    {
        $databasePath = (string) config("database.connections.$connectionName.database");

        if (! File::exists($databasePath)) {
            throw new \RuntimeException("No se encontró el archivo SQLite en [$databasePath].");
        }

        File::copy($databasePath, $backupDir.DIRECTORY_SEPARATOR.'database.sqlite');
    }

    protected function backupLogs(string $backupDir): void
    {
        $logsPath = rtrim((string) config('backup.logs_path'), DIRECTORY_SEPARATOR);
        $targetDir = $backupDir.DIRECTORY_SEPARATOR.'logs';

        File::ensureDirectoryExists($targetDir);

        $files = File::glob($logsPath.DIRECTORY_SEPARATOR.'*.log*');

        foreach ($files as $file) {
            if (is_file($file)) {
                File::copy($file, $targetDir.DIRECTORY_SEPARATOR.basename($file));
            }
        }
    }

    protected function pruneOldBackups(string $backupRoot): void
    {
        $keepDays = (int) config('backup.keep_days');

        if ($keepDays < 1 || ! is_dir($backupRoot)) {
            return;
        }

        $threshold = now()->subDays($keepDays)->getTimestamp();

        foreach (File::directories($backupRoot) as $directory) {
            if (File::lastModified($directory) < $threshold) {
                File::deleteDirectory($directory);
            }
        }
    }

    protected function resolveDatabaseDumpBinary(): string
    {
        foreach (['mysqldump', 'mariadb-dump'] as $binary) {
            $process = new Process(['sh', '-lc', 'command -v '.escapeshellarg($binary)]);
            $process->run();

            if ($process->isSuccessful() && Str::of($process->getOutput())->trim()->isNotEmpty()) {
                return $binary;
            }
        }

        throw new \RuntimeException('No se encontró `mysqldump` ni `mariadb-dump` en el sistema.');
    }
}
