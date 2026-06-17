#!/usr/bin/env bash
set -e

cd /var/www/html

# 1) Asegurar archivo .env (en cloud se usan variables de entorno reales)
if [ ! -f .env ]; then
    echo "[entrypoint] No existe .env, copiando desde .env.example"
    cp .env.example .env
fi

# 2) Generar APP_KEY si no está definida
if ! grep -q "^APP_KEY=base64" .env 2>/dev/null && [ -z "${APP_KEY}" ]; then
    echo "[entrypoint] Generando APP_KEY"
    php artisan key:generate --force
fi

# 3) Base de datos SQLite por defecto (si no se configura otra).
#    Respeta DB_DATABASE si viene definida (p.ej. en docker-compose para
#    persistir la BD en un volumen); si no, usa database/database.sqlite.
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
    DB_FILE="${DB_DATABASE:-database/database.sqlite}"
    DB_DIR="$(dirname "$DB_FILE")"
    if [ ! -f "$DB_FILE" ]; then
        echo "[entrypoint] Creando base de datos SQLite en $DB_FILE"
        mkdir -p "$DB_DIR"
        touch "$DB_FILE"
    fi
    # SQLite necesita escribir el archivo Y su directorio (journal/WAL)
    chown -R www-data:www-data "$DB_DIR" || true
    chmod -R 775 "$DB_DIR" || true
fi

# 4) Enlace simbólico de storage
php artisan storage:link || true

# 4b) Si la BD es MySQL/Postgres, esperar a que el servidor esté disponible
if [ "${DB_CONNECTION:-sqlite}" = "mysql" ] || [ "${DB_CONNECTION}" = "pgsql" ]; then
    echo "[entrypoint] Esperando a la base de datos (${DB_CONNECTION} en ${DB_HOST}:${DB_PORT})..."
    tries=0
    until php -r '
        $dsn = getenv("DB_CONNECTION").":host=".getenv("DB_HOST").";port=".(getenv("DB_PORT") ?: 3306).";dbname=".getenv("DB_DATABASE");
        try { new PDO($dsn, getenv("DB_USERNAME"), getenv("DB_PASSWORD")); exit(0); }
        catch (Throwable $e) { exit(1); }
    ' 2>/dev/null; do
        tries=$((tries + 1))
        if [ "$tries" -ge 30 ]; then
            echo "[entrypoint] La base de datos no respondió tras 60s, se continúa de todos modos."
            break
        fi
        sleep 2
    done
    echo "[entrypoint] Base de datos disponible."
fi

# 5) Migraciones (no aborta el arranque si fallan)
echo "[entrypoint] Ejecutando migraciones"
php artisan migrate --force || true

# 5b) Usuario admin inicial (admin@correo.com / qwerty)
#     El seeder no es idempotente: en reinicios falla por duplicado y se ignora.
echo "[entrypoint] Sembrando usuario admin inicial"
php artisan db:seed --class=UserSeeder --force || true

# 6) Cache de rutas y vistas para producción
#    NOTA: no se usa config:cache porque config/wizard.php contiene closures
#    no serializables. Limpiamos la config por si quedó cache previa.
php artisan config:clear || true
php artisan route:cache || true
php artisan view:cache || true

# 7) Permisos finales
chown -R www-data:www-data storage bootstrap/cache database || true

echo "[entrypoint] Iniciando: $*"
exec "$@"
