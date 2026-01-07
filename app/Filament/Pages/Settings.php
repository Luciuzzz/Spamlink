<?php

namespace App\Filament\Pages;

use App\Models\Setting as SettingModel;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Settings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Configuración';
    protected static ?string $navigationGroup = 'Contenido';

    protected static string $view = 'filament.pages.settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = SettingModel::query()->first();

        if (!$setting) {
            $setting = SettingModel::query()->create([
                'company_name' => 'Empresa',
            ]);
        }

        $this->form->fill($setting->toArray());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nombre de la empresa')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slogan')
                            ->label('Eslogan')
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->rows(4)
                            ->maxLength(2000),

                        Forms\Components\TextInput::make('whatsapp_number')
                            ->label('WhatsApp')
                            ->helperText('Ej: +595 9XX XXX XXX')
                            ->maxLength(50),

                        Forms\Components\TextInput::make('location_text')
                            ->label('Ubicación (texto)')
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Fondos')
                    ->schema([
                        Forms\Components\FileUpload::make('bg_desktop_path')
                            ->label('Fondo Desktop')
                            ->disk('public')
                            ->directory('backgrounds')
                            ->image()
                            ->imageEditor()
                            ->maxSize(4096),

                        Forms\Components\FileUpload::make('bg_mobile_path')
                            ->label('Fondo Mobile')
                            ->disk('public')
                            ->directory('backgrounds')
                            ->image()
                            ->imageEditor()
                            ->maxSize(4096),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $validated = $this->form->getState();

        $setting = SettingModel::query()->first();
        if (!$setting) {
            $setting = new SettingModel();
        }

        $setting->fill($validated);
        $setting->save();

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }
}
