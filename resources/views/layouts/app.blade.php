<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'ISMO Archive')</title>
    <!-- CSS files -->
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler-vendors.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@2.44.0/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root {
      	--tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
      }
      body {
      	font-feature-settings: "cv03", "cv04", "cv11";
      }
      .nav-link-icon i {
          font-size: 1.35rem;
          color: #6c7a91;
          transition: color 0.2s ease-in-out, transform 0.2s ease-in-out;
      }
      .nav-item.active .nav-link-icon i, .nav-item:hover .nav-link-icon i {
          color: #206bc4;
          transform: scale(1.05);
      }
    </style>
    @yield('css')
</head>
<body >
    <div class="page">
      <!-- Sidebar -->
      <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
        <div class="container-fluid">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-3">
            <a href="{{ route('dashboard') }}" class="text-decoration-none d-flex align-items-center">
              <img src="{{ asset('images/ofppt_logo.png') }}" alt="OFPPT Logo" class="navbar-brand-image me-2 rounded-circle border shadow-sm" style="height: 36px; width: 36px; object-fit: cover; background-color: white; padding: 2px;">
              <span class="fw-bold text-dark" style="letter-spacing: -0.5px; font-size: 1.2rem;">ISMO Archive</span>
            </a>
          </h1>
          <div class="navbar-nav flex-row d-lg-none">
            <div class="nav-item dropdown">
              <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                <span class="avatar avatar-sm" style="background-color: #206bc4;">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Déconnexion</button>
                </form>
              </div>
            </div>
          </div>
          <div class="collapse navbar-collapse" id="sidebar-menu">
            <ul class="navbar-nav pt-lg-3">
              <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}" >
                  <span class="nav-link-icon d-md-none d-lg-inline-block"><i class="ti ti-layout-dashboard"></i></span>
                  <span class="nav-link-title">Tableau de bord</span>
                </a>
              </li>

              <li class="nav-item nav-category mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 10px; padding-left: 1rem; letter-spacing: 0.5px;">Stagiaires</li>
              <li class="nav-item {{ request()->routeIs('trainees.*') && !request()->routeIs('trainees.import') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('trainees.index') }}">
                      <span class="nav-link-icon"><i class="ti ti-users-group"></i></span>
                      <span class="nav-link-title">Liste Stagiaires</span>
                  </a>
              </li>
              <li class="{{ request()->routeIs('diplomes.prets') ? 'active' : '' }} nav-item">
                  <a class="nav-link" href="{{ route('diplomes.prets') }}">
                      <span class="nav-link-icon"><i class="ti ti-certificate"></i></span>
                      <span class="nav-link-title">Diplômés</span>
                  </a>
              </li>

              <li class="nav-item {{ request()->routeIs('trainees.bac.final-out') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('trainees.bac.final-out') }}">
                      <span class="nav-link-icon"><i class="ti ti-file-x text-danger"></i></span>
                      <span class="nav-link-title">Retraits Bac Déf.
                          @php
                              // Abandons purs : Bac Final_Out SANS diplôme (statut ni Remis/Final_Out Diplôme)
                              $bacFinalCount = \App\Models\Document::where('type','Bac')
                                  ->where('status','Final_Out')
                                  ->whereHas('trainee', function($q) {
                                      $q->where(function($q) {
                                          $q->where('statut', '!=', 'diplome')->orWhereNull('statut');
                                      })->whereDoesntHave('documents', fn($q) =>
                                          $q->where('type','Diplome')->whereIn('status',['Final_Out','Remis'])
                                      );
                                  })->count();
                          @endphp
                          @if($bacFinalCount > 0)
                              <span class="badge bg-danger ms-auto">{{ $bacFinalCount }}</span>
                          @endif
                      </span>
                  </a>
              </li>

              <li class="nav-item {{ request()->routeIs('trainees.import') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('trainees.import') }}">
                      <span class="nav-link-icon"><i class="ti ti-file-upload"></i></span>
                      <span class="nav-link-title">Importer Excel</span>
                  </a>
              </li>

              <li class="nav-item nav-category mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 10px; padding-left: 1rem; letter-spacing: 0.5px;">Documents</li>
              <li class="nav-item dropdown {{ request()->is('documents/bac*') ? 'active' : '' }}">
                <a class="nav-link dropdown-toggle" href="#navbar-bac" data-bs-toggle="dropdown" data-bs-auto-close="false" role="button" aria-expanded="{{ request()->is('documents/bac*') ? 'true' : 'false' }}" >
                  <span class="nav-link-icon"><i class="ti ti-school"></i></span>
                  <span class="nav-link-title">Baccalauréat</span>
                </a>
                <div class="dropdown-menu {{ request()->is('documents/bac*') ? 'show' : '' }}">
                  <a class="dropdown-item" href="{{ url('documents/bac') }}">Liste</a>
                  <a class="dropdown-item" href="{{ url('documents/bac/temp-out') }}">Retraits temp.</a>
                  <a class="dropdown-item text-danger" href="{{ url('documents/bac/ecoule') }}">Écoulé</a>

                </div>
              </li>

              <li class="nav-item {{ request()->routeIs('documents.bulletin') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('documents.bulletin') }}">
                      <span class="nav-link-icon"><i class="ti ti-report-analytics"></i></span>
                      <span class="nav-link-title">Bulletins</span>
                  </a>
              </li>
              <li class="nav-item {{ request()->routeIs('documents.attestation') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('documents.attestation') }}">
                      <span class="nav-link-icon"><i class="ti ti-file-certificate"></i></span>
                      <span class="nav-link-title">Attestations</span>
                  </a>
              </li>

              <li class="nav-item nav-category mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 10px; padding-left: 1rem; letter-spacing: 0.5px;">Mouvements</li>
              <li class="nav-item {{ request()->routeIs('movements.index') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('movements.index') }}">
                      <span class="nav-link-icon"><i class="ti ti-history"></i></span>
                      <span class="nav-link-title">Historique</span>
                  </a>
              </li>
              <li class="nav-item {{ request()->routeIs('movements.today') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('movements.today') }}">
                      <span class="nav-link-icon"><i class="ti ti-calendar-event"></i></span>
                      <span class="nav-link-title">Aujourd'hui</span>
                  </a>
              </li>
              <li class="nav-item {{ request()->routeIs('calendrier') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('calendrier') }}">
                      <span class="nav-link-icon"><i class="ti ti-calendar-month text-red"></i></span>
                      <span class="nav-link-title">Calendrier</span>
                  </a>
              </li>

              <li class="nav-item nav-category mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 10px; padding-left: 1rem; letter-spacing: 0.5px;">Validations</li>
              <li class="nav-item {{ request()->routeIs('validations.*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('validations.index') }}">
                      <span class="nav-link-icon"><i class="ti ti-clipboard-check"></i></span>
                      <span class="nav-link-title">Registre</span>
                  </a>
              </li>

              <li class="nav-item nav-category mt-4 mb-2 text-muted text-uppercase fw-bold" style="font-size: 10px; padding-left: 1rem; letter-spacing: 0.5px;">Administration</li>
              <li class="nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('users.index') }}">
                      <span class="nav-link-icon"><i class="ti ti-shield-lock"></i></span>
                      <span class="nav-link-title">Utilisateurs</span>
                  </a>
              </li>
              <li class="nav-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('admin.requests.index') }}">
                      <span class="nav-link-icon"><i class="ti ti-inbox"></i></span>
                      <span class="nav-link-title">Demandes App <span class="badge bg-blue ms-auto">New</span></span>
                  </a>
              </li>

              <li class="nav-item {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                  <a class="nav-link" href="{{ route('admin.settings.availability') }}">
                      <span class="nav-link-icon"><i class="ti ti-settings"></i></span>
                      <span class="nav-link-title">Paramètres</span>
                  </a>
              </li>

            </ul>
          </div>
        </div>
      </aside>

      <!-- Navbar (Top) -->
      <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none" >
        <div class="container-fluid">
          <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="navbar-nav flex-row order-md-last align-items-center gap-2">
            <div class="nav-item dropdown ms-3">
              <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                <span class="avatar avatar-sm" style="background-color: #206bc4; color: white;">{{ substr(Auth::user()->name ?? 'A', 0, 1) }}</span>
                <div class="d-none d-xl-block ps-2">
                  <div>{{ Auth::user()->name ?? 'Admin' }}</div>
                  <div class="mt-1 small text-secondary">Administrateur</div>
                </div>
              </a>
              <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">Déconnexion</button>
                </form>
              </div>
            </div>
          </div>
          <div class="collapse navbar-collapse" id="navbar-menu">
             <!-- Search form can go here -->
          </div>
        </div>
      </header>

      <div class="page-wrapper">
        <!-- Page header -->
        @hasSection('content_header')
        <div class="page-header d-print-none">
          <div class="container-fluid">
            <div class="row g-2 align-items-center">
              <div class="col">
                <div class="mb-1">
                  @yield('content_header')
                </div>
              </div>
            </div>
          </div>
        </div>
        @endif
        
        <!-- Page body -->
        <div class="page-body">
          <div class="container-fluid">
            @yield('content')
          </div>
        </div>
        
      </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    
    <!-- Bootstrap 4 to 5 polyfill helpers (simplistic) -->
    <script>
      // Automatically convert data-toggle to data-bs-toggle for Bootstrap 5 components (modals, dropdowns)
      $(document).ready(function() {
          $('[data-toggle="modal"]').attr('data-bs-toggle', 'modal');
          $('[data-target]').each(function() {
              $(this).attr('data-bs-target', $(this).attr('data-target'));
          });
          $('[data-dismiss="modal"]').attr('data-bs-dismiss', 'modal');
          $('[data-toggle="dropdown"]').attr('data-bs-toggle', 'dropdown');
      });
    </script>

    @yield('js')
</body>
</html>
