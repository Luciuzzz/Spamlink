<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\SocialLink;


class LandingController extends Controller
{
    // "/" -> login (nunca mostrar landing global)
     public function show()
    {
        $settings = Setting::first();
        $links = SocialLink::where('is_active', true)->orderBy('order')->get();

        return view('landing', [
            'user' => null,
            'settings' => $settings,
            'links' => $links,
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

        return view('landing', [
            'user' => $user,
            'settings' => $settings,
            'links' => $links,
        ]);
    }
}
