@extends('layouts.app')

@section('title', 'Paramètres - Disponibilités')

@section('content_header')
    <h1><i class="fas fa-clock text-info"></i> Paramètres des horaires de retrait</h1>
@stop

@section('content')
<div class="card card-info card-outline shadow">
    <div class="card-header">
        <h3 class="card-title">Définir les créneaux par type de document</h3>
        <p class="text-muted text-sm mb-0 mt-2">
            Ces horaires serviront de texte informatif. Les stagiaires les verront lorsqu'ils seront sur le point de faire une demande.
        </p>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.settings.availability.update') }}" method="POST">
            @csrf
            
            <div class="row">
                @foreach($documentTypes as $type)
                    <div class="col-md-6 mb-4">
                        <div class="form-group border rounded p-3 bg-light">
                            <label class="text-primary"><i class="fas fa-file-alt"></i> Disponibilité : {{ $type }}</label>
                            <textarea name="availabilities[{{ $type }}]" class="form-control" rows="2" placeholder="Ex: Mardi et Jeudi, de 14h à 16h">{{ old('availabilities.'.$type, isset($availabilities[$type]) ? $availabilities[$type]->description : '') }}</textarea>
                            <small class="text-muted">Laissez vide si vous n'avez pas d'horaire spécifique.</small>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-info"><i class="fas fa-save"></i> Enregistrer les paramètres</button>
        </form>
    </div>
</div>
@stop
