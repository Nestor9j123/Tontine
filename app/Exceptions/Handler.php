<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Rediriger vers le login si le token CSRF a expiré (erreur 419)
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        // Rediriger vers le login pour les erreurs d'authentification (erreur 401)
        if ($exception instanceof AuthenticationException) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Gérer les erreurs HTTP spécifiques
        if ($exception instanceof HttpException) {
            $statusCode = $exception->getStatusCode();
            
            // Erreur 419 (Page Expired) - redirection vers login
            if ($statusCode === 419) {
                return redirect()->route('login')->with('error', 'Votre session a expiré. Veuillez vous reconnecter.');
            }
            
            // Erreur 401 (Unauthorized) - redirection vers login
            if ($statusCode === 401) {
                return redirect()->route('login')->with('error', 'Accès non autorisé. Veuillez vous connecter.');
            }
        }

        return parent::render($request, $exception);
    }
}
