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
<<<<<<< HEAD
use Symfony\Component\Mime\Email;
=======
use Illuminate\Support\Str;
>>>>>>> d43994b (2)

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
<<<<<<< HEAD
            'cf-turnstile-respose' => ['required', new Turnstile()],
        ],
        [
=======
            'cf-turnstile-response' => ['required', new Turnstile()],
        ], [
>>>>>>> d43994b (2)
            'email.unique' => 'Si los datos son correctos, ya puedes iniciar sesión.',
        ]);

        // 1) Base slug desde el name
        $base = Str::slug($request->name);

        // 2) Si queda vacío (por ejemplo solo símbolos), usa fallback
        if ($base === '') {
            $base = 'user';
        }

        // 3) Asegurar unicidad: base, base-2, base-3...
        $username = $base;
        $i = 2;
        while (User::where('username', $username)->exists()) {
            $username = $base . '-' . $i;
            $i++;
        }

        $user = User::create([
            'name' => $request->name,
            'username' => $username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }

}
