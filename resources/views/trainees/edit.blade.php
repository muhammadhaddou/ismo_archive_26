@extends('layouts.app')

@section('title', 'Modifier stagiaire')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1><i class="ti ti-user-edit text-primary"></i> Modifier — {{ $trainee->last_name }} {{ $trainee->first_name }}</h1>
    <a href="{{ route('trainees.show', $trainee) }}" class="btn btn-secondary">
        <i class="ti ti-arrow-left"></i> Retour au profil
    </a>
</div>
@stop

@section('content')

@if ($errors->any())
    <div class="alert alert-danger shadow-sm">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form action="{{ route('trainees.update', $trainee) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-3">
        <!-- Colonne Gauche : Photo -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm mb-3">
                <div class="card-body text-center">
                    <h3 class="card-title mb-4">Photo de profil</h3>
                    <div class="mb-4">
                        @if($trainee->image_profile)
                            <img src="{{ asset('storage/'.$trainee->image_profile) }}" class="rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                        @else
                            <div class="bg-light rounded-circle mx-auto d-flex align-items-center justify-content-center shadow-sm" style="width: 150px; height: 150px; border: 4px solid #fff;">
                                <i class="ti ti-user text-secondary" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="text-start">
                        <label class="form-label fw-bold">Modifier la photo</label>
                        <input type="file" name="image_profile" class="form-control" accept="image/*">
                        <small class="text-muted mt-1 d-block">Laissez vide pour conserver la photo actuelle.</small>
                    </div>
                </div>
            </div>

            <!-- Documents Scannés (CIN) -->
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-file-scan text-primary"></i> Documents Scannés</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">CIN Stagiaire <span class="text-danger">*</span></label>
                        <input type="text" name="cin" class="form-control mb-2" value="{{ old('cin',$trainee->cin) }}" placeholder="Ex: QY165535" required>
                        <label class="form-label small text-muted"><i class="ti ti-upload"></i> Scan CIN Stagiaire</label>
                        @if($trainee->cin_scan) 
                            <a href="{{ asset('storage/'.$trainee->cin_scan) }}" target="_blank" class="badge bg-success float-end"><i class="ti ti-eye"></i> Voir actuel</a> 
                        @endif
                        <input type="file" name="cin_scan" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">CIN Père</label>
                        <input type="text" name="cin_pere" class="form-control mb-2" value="{{ old('cin_pere', $trainee->cin_pere) }}" placeholder="Numéro CIN du père">
                        <label class="form-label small text-muted"><i class="ti ti-upload"></i> Scan CIN Père</label>
                        @if($trainee->cin_pere_scan) 
                            <a href="{{ asset('storage/'.$trainee->cin_pere_scan) }}" target="_blank" class="badge bg-success float-end"><i class="ti ti-eye"></i> Voir actuel</a> 
                        @endif
                        <input type="file" name="cin_pere_scan" class="form-control form-control-sm">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">CIN Mère</label>
                        <input type="text" name="cin_mere" class="form-control mb-2" value="{{ old('cin_mere', $trainee->cin_mere) }}" placeholder="Numéro CIN de la mère">
                        <label class="form-label small text-muted"><i class="ti ti-upload"></i> Scan CIN Mère</label>
                        @if($trainee->cin_mere_scan) 
                            <a href="{{ asset('storage/'.$trainee->cin_mere_scan) }}" target="_blank" class="badge bg-success float-end"><i class="ti ti-eye"></i> Voir actuel</a> 
                        @endif
                        <input type="file" name="cin_mere_scan" class="form-control form-control-sm">
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne Droite : Infos -->
        <div class="col-xl-8 col-lg-7">
            <!-- État Civil -->
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-id-badge text-primary"></i> État Civil & Contact</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name',$trainee->last_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name',$trainee->first_name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date de naissance</label>
                            <input type="date" name="date_naissance" class="form-control" value="{{ old('date_naissance',$trainee->date_naissance) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Téléphone</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone',$trainee->phone) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Parcours Scolaire -->
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <h3 class="card-title"><i class="ti ti-school text-primary"></i> Parcours Scolaire</h3>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">CEF</label>
                            <input type="text" name="cef" class="form-control" value="{{ old('cef',$trainee->cef) }}" placeholder="Numéro CEF">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Filière <span class="text-danger">*</span></label>
                            <select name="filiere_id" class="form-control select2" required>
                                @foreach($filieres as $filiere)
                                    <option value="{{ $filiere->id }}" {{ old('filiere_id',$trainee->filiere_id)==$filiere->id?'selected':'' }}>
                                        {{ $filiere->nom_filiere }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Groupe <span class="text-danger">*</span></label>
                            <input type="text" name="group" class="form-control" value="{{ old('group',$trainee->group) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Année de promotion <span class="text-danger">*</span></label>
                            <select name="graduation_year" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                @php
                                    $currentYear = date('Y') + 2; // Années futures automatiques
                                    $editYears = [];
                                    for ($y = 2018; $y <= $currentYear; $y++) {
                                        $editYears[] = $y . '-' . ($y + 1);
                                    }
                                    if ($trainee->graduation_year && !in_array($trainee->graduation_year, $editYears)) {
                                        $editYears[] = $trainee->graduation_year;
                                    }
                                    rsort($editYears); // Tri décroissant (plus récent en haut)
                                @endphp
                                @foreach($editYears as $yr)
                                    <option value="{{ $yr }}" {{ old('graduation_year', $trainee->graduation_year) == $yr ? 'selected' : '' }}>
                                        {{ $yr }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Statut <span class="text-danger">*</span></label>
                            <select name="statut" class="form-control" required>
                                <option value="en_formation" {{ old('statut',$trainee->statut)=='en_formation'?'selected':'' }}>🎓 En formation</option>
                                <option value="diplome" {{ old('statut',$trainee->statut)=='diplome'?'selected':'' }}>✅ Diplômé</option>
                                <option value="abandon" {{ old('statut',$trainee->statut)=='abandon'?'selected':'' }}>❌ Abandon</option>
                                <option value="redoublant" {{ old('statut',$trainee->statut)=='redoublant'?'selected':'' }}>🔄 Redoublant</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bouton de soumission -->
            <div class="card shadow-sm">
                <div class="card-body text-end">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="ti ti-device-floppy me-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
$(document).ready(function() {
    $('.select2').select2({
        width: '100%'
    });
});
</script>
@stop