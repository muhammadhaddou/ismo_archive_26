<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use App\Models\Filiere;
use App\Models\Validation;
use Illuminate\Http\Request;

class DiplomesPrêtsController extends Controller
{
    public function index(Request $request)
    {
        $filieres = Filiere::all();
        $groups   = Trainee::distinct()->pluck('group');
        $years    = Trainee::distinct()->pluck('graduation_year')->sortDesc();

        $trainees = Trainee::with('filiere', 'documents', 'validation')
            ->where('statut', 'diplome')
            ->when($request->filiere_id, fn($q) =>
                $q->where('filiere_id', $request->filiere_id))
            ->when($request->group, fn($q) =>
                $q->where('group', $request->group))
            ->when($request->graduation_year, fn($q) =>
                $q->where('graduation_year', $request->graduation_year))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('diplomes_prets.index', compact(
            'trainees', 'filieres', 'groups', 'years'
        ));
    }

    public function checkAndPromote(Request $request, $traineeId)
    {
        $trainee       = Trainee::with('documents')->findOrFail($traineeId);
        $requiredTypes = ['Bac', 'Diplome', 'Attestation', 'Bulletin'];
        $docs          = $trainee->documents->groupBy('type');

        foreach ($requiredTypes as $type) {
            if (!isset($docs[$type]) || $docs[$type]->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => "Document manquant : $type"
                ]);
            }
        }

        $trainee->update(['statut' => 'diplome']);

        return response()->json([
            'success' => true,
            'message' => 'Stagiaire promu avec succès !'
        ]);
    }

    public function saveSignature(Request $request, $traineeId)
    {
        $request->validate(['signature' => 'required|string']);

        $trainee   = Trainee::findOrFail($traineeId);
        $imageData = str_replace('data:image/png;base64,', '', $request->signature);
        $imageData = str_replace(' ', '+', $imageData);
        $decoded   = base64_decode($imageData);

        $filename = 'signatures/sig_' . $traineeId . '_' . time() . '.png';
        \Storage::disk('public')->put($filename, $decoded);

        $validation = $trainee->validation
            ?? Validation::create([
                'trainee_id'     => $traineeId,
                'date_validation'=> now(),
            ]);

        $validation->signature_path = $filename;
        $validation->save();

        return response()->json([
            'success' => true,
            'message' => 'Signature enregistrée.',
            'path'    => \Storage::url($filename)
        ]);
    }
}

 
