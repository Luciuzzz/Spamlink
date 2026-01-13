<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Forms\Components\MapPicker;
use GuzzleHttp\Client;

class MySettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.my-settings';
    protected static ?string $navigationLabel = 'Mi Configuración';
    protected static ?string $navigationGroup = 'Contenido';

    public ?array $data = [];

    public function mount(): void
    {
        $userId = Auth::id();
        $setting = Setting::firstOrCreate(
            ['user_id' => $userId],
            ['company_name' => 'Empresa']
        );
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

                        MapPicker::make('latitude')
                            ->longitudeField('longitude')
                            ->label('Ubicación')
                            ->required(),
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
        $userId = Auth::id();

        $setting = Setting::firstOrNew(['user_id' => $userId]);
        $setting->fill($validated);
        $setting->user_id = $userId;

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $lat = $validated['latitude'];
            $lng = $validated['longitude'];

            // Generamos link de OpenStreetMap
            $setting->location_text = "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lng}#map=17/{$lat}/{$lng}";

            // Obtenemos dirección legible con Nominatim
            $address = $this->getAddressFromCoordinates($lat, $lng);
            $setting->address_text = $address ?? '';
        }

        $setting->save();

        Notification::make()
            ->title('Configuración guardada')
            ->success()
            ->send();
    }


    protected function getAddressFromCoordinates($lat, $lng): ?string
    {
        try {
            $client = new Client([
                'headers' => [
                    'User-Agent' => 'MiApp/1.0 (tuemail@ejemplo.com)'
                ]
            ]);

            $url = "https://nominatim.openstreetmap.org/reverse?lat={$lat}&lon={$lng}&format=json";
            $response = $client->get($url);
            $data = json_decode($response->getBody(), true);

            return $data['display_name'] ?? null;

        } catch (\Exception $e) {
            // Si falla Nominatim, devolvemos null
            return null;
        }
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = Auth::id();
        return $data;
    }
}
