<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $users = DB::table('users')->whereNull('username')->get();

        foreach ($users as $user) {
            // Generar username único a partir del nombre
            $base = Str::slug($user->name) ?: 'user';
            $username = $base;
            $i = 2;

            while (DB::table('users')->where('username', $username)->exists()) {
                $username = $base . '-' . $i;
                $i++;
            }

            DB::table('users')
                ->where('id', $user->id)
                ->update(['username' => $username]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->update(['username' => null]);
    }
};
