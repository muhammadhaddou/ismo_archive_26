<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Trainee;
use App\Models\Filiere;
use App\Models\Movement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        // Read type from query string OR from route defaults (e.g. ->defaults('type', 'Bac'))
        $type = $request->input('type') ?? $request->route('type');

        $documents = Document::with('trainee.filiere')
            ->when($type, fn($q) => $q->where('type', $type))
            ->when($request->search, function($q) use ($request) {
                $q->whereHas('trainee', function($q) use ($request) {
                    $q->where('last_name',  'like', '%'.$request->search.'%')
                      ->orWhere('first_name','like', '%'.$request->search.'%');
                });
            })
            ->when($request->cin, function($q) use ($request) {
                $q->whereHas('trainee', function($q) use ($request) {
                    $q->where('cin', 'like', '%'.$request->cin.'%')
                      ->orWhere('cef', 'like', '%'.$request->cin.'%');
                });
            })
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->filiere_id, fn($q) =>
                $q->whereHas('trainee', fn($q) => $q->where('filiere_id', $request->filiere_id))
            )
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $filieres = Filiere::orderBy('code_filiere')->get();

        return view('documents.index', compact('documents', 'type', 'filieres'));
    }

    public function create()
    {
        $trainees = Trainee::with('filiere')->orderBy('last_name')->get();
        return view('documents.create', compact('trainees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'trainee_id'       => 'required|exists:trainees,id',
            'type'             => 'required|in:Bac,Diplome,Attestation,Bulletin',
            'level_year'       => 'nullable|in:1,2',
            'reference_number' => 'nullable|string|max:100',
            'scan_file'        => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $status = $request->type === 'Bac'
            ? ($request->bac_status ?? 'Temp_Out')
            : 'Stock';

        $scanPath = null;
        if ($request->hasFile('scan_file')) {
            $scanPath = $request->file('scan_file')->store('documents_scans', 'public');
        }

        $document = Document::create([
            'trainee_id'       => $request->trainee_id,
            'type'             => $request->type,
            'level_year'       => $request->level_year,
            'status'           => $status,
            'reference_number' => $request->reference_number,
            'scan_file'        => $scanPath,
        ]);

        $actionType = ($request->type === 'Bac' && $status !== 'Stock')
            ? 'Sortie'
            : 'Saisie';

        $deadline = ($status === 'Temp_Out') ? now()->addHours(48) : null;

        Movement::create([
            'document_id'  => $document->id,
            'user_id'      => Auth::id(),
            'action_type'  => $actionType,
            'date_action'  => now(),
            'deadline'     => $deadline,
            'observations' => match ($status) {
                'Temp_Out'  => 'Retrait temporaire (48h)',
                'Final_Out' => 'Retrait définitif',
                default     => 'Document enregistré',
            },
        ]);

        return match ($status) {
            'Temp_Out'  => redirect()->route('documents.bac.temp-out')
                ->with('success', 'Bac en retrait temporaire ✅'),

            'Final_Out' => redirect()->route('documents.bac.final-out')
                ->with('success', 'Bac en retrait définitif ✅'),

            default => redirect()->route('trainees.show', $request->trainee_id)
                ->with('success', 'Document ajouté ✅'),
        };
    }

    public function show(Document $document)
    {
        $document->load('trainee.filiere', 'movements.user');
        return view('documents.show', compact('document'));
    }

    public function sortie(Request $request, Document $document)
    {
        $request->validate([
            'action_type'  => 'required|in:Temp_Out,Final_Out',
            'observations' => 'nullable|string',
            'is_proxy'     => 'nullable|boolean',
            'proxy_name'   => 'nullable|string|required_if:is_proxy,1',
            'proxy_cin'    => 'nullable|string|required_if:is_proxy,1',
            'proxy_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120|required_if:is_proxy,1',
        ]);

        $document->update([
            'status' => $request->action_type
        ]);

        $deadline = $request->action_type === 'Temp_Out'
            ? now()->addHours(48)
            : null;

        $proxyPath = null;
        if ($request->hasFile('proxy_document')) {
            $proxyPath = $request->file('proxy_document')->store('procurations', 'public');
        }

        Movement::create([
            'document_id'         => $document->id,
            'user_id'             => Auth::id(),
            'action_type'         => 'Sortie',
            'date_action'         => now(),
            'deadline'            => $deadline,
            'observations'        => $request->observations,
            'is_proxy'            => $request->boolean('is_proxy'),
            'proxy_name'          => $request->proxy_name,
            'proxy_cin'           => $request->proxy_cin,
            'proxy_document_path' => $proxyPath,
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Sortie enregistrée ✅');
    }

    public function retour(Request $request, Document $document)
    {
        $document->update(['status' => 'Stock']);

        Movement::create([
            'document_id'  => $document->id,
            'user_id'      => Auth::id(),
            'action_type'  => 'Retour',
            'date_action'  => now(),
            'observations' => $request->observations ?? 'Retour du document',
        ]);

        return redirect()->route('documents.show', $document)
            ->with('success', 'Retour effectué ✅');
    }

    public function uploadScan(Request $request, Document $document)
    {
        $request->validate([
            'scan_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Delete old scan if exists
        if ($document->scan_file) {
            Storage::disk('public')->delete($document->scan_file);
        }

        $scanPath = $request->file('scan_file')->store('documents_scans', 'public');
        $document->update(['scan_file' => $scanPath]);

        return redirect()->route('trainees.show', $document->trainee_id)
            ->with('success', 'Scan uploadé avec succès ✅');
    }

    // 🟡 Retraits temporaires (UPDATED)
    public function tempOut(Request $request)
    {
        $filieres = Filiere::all();

        $groups = Trainee::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group');

        $years = Trainee::select('graduation_year')
            ->distinct()
            ->orderByDesc('graduation_year')
            ->pluck('graduation_year');

        $annees_etude = Trainee::select('annee_etude')
            ->whereNotNull('annee_etude')
            ->distinct()
            ->orderBy('annee_etude')
            ->pluck('annee_etude');

        $documents = Document::with(['trainee.filiere', 'movements'])
            ->where('type', 'Bac')
            ->where('status', 'Temp_Out')
            ->whereHas('latestSortie', function($q) {
                $q->where('deadline', '>=', now());
            })
            ->when($request->search, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('last_name',  'like', '%'.$request->search.'%')
                      ->orWhere('first_name','like', '%'.$request->search.'%')
                ))
            ->when($request->cin, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('cin', 'like', '%'.$request->cin.'%')
                      ->orWhere('cef', 'like', '%'.$request->cin.'%')
                ))
            ->when($request->filiere_id, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('filiere_id', $request->filiere_id)))

            ->when($request->group, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('group', $request->group)))

            ->when($request->graduation_year, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('graduation_year', $request->graduation_year)))

            ->when($request->annee_etude, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('annee_etude', $request->annee_etude)))

            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documents.temp-out', compact(
            'documents',
            'filieres',
            'groups',
            'years',
            'annees_etude'
        ));
    }

    // 🔴 Retraits écoulés (NEW)
    public function ecoule(Request $request)
    {
        $filieres = Filiere::all();
        $groups   = Trainee::distinct()->pluck('group');

        $documents = Document::with('trainee.filiere', 'movements')
            ->where('type', 'Bac')
            ->where('status', 'Temp_Out')
            ->whereHas('latestSortie', function($q) {
                $q->where('deadline', '<', now());
            })
            ->when($request->filiere_id, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('filiere_id', $request->filiere_id)))
            ->when($request->group, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('group', $request->group)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documents.ecoule', compact('documents', 'filieres', 'groups'));
    }

    // 🔴 Retraits définitifs (NEW)
    public function finalOut(Request $request)
    {
        $filieres = Filiere::all();
        $groups   = Trainee::distinct()->pluck('group');

        $documents = Document::with('trainee.filiere')
            ->where('type', 'Bac')
            ->where('status', 'Final_Out')
            ->when($request->filiere_id, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('filiere_id', $request->filiere_id)))
            ->when($request->group, fn($q) =>
                $q->whereHas('trainee', fn($q) =>
                    $q->where('group', $request->group)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documents.final-out', compact('documents', 'filieres', 'groups'));
    }

    // 🎓 Diplômes en stock
    public function prets()
    {
        $documents = Document::with('trainee.filiere')
            ->where('type', 'Diplome')
            ->where('status', 'Stock')
            ->latest()
            ->paginate(15);

        return view('documents.prets', compact('documents'));
    }
}