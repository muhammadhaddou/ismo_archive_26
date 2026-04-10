@extends('adminlte::page')

@section('title', 'Tableau de bord | ISMO')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-tachometer-alt mr-2"></i>Tableau de bord</h1>
        <div class="d-flex align-items-center" style="gap:10px">
            <span class="text-muted">{{ now()->format('d/m/Y') }}</span>

            {{-- Bouton ajout rapide --}}
            <div class="dropdown">
                <button class="btn btn-success btn-sm dropdown-toggle" type="button"
                        id="quickAddMenu" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-plus"></i> Ajouter
                </button>
                <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="quickAddMenu">
                    <h6 class="dropdown-header">Stagiaire</h6>
                    <a class="dropdown-item" href="{{ route('trainees.create') }}">
                        <i class="fas fa-user-plus text-primary mr-2"></i> Nouveau stagiaire
                    </a>
                    <div class="dropdown-divider"></div>
                    <h6 class="dropdown-header">Document</h6>
                    <a class="dropdown-item" href="{{ route('documents.create') }}?type=Bac">
                        <i class="fas fa-graduation-cap text-warning mr-2"></i> Retrait Baccalauréat
                    </a>
                    <a class="dropdown-item" href="{{ route('documents.create') }}?type=Diplome">
                        <i class="fas fa-scroll text-info mr-2"></i> Diplôme
                    </a>
                    <a class="dropdown-item" href="{{ route('documents.create') }}?type=Attestation">
                        <i class="fas fa-file-alt text-secondary mr-2"></i> Attestation
                    </a>
                    <a class="dropdown-item" href="{{ route('documents.create') }}?type=Bulletin">
                        <i class="fas fa-chart-bar text-dark mr-2"></i> Bulletin de notes
                    </a>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')

{{-- 🔴 Alerte globale --}}
@if($stats['bac_expired'] > 0)
<div class="alert alert-danger alert-dismissible fade show">
    <button type="button" class="close" data-dismiss="alert">&times;</button>
    <h5><i class="fas fa-exclamation-triangle"></i> Attention</h5>
    <strong>{{ $stats['bac_expired'] }}</strong> stagiaire(s) ont dépassé le délai de 48h
    <a href="{{ url('documents/bac/temp-out') }}" class="btn btn-sm btn-danger ml-2">
        Voir la liste
    </a>
</div>
@endif

{{-- 📊 Statistiques --}}
<div class="row">

    {{-- Total stagiaires --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-info">
            <div class="inner">
                <h3>{{ $stats['total_stagiaires'] }}</h3>
                <p>Total des stagiaires</p>
            </div>
            <div class="icon"><i class="fas fa-users"></i></div>
            <a href="{{ url('trainees') }}" class="small-box-footer">Voir tout</a>
        </div>
    </div>

    {{-- Bac temp --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $stats['bac_temp_out'] }}</h3>
                <p>Bac — Temporaire</p>
            </div>
            <div class="icon"><i class="fas fa-graduation-cap"></i></div>
            <a href="{{ url('documents/bac/temp-out') }}" class="small-box-footer">Voir</a>
        </div>
    </div>

    {{-- Diplômes prêts --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $stats['diplomes_prets'] }}</h3>
                <p>Diplômes prêts</p>
            </div>
            <div class="icon"><i class="fas fa-certificate"></i></div>
            <a href="{{ url('documents/diplome') }}" class="small-box-footer">Voir</a>
        </div>
    </div>

    {{-- Mouvements --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $stats['mouvements_today'] }}</h3>
                <p>Mouvements aujourd'hui</p>
            </div>
            <div class="icon"><i class="fas fa-exchange-alt"></i></div>
            <a href="{{ url('movements/today') }}" class="small-box-footer">Voir</a>
        </div>
    </div>

    {{-- 🔥 Diplômes en attente (NEW) --}}
    <div class="col-lg-3 col-6">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3>{{ $stats['diplomes_en_attente'] }}</h3>
                <p>Diplômés — En attente</p>
            </div>
            <div class="icon"><i class="fas fa-user-clock"></i></div>
            <a href="{{ route('diplomes.prets') }}" class="small-box-footer">
                Voir tout
            </a>
        </div>
    </div>

</div>

<div class="row">

    {{-- Colonne 1: ⚠️ Alertes détaillées --}}
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card card-outline card-danger shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-2">
                <h3 class="card-title text-danger font-weight-bold" style="font-size: 1.1rem;">
                    <i class="fas fa-bell blink-icon mr-2"></i> Alertes Bac (≥ 40h)
                </h3>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                @if($bac_alerts->count())
                <ul class="list-group list-group-flush">
                    @foreach($bac_alerts as $doc)
                    @php
                        $isEcoule = $doc->alert_level == 'ecoule';
                        $badgeClass = $isEcoule ? 'badge-danger' : 'badge-warning text-dark';
                        $icon = $isEcoule ? 'fa-exclamation-circle' : 'fa-clock';
                        $bgColor = $isEcoule ? 'bg-light-danger' : 'bg-light-warning';
                    @endphp
                    <li class="list-group-item custom-alert-item {{ $bgColor }} p-2 position-relative">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 font-weight-bold">
                                <a href="{{ route('trainees.show', $doc->trainee) }}" class="text-dark stretched-link">
                                    {{ strtoupper($doc->trainee->last_name) }} {{ ucfirst(strtolower($doc->trainee->first_name)) }}
                                </a>
                            </h6>
                            <span class="badge {{ $badgeClass }} px-2 py-1 shadow-sm font-weight-bold" style="border-radius: 4px; z-index: 2;">
                                <i class="fas fa-stopwatch"></i> {{ $doc->time_out_str }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-secondary font-weight-medium">
                                <i class="fas fa-id-card"></i> {{ $doc->trainee->cin }} | 
                                {{ $doc->trainee->filiere->code_filiere ?? 'N/A' }} 
                            </small>
                            @if($isEcoule)
                                <small class="text-danger font-weight-bold" style="z-index: 2; position: relative;"><i class="fas fa-times-circle"></i> Expiré</small>
                            @else
                                <small class="text-warning font-weight-bold" style="color: #d39e00 !important; z-index: 2; position: relative;"><i class="fas fa-fire"></i> Critique</small>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                @else
                <div class="p-4 text-center text-success font-weight-bold">
                    <i class="fas fa-check-circle fa-2x mb-2"></i><br>Aucune alerte en cours
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Colonne 2: Bac non retourné --}}
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card card-warning h-100 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header border-bottom-0 pt-3 pb-2">
                <h3 class="card-title font-weight-bold" style="font-size: 1.1rem;">Bac non retourné</h3>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nom</th>
                            <th>CIN</th>
                            <th>Filière</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bac_alerts as $doc)
                        <tr class="pointer-row" onclick="window.location='{{ route('trainees.show', $doc->trainee) }}';">
                            <td>
                                <span class="text-dark font-weight-bold">
                                    {{ ucfirst(strtolower($doc->trainee->first_name)) }} {{ strtoupper($doc->trainee->last_name) }}
                                </span>
                            </td>
                            <td>{{ $doc->trainee->cin }}</td>
                            <td>{{ $doc->trainee->filiere->code_filiere ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center p-3 text-muted">Aucune alerte</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Colonne 3: Derniers mouvements --}}
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card card-primary h-100 shadow-sm" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header border-bottom-0 pt-3 pb-2">
                <h3 class="card-title font-weight-bold" style="font-size: 1.1rem;">Derniers mouvements</h3>
            </div>
            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                <table class="table table-sm table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Nom</th>
                            <th>Doc</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_movements as $mov)
                        @if($mov->document && $mov->document->trainee)
                            <tr class="pointer-row" onclick="window.location='{{ route('trainees.show', $mov->document->trainee) }}';">
                        @else
                            <tr>
                        @endif
                            <td>
                                @if($mov->document && $mov->document->trainee)
                                    <span class="text-dark font-weight-bold">
                                        {{ ucfirst(strtolower($mov->document->trainee->first_name)) }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-secondary">{{ $mov->document->type ?? '-' }}</span>
                            </td>
                            <td>
                                @if($mov->action_type == 'Sortie')
                                    <span class="text-danger"><i class="fas fa-arrow-up"></i> {{ $mov->action_type }}</span>
                                @elseif($mov->action_type == 'Saisie' || $mov->action_type == 'Retour')
                                    <span class="text-success"><i class="fas fa-arrow-down"></i> {{ $mov->action_type }}</span>
                                @else
                                    {{ $mov->action_type }}
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center p-3 text-muted">Aucun mouvement</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@stop

@section('css')
<style>
.small-box { position: relative; }
.small-box .icon {
    position: absolute;
    right: 10px;
    top: 10px;
    font-size: 60px;
    opacity: 0.2;
}

/* Custom modern alerts CSS */
.bg-light-danger { background-color: #fff5f5 !important; border-left: 5px solid #dc3545 !important; }
.bg-light-warning { background-color: #fffdf5 !important; border-left: 5px solid #ffc107 !important; }
.custom-alert-item { 
    transition: all 0.2s ease-in-out; 
    margin: 8px 15px; 
    border-radius: 8px !important; 
    border: 1px solid rgba(0,0,0,0.05); 
}
.custom-alert-item:hover { 
    transform: translateY(-2px); 
    box-shadow: 0 4px 15px rgba(0,0,0,0.06); 
    background-color: #f8f9fa !important;
    z-index: 1;
}
.pointer-row { cursor: pointer; transition: background-color 0.2s; }
.pointer-row:hover { background-color: #f8f9fa; }
.blink-icon { animation: blinker 2s linear infinite; }
@keyframes blinker { 50% { opacity: 0.3; } }
</style>
@stop