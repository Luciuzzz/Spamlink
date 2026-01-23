<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;

if (! function_exists('is_superadmin')) {
    function is_superadmin(?User $user = null): bool
    {
        $user ??= Auth::user();
        return $user && $user->role === 'superadmin';
    }
}

if (! function_exists('is_admin')) {
    function is_admin(?User $user = null): bool
    {
        $user ??= Auth::user();
        return $user && in_array($user->role, ['admin', 'superadmin']);
    }
}
