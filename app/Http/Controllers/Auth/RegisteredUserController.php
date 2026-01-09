<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Mostrar la vista de registro.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Manejar la solicitud de registro.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validación
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'cf-turnstile-response' => ['required', new Turnstile()],
        ], [
            'email.unique' => 'Si los datos son correctos, ya puedes iniciar sesión.',
            'cf-turnstile-response.required' => 'Por favor completa el captcha para continuar.',
        ]);

        // Generar username único
        $base = Str::slug($validated['name']);
        if ($base === '') {
            $base = 'user';
        }

        $username = $base;
        $i = 2;
        while (User::where('username', $username)->exists()) {
            $username = $base . '-' . $i;
            $i++;
        }

        // Guardar usuario
        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        // Evento y login automático
        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
