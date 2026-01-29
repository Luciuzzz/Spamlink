<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Si el onboarding está activo o no
            $table->boolean('onboarding_active')
                  ->default(true)
                  ->after('remember_token');

            // Paso actual (ej: settings, products, sales, reports)
            $table->string('onboarding_step')
                  ->nullable()
                  ->after('onboarding_active');

            // Pasos completados
            $table->json('onboarding_completed')
                  ->nullable()
                  ->after('onboarding_step');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_active',
                'onboarding_step',
                'onboarding_completed',
            ]);
        });
    }
};
