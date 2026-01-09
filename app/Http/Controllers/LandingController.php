<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;


class LandingController extends Controller
{
    // "/" -> login (nunca mostrar landing global)
    public function show()
    {
        return redirect()->route('login');
    }
    
    // "/u/{username}" -> landing pública del usuario
    public function showUser(string $username)
    {
        // Buscar usuario por username o fallar
        $user = User::where('username', $username)->firstOrFail();

        // Obtener configuraciones y links activos
        $settings = $user->setting;
        $links = $user->socialLinks()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Retornar la vista pasando todo lo necesario
        return view('landing', [ // <-- cambiar 'landing.user' por 'landing'
            'user' => $user,
            'settings' => $settings,
            'links' => $links,
        ]);
    }
}
