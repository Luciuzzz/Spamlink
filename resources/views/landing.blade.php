<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>{{ $settings?->meta_title ?? $settings?->company_name ?? 'Landing' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Descripción y keywords --}}
    @if(!empty($settings?->meta_description))
        <meta name="description" content="{{ $settings->meta_description }}">
    @endif
    @if(!empty($settings?->meta_keywords))
        <meta name="keywords" content="{{ $settings->meta_keywords }}">
    @endif

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:title" content="{{ $settings?->meta_title ?? $settings?->company_name ?? 'Landing' }}">
    <meta property="og:description" content="{{ $settings?->meta_description ?? '' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($settings?->meta_image_path))
        <meta property="og:image" content="{{ asset('storage/'.$settings->meta_image_path) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $settings?->twitter_title ?? $settings?->meta_title ?? $settings?->company_name ?? 'Landing' }}">
    <meta name="twitter:description" content="{{ $settings?->twitter_description ?? $settings?->meta_description ?? '' }}">
    @if(!empty($settings?->twitter_image_path))
        <meta name="twitter:image" content="{{ asset('storage/'.$settings->twitter_image_path) }}">
    @elseif(!empty($settings?->meta_image_path))
        <meta name="twitter:image" content="{{ asset('storage/'.$settings->meta_image_path) }}">
    @endif

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <!-- Font Awesome 6 Free (CDN) -->
    <link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
      crossorigin="anonymous" referrerpolicy="no-referrer" />
    {{-- Turnstile --}}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    @php
        $desktopBgValue = $settings?->bg_desktop_path;
        $desktopIsColor = $desktopBgValue
            && (str_starts_with($desktopBgValue, '#')
                || str_starts_with($desktopBgValue, 'rgb')
                || str_starts_with($desktopBgValue, 'hsl'));
        $desktopBgImage = ($desktopBgValue && ! $desktopIsColor)
            ? asset('storage/'.$desktopBgValue)
            : null;
        $desktopBgColor = $desktopIsColor ? $desktopBgValue : null;

        $mobileBgValue = $settings?->bg_mobile_path ?: $settings?->bg_desktop_path;
        $mobileIsColor = $mobileBgValue
            && (str_starts_with($mobileBgValue, '#')
                || str_starts_with($mobileBgValue, 'rgb')
                || str_starts_with($mobileBgValue, 'hsl'));
        $mobileBgImage = ($mobileBgValue && ! $mobileIsColor)
            ? asset('storage/'.$mobileBgValue)
            : null;
        $mobileBgColor = $mobileIsColor ? $mobileBgValue : null;
    @endphp

    <style>
        html, body { transform: none !important; }

        /* Fondo responsive */
        #bg {
            position: fixed;
            inset: 0;
            z-index: -10;
            background-image: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)), var(--bg-image, none);
            background-color: var(--bg-color, transparent);
            background-size: cover;
            background-position: center;
        }

        @media (max-width: 768px) {
            #bg {
                background-image: linear-gradient(rgba(0,0,0,.55), rgba(0,0,0,.55)), var(--bg-image-mobile, var(--bg-image, none));
                background-color: var(--bg-color-mobile, var(--bg-color, transparent));
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
<div
    id="bg"
    style="
        --bg-image: {{ $desktopBgImage ? "url('{$desktopBgImage}')" : 'none' }};
        --bg-color: {{ $desktopBgColor ?? 'transparent' }};
        --bg-image-mobile: {{ $mobileBgImage ? "url('{$mobileBgImage}')" : 'none' }};
        --bg-color-mobile: {{ $mobileBgColor ?? 'transparent' }};
    "
></div>

<header class="sticky top-0 z-50 h-20 flex items-center justify-center bg-transparent backdrop-blur border-b border-white/10">
    @if(!empty($settings?->logo_path))
        <img src="{{ asset('storage/'.$settings->logo_path) }}"
             alt="{{ $settings->company_name }}"
             class="max-h-14 max-w-[80%] object-contain">
    @else
        <span class="text-white font-bold">{{ $settings?->company_name ?? 'Landing' }}</span>
    @endif
</header>

<main >{{-- class="max-w-md mx-auto px-4 py-10" --}}
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
<section class="mt-8 space-y-4 max-w-md mx-auto px-4 py-10">
    @forelse ($links as $record)

        <a href="{{ $record->full_url }}"
           target="_blank"
           rel="noopener"
           class="group block w-full rounded-xl px-4 py-3
                  bg-white/15 hover:bg-white/25
                  border border-white/20 backdrop-blur
                  transform transition-all duration-300
                  hover:scale-105 hover:shadow-lg">

            <div class="flex items-center gap-3">

                {{-- ICONO --}}
                @if (!empty($record->icon_path))
                    {{-- Ícono personalizado --}}
                    <img
                        src="{{ asset('storage/' . $record->icon_path) }}"
                        alt="{{ $record->name }}"
                        class="h-6 w-6 object-contain
                               transition-transform duration-300
                               group-hover:scale-110"
                    >

                @elseif (!empty($record->icon_class))
                    {{-- Ícono Font Awesome --}}
                    <i class="{{ $record->icon_class }}"
                       style="font-size: 1.4rem; color: {{ $record->icon_color }}"
                       class="transition-transform duration-300 group-hover:scale-110"></i>

                @else
                    {{-- Fallback --}}
                    <i class="fa-solid fa-link text-white/60"
                       style="font-size: 1.4rem"
                       class="transition-transform duration-300 group-hover:scale-110"></i>
                @endif

                {{-- NOMBRE --}}
                <div class="flex-1 font-medium transition-colors duration-300
                            group-hover:text-white">
                    {{ $record->name }}
                </div>

                {{-- ACCIÓN --}}
                <div class="text-xs uppercase tracking-widest text-white/40
                            transition-colors duration-300
                            group-hover:text-white/80">
                    Abrir
                </div>

            </div>
        </a>

    @empty
        <div class="text-center text-white/80 text-sm
                    bg-white/5 rounded-xl border border-dashed border-white/20
                    max-w-md mx-auto px-4 py-10">
            No hay enlaces configurados todavía.
        </div>
    @endforelse
</section>
{{-- BLOQUES MULTIMEDIA --}}
    @if(isset($multimedia))
        <x-landing-blocks :blocks="$multimedia->data['blocks'] ?? []" />
    @endif
{{-- Formulario de contacto --}}
    <section class="mt-8 mb-10 bg-black/20 backdrop-blur p-6 rounded-xl border border-white/10 max-w-md mx-auto px-4 py-10">
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

            {{-- Turnstile --}}
            <div class="mt-4 flex flex-col items-center">
                <x-turnstile />
                <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
            </div>


            <button class="w-full bg-white text-black font-bold py-3 rounded-xl mt-4">
                Enviar
            </button>
        </form>
    </section>

</main>
{{-- Mapa FULL --}}
<section class="mt-8">
    <h2 class="text-lg font-bold mb-3 text-center">Ubicación</h2>

    <div class="w-screen flex justify-center overflow-x-hidden">
        <div
            id="mapPanel"
            class="h-[50vh] w-[90vw] border border-white/20 rounded-xl z-0">
        </div>
    </div>
</section>

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
                <a href="{{ route('dashboard') }}" 
                    target="_blank" 
                    rel="noopener noreferrer" 
                    class="px-3 py-2 rounded-lg border border-white/20 bg-white/10 hover:bg-white/20 transition">
                        Ir al panel
                </a>

            @endguest
        </div>

        {{-- DERECHA --}}
        <div class="flex-1 text-right"></div>
    </div>
</footer>

{{-- Botones flotantes --}}
<a id="whatsappBtn" 
   href="https://wa.me/{{ preg_replace('/\D+/', '', $settings->whatsapp_number ?? '') }}"
   target="_blank" rel="noopener"
   class="fixed right-6 bottom-6 z-50 w-14 h-14 flex items-center justify-center
          bg-[#25D366] text-white rounded-full
          shadow-xl backdrop-blur-md gap-2
          px-4 py-2
          transition-all duration-300 ease-in-out
          hover:shadow-lg hover:scale-105">

    <i class="fa-brands fa-whatsapp text-[28px] 
              transition-transform duration-500 ease-in-out
              hover:rotate-360"></i>
</a>

<button id="shareBtn"
    class="group fixed left-6 bottom-6 z-50 bg-white text-black
           border border-black backdrop-blur rounded-xl
           flex items-center gap-2 px-4 py-2
           shadow-md hover:shadow-lg
           transition-all duration-300 ease-in-out
           hover:scale-105">

    <!-- Texto -->
    Compartir

    <!-- Icono Font Awesome -->
    <i class="fa-solid fa-retweet text-lg
              transition-transform duration-700 ease-in-out
              group-hover:rotate-[360deg]"></i>
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
