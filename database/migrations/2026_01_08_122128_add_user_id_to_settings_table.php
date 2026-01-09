<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // La columna user_id YA existe, así que no la agregamos.

        // Rellenar nulos (por si se ejecuta en otra máquina)
        DB::table('settings')->whereNull('user_id')->update(['user_id' => 1]);

        Schema::table('settings', function (Blueprint $table) {
            // Agregar la FK
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->cascadeOnDelete();
        });

        // Si querés forzar NOT NULL (opcional):
        // requiere doctrine/dbal o hacerlo con SQL manual.
        // Por ahora lo dejamos nullable para evitar fricción.
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropUnique(['user_id']);
            // NO dropeo la columna para no romper datos si ya está en uso
        });
    }
};
