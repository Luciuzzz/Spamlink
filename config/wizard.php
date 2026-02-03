<?php

return [
    'dashboard_route' => 'filament.admin.pages.dashboard',

    'steps' => [
        1 => [
            'label' => 'Datos básicos y branding',
            'complete' => function ($user) {
                $settings = \App\Models\Setting::where('user_id', $user->id)->first();

                return $settings
                    && ($settings->company_name || $settings->description || $settings->slogan)
                    && ! empty($settings->logo_path);
            },
            'routes' => [
                'filament.admin.pages.wizard',
                'filament.admin.pages.my-settings', // Datos + branding
            ],
        ],

        2 => [
            'label' => 'Redes sociales',
            'complete' => function ($user) {
                return \App\Models\SocialLink::where('user_id', $user->id)
                    ->where('is_active', 1)
                    ->whereNotNull('name')
                    ->whereNotNull('url')
                    ->exists();
            },
            'routes' => [
                'filament.admin.pages.wizard',
                'filament.admin.resources.social-links.*', // CRUD social links
            ],
        ],

        3 => [
            'label' => 'Multimedia',
            'complete' => function ($user) {
                return \App\Models\LandingSection::where('user_id', $user->id)
                    ->where('is_active', 1)
                    ->whereNotNull('title')
                    ->whereNotNull('description')
                    ->exists();
            },
            'routes' => [
                'filament.admin.pages.wizard',
                'filament.admin.pages.multimedia', // Página multimedia
            ],
        ],
    ],
];
