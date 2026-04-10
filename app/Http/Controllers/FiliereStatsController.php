<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Trainee;
use App\Models\Document;
use Illuminate\Http\Request;

class FiliereStatsController extends Controller
{
    public function index(Filiere $filiere)
    {
        $filiere->load('secteur');

        // إحصائيات عامة
        $total_trainees  = Trainee::where('filiere_id', $filiere->id)->count();
        $diplomes        = Trainee::where('filiere_id', $filiere->id)->where('statut', 'diplome')->count();
        $en_formation    = Trainee::where('filiere_id', $filiere->id)->where('statut', 'en_formation')->count();
        $abandon         = Trainee::where('filiere_id', $filiere->id)->where('statut', 'abandon')->count();
        $valides         = Trainee::where('filiere_id', $filiere->id)->whereHas('validation')->count();

        // إحصائيات الوثائق
        $trainee_ids = Trainee::where('filiere_id', $filiere->id)->pluck('id');

        $docs_stats = [
            'Bac' => [
                'stock'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Bac')->where('status','Stock')->count(),
                'temp_out'  => Document::whereIn('trainee_id', $trainee_ids)->where('type','Bac')->where('status','Temp_Out')->count(),
                'final_out' => Document::whereIn('trainee_id', $trainee_ids)->where('type','Bac')->where('status','Final_Out')->count(),
                'total'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Bac')->count(),
            ],
            'Diplome' => [
                'stock'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Diplome')->where('status','Stock')->count(),
                'temp_out'  => Document::whereIn('trainee_id', $trainee_ids)->where('type','Diplome')->where('status','Temp_Out')->count(),
                'final_out' => Document::whereIn('trainee_id', $trainee_ids)->where('type','Diplome')->where('status','Final_Out')->count(),
                'total'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Diplome')->count(),
            ],
            'Attestation' => [
                'stock'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Attestation')->where('status','Stock')->count(),
                'final_out' => Document::whereIn('trainee_id', $trainee_ids)->where('type','Attestation')->where('status','Final_Out')->count(),
                'total'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Attestation')->count(),
            ],
            'Bulletin' => [
                'stock'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Bulletin')->where('status','Stock')->count(),
                'final_out' => Document::whereIn('trainee_id', $trainee_ids)->where('type','Bulletin')->where('status','Final_Out')->count(),
                'total'     => Document::whereIn('trainee_id', $trainee_ids)->where('type','Bulletin')->count(),
            ],
        ];

        // المتدربين المتأخرين في إرجاع الباك
        $bac_retard = Document::with('trainee', 'movements')
            ->whereIn('trainee_id', $trainee_ids)
            ->where('type', 'Bac')
            ->where('status', 'Temp_Out')
            ->whereHas('movements', fn($q) =>
                $q->where('action_type', 'Sortie')
                  ->where('deadline', '<', now()))
            ->get();

        // المجموعات
        $groups = Trainee::where('filiere_id', $filiere->id)
            ->select('group')
            ->selectRaw('count(*) as total')
            ->selectRaw('sum(case when statut = "diplome" then 1 else 0 end) as diplomes')
            ->selectRaw('sum(case when statut = "en_formation" then 1 else 0 end) as en_formation')
            ->groupBy('group')
            ->orderBy('group')
            ->get();

        // آخر 10 متدربين أضيفوا
        $recent_trainees = Trainee::where('filiere_id', $filiere->id)
            ->with('documents')
            ->latest()
            ->take(10)
            ->get();

        return view('filieres.stats', compact(
            'filiere',
            'total_trainees',
            'diplomes',
            'en_formation',
            'abandon',
            'valides',
            'docs_stats',
            'bac_retard',
            'groups',
            'recent_trainees'
        ));
    }
}