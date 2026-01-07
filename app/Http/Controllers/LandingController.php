<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SocialLink;

class LandingController extends Controller
{
    public function show()
    {
        $settings = Setting::query()->first();
        $links = SocialLink::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
        ;

        return view('landing', compact('settings', 'links'));
    }
}
