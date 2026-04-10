<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Trainee;
use App\Models\Document;
use App\Models\Movement;
use App\Models\Validation;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 🔹 Statistiques générales
        $stats = [

            // Nombre total des stagiaires
            'total_stagiaires'  => Trainee::count(),

            // Nombre de Bac en sortie temporaire
            'bac_temp_out'      => Document::where('type', 'Bac')
                                           ->where('status', 'Temp_Out')
                                           ->count(),

            // Nombre de Bac sortis définitivement
            'bac_final_out'     => Document::where('type', 'Bac')
                                           ->where('status', 'Final_Out')
                                           ->count(),

            // Nombre de Bac expirés (délai dépassé)
            'bac_expired'       => Document::where('type', 'Bac')
                                           ->where('status', 'Temp_Out')
                                           ->whereHas('latestSortie', function($q) {
                                               $q->where('deadline', '<', now());
                                           })->count(),

            // Nombre de diplômes disponibles en stock
            'diplomes_prets'    => Document::where('type', 'Diplome')
                                           ->where('status', 'Stock')
                                           ->count(),

            // ✅ Diplômes en attente de validation (NEW)
            'diplomes_en_attente' => Trainee::where('statut', 'diplome')
                ->whereDoesntHave('validation')
                ->count(),

            // Nombre de mouvements aujourd'hui
            'mouvements_today'  => Movement::whereDate('date_action', today())
                                           ->count(),

            // Nombre total des validations
            'total_validations' => Validation::count(),
        ];

        // 🔹 10 derniers mouvements
        $recent_movements = Movement::with(['document.trainee', 'user'])
                                    ->latest('date_action')
                                    ->take(10)
                                    ->get();

        // 🔹 Alertes Bac (≥ 40h)
        $bac_alerts = Document::where('type', 'Bac')
                              ->where('status', 'Temp_Out')
                              ->with(['trainee', 'latestSortie'])
                              ->get()
                              ->map(function ($doc) {

                                  $sortie = $doc->latestSortie;

                                  if (!$sortie) return null;

                                  $dateAction = Carbon::parse($sortie->date_action);
                                  $diff = $dateAction->diff(now());

                                  $hours = (int) $dateAction->diffInHours(now());
                                  $doc->hours_out = $hours;

                                  $parts = [];
                                  if ($diff->d > 0) $parts[] = $diff->d . 'j';
                                  if ($diff->h > 0) $parts[] = $diff->h . 'h';
                                  if ($diff->i > 0) $parts[] = $diff->i . 'm';
                                  if ($diff->s > 0) $parts[] = $diff->s . 's';

                                  $doc->time_out_str = implode(' ', $parts) ?: '0s';

                                  $doc->alert_level = match (true) {
                                      $hours >= 48 => 'ecoule',
                                      $hours >= 40 => 'danger',
                                      default      => 'normal',
                                  };

                                  return $doc;
                              })
                              ->filter(fn($d) => $d->hours_out !== null && $d->hours_out >= 40)
                              ->sortByDesc('hours_out');

        // 🔹 Documents écoulés
        $ecouleDocs = Document::where('status', 'Ecoule')
                              ->with('trainee')
                              ->latest()
                              ->get();

        // 🔹 Return view
        return view('dashboard', compact(
            'stats',
            'recent_movements',
            'bac_alerts',
            'ecouleDocs'
        ));
    }
}