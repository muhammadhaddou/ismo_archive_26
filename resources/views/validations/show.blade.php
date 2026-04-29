@extends('layouts.app')
@section('title', 'Validation finale')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-check-double text-success"></i> Validation finale</h1>
        <a href="{{ route('trainees.show', $trainee) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
@stop
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">Détails de la validation</h3>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Stagiaire</th>
                        <td>{{ $trainee->last_name }} {{ $trainee->first_name }}</td>
                    </tr>
                    <tr>
                        <th>CIN</th>
                        <td>{{ $trainee->cin }}</td>
                    </tr>
                    <tr>
                        <th>CEF</th>
                        <td>{{ $trainee->cef ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Filière</th>
                        <td>{{ $trainee->filiere->nom_filiere }}</td>
                    </tr>
                    <tr>
                        <th>Date validation</th>
                        <td>
                            <strong class="text-success">
                                {{ $validation->date_validation->format('d/m/Y') }}
                            </strong>
                        </td>
                    </tr>
                    <tr>
                        <th>Validé par</th>
                        <td>{{ $validation->user->name }}</td>
                    </tr>
                    <tr>
                        <th>Observations</th>
                        <td>{{ $validation->observations ?? '—' }}</td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ asset('storage/' . $validation->signature_scan) }}"
                   target="_blank" class="btn btn-info btn-sm">
                    <i class="fas fa-download"></i> Télécharger le scan
                </a>
                <form action="{{ route('validations.destroy', $validation) }}"
                      method="POST" style="display:inline">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm"
                        onclick="return confirm('Supprimer cette validation?')">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-light">
                <h3 class="card-title">
                    <i class="fas fa-signature"></i> Scan de la signature
                </h3>
            </div>
            <div class="card-body text-center">
                @if($validation->signature_scan)
                    <img src="{{ asset('storage/' . $validation->signature_scan) }}"
                         class="img-fluid border rounded shadow-sm"
                         style="max-height:500px"
                         alt="Signature scanée">
                @else
                    <p class="text-muted">Aucun scan disponible</p>
                @endif
            </div>
        </div>
    </div>
</div>
@stop