<x-filament-panels::page>
    <form wire:submit.prevent="save" class="space-y-6">
        {{ $this->form }}

        <x-filament::button type="submit">
            Guardar
        </x-filament::button>
    </form>

    <form wire:submit.prevent="save" class="space-y-6">

        <div class="grid grid-cols-2 gap-4">

            <div>
                <label class="block font-medium text-sm text-gray-700">Ubicación</label>
                <div x-data="locationPicker()" class="relative">
                    <input type="text" x-ref="locationInput" wire:model="data.location_text"
                           placeholder="Escribí la dirección y seleccioná la opción"
                           class="w-full border rounded px-2 py-1"
                    />
                    <ul x-ref="resultsList"
                        class="absolute z-50 w-full max-h-48 overflow-y-auto bg-white text-black rounded-md shadow-lg mt-1 hidden">
                    </ul>
                </div>
            </div>

            <div>
                {{-- Aquí va tu MapPicker si quieres --}}
                {{ $this->form->getComponent('latitude') }}
            </div>

        </div>

        <x-filament::button type="submit">Guardar</x-filament::button>
    </form>

</x-filament-panels::page>


@once
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    @endpush

    @push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        // mapPicker para Alpine
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

                        if(this.map) {
                            this.map.remove();
                            this.map = null;
                        }

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
                            this.updateAddressInput(e.latlng.lat, e.latlng.lng);
                        });

                        this.marker.on('dragend', e => {
                            const p = e.target.getLatLng();
                            updateCoords(p.lat, p.lng);
                            this.updateAddressInput(p.lat, p.lng);
                        });

                        setTimeout(() => this.map.invalidateSize(), 300);
                    });
                },

                updateAddressInput(lat, lng) {
                    const input = document.querySelector('[name="address_text"]');
                    if(!input || input.value.trim() !== '') return; 

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`, {
                        headers: { 'User-Agent': 'MiApp/1.0 (tuemail@ejemplo.com)' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        input.value = data.display_name || '';
                    })
                    .catch(err => {
                        console.error('Error Nominatim:', err);
                    });
                }
            }
        }

            document.addEventListener('DOMContentLoaded', () => {
                if(window.Livewire) {
                    Livewire.hook('message.processed', (message, component) => {
                        document.querySelectorAll('[x-data]').forEach(el => {
                            if(el.__x) {
                                el.__x.initTree(el.__x.$data);
                            }
                        });
                    });
                }
            });
        </script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('locationPicker', () => ({
                init() {
                    const input = this.$refs.locationInput;
                    const list = this.$refs.resultsList;

                    input.addEventListener('input', async () => {
                        const query = input.value;
                        if(query.length < 3) {
                            list.classList.add('hidden');
                            list.innerHTML = '';
                            return;
                        }

                        try {
                            const res = await fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(query));
                            const data = await res.json();

                            list.innerHTML = '';
                            if(data.length > 0) {
                                list.classList.remove('hidden');
                                data.forEach(addr => {
                                    const li = document.createElement('li');
                                    li.className = 'px-2 py-1 hover:bg-gray-200 cursor-pointer';
                                    li.textContent = addr.display_name;
                                    li.addEventListener('click', () => {
                                        input.value = addr.display_name;

                                        if(window.Livewire) {
                                            const comp = Livewire.find(input.closest('[wire\\:id]').getAttribute('wire:id'));
                                            comp.set('data.location_text', addr.display_name);
                                            comp.set('data.latitude', addr.lat);
                                            comp.set('data.longitude', addr.lon);
                                        }

                                        list.classList.add('hidden');
                                        list.innerHTML = '';
                                    });
                                    list.appendChild(li);
                                });
                            } else {
                                list.classList.add('hidden');
                            }
                        } catch(e) {
                            console.error(e);
                        }
                    });
                    document.addEventListener('click', e => {
                        if(!input.contains(e.target) && !list.contains(e.target)) {
                            list.classList.add('hidden');
                        }
                    });
                }
            }));
        });
        </script>
    @endpush
@endonce
