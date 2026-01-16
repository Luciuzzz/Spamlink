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
        html, body { transform: none !important; }

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
                background-image: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)), url('{{ $settings?->bg_mobile_path ? asset('storage/'.$settings->bg_mobile_path) : ($settings?->bg_desktop_path ? asset('storage/'.$settings->bg_desktop_path) : '') }}');
            }
        }
    </style>

    {{-- Favicon --}}
    @if(!empty($settings?->favicon_path))
        <link rel="icon" type="image/png" href="{{ asset('storage/'.$settings->favicon_path) }}">
    @endif
</head>

<body class="min-h-screen text-white">

{{-- Fondo --}}
<div id="bg"></div>

<header class="sticky top-0 z-50 h-20 flex items-center justify-center bg-transparent backdrop-blur border-b border-white/10">
    @if(!empty($settings?->logo_path))
        <img src="{{ asset('storage/'.$settings->logo_path) }}"
             alt="{{ $settings->company_name }}"
             class="max-h-14 max-w-[80%] object-contain">
    @else
        <span class="text-white font-bold">{{ $settings->company_name }}</span>
    @endif
</header>

<main class="max-w-md mx-auto px-4 py-10">

    {{-- Header --}}
    <section class="text-center">
        <h1 class="text-3xl font-bold">{{ $settings?->company_name ?? 'Empresa' }}</h1>

        @if(!empty($settings?->slogan))
            <p class="mt-2 text-white/90">{{ $settings->slogan }}</p>
        @endif

        @if(!empty($settings?->description))
            <p class="mt-3 text-sm text-white/80">{{ $settings->description }}</p>
        @endif

        {{-- DIRECCIÓN --}}
        <p id="address-display" class="mt-4 text-sm text-white/80">
            Dirección: {{ !empty($settings->location_text) ? $settings->location_text : 'Cargando dirección…' }}
        </p>
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
                    <img src="{{ $link->icon_path ? asset('storage/'.$link->icon_path) : ($link->icon_preset ? asset('icons/'.$link->icon_preset.'.png') : asset('icons/link.png')) }}"
                         alt="{{ $link->name }}"
                         class="h-6 w-6 object-contain">
                    <div class="flex-1 font-medium">{{ $link->name }}</div>
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
        <h2 class="text-lg font-bold mb-3">Contacto</h2>

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

{{-- Footer --}}
<footer id="landingFooter" class="relative w-full mt-12 bg-black/40 backdrop-blur border-t border-white/10 text-white text-sm">
    <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">

        {{-- IZQUIERDA --}}
        <div class="flex-1 text-left text-white/70">
            &copy; {{ date('Y') }} {{ $settings->company_name ?? 'Empresa' }}. Todos los derechos reservados.
        </div>

        {{-- CENTRO --}}
        <div class="flex items-center gap-4 justify-center flex-1">
            <img src="{{ asset('icons/logo.png') }}" alt="Logo" class="h-8 w-8 object-contain">
            <span class="font-bold text-white text-lg">SpamLink</span>

            {{-- Botón central --}}
            @guest
                <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg border border-white/20 bg-white/10 hover:bg-white/20 transition">
                    ¿Querés el tuyo?
                </a>
            @else
                <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg border border-white/20 bg-white/10 hover:bg-white/20 transition">
                    Ir al panel
                </a>
            @endguest
        </div>

        {{-- DERECHA --}}
        <div class="flex-1 text-right"></div>
    </div>
</footer>

{{-- Botones flotantes --}}
<a id="whatsappBtn" href="https://wa.me/{{ preg_replace('/\D+/', '', $settings->whatsapp_number ?? '') }}"
   target="_blank" rel="noopener"
   class="fixed right-6 z-50 w-14 h-14 flex items-center justify-center bg-[#25D366] text-white rounded-full shadow-xl hover:scale-110 transition-transform duration-300">
    <img src="{{ asset('icons/wa.png') }}" alt="WhatsApp" class="w-10 h-10">
</a>

<button id="shareBtn"
    class="fixed left-6 z-50 bg-white/15 hover:bg-white/25 border border-white/20 backdrop-blur text-white px-4 py-2 rounded-xl flex items-center gap-2 transition">
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
document.addEventListener('DOMContentLoaded', () => {
    const addressEl = document.getElementById('address-display');
    const LOCATION_TEXT = @json($settings->location_text ?? '');
    const LAT = @json($settings->latitude ?? null);
    const LNG = @json($settings->longitude ?? null);

    // Dirección: location_text o reverse geocoding
    if (LOCATION_TEXT && LOCATION_TEXT.trim() !== '') {
        addressEl.textContent = "Dirección: " + LOCATION_TEXT;
    } else if (LAT && LNG) {
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${LAT}&lon=${LNG}&zoom=18`, {
            headers: { 'User-Agent': 'MiApp/1.0' }
        })
        .then(r => r.json())
        .then(d => {
            addressEl.textContent = d.display_name ? "Dirección: " + d.display_name : "Dirección: No disponible";
        })
        .catch(() => {
            addressEl.textContent = "Dirección: No disponible";
        });
    } else {
        addressEl.textContent = "Dirección: No disponible";
    }

    // Mini mapa
    if (LAT && LNG) {
        const map = L.map('mapPanel', { zoomControl: false, attributionControl: false }).setView([LAT, LNG], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        L.marker([LAT, LNG]).addTo(map);
        setTimeout(() => map.invalidateSize(), 300);
    }

    // Share button
    const shareBtn = document.getElementById('shareBtn');
    if (shareBtn) {
        shareBtn.addEventListener('click', async () => {
            const data = {
                title: "{{ $settings->meta_title ?? $settings->company_name }}",
                text: "{{ $settings->meta_description ?? '' }}",
                url: "{{ url()->current() }}"
            };
            if (navigator.share) {
                try { await navigator.share(data); } catch(e) {}
            } else {
                await navigator.clipboard.writeText(data.url);
                alert('Enlace copiado al portapapeles');
            }
        });
    }

    // Ajustar botones flotantes sobre footer
    const footer = document.getElementById('landingFooter');
    const whatsappBtn = document.getElementById('whatsappBtn');
    const defaultBottom = 24;
    const offset = 10;
    function adjustFloatingButtons() {
        if (!footer) return;
        const footerRect = footer.getBoundingClientRect();
        const viewportHeight = window.innerHeight;
        const minBottom = viewportHeight - footerRect.top + offset;
        if (whatsappBtn) whatsappBtn.style.bottom = `${Math.max(defaultBottom, minBottom)}px`;
        if (shareBtn) shareBtn.style.bottom = `${Math.max(defaultBottom, minBottom)}px`;
    }
    adjustFloatingButtons();
    window.addEventListener('scroll', adjustFloatingButtons);
    window.addEventListener('resize', adjustFloatingButtons);

});
</script>

</body>
</html>
