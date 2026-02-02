<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'id'                => 1,
            'name'              => 'Super Admin',
            'email'             => 'admin@correo.com',
            'username'          => 'superadmin',
            'email_verified_at' => Carbon::now(),
            'password'          => Hash::make('qwerty'), // Cambia esto
            'wizard_completed'  => true,
            'role'              => 'superadmin', // O el valor que use tu sistema
            'remember_token'    => null,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
        ]);
    }
}
