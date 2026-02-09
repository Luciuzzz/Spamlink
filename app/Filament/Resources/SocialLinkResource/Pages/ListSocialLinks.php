<?php

namespace App\Filament\Resources\SocialLinkResource\Pages;

use App\Filament\Resources\SocialLinkResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListSocialLinks extends ListRecords
{
    protected static string $resource = SocialLinkResource::class;
    protected string $view = 'filament.resources.social-link-resource.pages.list-social-links';
    protected Width|string|null $maxContentWidth = Width::Full;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->extraAttributes(['data-tour' => 'social-links-create']),
        ];
    }
}
