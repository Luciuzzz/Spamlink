<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ContactMessage;
use App\Models\Setting;
use App\Models\SocialLink;
use App\Models\LandingSection;
use Illuminate\Http\JsonResponse;

class LandingController extends Controller
{
    // "/" -> login (nunca mostrar landing global)
    public function show()
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $fallback = route('filament.admin.pages.dashboard');
        $previous = url()->previous();

        if (! $previous || $previous === url()->current()) {
            return redirect()->to($fallback);
        }

        return redirect()->to($previous);
    }

    public function lastUpdated(string $username): JsonResponse
    {
        $user = User::where('username', $username)->firstOrFail();

        $timestamps = [
            Setting::where('user_id', $user->id)->value('updated_at'),
            SocialLink::where('user_id', $user->id)->max('updated_at'),
            LandingSection::where('user_id', $user->id)->max('updated_at'),
        ];

        $latest = collect($timestamps)->filter()->max();

        return response()->json(['last_updated' => $latest]);
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
