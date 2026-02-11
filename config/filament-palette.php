<?php

use Filament\Support\Colors\Color;

return [
    'default' => env('FILAMENT_PALETTE_DEFAULT', 'sky'),

    'palette' => [
        'amber'   => [
            'primary' => Color::Amber,
            'warning' => Color::Yellow,
            'danger'  => Color::Red,
            'success' => Color::Lime,
            'info'    => Color::Blue,
        ],
        'slate'   => [
            'primary' => Color::Slate,
            'warning' => Color::Amber,
            'danger'  => Color::Red,
            'success' => Color::Emerald,
            'info'    => Color::Sky,
        ],
        'emerald' => [
            'primary' => Color::Emerald,
            'warning' => Color::Yellow,
            'danger'  => Color::Red,
            'success' => Color::Emerald,
            'info'    => Color::Sky,
        ],
        'sky'     => [
            'primary' => Color::Sky,
            'warning' => Color::Yellow,
            'danger'  => Color::Red,
            'success' => Color::Green,
            'info'    => Color::Cyan,
        ],
    ],
];
