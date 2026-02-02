<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\LandingSection;
use App\Models\SocialLink;

class Wizard extends Page
{
    protected static string $view = 'filament.pages.wizard';
    protected static ?string $navigationIcon = null;
    protected static ?string $navigationLabel = null;
    protected static bool $shouldRegisterNavigation = false;

    public int $step = 0;

    public function mount(): void
    {
        if (Auth::user()->wizard_completed) {
            redirect()->route('filament.admin.pages.dashboard');
        }
    }

    public function canProceed(): bool
    {
        $userId = Auth::id();

        return match ($this->step) {
            0 => $this->checkBasicSettings($userId),
            1 => $this->checkBranding($userId),
            2 => $this->checkSections($userId),
            3 => $this->checkSocialLinks($userId),
            default => false,
        };
    }

    protected function checkBasicSettings(int $userId): bool
    {
        $settings = Setting::where('user_id', $userId)->first();
        return $settings && !empty($settings->company_name) &&
               (!empty($settings->description) || !empty($settings->slogan));
    }

    protected function checkBranding(int $userId): bool
    {
        $settings = Setting::where('user_id', $userId)->first();
        return $settings && !empty($settings->logo_path);
    }

    protected function checkSections(int $userId): bool
    {
        return LandingSection::where('user_id', $userId)
            ->where('is_active', true)
            ->whereNotNull('title')
            ->whereNotNull('description')
            ->exists();
    }

    protected function checkSocialLinks(int $userId): bool
    {
        return SocialLink::where('user_id', $userId)
            ->where('is_active', true)
            ->whereNotNull('name')
            ->whereNotNull('url')
            ->exists();
    }

    public function next()
    {
        if (! $this->canProceed()) {
            $this->notify('danger', 'Debes completar los requisitos de este paso antes de continuar.');
            return;
        }

        $this->step++;
    }

    public function finish()
    {
        if (! $this->canProceed()) {
            $this->notify('danger', 'No puedes finalizar, hay requisitos pendientes.');
            return;
        }

        Auth::user()->update(['wizard_completed' => true]);

        $this->redirectRoute('filament.admin.pages.dashboard');
    }

    public function skipStep()
    {
        $this->step = min($this->step + 1, 3); // Avanza al siguiente paso
    }

}
