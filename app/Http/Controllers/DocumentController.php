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
    // ═══════════════════════════════════════════════════
    //  MACHINE D'ÉTAT — transitions autorisées
    //  Stock    → Temp_Out | Final_Out
    //  Temp_Out → Stock (retour) | Final_Out (définitif)
    //  Final_Out / Remis → rien (terminal)
    // ═══════════════════════════════════════════════════
    private const TERMINAL_STATUSES = ['Final_Out', 'Remis'];

    /**
     * Crée le mouvement associé à un document et retourne le message flash.
     * Centralise la logique de Movement::create() pour éviter la duplication.
     */
    private function recordMovement(Document $document, string $actionType, array $extras = []): void
    {
        $deadline = ($actionType === 'Sortie' && ($extras['doc_status'] ?? '') === 'Temp_Out')
            ? now()->addHours(48)
            : null;

        Movement::create(array_merge([
            'document_id' => $document->id,
            'user_id'     => Auth::id(),
            'action_type' => $actionType,
            'date_action' => now(),
            'deadline'    => $deadline,
        ], $extras['movement'] ?? []));
    }

    /**
     * Applique les effets secondaires sur le stagiaire selon le statut du document.
     *
     * Règles métier :
     * - Diplome Final_Out → trainee.statut = 'diplome'
     *                     → Bac du même stagiaire passe aussi en Final_Out (automatique)
     * - Diplome revient en Stock → annule statut diplome
     *                            → Bac revient en Stock si il était Final_Out suite au diplôme
     *
     * Bac Final_Out seul (sans Diplome) = retrait pur (abandon, etc.) — géré séparément.
     */
    private function syncTraineeStatut(Document $document): void
    {
        if ($document->type !== 'Diplome') return;

        $trainee = $document->trainee;

        if (in_array($document->status, self::TERMINAL_STATUSES)) {
            // 1. Marquer le stagiaire comme diplômé
            if ($trainee->statut !== 'diplome') {
                $trainee->update(['statut' => 'diplome']);
            }

            // 2. Fermer le Bac automatiquement s'il existe et n'est pas déjà Final_Out
            $bac = $trainee->documents()->where('type', 'Bac')->first();
            if ($bac && !in_array($bac->status, self::TERMINAL_STATUSES)) {
                $bac->update(['status' => 'Final_Out']);
                // Enregistrer le mouvement automatique
                Movement::create([
                    'document_id' => $bac->id,
                    'user_id'     => Auth::id(),
                    'action_type' => 'Sortie',
                    'date_action' => now(),
                    'observations'=> 'Retrait définitif automatique — diplôme remis au stagiaire',
                ]);
            }

        } else {
            // Diplôme revenu en stock → annule statut diplômé
            if ($trainee->statut === 'diplome') {
                $trainee->update(['statut' => null]);
            }
        }
    }

    // ────────────────────────────────────────────────────
    //  INDEX
    // ────────────────────────────────────────────────────
    public function index(Request $request)
    {
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

        $filieres = Filiere::orderBy('code_filiere', 'asc')->get();

        return view('documents.index', compact('documents', 'type', 'filieres'));
    }

    // ────────────────────────────────────────────────────
    //  CREATE / STORE
    // ────────────────────────────────────────────────────
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
            'is_proxy'         => 'nullable|boolean',
            'proxy_name'       => 'nullable|string|required_if:is_proxy,1',
            'proxy_cin'        => 'nullable|string|required_if:is_proxy,1',
            'proxy_document'   => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120|required_if:is_proxy,1',
        ]);

        $trainee = Trainee::findOrFail($request->trainee_id);

        $status = $request->bac_status ?? 'Stock';
        $actionType = ($status !== 'Stock') ? 'Sortie' : 'Saisie';

        $existingDoc = $trainee->documents()->where('type', $request->type)->first();

        if ($existingDoc) {
            if ($actionType === 'Saisie') {
                return redirect()->back()->with('error',
                    "❌ Ce stagiaire possède déjà un document de type « {$request->type} » en stock."
                );
            }
            if ($existingDoc->status === 'Final_Out' || $existingDoc->status === 'Remis') {
                return redirect()->back()->with('error',
                    "❌ Ce document est déjà retiré définitivement."
                );
            }
            if ($existingDoc->status === 'Temp_Out' && $status === 'Temp_Out') {
                return redirect()->back()->with('error',
                    "❌ Ce document est déjà en retrait temporaire."
                );
            }

            $document = $existingDoc;
            $document->status = $status;
            if ($request->level_year) $document->level_year = $request->level_year;
            if ($request->reference_number) $document->reference_number = $request->reference_number;

            if ($request->hasFile('scan_file')) {
                $document->scan_file = $request->file('scan_file')->store('documents_scans', 'local');
            }
            $document->save();
        } else {
            $scanPath = null;
            if ($request->hasFile('scan_file')) {
                $scanPath = $request->file('scan_file')->store('documents_scans', 'local');
            }

            $document = Document::create([
                'trainee_id'       => $request->trainee_id,
                'type'             => $request->type,
                'level_year'       => $request->level_year,
                'status'           => $status,
                'reference_number' => $request->reference_number,
                'scan_file'        => $scanPath,
            ]);
        }

        $proxyPath = null;
        if ($request->hasFile('proxy_document')) {
            $proxyPath = $request->file('proxy_document')->store('procurations', 'local');
        }

        $this->recordMovement($document, $actionType, [
            'doc_status' => $status,
            'movement'   => [
                'observations' => match ($status) {
                    'Temp_Out'  => 'Retrait temporaire (48h)',
                    'Final_Out' => 'Retrait définitif',
                    default     => 'Document enregistré en stock',
                },
                'is_proxy'            => $request->boolean('is_proxy'),
                'proxy_name'          => $request->proxy_name,
                'proxy_cin'           => $request->proxy_cin,
                'proxy_document_path' => $proxyPath,
            ],
        ]);

        // Sync statut stagiaire si nécessaire
        $this->syncTraineeStatut($document);

        return match ($status) {
            'Temp_Out'  => redirect()->route('documents.bac.temp-out')->with('success', 'Bac en retrait temporaire ✅'),
            'Final_Out' => redirect()->route('documents.bac.final-out')->with('success', 'Bac en retrait définitif ✅'),
            default     => redirect()->route('trainees.show', $request->trainee_id)->with('success', 'Document ajouté en stock ✅'),
        };
    }

    // ────────────────────────────────────────────────────
    //  SHOW
    // ────────────────────────────────────────────────────
    public function show(Document $document)
    {
        $document->load('trainee.filiere', 'movements.user');
        return view('documents.show', compact('document'));
    }

    // ────────────────────────────────────────────────────
    //  SORTIE (Temp_Out ou Final_Out)
    // ────────────────────────────────────────────────────
    public function sortie(Request $request, Document $document)
    {
        $request->validate([
            'action_type'    => 'required|in:Temp_Out,Final_Out',
            'observations'   => 'nullable|string',
            'is_proxy'       => 'nullable|boolean',
            'proxy_name'     => 'nullable|string|required_if:is_proxy,1',
            'proxy_cin'      => 'nullable|string|required_if:is_proxy,1',
            'proxy_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120|required_if:is_proxy,1',
        ]);

        // 🚫 Document déjà en état terminal → impossible de sortir à nouveau
        if (in_array($document->status, self::TERMINAL_STATUSES)) {
            return redirect()->back()->with('error',
                '❌ Ce document a déjà été remis définitivement. Aucune nouvelle action possible.'
            );
        }

        // 🚫 Déjà en Temp_Out et on essaie de refaire Temp_Out
        if ($document->status === 'Temp_Out' && $request->action_type === 'Temp_Out') {
            return redirect()->back()->with('error',
                '⚠️ Ce document est déjà en retrait temporaire. Enregistrez son retour en stock avant une nouvelle sortie.'
            );
        }

        // 🚫 Pour un Diplôme : uniquement retrait définitif (pas temporaire)
        if ($document->type === 'Diplome' && $request->action_type === 'Temp_Out') {
            return redirect()->back()->with('error',
                '❌ Un diplôme ne peut pas être en retrait temporaire. Choisissez "Retrait définitif".'
            );
        }

        $proxyPath = null;
        if ($request->hasFile('proxy_document')) {
            $proxyPath = $request->file('proxy_document')->store('procurations', 'local');
        }

        $document->update(['status' => $request->action_type]);

        $this->recordMovement($document, 'Sortie', [
            'doc_status' => $request->action_type,
            'movement'   => [
                'observations'        => $request->observations,
                'is_proxy'            => $request->boolean('is_proxy'),
                'proxy_name'          => $request->proxy_name,
                'proxy_cin'           => $request->proxy_cin,
                'proxy_document_path' => $proxyPath,
            ],
        ]);

        // Sync statut stagiaire
        $this->syncTraineeStatut($document);

        $msg = ($document->type === 'Diplome' && $request->action_type === 'Final_Out')
            ? '🎓 Diplôme remis ! Le stagiaire passe dans la liste des diplômés.'
            : 'Sortie enregistrée ✅';

        return redirect()->route('documents.show', $document)->with('success', $msg);
    }

    // ────────────────────────────────────────────────────
    //  RETOUR (revient en Stock — uniquement depuis Temp_Out)
    // ────────────────────────────────────────────────────
    public function retour(Request $request, Document $document)
    {
        // 🚫 Impossible de faire un retour si le document est en état terminal
        if (in_array($document->status, self::TERMINAL_STATUSES)) {
            return redirect()->back()->with('error',
                '❌ Ce document a été remis définitivement. Il ne peut pas revenir en stock.'
            );
        }

        // 🚫 Déjà en stock
        if ($document->status === 'Stock') {
            return redirect()->back()->with('error',
                '⚠️ Ce document est déjà en stock.'
            );
        }

        $document->update(['status' => 'Stock']);

        $this->recordMovement($document, 'Retour', [
            'movement' => [
                'observations' => $request->observations ?? 'Retour du document en stock',
            ],
        ]);

        // Annule le statut diplômé si c'était un diplôme (normalement impossible ici, mais par sécurité)
        $this->syncTraineeStatut($document);

        return redirect()->route('documents.show', $document)->with('success', 'Retour en stock enregistré ✅');
    }

    // ────────────────────────────────────────────────────
    //  UPLOAD SCAN
    // ────────────────────────────────────────────────────
    public function uploadScan(Request $request, Document $document)
    {
        $request->validate([
            'scan_file' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($document->scan_file) {
            Storage::disk('local')->delete($document->scan_file);
        }

        $scanPath = $request->file('scan_file')->store('documents_scans', 'local');
        $document->update(['scan_file' => $scanPath]);

        return redirect()->route('trainees.show', $document->trainee_id)
            ->with('success', 'Scan uploadé avec succès ✅');
    }

    // ────────────────────────────────────────────────────
    //  LISTES SPÉCIALISÉES
    // ────────────────────────────────────────────────────
    public function tempOut(Request $request)
    {
        $filieres     = Filiere::all();
        $groups       = Trainee::select('group')->distinct()->orderBy('group')->pluck('group');
        $years        = Trainee::select('graduation_year')->distinct()->orderByDesc('graduation_year')->pluck('graduation_year');
        $annees_etude = Trainee::select('annee_etude')->whereNotNull('annee_etude')->distinct()->orderBy('annee_etude')->pluck('annee_etude');

        $documents = Document::with(['trainee.filiere', 'movements'])
            ->where('type', 'Bac')
            ->where('status', 'Temp_Out')
            ->whereHas('latestSortie', fn($q) => $q->where('deadline', '>=', now()))
            ->when($request->search, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('last_name',  'like', '%'.$request->search.'%')
                  ->orWhere('first_name','like', '%'.$request->search.'%')))
            ->when($request->cin, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('cin', 'like', '%'.$request->cin.'%')
                  ->orWhere('cef', 'like', '%'.$request->cin.'%')))
            ->when($request->filiere_id, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('filiere_id', $request->filiere_id)))
            ->when($request->group, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('group', $request->group)))
            ->when($request->graduation_year, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('graduation_year', $request->graduation_year)))
            ->when($request->annee_etude, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('annee_etude', $request->annee_etude)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documents.temp-out', compact('documents', 'filieres', 'groups', 'years', 'annees_etude'));
    }

    public function ecoule(Request $request)
    {
        $filieres = Filiere::all();
        $groups   = Trainee::distinct()->pluck('group');

        $documents = Document::with('trainee.filiere', 'movements')
            ->where('type', 'Bac')
            ->where('status', 'Temp_Out')
            ->whereHas('latestSortie', fn($q) => $q->where('deadline', '<', now()))
            ->when($request->filiere_id, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('filiere_id', $request->filiere_id)))
            ->when($request->group, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('group', $request->group)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documents.ecoule', compact('documents', 'filieres', 'groups'));
    }

    public function finalOut(Request $request)
    {
        $filieres = Filiere::all();
        $groups   = Trainee::distinct()->pluck('group');

        $documents = Document::with('trainee.filiere')
            ->where('type', 'Bac')
            ->where('status', 'Final_Out')
            ->when($request->filiere_id, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('filiere_id', $request->filiere_id)))
            ->when($request->group, fn($q) => $q->whereHas('trainee', fn($q) =>
                $q->where('group', $request->group)))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('documents.final-out', compact('documents', 'filieres', 'groups'));
    }

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