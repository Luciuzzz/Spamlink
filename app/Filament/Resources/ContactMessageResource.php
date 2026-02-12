<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Mensajes de Contacto';

    protected static ?string $pluralLabel = 'Mensajes Recibidos';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->disabled(),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->disabled(),

                Forms\Components\Textarea::make('message')
                    ->label('Mensaje')
                    ->disabled()
                    ->rows(6),

                Forms\Components\DateTimePicker::make('read_at')
                    ->label('Leído el'),
            ]);
    }
    

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->label('Nombre')
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->label('Email')
                ->searchable(),

            Tables\Columns\TextColumn::make('message')
                ->label('Mensaje')
                ->limit(40),

            Tables\Columns\IconColumn::make('read_at')
                ->label('Leído')
                ->boolean()
                ->trueIcon('heroicon-o-check')
                ->falseIcon('heroicon-o-envelope'),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Enviado el')
                ->dateTime()
                ->sortable(),
        ])
        ->defaultSort('created_at', 'desc')
        ->recordActions([
            // Acción: Marcar como leído
            \Filament\Actions\Action::make('markAsRead')
                ->label('Marcar como leído')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn ($record) => is_null($record->read_at)) // solo si no está leído
                ->action(function ($record) {
                    $record->read_at = now();
                    $record->save();
                }),
        ]);
}
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'create' => Pages\CreateContactMessage::route('/create'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }

}
