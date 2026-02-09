<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Pages\Page;
use Filament\Forms;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use App\Forms\Components\MapPicker;
use App\Forms\Components\RangeSlider;
use App\Models\ChangeLog;
use GuzzleHttp\Client;
use Livewire\Attributes\Url;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MySettings extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected string $view = 'filament.pages.my-settings';
    protected static ?string $navigationLabel = 'Mi Configuración';
    protected static ?string $title = 'Configuración';
    protected static ?string $slug = 'my-settings';
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

        $data = $setting->toArray();

        if (isset($data['bg_desktop_path']) && $this->isColorValue($data['bg_desktop_path'])) {
            $data['bg_desktop_color'] = $data['bg_desktop_path'];
            $data['bg_desktop_path'] = null;
        }

        if (isset($data['bg_mobile_path']) && $this->isColorValue($data['bg_mobile_path'])) {
            $data['bg_mobile_color'] = $data['bg_mobile_path'];
            $data['bg_mobile_path'] = null;
        }

        $this->form->fill($data);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Información')
                    ->extraAttributes(['data-tour' => 'settings-info'])
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->label('Nombre de la empresa')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Nombre visible en la landing y metadatos'),

                        Forms\Components\TextInput::make('slogan')
                            ->label('Eslogan')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Frase corta debajo del nombre'),

                        Forms\Components\Textarea::make('description')
                            ->label('Descripción')
                            ->required()
                            ->rows(4)
                            ->maxLength(2000)
                            ->helperText('Texto principal de la landing'),

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
                            ->helperText('Selecciona una ubicación para mostrar dirección'),
                        // Campo del buscador
                        Forms\Components\Field::make('search_location')
                            ->label('Buscar ubicación')
                            ->extraAttributes(['wire:ignore'])
                            ->view('filament.components.location-search'),
                                 
                        Actions::make([
                            Action::make('clearLocationText')
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

                Section::make('Identidad visual')
                    ->extraAttributes(['data-tour' => 'settings-branding'])
                    ->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions(['1:1'])
                            ->maxSize(2048)
                            ->helperText('PNG/JPG cuadrado recomendado'),

                        Forms\Components\FileUpload::make('favicon_path')
                            ->label('Favicon')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->helperText('PNG o ICO ~ recomendado 32x32 o 48x48')
                            ->maxSize(512),
                    ])
                    ->columns(2),

                Section::make('SEO / Metadatos')
                    ->extraAttributes(['data-tour' => 'settings-seo'])
                    ->schema([
                        Forms\Components\TextInput::make('meta_title')
                            ->label('Meta title')
                            ->maxLength(70)
                            ->helperText('Título para Google y Open Graph'),

                        Forms\Components\Textarea::make('meta_description')
                            ->label('Meta description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Resumen para buscadores'),

                        Forms\Components\TextInput::make('meta_keywords')
                            ->label('Keywords')
                            ->helperText('Separadas por coma'),

                        Forms\Components\FileUpload::make('meta_image_path')
                            ->label('Imagen para redes (OG)')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions(['1.91:1'])
                            ->maxSize(4096)
                            ->helperText('1200x630 recomendado'),

                        Forms\Components\TextInput::make('twitter_title')
                            ->label('Twitter title')
                            ->maxLength(70)
                            ->helperText('Si queda vacío usa Meta title'),

                        Forms\Components\Textarea::make('twitter_description')
                            ->label('Twitter description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Si queda vacío usa Meta description'),

                        Forms\Components\FileUpload::make('twitter_image_path')
                            ->label('Imagen Twitter')
                            ->disk('public')
                            ->directory('branding')
                            ->image()
                            ->imageEditor()
                            ->imageEditorAspectRatioOptions(['1.91:1'])
                            ->maxSize(4096)
                            ->helperText('Si queda vacío usa Imagen OG'),
                    ])
                    ->columns(2),

                Section::make('Fondos')
                    ->extraAttributes(['data-tour' => 'settings-backgrounds'])
                    ->schema([
                        Forms\Components\FileUpload::make('bg_desktop_path')
                            ->label('Fondo Desktop')
                            ->disk('public')
                            ->directory('backgrounds')
                            ->image()
                            ->imageEditor()
                            ->maxSize(4096)
                            ->helperText('Se usa si no elegís color'),

                        Forms\Components\ColorPicker::make('bg_desktop_color')
                            ->label('Color fondo Desktop (opcional)')
                            ->helperText('Si hay imagen, la imagen tiene prioridad'),

                        Forms\Components\FileUpload::make('bg_mobile_path')
                            ->label('Fondo Mobile')
                            ->disk('public')
                            ->directory('backgrounds')
                            ->image()
                            ->imageEditor()
                            ->maxSize(4096)
                            ->helperText('Se usa si no elegís color'),

                        Forms\Components\ColorPicker::make('bg_mobile_color')
                            ->label('Color fondo Mobile (opcional)')
                            ->helperText('Si hay imagen, la imagen tiene prioridad'),

                        Forms\Components\Toggle::make('bg_overlay_enabled')
                            ->label('Capa de contraste')
                            ->helperText('Oscurece el fondo para mejorar legibilidad')
                            ->default(true),

                        RangeSlider::make('bg_overlay_opacity')
                            ->label('Intensidad de contraste')
                            ->min(0.1)
                            ->max(1)
                            ->step(0.05)
                            ->default(0.55)
                            ->helperText('De 0.1 a 1.0')
                            ->disabled(fn (Get $get) => ! $get('bg_overlay_enabled')),
                    ])
                    ->columns(2),

                    Section::make('Visibilidad de la Página')
                        ->extraAttributes(['data-tour' => 'settings-visibility'])
                        ->schema([
                        Forms\Components\Toggle::make('landing_available')
                            ->label('Landing disponible')
                            ->helperText('Si desactivás, la URL pública mostrará un aviso de no disponibilidad')
                            ->default(true),
                        ])
                    ->columns(2)
                        
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $userId = $this->user;
        $validated = $this->form->getState();
        $bgDesktopColor = $validated['bg_desktop_color'] ?? null;
        $bgMobileColor = $validated['bg_mobile_color'] ?? null;

        unset($validated['bg_desktop_color'], $validated['bg_mobile_color']);

        if (empty($validated['bg_desktop_path']) && $bgDesktopColor) {
            $validated['bg_desktop_path'] = $bgDesktopColor;
        }

        if (empty($validated['bg_mobile_path']) && $bgMobileColor) {
            $validated['bg_mobile_path'] = $bgMobileColor;
        }

        $setting = Setting::firstOrNew(['user_id' => $userId]);
        $before = $setting->toArray();
        $after  = $validated;

        $setting->fill($validated);
        $setting->user_id = $userId;
        $setting->location_text = $validated['location_text'] ?? null;
        $setting->bg_overlay_enabled = $validated['bg_overlay_enabled'] ?? true;
        $setting->bg_overlay_opacity = $validated['bg_overlay_opacity'] ?? 0.55;

        if (!empty($validated['latitude']) && !empty($validated['longitude'])) {
            $lat = $validated['latitude'];
            $lng = $validated['longitude'];
            if (empty($validated['address_text'])) {
                $address = $this->getAddressFromCoordinates($lat, $lng);
                $setting->address_text = $address ?? '';
            }
        }

        $setting->save();

        // Registro ChangeLog
        $changes = [];
        foreach ($after as $key => $newValue) {
            $oldValue = $before[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[$key] = ['from' => $oldValue, 'to' => $newValue];
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

        if (! Auth::user()->wizard_completed) {
            $this->redirectRoute('filament.admin.pages.wizard');
        }
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

    protected function getViewData(): array
    {
        return [
            'wizardSteps' => [
                [
                    'selector' => '[data-tour="settings-info"]',
                    'title' => 'Información básica',
                    'body' => 'Completá los datos que se verán en tu landing.',
                    'required' => 'Nombre de la empresa, Eslogan, Descripción',
                ],
                [
                    'selector' => '[data-tour="settings-branding"]',
                    'title' => 'Identidad visual',
                    'body' => 'Cargá logo y favicon para tu marca.',
                    'required' => 'Ninguno (todo opcional)',
                ],
                [
                    'selector' => '[data-tour="settings-seo"]',
                    'title' => 'SEO y Metadatos',
                    'body' => 'Configurá cómo se ve tu landing en Google y redes.',
                    'required' => 'Ninguno (todo opcional)',
                ],
                [
                    'selector' => '[data-tour="settings-backgrounds"]',
                    'title' => 'Fondos',
                    'body' => 'Elegí imagen o color para desktop y mobile.',
                    'required' => 'Ninguno (todo opcional)',
                ],
                [
                    'selector' => '[data-tour="settings-visibility"]',
                    'title' => 'Visibilidad',
                    'body' => 'Definí si la landing pública está disponible.',
                    'required' => 'Ninguno',
                    'scrollTopOnClose' => true,
                ],
            ],
        ];
    }

    protected function isColorValue(?string $value): bool
    {
        if (! $value) {
            return false;
        }

        return str_starts_with($value, '#')
            || str_starts_with($value, 'rgb')
            || str_starts_with($value, 'hsl');
    }
}
