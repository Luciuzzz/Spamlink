<x-filament::page>
    @php
        $totalSteps = 4;
        $progress = (($step + 1) / $totalSteps) * 100;
        $userSettingsId = Auth::user()->settings->id ?? null;
    @endphp

    {{-- Barra de progreso --}}
    <div class="w-full bg-gray-200 h-2 rounded mb-6">
        <div class="bg-blue-500 h-2 rounded transition-all duration-300" style="width: {{ $progress }}%"></div>
    </div>

    {{-- Checklist de requisitos --}}
    <ul class="mb-6 space-y-1 text-gray-600">
        <li class="{{ $this->checkBasicSettings(Auth::id()) ? 'line-through text-green-600' : '' }}">
            Paso 1: Configuración básica (nombre + descripción/slogan)
        </li>
        <li class="{{ $this->checkBranding(Auth::id()) ? 'line-through text-green-600' : '' }}">
            Paso 2: Branding (logo)
        </li>
        <li class="{{ $this->checkSections(Auth::id()) ? 'line-through text-green-600' : '' }}">
            Paso 3: Sección multimedia activa
        </li>
        <li class="{{ $this->checkSocialLinks(Auth::id()) ? 'line-through text-green-600' : '' }}">
            Paso 4: Al menos un link social activo
        </li>
    </ul>

    {{-- Contenido de cada paso --}}
    @if ($step === 0)
        <h2 class="text-xl font-bold mb-2">Configuración básica de la landing</h2>
        <p class="mb-4">
            Completa el <strong>nombre de la empresa</strong> y la <strong>descripción o slogan</strong>.
        </p>
        @if($userSettingsId)
            <x-filament::button
                tag="a"
                href="{{ route('filament.pages.my-settings') }}"
            >
                Ir a Settings
            </x-filament::button>
        @endif

    @elseif ($step === 1)
        <h2 class="text-xl font-bold mb-2">Branding básico</h2>
        <p class="mb-4">
            Sube el <strong>logo de tu empresa</strong> para que la landing se vea presentable.
        </p>
        @if($userSettingsId)
            <x-filament::button
                tag="a"
                href="{{ route('filament.pages.my-settings') }}"
            >
                Ir a Settings
            </x-filament::button>
        @endif

    @elseif ($step === 2)
        <h2 class="text-xl font-bold mb-2">Agregar al menos una sección activa</h2>
        <p class="mb-4">
            Crea al menos una sección activa para que tu landing tenga contenido visible.
        </p>
        <x-filament::button
            tag="a"
            href="/admin/multimedia?user={{ auth()->id() }}"
        >
            Ir a Multimedia
        </x-filament::button>

    @elseif ($step === 3)
        <h2 class="text-xl font-bold mb-2">Agregar al menos un link social activo</h2>
        <p class="mb-4">
            Agrega al menos un link social activo para que los usuarios puedan contactarte.
        </p>
        
        <!-- Social Links -->
        <x-filament::button
            tag="a"
            href="{{ route('filament.admin.resources.social-links.create') }}"
            class="w-full"
        >
            Redes / Links
        </x-filament::button>
    @endif

    {{-- Botón "Omitir paso" --}}
    <div class="mt-4">
        <x-filament::button
            color="secondary"
            wire:click="skipStep"
            class="text-sm"
        >
            Omitir paso
        </x-filament::button>
    </div>

    {{-- Botón Continuar / Finalizar --}}
    <div class="mt-6">
        <x-filament::button
            :disabled="! $this->canProceed()"
            wire:click="{{ $step === 3 ? 'finish' : 'next' }}"
        >
            {{ $step === 3 ? 'Finalizar' : 'Continuar' }}
        </x-filament::button>
    </div>

    {{-- Indicador de progreso textual --}}
    <p class="mt-2 text-gray-500 text-sm">Paso {{ $step + 1 }} de {{ $totalSteps }}</p>
</x-filament::page>
