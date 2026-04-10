@extends('adminlte::page')
@section('title', 'Stats — ' . $filiere->nom_filiere)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>
                <i class="fas fa-chart-bar text-primary"></i>
                {{ $filiere->nom_filiere }}
                <small class="text-muted">{{ $filiere->code_filiere }} — {{ $filiere->niveau }}</small>
            </h1>
            <p class="text-muted mb-0">{{ $filiere->secteur->nom_secteur }}</p>
        </div>
        <div>
            <a href="{{ route('trainees.index', ['filiere_id' => $filiere->id]) }}"
               class="btn btn-primary">
                <i class="fas fa-users"></i> Voir les stagiaires
            </a>
            <a href="{{ route('filieres.index') }}" class="btn btn-secondary ml-2">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>
@stop

@section('content')

{{-- Stats générales --}}
<div class="row">
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $total_trainees }}</h3>
                <p>Total stagiaires</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $diplomes }}</h3>
                <p>Diplômés</p>
            </div>
            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3>{{ $valides }}</h3>
                <p>Validations complètes</p>
            </div>
            <div class="icon"><i class="fas fa-check-double"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $bac_retard->count() }}</h3>
                <p>Bac — Retard</p>
            </div>
            <div class="icon"><i class="fas fa-exclamation-triangle"></i></div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Chart Statut --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-primary">
                <h3 class="card-title text-white">
                    <i class="fas fa-chart-pie"></i> Statut des stagiaires
                </h3>
            </div>
            <div class="card-body">
                <canvas id="statutChart" height="200"></canvas>
            </div>
        </div>
    </div>

    {{-- Chart Documents --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-success">
                <h3 class="card-title text-white">
                    <i class="fas fa-chart-bar"></i> État des documents
                </h3>
            </div>
            <div class="card-body">
                <canvas id="docsChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">

    {{-- Tableau par groupe --}}
    <div class="col-md-5">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title text-white">
                    <i class="fas fa-layer-group"></i> Par groupe
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Groupe</th>
                            <th>Total</th>
                            <th>En formation</th>
                            <th>Diplômés</th>
                            <th>%</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($groups as $g)
                        <tr>
                            <td><strong>{{ $g->group }}</strong></td>
                            <td>{{ $g->total }}</td>
                            <td>{{ $g->en_formation }}</td>
                            <td>
                                <span class="badge badge-success">{{ $g->diplomes }}</span>
                            </td>
                            <td>
                                @php $pct = $g->total > 0 ? round($g->diplomes / $g->total * 100) : 0 @endphp
                                <div class="progress" style="height:15px">
                                    <div class="progress-bar bg-success"
                                         style="width:{{ $pct }}%">
                                        {{ $pct }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Bac retard --}}
    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-warning">
                <h3 class="card-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Bac — Retard de retour
                    <span class="badge badge-danger ml-2">{{ $bac_retard->count() }}</span>
                </h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Stagiaire</th>
                            <th>CIN</th>
                            <th>Téléphone</th>
                            <th>Retard</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bac_retard as $doc)
                        @php
                            $mv = $doc->movements->where('action_type','Sortie')->sortByDesc('date_action')->first();
                            $diff = $mv?->deadline ? \Carbon\Carbon::parse($mv->deadline)->diff(now()) : null;
                            $overdue = $diff ? ($diff->days > 0 ? $diff->days.'j '.$diff->h.'h' : $diff->h.'h '.$diff->i.'min') : '—';
                        @endphp
                        <tr class="table-danger">
                            <td>{{ $doc->trainee->last_name }} {{ $doc->trainee->first_name }}</td>
                            <td>{{ $doc->trainee->cin }}</td>
                            <td>
                                @if($doc->trainee->phone)
                                    <a href="tel:{{ $doc->trainee->phone }}">{{ $doc->trainee->phone }}</a>
                                @else —
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-danger">{{ $overdue }}</span>
                            </td>
                            <td>
                                <a href="{{ route('documents.show', $doc) }}"
                                   class="btn btn-xs btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-success py-2">
                                <i class="fas fa-check-circle"></i> Aucun retard
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Derniers ajouts --}}
<div class="card">
    <div class="card-header bg-secondary">
        <h3 class="card-title text-white">
            <i class="fas fa-clock"></i> Derniers stagiaires ajoutés
        </h3>
    </div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>CIN</th>
                    <th>Groupe</th>
                    <th>Statut</th>
                    <th>Documents</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent_trainees as $t)
                <tr>
                    <td>{{ $t->last_name }} {{ $t->first_name }}</td>
                    <td>{{ $t->cin }}</td>
                    <td>{{ $t->group }}</td>
                    <td>
                        @if($t->statut == 'diplome')
                            <span class="badge badge-success">Diplômé</span>
                        @elseif($t->statut == 'abandon')
                            <span class="badge badge-danger">Abandon</span>
                        @else
                            <span class="badge badge-info">En formation</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge badge-primary">{{ $t->documents->count() }} doc(s)</span>
                    </td>
                    <td>
                        <a href="{{ route('trainees.show', $t) }}"
                           class="btn btn-xs btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
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
// Chart Statut
new Chart(document.getElementById('statutChart'), {
    type: 'doughnut',
    data: {
        labels: ['En formation', 'Diplômés', 'Abandon', 'Redoublants'],
        datasets: [{
            data: [{{ $en_formation }}, {{ $diplomes }}, {{ $abandon }}, 0],
            backgroundColor: ['#17a2b8', '#28a745', '#dc3545', '#ffc107'],
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});

// Chart Documents
new Chart(document.getElementById('docsChart'), {
    type: 'bar',
    data: {
        labels: ['Baccalauréat', 'Diplôme', 'Attestation', 'Bulletin'],
        datasets: [
            {
                label: 'En stock',
                data: [
                    {{ $docs_stats['Bac']['stock'] }},
                    {{ $docs_stats['Diplome']['stock'] }},
                    {{ $docs_stats['Attestation']['stock'] }},
                    {{ $docs_stats['Bulletin']['stock'] }}
                ],
                backgroundColor: '#28a745'
            },
            {
                label: 'Retrait temp.',
                data: [
                    {{ $docs_stats['Bac']['temp_out'] }},
                    {{ $docs_stats['Diplome']['temp_out'] }},
                    0, 0
                ],
                backgroundColor: '#ffc107'
            },
            {
                label: 'Remis / Définitif',
                data: [
                    {{ $docs_stats['Bac']['final_out'] }},
                    {{ $docs_stats['Diplome']['final_out'] }},
                    {{ $docs_stats['Attestation']['final_out'] }},
                    {{ $docs_stats['Bulletin']['final_out'] }}
                ],
                backgroundColor: '#007bff'
            },
        ]
    },
    options: {
        responsive: true,
        scales: { x: { stacked: false }, y: { beginAtZero: true } }
    }
});
</script>
@stop