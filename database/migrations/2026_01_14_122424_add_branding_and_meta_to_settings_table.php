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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('logo_path')->nullable()->after('company_name');
            $table->string('favicon_path')->nullable()->after('logo_path');

            $table->string('meta_title')->nullable()->after('favicon_path');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->string('meta_image_path')->nullable()->after('meta_keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'logo_path',
                'favicon_path',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'meta_image_path',
            ]);
        });
    }
};
