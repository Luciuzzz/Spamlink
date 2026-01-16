<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
    wire:ignore
>
    <div
        x-data="mapPicker(
            @entangle($getStatePath()),
            @entangle($getContainer()->getStatePath() . '.'.$getLongitudeField())
        )"
        x-init="init()"
        class="space-y-2"
    >
        <div class="text-sm text-gray-600">
            Haz clic en el mapa o arrastra el marcador para seleccionar la ubicación
        </div>

        <div
            x-ref="map"
            class="w-full rounded border"
            style="height: 400px; position: relative; z-index: 0;"
        ></div>
    </div>
</x-dynamic-component>

@once
    @push('styles')
        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        />
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            function mapPicker(latRef, lngRef) {
                return {
                    lat: latRef,
                    lng: lngRef,
                    map: null,
                    marker: null,

                    init() {
                        this.$nextTick(() => {
                            const lat = parseFloat(this.lat) || -25.3;
                            const lng = parseFloat(this.lng) || -57.6;

                            // Destruye el mapa anterior si existe
                            if (this.map) {
                                this.map.remove();
                                this.map = null;
                            }

                            // Inicializa el mapa
                            this.map = L.map(this.$refs.map).setView([lat, lng], 15);

                            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                attribution: '&copy; OpenStreetMap contributors'
                            }).addTo(this.map);

                            this.marker = L.marker([lat, lng], { draggable: true }).addTo(this.map);

                            const updateCoords = (lat, lng) => {
                                this.lat = lat.toFixed(7);
                                this.lng = lng.toFixed(7);
                            };

                            this.map.on('click', e => {
                                updateCoords(e.latlng.lat, e.latlng.lng);
                                this.marker.setLatLng(e.latlng);
                            });

                            this.marker.on('dragend', e => {
                                const p = e.target.getLatLng();
                                updateCoords(p.lat, p.lng);
                            });

                            // Asegura que Leaflet renderice correctamente
                            setTimeout(() => this.map.invalidateSize(), 300);
                        });
                    },
                }
            }
        </script>
    @endpush
@endonce
