@extends('layouts.app')
@section('title', 'Retraits temporaires — Bac')

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <div>
        <div class="text-muted small text-uppercase fw-bold mb-1" style="letter-spacing:1px;">Baccalauréat</div>
        <h2 class="fw-bold mb-0" style="font-size:1.6rem;">
            <i class="ti ti-clock me-2 text-warning"></i>Retraits temporaires
        </h2>
    </div>
    <span class="badge bg-warning-lt text-warning fw-bold px-3 py-2" style="font-size:.95rem;">
        <i class="ti ti-hourglass me-1"></i>{{ $documents->total() }} en cours
    </span>
</div>
@stop

@section('content')

{{-- Stats rapides --}}
@php
    $alerte = 0; $ok = 0;
    foreach($documents as $doc) {
        $ls = $doc->movements->where('action_type','Sortie')->sort(fn($a,$b) => $b->date_action <=> $a->date_action)->first();
        $dl = $ls?->deadline ? \Carbon\Carbon::parse($ls->deadline) : null;
        if ($dl && now()->diffInHours($dl, false) <= 8 && now()->diffInHours($dl, false) >= 0) {
            $alerte++;
        } else { $ok++; }
    }
@endphp

<div class="row row-deck mb-4">
    <div class="col-sm-4">
        <div class="card shadow-sm border-0" style="border-left: 4px solid #d63939 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="avatar rounded-3 bg-danger-lt" style="width:48px;height:48px;">
                    <i class="ti ti-flame text-danger" style="font-size:1.4rem;"></i>
                </span>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size:.7rem;letter-spacing:.5px;">Alerte Rouge (≤8h)</div>
                    <div class="h2 fw-bold mb-0 text-danger">{{ $alerte }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card shadow-sm border-0" style="border-left: 4px solid #f59f00 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="avatar rounded-3 bg-warning-lt" style="width:48px;height:48px;">
                    <i class="ti ti-clock text-warning" style="font-size:1.4rem;"></i>
                </span>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size:.7rem;letter-spacing:.5px;">Dans les délais</div>
                    <div class="h2 fw-bold mb-0 text-warning">{{ $ok }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="card shadow-sm border-0" style="border-left: 4px solid #206bc4 !important;">
            <div class="card-body d-flex align-items-center gap-3">
                <span class="avatar rounded-3 bg-primary-lt" style="width:48px;height:48px;">
                    <i class="ti ti-list text-primary" style="font-size:1.4rem;"></i>
                </span>
                <div>
                    <div class="text-muted small fw-bold text-uppercase" style="font-size:.7rem;letter-spacing:.5px;">Total en cours</div>
                    <div class="h2 fw-bold mb-0 text-primary">{{ $documents->total() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filtres --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('documents.bac.temp-out') }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Nom / Prénom</label>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">CIN / CEF</label>
                    <input type="text" name="cin" class="form-control" placeholder="CIN ou CEF..." value="{{ request('cin') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Filière</label>
                    <select name="filiere_id" class="form-control select2">
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
                    <label class="form-label small fw-bold text-muted">Année promo</label>
                    <select name="graduation_year" class="form-control">
                        <option value="">— Toutes —</option>
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ request('graduation_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill"><i class="ti ti-search"></i> Filtrer</button>
                    <a href="{{ route('documents.bac.temp-out') }}" class="btn btn-outline-secondary" title="Reset"><i class="ti ti-x"></i></a>
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
                <tr style="border-bottom: 2px solid var(--tblr-border-color);">
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;width:40px;">#</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Stagiaire</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">CIN</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Téléphone</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Filière / Gr.</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Date retrait</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Deadline 48h</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Temps restant</th>
                    <th class="text-muted fw-bold text-uppercase" style="font-size:.7rem;">Scan / Signature</th>
                    <th class="text-muted fw-bold text-uppercase text-end" style="font-size:.7rem;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                @php
                    $lastSortie = $doc->movements
                        ->where('action_type', 'Sortie')
                        ->sort(fn($a,$b) => $b->date_action <=> $a->date_action)
                        ->first();
                    $deadline  = $lastSortie?->deadline ? \Carbon\Carbon::parse($lastSortie->deadline) : null;
                    $hoursLeft = $deadline ? now()->diffInHours($deadline, false) : null;
                    $isAlerte  = $hoursLeft !== null && $hoursLeft <= 8 && $hoursLeft >= 0;
                    $isExpired = $hoursLeft !== null && $hoursLeft < 0;
                @endphp
                <tr style="{{ $isAlerte ? 'background:rgba(214,57,57,.06);' : ($isExpired ? 'background:rgba(214,57,57,.04);' : '') }}">
                    <td class="text-muted" style="font-size:.8rem;">{{ $loop->iteration }}</td>

                    <td>
                        <a href="{{ route('trainees.show', $doc->trainee) }}" class="fw-bold text-body text-decoration-none" style="font-size:.875rem;">
                            {{ strtoupper($doc->trainee->last_name) }} {{ ucfirst(strtolower($doc->trainee->first_name)) }}
                        </a>
                    </td>

                    <td class="font-monospace text-muted" style="font-size:.825rem;">{{ $doc->trainee->cin }}</td>

                    <td style="font-size:.825rem;">
                        @if($doc->trainee->phone)
                            <a href="tel:{{ $doc->trainee->phone }}" class="text-body">{{ $doc->trainee->phone }}</a>
                        @else <span class="text-muted">—</span> @endif
                    </td>

                    <td style="font-size:.825rem;">
                        <div class="text-body">{{ $doc->trainee->filiere->nom_filiere }}</div>
                        <span class="badge bg-blue-lt text-blue fw-bold">{{ $doc->trainee->group }}</span>
                    </td>

                    <td class="text-muted" style="font-size:.825rem;">
                        {{ $lastSortie ? \Carbon\Carbon::parse($lastSortie->date_action)->format('d/m/Y H:i') : '—' }}
                    </td>

                    <td class="text-muted" style="font-size:.825rem;">
                        {{ $deadline ? $deadline->format('d/m/Y H:i') : '—' }}
                    </td>

                    <td>
                        @if($isAlerte)
                            <span class="badge bg-danger fw-bold">
                                <i class="ti ti-alert-triangle me-1"></i>{{ round($hoursLeft) }}h restantes
                            </span>
                        @elseif($isExpired)
                            <span class="badge bg-danger-lt text-danger fw-bold">
                                <i class="ti ti-clock-x me-1"></i>Expiré
                            </span>
                        @elseif($hoursLeft !== null && $hoursLeft <= 24)
                            <span class="badge bg-warning-lt text-warning fw-bold">
                                <i class="ti ti-clock me-1"></i>{{ round($hoursLeft) }}h restantes
                            </span>
                        @elseif($hoursLeft !== null)
                            <span class="badge bg-success-lt text-success fw-bold">
                                <i class="ti ti-circle-check me-1"></i>{{ round($hoursLeft) }}h restantes
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>

                    <td class="text-center">
                        <button type="button"
                                class="btn btn-sm btn-outline-warning btn-scan-temp"
                                data-cin="{{ $doc->trainee->cin }}"
                                data-id="{{ $doc->id }}"
                                title="Scanner CIN / QR Code">
                            <i class="ti ti-qrcode me-1"></i> Scanner
                        </button>
                        <div id="scan-result-{{ $doc->id }}" class="mt-1" style="display:none">
                            <span class="badge scan-badge-{{ $doc->id }} bg-success">
                                <i class="ti ti-check"></i>
                                <span class="scan-text-{{ $doc->id }}"></span>
                            </span>
                        </div>
                    </td>

                    <td class="text-end">
                        <div class="d-flex justify-content-end gap-1">
                            <a href="{{ route('documents.show', $doc) }}" class="btn btn-sm btn-outline-primary btn-icon" title="Voir">
                                <i class="ti ti-eye"></i>
                            </a>
                            <form action="{{ route('documents.retour', $doc) }}" method="POST" style="display:inline">
                                @csrf
                                <button type="button" class="btn btn-sm btn-outline-success btn-confirm-retour" title="Retour en stock">
                                    <i class="ti ti-arrow-back-up me-1"></i>Retour
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="text-center py-5">
                        <div class="empty">
                            <div class="empty-icon text-success"><i class="ti ti-circle-check" style="font-size:3rem;"></i></div>
                            <p class="empty-title mt-3 fw-bold">Aucun retrait temporaire en cours</p>
                            <p class="empty-subtitle text-muted">Tous les Bacs sont en stock ou ont été remis.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        </div>
        @if($documents->hasPages())
        <div class="card-footer border-0 d-flex justify-content-end">{{ $documents->links() }}</div>
        @endif
    </div>
</div>

{{-- ✅ MODAL SCANNER CIN / QR --}}
<div class="modal fade" id="modal-scanner-temp" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-qrcode"></i>
                    Scanner CIN / QR Code
                    <small class="ms-2 text-dark font-weight-normal" id="modal-scan-cin-label"></small>
                </h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body p-2">

                {{-- Vidéo --}}
                <div style="position:relative; background:#000; border-radius:8px; overflow:hidden; min-height:200px">
                    <video id="modal-scanner-video"
                           style="width:100%; display:block"
                           autoplay playsinline></video>

                    {{-- Ligne scan animée --}}
                    <div style="
                        position:absolute; top:0; left:0; right:0;
                        height:3px;
                        background: linear-gradient(to right, transparent, #f39c12, transparent);
                        animation: scanLine 2s linear infinite;
                    "></div>

                    {{-- Cadre de visée --}}
                    <div style="
                        position:absolute; top:50%; left:50%;
                        transform:translate(-50%,-55%);
                        width:65%; height:55%;
                        border:2px solid #f39c12;
                        border-radius:8px;
                        pointer-events:none;
                    "></div>

                    {{-- Coins décoratifs --}}
                    <div style="position:absolute;top:22%;left:18%;width:18px;height:18px;border-top:3px solid #f39c12;border-left:3px solid #f39c12;border-radius:2px 0 0 0"></div>
                    <div style="position:absolute;top:22%;right:18%;width:18px;height:18px;border-top:3px solid #f39c12;border-right:3px solid #f39c12;border-radius:0 2px 0 0"></div>
                    <div style="position:absolute;bottom:22%;left:18%;width:18px;height:18px;border-bottom:3px solid #f39c12;border-left:3px solid #f39c12;border-radius:0 0 0 2px"></div>
                    <div style="position:absolute;bottom:22%;right:18%;width:18px;height:18px;border-bottom:3px solid #f39c12;border-right:3px solid #f39c12;border-radius:0 0 2px 0"></div>
                </div>

                <p class="text-center text-muted mt-2 mb-0">
                    <small><i class="fas fa-info-circle"></i>
                        Pointez la caméra vers le code CIN ou QR du stagiaire
                    </small>
                </p>

                {{-- Résultat --}}
                <div id="modal-scan-result" class="mt-2" style="display:none">
                    <div id="modal-scan-alert" class="alert mb-0 py-2">
                        <i class="fas fa-check-circle"></i>
                        <strong>Code détecté :</strong>
                        <span id="modal-scan-text" class="ms-1 font-weight-bold"></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i> Fermer
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scanLine {
    0%   { top: 0%; }
    100% { top: 100%; }
}
</style>

@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
    // ========== DATATABLE & SELECT2 ==========
    $('#tempout-table').DataTable({
        language: {
            url: "//cdn.datatables.net/plug-ins/1.10.19/i18n/French.json"
        },
        paging: false,
        order: [[8, "desc"]]
    });

    $('.select2').select2();

    // ========== SCANNER QR / CIN ==========
    (function () {
        var videoEl      = document.getElementById('modal-scanner-video');
        var canvas       = document.createElement('canvas');
        var ctx          = canvas.getContext('2d');
        var videoStream  = null;
        var scanInterval = null;
        var currentCIN   = '';
        var currentDocId = '';

        // Clic sur un bouton Scanner dans le tableau
        $(document).on('click', '.btn-scan-temp', function () {
            currentCIN   = $(this).data('cin');
            currentDocId = $(this).data('id');

            $('#modal-scan-cin-label').text(
                currentCIN ? '— CIN attendu : ' + currentCIN : ''
            );
            $('#modal-scan-result').hide();
            $('#modal-scan-text').text('');

            $('#modal-scanner-temp').modal('show');
        });

        // Démarrer la caméra à l'ouverture du modal
        $('#modal-scanner-temp').on('shown.bs.modal', function () {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(function (stream) {
                    videoStream = stream;
                    videoEl.srcObject = stream;
                    videoEl.play();

                    scanInterval = setInterval(function () {
                        if (videoEl.readyState !== videoEl.HAVE_ENOUGH_DATA) return;

                        canvas.width  = videoEl.videoWidth;
                        canvas.height = videoEl.videoHeight;
                        ctx.drawImage(videoEl, 0, 0, canvas.width, canvas.height);

                        var imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        var code = jsQR(imageData.data, imageData.width, imageData.height);

                        if (code) {
                            stopCamera();
                            showScanResult(code.data);
                        }
                    }, 500);
                })
                .catch(function (err) {
                    alert('Impossible d\'accéder à la caméra : ' + err.message);
                    $('#modal-scanner-temp').modal('hide');
                });
        });

        // Arrêter la caméra à la fermeture du modal
        $('#modal-scanner-temp').on('hidden.bs.modal', function () {
            stopCamera();
        });

        function stopCamera() {
            clearInterval(scanInterval);
            scanInterval = null;
            if (videoStream) {
                videoStream.getTracks().forEach(function (t) { t.stop(); });
                videoStream = null;
            }
        }

        function showScanResult(data) {
            var alertEl = $('#modal-scan-alert');
            var match   = (data === currentCIN);

            alertEl.removeClass('alert-success alert-warning alert-danger');

            if (!currentCIN) {
                alertEl.addClass('alert-success');
                $('#modal-scan-text').text(data + ' ✅');
            } else if (match) {
                alertEl.addClass('alert-success');
                $('#modal-scan-text').text(data + ' ✅ CIN confirmé !');
            } else {
                alertEl.addClass('alert-warning');
                $('#modal-scan-text').text(data + ' ⚠️ CIN différent du stagiaire');
            }

            $('#modal-scan-result').show();

            // Mettre à jour le badge dans la ligne du tableau
            var badge = $('.scan-badge-' + currentDocId);
            var text  = $('.scan-text-'  + currentDocId);
            badge.removeClass('bg-success bg-warning')
                 .addClass(match ? 'bg-success' : 'bg-warning');
            text.text(match ? '✅ ' + data : '⚠️ ' + data);
            $('#scan-result-' + currentDocId).show();
        }
    })();

    // ========== CONFIRMATION DE RETOUR ==========
    $(document).on('click', '.btn-confirm-retour', function(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        
        Swal.fire({
            title: 'Confirmer le retour',
            html: "Êtes-vous sûr de vouloir réintégrer ce document dans le stock ?<br><br><small class='text-muted'>Ceci mettra fin au retrait temporaire.</small>",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-check me-1"></i> Oui, confirmer',
            cancelButtonText: '<i class="ti ti-x me-1"></i> Annuler',
            customClass: {
                confirmButton: 'btn btn-success me-2',
                cancelButton: 'btn btn-secondary'
            },
            buttonsStyling: false,
            backdrop: `rgba(0,0,0,0.4)`
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Mise à jour...',
                    text: 'Veuillez patienter',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                form.submit();
            }
        });
    });
</script>
@stop