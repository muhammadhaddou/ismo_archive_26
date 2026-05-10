@extends('layouts.app')
@section('title', 'Retraits Bac Définitifs')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <div class="text-muted small text-uppercase fw-bold mb-1" style="letter-spacing:1px;">Stagiaires</div>
        <h2 class="fw-bold mb-0" style="font-size:1.6rem;">
            <i class="ti ti-file-x me-2 text-danger"></i>Retraits Bac Définitifs
        </h2>
    </div>
    <span class="badge bg-danger-lt text-danger fw-bold px-3 py-2" style="font-size:.95rem;">
        <i class="ti ti-lock me-1"></i>{{ $documents->total() }} retrait(s)
    </span>
</div>
@stop

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible shadow-sm mb-3">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    <i class="ti ti-circle-check me-2"></i>{{ session('success') }}
</div>
@endif

{{-- Filtres --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('trainees.bac.final-out') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="CIN, Nom, Prénom..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filière</label>
                    <select name="filiere_id" class="form-control">
                        <option value="">— Toutes —</option>
                        @foreach($filieres as $f)
                            <option value="{{ $f->id }}" {{ request('filiere_id') == $f->id ? 'selected' : '' }}>{{ $f->nom_filiere }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Groupe</label>
                    <select name="group" class="form-control">
                        <option value="">— Tous —</option>
                        @foreach($groups as $g)
                            <option value="{{ $g }}" {{ request('group') == $g ? 'selected' : '' }}>{{ $g }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Promotion</label>
                    <select name="graduation_year" class="form-control">
                        <option value="">— Toutes —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-search me-1"></i>Filtrer</button>
                    <a href="{{ route('trainees.bac.final-out') }}" class="btn btn-outline-secondary" title="Réinitialiser"><i class="ti ti-x"></i></a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-vcenter table-hover mb-0">
                <thead>
                    <tr style="border-bottom:2px solid var(--tblr-border-color);">
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;width:40px;">#</th>
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Stagiaire</th>
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">CIN</th>
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Filière / Groupe</th>
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Promotion</th>
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Date retrait</th>
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Signataire</th>
                        <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Cause / Observations</th>
                        <th class="text-muted fw-bold text-uppercase text-end" style="font-size:.7rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    @php
                        // Récupérer le dernier mouvement de type "Sortie" (le retrait définitif)
                        $sortie = $doc->movements
                            ->where('action_type', 'Sortie')
                            ->sort(fn($a,$b) => $b->date_action <=> $a->date_action)
                            ->first();
                        $t = $doc->trainee;
                    @endphp
                    <tr>
                        <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>

                        {{-- Stagiaire --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @if($t->image_profile)
                                    <img src="{{ asset('storage/'.$t->image_profile) }}"
                                         class="rounded-circle"
                                         style="width:32px;height:32px;object-fit:cover;"
                                         alt="">
                                @else
                                    <span class="avatar avatar-sm rounded-circle bg-danger-lt text-danger fw-bold"
                                          style="width:32px;height:32px;font-size:.7rem;display:inline-flex;align-items:center;justify-content:center;">
                                        {{ substr($t->first_name,0,1) }}{{ substr($t->last_name,0,1) }}
                                    </span>
                                @endif
                                <div>
                                    <a href="{{ route('trainees.show', $t) }}"
                                       class="fw-bold text-body text-decoration-none" style="font-size:.875rem;">
                                        {{ strtoupper($t->last_name) }} {{ ucfirst(strtolower($t->first_name)) }}
                                    </a>
                                    @if($t->statut === 'abandon')
                                        <div><span class="badge bg-danger-lt text-danger" style="font-size:.65rem;">Abandon</span></div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- CIN --}}
                        <td class="font-monospace text-muted" style="font-size:.825rem;">{{ $t->cin }}</td>

                        {{-- Filière / Groupe --}}
                        <td style="font-size:.825rem;">
                            <div class="text-body">{{ $t->filiere->nom_filiere ?? '—' }}</div>
                            <span class="badge bg-blue-lt text-blue fw-bold">{{ $t->group }}</span>
                        </td>

                        {{-- Promotion --}}
                        <td class="text-muted" style="font-size:.825rem;">{{ $t->graduation_year ?? '—' }}</td>

                        {{-- Date retrait --}}
                        <td style="font-size:.825rem;">
                            @if($sortie)
                                <div class="fw-semibold text-danger">
                                    {{ $sortie->date_action->format('d/m/Y') }}
                                </div>
                                <div class="text-muted" style="font-size:.75rem;">
                                    {{ $sortie->date_action->format('H:i') }}
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Signataire --}}
                        <td style="font-size:.825rem;">
                            @if($sortie && $sortie->user)
                                <div class="d-flex align-items-center gap-2">
                                    <span class="avatar avatar-xs rounded-circle bg-primary text-white fw-bold"
                                          style="font-size:.65rem;width:24px;height:24px;display:inline-flex;align-items:center;justify-content:center;">
                                        {{ substr($sortie->user->name, 0, 1) }}
                                    </span>
                                    <div>
                                        <div class="text-body fw-semibold">{{ $sortie->user->name }}</div>
                                        @if($sortie->is_proxy)
                                            <div class="text-warning" style="font-size:.72rem;">
                                                <i class="ti ti-user-shield me-1"></i>
                                                Procuration : {{ $sortie->proxy_name }}
                                                @if($sortie->proxy_cin)
                                                    ({{ $sortie->proxy_cin }})
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>

                        {{-- Cause / Observations --}}
                        <td style="font-size:.825rem;max-width:200px;">
                            @if($sortie && $sortie->observations)
                                <span class="text-body">{{ $sortie->observations }}</span>
                            @else
                                <span class="text-muted fst-italic">Non renseignée</span>
                            @endif
                            @if($sortie && $sortie->proxy_document_path)
                                <div class="mt-1">
                                    <a href="{{ route('scans.show', ['path' => $sortie->proxy_document_path]) }}"
                                       target="_blank" class="text-warning" style="font-size:.75rem;">
                                        <i class="ti ti-paperclip me-1"></i>Procuration jointe
                                    </a>
                                </div>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="text-end">
                            <div class="d-flex justify-content-end gap-1">
                                <a href="{{ route('trainees.show', $t) }}"
                                   class="btn btn-sm btn-outline-primary btn-icon" title="Voir stagiaire">
                                    <i class="ti ti-user"></i>
                                </a>
                                <a href="{{ route('documents.show', $doc) }}"
                                   class="btn btn-sm btn-outline-secondary btn-icon" title="Voir document">
                                    <i class="ti ti-file-description"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5">
                            <div class="empty">
                                <div class="empty-icon text-success">
                                    <i class="ti ti-circle-check" style="font-size:3rem;"></i>
                                </div>
                                <p class="empty-title mt-3 fw-bold">Aucun retrait définitif de Bac enregistré</p>
                                <p class="empty-subtitle text-muted">
                                    Tous les Bacs sont en stock ou en retrait temporaire.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
        <div class="card-footer border-0 d-flex justify-content-end">
            {{ $documents->links() }}
        </div>
        @endif
    </div>
</div>

@stop
