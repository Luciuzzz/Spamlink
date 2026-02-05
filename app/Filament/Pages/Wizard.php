<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;
use App\Models\LandingSection;
use App\Models\SocialLink;

class Wizard extends Page
{
    protected string $view = 'filament.pages.wizard';
    protected static string|\BackedEnum|null $navigationIcon = null;
    protected static ?string $navigationLabel = null;
    protected static bool $shouldRegisterNavigation = false;

    public int $step = 0;

    /* =========================
     *  Lifecycle
     * ========================= */

    public function mount(): void
    {
        if (Auth::user()->wizard_completed) {
            $this->redirectRoute('filament.admin.pages.dashboard');
        }

        $this->step = $this->detectCurrentStep();
    }

    /* =========================
     *  Navigation
     * ========================= */

    public function next(): void
    {
        if (! $this->canProceed()) {
            Notification::make()
                ->title('Debes completar este paso antes de continuar')
                ->danger()
                ->send();

            return;
        }

        $this->step = min($this->step + 1, 2);
    }

    public function finish(): void
    {
        $userId = Auth::id();

        if (
            ! $this->checkSections($userId) ||
            ! $this->checkSocialLinks($userId)
        ) {
            Notification::make()
                ->title('El wizard no está completo')
                ->danger()
                ->send();

            return;
        }

        Auth::user()
            ->forceFill(['wizard_completed' => true])
            ->save();

        $this->redirectRoute('filament.admin.pages.dashboard');
    }

    /**
     * SOLO para testing local
     */
    public function skipStep(): void
    {
        if (! app()->isLocal()) {
            return;
        }

        $this->step = min($this->step + 1, 3);
    }

    /* =========================
     *  Guards
     * ========================= */

    protected function canProceed(): bool
    {
        $userId = Auth::id();

        return match ($this->step) {
            0 => $this->checkBasicSettings($userId),
            1 => $this->checkSocialLinks($userId),
            2 => $this->checkSections($userId),
            default => false,
        };
    }

    protected function detectCurrentStep(): int
    {
        $userId = Auth::id();

        if (! $this->checkBasicSettings($userId)) {
            return 0;
        }

        if (! $this->checkSocialLinks($userId)) {
            return 1;
        }

        if (! $this->checkSections($userId)) {
            return 2;
        }

        return 2;
    }

    /* =========================
     *  Step checks
     * ========================= */

    protected function checkBasicSettings(int $userId): bool
    {
        $settings = Setting::where('user_id', $userId)->first();

        return $settings
            && ! empty($settings->company_name)
            && ! empty($settings->slogan)
            && ! empty($settings->description);
    }

    protected function checkBranding(int $userId): bool
    {
        return true;
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
}
