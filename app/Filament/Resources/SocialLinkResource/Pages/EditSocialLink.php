<?php

namespace App\Filament\Resources\SocialLinkResource\Pages;

use App\Filament\Resources\SocialLinkResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class EditSocialLink extends EditRecord
{
    protected static string $resource = SocialLinkResource::class;
    protected Width|string|null $maxContentWidth = Width::Full;

    protected function afterSave(): void
    {
        if (! Auth::user()->wizard_completed) {
            $this->redirectRoute('filament.admin.pages.wizard');
        }
    }
}
