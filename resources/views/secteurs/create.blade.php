@extends('layouts.app')
@section('title', 'Ajouter secteur')
@section('content_header')
    <h1><i class="fas fa-plus"></i> Ajouter un secteur</h1>
@stop
@section('content')
<div class="card col-md-6">
    <div class="card-body">
        <form action="{{ route('secteurs.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Nom du secteur <span class="text-danger">*</span></label>
                <input type="text" name="nom_secteur"
                       class="form-control @error('nom_secteur') is-invalid @enderror"
                       value="{{ old('nom_secteur') }}" autofocus>
                @error('nom_secteur')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer
            </button>
            <a href="{{ route('secteurs.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </form>
    </div>
</div>
@stop