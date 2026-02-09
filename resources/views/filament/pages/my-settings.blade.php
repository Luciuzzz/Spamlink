<x-filament-panels::page>
    @include('filament.components.wizard-tour', ['steps' => $wizardSteps ?? []])

    {{ $this->form }}

    <x-filament::button wire:click="save" class="mt-6">
        Guardar
    </x-filament::button>

</x-filament-panels::page>
