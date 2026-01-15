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

                        Forms\Components\Placeholder::make('Ubicación')
                            ->content(<<<'HTML'
                                <div x-data="locationPicker()" class="relative">
                                    <input type="text" x-ref="locationInput" placeholder="Escribí la dirección..."
                                        class="form-control z-0 py-1 px-2 w-full"
                                        autocomplete="on">
                                    <ul x-ref="resultsList"
                                        class="absolute z-50 w-full max-h-48 overflow-y-auto bg-white text-black rounded-md shadow-lg mt-1 hidden"></ul>
                                </div>
                                HTML
                                    ),


                        MapPicker::make('latitude')
                            ->longitudeField('longitude')
                            ->label('Ubicación')
                            ->required(),
                    ])
                    ->columns(2),
                
                Forms\Components\Section::make('Identidad visual')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1:1'])
                            ->maxSize(2048),

                        Forms\Components\FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->helperText('PNG o ICO ~ recomendado 32x32 o 48x48')
                            ->maxSize(512),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('SEO / Metadatos')
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(70)
                            ->helperText('Título para Google y redes'),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->maxLength(160),

                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Keywords')
                            ->helperText('Separadas por coma'),

                        Forms\Components\FileUpload::make('meta_image_path')
                            ->label('Imagen para redes (OG)')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatios(['1.91:1'])
                            ->maxSize(4096),
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

        if (!empty($validated['location_text'])) {
            $setting->location_text = $validated['location_text'];
        }

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $lat = $validated['latitude'];
            $lng = $validated['longitude'];

            if (empty($validated['location_text'])) {
                $setting->location_text = "https://www.openstreetmap.org/?mlat={$lat}&mlon={$lng}#map=17/{$lat}/{$lng}";
            }

            if (empty($validated['address_text'])) {
                $address = $this->getAddressFromCoordinates($lat, $lng);
                $setting->address_text = $address ?? '';
            }
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
