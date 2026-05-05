@extends('layouts.app')
@section('title', 'Diplômés — Documents à récupérer')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-graduation-cap text-success"></i>
            Diplômés — Documents à récupérer
        </h1>
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success me-2" style="font-size:14px">
                {{ $trainees->total() }} diplômés
            </span>
            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#modalPromouvoir">
                <i class="fas fa-user-graduate"></i> Promouvoir un diplômé
            </button>
        </div>
    </div>
@stop

@section('content')

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="{{ route('diplomes.prets') }}">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Recherche (CIN, Nom...)" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="filiere_id" class="form-control select2">
                        <option value="">— Toutes les filières —</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}" {{ request('filiere_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->nom_filiere }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="group" class="form-control">
                        <option value="">— Tous les groupes —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g }}" {{ request('group') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="graduation_year" class="form-control">
                        <option value="">— Toutes les années —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('diplomes.prets') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table id="prets-table" class="table table-bordered table-hover">
            <thead class="bg-success text-white">
                <tr>
                    <th>#</th>
                    <th>Stagiaire</th>
                    <th>CIN</th>
                    <th>Filière</th>
                    <th>Groupe</th>
                    <th>Année</th>
                    <th>Bac</th>
                    <th>Diplôme</th>
                    <th>Attestation</th>
                    <th>Bulletin</th>
                    <th>Validation</th>
                    <th>Signature</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trainees as $t)
                @php
                    $docs       = $t->documents->groupBy('type');
                    $allPresent = collect(['Bac','Diplome','Attestation','Bulletin'])
                        ->every(fn($type) => isset($docs[$type]) && $docs[$type]->isNotEmpty() && in_array($docs[$type]->first()->status, ['Final_Out', 'Remis']));
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <a href="{{ route('trainees.show', $t) }}">
                            <strong>{{ $t->last_name }} {{ $t->first_name }}</strong>
                        </a>
                        @if($t->phone)
                            <br><small><a href="tel:{{ $t->phone }}">📞 {{ $t->phone }}</a></small>
                        @endif
                        @if($allPresent)
                            <br>
                            <span class="badge bg-success mt-1">
                                <i class="fas fa-check-circle"></i> Complet
                            </span>
                        @else
                            <br>
                            <button class="btn btn-xs btn-warning mt-1 btn-promote" data-id="{{ $t->id }}">
                                <i class="fas fa-hand-holding"></i> Retirer documents
                            </button>
                        @endif
                    </td>
                    <td>{{ $t->cin }}</td>
                    <td>{{ $t->filiere->nom_filiere }}</td>
                    <td>{{ $t->group }}</td>
                    <td>{{ $t->graduation_year }}</td>

                    @foreach(['Bac','Diplome','Attestation','Bulletin'] as $type)
                    @php $doc = isset($docs[$type]) ? $docs[$type]->first() : null; @endphp
                    <td class="text-center">
                        @if(!$doc)
                            <span class="badge bg-light border">
                                <i class="fas fa-times text-danger"></i> Manquant
                            </span>
                        @elseif(in_array($doc->status, ['Final_Out','Remis']))
                            <span class="badge bg-success">
                                <i class="fas fa-check"></i> Remis
                            </span>
                        @elseif($doc->status == 'Temp_Out')
                            <span class="badge bg-warning">
                                <i class="fas fa-clock"></i> Temp.
                            </span>
                        @else
                            <span class="badge bg-info">
                                <i class="fas fa-archive"></i> En stock
                            </span>
                        @endif
                    </td>
                    @endforeach

                    <td class="text-center">
                        @if($t->validation)
                            <span class="badge bg-success">
                                <i class="fas fa-check-double"></i>
                                {{ \Carbon\Carbon::parse($t->validation->date_validation)->format('d/m/Y') }}
                            </span>
                        @else
                            <a href="{{ route('validations.create', $t) }}" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-signature"></i> Valider
                            </a>
                        @endif
                    </td>

                    {{-- Colonne Signature --}}
                    <td class="text-center">
                        @if($t->validation && $t->validation->signature_scan)
                            @php $sigUrl = route('scans.show', $t->validation->signature_scan); @endphp
                            <img src="{{ $sigUrl }}"
                                 alt="Signature"
                                 style="max-height:40px;border:1px solid #ccc;border-radius:4px;cursor:pointer;"
                                 onclick="viewSignature('{{ $sigUrl }}')">
                        @else
                            <button class="btn btn-sm btn-outline-primary btn-scan-signature"
                                    data-id="{{ $t->id }}"
                                    data-name="{{ $t->last_name }} {{ $t->first_name }}">
                                <i class="fas fa-camera"></i> Scanner
                            </button>
                        @endif
                    </td>

                    <td>
                        <a href="{{ route('trainees.show', $t) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="13" class="text-center py-4 text-muted">
                        <i class="fas fa-graduation-cap fa-2x mb-2"></i><br>
                        Aucun diplômé trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $trainees->links() }}
    </div>
</div>

{{-- MODALE CAMÉRA --}}
<div class="modal fade" id="signatureModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-camera"></i> Scanner la signature —
                    <span id="sigModalName"></span>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 text-center">
                        <label class="font-weight-bold mb-2">📷 Caméra en direct</label>
                        <div style="position:relative;border:2px solid #28a745;border-radius:8px;overflow:hidden;">
                            <video id="cameraStream" autoplay playsinline
                                   style="width:100%;max-height:300px;background:#000;"></video>
                            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                                        width:80%;height:60%;border:2px dashed rgba(255,255,255,0.7);
                                        border-radius:4px;pointer-events:none;"></div>
                        </div>
                        <div class="mt-2">
                            <button id="btnCapture" class="btn btn-success me-2">
                                <i class="fas fa-camera"></i> Capturer
                            </button>
                            <button id="btnSwitchCamera" class="btn btn-secondary">
                                <i class="fas fa-sync"></i> Changer
                            </button>
                        </div>
                        <hr class="mt-3 mb-3 border-secondary">
                        <label class="font-weight-bold mb-2">📁 Ou importer un fichier (Scanner)</label>
                        <input type="file" id="signatureFile" class="form-control" accept="image/*">
                    </div>
                    <div class="col-md-6 text-center">
                        <label class="font-weight-bold mb-2">✅ Aperçu</label>
                        <div style="border:2px solid #dee2e6;border-radius:8px;min-height:300px;
                                    display:flex;align-items:center;justify-content:center;background:#f8f9fa;">
                            <canvas id="captureCanvas" style="max-width:100%;max-height:300px;display:none;"></canvas>
                            <span id="noCapture" class="text-muted">
                                <i class="fas fa-arrow-left fa-2x"></i><br>
                                Cliquez sur "Capturer"
                            </span>
                        </div>
                        <div class="mt-2">
                            <button id="btnRetake" class="btn btn-warning me-2" style="display:none;">
                                <i class="fas fa-redo"></i> Reprendre
                            </button>
                            <button id="btnSaveSignature" class="btn btn-primary" style="display:none;">
                                <i class="fas fa-save"></i> Enregistrer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALE VISIONNEUSE --}}
<div class="modal fade" id="viewSignatureModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Signature enregistrée</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <img id="viewSignatureImg" src="" style="max-width:100%;">
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL PROMOUVOIR UN DIPLÔMÉ — Formulaire complet --}}
{{-- ============================================================ --}}
<div class="modal fade" id="modalPromouvoir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate me-2"></i> Nouveau Diplômé
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">

                {{-- ÉTAPE 1 : Recherche stagiaire --}}
                <div id="step-search">
                    <div class="alert alert-info py-2 mb-3">
                        <i class="fas fa-info-circle"></i>
                        Recherchez le stagiaire à promouvoir. Tous ses documents seront automatiquement remis.
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold"><i class="fas fa-search me-1"></i> Nom / CIN / CEF</label>
                        <input type="text" id="promoteSearchInput" class="form-control form-control-lg"
                               placeholder="Tapez un nom, CIN ou CEF..." autocomplete="off">
                        <div id="promoteSearchResults" class="list-group mt-1" style="max-height:220px;overflow-y:auto;display:none"></div>
                    </div>
                </div>

                {{-- ÉTAPE 2 : Confirmation + Uploads (caché au départ) --}}
                <div id="step-confirm" style="display:none">
                    {{-- Fiche stagiaire --}}
                    <div class="card border-success mb-3">
                        <div class="card-header bg-success text-white py-2">
                            <strong><i class="fas fa-user-check me-1"></i> Stagiaire sélectionné</strong>
                        </div>
                        <div class="card-body py-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong id="prom-name"></strong><br>
                                    <small class="text-muted">CIN : <span id="prom-cin"></span> &nbsp;|&nbsp; CEF : <span id="prom-cef"></span></small>
                                </div>
                                <div class="col-md-6 text-md-right">
                                    <small class="text-muted">Filière : <span id="prom-filiere"></span></small><br>
                                    <small class="text-muted">Groupe : <span id="prom-group"></span> &nbsp;|&nbsp; Année : <span id="prom-year"></span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="prom-trainee-id">

                    {{-- Documents à remettre --}}
                    <h6 class="font-weight-bold text-secondary mb-2"><i class="fas fa-folder-open me-1"></i> Documents à remettre automatiquement</h6>
                    <div class="row mb-3">
                        {{-- BAC --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border-danger">
                                <div class="card-header bg-danger-lt py-1">
                                    <strong>🎓 Baccalauréat</strong>
                                    <span class="badge bg-danger ms-2">Retrait Définitif</span>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted"><i class="fas fa-file-upload me-1"></i> Scan (optionnel)</label>
                                        <input type="file" name="scan_bac" id="scan_bac" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- DIPLOME --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border-success">
                                <div class="card-header bg-success-lt py-1">
                                    <strong>📜 Diplôme</strong>
                                    <span class="badge bg-success ms-2">Remis</span>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted"><i class="fas fa-file-upload me-1"></i> Scan (optionnel)</label>
                                        <input type="file" name="scan_diplome" id="scan_diplome" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- ATTESTATION --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border-info">
                                <div class="card-header bg-info-lt py-1">
                                    <strong>📋 Attestation</strong>
                                    <span class="badge bg-info ms-2">Remis</span>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted"><i class="fas fa-file-upload me-1"></i> Scan (optionnel)</label>
                                        <input type="file" name="scan_attestation" id="scan_attestation" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- BULLETIN --}}
                        <div class="col-md-6 mb-3">
                            <div class="card border-warning">
                                <div class="card-header bg-warning-lt py-1">
                                    <strong>📊 Bulletin</strong>
                                    <span class="badge bg-warning ms-2">Remis</span>
                                </div>
                                <div class="card-body py-2">
                                    <div class="form-group mb-0">
                                        <label class="small text-muted"><i class="fas fa-file-upload me-1"></i> Scan (optionnel)</label>
                                        <input type="file" name="scan_bulletin" id="scan_bulletin" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Signature --}}
                    <div class="card border-primary mb-2">
                        <div class="card-header bg-primary-lt py-1">
                            <strong><i class="fas fa-pen-nib me-1"></i> Signature du stagiaire</strong>
                            <small class="text-muted ms-2">(optionnel)</small>
                        </div>
                        <div class="card-body py-2">
                            <div class="form-group mb-0">
                                <label class="small text-muted"><i class="fas fa-image me-1"></i> Importer une signature (JPG, PNG)</label>
                                <input type="file" name="signature_file" id="prom-signature" class="form-control-file" accept=".jpg,.jpeg,.png">
                            </div>
                        </div>
                    </div>

                    <div id="promoteResult" class="mt-2" style="display:none"></div>

                    <button type="button" class="btn btn-sm btn-link text-muted" id="btnChangeTrainee">
                        <i class="fas fa-arrow-left"></i> Changer de stagiaire
                    </button>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Annuler
                </button>
                <button type="button" id="btnConfirmPromote" class="btn btn-success" style="display:none">
                    <i class="fas fa-check"></i> Confirmer la promotion
                </button>
            </div>
        </div>
    </div>
</div>

@stop

@section('css')
<style>
@keyframes spin {
    0%   { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
@stop

@section('js')
<script>
// ═══════════════════════════════════════════
// Données pour la recherche live (promote)
// ═══════════════════════════════════════════
@php
$promoteTrainees = \App\Models\Trainee::with('filiere')
    ->whereIn('statut', ['en_formation','redoublant','abandon'])
    ->orderBy('last_name')->get()
    ->map(fn($t) => [
        'id'       => $t->id,
        'nom'      => strtoupper($t->last_name).' '.ucfirst(strtolower($t->first_name)),
        'cin'      => $t->cin ?? '',
        'cef'      => $t->cef ?? '',
        'filiere'  => optional($t->filiere)->nom_filiere ?? '—',
        'group'    => $t->group ?? '—',
        'year'     => $t->graduation_year ?? '—',
    ])->values();
@endphp
var promoteTrainees = {!! json_encode($promoteTrainees) !!};

// Reset modal when opened
$('#modalPromouvoir').on('show.bs.modal', function () {
    $('#promoteSearchInput').val('');
    $('#promoteSearchResults').hide().html('');
    $('#step-search').show();
    $('#step-confirm').hide();
    $('#btnConfirmPromote').hide();
    $('#promoteResult').hide();
    // reset file inputs
    ['scan_bac','scan_diplome','scan_attestation','scan_bulletin','prom-signature'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.value = '';
    });
});

// Live search
$('#promoteSearchInput').on('input', function () {
    const q = $(this).val().toLowerCase().trim();
    if (!q) { $('#promoteSearchResults').hide(); return; }

    const results = promoteTrainees.filter(t =>
        t.nom.toLowerCase().includes(q) ||
        t.cin.toLowerCase().includes(q) ||
        t.cef.toLowerCase().includes(q)
    ).slice(0, 15);

    const $res = $('#promoteSearchResults').html('').show();
    if (!results.length) {
        $res.html('<div class="list-group-item text-muted text-center"><i class="fas fa-inbox"></i> Aucun stagiaire trouvé</div>');
        return;
    }
    results.forEach(t => {
        const $item = $(`
            <button type="button" class="list-group-item list-group-item-action">
                <strong>${t.nom}</strong>
                <span class="badge bg-secondary ms-1">${t.cin}</span>
                ${t.cef ? `<span class="badge bg-info ms-1">CEF:${t.cef}</span>` : ''}
                <span class="ms-2 text-muted small">${t.filiere}</span>
            </button>
        `);
        $item.on('click', () => selectPromoteTrainee(t));
        $res.append($item);
    });
});

function selectPromoteTrainee(t) {
    $('#prom-trainee-id').val(t.id);
    $('#prom-name').text(t.nom);
    $('#prom-cin').text(t.cin);
    $('#prom-cef').text(t.cef || '—');
    $('#prom-filiere').text(t.filiere);
    $('#prom-group').text(t.group);
    $('#prom-year').text(t.year);
    $('#step-search').hide();
    $('#step-confirm').show();
    $('#btnConfirmPromote').show();
}

$('#btnChangeTrainee').on('click', function () {
    $('#step-confirm').hide();
    $('#step-search').show();
    $('#btnConfirmPromote').hide();
    $('#promoteSearchInput').val('').focus();
});

// Confirm promotion with file uploads
$('#btnConfirmPromote').on('click', function () {
    const traineeId = $('#prom-trainee-id').val();
    if (!traineeId) { toastr.warning("Aucun stagiaire sélectionné !"); return; }

    const $btn = $(this).prop('disabled', true).html('<img src="/images/ofppt_logo.png" style="height:22px;width:22px;object-fit:contain;animation:spin 1s linear infinite;border-radius:50%;"> En cours...');

    const fd = new FormData();
    fd.append('_token', '{{ csrf_token() }}');
    ['scan_bac','scan_diplome','scan_attestation','scan_bulletin'].forEach(id => {
        const el = document.getElementById(id);
        if (el && el.files[0]) fd.append(id, el.files[0]);
    });
    const sig = document.getElementById('prom-signature');
    if (sig && sig.files[0]) fd.append('signature_file', sig.files[0]);

    $.ajax({
        url: `/diplomes-prets/${traineeId}/check-promote`,
        method: 'POST',
        data: fd,
        processData: false,
        contentType: false,
        dataType: 'json',
        headers: { 'Accept': 'application/json' }
    })
    .done(res => {
        toastr.success(res.message || 'Stagiaire promu en Diplômé avec succès !');
        $('#modalPromouvoir').modal('hide');
        setTimeout(() => location.reload(), 1200);
    })
    .fail(xhr => {
        const msg = xhr.responseJSON?.message ?? 'Erreur serveur.';
        toastr.error(msg);
        $('#promoteResult').html(`<div class="alert alert-danger py-2"><i class="fas fa-times-circle"></i> ${msg}</div>`).show();
    })
    .always(() => $btn.prop('disabled', false).html('<i class="fas fa-check"></i> Confirmer la promotion'));
});

// Inline "Retirer documents" btn
$(document).on('click', '.btn-promote', function () {
    const id = $(this).data('id');
    if (!confirm('Voulez-vous retirer tous les documents pour ce stagiaire ?')) return;
    $.post(`/diplomes-prets/${id}/check-promote`, { _token: '{{ csrf_token() }}' })
        .done(res => { toastr.success(res.message); setTimeout(() => location.reload(), 1000); })
        .fail(() => toastr.error('Erreur serveur.'));
});

// ── Caméra ──
let stream = null, traineeId = null, facingMode = 'environment';
const video  = document.getElementById('cameraStream');
const canvas = document.getElementById('captureCanvas');
const ctx    = canvas.getContext('2d');

async function startCamera() {
    if (stream) stream.getTracks().forEach(t => t.stop());
    try {
        stream = await navigator.mediaDevices.getUserMedia({
            video: { facingMode, width: { ideal: 1280 } }
        });
        video.srcObject = stream;
    } catch (e) {
        toastr.error("Caméra inaccessible : " + e.message);
    }
}

$(document).on('click', '.btn-scan-signature', function () {
    traineeId = $(this).data('id');
    $('#sigModalName').text($(this).data('name'));
    canvas.style.display = 'none';
    $('#noCapture').show();
    $('#btnRetake, #btnSaveSignature').hide();
    $('#signatureModal').modal('show');
    startCamera();
});

$('#signatureModal').on('hidden.bs.modal', () => {
    if (stream) stream.getTracks().forEach(t => t.stop());
    $('#signatureFile').val('');
});

$('#btnSwitchCamera').on('click', () => {
    facingMode = facingMode === 'environment' ? 'user' : 'environment';
    startCamera();
});

$('#btnCapture').on('click', () => {
    canvas.width  = video.videoWidth;
    canvas.height = video.videoHeight;
    ctx.drawImage(video, 0, 0);
    canvas.style.display = 'block';
    $('#noCapture').hide();
    $('#btnRetake, #btnSaveSignature').show();
});

$('#signatureFile').on('change', function(e) {
    if (this.files && this.files[0]) {
        $('#btnSaveSignature').show();
        $('#noCapture').html('<i class="fas fa-file-image fa-2x"></i><br>Fichier sélectionné');
    }
});

$('#btnRetake').on('click', () => {
    canvas.style.display = 'none';
    $('#noCapture').show();
    $('#btnRetake, #btnSaveSignature').hide();
});

$('#btnSaveSignature').on('click', function () {
    const fileInput = document.getElementById('signatureFile');
    const hasFile = fileInput.files && fileInput.files.length > 0;
    
    if (!hasFile && canvas.style.display === 'none') {
        toastr.error('Veuillez capturer ou uploader une signature.');
        return;
    }

    const $btn = $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    
    if (hasFile) {
        formData.append('signature_file', fileInput.files[0]);
    } else {
        formData.append('signature', canvas.toDataURL('image/png'));
    }

    $.ajax({
        url: `/diplomes-prets/${traineeId}/signature`,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false
    })
    .done(res => {
        if (res.success) {
            toastr.success(res.message);
            $('#signatureModal').modal('hide');
            $(`.btn-scan-signature[data-id="${traineeId}"]`)
                .replaceWith(`<img src="${res.path}"
                    style="max-height:40px;border:1px solid #ccc;border-radius:4px;cursor:pointer;"
                    onclick="viewSignature('${res.path}')">`);
        } else {
            toastr.error(res.message);
        }
    })
    .fail(() => toastr.error('Erreur enregistrement.'))
    .always(() => $btn.prop('disabled', false).html('<i class="fas fa-save"></i> Enregistrer'));
});

function viewSignature(path) {
    $('#viewSignatureImg').attr('src', path);
    $('#viewSignatureModal').modal('show');
}
</script>
@stop
