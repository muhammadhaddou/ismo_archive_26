@extends('layouts.app')

@section('title', 'Filières')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-code-branch"></i> Filières</h1>
        <a href="{{ route('filieres.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter
        </a>
    </div>
@stop

@section('content')

{{-- Messages --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        {{ session('error') }}
    </div>
@endif

<div class="card">
    <div class="card-body">
        <table id="filieres-table" class="table table-bordered table-hover">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Code</th>
                    <th>Nom</th>
                    <th>Secteur</th>
                    <th>Niveau</th>
                    <th>Stagiaires</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($filieres as $filiere)
                <tr>
                    <td>
                        <span class="badge bg-primary">
                            {{ $filiere->code_filiere }}
                        </span>
                    </td>

                    <td>{{ $filiere->nom_filiere }}</td>

                    <td>{{ $filiere->secteur->nom_secteur }}</td>

                    <td>
                        <span class="badge bg-info">
                            {{ $filiere->niveau }}
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-success">
                            {{ $filiere->trainees_count }}
                        </span>
                    </td>

                    <td class="text-center">
                        {{-- Statistiques --}}
                        <a href="{{ route('filieres.stats', $filiere) }}"
                           class="btn btn-sm btn-info me-1">
                            <i class="fas fa-chart-bar"></i>
                        </a>

                        {{-- Modifier --}}
                        <a href="{{ route('filieres.edit', $filiere) }}"
                           class="btn btn-sm btn-warning me-1">
                            <i class="fas fa-edit"></i>
                        </a>

                        {{-- Supprimer --}}
                        <form action="{{ route('filieres.destroy', $filiere) }}"
                              method="POST" style="display:inline">
                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Confirmer la suppression ?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@stop

@section('js')
<script>
    $(document).ready(function() {
        $('#filieres-table').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"
            }
        });
    });
</script>
@stop