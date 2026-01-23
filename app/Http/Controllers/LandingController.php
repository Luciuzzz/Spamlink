<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Models\LandingSection;

class LandingController extends Controller
{
    // "/" -> login (nunca mostrar landing global)
    public function show()
    {
        $settings = Setting::first();
        $links = SocialLink::where('is_active', true)->orderBy('order')->get();
        $multimedia = null; // no mostrar multimedia global

        return view('landing', [
            'user' => null,
            'settings' => $settings,
            'links' => $links,
            'multimedia' => $multimedia,
        ]);
    }

    // Landing de un usuario
    public function showUser(string $username)
    {
        $user = User::where('username', $username)->firstOrFail();

        $settings = $user->setting;
        $links = $user->socialLinks()
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        // Filtramos multimedia solo del usuario
        $multimedia = LandingSection::where('slug', 'multimedia')
            ->where('user_id', $user->id)
            ->where('is_active', true)
            ->first();

        return view('landing', [
            'user' => $user,
            'settings' => $settings,
            'links' => $links,
            'multimedia' => $multimedia
        ]);
    }
}
