<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureWizardCompleted
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $currentRoute = $request->route()?->getName();

        if ($request->headers->has('X-Livewire') || $request->routeIs('*livewire.update')) {
            return $next($request);
        }

        if ($user->wizard_completed) {
            if ($currentRoute === 'filament.admin.pages.wizard') {
                return redirect()->route('filament.admin.pages.dashboard');
            }

            return $next($request);
        }

        if (is_superadmin($user)) {
            return $next($request);
        }

        $steps = config('wizard.steps', []);
        $nextStep = null;

        // Encontrar el primer paso incompleto
        foreach ($steps as $stepNumber => $step) {
            if (! $step['complete']($user)) {
                $nextStep = $stepNumber;
                break;
            }
        }

        // Wizard completado → acceso total
        if (! $nextStep) {
            return $next($request);
        }

        // Rutas permitidas para el paso actual
        $allowedRoutes = $steps[$nextStep]['routes'];

        // Si la ruta actual está permitida → dejar pasar
        foreach ($allowedRoutes as $routePattern) {
            // Permite wildcard: filtra routes tipo "filament.admin.resources.social-links.*"
            $pattern = str_replace('*', '', $routePattern);
            if (str_starts_with($currentRoute, $pattern)) {
                return $next($request);
            }
        }

        // Todo lo demás → redirigir al wizard
        return redirect()->route('filament.admin.pages.wizard');
    }
}
