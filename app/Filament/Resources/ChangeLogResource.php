<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChangeLogResource\Pages;
use App\Models\ChangeLog;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChangeLogResource extends Resource
{
    protected static ?string $model = ChangeLog::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Historial de Cambios';
    protected static ?string $pluralLabel = 'Cambios';
    protected static ?int $navigationSort = 11;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->role === 'superadmin';
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Usuario')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('model_type')
                    ->label('Entidad')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->badge(),

                TextColumn::make('action')
                    ->label('Acción')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'create' => 'success',
                        'update' => 'warning',
                        'delete' => 'danger',
                        default => 'gray',
                    }),

                // TextColumn::make('')
                //     ->label('Cambios')
                //     ->limit(30),
            ])
            ->filters([
                SelectFilter::make('model_type')
                    ->label('Entidad')
                    ->options([
                        'App\Models\Setting' => 'Configuración',
                        'App\Models\LandingSection' => 'Secciones Landing',
                    ]),

                SelectFilter::make('action')
                    ->label('Acción')
                    ->options([
                        'create' => 'Creación',
                        'update' => 'Actualización',
                        'delete' => 'Eliminación',
                    ]),

                SelectFilter::make('user_id')
                    ->label('Usuario')
                    ->relationship('user', 'name'),
            ])
            ->actions([
                Action::make('verCambios')
                    ->icon('heroicon-o-eye')
                    ->label('')
                    ->modalHeading('Detalle de Cambios')
                    ->modalWidth('7xl')
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar')
                    ->modalContent(fn ($record) => view('filament.modals.change-log', [
                        'record' => $record,
                    ]))

            ])
            ->bulkActions([]);
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListChangeLogs::route('/'),
        ];
    }
}
