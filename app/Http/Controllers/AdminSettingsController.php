<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentAvailability;

class AdminSettingsController extends Controller
{
    public function index()
    {
        $availabilities = DocumentAvailability::all()->keyBy('document_type');
        $documentTypes = ['Bac', 'Diplome', 'Attestation', 'Bulletin', 'Certificat Scolaire'];
        
        return view('admin.settings.index', compact('availabilities', 'documentTypes'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'availabilities' => 'array',
            'availabilities.*' => 'nullable|string|max:255'
        ]);

        foreach ($request->availabilities as $type => $description) {
            DocumentAvailability::updateOrCreate(
                ['document_type' => $type],
                ['description' => $description]
            );
        }

        return back()->with('success', 'Les disponibilités ont été mises à jour avec succès.');
    }
}
