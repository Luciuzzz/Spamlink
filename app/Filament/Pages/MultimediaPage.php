<?php

namespace App\Filament\Pages;

use App\Models\LandingSection;
use Filament\Forms;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->extraAttributes(['data-tour' => 'multimedia-title']),

                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->extraAttributes(['data-tour' => 'multimedia-description']),

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
                                    ->json()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('landing-images')
                                    ->fileAttachmentsVisibility('public')
                                    ->saveUploadedFileAttachmentUsing(function (TemporaryUploadedFile $file, Forms\Components\RichEditor $component): string {
                                        Validator::validate(
                                            ['file' => $file],
                                            [
                                                'file' => [
                                                    'image',
                                                    'max:10240',
                                                ],
                                            ],
                                            [
                                                'file.image' => 'El archivo debe ser una imagen válida.',
                                                'file.max' => 'La imagen no puede superar los 10 MB.',
                                            ],
                                        );

                                        return $file->store('landing-images', 'public');
                                    })
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript', 'link'],
                                        ['h2', 'h3', 'alignStart', 'alignCenter', 'alignEnd', 'alignJustify'],
                                        ['blockquote', 'codeBlock', 'bulletList', 'orderedList'],
                                        ['table', 'textColor', 'highlight', 'attachFiles'],
                                    ]),
                            ]),

                        Forms\Components\Builder\Block::make('image')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Activo?')
                                    ->default(true),
                                Forms\Components\FileUpload::make('images')
                                    ->label('Imágenes')
                                    ->hint('Recorte las imgenes de un tamaño similar')
                                    ->helperText('Recorta las imagenes de un tamaño similar')
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorAspectRatioOptions(['16:9'])
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('landing-images')
                                    ->rules([
                                        Rule::dimensions()
                                            ->minWidth(960)
                                            ->minHeight(540)
                                            ->maxWidth(1920)
                                            ->maxHeight(1080),
                                    ])
                                    ->validationMessages([
                                        'dimensions' => 'La imagen debe medir entre 960x540 y 1920x1080 px.',
                                    ])
                                    ->multiple(),
                            ]),

                        Forms\Components\Builder\Block::make('video')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('¿Activo?')
                                    ->default(true),
                                Forms\Components\TextInput::make('embed_url')
                                    ->label('URL del video')
                                    ->placeholder('Url de Youtube')
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

    protected function getViewData(): array
    {
        return [
            'wizardSteps' => [
                [
                    'selector' => '[data-tour="multimedia-title"]',
                    'title' => 'Título de la sección',
                    'body' => 'Aparece como encabezado del bloque multimedia en la landing.',
                    'required' => 'Obligatorio',
                ],
                [
                    'selector' => '[data-tour="multimedia-description"]',
                    'title' => 'Descripción de la sección',
                    'body' => 'Texto corto que acompaña al título (opcional, pero recomendado).',
                    'required' => 'Opcional',
                ],
                [
                    'selector' => '[data-tour="multimedia-blocks"]',
                    'title' => 'Bloques multimedia',
                    'body' => 'Agregá bloques de texto, imágenes o video para tu landing.',
                    'required' => 'Al menos un bloque activo con contenido',
                ],
            ],
        ];
    }
}
