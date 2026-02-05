<?php

namespace App\Filament\Pages;

use App\Models\LandingSection;
use Filament\Forms;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class MultimediaPage extends Page
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationLabel = 'Multimedia';

    protected static ?string $slug = 'multimedia';

    protected static ?string $title = 'Editor de Multimedia';

    protected string $view = 'filament.pages.multimedia-page';

    protected static ?int $navigationSort = 2;

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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->schema([
                View::make('wizard_tour_multimedia')
                    ->view('filament.components.wizard-tour')
                    ->viewData([
                        'steps' => [
                            [
                                'selector' => '[data-tour="multimedia-blocks"]',
                                'title' => 'Bloques multimedia',
                                'body' => 'Agregá bloques de texto, imágenes o video para tu landing.',
                                'required' => 'Al menos un bloque activo con contenido',
                            ],
                        ],
                    ])
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción'),

                Forms\Components\Toggle::make('is_active')
                    ->label('¿Activa?'),

                Forms\Components\Builder::make('blocks')
                    ->label('Contenido')
                    ->extraAttributes(['data-tour' => 'multimedia-blocks'])
                    ->blocks([
                        Forms\Components\Builder\Block::make('text')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Activo?')
                                    ->default(true),
                                Forms\Components\RichEditor::make('content')
                                    ->label('Contenido')
                                    ->required()
                                    ->extraInputAttributes(['style' => 'min-height: 200px; h-auto;'])
                                    ->placeholder('Escribí tu contenido aquí...')
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                        ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
                                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                        ['table', 'textColor', 'highlight', 'attachFiles'],
                                        ['undo', 'redo'],
                                    ]),
                            ]),

                        Forms\Components\Builder\Block::make('image')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Activo?')
                                    ->default(true),
                                Forms\Components\FileUpload::make('images')
                                    ->label('Imágenes')
                                    ->image()
                                    ->directory('landing-images')
                                    ->multiple(),
                            ]),

                        Forms\Components\Builder\Block::make('video')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Activo?')
                                    ->default(true),
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

        $section = LandingSection::firstOrNew([
            'slug' => 'multimedia',
            'user_id' => $this->userId,
        ]);

        $before = $section->toArray();
        $after = [
            'title' => $state['title'] ?? $section->title,
            'description' => $state['description'] ?? $section->description,
            'is_active' => $state['is_active'] ?? $section->is_active,
            'data' => ['blocks' => $state['blocks'] ?? []],
        ];

        $section->title = $after['title'];
        $section->description = $after['description'];
        $section->is_active = $after['is_active'];
        $section->data = $after['data'];
        $section->save();

        // Registro ChangeLog
        $changes = [];
        foreach ($after as $key => $newValue) {
            $oldValue = $before[$key] ?? null;
            if ($oldValue != $newValue) {
                $changes[$key] = ['from' => $oldValue, 'to' => $newValue];
            }
        }

        if (! empty($changes)) {
            \App\Models\ChangeLog::create([
                'user_id' => $this->userId,
                'model_type' => LandingSection::class,
                'model_id' => $section->id,
                'action' => $section->wasRecentlyCreated ? 'create' : 'update',
                'changes' => $changes,
            ]);
        }

        Notification::make()
            ->title('Multimedia guardada correctamente')
            ->success()
            ->send();

        if (! Auth::user()->wizard_completed) {
            $this->redirectRoute('filament.admin.pages.wizard');
        }
    }

    public static function canAccess(): bool
    {
        return Auth::check();
    }
}
