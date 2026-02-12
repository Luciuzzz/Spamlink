<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ $settings?->meta_title ?? ($settings?->company_name ?? 'Landing') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    {{-- Descripción y keywords --}}
    @if (!empty($settings?->meta_description))
        <meta name="description" content="{{ $settings->meta_description }}">
    @endif
    @if (!empty($settings?->meta_keywords))
        <meta name="keywords" content="{{ $settings->meta_keywords }}">
    @endif

    {{-- Open Graph / Facebook / WhatsApp --}}
    <meta property="og:title" content="{{ $settings?->meta_title ?? ($settings?->company_name ?? 'Landing') }}">
    <meta property="og:description" content="{{ $settings?->meta_description ?? '' }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    @if (!empty($settings?->meta_image_path))
        <meta property="og:image" content="{{ asset('storage/' . $settings->meta_image_path) }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
    @endif

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title"
        content="{{ $settings?->twitter_title ?? ($settings?->meta_title ?? ($settings?->company_name ?? 'Landing')) }}">
    <meta name="twitter:description"
        content="{{ $settings?->twitter_description ?? ($settings?->meta_description ?? '') }}">
    @if (!empty($settings?->twitter_image_path))
        <meta name="twitter:image" content="{{ asset('storage/' . $settings->twitter_image_path) }}">
    @elseif(!empty($settings?->meta_image_path))
        <meta name="twitter:image" content="{{ asset('storage/' . $settings->meta_image_path) }}">
    @endif

    {{-- Astral CSS --}}
    <link rel="stylesheet" href="{{ asset('landing/astral/assets/css/fontawesome-all.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('landing/astral/assets/css/main.css') }}" />
    <noscript>
        <link rel="stylesheet" href="{{ asset('landing/astral/assets/css/noscript.css') }}" />
    </noscript>

    {{-- Font Awesome 6 (CDN) --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
          crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Turnstile --}}
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

    @php
        $desktopBgValue = $settings?->bg_desktop_path;
        $desktopIsColor =
            $desktopBgValue &&
            (str_starts_with($desktopBgValue, '#') ||
                str_starts_with($desktopBgValue, 'rgb') ||
                str_starts_with($desktopBgValue, 'hsl'));
        $desktopBgImage = $desktopBgValue && !$desktopIsColor ? asset('storage/' . $desktopBgValue) : null;
        $desktopBgColor = $desktopIsColor ? $desktopBgValue : null;

        $mobileBgValue = $settings?->bg_mobile_path ?: $settings?->bg_desktop_path;
        $mobileIsColor =
            $mobileBgValue &&
            (str_starts_with($mobileBgValue, '#') ||
                str_starts_with($mobileBgValue, 'rgb') ||
                str_starts_with($mobileBgValue, 'hsl'));
        $mobileBgImage = $mobileBgValue && !$mobileIsColor ? asset('storage/' . $mobileBgValue) : null;
        $mobileBgColor = $mobileIsColor ? $mobileBgValue : null;

        $defaultBgColor = '#0b0b0b';
        if (!$desktopBgImage && !$desktopBgColor) {
            $desktopBgColor = $defaultBgColor;
        }
        if (!$mobileBgImage && !$mobileBgColor) {
            $mobileBgColor = $desktopBgColor ?? $defaultBgColor;
        }

        $overlayEnabled = $settings?->bg_overlay_enabled ?? true;
        $overlayAlpha = $overlayEnabled ? max(0.1, min(1, (float) ($settings?->bg_overlay_opacity ?? 0.55))) : 0;
    @endphp

    <link rel="stylesheet" href="{{ asset('landing/custom.css') }}" />

    {{-- Favicon --}}
    @if (!empty($settings?->favicon_path))
        <link rel="icon" type="image/png" href="{{ asset('storage/' . $settings->favicon_path) }}">
    @endif
</head>

<body class="is-preload">

    <div id="bg"
        style="
        --bg-image: {{ $desktopBgImage ? "url('{$desktopBgImage}')" : 'none' }};
        --bg-color: {{ $desktopBgColor ?? 'transparent' }};
        --bg-overlay-alpha: {{ $overlayAlpha }};
        --bg-image-mobile: {{ $mobileBgImage ? "url('{$mobileBgImage}')" : 'none' }};
        --bg-color-mobile: {{ $mobileBgColor ?? 'transparent' }};
    "
        data-bg-color="{{ $desktopBgColor ?? 'transparent' }}"
        data-bg-color-mobile="{{ $mobileBgColor ?? ($desktopBgColor ?? 'transparent') }}"
        data-has-image="{{ $desktopBgImage ? '1' : '0' }}"
        data-has-image-mobile="{{ $mobileBgImage ? '1' : ($desktopBgImage ? '1' : '0') }}"
        data-overlay-alpha="{{ $overlayAlpha }}"></div>

    @php
        $showCompanyName = $settings?->show_company_name ?? true;
    @endphp

    @include('landing.partials.header')

    <div id="wrapper">

        <nav id="nav">
        <a href="#identity" class="icon solid fa-home"></a>
            <a href="#links" class="icon solid fa-link"></a>
        <a href="#multimedia" class="icon solid fa-folder"></a>
            <a href="#contact" class="icon solid fa-envelope"></a>
        </nav>

        <div id="main">

            <section id="identity" class="panel intro">
                <div class="panel-header">
                    @if ($showCompanyName)
                        <h1>{{ $settings?->company_name ?? 'Empresa' }}</h1>
                    @endif

                    @if (!empty($settings?->slogan))
                        <p class="identity-slogan">{{ $settings->slogan }}</p>
                    @endif

                    @if (!empty($settings?->description))
                        <p class="text-muted" style="margin-top: .6rem;">
                            {{ $settings->description }}
                        </p>
                    @endif

                    @php
                        $hasLocationText = !empty($settings?->location_text);
                        $hasCoords = !empty($settings?->latitude) && !empty($settings?->longitude);
                    @endphp

                    @if ($hasLocationText || $hasCoords)
                        <p id="address-display" class="text-muted" style="margin-top: 1rem;">
                            Dirección: {{ $hasLocationText ? $settings->location_text : 'Cargando dirección…' }}
                        </p>
                    @endif
                </div>

            </section>

            <section id="links" class="panel">
                <div class="panel-header">
                    <h2>Nuestras Redes</h2>
                </div>

                <section>
                    <div class="row">
                        @forelse ($links as $record)
                        <div class="col-6 col-12-medium {{ $loop->last && $loop->odd ? 'links-center' : '' }}">
                                <a href="{{ $record->full_url }}" target="_blank" rel="noopener" class="link-card">
                                    <div class="link-row">
                                        @if (!empty($record->icon_path))
                                            <img src="{{ asset('storage/' . $record->icon_path) }}"
                                                alt="{{ $record->name }}"
                                                style="height: 28px; width: 28px; object-fit: contain;">
                                        @elseif (!empty($record->icon_class))
                                            <i class="{{ $record->icon_class }}"
                                                style="font-size: 1.2rem; color: {{ $record->icon_color }}"></i>
                                        @else
                                            <i class="fas fa-link" style="font-size: 1.1rem; color: #777777"></i>
                                        @endif

                                        <span class="link-title">{{ $record->name }}</span>
                                        <span class="link-action">Abrir</span>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="links-empty">No hay enlaces configurados todavía.</div>
                            </div>
                        @endforelse
                    </div>
                </section>
            </section>

            <section id="multimedia" class="panel">
                @if (isset($multimedia))
                    <x-landing-blocks :blocks="$multimedia->data['blocks'] ?? []" :title="$multimedia->title ?? null" :description="$multimedia->description ?? null" />
                @else
                    <p class="text-muted">No hay contenido multimedia configurado.</p>
                @endif
            </section>

            <section id="contact" class="panel">
                <div class="panel-header">
                    <h2>Contacto</h2>
                </div>

                @if (isset($user) && $user)
                    <form method="POST" action="{{ route('landing.contact', $user->username) }}">
                        @csrf

                        <div class="row">
                            <div class="col-6 col-12-medium">
                            <input name="name" required placeholder="Tu nombre" class="contact-input" />
                            </div>
                            <div class="col-6 col-12-medium">
                            <input name="email" type="email" required placeholder="Tu email" class="contact-input" />
                            </div>
                            <div class="col-12">
                            <textarea name="message" required rows="6" placeholder="¿En qué puedo ayudarte?" class="contact-input"></textarea>
                            </div>
                            <div class="col-12" style="margin-top: 1rem;">
                                <div class="mt-4 flex flex-col items-center">
                                    <x-turnstile />
                                    <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
                                </div>
                            </div>
                            <div class="col-12">
                                <input type="submit" value="Enviar" />
                            </div>
                        </div>
                    </form>
                @else
                    <p class="text-muted">Inicia sesión para enviar un mensaje.</p>
                @endif
            </section>

        </div>

    </div>

    @if ($hasCoords)
        <div id="mapSection" class="identity-map">
            <div class="identity-map__inner">
                <div id="mapPanel" class="identity-map__panel"></div>
            </div>
        </div>
    @endif

    @include('landing.partials.footer')

    @php
        $whatsappDigits = preg_replace('/\D+/', '', $settings->whatsapp_number ?? '');
    @endphp
    @if (!empty($whatsappDigits))
        <a id="whatsappBtn" href="https://wa.me/{{ $whatsappDigits }}" target="_blank" rel="noopener"
            class="floating-whatsapp">
            <i class="fab fa-whatsapp"></i>
        </a>
    @endif

    <a id="shareBtn" class="floating-share">
        Compartir
        <i class="fas fa-retweet"></i>
    </a>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('landing/astral/assets/js/jquery.min.js') }}"></script>
    <script src="{{ asset('landing/astral/assets/js/browser.min.js') }}"></script>
    <script src="{{ asset('landing/astral/assets/js/breakpoints.min.js') }}"></script>
    <script src="{{ asset('landing/astral/assets/js/util.js') }}"></script>
    <script src="{{ asset('landing/astral/assets/js/main.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const addressEl = document.getElementById('address-display');
            const LOCATION_TEXT = @json($settings->location_text ?? '');
            const LAT = @json($settings->latitude ?? null);
            const LNG = @json($settings->longitude ?? null);
            let mapInstance = null;

            if (addressEl) {
                if (LOCATION_TEXT && LOCATION_TEXT.trim() !== '') {
                    addressEl.textContent = "Dirección: " + LOCATION_TEXT;
                } else if (LAT && LNG) {
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${LAT}&lon=${LNG}&zoom=18`, {
                            headers: {
                                'User-Agent': 'MiApp/1.0'
                            }
                        })
                        .then(r => r.json())
                        .then(d => {
                            addressEl.textContent = d.display_name ?
                                "Dirección: " + d.display_name :
                                "Dirección: No disponible";
                        })
                        .catch(() => {
                            addressEl.textContent = "Dirección: No disponible";
                        });
                }
            }

            function ensureMapVisible() {
                if (!LAT || !LNG) return;
                const mapEl = document.getElementById('mapPanel');
                const mapSection = document.getElementById('mapSection');
                if (!mapEl) return;
                const activeHash = window.location.hash || '#identity';
                const shouldShow = activeHash === '#identity';
                if (mapSection) {
                    mapSection.classList.toggle('is-active', shouldShow);
                }
                const isVisible = mapEl.offsetParent !== null &&
                    mapEl.getBoundingClientRect().width > 0 &&
                    mapEl.getBoundingClientRect().height > 0;
                if (!isVisible) return;
                if (!mapInstance) {
                    mapInstance = L.map('mapPanel', {
                            zoomControl: false,
                            attributionControl: false
                        })
                        .setView([LAT, LNG], 15);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(mapInstance);
                    L.marker([LAT, LNG]).addTo(mapInstance);
                }
                setTimeout(() => mapInstance.invalidateSize(), 200);
            }

            const shareBtn = document.getElementById('shareBtn');
            if (shareBtn) {
                shareBtn.addEventListener('click', async () => {
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
            }

            const footer = document.getElementById('landingFooter');
            const whatsappBtn = document.getElementById('whatsappBtn');
            const mapSection = document.getElementById('mapSection');
            const defaultBottom = 24;
            const offset = 10;
            const mobileMapBottom = 96;

            function adjustFloatingButtons() {
                const isMobile = window.matchMedia('(max-width: 736px)').matches;
                const isMapActive = mapSection && mapSection.classList.contains('is-active');
                let targetBottom = defaultBottom;

                if (isMobile && isMapActive) {
                    targetBottom = mobileMapBottom;
                }

                if (footer) {
                    const footerRect = footer.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;
                    const minBottom = viewportHeight - footerRect.top + offset;
                    targetBottom = Math.max(targetBottom, minBottom);
                }

                if (whatsappBtn) whatsappBtn.style.bottom = `${targetBottom}px`;
                if (shareBtn) shareBtn.style.bottom = `${targetBottom}px`;
            }
            adjustFloatingButtons();
            window.addEventListener('scroll', adjustFloatingButtons);
            window.addEventListener('resize', adjustFloatingButtons);

            window.addEventListener('hashchange', () => {
                setTimeout(() => {
                    ensureMapVisible();
                    adjustFloatingButtons();
                }, 300);
            });
            window.addEventListener('resize', () => setTimeout(() => {
                ensureMapVisible();
                adjustFloatingButtons();
            }, 150));
            setTimeout(() => {
                ensureMapVisible();
                adjustFloatingButtons();
            }, 400);
        });
    </script>
</body>
</html>
