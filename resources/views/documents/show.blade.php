@extends('layouts.app')
@section('title', 'Document')
@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1><i class="fas fa-file"></i> Document — {{ $document->type }}</h1>
        <a href="{{ route('documents.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
    </div>
@stop
@section('content')
<div class="row">
    <div class="col-md-5">
        <div class="card card-primary card-outline">
            <div class="card-header"><h3 class="card-title">Informations</h3></div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>Stagiaire</th><td>{{ $document->trainee->last_name }} {{ $document->trainee->first_name }}</td></tr>
                    <tr><th>CIN</th><td>{{ $document->trainee->cin }}</td></tr>
                    <tr><th>Type</th><td>{{ $document->type }}</td></tr>
                    <tr><th>Référence</th><td>{{ $document->reference_number ?? '—' }}</td></tr>
                    <tr>
                        <th>Statut</th>
                        <td>
                            @if($document->status == 'Stock')
                                <span class="badge bg-success">En stock</span>
                            @elseif($document->status == 'Temp_Out')
                                <span class="badge bg-warning">Retrait temporaire</span>
                            @elseif($document->status == 'Final_Out')
                                <span class="badge bg-danger">Retrait définitif</span>
                            @else
                                <span class="badge bg-info">Remis</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            <div class="card-footer">
                @if($document->status == 'Stock')
                <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#sortieModal">
                    <i class="fas fa-sign-out-alt"></i> Retrait
                </button>
                @elseif($document->status == 'Temp_Out')
                <form action="{{ route('documents.retour', $document) }}" method="POST" style="display:inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        <i class="fas fa-undo"></i> Retour
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header bg-info">
                <h3 class="card-title text-white"><i class="fas fa-history"></i> Historique</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead>
                        <tr><th>Action</th><th>Par</th><th>Date</th><th>Observations</th></tr>
                    </thead>
                    <tbody>
                        @forelse($document->movements as $mv)
                        <tr>
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
                            <td>
                                {{ $mv->observations ?? '—' }}
                                @if($mv->is_proxy)
                                    <br><small class="text-primary">
                                        <i class="fas fa-user-shield"></i> Par procuration ({{ $mv->proxy_name }} - {{ $mv->proxy_cin }})
                                        @if($mv->proxy_document_path)
                                            <a href="{{ route('scans.show', ['path' => $mv->proxy_document_path]) }}" target="_blank" class="ms-1"><i class="fas fa-download"></i> Fichier joint</a>
                                        @endif
                                    </small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Aucun mouvement</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Sortie -->
<div class="modal fade" id="sortieModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('documents.sortie', $document) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header bg-warning">
                    <h5 class="modal-title">Retrait du document</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Type de retrait</label>
                        <select name="action_type" class="form-control" required>
                            <option value="Temp_Out">Retrait temporaire</option>
                            <option value="Final_Out">Retrait définitif</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Observations</label>
                        <textarea name="observations" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="isProxyCheck" name="is_proxy" value="1">
                            <label class="custom-control-label" for="isProxyCheck">Saisi par procuration (ولي الأمر / بوكالة)</label>
                        </div>
                    </div>

                    <div id="proxyFields" style="display: none; background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 3px solid #ffc107; margin-bottom: 1rem;">
                        <div class="form-group">
                            <label>Nom du mandataire (اسم المستلم)</label>
                            <input type="text" name="proxy_name" class="form-control" placeholder="Nom complet">
                        </div>
                        <div class="form-group">
                            <label>CIN du mandataire (رقم البطاقة الوطنية)</label>
                            <input type="text" name="proxy_cin" class="form-control" placeholder="Ex: AB123456">
                        </div>
                        <div class="form-group mb-0">
                            <label>Importer la procuration depuis l'appareil (صورة الوكالة)</label>
                            <input type="file" name="proxy_document" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-warning">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop

@section('js')
<script>
    document.getElementById('isProxyCheck').addEventListener('change', function() {
        document.getElementById('proxyFields').style.display = this.checked ? 'block' : 'none';
        
        // Toggle required attributes dynamically
        const inputs = document.querySelectorAll('#proxyFields input');
        inputs.forEach(input => {
            if (this.checked) {
                input.setAttribute('required', 'required');
            } else {
                input.removeAttribute('required');
                if (input.type !== 'checkbox' && input.type !== 'radio') {
                    input.value = ''; // clear values on hide
                }
            }
        });
    });
</script>
@stop