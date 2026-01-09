<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Panel de bienvenida --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("¡Has iniciado sesión con éxito!") }}
                    
                    <!-- Botón para ir a Filament Admin -->
                    <div class="mt-6 border-t pt-4">
                        <p class="mb-4 text-sm text-gray-600">
                            ¿Eres administrador? Accede a las herramientas de gestión:
                        </p>
                        <a href="/admin"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Ir al Panel Admin
                        </a>
                    </div>
                </div>
            </div>

            {{-- NUEVO: Cuadro Landing Pública --}}
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-2">Tu landing pública</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        Comparte tu landing con clientes o amigos usando el enlace.
                    </p>

                    <div class="flex gap-3 items-center">
                        <!-- Botón para abrir landing -->
                        <a href="{{ url('/u/' . auth()->user()->username) }}" target="_blank"
                           class="flex-1 inline-flex justify-center px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                           Abrir Landing
                        </a>

                        <!-- Botón para copiar link con tooltip -->
                        <div class="relative flex-1">
                            <button id="copyButton"
                                    class="w-full inline-flex justify-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Copiar link
                            </button>
                            <!-- Tooltip oculto -->
                            <span id="copyTooltip"
                                  class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-2 py-1 bg-gray-800 text-white text-xs rounded opacity-0 transition-opacity duration-300">
                                Copiado!
                            </span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</x-app-layout>

<script>
const copyButton = document.getElementById('copyButton');
const tooltip = document.getElementById('copyTooltip');

copyButton.addEventListener('click', () => {
    const link = "{{ url('/u/' . auth()->user()->username) }}";

    navigator.clipboard.writeText(link).then(() => {
        // Mostrar tooltip
        tooltip.classList.remove('opacity-0');
        tooltip.classList.add('opacity-100');

        // Ocultar tooltip después de 1.5s
        setTimeout(() => {
            tooltip.classList.remove('opacity-100');
            tooltip.classList.add('opacity-0');
        }, 1500);
    }).catch(() => {
        alert("No se pudo copiar el link.");
    });
});
</script>
