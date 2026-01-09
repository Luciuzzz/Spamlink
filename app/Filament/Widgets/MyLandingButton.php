<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MyLandingButton extends Widget
{
    protected static string $view = 'filament.widgets.my-landing-button';

    protected int | string | array $columnSpan = 'full';

    public function getLandingUrl(): string
    {
        return route('landing.user', Auth::user()->username);
    }
}
