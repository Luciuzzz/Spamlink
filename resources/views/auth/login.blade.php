<x-guest-layout>
    <!-- Estado de la sesión -->
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
            @error('email')
                <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
            @enderror
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
            @error('password')
                <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" name="remember"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                <span class="ms-2 text-sm text-amber-500">{{ __('Recordarme') }}</span>
            </label>
        </div>

        <!-- Turnstile -->
        <div class="mt-4 flex flex-col items-center">
            <x-turnstile :site-key="config('cloudflare-turnstile.site_key') ?? ''" />
            @error('cf-turnstile-response')
                <span class="text-sm text-red-600 mt-1 block">{{ $message }}</span>
            @enderror
        </div>

        <!-- Botón Login -->
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
    </form>
</x-guest-layout>
