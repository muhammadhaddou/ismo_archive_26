@extends('layouts.app')
@section('title', 'Calendrier des relances')

@section('content')

<!-- HEADER SECTION -->
<div class="row align-items-center mt-4 mb-4">
    <div class="col-md-6">
        <h2 class="font-weight-bold text-body" style="letter-spacing: -0.5px;">
            <i class="fas fa-calendar-alt text-primary me-2"></i> Calendrier des relances
        </h2>
        <p class="text-muted mb-0">Suivi des échéances pour les retraits temporaires.</p>
    </div>
    <div class="col-md-6 text-end d-none d-md-block">
        <a href="{{ route('documents.bac.temp-out') }}" class="btn btn-warning font-weight-bold shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-clock me-1"></i> Voir tous les retraits temporaires
        </a>
    </div>
    <div class="col-12 mt-3 d-md-none">
        <a href="{{ route('documents.bac.temp-out') }}" class="btn btn-warning w-100 font-weight-bold shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-clock me-1"></i> Voir tous les retraits temporaires
        </a>
    </div>
</div>

{{-- Stats --}}
<div class="row mb-4">
    <div class="col-xl col-md-4 mb-3">
        <div class="card phoenix-card h-100 bg-danger-lt" style="border-top: 4px solid #ef4444;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-danger font-weight-bold text-uppercase" style="font-size: 0.75rem;">Délai dépassé</h6>
                    <span class="badge bg-danger text-white"><i class="fas fa-exclamation-triangle"></i></span>
                </div>
                <h3 class="font-weight-bold text-body mb-1">{{ $stats['expired'] }}</h3>
                <p class="text-muted small mb-0">Retraits en retard</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl col-md-4 mb-3">
        <div class="card phoenix-card h-100 bg-warning-lt" style="border-top: 4px solid #f59e0b;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-warning font-weight-bold text-uppercase" style="font-size: 0.75rem;">Échéance aujourd'hui</h6>
                    <span class="badge bg-warning text-white"><i class="fas fa-bell"></i></span>
                </div>
                <h3 class="font-weight-bold text-body mb-1">{{ $stats['today'] }}</h3>
                <p class="text-muted small mb-0">À retourner aujourd'hui</p>
            </div>
        </div>
    </div>
    
    <div class="col-xl col-md-4 mb-3">
        <div class="card phoenix-card h-100 bg-primary-lt" style="border-top: 4px solid #3b82f6;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <h6 class="text-primary font-weight-bold text-uppercase" style="font-size: 0.75rem;">Total en attente</h6>
                    <span class="badge bg-primary text-white"><i class="fas fa-list"></i></span>
                </div>
                <h3 class="font-weight-bold text-body mb-1">{{ $stats['total'] }}</h3>
                <p class="text-muted small mb-0">Tous les retraits en cours</p>
            </div>
        </div>
    </div>
</div>

{{-- Navigation شهر --}}
<div class="card phoenix-card mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            @php
                $prevMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->subMonth();
                $nextMonth = \Carbon\Carbon::createFromDate($year, $month, 1)->addMonth();
            @endphp
            <a href="{{ route('calendrier', ['month' => $prevMonth->month, 'year' => $prevMonth->year]) }}"
               class="btn btn-light font-weight-bold" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                <i class="fas fa-chevron-left me-1 d-none d-sm-inline"></i> <span class="d-none d-sm-inline">{{ mb_convert_case($prevMonth->translatedFormat('F Y'), MB_CASE_TITLE, 'UTF-8') }}</span><span class="d-sm-none"><i class="fas fa-chevron-left"></i></span>
            </a>
            <h4 class="mb-0 text-primary font-weight-bold" style="font-size: 1.25rem;">
                <i class="far fa-calendar-alt me-2 d-none d-sm-inline"></i>{{ mb_convert_case($startOfMonth->translatedFormat('F Y'), MB_CASE_TITLE, 'UTF-8') }}
            </h4>
            <a href="{{ route('calendrier', ['month' => $nextMonth->month, 'year' => $nextMonth->year]) }}"
               class="btn btn-light font-weight-bold" style="border-radius: 8px; border: 1px solid #e2e8f0;">
                <span class="d-none d-sm-inline">{{ mb_convert_case($nextMonth->translatedFormat('F Y'), MB_CASE_TITLE, 'UTF-8') }}</span> <i class="fas fa-chevron-right ms-1 d-none d-sm-inline"></i><span class="d-sm-none"><i class="fas fa-chevron-right"></i></span>
            </a>
        </div>
    </div>
</div>

{{-- Calendrier --}}
<div class="card phoenix-card mb-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0" id="calendar-table">
                <thead>
                    <tr class="text-center text-uppercase text-muted">
                        <th>Lun</th>
                        <th>Mar</th>
                        <th>Mer</th>
                        <th>Jeu</th>
                        <th>Ven</th>
                        <th style="background-color: #f8fafc;">Sam</th>
                        <th style="background-color: #f8fafc;">Dim</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $firstDay    = $startOfMonth->copy()->startOfWeek(1); // 1 = Lundi
                        $lastDay     = $startOfMonth->copy()->endOfMonth()->endOfWeek(0); // 0 = Dimanche
                        $currentDay  = $firstDay->copy();
                    @endphp

                    @while($currentDay <= $lastDay)
                    <tr>
                        @for($i = 0; $i < 7; $i++)
                        @php
                            $day        = $currentDay->day;
                            $isCurrentMonth = $currentDay->month == $month;
                            $isToday    = $currentDay->isToday();
                            $isWeekend  = $currentDay->isWeekend();
                            $dayEvents  = $events->get($day, collect());
                            $hasExpired = $isCurrentMonth && $dayEvents->where('is_expired', true)->count() > 0;
                            $hasToday   = $isCurrentMonth && $isToday && $dayEvents->count() > 0;
                        @endphp
                        <td style="width:14.28%; min-width: 120px; min-height:120px; vertical-align:top; padding:10px;
                                   {{ !$isCurrentMonth ? 'background:#f8fafc; color:#cbd5e1;' : '' }}
                                   {{ $isWeekend && $isCurrentMonth ? 'background:#fdfdfd;' : '' }}
                                   {{ $isToday ? 'border: 2px solid #3b82f6 !important; background:#f0f9ff;' : '' }}">

                            {{-- رقم اليوم --}}
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="font-weight-bold" style="color:{{ $isToday ? '#3b82f6' : ($isCurrentMonth ? '#475569' : 'inherit') }}; font-size:1rem;">
                                    {{ $isCurrentMonth ? $day : '' }}
                                </span>
                                @if($isToday && $isCurrentMonth)
                                    <span class="badge bg-primary text-white" style="font-size:0.7rem">Auj.</span>
                                @endif
                            </div>

                            {{-- Events --}}
                            @if($isCurrentMonth)
                                @foreach($dayEvents as $event)
                                <a href="{{ $event['doc_url'] }}"
                                   class="d-block mb-2 p-2 rounded text-decoration-none shadow-sm event-card text-white"
                                   style="font-size:0.75rem; line-height:1.4;
                                          background: {{ $event['is_expired'] ? '#ef4444' : ($isToday ? '#f59e0b' : '#3b82f6') }};
                                          border-left: 4px solid {{ $event['is_expired'] ? '#b91c1c' : ($isToday ? '#b45309' : '#1d4ed8') }};
                                          transition: transform 0.2s;">
                                    <strong class="d-block text-truncate">{{ $event['trainee'] }}</strong>
                                    <span class="text-truncate d-block opacity-75">{{ $event['filiere'] }}</span>
                                    @if($event['is_expired'])
                                        <div class="mt-1 font-weight-bold" style="font-size:0.7rem; color: #fee2e2;">
                                            <i class="fas fa-exclamation-circle"></i> Retard: {{ $event['overdue'] }}
                                        </div>
                                    @endif
                                    @if($event['phone'])
                                        <div class="mt-1">
                                            <span style="color:inherit; font-size:0.7rem; opacity:0.9;">
                                                <i class="fas fa-phone-alt me-1"></i> {{ $event['phone'] }}
                                            </span>
                                        </div>
                                    @endif
                                </a>
                                @endforeach
                            @endif
                        </td>
                        @php $currentDay->addDay() @endphp
                        @endfor
                    </tr>
                    @endwhile
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Légende --}}
<div class="card phoenix-card mb-4">
    <div class="card-body py-3">
        <div class="d-flex flex-wrap justify-content-center align-items-center">
            <div class="mx-3 my-1 d-flex align-items-center">
                <span class="rounded d-inline-block me-2 shadow-sm" style="width:16px; height:16px; background:#ef4444; border-left:4px solid #b91c1c;"></span>
                <span class="font-weight-medium text-muted small text-uppercase">Délai dépassé</span>
            </div>
            <div class="mx-3 my-1 d-flex align-items-center">
                <span class="rounded d-inline-block me-2 shadow-sm" style="width:16px; height:16px; background:#f59e0b; border-left:4px solid #b45309;"></span>
                <span class="font-weight-medium text-muted small text-uppercase">Échéance aujourd'hui</span>
            </div>
            <div class="mx-3 my-1 d-flex align-items-center">
                <span class="rounded d-inline-block me-2 shadow-sm" style="width:16px; height:16px; background:#3b82f6; border-left:4px solid #1d4ed8;"></span>
                <span class="font-weight-medium text-muted small text-uppercase">Échéance à venir</span>
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

/* Table Style */
.phoenix-table th {
    border-top: none;
    border-bottom: 1px solid #e2e8f0;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.5px;
}
.phoenix-table td {
    border: 1px solid #e2e8f0;
}

#calendar-table td { height: 120px; }
.event-card:hover { 
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); 
    opacity: 0.9;
}
</style>
@stop