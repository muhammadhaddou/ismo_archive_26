@extends('layouts.app')
@section('title', 'Retraits définitifs — Bac')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-sign-out-alt"></i> Bac — Retraits définitifs</h1>
        <span class="badge bg-danger" style="font-size:14px">
            {{ $documents->total() }} retraits définitifs
        </span>
    </div>
@stop

@section('content')

{{-- Filters --}}
<div class="card mb-3">
    <div class="card-header bg-light">
        <h3 class="card-title"><i class="fas fa-filter"></i> Filtres</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('documents.bac.final-out') }}">
            <div class="row">
                <div class="col-md-4">
                    <label>Filière</label>
                    <select name="filiere_id" class="form-control select2">
                        <option value="">— Toutes —</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}"
                                {{ request('filiere_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->nom_filiere }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Groupe</label>
                    <select name="group" class="form-control">
                        <option value="">— Tous —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g }}"
                                {{ request('group') == $g ? 'selected' : '' }}>
                                {{ $g }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('documents.bac.final-out') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table id="finalout-table" class="table table-bordered table-hover">
            <thead class="bg-danger">
                <tr>
                    <th>#</th>
                    <th>Stagiaire</th>
                    <th>CIN</th>
                    <th>CEF</th>
                    <th>Téléphone</th>
                    <th>Filière</th>
                    <th>Groupe</th>
                    <th>Date retrait</th>
                    <th>Signature</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                @php
                    $lastSortie = $doc->movements
                        ->where('action_type', 'Sortie')
                        ->sortByDesc('date_action')
                        ->first();
                    $validation = $doc->trainee->validation;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <a href="{{ route('trainees.show', $doc->trainee) }}">
                            {{ $doc->trainee->last_name }} {{ $doc->trainee->first_name }}
                        </a>
                    </td>
                    <td>{{ $doc->trainee->cin }}</td>
                    <td>{{ $doc->trainee->cef ?? '—' }}</td>
                    <td>
                        @if($doc->trainee->phone)
                            <a href="tel:{{ $doc->trainee->phone }}">
                                {{ $doc->trainee->phone }}
                            </a>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $doc->trainee->filiere->nom_filiere }}</td>
                    <td>{{ $doc->trainee->group }}</td>
                    <td>
                        {{ $lastSortie
                            ? \Carbon\Carbon::parse($lastSortie->date_action)->format('d/m/Y H:i')
                            : '—' }}
                    </td>
                    <td class="text-center">
                        @if($validation && $validation->signature_scan)
                            <a href="{{ asset('storage/' . $validation->signature_scan) }}"
                               target="_blank"
                               class="btn btn-sm btn-info"
                               title="Voir signature">
                                <i class="fas fa-file-image"></i> Voir
                            </a>
                        @else
                            @if(!$validation)
                                <a href="{{ route('validations.create', $doc->trainee) }}"
                                   class="btn btn-sm btn-success"
                                   title="Ajouter signature">
                                    <i class="fas fa-upload"></i> Scanner
                                </a>
                            @else
                                <span class="badge bg-secondary">Sans signature</span>
                            @endif
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('documents.show', $doc) }}"
                           class="btn btn-sm btn-primary">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle fa-2x"></i>
                        <br>
                        <strong>Aucun retrait définitif enregistré</strong>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $documents->links() }}
    </div>
</div>
@stop

@section('js')
<script>
    $('#finalout-table').DataTable({
        "language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"},
        "paging": false,
        "order": [[7, "desc"]]
    });
    $('.select2').select2();
</script>
@stop