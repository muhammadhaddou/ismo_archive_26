<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Trainee;
use Illuminate\Support\Facades\View;

class CheckTraineeSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->session()->has('trainee_id')) {
            return redirect()->route('trainee.login')->with('error', 'Veuillez vous connecter pour accéder à l\'espace stagiaire.');
        }

        $trainee = Trainee::find($request->session()->get('trainee_id'));

        if (!$trainee) {
            $request->session()->forget('trainee_id');
            return redirect()->route('trainee.login')->with('error', 'Session invalide.');
        }

        // Force password setup on first login
        if (empty($trainee->password) && !$request->routeIs('trainee.password.setup') && !$request->routeIs('trainee.password.store') && !$request->routeIs('trainee.logout')) {
            return redirect()->route('trainee.password.setup')->with('warning', 'Pour des raisons de sécurité, vous devez créer un mot de passe avant d\'accéder à votre espace.');
        }

        // Share the trainee with views
        View::share('currentTrainee', $trainee);

        return $next($request);
    }
}
