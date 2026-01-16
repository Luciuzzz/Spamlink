<div
    wire:ignore
    x-data="locationPicker(@entangle('data.latitude'), @entangle('data.longitude'), @entangle('data.location_text'))"
    class="relative w-full max-w-md"
>
    {{-- Hidden para enviar cualquier texto al backend --}}
    <input type="hidden" x-ref="hiddenField" wire:model="data.location_text">

    {{-- Input de búsqueda --}}
    <input
        type="text"
        x-model="term"
        placeholder="Buscar dirección…"
        class="w-full rounded-lg border px-3 py-2 mb-2
               bg-white text-gray-700 placeholder-gray-400
               focus:border-indigo-500 focus:ring-indigo-500 z-20"
        @input.debounce.400ms="search"
    />

    {{-- Lista de resultados --}}
    <ul
        x-show="results.length"
        class="absolute z-10 mt-1 w-full rounded-lg border"
        style="background-color: white !important; color: black !important;"
    >
        <template x-for="item in results" :key="item.place_id">
            <li
                class="cursor-pointer px-3 py-2 hover:bg-gray-200"
                @click="select(item)"
                x-text="item.display_name"
                style="color: black !important;"
            ></li>
        </template>
    </ul>


@once
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
function locationPicker(latRef, lngRef, textRef) {
    return {
        term: '',
        results: [],
        lat: latRef,
        lng: lngRef,
        text: textRef,
        map: null,
        marker: null,

        init() {
            this.$nextTick(() => {
                const lat = parseFloat(this.lat) || -25.3;
                const lng = parseFloat(this.lng) || -57.6;

                // Inicializar mapa
                this.map = L.map(this.$refs.map).setView([lat, lng], 15);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(this.map);

                // Inicializar marcador
                this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

                // Función para actualizar coordenadas y location_text
                const updateCoords = (lat, lng) => {
                    this.lat = lat.toFixed(7);
                    this.lng = lng.toFixed(7);

                    // Solo actualiza link si usuario no escribió texto manual
                    if (!this.term || this.term.trim() === '') {
                        this.text = `https://www.openstreetmap.org/?mlat=${lat}&mlon=${lng}#map=17/${lat}/${lng}`;
                    }
                };

                // Click en mapa
                this.map.on('click', (e) => {
                    updateCoords(e.latlng.lat, e.latlng.lng);
                    this.marker.setLatLng(e.latlng);
                });

                // Drag del marcador
                this.marker.on('dragend', (e) => {
                    const p = e.target.getLatLng();
                    updateCoords(p.lat, p.lng);
                });

                // Forzar redraw del mapa
                setTimeout(() => this.map.invalidateSize(), 300);

                // Sincronizar input manual con hidden
                this.$watch('term', val => {
                    this.text = val;
                    this.$refs.hiddenField.value = val;
                });

                this.$watch('text', val => {
                    this.$refs.hiddenField.value = val;
                });
            });
        },

        // Buscar en Nominatim
        async search() {
            if (this.term.length < 3) {
                this.results = [];
                return;
            }

            const url = `https://nominatim.openstreetmap.org/search.php?q=${encodeURIComponent(this.term)}&format=jsonv2&limit=5`;

            try {
                const res = await fetch(url, { headers: { 'User-Agent': 'MiApp/1.0' } });
                this.results = await res.json();
            } catch (e) {
                console.error('Nominatim error', e);
                this.results = [];
            }
        },

        // Seleccionar resultado del buscador
        select(item) {
            this.lat = item.lat;
            this.lng = item.lon;
            this.term = item.display_name;
            this.text = item.display_name;
            this.results = [];

            const newLatLng = L.latLng(item.lat, item.lon);
            this.marker.setLatLng(newLatLng);
            this.map.setView(newLatLng, 15);
        }
    }
}
</script>
@endpush
@endonce
