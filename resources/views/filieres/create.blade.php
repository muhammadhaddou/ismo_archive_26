@extends('layouts.app')
@section('title', 'Ajouter filière')
@section('content_header')
    <h1><i class="fas fa-plus"></i> Ajouter une filière</h1>
@stop
@section('content')
<div class="card col-md-8">
    <div class="card-body">
        <form action="{{ route('filieres.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Secteur <span class="text-danger">*</span></label>
                        <select name="secteur_id" class="form-control select2 @error('secteur_id') is-invalid @enderror" required>
                            <option value="">-- Choisir --</option>
                            @foreach($secteurs as $secteur)
                                <option value="{{ $secteur->id }}" {{ old('secteur_id') == $secteur->id ? 'selected' : '' }}>
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
                               value="{{ old('code_filiere') }}" placeholder="ex: DD">
                        @error('code_filiere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Nom de la filière <span class="text-danger">*</span></label>
                        <input type="text" name="nom_filiere"
                               class="form-control @error('nom_filiere') is-invalid @enderror"
                               value="{{ old('nom_filiere') }}">
                        @error('nom_filiere')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label>Niveau <span class="text-danger">*</span></label>
                        <select name="niveau" class="form-control @error('niveau') is-invalid @enderror" required>
                            <option value="">--</option>
                            <option value="TS">TS</option>
                            <option value="T">T</option>
                            <option value="Q">Q</option>
                            <option value="S">S</option>
                            <option value="BP">BP</option>
                        </select>
                        @error('niveau')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
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