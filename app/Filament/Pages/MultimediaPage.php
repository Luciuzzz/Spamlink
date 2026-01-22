<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms\Components\ViewField;
use App\Models\LandingSection;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Concerns\InteractsWithForms;

class MultimediaPage extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationLabel = 'Multimedia';
    protected static ?string $slug = 'landing/multimedia';
    protected static string $view = 'filament.pages.multimedia-page';

    public array $data = []; // estado principal del formulario

    public function mount(): void
    {
        $section = LandingSection::where('slug', 'multimedia')
            ->where('user_id', Auth::id())
            ->first();

        if (! $section) {
            $section = LandingSection::create([
                'slug' => 'multimedia',
                'user_id' => Auth::id(),
                'title' => 'Sección Multimedia',
                'description' => '',
                'is_active' => true,
                'data' => ['blocks' => []],
            ]);
        }

        // Llenamos $data con los valores de la DB
        $this->data = [
            'title' => $section->title,
            'description' => $section->description,
            'is_active' => $section->is_active,
            'blocks' => $section->data['blocks'] ?? [],
        ];

        // Llenamos el formulario con $data
        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data') // todo lo del formulario vive dentro de $this->data
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción'),

                Forms\Components\Toggle::make('is_active')
                    ->label('¿Activa?'),

                Forms\Components\Builder::make('blocks')
                    ->label('Contenido')
                    ->blocks([
                        Forms\Components\Builder\Block::make('text')
                            ->schema([
                                Forms\Components\RichEditor::make('content')
                                    ->label('Contenido')
                                    ->toolbarButtons([
                                        'bold',        // negrita
                                        'italic',      // cursiva
                                        'underline',   // subrayado
                                        'strike',      // tachado
                                        'h1',          // encabezado grande
                                        'h2',          // encabezado mediano
                                        'h3',          // encabezado pequeño
                                        'bulletList',  // lista con viñetas
                                        'orderedList', // lista numerada
                                        'link',        // enlace
                                        'redo',
                                        'undo',
                                    ])
                                    ->required(),

                                Forms\Components\ColorPicker::make('text_color')
                                    ->label('Color de texto')
                                    ->default('#ffffff'),
                            ]),


                        Forms\Components\Builder\Block::make('image')
                            ->schema([
                                Forms\Components\FileUpload::make('images') // cambia de singular a plural
                                    ->label('Imágenes')
                                    ->image()
                                    ->directory('landing-images')
                                    ->multiple(), // permite subir varias imágenes
                            ]),

                        Forms\Components\Builder\Block::make('video')
                            ->schema([Forms\Components\TextInput::make('embed_url')->url()]),
                    ])
                    ->columnSpanFull()
                    ->reactive()           // sincroniza automáticamente con $this->data
                    ->statePath('blocks'), // apunta directamente al array de bloques dentro de data

                ViewField::make('preview')
                    ->label('Vista previa')
                    ->view('components.landing-blocks')
                    ->viewData(['blocks' => fn () => $this->data['blocks'] ?? []])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        LandingSection::updateOrCreate(
            [
                'slug' => 'multimedia',
                'user_id' => Auth::id(),
            ],
            [
                'title' => $state['title'] ?? null,
                'description' => $state['description'] ?? null,
                'is_active' => $state['is_active'] ?? true,
                'data' => [
                    'blocks' => $state['blocks'] ?? [],
                ],
            ]
        );

        Notification::make()
            ->title('Contenido guardado correctamente')
            ->success()
            ->send();
    }
}
