<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Idempotente: no fija el id (para no desincronizar la secuencia de
        // PostgreSQL) y no falla si el admin ya existe en reinicios.
        User::firstOrCreate(
            ['email' => 'admin@correo.com'],
            [
                'name'              => 'Super Admin',
                'username'          => 'superadmin',
                'email_verified_at' => Carbon::now(),
                'password'          => Hash::make('qwerty'), // Cambia esto
                'wizard_completed'  => true,
                'role'              => 'superadmin',
                'remember_token'    => null,
            ]
        );

        // PostgreSQL: resincronizar la secuencia del id con el MAX(id) actual.
        // Necesario porque versiones previas sembraban el admin con id fijo, lo
        // que dejaba la secuencia sin avanzar y hacía que el primer registro
        // nuevo chocara con "users_pkey". Se ejecuta siempre (tambien repara
        // bases ya desincronizadas) y solo aplica al driver pgsql.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement(
                "SELECT setval(pg_get_serial_sequence('users', 'id'), COALESCE((SELECT MAX(id) FROM users), 1))"
            );
        }
    }
}
