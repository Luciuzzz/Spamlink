<?php

namespace App\Filament\Resources\SocialLinkResource\Pages;

use App\Filament\Resources\SocialLinkResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditSocialLink extends EditRecord
{
    protected static string $resource = SocialLinkResource::class;

    protected function afterSave(): void
    {
        parent::afterSave();

        if (! Auth::user()->wizard_completed) {
            $this->redirectRoute('filament.admin.pages.wizard');
        }
    }
}
