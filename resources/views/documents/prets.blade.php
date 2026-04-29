@extends('layouts.app')
@section('title', 'Diplômes prêts')
@section('content_header')
    <h1><i class="fas fa-check-circle"></i> Diplômes prêts à remettre</h1>
@stop
@section('content')
<div class="card">
    <div class="card-body">
        <table id="prets-table" class="table table-bordered table-hover">
            <thead class="bg-success">
                <tr>
                    <th>Stagiaire</th>
                    <th>CIN</th>
                    <th>Filière</th>
                    <th>Référence</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                <tr>
                    <td>{{ $doc->trainee->last_name }} {{ $doc->trainee->first_name }}</td>
                    <td>{{ $doc->trainee->cin }}</td>
                    <td>{{ $doc->trainee->filiere->nom_filiere }}</td>
                    <td>{{ $doc->reference_number ?? '—' }}</td>
                    <td>
                        <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form action="{{ route('documents.sortie', $doc) }}" method="POST" style="display:inline">
                            @csrf
                            <input type="hidden" name="action_type" value="Final_Out">
                            <button type="submit" class="btn btn-sm btn-warning"
                                onclick="return confirm('Confirmer la remise définitive?')">
                                <i class="fas fa-hand-holding"></i> Remettre
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">
                        Aucun diplôme prêt pour le moment
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        {{ $documents->links() }}
    </div>
</div>
@stop
@section('js')
<script>
    $('#prets-table').DataTable({
        "language": {"url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"},
        "paging": false
    });
</script>
@stop