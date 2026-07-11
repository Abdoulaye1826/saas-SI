<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpFoundation\Response;
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
     * Un token CSRF invalide (419) signifie presque toujours que la session
     * a expiré pendant que l'utilisateur remplissait un formulaire. Plutôt
     * que d'afficher la page d'erreur brute, on renvoie directement vers la
     * connexion avec un message clair — le comportement attendu d'un
     * logiciel de gestion utilisé toute une journée en boutique.
     *
     * Les requêtes AJAX (Accept: application/json) gardent la réponse 419
     * standard : c'est le script public/js/session-keepalive.js qui
     * l'intercepte côté client pour appliquer le même traitement.
     */
    public function render($request, Throwable $e): Response
    {
        if ($e instanceof TokenMismatchException && ! $request->expectsJson()) {
            return redirect()->route('login')
                ->with('status', 'Votre session a expiré. Veuillez vous reconnecter.');
        }

        return parent::render($request, $e);
    }
}
