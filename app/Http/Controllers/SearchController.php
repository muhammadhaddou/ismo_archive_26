<?php

namespace App\Http\Controllers;

use App\Models\Trainee;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query   = $request->get('q');
        $results = [];

        if ($query && strlen($query) >= 2) {
            $results = Trainee::with('filiere', 'documents')
                ->where('cin', 'LIKE', "%{$query}%")
                ->orWhere('cef', 'LIKE', "%{$query}%")
                ->orWhere('first_name', 'LIKE', "%{$query}%")
                ->orWhere('last_name', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get()
                ->map(function ($t) {
                    return [
                        'id'         => $t->id,
                        'name'       => $t->last_name . ' ' . $t->first_name,
                        'cin'        => $t->cin,
                        'cef'        => $t->cef ?? '—',
                        'filiere'    => $t->filiere->nom_filiere,
                        'url'        => route('trainees.show', $t),
                        'validated'  => $t->validation ? true : false,
                        'docs_count' => $t->documents->count(),
                    ];
                });
        }

        return response()->json($results);
    }
}