<x-guest-layout>
    <!-- Estado de la sesión (ej. mensaje de contraseña restablecida) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Nombre -->
        <div>
            <x-input-label for="name" :value="__('Nombre')" />
            <x-text-input
                id="name"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
                @class([
                    'block mt-1 w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500',
                    'border-red-500' => $errors->has('name'),
                    'border-gray-300' => !$errors->has('name'),
                ])
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
                @class([
                    'block mt-1 w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500',
                    'border-red-500' => $errors->has('email'),
                    'border-gray-300' => !$errors->has('email'),
                ])
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="new-password"
                @class([
                    'block mt-1 w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500',
                    'border-red-500' => $errors->has('password'),
                    'border-gray-300' => !$errors->has('password'),
                ])
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmar Contraseña -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
            <x-text-input
                id="password_confirmation"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
                @class([
                    'block mt-1 w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500',
                    'border-red-500' => $errors->has('password_confirmation'),
                    'border-gray-300' => !$errors->has('password_confirmation'),
                ])
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Cloudflare Turnstile -->
        <div class="mt-4 flex flex-col items-center">
            <x-turnstile :site-key="config('cloudflare-turnstile.site_key') ?? ''" />
            <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
        </div>

        <!-- Botón de Registro -->
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-amber-500 hover:text-amber-700" href="{{ route('login') }}">
                {{ __('Ya estás registrado?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Registrarse') }}
            </x-primary-button>
        </div>

        <!-- Errores generales -->
        @if ($errors->any())
            <div class="mt-4 rounded-md bg-red-50 p-4">
                <ul class="text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </form>
</x-guest-layout>
