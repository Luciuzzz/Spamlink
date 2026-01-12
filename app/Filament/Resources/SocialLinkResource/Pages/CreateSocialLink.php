<?php

namespace App\Filament\Resources\SocialLinkResource\Pages;

use App\Filament\Resources\SocialLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSocialLink extends CreateRecord
{
    protected static string $resource = SocialLinkResource::class;

    protected function getRedirectUrl(): string
    {
        // Por defecto ir al listado
        return $this->getResource()::getUrl('index');
    }
}