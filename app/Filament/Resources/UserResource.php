<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Pages\MySettings;
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\MultimediaPage;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Usuarios';
    protected static ?string $pluralLabel = 'Usuarios';
    protected static ?string $slug = 'usuarios';

    public static function canViewAny(): bool
    {
        return Auth::check() && Auth::user()->role === 'superadmin';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('username')
                    ->searchable(),

                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\Action::make('landing')
                    ->label('Ver landing')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (User $record) => route('landing.user', $record->username))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('settings')
                    ->label('Editar configuración')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->url(fn (User $record) =>
                        MySettings::getUrl(['user' => $record->id])
                    )
                    ->openUrlInNewTab(),

                    Tables\Actions\Action::make('multimedia')
                        ->label('Multimedia')
                        ->icon('heroicon-o-photo')
                        ->color('indigo')
                        ->url(fn ($record) =>
                            MultimediaPage::getUrl(['user' => $record->id])
                        )
                        ->openUrlInNewTab(),
            ])
            ->defaultSort('id');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
        ];
    }
}
