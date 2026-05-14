@extends('layouts.app')
@section('title', 'Document — ' . $document->type)

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <div class="text-muted small text-uppercase fw-bold mb-1" style="letter-spacing:1px;">Documents</div>
        <h2 class="fw-bold mb-0" style="font-size:1.6rem;">
            <i class="ti ti-file-description me-2 text-primary"></i>Document — {{ $document->type }}
        </h2>
    </div>
    <a href="{{ route('documents.index') }}" class="btn btn-outline-secondary">
        <i class="ti ti-arrow-left me-1"></i> Retour
    </a>
</div>
@stop

@section('content')

{{-- Messages flash --}}
@if(session('success'))
<div class="alert alert-success alert-dismissible shadow-sm mb-3">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible shadow-sm mb-3">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <i class="ti ti-alert-circle me-2"></i>{{ session('error') }}
</div>
@endif

<div class="row row-deck g-3">

    {{-- ====== COLONNE GAUCHE : Profil stagiaire ====== --}}
    <div class="col-lg-4">

        {{-- Carte Profil --}}
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center pt-4 pb-3">
                @php $t = $document->trainee; @endphp
                @if($t->image_profile)
                    <img src="{{ asset('storage/' . $t->image_profile) }}"
                         class="rounded-circle shadow mb-3"
                         style="width:100px;height:100px;object-fit:cover;border:3px solid var(--tblr-border-color);"
                         alt="{{ $t->first_name }}">
                @else
                    <div class="avatar avatar-xl rounded-circle bg-primary-lt text-primary fw-bold mx-auto mb-3"
                         style="width:100px;height:100px;font-size:2rem;display:flex;align-items:center;justify-content:center;">
                        {{ substr($t->first_name,0,1) }}{{ substr($t->last_name,0,1) }}
                    </div>
                @endif

                <h3 class="fw-bold mb-0 text-body" style="font-size:1.1rem;">
                    {{ strtoupper($t->last_name) }} {{ ucfirst(strtolower($t->first_name)) }}
                </h3>
                @if($t->prenom_arabe || $t->nom_arabe)
                <div class="text-muted small mt-1" dir="rtl">{{ $t->nom_arabe }} {{ $t->prenom_arabe }}</div>
                @endif

                <div class="mt-2 d-flex justify-content-center gap-2 flex-wrap">
                    @if($t->statut == 'diplome')
                        <span class="badge bg-success-lt text-success"><i class="ti ti-certificate me-1"></i>Diplômé</span>
                    @elseif($t->statut == 'abandon')
                        <span class="badge bg-danger-lt text-danger"><i class="ti ti-ban me-1"></i>Abandon</span>
                    @elseif($t->statut == 'redoublant')
                        <span class="badge bg-warning-lt text-warning"><i class="ti ti-refresh me-1"></i>Redoublant</span>
                    @else
                        <span class="badge bg-azure-lt text-azure"><i class="ti ti-school me-1"></i>En formation</span>
                    @endif
                    <span class="badge bg-blue-lt text-blue">Gr. {{ $t->group }}</span>
                </div>
            </div>

            <div class="card-body border-top pt-3 pb-2">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;width:40%;">CIN</td>
                        <td class="font-monospace fw-bold" style="font-size:.85rem;">{{ $t->cin }}</td>
                    </tr>
                    @if($t->cef)
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">CEF</td>
                        <td class="font-monospace" style="font-size:.85rem;">{{ $t->cef }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Filière</td>
                        <td style="font-size:.85rem;">{{ $t->filiere->nom_filiere ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Promotion</td>
                        <td style="font-size:.85rem;">{{ $t->graduation_year ?? '—' }}</td>
                    </tr>
                    @if($t->phone)
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Téléphone</td>
                        <td style="font-size:.85rem;"><a href="tel:{{ $t->phone }}" class="text-body">{{ $t->phone }}</a></td>
                    </tr>
                    @endif
                    @if($t->date_naissance)
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Naissance</td>
                        <td style="font-size:.85rem;">{{ \Carbon\Carbon::parse($t->date_naissance)->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    @if($t->lieu_naissance)
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Lieu naiss.</td>
                        <td style="font-size:.85rem;">{{ $t->lieu_naissance }}</td>
                    </tr>
                    @endif
                    @if($t->nationalite)
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Nationalité</td>
                        <td style="font-size:.85rem;">{{ $t->nationalite }}</td>
                    </tr>
                    @endif
                    @if($t->adresse)
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Adresse</td>
                        <td style="font-size:.85rem;">{{ $t->adresse }}</td>
                    </tr>
                    @endif
                </table>
            </div>

            <div class="card-footer border-0 text-center pb-3">
                <a href="{{ route('trainees.show', $t) }}" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-user me-1"></i>Voir profil complet
                </a>
            </div>
        </div>

        {{-- Carte Document --}}
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 bg-transparent pt-3 pb-2">
                <h3 class="card-title fw-bold text-body">
                    <i class="ti ti-file me-2 text-primary"></i>Détails du document
                </h3>
            </div>
            <div class="card-body pt-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;width:40%;">Type</td>
                        <td><span class="badge bg-primary-lt text-primary fw-bold">{{ $document->type }}</span></td>
                    </tr>
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Référence</td>
                        <td class="font-monospace" style="font-size:.85rem;">{{ $document->reference_number ?? '—' }}</td>
                    </tr>
                    @if($document->level_year)
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Année</td>
                        <td style="font-size:.85rem;">{{ $document->level_year }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="text-muted fw-semibold" style="font-size:.8rem;">Statut</td>
                        <td>
                            @if($document->status == 'Stock')
                                <span class="badge bg-success-lt text-success"><i class="ti ti-package me-1"></i>En stock</span>
                            @elseif($document->status == 'Temp_Out')
                                <span class="badge bg-warning-lt text-warning"><i class="ti ti-hourglass me-1"></i>Retrait temporaire</span>
                            @elseif($document->status == 'Final_Out')
                                <span class="badge bg-danger-lt text-danger"><i class="ti ti-circle-x me-1"></i>Retrait définitif</span>
                            @else
                                <span class="badge bg-azure-lt text-azure"><i class="ti ti-circle-check me-1"></i>Remis</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer border-0 d-flex gap-2 justify-content-start">
                @if(in_array($document->status, ['Final_Out','Remis']))
                    {{-- Document remis : aucune action possible --}}
                    <span class="text-muted small"><i class="ti ti-lock me-1"></i>Document remis définitivement — aucune action</span>
                @elseif($document->status === 'Temp_Out')
                    {{-- En retrait temporaire : seul retour en stock possible --}}
                    <form action="{{ route('documents.retour', $document) }}" method="POST" style="display:inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="ti ti-arrow-back-up me-1"></i>Retour en stock
                        </button>
                    </form>
                    {{-- Peut aussi passer en définitif depuis temp --}}
                    @if($document->type !== 'Diplome')
                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#sortieModal">
                        <i class="ti ti-lock me-1"></i>Rendre définitif
                    </button>
                    @else
                    {{-- Diplome temp_out → ne devrait pas arriver mais sécurité --}}
                    <span class="badge bg-danger-lt text-danger">État incohérent — contactez l'admin</span>
                    @endif
                @else
                    {{-- En stock : sortie possible --}}
                    <button class="btn btn-warning w-100 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#sortieModal">
                        <i class="ti ti-logout me-2"></i> Nouveau retrait
                    </button>
                @endif
            </div>
        </div>
    </div>

    {{-- ====== COLONNE DROITE : Historique ====== --}}
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header border-0 bg-transparent pt-4 pb-2">
                <h3 class="card-title fw-bold text-body">
                    <i class="ti ti-history me-2 text-blue"></i>Historique des mouvements
                </h3>
                <span class="badge bg-blue-lt text-blue ms-auto">{{ $document->movements->count() }} mouvement(s)</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-vcenter mb-0">
                        <thead>
                            <tr style="border-bottom:2px solid var(--tblr-border-color);">
                                <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Action</th>
                                <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Par</th>
                                <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Date & Heure</th>
                                <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Observations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($document->movements as $mv)
                            <tr>
                                <td>
                                    @if($mv->action_type == 'Saisie')
                                        <span class="badge bg-azure-lt text-azure fw-bold">
                                            <i class="ti ti-plus me-1"></i>Saisie
                                        </span>
                                    @elseif($mv->action_type == 'Sortie')
                                        <span class="badge bg-warning-lt text-warning fw-bold">
                                            <i class="ti ti-logout me-1"></i>Sortie
                                        </span>
                                    @else
                                        <span class="badge bg-success-lt text-success fw-bold">
                                            <i class="ti ti-arrow-back-up me-1"></i>Retour
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="avatar avatar-xs rounded-circle bg-primary text-white fw-bold" style="font-size:.65rem;">
                                            {{ substr($mv->user->name ?? 'A', 0, 1) }}
                                        </span>
                                        <span class="text-body" style="font-size:.85rem;">{{ $mv->user->name ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="text-muted" style="font-size:.82rem;">
                                    <div>{{ $mv->date_action->format('d/m/Y') }}</div>
                                    <div class="text-muted" style="font-size:.75rem;">{{ $mv->date_action->format('H:i') }}</div>
                                </td>
                                <td style="font-size:.82rem;">
                                    <div>{{ $mv->observations ?? '—' }}</div>
                                    @if($mv->is_proxy)
                                        <div class="mt-1 p-2 rounded bg-warning-lt" style="font-size:.75rem;">
                                            <i class="ti ti-user-shield text-warning me-1"></i>
                                            <strong>Procuration :</strong> {{ $mv->proxy_name }} — {{ $mv->proxy_cin }}
                                            @if($mv->proxy_document_path)
                                                <a href="{{ route('scans.show', ['path' => $mv->proxy_document_path]) }}" target="_blank" class="ms-2 text-warning">
                                                    <i class="ti ti-download"></i> Fichier
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="empty">
                                        <div class="empty-icon text-muted"><i class="ti ti-history" style="font-size:2.5rem;"></i></div>
                                        <p class="empty-title mt-3">Aucun mouvement enregistré</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Sortie --}}
<div class="modal fade" id="sortieModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('documents.sortie', $document) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="ti ti-logout me-2 text-warning"></i>Retrait du document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Type de retrait</label>
                        <select name="action_type" class="form-control" required>
                            @if($document->type === 'Diplome')
                                {{-- Diplôme : uniquement définitif --}}
                                <option value="Final_Out">❌ Retrait définitif (remise au stagiaire)</option>
                            @elseif($document->status === 'Temp_Out')
                                {{-- Déjà en temp_out : uniquement passer en définitif --}}
                                <option value="Final_Out">❌ Convertir en retrait définitif</option>
                            @else
                                {{-- Stock : les deux options --}}
                                <option value="Temp_Out">⏳ Retrait temporaire (48h)</option>
                                <option value="Final_Out">❌ Retrait définitif</option>
                            @endif
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Observations</label>
                        <textarea name="observations" class="form-control" rows="3" placeholder="Motif du retrait..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" id="isProxyCheck" name="is_proxy" value="1">
                            <span class="form-check-label fw-bold">Par procuration (ولي الأمر / بوكالة)</span>
                        </label>
                    </div>
                    <div id="proxyFields" style="display:none;" class="p-3 rounded bg-warning-lt border border-warning-subtle">
                        <div class="mb-2">
                            <label class="form-label fw-bold small">Nom du mandataire</label>
                            <input type="text" name="proxy_name" class="form-control" placeholder="Nom complet">
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small">CIN du mandataire</label>
                            <input type="text" name="proxy_cin" class="form-control" placeholder="Ex: AB123456">
                        </div>
                        <div>
                            <label class="form-label fw-bold small">Procuration (fichier)</label>
                            <input type="file" name="proxy_document" class="form-control" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning fw-bold">
                        <i class="ti ti-logout me-1"></i>Confirmer le retrait
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    document.getElementById('isProxyCheck').addEventListener('change', function() {
        document.getElementById('proxyFields').style.display = this.checked ? 'block' : 'none';
        const inputs = document.querySelectorAll('#proxyFields input');
        inputs.forEach(input => {
            if (this.checked) {
                input.setAttribute('required', 'required');
            } else {
                input.removeAttribute('required');
                if (input.type !== 'checkbox' && input.type !== 'radio') input.value = '';
            }
        });
    });
</script>
@stop