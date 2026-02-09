<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ContactMessage;
use RyanChandler\LaravelCloudflareTurnstile\Rules\Turnstile;

class LandingContactController extends Controller
{
    // Mostrar landing
    public function showLanding(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $settings = $user->setting; // Relación user->setting
        $links = $user->socialLinks; // Relación user->socialLinks

        return view('landing', compact('user', 'settings', 'links'));
    }

    // Guardar mensaje
    public function store(Request $request, string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'cf-turnstile-response' => ['bail', 'required', 'string', new Turnstile()],
        ]);

        ContactMessage::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'email' => $request->email,
            'message' => $request->message,
        ]);

        return back()->with('success', 'Mensaje enviado correctamente.');
    }
}
