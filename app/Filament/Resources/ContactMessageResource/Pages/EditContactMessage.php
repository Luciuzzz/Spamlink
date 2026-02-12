<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactMessage extends EditRecord
{
    protected static string $resource = ContactMessageResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        if (blank($this->record->read_at)) {
            $this->record->update([
                'read_at' => now(),
                'is_read' => true,
            ]);

            $this->record->refresh();
            $this->form->fill($this->record->attributesToArray());
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Volver')
                ->icon('heroicon-o-arrow-left')
                ->url(ContactMessageResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
