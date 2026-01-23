<x-filament::page>
    <h2 class="text-xl font-semibold mb-4">Usuarios</h2>

    {{-- Buscador --}}
    <input type="text"
           placeholder="Buscar por ID, nombre o username"
           wire:model.debounce.500ms="search"
           class="w-full mb-4 px-4 py-2 rounded-xl border border-gray-300
                  !bg-gray-900 !text-white placeholder-gray-400 focus:ring focus:ring-indigo-400 focus:outline-none">

    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100 text-gray-800">
                <th class="border p-2">ID</th>
                <th class="border p-2">Nombre</th>
                <th class="border p-2">Username</th>
                <th class="border p-2">Email</th>
                <th class="border p-2">Acción</th>
            </tr>
        </thead>
        <tbody>
        @forelse ($this->filteredUsers as $user)
            <tr class="hover:bg-gray-50">
                <td class="border p-2">{{ $user->id }}</td>
                <td class="border p-2">{{ $user->name }}</td>
                <td class="border p-2">{{ $user->username }}</td>
                <td class="border p-2">{{ $user->email }}</td>
                <td class="border p-2">
                    <x-filament::button wire:click="editUserSettings({{ $user->id }})">
                        Editar configuración
                    </x-filament::button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center py-4 text-gray-500">
                    No se encontraron usuarios.
                </td>
            </tr>
        @endforelse
    </tbody>

    </table>
</x-filament::page>
