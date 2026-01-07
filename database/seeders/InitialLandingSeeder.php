<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SocialLink;

class InitialLandingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::query()->updateOrCreate(
            ['id' => 1],
            [
                'company_name' => 'Mi Empresa',
                'slogan' => 'La mejor empresa del mundo',
                'description' => 'Bienvenido a la página de inicio de Mi Empresa. Ofrecemos los mejores servicios para ti.',
                'bg_desktop_path' => 'defaults/landing/bg_desktop.jpg',
                'bg_mobile_path' => 'defaults/landing/bg_mobile.jpg',
                'whatsapp_number' => '+1234567890',
                'location_text' => '123 Calle Principal, Ciudad, País',
            ]
        );
        SocialLink::query()->updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Facebook',
                'url' => 'https://www.facebook.com/miempresa',
                'icon_path' => 'defaults/icons/facebook.png',
                'order' => 1,
                'is_active' => true,
            ]
        );
        SocialLink::query()->updateOrCreate(
            ['id' => 2],
            [
                'name' => 'Twitter',
                'url' => 'https://www.twitter.com/miempresa',
                'icon_path' => 'defaults/icons/twitter.png',
                'order' => 2,
                'is_active' => true,
            ]
        );
        SocialLink::query()->updateOrCreate(
            ['id' => 3],
            [
                'name' => 'Instagram',
                'url' => 'https://www.instagram.com/miempresa',
                'icon_path' => 'defaults/icons/instagram.png',
                'order' => 3,
                'is_active' => true,
            ]
        );
    }
}
