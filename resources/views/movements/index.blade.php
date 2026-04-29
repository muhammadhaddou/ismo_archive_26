@extends('layouts.app')
@section('title', 'Historique des mouvements')
@section('content_header')
    <h1><i class="fas fa-exchange-alt"></i> Historique des mouvements</h1>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <table id="mv-table" class="table table-bordered table-hover">
            <thead class="bg-primary">
                <tr>
                    <th>Stagiaire</th>
                    <th>Document</th>
                    <th>Action</th>
                    <th>Par</th>
                    <th>Date</th>
                    <th>Observations</th>
                </tr>
            </thead>
            <tbody>
                @foreach($movements as $mv)
                <tr>
                    <td>{{ $mv->document->trainee->last_name }} {{ $mv->document->trainee->first_name }}</td>
                    <td><span class="badge bg-primary">{{ $mv->document->type }}</span></td>
                    <td>
                        @if($mv->action_type == 'Saisie')
                            <span class="badge bg-info">Saisie</span>
                        @elseif($mv->action_type == 'Sortie')
                            <span class="badge bg-warning">Sortie</span>
                        @else
                            <span class="badge bg-success">Retour</span>
                        @endif
                    </td>
                    <td>{{ $mv->user->name }}</td>
                    <td>{{ $mv->date_action->format('d/m/Y H:i') }}</td>
                    <td>{{ $mv->observations ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $movements->links() }}
    </div>
</div>
@stop
@section('js')
<script>
    $('#mv-table').DataTable({
        "language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"},
        "paging": false,
        "order": [[4, "desc"]]
    });
</script>
@stop