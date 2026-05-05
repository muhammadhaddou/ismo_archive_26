@extends('layouts.app')
@php $isBac = old('type', request('type')) === 'Bac'; @endphp
@section('title', $isBac ? 'Retrait Baccalauréat' : 'Ajouter document')
@section('content_header')
    <h1>
        @if($isBac)
            <i class="fas fa-graduation-cap text-warning"></i> Enregistrer un retrait de Baccalauréat
        @else
            <i class="fas fa-plus"></i> Ajouter un document
        @endif
    </h1>
@stop

@section('content')
<div class="card shadow-sm" style="border-radius: 10px; overflow: hidden;">
    <div class="card-header {{ $isBac ? 'bg-warning' : 'bg-primary' }} text-white">
        <h3 class="card-title">
            @if($isBac)
                <i class="fas fa-graduation-cap me-2"></i> Retrait de Baccalauréat
            @else
                <i class="fas fa-file-medical me-2"></i> Nouveau document
            @endif
        </h3>
    </div>
    <div class="card-body">

        @if($isBac)
        {{-- Bandeau informatif mode Bac --}}
        <div class="alert alert-warning border-left-warning py-2">
            <i class="fas fa-info-circle"></i>
            Vous êtes en mode <strong>Baccalauréat</strong>. Choisissez le type de retrait :
            <ul class="mb-0 mt-1">
                <li>🟡 <strong>Temporaire</strong> — Le stagiaire doit retourner le Bac sous <strong>48h</strong></li>
                <li>🔴 <strong>Définitif</strong> — Le Bac est remis définitivement (sans retour)</li>
            </ul>
        </div>
        @endif

        <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Type caché si Bac, sinon select normal --}}
            @if($isBac)
                <input type="hidden" name="type" value="Bac">
            @endif

            <div class="row">

                {{-- Stagiaire: recherche par Nom + CIN --}}
                <div class="col-md-12 mb-3">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white py-2">
                            <strong><i class="fas fa-search me-1"></i> Rechercher un Stagiaire</strong>
                        </div>
                        <div class="card-body pb-2">
                            <input type="hidden" name="trainee_id" id="trainee_id_hidden"
                                   value="{{ old('trainee_id', request('trainee_id')) }}"
                                   required>
                            <div class="row">
                                {{-- Barre Nom --}}
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold"><i class="fas fa-user me-1"></i> Nom / Prénom</label>
                                        <input type="text" id="search-nom" class="form-control"
                                               placeholder="Tapez un nom..."
                                               autocomplete="off">
                                    </div>
                                </div>
                                {{-- Barre CIN --}}
                                <div class="col-md-5">
                                    <div class="form-group mb-2">
                                        <label class="small font-weight-bold"><i class="fas fa-id-card me-1"></i> CIN / CEF</label>
                                        <input type="text" id="search-cin" class="form-control"
                                               placeholder="Tapez un CIN ou CEF..."
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="button" class="btn btn-secondary btn-sm mb-2 w-100"
                                            onclick="clearTraineeSearch()">
                                        <i class="fas fa-times"></i> Effacer
                                    </button>
                                </div>
                            </div>

                            {{-- Stagiaire sélectionné --}}
                            <div id="selected-trainee" style="display:none" class="alert alert-success py-2 mb-2">
                                <i class="fas fa-check-circle"></i>
                                <strong id="selected-trainee-name"></strong>
                                <span id="selected-trainee-cin" class="ms-2 text-muted small"></span>
                                <button type="button" class="close" onclick="clearSelectedTrainee()">
                                    <span>&times;</span>
                                </button>
                            </div>

                            {{-- Liste des résultats --}}
                            <div id="trainee-results" class="list-group" style="max-height:200px; overflow-y:auto; display:none"></div>

                            @error('trainee_id')
                                <div class="text-danger small mt-1"><i class="fas fa-exclamation-triangle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                @if(!$isBac)
                {{-- Type de document (visible seulement hors mode Bac) --}}
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="fas fa-tag me-1 text-info"></i>
                            Type <span class="text-danger">*</span>
                        </label>
                        <select name="type" id="type-select"
                                class="form-control @error('type') is-invalid @enderror" required>
                            <option value="">-- Choisir le type --</option>
                            <option value="Bac"         {{ old('type', request('type')) == 'Bac'         ? 'selected' : '' }}>🎓 Baccalauréat</option>
                            <option value="Attestation" {{ old('type', request('type')) == 'Attestation' ? 'selected' : '' }}>📋 Attestation</option>
                            <option value="Bulletin"    {{ old('type', request('type')) == 'Bulletin'    ? 'selected' : '' }}>📊 Bulletin de notes</option>
                        </select>
                        @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                @endif

                {{-- Type de retrait (Bac uniquement — toujours visible en mode Bac) --}}
                @if($isBac)
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="fas fa-exchange-alt me-1 text-warning"></i>
                            Type de retrait <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            <div class="col-6">
                                <div class="card border-warning text-center p-3 bac-type-card"
                                     id="card-temp" onclick="selectBacType('Temp_Out')"
                                     style="cursor:pointer; border-radius:8px;">
                                    <div class="fa-2x mb-1">🟡</div>
                                    <strong>Temporaire</strong>
                                    <small class="d-block text-muted">Retour sous 48h</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card border-danger text-center p-3 bac-type-card"
                                     id="card-final" onclick="selectBacType('Final_Out')"
                                     style="cursor:pointer; border-radius:8px;">
                                    <div class="fa-2x mb-1">🔴</div>
                                    <strong>Définitif</strong>
                                    <small class="d-block text-muted">Sans retour</small>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="bac_status" id="bac-status-input" value="Temp_Out">
                        <small class="text-muted mt-1 d-block">
                            <i class="fas fa-hand-pointer"></i> Cliquez sur un type pour le sélectionner
                        </small>
                    </div>
                </div>
                @else
                {{-- Bac type (caché, déclenché si type=Bac dans le select) --}}
                <div class="col-md-6 mb-3" id="bac-status-div" style="display:none">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="fas fa-exchange-alt me-1 text-warning"></i>
                            Type de retrait <span class="text-danger">*</span>
                        </label>
                        <select name="bac_status" class="form-control">
                            <option value="Temp_Out">🟡 Retrait temporaire (retour sous 48h)</option>
                            <option value="Final_Out">🔴 Retrait définitif</option>
                        </select>
                    </div>
                </div>

                {{-- Bulletin: Année --}}
                <div class="col-md-6 mb-3" id="level-year-div" style="display:none">
                    <div class="form-group">
                        <label class="font-weight-bold">Année (1 ou 2)</label>
                        <select name="level_year" class="form-control">
                            <option value="">— Non applicable —</option>
                            <option value="1">Année 1</option>
                            <option value="2">Année 2</option>
                        </select>
                    </div>
                </div>
                @endif

                {{-- Numéro de référence --}}
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="fas fa-hashtag me-1 text-secondary"></i>
                            Numéro de référence
                        </label>
                        <input type="text" name="reference_number"
                               class="form-control"
                               placeholder="Ex: BAC-2025-001"
                               value="{{ old('reference_number') }}">
                    </div>
                </div>

                {{-- Scan --}}
                <div class="col-md-6 mb-3">
                    <div class="form-group">
                        <label class="font-weight-bold">
                            <i class="fas fa-file-upload me-1 text-secondary"></i>
                            Scan / Importer depuis l'appareil
                            <small class="text-muted font-weight-normal">(PDF, JPG, PNG — max 5MB)</small>
                        </label>
                        <input type="file" name="scan_file"
                               class="form-control-file @error('scan_file') is-invalid @enderror"
                               accept=".pdf,.jpg,.jpeg,.png">
                        @error('scan_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

            </div>{{-- end row --}}

            <hr>
            <button type="submit" class="btn {{ $isBac ? 'btn-warning' : 'btn-primary' }}">
                <i class="fas fa-save"></i>
                {{ $isBac ? 'Enregistrer le retrait Bac' : 'Enregistrer le document' }}
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </form>
    </div>
</div>
@stop

@section('css')
<style>
.select2-container--default .select2-selection--single {
    height: 38px !important;
    border: 1px solid #ced4da !important;
    border-radius: 4px !important;
    padding: 4px 10px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px !important; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px !important; color: #495057 !important; }
.select2-dropdown { z-index: 9999 !important; }

.bac-type-card { transition: all 0.2s ease; }
.bac-type-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
.bac-type-card.selected-temp { background: #fff3cd; border-color: #ffc107 !important; box-shadow: 0 0 0 3px rgba(255,193,7,0.3); }
.bac-type-card.selected-final { background: #fde8e8; border-color: #dc3545 !important; box-shadow: 0 0 0 3px rgba(220,53,69,0.25); }
</style>
@stop

@php
$traineesData = $trainees->map(function($t) {
    return [
        'id'         => $t->id,
        'nom'        => strtoupper($t->last_name) . ' ' . ucfirst(strtolower($t->first_name)),
        'last_name'  => strtolower($t->last_name),
        'first_name' => strtolower($t->first_name),
        'cin'        => $t->cin ?? '',
        'cef'        => $t->cef ?? '',
        'filiere'    => optional($t->filiere)->code_filiere ?? '',
    ];
})->values();
@endphp

@section('js')
<script>
// ===== Données des stagiaires pour la recherche =====
var trainees = {!! json_encode($traineesData) !!};

// Restaurer sélection si old() ou request()
var preselectedId = '{{ old('trainee_id', request('trainee_id')) }}';
if (preselectedId) {
    var found = trainees.find(t => t.id == preselectedId);
    if (found) showSelectedTrainee(found);
}

function searchTrainees() {
    var nom = document.getElementById('search-nom').value.toLowerCase().trim();
    var cin = document.getElementById('search-cin').value.toLowerCase().trim();

    if (!nom && !cin) {
        document.getElementById('trainee-results').style.display = 'none';
        return;
    }

    var results = trainees.filter(function(t) {
        var matchNom = !nom ||
            t.last_name.includes(nom) ||
            t.first_name.includes(nom) ||
            (t.last_name + ' ' + t.first_name).includes(nom);
        var matchCin = !cin ||
            t.cin.toLowerCase().includes(cin) ||
            t.cef.toLowerCase().includes(cin);
        return matchNom && matchCin;
    });

    var container = document.getElementById('trainee-results');
    container.innerHTML = '';

    if (results.length === 0) {
        container.innerHTML = '<div class="list-group-item text-muted text-center"><i class="fas fa-inbox"></i> Aucun stagiaire trouvé</div>';
    } else {
        results.slice(0, 20).forEach(function(t) {
            var item = document.createElement('button');
            item.type = 'button';
            item.className = 'list-group-item list-group-item-action py-2 trainee-suggestion-btn';
            item.setAttribute('data-id', t.id);
            item.innerHTML =
                '<strong>' + t.nom + '</strong>' +
                '<span class="ms-2 badge bg-secondary">' + t.cin + '</span>' +
                (t.cef ? '<span class="ms-1 badge bg-info">CEF: ' + t.cef + '</span>' : '') +
                (t.filiere ? '<span class="ms-1 text-muted small"> | ' + t.filiere + '</span>' : '');
            container.appendChild(item);
        });

        if (results.length > 20) {
            container.innerHTML += '<div class="list-group-item text-muted text-center small">' + results.length + ' résultats — affinez votre recherche</div>';
        }
    }

    container.style.display = 'block';
}

// Utiliser l'event delegation avec mousedown pour éviter les conflits de focus
document.getElementById('trainee-results').addEventListener('mousedown', function(e) {
    var btn = e.target.closest('.trainee-suggestion-btn');
    if (btn) {
        e.preventDefault(); // Empêche la perte de focus de l'input si nécessaire
        var t_id = btn.getAttribute('data-id');
        var t = trainees.find(x => x.id == t_id);
        if (t) selectTrainee(t);
    }
});

function selectTrainee(t) {
    document.getElementById('trainee_id_hidden').value = t.id;
    document.getElementById('trainee-results').style.display = 'none';
    document.getElementById('search-nom').value = '';
    document.getElementById('search-cin').value = '';
    showSelectedTrainee(t);
}

function showSelectedTrainee(t) {
    document.getElementById('selected-trainee-name').textContent = t.nom;
    document.getElementById('selected-trainee-cin').textContent = 'CIN: ' + t.cin + (t.cef ? ' | CEF: ' + t.cef : '');
    document.getElementById('selected-trainee').style.display = 'block';
}

function clearSelectedTrainee() {
    document.getElementById('trainee_id_hidden').value = '';
    document.getElementById('selected-trainee').style.display = 'none';
}

function clearTraineeSearch() {
    document.getElementById('search-nom').value = '';
    document.getElementById('search-cin').value = '';
    document.getElementById('trainee-results').style.display = 'none';
    clearSelectedTrainee();
}

// Ecoute des deux champs
document.getElementById('search-nom').addEventListener('input', searchTrainees);
document.getElementById('search-cin').addEventListener('input', searchTrainees);

// ===== Type de document conditionnel =====
$(function() {
    @if(!$isBac)
    function updateFields(type) {
        $('#bac-status-div').hide();
        $('#level-year-div').hide();
        if (type === 'Bac') {
            $('#bac-status-div').fadeIn(200);
        } else if (type === 'Bulletin') {
            $('#level-year-div').fadeIn(200);
        }
    }

    $('#type-select').on('change', function() {
        updateFields($(this).val());
    });

    var initialType = '{{ old('type', request('type')) }}';
    if (initialType) {
        $('#type-select').val(initialType).trigger('change');
    }
    @endif

    @if($isBac)
    selectBacType('Temp_Out');
    @endif
});

@if($isBac)
function selectBacType(type) {
    $('#bac-status-input').val(type);
    $('#card-temp').removeClass('selected-temp selected-final');
    $('#card-final').removeClass('selected-temp selected-final');
    if (type === 'Temp_Out') {
        $('#card-temp').addClass('selected-temp');
    } else {
        $('#card-final').addClass('selected-final');
    }
}
@endif
</script>
@stop