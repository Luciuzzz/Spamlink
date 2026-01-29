<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
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

        // Generar username único
        $base = Str::slug($validated['name']) ?: 'user';
        $username = $base;
        $i = 2;
        while (User::where('username', $username)->exists()) {
            $username = $base . '-' . $i;
            $i++;
        }

        $user = User::create([
            'name' => $validated['name'],
            'username' => $username,
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard');
    }
}
