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

    public array $data = [];
    public ?int $userId = null;

    public function mount(): void
    {
        $authUser = Auth::user();

        if ($authUser->role === 'superadmin' && request()->has('user')) {
            $this->userId = (int) request()->get('user');
        } else {
            $this->userId = $authUser->id;
        }

        $section = LandingSection::where('slug', 'multimedia')
            ->where('user_id', $this->userId)
            ->first();

        if (! $section) {
            $section = LandingSection::create([
                'slug' => 'multimedia',
                'user_id' => $this->userId,
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
                                Forms\Components\RichEditor::make('content')
                                    ->label('Contenido')
                                    ->required(),
                                Forms\Components\ColorPicker::make('text_color')
                                    ->label('Color de texto')
                                    ->default('#ffffff'),
                            ]),

                        Forms\Components\Builder\Block::make('image')
                            ->schema([
                                Forms\Components\FileUpload::make('images')
                                    ->label('Imágenes')
                                    ->image()
                                    ->directory('landing-images')
                                    ->multiple(),
                            ]),

                        Forms\Components\Builder\Block::make('video')
                            ->schema([
                                Forms\Components\TextInput::make('embed_url')
                                    ->label('URL del video')
                                    ->url(),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->reactive(),

                ViewField::make('preview')
                    ->label('Vista previa')
                    ->view('components.landing-blocks')
                    ->viewData([
                        'blocks' => fn () => $this->data['blocks'] ?? [],
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function save(): void
    {
        $state = $this->form->getState();

        LandingSection::updateOrCreate(
            [
                'slug' => 'multimedia',
                'user_id' => $this->userId,
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
            ->title('Multimedia guardada correctamente')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}
