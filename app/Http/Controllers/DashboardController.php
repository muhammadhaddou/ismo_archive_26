<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use App\Models\Document;
use App\Models\Movement;
use App\Models\Validation;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_stagiaires'  => Trainee::count(),

            'bac_temp_out'      => Document::where('type', 'Bac')
                                           ->where('status', 'Temp_Out')
                                           ->count(),

            'bac_final_out'     => Document::where('type', 'Bac')
                                           ->where('status', 'Final_Out')
                                           ->count(),

            // 🔴 عدد المتأخرين (48h أو حسب deadline)
            'bac_expired'       => Document::where('type', 'Bac')
                                           ->where('status', 'Temp_Out')
                                           ->whereHas('movements', function ($q) {
                                               $q->where('action_type', 'Sortie')
                                                 ->whereNotNull('deadline')
                                                 ->where('deadline', '<', now());
                                           })
                                           ->count(),

            'diplomes_prets'    => Document::where('type', 'Diplome')
                                           ->where('status', 'Stock')
                                           ->count(),

            'mouvements_today'  => Movement::whereDate('date_action', today())
                                           ->count(),

            'total_validations' => Validation::count(),
        ];

        $recent_movements = Movement::with(['document.trainee', 'user'])
                                    ->latest('date_action')
                                    ->take(10)
                                    ->get();

        $bac_alerts = Document::with('trainee')
                              ->where('type', 'Bac')
                              ->where('status', 'Temp_Out')
                              ->get();

        return view('dashboard', compact('stats', 'recent_movements', 'bac_alerts'));
    }
}