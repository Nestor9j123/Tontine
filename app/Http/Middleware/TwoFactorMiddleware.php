<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // Si l'utilisateur n'est pas connecté, laisser passer
        if (!$user) {
            return $next($request);
        }

        // Si la 2FA n'est pas activée, laisser passer
        if (!$user->google2fa_enabled) {
            return $next($request);
        }

        // Si c'est une route 2FA, laisser passer
        if ($request->routeIs('two-factor.*')) {
            return $next($request);
        }

        // Si c'est une route de déconnexion, laisser passer
        if ($request->routeIs('logout')) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a déjà validé sa 2FA dans cette session
        if (!session('2fa_verified_' . $user->id)) {
            // Rediriger vers la page de vérification 2FA
            return redirect()->route('two-factor.challenge');
        }

        return $next($request);
    }
}
