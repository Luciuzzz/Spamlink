<?php

namespace App\Support\Wizard;

use App\Models\Setting;
use App\Models\LandingSection;
use App\Models\SocialLink;
use Illuminate\Support\Facades\Auth;

class WizardState
{
    /** Paso actual */
    public static function currentStep(): int
    {
        if (! static::settingsCompleted()) {
            return 1;
        }

        if (! static::multimediaCompleted()) {
            return 2;
        }

        if (! static::socialsCompleted()) {
            return 3;
        }

        return 4; // completado
    }

    /** Wizard terminado */
    public static function completed(): bool
    {
        return static::currentStep() === 4;
    }

    /* ======================
       VALIDACIONES REALES
       ====================== */

    public static function settingsCompleted(): bool
    {
        return Setting::where('user_id', Auth::id())
            ->whereNotNull('company_name')
            ->where('company_name', '!=', '')
            ->exists();
    }

    public static function multimediaCompleted(): bool
    {
        return LandingSection::where('user_id', Auth::id())
            ->where('slug', 'multimedia')
            ->whereRaw("JSON_LENGTH(data->'$.blocks') > 0")
            ->exists();
    }

    public static function socialsCompleted(): bool
    {
        return SocialLink::where('user_id', Auth::id())
            ->where('is_active', true)
            ->exists();
    }
}
