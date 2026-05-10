@extends('layouts.app')

@section('title', 'Liste des stagiaires')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <div class="text-muted small text-uppercase fw-bold mb-1" style="letter-spacing:1px;">Gestion</div>
        <h2 class="fw-bold mb-0" style="font-size:1.6rem;">Stagiaires</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('trainees.import') }}" class="btn btn-outline-success">
            <i class="ti ti-table-import me-1"></i> Importer Excel
        </a>
        <a href="{{ route('trainees.create') }}" class="btn btn-primary">
            <i class="ti ti-user-plus me-1"></i> Ajouter
        </a>
    </div>
</div>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible shadow-sm">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
</div>
@endif

{{-- Filtres --}}
<div class="card mb-3 shadow-sm border-0">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('trainees.index') }}" id="filter-form">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="CIN, CEF, Nom..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filière</label>
                    <select name="filiere_id" id="filiere-select" class="form-control select2">
                        <option value="">— Toutes les filières —</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}" {{ request('filiere_id') == $f->id ? 'selected' : '' }}>
                                {{ $f->code_filiere }} — {{ $f->nom_filiere }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Groupe</label>
                    <select name="group" id="group-select" class="form-control">
                        <option value="">— Tous —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g }}" {{ request('group') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Année</label>
                    <select name="graduation_year" id="year-select" class="form-control">
                        <option value="">— Toutes —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="ti ti-search"></i> Chercher
                    </button>
                    <a href="{{ route('trainees.index') }}" class="btn btn-outline-secondary" title="Réinitialiser">
                        <i class="ti ti-x"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
        <table class="table table-vcenter table-hover mb-0" style="min-width:1100px;">
            <thead>
                <tr style="border-bottom: 2px solid var(--tblr-border-color);">
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem; width:40px;">#</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Stagiaire</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">CIN</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">CEF</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Naissance</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Téléphone</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Filière</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Gr.</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Année</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Statut</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Documents</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem; text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trainees as $t)
                <tr style="cursor:pointer;" onclick="window.location='{{ route('trainees.show', $t) }}'">
                    <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>

                    {{-- Avatar + Nom --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            @if($t->image_profile)
                                <span class="avatar avatar-sm rounded-circle" style="background-image: url('{{ asset('storage/'.$t->image_profile) }}');"></span>
                            @else
                                <span class="avatar avatar-sm rounded-circle bg-primary-lt text-primary fw-bold" style="font-size:.7rem;">
                                    {{ substr($t->first_name,0,1) }}{{ substr($t->last_name,0,1) }}
                                </span>
                            @endif
                            <div>
                                <div class="fw-bold text-body" style="font-size:.875rem;">{{ strtoupper($t->last_name) }} {{ ucfirst(strtolower($t->first_name)) }}</div>
                                <div class="text-muted" style="font-size:.75rem;">{{ $t->sexe ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    <td class="text-body fw-medium font-monospace" style="font-size:.825rem;">{{ $t->cin }}</td>
                    <td class="text-muted" style="font-size:.825rem;">{{ $t->cef ?? '—' }}</td>
                    <td class="text-muted" style="font-size:.825rem;">
                        {{ $t->date_naissance ? \Carbon\Carbon::parse($t->date_naissance)->format('d/m/Y') : '—' }}
                    </td>
                    <td style="font-size:.825rem;">
                        @if($t->phone)
                            <a href="tel:{{ $t->phone }}" class="text-body text-decoration-none" onclick="event.stopPropagation()">{{ $t->phone }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td class="text-muted" style="font-size:.825rem;">{{ $t->filiere->nom_filiere ?? '—' }}</td>
                    <td>
                        <span class="badge bg-blue-lt text-blue fw-bold">{{ $t->group }}</span>
                    </td>
                    <td class="text-muted" style="font-size:.825rem;">{{ $t->graduation_year }}</td>

                    {{-- Statut --}}
                    <td>
                        @if($t->statut == 'diplome')
                            <span class="badge bg-success-lt text-success"><i class="ti ti-certificate me-1"></i>Diplômé</span>
                        @elseif($t->statut == 'abandon')
                            <span class="badge bg-danger-lt text-danger"><i class="ti ti-ban me-1"></i>Abandon</span>
                        @elseif($t->statut == 'redoublant')
                            <span class="badge bg-warning-lt text-warning"><i class="ti ti-refresh me-1"></i>Redoublant</span>
                        @else
                            <span class="badge bg-azure-lt text-azure"><i class="ti ti-school me-1"></i>En formation</span>
                        @endif
                    </td>

                    {{-- Documents --}}
                    <td onclick="event.stopPropagation()">
                        @php $docs = $t->documents->groupBy('type'); @endphp
                        <div class="d-flex flex-column gap-1">
                        @foreach(['Bac','Diplome','Attestation','Bulletin'] as $type)
                            @php
                                $doc   = $docs->get($type)?->first() ?? null;
                                $short = ['Bac'=>'Bac','Diplome'=>'Diplôme','Attestation'=>'Attest.','Bulletin'=>'Bulletin'][$type];
                                if (!$doc) {
                                    $bg='#f1f3f5'; $color='#adb5bd'; $border='1px dashed #ced4da';
                                    $icon='ti-x'; $title='Absent';
                                } elseif(in_array($doc->status,['Remis','Final_Out'])) {
                                    $bg='#ebfbee'; $color='#2f9e44'; $border='1px solid #b2f2bb';
                                    $icon='ti-circle-check-filled'; $title='Remis';
                                } elseif($doc->status=='Temp_Out') {
                                    $bg='#fff9db'; $color='#e67700'; $border='1px solid #ffec99';
                                    $icon='ti-hourglass'; $title='Sorti temporairement';
                                } else {
                                    $bg='#e7f5ff'; $color='#1c7ed6'; $border='1px solid #a5d8ff';
                                    $icon='ti-package'; $title='En stock';
                                }
                            @endphp
                            <span title="{{ $title }}" style="display:inline-flex;align-items:center;gap:5px;padding:3px 8px;border-radius:6px;font-size:.75rem;font-weight:600;background:{{ $bg }};color:{{ $color }};border:{{ $border }};white-space:nowrap;">
                                <i class="ti {{ $icon }}" style="font-size:1rem;flex-shrink:0;"></i>
                                <span>{{ $short }}</span>
                            </span>
                        @endforeach
                        </div>
                    </td>

                    {{-- Actions --}}
                    <td onclick="event.stopPropagation()">
                        <div class="d-flex justify-content-end gap-1 flex-nowrap">
                            <a href="{{ route('trainees.show', $t) }}" class="btn btn-sm btn-outline-primary btn-icon rounded-2" title="Voir profil">
                                <i class="ti ti-eye"></i>
                            </a>
                            <a href="{{ route('trainees.edit', $t) }}" class="btn btn-sm btn-outline-warning btn-icon rounded-2" title="Modifier">
                                <i class="ti ti-edit"></i>
                            </a>
                            <a href="{{ route('trainees.report', $t) }}" target="_blank" class="btn btn-sm btn-outline-secondary btn-icon rounded-2" title="Rapport PDF">
                                <i class="ti ti-file-type-pdf"></i>
                            </a>
                            <form action="{{ route('trainees.destroy', $t) }}" method="POST" style="display:inline" onclick="event.stopPropagation()">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-icon rounded-2" title="Supprimer" onclick="return confirm('Supprimer ce stagiaire ?')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="12" class="text-center py-5">
                        <div class="empty">
                            <div class="empty-icon text-muted"><i class="ti ti-users-off" style="font-size:2.5rem;"></i></div>
                            <p class="empty-title mt-3">Aucun stagiaire trouvé</p>
                            <p class="empty-subtitle text-muted">Essayez d'autres critères de filtrage ou ajoutez un nouveau stagiaire.</p>
                            <a href="{{ route('trainees.create') }}" class="btn btn-primary mt-2"><i class="ti ti-user-plus me-1"></i> Ajouter</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($trainees->hasPages())
        <div class="card-footer d-flex align-items-center border-0 bg-transparent pt-3">
            <p class="m-0 text-muted" style="font-size:.85rem;">
                Affichage de <strong>{{ $trainees->firstItem() }}</strong> à <strong>{{ $trainees->lastItem() }}</strong>
                sur <strong>{{ $trainees->total() }}</strong> stagiaires
            </p>
            <div class="ms-auto">{{ $trainees->links() }}</div>
        </div>
        @endif
    </div>
</div>

@stop

@section('js')
<script>
$('.select2').select2({ width: '100%' });

$('#filiere-select').on('change', function() {
    var filiereId = $(this).val();
    if (!filiereId) {
        $('#group-select').html('<option value="">— Tous —</option>');
        $('#year-select').html('<option value="">— Toutes —</option>');
        return;
    }
    $.get('/api/filiere/' + filiereId + '/groups', function(data) {
        var o = '<option value="">— Tous —</option>';
        data.groups.forEach(function(g) { o += '<option value="'+g+'">'+g+'</option>'; });
        $('#group-select').html(o);
    });
    $.get('/api/filiere/' + filiereId + '/years', function(data) {
        var o = '<option value="">— Toutes —</option>';
        data.years.forEach(function(y) { o += '<option value="'+y+'">'+y+'</option>'; });
        $('#year-select').html(o);
    });
});
</script>
@stop