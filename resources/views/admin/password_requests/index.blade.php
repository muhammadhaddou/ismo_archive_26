@extends('layouts.app')

@section('title', 'Demandes de changement de mot de passe')

@section('content_header')
    <h1><i class="fas fa-key text-primary"></i> Demandes de réinitialisation de mot de passe</h1>
@stop

@section('content')
<div class="card card-primary card-outline card-outline-tabs shadow">
    <div class="card-header p-0 border-bottom-0">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link text-muted" href="{{ route('admin.requests.index') }}"><i class="fas fa-file-alt"></i> Demandes de documents</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active font-weight-bold" href="{{ route('admin.password_requests.index') }}"><i class="fas fa-key"></i> Changements de mot de passe</a>
            </li>
        </ul>
    </div>
    <div class="card-header d-flex justify-content-between align-items-center mt-2 border-top-0 pt-0">
        <h3 class="card-title m-0"></h3>
        
        <!-- Search Form -->
        <form action="{{ route('admin.password_requests.index') }}" method="GET" class="form-inline ml-auto">
            <div class="input-group input-group-sm" style="width: 250px;">
                <input type="text" name="search" class="form-control float-right" placeholder="Rechercher stagiaire (Nom, CEF, CIN)" value="{{ request('search') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-default">
                        <i class="fas fa-search"></i>
                    </button>
                    @if(request('search'))
                        <a href="{{ route('admin.password_requests.index') }}" class="btn btn-danger" title="Effacer la recherche">
                            <i class="fas fa-times"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
    
    <div class="card-body table-responsive p-0">
        @if(session('success'))
            <div class="alert alert-success m-3">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger m-3">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Date demande</th>
                    <th>Stagiaire</th>
                    <th>Filière</th>
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
                            @if($req->document_type == 'Configuration Mot de passe')
                                <span class="badge bg-success text-uppercase">Nouveau MDP Configuré</span><br>
                                <small class="font-weight-bold {{ $req->status == 'en_attente' ? 'text-danger' : 'text-muted' }}">Mot de passe : {{ $req->admin_message }}</small>
                                @if($req->status != 'en_attente')
                                    <br><small class="text-muted"><i class="fas fa-check"></i> Lu</small>
                                @endif
                            @else
                                @if($req->status == 'en_attente')
                                    <span class="badge bg-warning text-uppercase">En attente (Oubli)</span>
                                @elseif($req->status == 'termine')
                                    <span class="badge bg-success text-uppercase">Réinitialisé</span>
                                @elseif($req->status == 'rejete')
                                    <span class="badge bg-danger text-uppercase">Rejeté</span><br>
                                    <small class="text-muted" title="{{ $req->admin_message }}">Motif: {{ Str::limit($req->admin_message, 20) }}</small>
                                @endif
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                @if($req->document_type == 'Configuration Mot de passe' && $req->status == 'en_attente')
                                    <form action="{{ route('admin.password_requests.approve', $req->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-info" title="Marquer comme lu">
                                            <i class="fas fa-eye"></i> Marquer Lu
                                        </button>
                                    </form>
                                @elseif($req->document_type == 'Changement Mot de passe' && $req->status == 'en_attente')
                                    <!-- Bouton Approuver -->
                                    <form action="{{ route('admin.password_requests.approve', $req->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer le mot de passe de ce stagiaire ? Il devra se connecter avec son CIN et en créer un nouveau.');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" title="Approuver et réinitialiser">
                                            <i class="fas fa-check"></i> Réinitialiser
                                        </button>
                                    </form>

                                    <!-- Bouton Rejeter -->
                                    <button type="button" class="btn btn-sm btn-danger ml-1" data-toggle="modal" data-target="#modal-reject-{{ $req->id }}" title="Rejeter la demande">
                                        <i class="fas fa-times"></i> Rejeter
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-default" disabled><i class="fas fa-lock"></i> Clôturé</button>
                                @endif
                            </div>
                            
                            <!-- Modal Rejeter -->
                            @if($req->document_type == 'Changement Mot de passe' && $req->status == 'en_attente')
                            <div class="modal fade" id="modal-reject-{{ $req->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <form action="{{ route('admin.password_requests.reject', $req->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-content text-start font-weight-normal">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title">Rejeter la demande</h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="form-group">
                                                    <label>Motif de refus *</label>
                                                    <textarea name="admin_message" class="form-control" rows="3" required placeholder="Ex: Veuillez vous présenter à l'administration avec votre CIN."></textarea>
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
                        <td colspan="5" class="text-center py-4 text-muted">
                            <i class="fas fa-shield-alt mb-2" style="font-size:2rem;"></i><br>
                            @if(request('search'))
                                Aucun résultat pour "{{ request('search') }}".
                            @else
                                Aucune demande de changement de mot de passe en cours.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@stop
