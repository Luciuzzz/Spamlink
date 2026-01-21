<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('landing_sections', function (Blueprint $table) {
            $table->id();

            // Relación con usuarios
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Slug, título, descripción
            $table->string('slug'); // ej. 'multimedia'
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // Estado y datos
            $table->boolean('is_active')->default(true);
            $table->json('data')->nullable(); // para bloques, imágenes, videos

            $table->timestamps();

            // Índice único compuesto: un mismo usuario no puede tener dos slugs iguales
            $table->unique(['user_id', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_sections');
    }
};
