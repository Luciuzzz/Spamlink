<x-filament::card>
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold">Tu landing personal</h2>
            <p class="text-sm text-gray-500">
                Visualizá cómo ven tu página pública
            </p>
        </div>

        <a
            href="{{ $this->getLandingUrl() }}"
            target="_blank"
            class="filament-button filament-button-size-md filament-button-color-primary"
        >
            Ver mi landing
        </a>
    </div>
</x-filament::card>
