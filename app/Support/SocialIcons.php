<?php

namespace App\Support;

class SocialIcons
{
    public static function getIconClass(string $iconPreset): string
    {
        $map = [
            'facebook'  => 'fa-brands fa-facebook-f',
            'instagram' => 'fa-brands fa-instagram',
            'twitter'   => 'fa-brands fa-x-twitter',
            'threads'   => 'fa-brands fa-threads',
            'youtube'   => 'fa-brands fa-youtube',
            'tiktok'    => 'fa-brands fa-tiktok',
            'email'     => 'fa-solid fa-envelope',
            'telegram'  => 'fa-brands fa-telegram',
            'whatsapp'  => 'fa-brands fa-whatsapp',
            'linkedin'  => 'fa-brands fa-linkedin-in',
            'github'    => 'fa-brands fa-github',
            'pinterest' => 'fa-brands fa-pinterest',
            'website'   => 'fa-solid fa-globe',
        ];

        return $map[$iconPreset] ?? 'fa-solid fa-circle';
    }

    public static function getIconColor(string $iconPreset): string
    {
        $colors = [
            'facebook'  => '#1877F2',
            'instagram' => '#E1306C',
            'twitter'   => '#1DA1F2',
            'threads'   => '#000000',
            'youtube'   => '#FF0000',
            'tiktok'    => '#000000',
            'email'     => '#EA4335',
            'telegram'  => '#24A1DE',
            'whatsapp'  => '#25D366',
            'linkedin'  => '#0A66C2',
            'github'    => '#181717',
            'pinterest' => '#BD081C',
            'website'   => '#4B5563',
        ];

        return $colors[$iconPreset] ?? '#6B7280';
    }
}
