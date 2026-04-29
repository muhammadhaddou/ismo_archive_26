@extends('layouts.app')
@section('title', 'Documents — ' . ($type ?? 'Tous'))
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-folder-open"></i> Documents — {{ $type ?? 'Tous' }}</h1>
        <a href="{{ route('documents.create') . ($type ? '?type='.$type : '') }}" class="btn btn-primary">
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

{{-- Filtre de recherche --}}
<div class="card card-outline card-primary mb-3">
    <div class="card-body py-2">
        <form method="GET" action="{{ request()->url() }}" class="form-row align-items-end">
            @if($type)
                <input type="hidden" name="type" value="{{ $type }}">
            @endif

            {{-- Recherche par Nom --}}
            <div class="col-md-3 mb-2">
                <label class="mb-1 font-weight-bold small">Nom / Prénom</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                    </div>
                    <input type="text" name="search" class="form-control"
                           placeholder="Rechercher par nom..."
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- Recherche par CIN / CEF --}}
            <div class="col-md-3 mb-2">
                <label class="mb-1 font-weight-bold small">CIN / CEF</label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                    </div>
                    <input type="text" name="cin" class="form-control"
                           placeholder="Rechercher par CIN ou CEF..."
                           value="{{ request('cin') }}">
                </div>
            </div>

            {{-- Filtre Filière --}}
            <div class="col-md-3 mb-2">
                <label class="mb-1 font-weight-bold small">Filière</label>
                <select name="filiere_id" class="form-control form-control-sm">
                    <option value="">— Toutes les filières —</option>
                    @foreach($filieres as $f)
                        <option value="{{ $f->id }}" {{ request('filiere_id') == $f->id ? 'selected' : '' }}>
                            {{ $f->code_filiere }} — {{ $f->nom_filiere }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre Statut --}}
            <div class="col-md-3 mb-2">
                <label class="mb-1 font-weight-bold small">Statut</label>
                <select name="status" class="form-control form-control-sm">
                    <option value="">— Tous les statuts —</option>
                    <option value="Stock"     {{ request('status') == 'Stock'     ? 'selected' : '' }}>✅ En stock</option>
                    <option value="Temp_Out"  {{ request('status') == 'Temp_Out'  ? 'selected' : '' }}>🟡 Retrait temporaire</option>
                    <option value="Final_Out" {{ request('status') == 'Final_Out' ? 'selected' : '' }}>🔴 Retrait définitif</option>
                    <option value="Ecoule"    {{ request('status') == 'Ecoule'   ? 'selected' : '' }}>⛔ Écoulé</option>
                    <option value="Remis"     {{ request('status') == 'Remis'    ? 'selected' : '' }}>📦 Remis</option>
                </select>
            </div>

            {{-- Boutons --}}
            <div class="col-md-2 mb-2 d-flex" style="gap:6px">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="{{ request()->url() }}{{ $type ? '?type='.$type : '' }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Résumé résultats --}}
<div class="mb-2 small text-muted">
    <i class="fas fa-info-circle"></i>
    {{ $documents->total() }} document(s) trouvé(s)
    @if(request('search')) — recherche : <strong>{{ request('search') }}</strong> @endif
</div>

{{-- Tableau --}}
<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="bg-primary text-white">
                <tr>
                    <th>Stagiaire</th>
                    <th>CIN</th>
                    <th>Filière</th>
                    @if(!$type)
                    <th>Type</th>
                    @endif
                    <th>Référence</th>
                    <th>Scan</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr class="pointer-row" onclick="window.location='{{ route('trainees.show', $doc->trainee) }}';">
                    <td class="font-weight-bold">
                        {{ strtoupper($doc->trainee->last_name) }} {{ ucfirst(strtolower($doc->trainee->first_name)) }}
                    </td>
                    <td>{{ $doc->trainee->cin }}</td>
                    <td>{{ $doc->trainee->filiere->code_filiere ?? '—' }}</td>
                    @if(!$type)
                    <td><span class="badge bg-primary">{{ $doc->type }}</span></td>
                    @endif
                    <td>{{ $doc->reference_number ?? '—' }}</td>
                    <td>
                        @if($doc->scan_file)
                            <a href="{{ asset('storage/' . $doc->scan_file) }}" target="_blank"
                               class="btn btn-xs btn-outline-success" onclick="event.stopPropagation();"
                               style="font-size:11px">
                                <i class="fas fa-file-pdf"></i> Voir
                            </a>
                        @else
                            <span class="text-muted small">—</span>
                        @endif
                    </td>
                    <td>
                        @php
                            $statuts = [
                                'Stock'     => ['bg-success',  'En stock'],
                                'Temp_Out'  => ['bg-warning',  'Retrait temp.'],
                                'Final_Out' => ['bg-danger',   'Retrait déf.'],
                                'Ecoule'    => ['bg-dark',     'Écoulé'],
                                'Remis'     => ['bg-info',     'Remis'],
                            ];
                            [$cls, $lbl] = $statuts[$doc->status] ?? ['bg-secondary', $doc->status];
                        @endphp
                        <span class="badge {{ $cls }}">{{ $lbl }}</span>
                    </td>
                    <td onclick="event.stopPropagation();">
                        <a href="{{ route('documents.show', $doc) }}" class="btn btn-xs btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        <i class="fas fa-inbox fa-2x mb-2"></i><br>Aucun document trouvé
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $documents->links() }}
    </div>
</div>

<style>
.pointer-row { cursor: pointer; }
.pointer-row:hover { background-color: #f4f6f9; }
</style>

@stop