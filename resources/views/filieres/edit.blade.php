@extends('layouts.app')
@section('title', 'Modifier filière')
@section('content_header')
    <h1><i class="fas fa-edit"></i> Modifier — {{ $filiere->nom_filiere }}</h1>
@stop
@section('content')
<div class="card col-md-8">
    <div class="card-body">
        <form action="{{ route('filieres.update', $filiere) }}" method="POST">
            @csrf @method('PUT')
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Secteur <span class="text-danger">*</span></label>
                        <select name="secteur_id" class="form-control select2 @error('secteur_id') is-invalid @enderror" required>
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}"
                                    {{ old('secteur_id', $filiere->secteur_id) == $secteur->id ? 'selected' : '' }}>
                                    {{ $secteur->nom_secteur }}
                                </option>
                            @endforeach
                        </select>
                        @error('secteur_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Code filière <span class="text-danger">*</span></label>
                        <input type="text" name="code_filiere"
                               class="form-control @error('code_filiere') is-invalid @enderror"
                               value="{{ old('code_filiere', $filiere->code_filiere) }}">
                        @error('code_filiere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Nom de la filière <span class="text-danger">*</span></label>
                        <input type="text" name="nom_filiere"
                               class="form-control @error('nom_filiere') is-invalid @enderror"
                               value="{{ old('nom_filiere', $filiere->nom_filiere) }}">
                        @error('nom_filiere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Niveau <span class="text-danger">*</span></label>
                        <select name="niveau" class="form-control @error('niveau') is-invalid @enderror" required>
                            @foreach(['TS','T','Q','S','BP'] as $niv)
                                <option value="{{ $niv }}"
                                    {{ old('niveau', $filiere->niveau) == $niv ? 'selected' : '' }}>
                                    {{ $niv }}
                                </option>
                            @endforeach
                        </select>
                        @error('niveau')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i> Modifier
            </button>
            <a href="{{ route('filieres.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </form>
    </div>
</div>
@stop
@section('js')
<script>$('.select2').select2();</script>
@stop