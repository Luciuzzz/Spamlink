<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use App\Forms\Components\MapPicker;
use App\Models\ChangeLog;
use GuzzleHttp\Client;
use Livewire\Attributes\Url;


class MySettings extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static string $view = 'filament.pages.my-settings';
    protected static ?string $navigationLabel = 'Mi Configuración';
    protected static ?string $title = 'Configuración';
    protected static ?string $slug = 'my-settingsS';
    protected static ?int $navigationSort = 1;


    public ?array $data = [];
    #[Url(as: 'user')]
    public int|string|null $user = null;

    public function mount(): void
    {
        //dd($this->user);

        if (Auth::user()->role !== 'superadmin') {
            $this->user = Auth::id();
        }

        if (!$this->user) {
            $this->user = Auth::id();
        }

        $setting = Setting::firstOrCreate(
            ['user_id' => (int) $this->user],
            // ['company_name' => 'Empresa']
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
                            // ->required()
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
                        
                        Forms\Components\Hidden::make('latitude'),
                        Forms\Components\Hidden::make('longitude'),
                        Forms\Components\Hidden::make('location_text'),
                        //mapa
                        MapPicker::make('latitude')
                            ->longitudeField('longitude')
                            ->label('Mapa')
                            ->required(),
                        // Campo del buscador
                        Forms\Components\Field::make('search_location')
                            ->label('Buscar ubicación')
                            ->extraAttributes(['wire:ignore'])
                            ->view('filament.components.location-search'),
                                 
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('clearLocationText')
                                ->label('Borrar ubicación personalizada')
                                ->color('danger')
                                ->icon('heroicon-o-trash')
                                ->action(function () {
                                    $this->data['location_text'] = null;

                                    Notification::make()
                                        ->title('Ubicación personalizada eliminada')
                                        ->success()
                                        ->send();
                                })
                                ->visible(fn () => !empty($this->data['location_text'])),
                        ]),

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
                    ->columns(2)
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $userId = $this->user;
        $validated = $this->form->getState();

        $setting = Setting::firstOrNew(['user_id' => $userId]);
        
        // Guardamos el estado previo para comparar cambios
        $before = $setting->toArray();
        $after = $validated;

        $setting->fill($validated);
        $setting->user_id = $userId;
        $setting->location_text = $validated['location_text'] ?? null;

        // Si hay lat/lng, generar dirección legible si address_text está vacío
        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $lat = $validated['latitude'];
            $lng = $validated['longitude'];

            if (empty($validated['address_text'])) {
                $address = $this->getAddressFromCoordinates($lat, $lng);
                $setting->address_text = $address ?? '';
            }
        }

        $setting->save();

        // --- Registro en ChangeLog ---
        $changes = [];
        foreach ($after as $key => $newValue) {
            $oldValue = $before[$key] ?? null;

            if ($oldValue != $newValue) {
                $changes[$key] = [
                    'from' => $oldValue,
                    'to'   => $newValue,
                ];
            }
        }

        if (!empty($changes)) {
            \App\Models\ChangeLog::create([
                'user_id'    => $userId,
                'model_type' => Setting::class,
                'model_id'   => $setting->id,
                'action'     => $setting->wasRecentlyCreated ? 'create' : 'update',
                'changes'    => $changes,
            ]);
        }

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

}
