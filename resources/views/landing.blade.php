<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $settings?->company_name ?? 'Landing' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                        <img src="{{ asset('storage/'.$link->icon_path) }}"
                             alt="{{ $link->name }}"
                             class="h-6 w-6 rounded">
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
</main>

{{-- LINKS CLÁSICOS (abajo a la derecha) --}}
<div class="fixed bottom-6 right-6 z-50 flex items-center gap-4 text-sm text-white/80">

    {{-- Contacto --}}
    <button onclick="openContactModal()" class="hover:text-white underline">
        Contacto
    </button>

    {{-- Cuenta --}}
    <details class="relative">
        <summary class="cursor-pointer hover:text-white underline list-none">
            Querés el tuyo? Registrate aquí
        </summary>

        <div class="absolute bottom-6 right-0 w-56 rounded-xl border border-white/20
                    bg-black/70 backdrop-blur shadow-xl">
            <div class="p-2 space-y-1">
                @guest
                    <a href="{{ route('login') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-white/10">
                        Iniciar sesión
                    </a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="block rounded-lg px-3 py-2 hover:bg-white/10">
                            Registrarse
                        </a>
                    @endif
                @else
                    <a href="{{ route('dashboard') }}"
                       class="block rounded-lg px-3 py-2 hover:bg-white/10">
                        Ir al panel
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="w-full text-left rounded-lg px-3 py-2 hover:bg-white/10">
                            Cerrar sesión
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </details>
</div>

{{-- WhatsApp flotante (SIN CAMBIOS) --}}
@if (!empty($settings?->whatsapp_number))
    @php
        $wa = preg_replace('/\D+/', '', $settings->whatsapp_number);
    @endphp
    <a href="https://wa.me/{{ $wa }}"
       target="_blank" rel="noopener"
       class="fixed bottom-20 right-6 rounded-full px-5 py-3 bg-green-500
              text-white font-semibold shadow-lg">
        WhatsApp
    </a>
@endif

{{-- MODAL CONTACTO --}}
<div id="contactModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70">
    <div class="w-full max-w-md rounded-xl bg-black/80 p-6 border border-white/20">
        <h2 class="text-lg font-semibold mb-4">Contacto</h2>

        <form method="POST" action="{{ route('landing.contact', $user->username) }}">
            @csrf

            <input name="name" required placeholder="Nombre"
                   class="w-full mb-3 px-3 py-2 bg-white/10 rounded">

            <input name="email" type="email" required placeholder="Email"
                   class="w-full mb-3 px-3 py-2 bg-white/10 rounded">

            <textarea name="message" required rows="4" placeholder="Mensaje"
                      class="w-full mb-3 px-3 py-2 bg-white/10 rounded"></textarea>

            <div class="mb-3">
                <div class="cf-turnstile"
                     data-sitekey="{{ config('services.turnstile.key') }}"></div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeContactModal()"
                        class="text-white/80 hover:text-white">
                    Cancelar
                </button>
                <button class="bg-white text-black px-4 py-2 rounded">
                    Enviar
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openContactModal() {
    const m = document.getElementById('contactModal');
    m.classList.remove('hidden');
    m.classList.add('flex');
}

function closeContactModal() {
    document.getElementById('contactModal').classList.add('hidden');
}
</script>

<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>

</body>
</html>
