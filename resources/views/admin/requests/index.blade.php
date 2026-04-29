@extends('layouts.app')

@section('title', 'Boîte de réception - Demandes')

@section('content_header')
    <h1><i class="fas fa-inbox text-primary"></i> Boîte de réception des demandes stagiaires</h1>
@stop

@section('content')
<div class="card card-primary card-outline shadow">
    <div class="card-header">
        <h3 class="card-title">Toutes les demandes reçues</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Date demande</th>
                    <th>Stagiaire</th>
                    <th>Filière</th>
                    <th>Document demandé</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $req)
                    <tr class="{{ $req->status == 'en_attente' ? 'bg-light font-weight-bold' : '' }}">
                        <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                        <td>
                            <div>{{ $req->trainee->first_name }} {{ $req->trainee->last_name }}</div>
                            <small class="text-muted">CIN: {{ $req->trainee->cin }} | CEF: {{ $req->trainee->cef }}</small>
                        </td>
                        <td>{{ $req->trainee->filiere->code_filiere ?? 'N/A' }}</td>
                        <td>
                            @if($req->document_type == 'Bac')
                                <span class="badge bg-warning"><i class="fas fa-graduation-cap"></i> Bac</span>
                            @elseif($req->document_type == 'Diplome')
                                <span class="badge bg-info"><i class="fas fa-scroll"></i> Diplôme</span>
                            @else
                                <span class="badge bg-secondary">{{ $req->document_type }}</span>
                            @endif
                        </td>
                        <td>
                            @if($req->status == 'en_attente')
                                <span class="badge bg-warning text-uppercase">En attente</span>
                            @elseif($req->status == 'planifie')
                                <span class="badge bg-info text-uppercase">Rdv fixé</span><br>
                                <small class="text-success"><i class="far fa-calendar-alt"></i> {{ $req->appointment_date ? $req->appointment_date->format('d/m/Y H:i') : '' }}</small>
                            @elseif($req->status == 'termine')
                                <span class="badge bg-success text-uppercase">Terminé</span>
                            @elseif($req->status == 'rejete')
                                <span class="badge bg-danger text-uppercase">Rejeté</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                @if(in_array($req->status, ['en_attente', 'planifie']))
                                    <!-- Bouton Programmer RDV -->
                                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#modal-schedule-{{ $req->id }}" title="Donner un RDV">
                                        <i class="fas fa-calendar-check"></i>
                                    </button>

                                    <!-- Bouton Terminer (Retiré) -->
                                    <form action="{{ route('admin.requests.complete', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Confirmez-vous que le document a été remis au stagiaire ?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Marquer comme retiré">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    </form>

                                    <!-- Bouton Rejeter -->
                                    <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#modal-reject-{{ $req->id }}" title="Rejeter la demande">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-default" disabled><i class="fas fa-lock"></i> Clôturé</button>
                                @endif
                            </div>
                            
                            <!-- Modal Programmer RDV -->
                            @if(in_array($req->status, ['en_attente', 'planifie']))
                            <div class="modal fade" id="modal-schedule-{{ $req->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('admin.requests.schedule', $req->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content text-start font-weight-normal">
                                            <div class="modal-header bg-primary">
                                                <h5 class="modal-title">Fixer un Rendez-vous de retrait</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Date et Heure du rendez-vous *</label>
                                                    <input type="datetime-local" name="appointment_date" class="form-control" value="{{ $req->appointment_date ? $req->appointment_date->format('Y-m-d\TH:i') : '' }}" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>Message pour le stagiaire (Optionnel mais recommandé)</label>
                                                    <textarea name="admin_message" class="form-control" rows="3" placeholder="Ex: Veuillez vous présenter à l'administration muni de votre CIN originale.">{{ $req->admin_message }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-primary">Envoyer RDV</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <!-- Modal Rejeter -->
                            <div class="modal fade" id="modal-reject-{{ $req->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('admin.requests.reject', $req->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content text-start font-weight-normal">
                                            <div class="modal-header bg-danger">
                                                <h5 class="modal-title">Rejeter la demande</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Motif de refus (sera envoyé au stagiaire) *</label>
                                                    <textarea name="admin_message" class="form-control" rows="3" required placeholder="Ex: Votre baccalauréat a déjà été retiré."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                                                <button type="submit" class="btn btn-danger">Rejeter</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endif

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted"><i class="fas fa-clipboard-check mb-2" style="font-size:2rem;"></i><br>Aucune demande stagiaire dans la boîte.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
