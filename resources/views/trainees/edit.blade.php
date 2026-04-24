@extends('adminlte::page')

@section('title', 'Modifier stagiaire')

@section('content_header')
    <h1>Modifier — {{ $trainee->last_name }} {{ $trainee->first_name }}</h1>
@stop

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('trainees.update', $trainee) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6">
                    <div class="form-group border p-2 rounded">
                        <label>CIN Stagiaire *</label>
                        <input type="text" name="cin" class="form-control mb-2" value="{{ old('cin',$trainee->cin) }}" placeholder="Numéro CIN">
                        <label class="small text-muted mb-0"><i class="fas fa-file-upload"></i> Importer CIN Stagiaire depuis l'appareil (PDF/Image)</label>
                        @if($trainee->cin_scan) <a href="{{ asset('storage/'.$trainee->cin_scan) }}" target="_blank" class="badge badge-success ml-2">Voir fichier actuel</a> @endif
                        <input type="file" name="cin_scan" class="form-control-file mt-1">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group border p-2 rounded">
                        <label>CIN Père</label>
                        <input type="text" name="cin_pere" class="form-control mb-2"
                               value="{{ old('cin_pere', $trainee->cin_pere) }}"
                               placeholder="CIN du père">
                        <label class="small text-muted mb-0"><i class="fas fa-file-upload"></i> Importer CIN Père depuis l'appareil (PDF/Image)</label>
                        @if($trainee->cin_pere_scan) <a href="{{ asset('storage/'.$trainee->cin_pere_scan) }}" target="_blank" class="badge badge-success ml-2">Voir fichier actuel</a> @endif
                        <input type="file" name="cin_pere_scan" class="form-control-file mt-1">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group border p-2 rounded">
                        <label>CIN Mère</label>
                        <input type="text" name="cin_mere" class="form-control mb-2"
                               value="{{ old('cin_mere', $trainee->cin_mere) }}"
                               placeholder="CIN de la mère">
                        <label class="small text-muted mb-0"><i class="fas fa-file-upload"></i> Importer CIN Mère depuis l'appareil (PDF/Image)</label>
                        @if($trainee->cin_mere_scan) <a href="{{ asset('storage/'.$trainee->cin_mere_scan) }}" target="_blank" class="badge badge-success ml-2">Voir fichier actuel</a> @endif
                        <input type="file" name="cin_mere_scan" class="form-control-file mt-1">
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group p-2">
                        <label>CEF</label>
                        <input type="text" name="cef" class="form-control" value="{{ old('cef',$trainee->cef) }}" placeholder="Numéro CEF">
                    </div>
                </div>

                <div class="col-md-6">
                    <label>Prénom *</label>
                    <input type="text" name="first_name" class="form-control" value="{{ old('first_name',$trainee->first_name) }}">
                </div>

                <div class="col-md-6">
                    <label>Nom *</label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name',$trainee->last_name) }}">
                </div>

                <div class="col-md-6">
                    <label>Date naissance</label>
                    <input type="date" name="date_naissance" class="form-control" value="{{ old('date_naissance',$trainee->date_naissance) }}">
                </div>

                <div class="col-md-6">
                    <label>Téléphone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone',$trainee->phone) }}">
                </div>

                <!-- Filière -->
                <div class="col-md-6">
                    <label>Filière *</label>
                    <select name="filiere_id" class="form-control select2">
                        @foreach($filieres as $filiere)
                            <option value="{{ $filiere->id }}"
                                {{ old('filiere_id',$trainee->filiere_id)==$filiere->id?'selected':'' }}>
                                {{ $filiere->nom_filiere }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Groupe -->
                <div class="col-md-6">
                    <label>Groupe *</label>
                    <input type="text" name="group" class="form-control" value="{{ old('group',$trainee->group) }}">
                </div>

                <!-- Année -->
                <div class="col-md-6">
                    <label>Année de promotion *</label>
                    <select name="graduation_year" class="form-control">
                        <option value="">-- Choisir --</option>
                        @php
                            $currentYear = date('Y');
                            $editYears = [];
                            for ($y = 2019; $y <= $currentYear; $y++) {
                                $editYears[] = $y . '-' . ($y + 1);
                            }
                        @endphp
                        @foreach(array_reverse($editYears) as $yr)
                            <option value="{{ $yr }}"
                                {{ old('graduation_year', $trainee->graduation_year) == $yr ? 'selected' : '' }}>
                                {{ $yr }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- ✅ Statut -->
                <div class="col-md-6">
                    <label>Statut *</label>
                    <select name="statut" class="form-control">
                        <option value="en_formation" {{ old('statut',$trainee->statut)=='en_formation'?'selected':'' }}>🎓 En formation</option>
                        <option value="diplome" {{ old('statut',$trainee->statut)=='diplome'?'selected':'' }}>✅ Diplômé</option>
                        <option value="abandon" {{ old('statut',$trainee->statut)=='abandon'?'selected':'' }}>❌ Abandon</option>
                        <option value="redoublant" {{ old('statut',$trainee->statut)=='redoublant'?'selected':'' }}>🔄 Redoublant</option>
                    </select>
                </div>

                <!-- Photo -->
                <div class="col-md-6">
                    @if($trainee->image_profile)
                        <img src="{{ asset('storage/'.$trainee->image_profile) }}" width="80">
                    @endif
                    <input type="file" name="image_profile" class="form-control mt-2">
                </div>

            </div>

            <button class="btn btn-warning mt-3">Modifier</button>
        </form>
    </div>
</div>
@stop

@section('js')
<script>
$('.select2').select2();
</script>
@stop