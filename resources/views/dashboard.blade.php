@extends('adminlte::page')

@section('title', 'Tableau de bord | ISMO')

@section('content')

{{-- 🔴 Alerte globale --}}
@if($stats['bac_expired'] > 0)
<div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mt-3" style="border-radius: 12px;">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
        <div>
            <h5 class="mb-1 font-weight-bold">Action Requise</h5>
            <span><strong>{{ $stats['bac_expired'] }}</strong> stagiaire(s) ont dépassé le délai de 48h pour leur Baccalauréat.</span>
        </div>
        <a href="{{ url('documents/bac/temp-out') }}" class="btn btn-light text-danger ml-auto font-weight-bold" style="border-radius: 8px;">
            Traiter maintenant
        </a>
    </div>
</div>
@endif

<!-- HEADER SECTION -->
<div class="row align-items-center mt-4 mb-4">
    <div class="col-md-6">
        <h2 class="font-weight-bold" style="color: #1e293b; letter-spacing: -0.5px;">Tableau de bord ISMO</h2>
        <p class="text-muted mb-0">Voici l'état actuel de la gestion documentaire.</p>
    </div>
    <div class="col-md-6 text-right d-none d-md-block">
        <div class="d-inline-flex align-items-center bg-white p-2 shadow-sm" style="border-radius: 8px; border: 1px solid #e2e8f0;">
            <i class="far fa-calendar-alt text-primary mr-2 ml-1"></i>
            <span class="font-weight-bold text-dark mr-2">{{ now()->format('d M Y') }}</span>
        </div>
        
        <div class="dropdown d-inline-block ml-2">
            <button class="btn btn-primary font-weight-bold shadow-sm dropdown-toggle" type="button" data-toggle="dropdown" style="border-radius: 8px;">
                <i class="fas fa-plus mr-1"></i> Ajouter
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow border-0" style="border-radius: 12px;">
                <a class="dropdown-item py-2" href="{{ route('trainees.create') }}"><i class="fas fa-user-plus text-primary mr-2"></i> Nouveau stagiaire</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item py-2" href="{{ route('documents.create') }}?type=Bac"><i class="fas fa-graduation-cap text-warning mr-2"></i> Retrait Bac</a>
                <a class="dropdown-item py-2" href="{{ route('documents.create') }}?type=Diplome"><i class="fas fa-scroll text-info mr-2"></i> Remise Diplôme</a>
            </div>
        </div>
    </div>
</div>

<!-- 5 STAT CARDS (Top Row) -->
<div class="row mb-4">
    
    <div class="col-xl col-md-6 mb-3">
        <div class="card phoenix-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Stagiaires</h6>
                    <span class="badge badge-soft-info"><i class="fas fa-users"></i></span>
                </div>
                <h3 class="font-weight-bold text-dark mb-1">{{ $stats['total_stagiaires'] }}</h3>
                <p class="text-muted small mb-0">Total enregistrés</p>
            </div>
        </div>
    </div>

    <div class="col-xl col-md-6 mb-3">
        <div class="card phoenix-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Bac Temp.</h6>
                    <span class="badge badge-soft-warning"><i class="fas fa-clock"></i> Sortie</span>
                </div>
                <h3 class="font-weight-bold text-dark mb-1">{{ $stats['bac_temp_out'] }}</h3>
                <p class="text-muted small mb-0">En cours de retrait</p>
            </div>
        </div>
    </div>

    <div class="col-xl col-md-6 mb-3">
        <div class="card phoenix-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Diplômes</h6>
                    <span class="badge badge-soft-success"><i class="fas fa-check"></i> Prêts</span>
                </div>
                <h3 class="font-weight-bold text-dark mb-1">{{ $stats['diplomes_prets'] }}</h3>
                <p class="text-muted small mb-0">À remettre</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl col-md-6 mb-3">
        <div class="card phoenix-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-muted font-weight-bold text-uppercase" style="font-size: 0.75rem;">Diplômés</h6>
                    <span class="badge badge-soft-secondary"><i class="fas fa-hourglass-half"></i></span>
                </div>
                <h3 class="font-weight-bold text-dark mb-1">{{ $stats['diplomes_en_attente'] }}</h3>
                <p class="text-muted small mb-0">En attente d'action</p>
            </div>
        </div>
    </div>

    <div class="col-xl col-md-12 mb-3">
        <div class="card phoenix-card h-100" style="background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);">
            <div class="card-body text-white">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="font-weight-bold text-uppercase text-white-50" style="font-size: 0.75rem;">Mouvements</h6>
                    <span class="badge bg-white text-primary"><i class="fas fa-bolt"></i> Aujourd'hui</span>
                </div>
                <h3 class="font-weight-bold text-white mb-1">{{ $stats['mouvements_today'] }}</h3>
                <p class="text-white-50 small mb-0">Transactions traitées</p>
            </div>
        </div>
    </div>

</div>

<!-- CHARTS SECTION -->
<div class="row mb-4">
    <div class="col-lg-8 mb-4 mb-lg-0">
        <div class="card phoenix-card h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="font-weight-bold mb-0 text-dark">Activité des documents</h5>
                        <p class="text-muted small mb-0">Évolution des sorties et retours sur 7 jours</p>
                    </div>
                    <select class="custom-select custom-select-sm w-auto" style="border-radius: 6px; border-color: #e2e8f0;">
                        <option>7 Derniers Jours</option>
                    </select>
                </div>
                <div style="height: 300px;">
                    <canvas id="activityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card phoenix-card h-100">
            <div class="card-body">
                <h5 class="font-weight-bold mb-1 text-dark">État des Bacs</h5>
                <p class="text-muted small mb-4">Répartition actuelle</p>
                <div style="height: 200px; position: relative;">
                    <canvas id="bacChart"></canvas>
                    <div class="chart-center-text">
                        <h3 class="font-weight-bold mb-0 text-dark">{{ $stats['bac_temp_out'] + 120 }}</h3>
                        <span class="text-muted small">Total Bacs</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span><i class="fas fa-circle text-primary mr-1"></i> En Stock</span>
                        <span class="font-weight-bold">120</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span><i class="fas fa-circle text-warning mr-1"></i> Sortie Temp.</span>
                        <span class="font-weight-bold">{{ $stats['bac_temp_out'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between small">
                        <span><i class="fas fa-circle text-danger mr-1"></i> Expirés</span>
                        <span class="font-weight-bold">{{ $stats['bac_expired'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLES SECTION -->
<div class="row">
    
    {{-- Derniers Mouvements --}}
    <div class="col-lg-7 mb-4">
        <div class="card phoenix-card h-100">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-dark mb-0">Derniers Mouvements</h5>
                    <a href="{{ url('movements/today') }}" class="btn btn-sm btn-light font-weight-bold" style="border-radius: 6px;">Voir tout</a>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table phoenix-table table-hover align-middle mb-0">
                        <thead class="text-uppercase text-muted">
                            <tr>
                                <th>Stagiaire</th>
                                <th>Document</th>
                                <th>Action</th>
                                <th class="text-right">Heure</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recent_movements->take(6) as $mov)
                            <tr class="pointer-row" onclick="window.location='{{ $mov->document && $mov->document->trainee ? route('trainees.show', $mov->document->trainee) : '#' }}';">
                                <td>
                                    @if($mov->document && $mov->document->trainee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-soft-primary text-primary font-weight-bold rounded-circle mr-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                {{ substr($mov->document->trainee->first_name, 0, 1) }}{{ substr($mov->document->trainee->last_name, 0, 1) }}
                                            </div>
                                            <span class="font-weight-bold text-dark">{{ ucfirst(strtolower($mov->document->trainee->first_name)) }} {{ strtoupper($mov->document->trainee->last_name) }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td><span class="font-weight-medium">{{ $mov->document->type ?? '-' }}</span></td>
                                <td>
                                    @if($mov->action_type == 'Sortie')
                                        <span class="badge badge-soft-warning"><i class="fas fa-arrow-up mr-1"></i> Sortie</span>
                                    @elseif($mov->action_type == 'Saisie' || $mov->action_type == 'Retour')
                                        <span class="badge badge-soft-success"><i class="fas fa-arrow-down mr-1"></i> {{ $mov->action_type }}</span>
                                    @else
                                        <span class="badge badge-soft-secondary">{{ $mov->action_type }}</span>
                                    @endif
                                </td>
                                <td class="text-right text-muted small">{{ $mov->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center p-4 text-muted">Aucun mouvement récent</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Alertes Bacs --}}
    <div class="col-lg-5 mb-4">
        <div class="card phoenix-card h-100 border-top-danger" style="border-top: 4px solid #dc3545;">
            <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="font-weight-bold text-dark mb-0"><i class="fas fa-bell text-danger mr-2 blink-icon"></i> Alertes Bac</h5>
                    <span class="badge badge-soft-danger">{{ $bac_alerts->count() }} critiques</span>
                </div>
            </div>
            <div class="card-body">
                @if($bac_alerts->count())
                <div class="list-group list-group-flush">
                    @foreach($bac_alerts->take(5) as $doc)
                    @php
                        $isEcoule = $doc->alert_level == 'ecoule';
                        $badgeClass = $isEcoule ? 'badge-soft-danger' : 'badge-soft-warning';
                    @endphp
                    <a href="{{ route('trainees.show', $doc->trainee) }}" class="list-group-item list-group-item-action px-0 border-bottom">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 font-weight-bold text-dark">
                                {{ strtoupper($doc->trainee->last_name) }} {{ ucfirst(strtolower($doc->trainee->first_name)) }}
                            </h6>
                            <span class="badge {{ $badgeClass }}">{{ $doc->time_out_str }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-id-card mr-1"></i> {{ $doc->trainee->cin }}</small>
                            @if($isEcoule)
                                <small class="text-danger font-weight-bold"><i class="fas fa-times-circle"></i> Expiré</small>
                            @else
                                <small class="text-warning font-weight-bold" style="color: #d39e00 !important;"><i class="fas fa-exclamation-triangle"></i> Dépassement</small>
                            @endif
                        </div>
                    </a>
                    @endforeach
                </div>
                @else
                <div class="d-flex flex-column align-items-center justify-content-center h-100 py-4">
                    <div class="bg-soft-success text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-check fa-2x"></i>
                    </div>
                    <h6 class="font-weight-bold text-dark">Tout est en ordre</h6>
                    <p class="text-muted small text-center mb-0">Aucun Baccalauréat n'a dépassé le délai de retrait temporaire autorisé.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

</div>

@stop

@section('css')
<style>
/* Font override for Phoenix Theme */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

body, .content-wrapper, .main-header, .main-sidebar {
    font-family: 'Inter', sans-serif !important;
}

/* Background Override */
.content-wrapper {
    background-color: #f8fafc !important; /* Very light slate gray */
}

/* Phoenix Card Style */
.phoenix-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
    transition: box-shadow 0.3s ease-in-out;
}
.phoenix-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
}

/* Soft Badges */
.badge-soft-primary { background-color: #e0e7ff; color: #4338ca; }
.badge-soft-success { background-color: #dcfce7; color: #15803d; }
.badge-soft-warning { background-color: #fef3c7; color: #b45309; }
.badge-soft-danger { background-color: #fee2e2; color: #b91c1c; }
.badge-soft-info { background-color: #e0f2fe; color: #0369a1; }
.badge-soft-secondary { background-color: #f1f5f9; color: #475569; }
.bg-soft-primary { background-color: #e0e7ff; }
.bg-soft-success { background-color: #dcfce7; }

/* Table Style */
.phoenix-table th {
    border-top: none;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.phoenix-table td {
    vertical-align: middle;
    border-bottom: 1px solid #f1f5f9;
}
.pointer-row { cursor: pointer; }

/* Chart Center Text */
.chart-center-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
    pointer-events: none;
}

.blink-icon { animation: blinker 2s linear infinite; }
@keyframes blinker { 50% { opacity: 0.3; } }
</style>
@stop

@section('js')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    $(document).ready(function() {
        
        // ---- CHART 1 : Ligne (Simulé) ----
        const ctxActivity = document.getElementById('activityChart').getContext('2d');
        
        // Gradient pour la ligne
        let gradientStroke = ctxActivity.createLinearGradient(0, 0, 0, 300);
        gradientStroke.addColorStop(0, 'rgba(56, 189, 248, 0.5)');
        gradientStroke.addColorStop(1, 'rgba(56, 189, 248, 0)');

        new Chart(ctxActivity, {
            type: 'line',
            data: {
                labels: ['J-6', 'J-5', 'J-4', 'J-3', 'J-2', 'Hier', 'Auj.'],
                datasets: [{
                    label: 'Mouvements',
                    data: [12, 19, 15, 25, 22, 30, {{ $stats['mouvements_today'] > 0 ? $stats['mouvements_today'] : 35 }}],
                    borderColor: '#0ea5e9',
                    backgroundColor: gradientStroke,
                    borderWidth: 3,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#0ea5e9',
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
                    x: { grid: { display: false }, ticks: { font: { family: 'Inter' } } },
                    y: { grid: { borderDash: [5, 5], color: '#f1f5f9' }, ticks: { font: { family: 'Inter' } }, beginAtZero: true }
                }
            }
        });

        // ---- CHART 2 : Donut Bacs ----
        const ctxBac = document.getElementById('bacChart').getContext('2d');
        new Chart(ctxBac, {
            type: 'doughnut',
            data: {
                labels: ['En Stock', 'Sortie Temp.', 'Expirés'],
                datasets: [{
                    data: [120, {{ $stats['bac_temp_out'] }}, {{ $stats['bac_expired'] }}],
                    backgroundColor: ['#3b82f6', '#f59e0b', '#ef4444'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
        
        // ---- ALERTES NOUVELLES DEMANDES ----
        let lastCount = 0;
        function checkNewRequests() {
            $.ajax({
                url: '/api/check-new-requests',
                type: 'GET',
                success: function(response) {
                    if (response.has_new && response.count > lastCount) {
                        toastr.options = { "closeButton": true, "timeOut": "60000", "positionClass": "toast-top-right" };
                        toastr.info("Vous avez " + response.count + " nouvelle(s) demande(s) !", "Notification");
                        lastCount = response.count;
                    }
                }
            });
        }
        checkNewRequests();
        setInterval(checkNewRequests, 15000);
    });
</script>
@stop