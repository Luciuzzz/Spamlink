<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->float('panel_bg_opacity')->default(0.94)->after('panel_bg_color');
            $table->string('panel_text_color')->nullable()->after('panel_bg_opacity');
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['panel_bg_opacity', 'panel_text_color']);
        });
    }
};
