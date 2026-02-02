<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class WizardLauncher extends Widget
{
    protected static string $view = 'filament.widgets.wizard-launcher';

    public function launchWizard()
    {
        Auth::user()->update([
            'wizard_completed' => false,
        ]);

        redirect()->route('filament.admin.pages.wizard');
    }
}
