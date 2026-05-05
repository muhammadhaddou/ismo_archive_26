<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentRequest;
use App\Models\DocumentAvailability;
use Illuminate\Support\Facades\View;

class TraineePortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $trainee = View::shared('currentTrainee');
        
        $requests = DocumentRequest::where('trainee_id', $trainee->id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        // Availability settings
        $availabilities = DocumentAvailability::all()->keyBy('document_type');

        return view('portal.dashboard', compact('trainee', 'requests', 'availabilities'));
    }

    public function storeRequest(Request $request)
    {
        $request->validate([
            'document_type' => 'required|string|in:Bac,Diplome,Attestation,Bulletin,Certificat de scolarité',
            'bac_type' => 'nullable|string'
        ]);

        $trainee = View::shared('currentTrainee');
        
        $finalType = $request->document_type;
        if ($finalType === 'Bac' && $request->filled('bac_type')) {
            $finalType .= $request->bac_type; // e.g., "Bac - Temporaire" or "Bac - Définitif"
        }

        // Check if there's already a pending or scheduled request for this exact type
        $existing = DocumentRequest::where('trainee_id', $trainee->id)
            ->where('document_type', $finalType)
            ->whereIn('status', ['en_attente', 'planifie'])
            ->first();

        if ($existing) {
            return back()->with('error', 'Vous avez déjà une demande en cours pour ce document.');
        }

        DocumentRequest::create([
            'trainee_id' => $trainee->id,
            'document_type' => $finalType,
            'status' => 'en_attente',
        ]);

        return back()->with('success', 'Votre demande a été envoyée avec succès.');
    }
}
