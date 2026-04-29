@extends('layouts.app')
@section('title', 'Secteurs')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-building"></i> Secteurs</h1>
        <a href="{{ route('secteurs.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Ajouter
        </a>
    </div>
@stop
@section('content')
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
        <table id="secteurs-table" class="table table-bordered table-hover">
            <thead class="bg-primary">
                <tr>
                    <th>#</th>
                    <th>Nom du secteur</th>
                    <th>Nb. filières</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($secteurs as $secteur)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $secteur->nom_secteur }}</td>
                    <td>
                        <span class="badge bg-info">{{ $secteur->filieres_count }}</span>
                    </td>
                    <td>
                        <a href="{{ route('secteurs.edit', $secteur) }}" class="btn btn-sm btn-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('secteurs.destroy', $secteur) }}" method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Confirmer la suppression?')">
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
    $('#secteurs-table').DataTable({
        "language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"}
    });
</script>
@stop