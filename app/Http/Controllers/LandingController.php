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
        if (auth()->check()) {
            $fallback = route('filament.admin.pages.dashboard');
            $previous = url()->previous();

            return redirect()->to($previous ?: $fallback);
        }

        $settings = Setting::first() ?? new Setting();
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
        $user = User::where('username', $username)->first();

        if (! $user) {
            return view('landing-unavailable', [
                'user' => null,
                'settings' => null,
            ]);
        }

        $settings = Setting::firstOrCreate(['user_id' => $user->id]);

        if (! $settings->landing_available) {
            return view('landing-unavailable', [
                'user' => $user,
                'settings' => $settings,
            ]);
        }

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
