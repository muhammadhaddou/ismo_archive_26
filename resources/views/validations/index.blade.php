@extends('adminlte::page')
@section('title', 'Registre des validations')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-check-double"></i> Registre des validations</h1>
        <span class="badge badge-success" style="font-size:14px">
            {{ $validations->total() }} stagiaires validés
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
        <form method="GET" action="{{ route('validations.index') }}">
            <div class="row">
                <div class="col-md-3">
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
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label>Année</label>
                    <select name="graduation_year" class="form-control">
                        <option value="">— Toutes —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}"
                                {{ request('graduation_year') == $y ? 'selected' : '' }}>
                                {{ $y }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="{{ route('validations.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body table-responsive">
        <table id="val-table" class="table table-bordered table-hover">
            <thead class="bg-success">
                <tr>
                    <th>#</th>
                    <th>Stagiaire</th>
                    <th>CIN / CEF</th>
                    <th>Filière</th>
                    <th>Groupe</th>
                    <th>Année</th>
                    <th>Bac</th>
                    <th>Diplôme</th>
                    <th>Attestation</th>
                    <th>Bulletin</th>
                    <th>Date validation</th>
                    <th>Validé par</th>
                    <th>Signature</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($validations as $val)
                @php
                    $trainee = $val->trainee;
                    $docs    = $trainee->documents->groupBy('type');
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>
                        <a href="{{ route('trainees.show', $trainee) }}">
                            {{ $trainee->last_name }} {{ $trainee->first_name }}
                        </a>
                    </td>
                    <td>
                        {{ $trainee->cin }}
                        @if($trainee->cef)
                            <br><small class="text-muted">{{ $trainee->cef }}</small>
                        @endif
                    </td>
                    <td>{{ $trainee->filiere->nom_filiere }}</td>
                    <td>{{ $trainee->group }}</td>
                    <td>{{ $trainee->graduation_year }}</td>

                    {{-- حالة كل وثيقة --}}
                    @foreach(['Bac','Diplome','Attestation','Bulletin'] as $type)
                    @php
                        $doc = isset($docs[$type]) ? $docs[$type]->first() : null;
                    @endphp
                    <td class="text-center">
                        @if(!$doc)
                            <span class="badge badge-light border">
                                <i class="fas fa-times text-danger"></i>
                            </span>
                        @elseif($doc->status == 'Final_Out')
                            <span class="badge badge-danger"
                                  title="Retrait définitif">
                                <i class="fas fa-sign-out-alt"></i> Définitif
                            </span>
                        @elseif($doc->status == 'Remis')
                            <span class="badge badge-success"
                                  title="Remis">
                                <i class="fas fa-check"></i> Remis
                            </span>
                        @elseif($doc->status == 'Temp_Out')
                            <span class="badge badge-warning"
                                  title="Retrait temporaire">
                                <i class="fas fa-clock"></i> Temp.
                            </span>
                        @else
                            <span class="badge badge-secondary"
                                  title="En stock">
                                <i class="fas fa-archive"></i> Stock
                            </span>
                        @endif
                    </td>
                    @endforeach

                    <td>
                        <span class="badge badge-success">
                            {{ $val->date_validation->format('d/m/Y') }}
                        </span>
                    </td>
                    <td>{{ $val->user->name }}</td>
                    <td class="text-center">
                        @if($val->signature_scan)
                            <a href="{{ asset('storage/' . $val->signature_scan) }}"
                               target="_blank"
                               class="btn btn-sm btn-info"
                               title="Voir le scan">
                                <i class="fas fa-file-image"></i>
                            </a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('validations.show', $trainee) }}"
                           class="btn btn-sm btn-primary"
                           title="Voir détails">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('validations.destroy', $val) }}"
                              method="POST" style="display:inline">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer cette validation?')"
                                    title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $validations->links() }}
    </div>
</div>
@stop

@section('js')
<script>
    $('#val-table').DataTable({
        "language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"},
        "paging": false,
        "order": [[10, "desc"]],
        "scrollX": true
    });
    $('.select2').select2();
</script>
@stop