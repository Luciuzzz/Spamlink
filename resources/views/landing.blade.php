<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings?->company_name ?? 'Landing' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen text-white">
    {{-- Fondo responsive --}}
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
                url('{{ $settings?->bg_mobile_path ? asset('storage/'.$settings->bg_mobile_path) : ($settings?->bg_desktop_path ? asset('storage/'.$settings->bg_desktop_path) : '') }}') !important;
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

            @if(!empty($settings?->location_text))
                <p class="mt-4 text-sm text-white/80">
                    {{ $settings->location_text }}
                </p>
            @endif
        </section>

        {{-- Links --}}
        <section class="mt-8 space-y-3">
            @forelse ($links as $link)
                <a href="{{ $link->url }}" target="_blank" rel="noopener"
                   class="block w-full rounded-xl px-4 py-3 bg-white/15 hover:bg-white/25 border border-white/20 backdrop-blur">
                    <div class="flex items-center gap-3">
                        @if(!empty($link->icon_path))
                            <img
                                src="{{ asset('storage/'.$link->icon_path) }}"
                                alt="{{ $link->name }}"
                                class="h-6 w-6 rounded"
                            />
                        @endif

                        <div class="flex-1 font-medium">{{ $link->name }}</div>
                        <div class="text-sm text-white/80">Abrir</div>
                    </div>
                </a>
            @empty
                <div class="text-center text-white/80 text-sm">
                    No hay enlaces configurados todavía.
                </div>
            @endforelse
        </section>

        {{-- WhatsApp flotante --}}
        @if (!empty($settings?->whatsapp_number))
            @php
                $wa = preg_replace('/\D+/', '', $settings->whatsapp_number);
            @endphp
            <a href="https://wa.me/{{ $wa }}"
               target="_blank" rel="noopener"
               class="fixed bottom-6 right-6 rounded-full px-5 py-3 bg-green-500 text-white font-semibold shadow-lg">
                WhatsApp
            </a>
        @endif
    </main>
    {{-- Menú desplegable (Login / Registro) --}}
    <div class="fixed bottom-6 right-6 z-50">
        <details class="group relative">
            <summary
                class="list-none cursor-pointer select-none rounded-full bg-white/15 hover:bg-white/25
                    border border-white/20 backdrop-blur px-4 py-3 shadow-lg flex items-center gap-2">
                <span class="font-medium">Cuenta</span>

                <svg class="h-4 w-4 transition-transform group-open:rotate-180" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.25a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                </svg>
            </summary>

            <div
                class="absolute bottom-14 right-0 w-56 overflow-hidden rounded-xl border border-white/20
                    bg-black/60 backdrop-blur shadow-xl">
                <div class="p-2 space-y-1">
                    @guest
                        <a href="{{ route('login') }}"
                        class="block w-full rounded-lg px-3 py-2 text-sm hover:bg-white/10">
                            Iniciar sesión
                        </a>

                        @if (Route::has('register'))
                            <a href="{{ route('register') }}"
                            class="block w-full rounded-lg px-3 py-2 text-sm hover:bg-white/10">
                                Registrarse
                            </a>
                        @endif
                    @else
                        <a href="{{ route('dashboard') }}"
                        class="block w-full rounded-lg px-3 py-2 text-sm hover:bg-white/10">
                            Ir al panel
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                    class="w-full text-left rounded-lg px-3 py-2 text-sm hover:bg-white/10">
                                Cerrar sesión
                            </button>
                        </form>
                    @endguest
                </div>
            </div>
        </details>
    </div>

</body>
</html>
