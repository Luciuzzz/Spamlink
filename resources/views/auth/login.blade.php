<x-guest-layout>
    <!-- Estado de la sesión (ej. mensaje de contraseña restablecida) -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                @class([
                    'block mt-1 w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500',
                    'border-red-500' => $errors->has('email'),
                    'border-gray-300' => !$errors->has('email'),
                ])
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Contraseña')" />
            <x-text-input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                @class([
                    'block mt-1 w-full rounded-md focus:ring-indigo-500 focus:border-indigo-500',
                    'border-red-500' => $errors->has('password'),
                    'border-gray-300' => !$errors->has('password'),
                ])
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-amber-500">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <!-- Cloudflare Turnstile -->
        <div class="mt-4 flex flex-col items-center">
            <x-turnstile :site-key="config('cloudflare-turnstile.site_key') ?? ''" />
            <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
        </div>

        <!-- Botón de Login grande -->
        <div class="mt-6">
            <x-primary-button class="w-full justify-center py-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <!-- Enlaces auxiliares -->
        <div class="flex justify-between mt-6">
            <a class="underline text-sm text-amber-500 hover:text-amber-700" href="{{ route('register') }}">
                {{ __('¿No tienes cuenta?') }}
            </a>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-amber-500 hover:text-amber-700" href="{{ route('password.request') }}">
                    {{ __('¿Olvidaste tu contraseña?') }}
                </a>
            @endif
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
