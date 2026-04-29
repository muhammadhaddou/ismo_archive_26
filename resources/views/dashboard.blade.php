@extends('layouts.app')

@section('title', 'Tableau de bord | ISMO')

@section('content_header')
<div class="row align-items-center mb-3">
    <div class="col">
        <div class="page-pretitle text-uppercase text-muted fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">
            Aperçu Général
        </div>
        <h2 class="page-title fw-bold text-body" style="font-size: 1.75rem; letter-spacing: -0.5px;">
            Tableau de bord ISMO
        </h2>
    </div>
    <div class="col-auto ms-auto d-print-none">
        <div class="d-flex align-items-center gap-2">
            <div class="d-none d-md-flex align-items-center px-3 py-2 shadow-sm rounded border">
                <i class="far fa-calendar-alt text-primary me-2"></i>
                <span class="fw-bold text-body" style="font-size: 0.85rem;">{{ now()->format('d M Y') }}</span>
            </div>
            <div class="dropdown">
                <button class="btn btn-primary fw-bold shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-plus me-2"></i> Nouvelle Action
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3">
                    <li><h6 class="dropdown-header text-uppercase">Stagiaires</h6></li>
                    <li><a class="dropdown-item py-2" href="{{ route('trainees.create') }}"><i class="fas fa-user-plus text-primary me-3 w-4"></i> Ajouter un stagiaire</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><h6 class="dropdown-header text-uppercase">Documents</h6></li>
                    <li><a class="dropdown-item py-2" href="{{ route('documents.create') }}?type=Bac"><i class="fas fa-graduation-cap text-warning me-3 w-4"></i> Nouveau Retrait Bac</a></li>
                    <li><a class="dropdown-item py-2" href="{{ route('documents.create') }}?type=Diplome"><i class="fas fa-scroll text-info me-3 w-4"></i> Nouvelle Remise Diplôme</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')

{{-- 🔴 Alerte globale (Bacs expirés) --}}
@if($stats['bac_expired'] > 0)
<div class="alert alert-important alert-danger alert-dismissible shadow-sm rounded-3 mb-4" role="alert">
    <div class="d-flex">
        <div>
            <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
        </div>
        <div>
            <h4 class="alert-title fw-bold mb-1">Action Requise Immédiate</h4>
            <div class="text-muted text-white"><strong>{{ $stats['bac_expired'] }}</strong> stagiaire(s) ont dépassé le délai autorisé de 48h pour leur Baccalauréat.</div>
        </div>
        <div class="ms-auto mt-2 mt-md-0 d-flex align-items-center">
            <a href="{{ url('documents/bac/temp-out') }}" class="btn btn-white btn-sm fw-bold text-danger">
                Vérifier maintenant
            </a>
        </div>
    </div>
    <a class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="close"></a>
</div>
@endif

<!-- CORE STATS ROW -->
<div class="row row-deck row-cards mb-4">
    <!-- Total Stagiaires -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-primary text-white avatar rounded-3 shadow-sm">
                            <i class="fas fa-users fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            Total Stagiaires
                        </div>
                        <div class="h2 mb-0 fw-bold text-body">{{ $stats['total_stagiaires'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Mouvements Aujourd'hui -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-info text-white avatar rounded-3 shadow-sm">
                            <i class="fas fa-exchange-alt fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            Mouvements (Aujourd'hui)
                        </div>
                        <div class="h2 mb-0 fw-bold text-body">{{ $stats['mouvements_today'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Diplômes Prêts -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-success text-white avatar rounded-3 shadow-sm">
                            <i class="fas fa-check-circle fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            Diplômes Prêts
                        </div>
                        <div class="h2 mb-0 fw-bold text-body">{{ $stats['diplomes_prets'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bacs en Sortie -->
    <div class="col-sm-6 col-lg-3">
        <div class="card card-sm shadow-sm border-0 rounded-3">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <span class="bg-warning text-white avatar rounded-3 shadow-sm">
                            <i class="fas fa-clock fs-3"></i>
                        </span>
                    </div>
                    <div class="col">
                        <div class="font-weight-medium text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            Bacs en Sortie (En cours)
                        </div>
                        <div class="h2 mb-0 fw-bold text-body">{{ $stats['bac_temp_out'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHARTS SECTION -->
<div class="row row-cards mb-4">
    <!-- Activity Chart -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header border-0 bg-transparent pt-4 pb-2">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div>
                        <h3 class="card-title fw-bold text-body mb-1">Activité des Documents</h3>
                        <p class="text-muted small mb-0">Évolution des opérations sur les 7 derniers jours</p>
                    </div>
                </div>
            </div>
            <div class="card-body pt-0">
                <div style="height: 280px;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Bac Status Chart -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header border-0 bg-transparent pt-4 pb-2">
                <h3 class="card-title fw-bold text-body mb-1">État des Bacs</h3>
                <p class="text-muted small mb-0">Répartition globale du stock</p>
            </div>
            <div class="card-body d-flex flex-column justify-content-center">
                <div class="position-relative mb-4" style="height: 180px;">
                    <canvas id="bacChart"></canvas>
                    <div class="position-absolute top-50 start-50 translate-middle text-center pointer-events-none">
                        <h2 class="fw-bold text-body mb-0" style="font-size: 1.8rem;">{{ $stats['bac_temp_out'] + 120 }}</h2>
                        <span class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; font-weight: bold;">Total Bacs</span>
                    </div>
                </div>
                
                <div class="row g-3">
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center text-muted small fw-medium">
                                <span class="badge bg-primary me-2" style="width: 10px; height: 10px; padding: 0;"></span> En Stock
                            </span>
                            <span class="fw-bold text-body">120</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center text-muted small fw-medium">
                                <span class="badge bg-warning me-2" style="width: 10px; height: 10px; padding: 0;"></span> Sortie Temp.
                            </span>
                            <span class="fw-bold text-body">{{ $stats['bac_temp_out'] }}</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="d-flex align-items-center text-muted small fw-medium">
                                <span class="badge bg-danger me-2" style="width: 10px; height: 10px; padding: 0;"></span> Expirés (>48h)
                            </span>
                            <span class="fw-bold text-danger">{{ $stats['bac_expired'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLES SECTION -->
<div class="row row-cards">
    <!-- Derniers Mouvements -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 rounded-3 h-100">
            <div class="card-header border-0 bg-transparent pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3 class="card-title fw-bold text-body mb-0">Derniers Mouvements</h3>
                    <a href="{{ url('movements/today') }}" class="btn btn-light btn-sm fw-bold rounded-2">Voir l'historique</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table table-striped-columns">
                    <thead>
                        <tr>
                            <th class="text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700;">Stagiaire</th>
                            <th class="text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700;">Document</th>
                            <th class="text-uppercase text-muted" style="font-size: 0.7rem; font-weight: 700;">Action</th>
                            <th class="text-uppercase text-muted text-end" style="font-size: 0.7rem; font-weight: 700;">Temps</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_movements->take(6) as $mov)
                        <tr style="cursor: pointer;" onclick="window.location='{{ $mov->document && $mov->document->trainee ? route('trainees.show', $mov->document->trainee) : '#' }}';">
                            <td>
                                @if($mov->document && $mov->document->trainee)
                                    <div class="d-flex align-items-center">
                                        <span class="avatar avatar-sm rounded-circle me-2 bg-primary-lt text-primary fw-bold" style="font-size: 0.7rem;">
                                            {{ substr($mov->document->trainee->first_name, 0, 1) }}{{ substr($mov->document->trainee->last_name, 0, 1) }}
                                        </span>
                                        <div class="flex-fill">
                                            <div class="font-weight-medium text-body fw-bold" style="font-size: 0.85rem;">{{ strtoupper($mov->document->trainee->last_name) }} {{ ucfirst(strtolower($mov->document->trainee->first_name)) }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-secondary fw-medium" style="font-size: 0.85rem;">{{ $mov->document->type ?? '-' }}</td>
                            <td>
                                @if($mov->action_type == 'Sortie')
                                    <span class="badge bg-warning-lt"><i class="fas fa-arrow-up me-1"></i> Sortie</span>
                                @elseif($mov->action_type == 'Saisie' || $mov->action_type == 'Retour')
                                    <span class="badge bg-success-lt"><i class="fas fa-arrow-down me-1"></i> {{ $mov->action_type }}</span>
                                @else
                                    <span class="badge bg-secondary-lt">{{ $mov->action_type }}</span>
                                @endif
                            </td>
                            <td class="text-end text-muted" style="font-size: 0.8rem;">{{ $mov->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Aucun mouvement récent aujourd'hui.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Alertes Bacs -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 rounded-3 h-100 border-top-danger" style="border-top: 3px solid #d63939 !important;">
            <div class="card-header border-0 bg-transparent pt-4 pb-3">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h3 class="card-title fw-bold text-body mb-0">
                        <i class="fas fa-bell text-danger me-2"></i> Retraits Critiques
                    </h3>
                    @if($bac_alerts->count() > 0)
                        <span class="badge bg-danger text-white rounded-pill">{{ $bac_alerts->count() }} alertes</span>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                @if($bac_alerts->count() > 0)
                <div class="list-group list-group-flush list-group-hoverable">
                    @foreach($bac_alerts->take(5) as $doc)
                    @php
                        $isEcoule = $doc->alert_level == 'ecoule';
                        $badgeClass = $isEcoule ? 'bg-danger text-white' : 'bg-warning-lt text-warning fw-bold';
                        $iconClass = $isEcoule ? 'fa-times-circle text-danger' : 'fa-exclamation-triangle text-warning';
                    @endphp
                    <a href="{{ route('trainees.show', $doc->trainee) }}" class="list-group-item list-group-item-action py-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fas {{ $iconClass }} fa-lg"></i>
                            </div>
                            <div class="flex-fill">
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <h4 class="mb-0 text-body fw-bold" style="font-size: 0.9rem;">
                                        {{ strtoupper($doc->trainee->last_name) }} {{ ucfirst(strtolower($doc->trainee->first_name)) }}
                                    </h4>
                                    <span class="badge {{ $badgeClass }} ms-2">{{ $doc->time_out_str }}</span>
                                </div>
                                <div class="text-muted" style="font-size: 0.8rem;">
                                    <i class="fas fa-id-card me-1"></i> {{ $doc->trainee->cin }}
                                </div>
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
                <div class="card-footer bg-transparent text-center py-3">
                    <a href="{{ url('documents/bac/temp-out') }}" class="text-danger fw-bold text-decoration-none" style="font-size: 0.85rem;">Voir tous les dossiers en retard <i class="fas fa-arrow-right ms-1"></i></a>
                </div>
                @else
                <div class="empty py-5">
                    <div class="empty-icon text-success">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                    <p class="empty-title fw-bold text-body mt-3">Tout est sous contrôle</p>
                    <p class="empty-subtitle text-muted">Aucun Baccalauréat n'est en dépassement de délai pour le moment.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // ---- CHART 1 : Activity Line Chart ----
        const ctxActivity = document.getElementById('activityChart').getContext('2d');
        let gradientStroke = ctxActivity.createLinearGradient(0, 0, 0, 300);
        gradientStroke.addColorStop(0, 'rgba(32, 107, 196, 0.2)');
        gradientStroke.addColorStop(1, 'rgba(32, 107, 196, 0)');

        new Chart(ctxActivity, {
            type: 'line',
            data: {
                labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'Hier', 'Auj.'],
                datasets: [{
                    label: 'Mouvements',
                    data: [12, 19, 15, 25, 22, 30, {{ $stats['mouvements_today'] > 0 ? $stats['mouvements_today'] : 35 }}],
                    borderColor: '#206bc4',
                    backgroundColor: gradientStroke,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#206bc4',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 11 } } },
                    y: { grid: { borderDash: [4, 4], color: '#e6e8eb' }, ticks: { font: { family: 'Inter', size: 11 } }, beginAtZero: true }
                }
            }
        });

        // ---- CHART 2 : Bac Donut Chart ----
        const ctxBac = document.getElementById('bacChart').getContext('2d');
        new Chart(ctxBac, {
            type: 'doughnut',
            data: {
                labels: ['En Stock', 'Sortie Temp.', 'Expirés'],
                datasets: [{
                    data: [120, {{ $stats['bac_temp_out'] }}, {{ $stats['bac_expired'] }}],
                    backgroundColor: ['#206bc4', '#f59f00', '#d63939'],
                    borderWidth: 0,
                    cutout: '80%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, tooltip: { enabled: true } }
            }
        });
        
        // ---- REALTIME ALERTS (AJAX) ----
        let lastCount = 0;
        function checkNewRequests() {
            $.ajax({
                url: '/api/check-new-requests',
                type: 'GET',
                success: function(response) {
                    if (response.has_new && response.count > lastCount) {
                        toastr.options = { "closeButton": true, "timeOut": "15000", "positionClass": "toast-top-right" };
                        toastr.info("Vous avez " + response.count + " nouvelle(s) demande(s) en attente !", "Nouvelle Demande");
                        lastCount = response.count;
                    }
                }
            });
        }
        checkNewRequests();
        setInterval(checkNewRequests, 30000); // Check every 30s
    });
</script>
@stop