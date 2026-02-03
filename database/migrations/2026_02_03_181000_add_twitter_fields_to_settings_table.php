<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('twitter_title')->nullable()->after('meta_image_path');
            $table->text('twitter_description')->nullable()->after('twitter_title');
            $table->string('twitter_image_path')->nullable()->after('twitter_description');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'twitter_title',
                'twitter_description',
                'twitter_image_path',
            ]);
        });
    }
};
