<x-filament::page>
    @php
        $totalSteps = 3;
        $progress = (($step + 1) / $totalSteps) * 100;
    @endphp

    {{-- Barra de progreso --}}
    <div class="w-full bg-gray-200 h-2 rounded mb-6">
        <div class="bg-primary-600 h-2 rounded transition-all duration-300"
             style="width: {{ $progress }}%">
        </div>
    </div>

    {{-- Checklist --}}
    <ul class="mb-6 space-y-1 text-sm">
        <li class="{{ ($this->checkBasicSettings(Auth::id()) && $this->checkBranding(Auth::id())) ? 'text-green-600 line-through' : '' }}">
            1. Datos básicos y branding
        </li>
        <li class="{{ $this->checkSocialLinks(Auth::id()) ? 'text-green-600 line-through' : '' }}">
            2. Redes / Links
        </li>
        <li class="{{ $this->checkSections(Auth::id()) ? 'text-green-600 line-through' : '' }}">
            3. Contenido multimedia
        </li>
    </ul>

    {{-- PASOS --}}
    <x-filament::card class="space-y-4">

        @if ($step === 0)
            <h2 class="text-xl font-bold">Configuración básica y branding</h2>
            <p>Nombre de empresa + descripción o slogan y logo.</p>

            @unless($this->checkBasicSettings(Auth::id()) && $this->checkBranding(Auth::id()))
                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.pages.my-settings') }}"
                    icon="heroicon-o-cog-6-tooth"
                >
                    Ir a Settings
                </x-filament::button>
            @endunless

        @elseif ($step === 1)
            <h2 class="text-xl font-bold">Redes / Links</h2>
            <p>Agregá al menos un link activo.</p>

            @unless($this->checkSocialLinks(Auth::id()))
                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.resources.social-links.index') }}"
                    icon="heroicon-o-link"
                >
                    Administrar Redes
                </x-filament::button>
            @endunless

        @elseif ($step === 2)
            <h2 class="text-xl font-bold">Contenido multimedia</h2>
            <p>Agregá al menos una sección activa.</p>

            @unless($this->checkSections(Auth::id()))
                <x-filament::button
                    tag="a"
                    href="{{ route('filament.admin.pages.multimedia') }}"
                    icon="heroicon-o-rectangle-stack"
                >
                    Ir a Multimedia
                </x-filament::button>
            @endunless
        @endif

        {{-- ACCIONES --}}
        <div class="flex flex-col gap-2 mt-4">

            {{-- Revalidar / Continuar --}}
            <x-filament::button
                wire:click="{{ $step === 2 ? 'finish' : 'next' }}"
                :disabled="! $this->canProceed()"
            >
                {{ $step === 2 ? 'Finalizar configuración' : 'Ya completé este paso' }}
            </x-filament::button>

            {{-- Omitir (solo testing) --}}
            <x-filament::button
                color="secondary"
                wire:click="skipStep"
                size="sm"
            >
                Omitir paso (testing)
            </x-filament::button>

        </div>

    </x-filament::card>

    <p class="mt-3 text-center text-sm text-gray-500">
        Paso {{ $step + 1 }} de {{ $totalSteps }}
    </p>
</x-filament::page>
