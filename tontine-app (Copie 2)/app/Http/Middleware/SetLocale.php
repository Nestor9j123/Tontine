<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Vérifier la langue dans l'URL (ex: /en/dashboard, /fr/dashboard)
        if ($request->segment(1) && in_array($request->segment(1), ['en', 'fr'])) {
            $locale = $request->segment(1);
            Session::put('locale', $locale);
            App::setLocale($locale);
        }
        // Sinon, utiliser la langue de la session
        elseif (Session::has('locale')) {
            App::setLocale(Session::get('locale'));
        }
        // Sinon, utiliser la langue du navigateur
        elseif ($request->header('Accept-Language')) {
            $locale = substr($request->header('Accept-Language'), 0, 2);
            if (in_array($locale, ['en', 'fr'])) {
                App::setLocale($locale);
                Session::put('locale', $locale);
            }
        }
        // Par défaut, utiliser le français
        else {
            App::setLocale('fr');
            Session::put('locale', 'fr');
        }

        return $next($request);
    }
}
