{{-- <div class="space-y-4"> --}}

    {{-- Información general --}}
    {{-- <div class="text-sm text-gray-700 dark:text-gray-300">
        <strong>Usuario:</strong>
        {{ $record->user?->name ?? '—' }}
    </div>

    <div class="text-sm text-gray-700 dark:text-gray-300">
        <strong>Acción:</strong>
        {{ $record->action }}
    </div>

    <div class="text-sm text-gray-700 dark:text-gray-300">
        <strong>Fecha:</strong>
        {{ $record->created_at?->format('d/m/Y H:i') }}
    </div> --}}

    {{-- Detalles --}}
    {{-- <div class="mt-4">
        <strong class="block mb-2">Detalles:</strong>

        <div id="changes-container" class="space-y-2 max-h-[70vh] overflow-auto"> --}}
            {{-- El contenido se rellenará con JS --}}
        {{-- </div>

    </div>
</div> --}}

{{-- Script para formatear los cambios y mostrar solo from y to --}}
{{-- <script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('changes-container');

        // Obtenemos los cambios desde Blade
        let changes = @json($record->changes);

        try {
            // Si es string JSON, parsearlo
            if (typeof changes === 'string') {
                changes = JSON.parse(changes);
            }

            // Recorrer cada campo y mostrar solo from/to
            for (const field in changes) {
                if (changes[field]?.from !== undefined || changes[field]?.to !== undefined) {

                    // From
                    if (changes[field].from !== undefined) {
                        const fromDiv = document.createElement('div');
                        fromDiv.className = 'bg-red-100 text-red-800 rounded-xl p-3 overflow-auto';
                        fromDiv.innerHTML = `<strong>From:</strong> <pre class="text-sm whitespace-pre-wrap break-words">${changes[field].from}</pre>`;
                        container.appendChild(fromDiv);
                    }

                    // To
                    if (changes[field].to !== undefined) {
                        const toDiv = document.createElement('div');
                        toDiv.className = 'bg-green-100 text-green-800 rounded-xl p-3 overflow-auto';
                        toDiv.innerHTML = `<strong>To:</strong> <pre class="text-sm whitespace-pre-wrap break-words">${changes[field].to}</pre>`;
                        container.appendChild(toDiv);
                    }
                }
            }

            // Si no hay cambios
            if (!container.hasChildNodes()) {
                container.innerHTML = '<p>No hay cambios registrados para este elemento.</p>';
            }

        } catch (e) {
            console.error('Error parseando cambios:', e);
            container.innerHTML = '<p>Error mostrando los cambios.</p>';
        }
    });
</script> --}}
