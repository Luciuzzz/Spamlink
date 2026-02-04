<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $email = strtolower($validated['email']);

        if (User::where('email', $email)->exists()) {
            return back()
                ->withErrors(['email' => 'Si los datos son correctos, ya puedes iniciar sesión.'])
                ->withInput($request->except(['password', 'password_confirmation']));
        }

        // Generar username único
        $base = Str::slug($validated['name']) ?: 'user';
        $username = $base;
        $i = 2;
        while (User::where('username', $username)->exists()) {
            $username = $base . '-' . $i;
            $i++;
        }

        try {
            $user = User::create([
                'name' => $validated['name'],
                'username' => $username,
                'email' => $email,
                'password' => Hash::make($validated['password']),
            ]);
        } catch (QueryException $e) {
            $sqlState = $e->errorInfo[0] ?? null;
            if ($sqlState === '23000') {
                return back()
                    ->withErrors(['email' => 'Si los datos son correctos, ya puedes iniciar sesión.'])
                    ->withInput($request->except(['password', 'password_confirmation']));
            }

            throw $e;
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
