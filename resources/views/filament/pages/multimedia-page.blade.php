<x-filament::page>
    @include('filament.components.wizard-tour', ['steps' => $wizardSteps ?? []])

    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            Guardar
        </x-filament::button>
    </form>
</x-filament::page>
