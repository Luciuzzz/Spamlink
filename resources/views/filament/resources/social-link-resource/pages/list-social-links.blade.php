<x-filament-panels::page
    @class([
        'fi-resource-list-records-page',
        'fi-resource-' . str_replace('/', '-', $this->getResource()::getSlug()),
    ])
>
    @include('filament.components.wizard-tour', [
        'steps' => [
            [
                'selector' => '[data-tour="social-links-table"]',
                'title' => 'Tabla de enlaces',
                'body' => 'Acá ves todos tus links activos y podés editarlos.',
            ],
            [
                'selector' => '[data-tour="social-links-create"]',
                'title' => 'Crear enlace',
                'body' => 'Usá este botón para agregar un nuevo link a tu landing.',
            ],
        ],
    ])

    <div class="flex flex-col gap-y-6">
        <x-filament-panels::resources.tabs />

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_BEFORE, scopes: $this->getRenderHookScopes()) }}

        <div data-tour="social-links-table">
            {{ $this->table }}
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::RESOURCE_PAGES_LIST_RECORDS_TABLE_AFTER, scopes: $this->getRenderHookScopes()) }}
    </div>
</x-filament-panels::page>
