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
    protected static ?string $modelLabel = 'Link';
    protected static ?string $pluralModelLabel = 'Links';
    protected static ?string $navigationGroup = 'Contenido';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Seguridad: El ID del usuario se asigna automáticamente
            Forms\Components\Hidden::make('user_id')
                ->default(fn() => Auth::id())
                ->required(),

            Forms\Components\Section::make('Datos del enlace')
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Nombre')
                        ->placeholder('Ej: Mi Instagram o Contacto')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Select::make('type')
                        ->label('Tipo')
                        ->required()
                        ->options([
                            'url' => 'URL / Sitio Web',
                            'email' => 'Correo electrónico',
                            'whatsapp' => 'WhatsApp',
                        ])
                        ->default('url')
                        ->live(), // Permite que el campo URL reaccione al cambio inmediatamente

                    Forms\Components\TextInput::make('url')
                        ->label(fn ($get) => match ($get('type')) {
                            'email' => 'Dirección de correo',
                            'whatsapp' => 'Número de WhatsApp',
                            default => 'URL o enlace',
                        })
                        ->placeholder(fn ($get) => match ($get('type')) {
                            'email' => 'ejemplo@correo.com',
                            'whatsapp' => '595981123456',
                            default => 'sitio.com',
                        })
                        ->required()
                        ->maxLength(2048)
                        // Validación dinámica corregida para no bloquear tipos distintos a URL
                        ->rules(fn ($get) => match ($get('type')) {
                            'email' => ['email'],
                            'whatsapp' => ['regex:/^[0-9+]+$/'],
                            'url' => ['url'],
                            default => [],
                        })
                        ->dehydrateStateUsing(function ($state, $get) {
                            if ($get('type') === 'whatsapp') {
                                // Limpia espacios y caracteres para guardar solo el número
                                return preg_replace('/\D+/', '', $state);
                            }
                            return $state;
                        }),

                    Forms\Components\FileUpload::make('icon_path')
                        ->label('Ícono (opcional)')
                        ->disk('public')
                        ->directory('social-icons')
                        ->image()
                        ->imageEditor()
                        ->imageEditorAspectRatios(['1:1'])
                        ->maxSize(1024),

                    Forms\Components\Select::make('icon_preset')
                        ->label('Ícono predefinido')
                        ->options([
                            'facebook'  => 'Facebook',
                            'instagram' => 'Instagram',
                            'X'         => 'Twitter',
                            'hilos'     => 'Threads',
                            'youtube'   => 'YouTube',
                            'tik-tok'   => 'TikTok',
                            'email'     => 'Correo electrónico',
                            'telegrama'  => 'Telegram',
                            'whatsapp'  => 'WhatsApp',
                            'linkedin'  => 'LinkedIn',
                            'github'    => 'GitHub',
                            'pinterest' => 'Pinterest',
                            'website'   => 'Sitio web',
                        ])
                        ->searchable()
                        ->placeholder('Seleccionar ícono')
                        ->helperText('Se usa solo si no se sube un ícono propio'),


                    Forms\Components\TextInput::make('order')
                        ->label('Orden de aparición')
                        ->numeric()
                        ->default(0)
                        ->helperText('Menor número aparece primero'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Enlace visible')
                        ->default(true)
                        ->inline(false),

                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order', 'asc')
            ->reorderable('order')
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\ImageColumn::make('icon_path')
                    ->label('Icono')
                    ->circular(),

                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'url' => 'info',
                        'whatsapp' => 'success',
                        'email' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\TextColumn::make('url')
                    ->label('Enlace/Dato')
                    ->limit(30)
                    ->copyable()
                    ->toggleable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Estado de actividad'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSocialLinks::route('/'),
            'create' => Pages\CreateSocialLink::route('/create'),
            'edit' => Pages\EditSocialLink::route('/{record}/edit'),
        ];
    }

    // El usuario solo puede gestionar sus propios registros
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', Auth::id());
    }
}
