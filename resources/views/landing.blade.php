<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings?->company_name ?? 'Landing' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body class="min-h-screen text-white">

{{-- Fondo --}}
<div class="fixed inset-0 -z-10 bg-center bg-cover"
     style="
        background-image:
        linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
        url('{{ $settings?->bg_desktop_path ? asset('storage/'.$settings->bg_desktop_path) : '' }}');
     ">
</div>

<style>
@media (max-width: 768px) {
    .bg-mobile {
        background-image:
        linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)),
        url('{{ $settings?->bg_mobile_path
            ? asset('storage/'.$settings->bg_mobile_path)
            : ($settings?->bg_desktop_path ? asset('storage/'.$settings->bg_desktop_path) : '') }}') !important;
    }
}
</style>

<div class="fixed inset-0 -z-10 bg-center bg-cover bg-mobile"></div>

<main class="max-w-md mx-auto px-4 py-10">

    {{-- Header --}}
    <section class="text-center">
        <h1 class="text-3xl font-bold">
            {{ $settings?->company_name ?? 'Empresa' }}
        </h1>

        @if(!empty($settings?->slogan))
            <p class="mt-2 text-white/90">{{ $settings->slogan }}</p>
        @endif

        @if(!empty($settings?->description))
            <p class="mt-3 text-sm text-white/80">{{ $settings->description }}</p>
        @endif

        {{-- Dirección --}}
        <p id="address-display" class="mt-4 text-sm text-white/80">
            Dirección: {{ $settings->address_text ?? 'Cargando...' }}
        </p>

        {{-- Link a OpenStreetMap 
        @if(!empty($settings?->location_text))
            <p class="mt-2 text-sm text-blue-400 underline">
                <a href="{{ $settings->location_text }}" target="_blank" rel="noopener">Ver en mapa</a>
            </p>
        @endif--}}
    </section>

    {{-- Links --}}
    <section class="mt-8 space-y-3">
        @forelse ($links as $link)
            <a href="{{ $link->full_url }}"
               target="_blank"
               rel="noopener"
               class="block w-full rounded-xl px-4 py-3
                      bg-white/15 hover:bg-white/25
                      border border-white/20 backdrop-blur transition-all duration-200">

                <div class="flex items-center gap-3">
                    @if(!empty($link->icon_path))
                        <img src="{{ asset('storage/'.$link->icon_path) }}"
                             alt="{{ $link->name }}"
                             class="h-6 w-6 rounded object-cover">
                    @else
                        <div class="h-6 w-6 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                            </svg>
                        </div>
                    @endif

                    <div class="flex-1 font-medium">
                        {{ $link->name }}
                    </div>

                    <div class="text-xs uppercase tracking-widest text-white/40 group-hover:text-white/80">
                        Abrir
                    </div>
                </div>
            </a>

        @empty
            <div class="text-center text-white/80 text-sm py-10 bg-white/5 rounded-xl border border-dashed border-white/20">
                No hay enlaces configurados todavía.
            </div>
        @endforelse
    </section>

    {{-- Mini-mapa --}}
    <div
        id="mini-map"
        style="
            position: fixed;
            bottom: 1rem;
            left: 1rem;
            width: 20vw;
            height: 10vh;
            min-width: 160px;
            min-height: 100px;
            z-index: 50;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
        "
    ></div>
</main>

{{-- FOOTER Y MODALES --}}
<div class="fixed bottom-6 right-6 z-50 flex items-center gap-4 text-sm text-white/80">
    <button onclick="openContactModal()" class="hover:text-white underline">
        Contacto
    </button>

    <details class="relative">
        <summary class="cursor-pointer hover:text-white underline list-none">
            ¿Querés el tuyo? Registrate
        </summary>
        <div class="absolute bottom-10 right-0 w-56 rounded-xl border border-white/20 bg-black/90 backdrop-blur shadow-2xl p-2">
            @guest
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Iniciar sesión</a>
                <a href="{{ route('register') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Registrarse</a>
            @else
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 hover:bg-white/10">Ir al panel</a>
            @endguest
        </div>
    </details>
</div>

@if (!empty($settings?->whatsapp_number))
    @php $wa = preg_replace('/\D+/', '', $settings->whatsapp_number); @endphp
    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
       class="fixed bottom-20 right-6 z-50 w-12 h-12 flex items-center justify-center bg-green-500 text-white rounded-full shadow-lg hover:scale-110 transition-transform">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
            <path d="M20.52 3.48A11.815 11.815 0 0 0 12 0C5.372 0 0 5.372 0 12c0 2.114.553 4.078 1.52 5.77L0 24l6.44-1.498A11.944 11.944 0 0 0 12 24c6.628 0 12-5.372 12-12 0-3.21-1.243-6.218-3.48-8.52zM12 22c-1.876 0-3.637-.512-5.162-1.39l-.365-.216-3.822.889.812-3.694-.235-.372A9.943 9.943 0 0 1 2 12c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10zm5.242-7.658c-.144-.072-.852-.42-1.137-.467-.285-.048-.492-.072-.7.072-.207.144-.798.467-.978.564-.18.096-.33.144-.48-.144-.144-.288-.564-1.11-.69-1.332-.144-.216-.288-.24-.532-.096-.24.144-1.032.384-1.968-1.212-.726-1.02-1.212-2.28-1.356-2.496-.144-.216-.012-.336.108-.456.108-.108.24-.288.36-.432.12-.144.16-.24.24-.408.08-.144.04-.288-.02-.408-.064-.12-.7-1.684-.956-2.304-.252-.6-.508-.52-.7-.53-.18-.008-.384-.008-.588-.008s-.432.072-.66.288c-.228.216-.87.852-.87 2.076s.892 2.412 1.02 2.58c.12.168 1.764 2.7 4.272 3.78.6.26 1.066.416 1.428.532.6.192 1.146.164 1.578.1.482-.07 1.476-.602 1.684-1.184.204-.572.204-1.062.144-1.18-.06-.12-.216-.192-.456-.288z"/>
        </svg>
    </a>
@endif

<div id="contactModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/80 backdrop-blur-sm p-4">
    <div class="w-full max-w-md rounded-2xl bg-[#111] p-6 border border-white/10 shadow-2xl">
        <h2 class="text-xl font-bold mb-4">Enviar mensaje</h2>
        <form method="POST" action="{{ isset($user) ? route('landing.contact', $user->username) : '#' }}">
            @csrf
            <input name="name" required placeholder="Tu nombre" class="w-full mb-3 px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-white/30 outline-none">
            <input name="email" type="email" required placeholder="Tu email" class="w-full mb-3 px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-white/30 outline-none">
            <textarea name="message" required rows="4" placeholder="¿En qué puedo ayudarte?" class="w-full mb-4 px-4 py-3 bg-white/5 border border-white/10 rounded-xl focus:border-white/30 outline-none"></textarea>

            <div id="turnstile-container" class="cf-turnstile"
                data-sitekey="{{ config('services.turnstile.site_key') }}"
                data-size="invisible">
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeContactModal()" class="px-4 py-2 text-white/60 hover:text-white">Cerrar</button>
                <button class="bg-white text-black font-bold px-6 py-2 rounded-xl hover:bg-gray-200 transition">Enviar</button>
            </div>
        </form>
    </div>
</div>
<script>
function openContactModal() {
        const modal = document.getElementById('contactModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if(window.turnstile) {
            turnstile.render('#turnstile-container', {
                sitekey: "{{ config('services.turnstile.site_key') }}"
            });
        }
    }

    function closeContactModal() {
        const modal = document.getElementById('contactModal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
<script>
let miniMap = null; // Referencia global al mapa

const LAT = {{ $settings->latitude ?? '-25.3' }};
const LNG = {{ $settings->longitude ?? '-57.6' }};
const LOCATION_LINK = "{{ $settings->location_text ?? '#' }}";

function initializeMiniMap() {
    const container = document.getElementById('mini-map');
    if (!container) return;

    // Si ya hay un mapa, lo destruimos
    if (miniMap) {
        miniMap.remove();
        miniMap = null;
    }

    miniMap = L.map(container, { zoomControl: false, attributionControl: false }).setView([LAT, LNG], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);
    L.marker([LAT, LNG]).addTo(miniMap);

    miniMap.on('click', () => {
        if (LOCATION_LINK !== '#') window.open(LOCATION_LINK, '_blank');
    });

    // Forzar refresco de tamaño
    setTimeout(() => miniMap.invalidateSize(), 300);
}

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    initializeMiniMap();
    updateAddress();
});

// Función para actualizar la dirección si no existe en DB
function updateAddress() {
    const addressEl = document.getElementById('address-display');
    if(!addressEl || !LAT || !LNG) return;

    if(!addressEl.textContent || addressEl.textContent.includes('Cargando')) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${LAT}&lon=${LNG}&zoom=18&addressdetails=1`, {
            headers: { 'User-Agent': 'MiApp/1.0 (lm9034064@gmail.com)' }
        })
        .then(res => res.json())
        .then(data => {
            addressEl.textContent = data.display_name ? "Dirección: " + data.display_name : "Dirección: No disponible";
        })
        .catch(err => {
            console.error('Error Nominatim:', err);
            addressEl.textContent = "Dirección: No disponible";
        });
    }
}

// Hook de Livewire para reinicializar el mapa después de renderizar
Livewire.hook('message.processed', () => {
    // Esperamos un frame para que el DOM esté listo
    requestAnimationFrame(() => {
        initializeMiniMap();
        updateAddress();
    });
});
</script>


</body>
</html>
