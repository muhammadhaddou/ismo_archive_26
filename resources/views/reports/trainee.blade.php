{{-- resources/views/reports/trainee.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 13px;
            color: #1a1a1a;
            direction: ltr;
        }

        /* ========================
           HEADER OFPPT STYLE
        ======================== */
        .header-wrapper {
            border-bottom: 3px solid #3d7a3d;
            padding-bottom: 10px;
            margin-bottom: 0;
        }

        .header-logo-row {
            display: table;
            width: 100%;
        }
        .header-logo-left {
            display: table-cell;
            vertical-align: middle;
            width: 60%;
            padding: 15px 20px;
        }
        .header-logo-right {
            display: table-cell;
            vertical-align: middle;
            text-align: right;
            padding: 15px 20px;
        }

        /* OFPPT Diamond Logo (SVG-based) */
        .ofppt-logo-block {
            display: inline-block;
        }
        .ofppt-diamonds {
            margin-bottom: 4px;
        }
        .ofppt-text {
            font-size: 22px;
            font-weight: bold;
            color: #1a1a1a;
            letter-spacing: 2px;
        }
        .ofppt-arabic {
            font-size: 15px;
            color: #1a1a1a;
            direction: rtl;
            font-family: DejaVu Sans, sans-serif;
            margin-top: 4px;
            border-top: 1px solid #003087;
            padding-top: 4px;
        }
        .ofppt-french {
            font-size: 11px;
            color: #003087;
            font-weight: bold;
            margin-top: 6px;
        }
        .ofppt-french span { color: #c8a000; }

        /* HEADER RIGHT: Report info */
        .report-info-box {
            background: #003087;
            color: white;
            padding: 14px 18px;
            border-radius: 6px;
            text-align: center;
            display: inline-block;
            min-width: 200px;
        }
        .report-info-box h2 {
            font-size: 14px;
            margin-bottom: 6px;
            border-bottom: 1px solid rgba(255,255,255,0.4);
            padding-bottom: 6px;
        }
        .report-info-box p {
            font-size: 10px;
            opacity: 0.85;
            margin-bottom: 3px;
        }

        /* SUBHEADER BAND */
        .subheader-band {
            background: #003087;
            color: white;
            padding: 8px 20px;
            font-size: 12px;
            display: table;
            width: 100%;
        }
        .subheader-band .sh-left { display: table-cell; }
        .subheader-band .sh-right { display: table-cell; text-align: right; }

        .content { padding: 16px 20px; }

        /* SECTION */
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: white;
            background: #003087;
            padding: 6px 12px;
            margin: 18px 0 8px;
            border-left: 4px solid #3d7a3d;
        }

        /* INFO TABLE */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .info-table td {
            padding: 6px 10px;
            border: 1px solid #ddd;
        }
        .info-table td.label {
            background: #f0f4f8;
            font-weight: bold;
            color: #444;
            width: 30%;
        }

        /* DATA TABLE */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
            font-size: 12px;
        }
        table.data-table thead tr { background: #003087; color: white; }
        table.data-table th { padding: 7px 10px; border: 1px solid #003087; text-align: left; font-size: 11px; }
        table.data-table td { padding: 6px 10px; border: 1px solid #ddd; text-align: left; }
        table.data-table tbody tr:nth-child(even) { background: #f0f4ff; }

        /* STATUS BADGE */
        .status {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
        }
        .status-stock  { background:#d1fae5; color:#065f46; }
        .status-temp   { background:#fef3c7; color:#92400e; }
        .status-final  { background:#fee2e2; color:#991b1b; }
        .status-remis  { background:#dbeafe; color:#1e40af; }
        .status-ecoule { background:#ffe4e6; color:#9f1239; }

        /* QR SECTION */
        .qr-row {
            display: table;
            width: 100%;
            margin-top: 16px;
        }
        .qr-cell {
            display: table-cell;
            vertical-align: middle;
        }
        .qr-box {
            border: 1px solid #ddd;
            padding: 6px;
            display: inline-block;
            border-radius: 4px;
        }
        .qr-label {
            font-size: 9px;
            color: #888;
            text-align: center;
            margin-top: 4px;
        }

        /* FOOTER */
        .footer {
            margin-top: 20px;
            border-top: 2px solid #2e7d49;
            padding-top: 10px;
            font-size: 9px;
            color: #888;
            display: table;
            width: 100%;
        }
        .footer .f-left  { display: table-cell; }
        .footer .f-right { display: table-cell; text-align: right; }
    </style>
</head>
<body>

{{-- ===== HEADER OFPPT ===== --}}
<div class="header-wrapper">
    <div class="header-logo-row">

        {{-- LEFT: OFPPT Logo block --}}
        <div class="header-logo-left">
            <div class="ofppt-logo-block">
                {{-- Real OFPPT logo image --}}
                <img src="{{ public_path('images/ofppt_logo.png') }}"
                     alt="OFPPT Logo"
                     style="height: 60px; display: block; margin-bottom: 6px;">
                <div class="ofppt-arabic" style="direction: rtl; unicode-bidi: bidi-override; text-align: right;">{!! $arabicHeader !!}</div>
                <div class="ofppt-french">Office de la Formation Professionnelle<br>et de la <span>Promotion du Travail</span></div>
            </div>
        </div>

        {{-- RIGHT: Report metadata box --}}
        <div class="header-logo-right">
            <div class="report-info-box">
                <h2>Rapport Stagiaire</h2>
                <p>ISMO Archive</p>
                <p>Système de suivi des documents</p>
                <p style="margin-top:6px; font-size:11px; font-weight:bold;">
                    {{ now()->format('d/m/Y à H:i') }}
                </p>
            </div>
        </div>

    </div>
</div>

{{-- SUBHEADER BAND --}}
<div class="subheader-band">
    <div class="sh-left">
        <strong>{{ $trainee->last_name }} {{ $trainee->first_name }}</strong>
        &nbsp;|&nbsp; CIN : {{ $trainee->cin }}
        @if($trainee->cef) &nbsp;|&nbsp; CEF : {{ $trainee->cef }} @endif
    </div>
    <div class="sh-right">
        Groupe : {{ $trainee->group }} &nbsp;|&nbsp; Promotion : {{ $trainee->graduation_year }}
    </div>
</div>

<div class="content">

    {{-- 1. Informations du stagiaire --}}
    <div class="section-title">&#128203; Informations du stagiaire</div>
    <table class="info-table">
        <tr>
            <td class="label">Nom complet</td>
            <td>{{ $trainee->first_name }} {{ $trainee->last_name }}</td>
            <td class="label">CIN</td>
            <td>{{ $trainee->cin }}</td>
        </tr>
        <tr>
            <td class="label">Filière</td>
            <td>{{ $trainee->filiere->nom_filiere ?? '—' }} ({{ $trainee->filiere->code_filiere ?? '—' }})</td>
            <td class="label">Secteur</td>
            <td>{{ $trainee->filiere->secteur->nom_secteur ?? '—' }}</td>
        </tr>
        <tr>
            <td class="label">Groupe</td>
            <td>{{ $trainee->group }}</td>
            <td class="label">Année de graduation</td>
            <td>{{ $trainee->graduation_year }}</td>
        </tr>
        <tr>
            <td class="label">Date de naissance</td>
            <td>{{ $trainee->date_naissance ? \Carbon\Carbon::parse($trainee->date_naissance)->format('d/m/Y') : '—' }}</td>
            <td class="label">Téléphone</td>
            <td>{{ $trainee->phone ?? '—' }}</td>
        </tr>
    </table>

    {{-- 2. État des documents --}}
    <div class="section-title">&#128193; État des documents</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Type de document</th>
                <th>Année</th>
                <th>Statut</th>
                <th>Référence</th>
            </tr>
        </thead>
        <tbody>
            @forelse($trainee->documents as $doc)
            @php
                $statusMap = [
                    'Stock'     => ['label' => 'En stock',           'class' => 'status-stock'],
                    'Temp_Out'  => ['label' => 'Sortie temporaire',  'class' => 'status-temp'],
                    'Final_Out' => ['label' => 'Sortie définitive',  'class' => 'status-final'],
                    'Remis'     => ['label' => 'Remis',              'class' => 'status-remis'],
                    'Ecoule'    => ['label' => 'Écoulé',             'class' => 'status-ecoule'],
                ];
                $s = $statusMap[$doc->status] ?? ['label' => $doc->status, 'class' => ''];
            @endphp
            <tr>
                <td>{{ $doc->type }}</td>
                <td>{{ $doc->level_year ?? '—' }}</td>
                <td><span class="status {{ $s['class'] }}">{{ $s['label'] }}</span></td>
                <td>{{ $doc->reference_number ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;color:#888;">Aucun document enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- 3. Historique des mouvements --}}
    <div class="section-title">&#128336; Historique des mouvements</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Document</th>
                <th>Opération</th>
                <th>Responsable</th>
                <th>Observations</th>
            </tr>
        </thead>
        <tbody>
            @php
                $allMovements = $trainee->documents->flatMap->movements->sortByDesc('date_action');
            @endphp
            @forelse($allMovements as $mv)
            <tr>
                <td>{{ \Carbon\Carbon::parse($mv->date_action)->format('d/m/Y H:i') }}</td>
                <td>{{ $mv->document->type ?? '—' }}</td>
                <td>{{ $mv->action_type }}</td>
                <td>{{ $mv->user->name ?? '—' }}</td>
                <td>{{ $mv->observations ?? '—' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align:center;color:#888;">Aucun mouvement enregistré</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- QR Code + Signature --}}
    <div class="qr-row" style="margin-top: 24px;">
        <div class="qr-cell" style="width: 15%;">
            <div class="qr-box">
                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(70)->generate(route('trainees.show', $trainee)) !!}
            </div>
            <div class="qr-label">Vérification en ligne</div>
        </div>
        <div class="qr-cell" style="padding-left: 20px; color: #666; font-size: 11px;">
            <p>Ce document a été généré automatiquement par le système <strong>ISMO Archive</strong>.</p>
            <p style="margin-top: 4px;">Il est certifié conforme aux données enregistrées dans le système d'information de l'ISMO.</p>
            <p style="margin-top: 4px; color: #999; font-size: 10px;">Scannez le QR code pour vérifier l'authenticité de ce document.</p>
        </div>
        <div class="qr-cell" style="text-align: right; width: 30%; vertical-align: bottom;">
            <p style="font-size: 11px; color: #555;">Le chef de service :</p>
            <div style="margin-top: 30px; border-top: 1px solid #999; width: 140px; display: inline-block;"></div>
            <p style="font-size: 9px; color: #aaa; margin-top: 2px;">Signature et cachet</p>
        </div>
    </div>

</div>

{{-- FOOTER --}}
<div class="footer">
    <div class="f-left">
        ISMO Archive &mdash; OFPPT &mdash; Document interne confidentiel
    </div>
    <div class="f-right">
        Imprimé le {{ now()->format('d/m/Y à H:i') }}
    </div>
</div>

</body>
</html>