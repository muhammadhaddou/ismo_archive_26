<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use App\Models\Filiere;
use App\Models\Validation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DiplomesPrêtsController extends Controller
{
    public function index(Request $request)
    {
        $filieres = Filiere::all();
        $groups   = Trainee::distinct()->pluck('group');
        $years    = Trainee::distinct()->pluck('graduation_year')->sortDesc();

        $trainees = Trainee::with('filiere', 'documents', 'validation')
            ->where('statut', 'diplome')
            ->when($request->search, fn($q) =>
                $q->where(fn($subQ) =>
                    $subQ->where('last_name', 'like', "%{$request->search}%")
                         ->orWhere('first_name', 'like', "%{$request->search}%")
                         ->orWhere('cin', 'like', "%{$request->search}%")
                         ->orWhere('cef', 'like', "%{$request->search}%")
                )
            )
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
        $trainee = Trainee::with('documents')->findOrFail($traineeId);

        // 1. Promouvoir
        $trainee->update(['statut' => 'diplome']);

        // 2. Retrait de tous les documents
        foreach ($trainee->documents as $doc) {
            if (!in_array($doc->status, ['Final_Out', 'Remis'])) {
                $doc->update(['status' => 'Remis']);
                \App\Models\Movement::create([
                    'document_id'    => $doc->id,
                    'user_id'        => Auth::id() ?? 1,
                    'action_type'    => 'Sortie',
                    'date_mouvement' => now(),
                    'notes'          => 'Remise globale suite à diplomation'
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Stagiaire promu et tous les documents ont été remis !'
        ]);
    }

    public function saveSignature(Request $request, $traineeId)
    {
        $trainee = Trainee::findOrFail($traineeId);
        $filename = 'signatures/sig_' . $traineeId . '_' . time() . '.png';

        if ($request->hasFile('signature_file')) {
            $request->validate(['signature_file' => 'image|max:2048']);
            $file = $request->file('signature_file');
            $file->storeAs('public', $filename);
        } else {
            $request->validate(['signature' => 'required|string']);
            $imageData = str_replace('data:image/png;base64,', '', $request->signature);
            $imageData = str_replace(' ', '+', $imageData);
            $decoded   = base64_decode($imageData);
            Storage::disk('public')->put($filename, $decoded);
        }

        $validation = $trainee->validation
            ?? Validation::create([
                'trainee_id'     => $traineeId,
                'date_validation'=> now(),
            ]);

        $validation->signature_path = $filename;
        $validation->save();

        return response()->json([
            'success' => true,
            'message' => 'Signature enregistrée avec succès.',
            'path'    => Storage::url($filename)
        ]);
    }
}

 
