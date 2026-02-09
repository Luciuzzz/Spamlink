<?php

namespace App\Filament\Resources\SocialLinkResource\Pages;

use App\Filament\Resources\SocialLinkResource;
use App\Models\ChangeLog;
use App\Models\SocialLink;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;
use Illuminate\Support\Facades\Auth;

class CreateSocialLink extends CreateRecord
{
    protected static string $resource = SocialLinkResource::class;
    // protected Width|string|null $maxContentWidth = Width::Full;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        /** @var SocialLink $record */
        $record = $this->record;
        $changes = [];
        $data = $record->toArray();

        unset($data['id'], $data['user_id'], $data['created_at'], $data['updated_at']);

        foreach ($data as $key => $value) {
            $changes[$key] = ['from' => null, 'to' => $value];
        }

        if (! empty($changes)) {
            ChangeLog::create([
                'user_id' => $record->user_id ?? Auth::id(),
                'model_type' => SocialLink::class,
                'model_id' => $record->id,
                'action' => 'create',
                'changes' => $changes,
            ]);
        }
    }
}
