<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Usuario') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("¡Has iniciado sesión con éxito!") }}

                    <!-- Botón para ir a Filament Admin -->
                    <div class="mt-6 border-t pt-4">
                        <p class="mb-4 text-sm text-gray-600">¿Eres administrador? Accede a las herramientas de gestión:</p>
                        <a href="/admin" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Ir al Panel Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
