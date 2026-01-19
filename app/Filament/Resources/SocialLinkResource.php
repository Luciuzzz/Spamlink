<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SocialLinkResource\Pages;
use App\Models\SocialLink;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class SocialLinkResource extends Resource
{
    protected static ?string $model = SocialLink::class;
    protected static ?string $navigationIcon = 'heroicon-o-link';
    protected static ?string $navigationLabel = 'Redes / Links';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('user_id')
                ->default(fn () => Auth::id())
                ->required(),

            Forms\Components\Section::make('Datos del enlace')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('type')
                        ->label('Tipo')
                        ->required()
                        ->options([
                            'url'      => 'URL / Sitio web',
                            'email'    => 'Correo electrónico',
                            'whatsapp' => 'WhatsApp',
                        ])
                        ->default('url')
                        ->live(),

                    Forms\Components\TextInput::make('url')
                        ->label('Destino')
                        ->required()
                        ->rules(fn ($get) => match ($get('type')) {
                            'email'    => ['email'],
                            'whatsapp' => ['regex:/^[0-9+]+$/'],
                            default    => ['max:2048'],
                        }),

                    Forms\Components\Select::make('icon_preset')
                        ->label('Icono')
                        ->options([
                            'facebook'  => 'Facebook',
                            'instagram' => 'Instagram',
                            'twitter'   => 'X / Twitter',
                            'threads'   => 'Threads',
                            'youtube'   => 'YouTube',
                            'tiktok'    => 'TikTok',
                            'email'     => 'Email',
                            'telegram'  => 'Telegram',
                            'whatsapp'  => 'WhatsApp',
                            'linkedin'  => 'LinkedIn',
                            'github'    => 'GitHub',
                            'pinterest' => 'Pinterest',
                            'website'   => 'Website',
                        ])
                        ->required(),

                    Forms\Components\FileUpload::make('icon_path')
                        ->label('Ícono personalizado (opcional)')
                        ->disk('public')
                        ->directory('social-icons')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1'])
                        ->maxSize(1024),

                    Forms\Components\TextInput::make('order')
                        ->numeric()
                        ->default(0),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Visible')
                        ->default(true),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->badge(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSocialLinks::route('/'),
            'create' => Pages\CreateSocialLink::route('/create'),
            'edit'   => Pages\EditSocialLink::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
