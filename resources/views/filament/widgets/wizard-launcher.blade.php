<x-filament::card>
    <h2 class="font-bold text-lg">Wizard de configuración</h2>
    <p class="text-sm text-gray-500">
        Forzar el wizard de configuración inicial (solo pruebas)
    </p>
    <x-filament::button
        color="warning"
        wire:click="launchWizard"
    >
        Ejecutar wizard
    </x-filament::button>
</x-filament::card>
