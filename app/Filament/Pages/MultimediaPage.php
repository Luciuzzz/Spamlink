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
use Filament\Forms\Components\Builder\Block;

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

        $this->data = [
            'title' => $section->title,
            'description' => $section->description,
            'is_active' => $section->is_active,
            'blocks' => $section->data['blocks'] ?? [],
        ];

        $this->form->fill($this->data);
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('data')
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
                                Forms\Components\Textarea::make('content'),
                            ]),

                        Forms\Components\Builder\Block::make('image')
                            ->schema([
                                Forms\Components\FileUpload::make('images')
                                    ->label('Imágenes')
                                    ->image()
                                    ->directory('landing-images')
                                    ->multiple(),
                            ]),

                        Block::make('video')
                            ->schema([
                                Forms\Components\Select::make('data.source')
                                    ->label('Tipo de video')
                                    ->options([
                                        'local' => 'Video propio (MP4)',
                                        'youtube' => 'YouTube',
                                        'vimeo' => 'Vimeo',
                                    ])
                                    ->required()
                                    ->reactive(),

                                Forms\Components\FileUpload::make('data.file')
                                    ->label('Archivo de video')
                                    ->directory('landing-videos')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm'])
                                    ->visible(fn ($get) => $get('data.source') === 'local')
                                    ->required(fn ($get) => $get('data.source') === 'local'),

                                Forms\Components\TextInput::make('data.url')
                                    ->label('URL del video')
                                    ->placeholder('https://www.youtube.com/watch?v=...')
                                    ->visible(fn ($get) => in_array($get('data.source'), ['youtube', 'vimeo']))
                                    ->required(fn ($get) => in_array($get('data.source'), ['youtube', 'vimeo'])),

                            ]),

                    ])
                    ->columnSpanFull()
                    ->reactive()
                    ->statePath('blocks'),

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
