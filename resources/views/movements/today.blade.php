@extends('layouts.app')
@section('title', "Retraits d'aujourd'hui")
@section('content_header')
    <h1><i class="fas fa-calendar-day"></i> Retraits du {{ now()->format('d/m/Y') }}</h1>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <table id="today-table" class="table table-bordered table-hover">
            <thead class="bg-warning">
                <tr>
                    <th>Stagiaire</th>
                    <th>CIN</th>
                    <th>Document</th>
                    <th>Action</th>
                    <th>Par</th>
                    <th>Heure</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $mv)
                <tr>
                    <td>{{ $mv->document->trainee->last_name }} {{ $mv->document->trainee->first_name }}</td>
                    <td>{{ $mv->document->trainee->cin }}</td>
                    <td><span class="badge bg-primary">{{ $mv->document->type }}</span></td>
                    <td>
                        @if($mv->action_type == 'Sortie')
                            <span class="badge bg-warning">Sortie</span>
                        @elseif($mv->action_type == 'Retour')
                            <span class="badge bg-success">Retour</span>
                        @else
                            <span class="badge bg-info">Saisie</span>
                        @endif
                    </td>
                    <td>{{ $mv->user->name }}</td>
                    <td>{{ $mv->date_action->format('H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">Aucun mouvement aujourd'hui</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
@section('js')
<script>$('#today-table').DataTable({"language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"}});</script>
@stop