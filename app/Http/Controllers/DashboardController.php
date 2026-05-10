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

            'total_stagiaires' => Trainee::query()->count(),

            'bac_temp_out' => Document::query()->where('type', 'Bac')
                ->where('status', 'Temp_Out')
                ->count(),

            'bac_final_out' => Document::query()->where('type', 'Bac')
                ->where('status', 'Final_Out')
                ->count(),

            'bac_expired' => Document::query()->where('type', 'Bac')
                ->where('status', 'Temp_Out')
                ->whereHas('latestSortie', function ($q) {
                    $q->where('deadline', '<', now());
                })
                ->count(),

            'diplomes_prets' => Document::query()->where('type', 'Diplome')
                ->where('status', 'Stock')
                ->count(),

            'diplomes_en_attente' => Trainee::query()->where('statut', 'diplome')
                ->whereDoesntHave('validation')
                ->count(),

            'mouvements_today' => Movement::query()->whereDate('date_action', today())
                ->count(),

            'total_validations' => Validation::query()->count(),
        ];

        // 🔹 10 derniers mouvements
        $recent_movements = Movement::with(['document.trainee', 'user'])
            ->latest('date_action')
            ->take(10)
            ->get();

        // 🔹 Alertes Bac (≥ 40h)
        $bac_alerts = Document::query()->where('type', 'Bac')
            ->where('status', 'Temp_Out')
            ->with(['trainee', 'latestSortie'])
            ->get()
            ->map(function ($doc) {

                $sortie = $doc->latestSortie;

                // ❗ skip si pas de sortie
                if (!$sortie) {
                    return null;
                }

                $dateAction = Carbon::parse($sortie->date_action);

                $hours = (int) $dateAction->diffInHours(now());
                $diff = $dateAction->diff(now());

                $doc->hours_out = $hours;

                // format temps
                $parts = [];
                if ($diff->d > 0) $parts[] = $diff->d . 'j';
                if ($diff->h > 0) $parts[] = $diff->h . 'h';
                if ($diff->i > 0) $parts[] = $diff->i . 'm';
                if ($diff->s > 0) $parts[] = $diff->s . 's';

                $doc->time_out_str = count($parts) ? implode(' ', $parts) : '0s';

                // alert level
                $doc->alert_level = match (true) {
                    $hours >= 48 => 'ecoule',
                    $hours >= 40 => 'danger',
                    default => 'normal',
                };

                return $doc;
            })
            ->filter() // 🔥 remove nulls (IMPORTANT FIX)
            ->filter(fn($d) => $d->hours_out >= 40)
            ->sort(fn($a, $b) => $b->hours_out <=> $a->hours_out);

        // 🔹 Documents écoulés
        $ecouleDocs = Document::query()->where('status', 'Ecoule')
            ->with('trainee')
            ->latest()
            ->get();

        // 🔹 Activité des documents (Mouvements des 7 derniers jours)
        $chart_data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Movement::query()->whereDate('date_action', $date)->count();
            $chart_data[] = $count;
        }

        // 🔹 Return view
        return view('dashboard', compact(
            'stats',
            'recent_movements',
            'bac_alerts',
            'ecouleDocs',
            'chart_data'
        ));
    }
}