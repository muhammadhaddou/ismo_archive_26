<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Espace Stagiaire | Tableau de bord</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-vendors.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
        background-color: #f4f6f9;
      }
      .portal-navbar {
          background: linear-gradient(135deg, #007bff, #0056b3);
      }
      .portal-navbar .navbar-brand {
          color: white !important;
      }
      .btn-logout {
          color: white;
          border-color: rgba(255,255,255,0.5);
      }
      .btn-logout:hover {
          background: rgba(255,255,255,0.1);
          color: white;
      }
    </style>
</head>
<body >
<div class="page">
    <!-- Navbar -->
    <header class="navbar navbar-expand-md portal-navbar d-print-none" data-bs-theme="dark">
        <div class="container-xl">
            <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
                <a href="#">
                    <i class="fas fa-user-graduate me-2"></i> ISMO Espace Stagiaire
                </a>
            </h1>
            <div class="navbar-nav flex-row order-md-last">
                <div class="nav-item">
                    <span class="me-3 fw-bold d-none d-sm-inline-block text-white">{{ $trainee->first_name }} {{ $trainee->last_name }}</span>
                    <form action="{{ route('trainee.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-logout"><i class="fas fa-sign-out-alt me-1"></i> Déconnexion</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="page-wrapper">
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <h2 class="page-title">👋 Bienvenue, {{ $trainee->first_name }} {{ $trainee->last_name }}</h2>
                        <div class="text-secondary mt-1">Cet espace vous permet de formuler vos demandes de retrait de documents administratifs.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="page-body">
            <div class="container-xl">
                
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible"><button type="button" class="btn-close" data-bs-dismiss="alert"></button>{{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row row-cards">
                    <!-- Formulaire de demande -->
                    <div class="col-lg-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <h3 class="card-title text-white"><i class="fas fa-plus-circle me-1"></i> Nouvelle demande</h3>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('trainee.requests.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Document souhaité *</label>
                                        <select name="document_type" id="document_type" class="form-select" required>
                                            <option value="">-- Sélectionnez --</option>
                                            <option value="Bac">Baccalauréat</option>
                                            <option value="Diplome">Diplôme</option>
                                            <option value="Attestation">Attestation de réussite</option>
                                            <option value="Bulletin">Bulletin de notes</option>
                                        </select>
                                    </div>
                                    
                                    <div id="bac_options" style="display: none;" class="mb-3 bg-light p-3 border rounded">
                                        <label class="form-label border-bottom pb-1 mb-2">Type de retrait pour le Baccalauréat :</label>
                                        <label class="form-check">
                                            <input class="form-check-input" type="radio" name="bac_type" value=" - Temporaire" checked>
                                            <span class="form-check-label">Retrait Temporaire (Ex: Pour inscription ou concours)</span>
                                        </label>
                                        <label class="form-check mt-2">
                                            <input class="form-check-input" type="radio" name="bac_type" value=" - Définitif">
                                            <span class="form-check-label text-danger">Retrait Définitif (Fin de formation ou abandon)</span>
                                        </label>
                                    </div>

                                    <div id="document_conditions" class="alert alert-info py-2 px-3 text-sm shadow-sm" style="display: none;">
                                        <!-- Les conditions s'afficheront ici -->
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-paper-plane me-2"></i> Soumettre la demande</button>
                                </form>

                                <!-- Disponibilités informatives -->
                                @if($availabilities->count() > 0)
                                <div class="mt-4">
                                    <h4 class="card-title"><i class="fas fa-clock me-1 text-info"></i> Horaires annoncés :</h4>
                                    <ul class="list-unstyled small text-muted">
                                        @foreach($availabilities as $type => $av)
                                            @if($av->description)
                                                <li class="mb-1"><strong>{{ $type }} :</strong> {{ $av->description }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                
                            </div>
                        </div>
                    </div>

                    <!-- Liste des demandes -->
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <h3 class="card-title"><i class="fas fa-history me-1"></i> Historique & Réponses</h3>
                            </div>
                            <div class="table-responsive">
                                <table class="table card-table table-vcenter text-nowrap datatable">
                                    <thead>
                                        <tr>
                                            <th>Date de demande</th>
                                            <th>Document</th>
                                            <th>Statut</th>
                                            <th>Message de l'admin & RDV</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($requests as $req)
                                            <tr>
                                                <td>{{ $req->created_at->format('d/m/Y H:i') }}</td>
                                                <td><span class="fw-bold">{{ $req->document_type }}</span></td>
                                                <td>
                                                    @if($req->status == 'en_attente')
                                                        <span class="badge bg-warning text-white">En attente</span>
                                                    @elseif($req->status == 'planifie')
                                                        <span class="badge bg-info text-white">RDV Fixé</span>
                                                    @elseif($req->status == 'termine')
                                                        <span class="badge bg-success text-white">Retrait Terminé</span>
                                                    @elseif($req->status == 'rejete')
                                                        <span class="badge bg-danger text-white">Demande Rejetée</span>
                                                    @endif
                                                </td>
                                                <td class="text-wrap">
                                                    @if($req->appointment_date)
                                                        <div class="text-success fw-bold mb-1">
                                                            <i class="far fa-calendar-alt"></i> RDV: {{ $req->appointment_date->format('d/m/Y à H:i') }}
                                                        </div>
                                                    @endif
                                                    @if($req->admin_message)
                                                        <div class="p-2 bg-light border rounded text-muted small mt-1">
                                                            <i class="fas fa-comment-dots text-primary me-1"></i> "{{ $req->admin_message }}"
                                                        </div>
                                                    @else
                                                        <span class="text-muted small">Aucun message pour le moment</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    Aucune demande soumise.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
<script>
    $(document).ready(function() {
        const conditions = {
            'Bac': {
                text: "<strong>Conditions Baccalauréat :</strong><br>Le stagiaire doit se présenter personnellement muni de sa Carte d'Identité Nationale (CIN) originale.",
                icon: "fa-graduation-cap"
            },
            'Diplome': {
                text: "<strong>Conditions Diplôme :</strong><br>Le diplôme original n'est remis qu'au stagiaire en personne ou à une personne munie d'une procuration légalisée.",
                icon: "fa-scroll"
            },
            'Attestation': {
                text: "<strong>Conditions Attestation :</strong><br>L'attestation ne peut être délivrée qu'après validation de vos résultats annuels par le conseil de classe.",
                icon: "fa-file-signature"
            },
            'Bulletin': {
                text: "<strong>Conditions Bulletin de notes :</strong><br>Le bulletin est délivré à la fin de chaque semestre après les délibérations officielles.",
                icon: "fa-list-alt"
            }
        };

        $('#document_type').change(function() {
            var selected = $(this).val();
            
            if (selected === 'Bac') {
                $('#bac_options').slideDown(200);
            } else {
                $('#bac_options').slideUp(200);
            }

            if (selected && conditions[selected]) {
                var content = '<div class="d-flex align-items-start"><i class="fas ' + conditions[selected].icon + ' mt-1 me-2 text-primary" style="font-size:1.2rem;"></i><div>' + conditions[selected].text + '</div></div>';
                $('#document_conditions').html(content).slideDown(200);
            } else {
                $('#document_conditions').slideUp(200);
            }
        });
    });
</script>
</body>
</html>
