<?php

namespace App\Filament\Resources\ChangeLogResource\Pages;

use App\Filament\Resources\ChangeLogResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;

class ListChangeLogs extends ListRecords
{
    protected static string $resource = ChangeLogResource::class;

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->label('ID')->sortable(),
            TextColumn::make('user.name')->label('Usuario')->sortable(),
            TextColumn::make('created_at')->label('Fecha')->dateTime()->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Action::make('ver_cambios')
                ->label('Ver Cambios')
                ->button()
                ->modal(fn ($record) => [
                    'heading' => 'Cambios del registro #' . $record->id,
                    'subheading' => 'Detalles de cambios recientes',
                    'content' => view('filament.changelog.modal', [
                        'changes' => $record->changes ?? null,
                    ]),
                ]),
        ];
    }
}
