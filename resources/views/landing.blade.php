<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $settings->meta_title ?? $settings->company_name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Descripción y keywords --}}
    @if(!empty($settings?->meta_description))
    <meta name="description" content="{{ $settings->meta_description }}">
    @endif
    @if(!empty($settings?->meta_keywords))
    <meta name="keywords" content="{{ $settings->meta_keywords }}">
    @endif

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:title" content="{{ $settings->meta_title ?? $settings->company_name }}">
    <meta property="og:description" content="{{ $settings->meta_description ?? '' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($settings?->meta_image_path))
    <meta property="og:image" content="{{ asset('storage/'.$settings->meta_image_path) }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    @endif

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $settings->meta_title ?? $settings->company_name }}">
    <meta name="twitter:description" content="{{ $settings->meta_description ?? '' }}">
    @if(!empty($settings?->meta_image_path))
    <meta name="twitter:image" content="{{ asset('storage/'.$settings->meta_image_path) }}">
    @endif

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    {{-- Turnstile --}}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    <style>
        html, body {
            transform: none !important;
        }

        /* Fondo responsive */
        #bg {
            position: fixed;
            inset: 0;
            z-index: -10;
            background-image: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)), url('{{ $settings?->bg_desktop_path ? asset('storage/'.$settings->bg_desktop_path) : '' }}');
            background-size: cover;
            background-position: center;
        }

        @media (max-width: 768px) {
            #bg {
                background-image: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)), url('{{ $settings?->bg_mobile_path
                    ? asset('storage/'.$settings->bg_mobile_path)
                    : ($settings?->bg_desktop_path ? asset('storage/'.$settings->bg_desktop_path) : '') }}');
            }
        }
    </style>

    {{-- Favicon --}}
    @if(!empty($settings?->favicon_path))
        <link rel="icon" type="image/png" href="{{ asset('storage/'.$settings->favicon_path) }}">
    @endif
</head>

<header class="sticky top-0 z-50 h-20 flex items-center justify-center bg-transparent backdrop-blur border-b border-white/10">
    @if(!empty($settings?->logo_path))
        <img
            src="{{ asset('storage/'.$settings->logo_path) }}"
            alt="{{ $settings->company_name }}"
            class="max-h-14 max-w-[80%] object-contain"
        >
    @else
        <span class="text-white font-bold">
            {{ $settings->company_name }}
        </span>
    @endif
</header>

<body class="min-h-screen text-white">

{{-- Fondo --}}
<div id="bg"></div>

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

    <p id="address-display" class="mt-4 text-sm text-white/80">
        Dirección: {{ $settings->location_text ?? 'Cargando...' }}
    </p>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const addressEl = document.getElementById('address-display');
    const LAT = {{ $settings->latitude ?? '-25.3' }};
    const LNG = {{ $settings->longitude ?? '-57.6' }};
    const LOCATION_TEXT = "{{ $settings->location_text ?? '' }}";

    // Si ya hay location_text, no hacemos reverse geocoding
    if (!LOCATION_TEXT) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${LAT}&lon=${LNG}&zoom=18&addressdetails=1`, {
            headers: { 'User-Agent': 'MiApp/1.0 (tuemail@ejemplo.com)' }
        })
        .then(res => res.json())
        .then(data => {
            addressEl.textContent = data.display_name
                ? "Dirección: " + data.display_name
                : "Dirección: No disponible";
        })
        .catch(err => {
            console.error('Error Nominatim:', err);
            addressEl.textContent = "Dirección: No disponible";
        });
    }
});
</script>

    </section>

    {{-- Links --}}
@php
    $defaultIcons = [
        'facebook' => asset('icons/facebook.png'),
        'instagram' => asset('icons/instagram.png'),
        'twitter' => asset('icons/twitter.png'),
        'whatsapp' => asset('icons/whatsapp.png'),
        'linkedin' => asset('icons/linkedin.png'),
    ];
@endphp
    <section class="mt-8 space-y-3">
        @forelse ($links as $link)
            <a href="{{ $link->full_url }}" target="_blank" rel="noopener"
            class="block w-full rounded-xl px-4 py-3
                    bg-white/15 hover:bg-white/25
                    border border-white/20 backdrop-blur transition-all duration-200">
                <div class="flex items-center gap-3">
                    <img src="{{
                        $link->icon_path
                            ? asset('storage/'.$link->icon_path)
                            : (
                                $link->icon_preset
                                    ? asset('icons/'.$link->icon_preset.'.png')
                                    : asset('icons/link.png')
                            )
                    }}"
                    alt="{{ $link->name }}"
                    class="h-6 w-6 object-contain">

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

    {{-- Mapa --}}
    <section class="mt-8 z-0">
        <h2 class="text-lg font-bold mb-3">Ubicación</h2>
        <div id="mapPanel" class="w-full h-[50vh] border border-white/20 rounded-xl overflow-hidden z-0"></div>

    </section>

    {{-- Formulario de contacto --}}
    <section class="mt-8 mb-10 bg-black/20 backdrop-blur p-6 rounded-xl border border-white/10">
        <h2 class="text-lg font-bold mb-3 ">Contacto</h2>

        <form method="POST" action="{{ isset($user) ? route('landing.contact', $user->username) : route('landing.contact') }}">
            @csrf

            <input name="name" required placeholder="Tu nombre"
                class="w-full mb-3 px-4 py-3 bg-white/5 border border-white/10 rounded-xl">

            <input name="email" type="email" required placeholder="Tu email"
                class="w-full mb-3 px-4 py-3 bg-white/5 border border-white/10 rounded-xl">

            <textarea name="message" required rows="4"
                placeholder="¿En qué puedo ayudarte?"
                class="w-full mb-4 px-4 py-3 bg-white/5 border border-white/10 rounded-xl"></textarea>

            <div id="turnstile-container" class="cf-turnstile mb-4" data-sitekey="{{ config('services.turnstile.site_key') }}"></div>

            <button class="w-full bg-white text-black font-bold py-3 rounded-xl">
                Enviar
            </button>
        </form>
    </section>

</main>

{{-- Footer y modales --}}
<div class="fixed bottom-6 right-6 z-50 flex items-center gap-4 text-sm text-white/80">
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

{{-- WhatsApp flotante --}}
@if (!empty($settings?->whatsapp_number))
    @php $wa = preg_replace('/\D+/', '', $settings->whatsapp_number); @endphp
    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener"
       class="fixed bottom-20 right-6 z-50 w-14 h-14 flex items-center justify-center bg-[#25D366] text-white rounded-full shadow-xl hover:scale-110 transition-transform duration-300">
        <svg xmlns="www.w3.org" 
             viewBox="0 0 24 24" 
             class="w-10 h-10" 
             fill="currentColor"
             style="overflow: visible;"> 
             <path d="M12.04 2C6.51 2 2 6.52 2 12.06c0 1.77.45 3.42 1.23 4.88L2 22l5.22-1.39A10.02 10.02 0 0 0 12.04 22c5.53 0 10.02-4.52 10.02-10.06S17.57 2 12.04 2zm0 18a8.03 8.03 0 0 1-4.3-1.22l-.25-.15-2.61.69.7-2.58-.16-.25A8.01 8.01 0 0 1 4 12.06C4 7.6 7.6 4 12.04 4c4.43 0 8.03 3.6 8.03 8.06 0 4.47-3.6 8.06-8.03 8.06zm4.53-6.49c-.25-.13-.88-.43-1.28-.51-.39-.08-.68-.1-.97.4-.3.49-1.12 1.48-1.38 1.78-.26.3-.51.34-.96.12-.45-.22-1.89-.7-2.73-2.14-.65-1.11-.18-1.71.13-2.02.28-.29.63-.37.89-.37.26 0 .54-.01.78.02.24.03.43-.1.54-.3.12-.2.4-.97.58-1.32.18-.35.09-.3.07-.3-.21-.07-.73-.28-1.01-.28-.27 0-.71.1-.96.34-.25.25-1 1.05-1 2.56 0 1.51 1.03 2.96 1.18 3.16.15.2.29.41.54.59 1.04.75 2.05 1.1 2.92 1.34.87.24 1.39.2 1.9-.1.52-.31 1.12-1.25 1.28-1.57.16-.31.11-.22.08-.18z"/>
        </svg>
    </a>
@endif

<!-- Botón de Compartir -->
<button
    id="shareBtn"
    class="fixed bottom-6 left-6 z-50
           bg-white/15 hover:bg-white/25
           border border-white/20 backdrop-blur
           text-white px-4 py-2 rounded-xl
           flex items-center gap-2 transition">
    Compartir
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" class="w-5 h-5">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 8a3 3 0 10-6 0m6 8a3 3 0 10-6 0m9-4a3 3 0 10-6 0" />
    </svg>
</button>

{{-- Scripts --}}
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Mapa y dirección
    let miniMap = null;
    const LAT = {{ $settings->latitude ?? '-25.3' }};
    const LNG = {{ $settings->longitude ?? '-57.6' }};
    const LOCATION_LINK = "{{ $settings->location_text ?? '#' }}";

    function initializeMiniMap() {
        const container = document.getElementById('mapPanel');
        if (!container) return;
        if (miniMap) { miniMap.remove(); miniMap = null; }

        miniMap = L.map(container, { zoomControl: false, attributionControl: false }).setView([LAT, LNG], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(miniMap);
        L.marker([LAT, LNG]).addTo(miniMap);

        miniMap.on('click', () => {
            if (LOCATION_LINK !== '#') window.open(LOCATION_LINK, '_blank');
        });

        setTimeout(() => miniMap.invalidateSize(), 300);
    }

    function updateAddress() {
        const addressEl = document.getElementById('address-display');
        if(!addressEl || !LAT || !LNG) return;

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

    document.addEventListener('DOMContentLoaded', () => {
        initializeMiniMap();
        updateAddress();
    });

    // Share button
    document.getElementById('shareBtn').addEventListener('click', async () => {
        const data = {
            title: "{{ $settings->meta_title ?? $settings->company_name }}",
            text: "{{ $settings->meta_description ?? '' }}",
            url: "{{ url()->current() }}"
        };

        if (navigator.share) {
            try {
                await navigator.share(data);
            } catch (e) {}
        } else {
            await navigator.clipboard.writeText(data.url);
            alert('Enlace copiado al portapapeles');
        }
    });
    document.addEventListener('DOMContentLoaded', () => {
    const addressEl = document.getElementById('address-display');
    if (!addressEl) return;

    // PASAR EL TEXTO DE MANERA SEGURA A JS
    const LOCATION_TEXT = @json($settings->location_text ?? '');
    const LAT = @json($settings->latitude ?? null);
    const LNG = @json($settings->longitude ?? null);

    if (LOCATION_TEXT && LOCATION_TEXT.trim() !== '') {
        // Mostrar lo que se puso en el input location_text
        addressEl.textContent = "Dirección: " + LOCATION_TEXT;
    } else if (LAT && LNG) {
        // Si no hay location_text, hacemos reverse geocoding con lat/lng
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
    } else {
        addressEl.textContent = "Dirección: No disponible";
    }
});
</script>
</body>
</html>
