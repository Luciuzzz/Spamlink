<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingController::class, 'show'])->name('home');
Route::get('/u/{username}', [LandingController::class, 'showUser'])->name('landing.user');
Route::get('/landing/{username}', [LandingController::class, 'showUser'])
    ->name('landing.show');

Route::get('/dashboard', function () {
    return view('dashboard');
})
->middleware(['auth', 'verified'])
->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/mantenimiento', function () {
        return "Solo para administradores";
    });
});

require __DIR__.'/auth.php';

Route::fallback(fn () => redirect()->route('auth.login'));
