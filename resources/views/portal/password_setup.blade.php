<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Espace Stagiaire | Première connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
      @import url('https://rsms.me/inter/inter.css');
      :root { --tblr-font-sans-serif: 'Inter Var', -apple-system, BlinkMacSystemFont, sans-serif; }
      body { font-feature-settings: "cv03", "cv04", "cv11"; background:#f0f4f8; }
      .institution-titles { text-align:center; margin-bottom:24px; padding:0 15px; }
      .institution-titles h5 { color:#0056b3; font-weight:700; font-size:1.05rem; margin-bottom:6px; }
      .institution-titles h6 { color:#555; font-weight:600; font-size:.9rem; }
    </style>
</head>
<body class="d-flex flex-column">
    <div class="page page-center">
      <div class="container container-tight py-4">

        <div class="institution-titles">
            <h5>
                مكتب التكوين المهني وإنعاش الشغل<br>
                <span class="text-muted" style="font-size:.85em;">Office de la Formation Professionnelle et de la Promotion du Travail</span>
            </h5>
            <h6>
                المعهد المتخصص في مهن الأوفشورينغ<br>
                <span class="text-muted" style="font-size:.85em;">Institut Spécialisé dans les Métiers de l'Offshoring</span>
            </h6>
        </div>

        {{-- Carte identité stagiaire --}}
        <div class="card shadow-sm border-0 mb-3" style="border-radius:12px; border-left:4px solid #0054a6 !important;">
          <div class="card-body py-3">
            <div class="d-flex align-items-center gap-3">
              <div class="avatar avatar-lg rounded-circle bg-blue text-white fw-bold" style="font-size:1.1rem;width:52px;height:52px;display:inline-flex;align-items:center;justify-content:center;">
                {{ substr($trainee->first_name,0,1) }}{{ substr($trainee->last_name,0,1) }}
              </div>
              <div>
                <div class="fw-bold" style="font-size:1rem;">{{ $trainee->first_name }} {{ strtoupper($trainee->last_name) }}</div>
                <div class="text-muted small mt-1">
                  <span class="me-3"><i class="fas fa-id-card me-1 text-primary"></i>CIN : <strong>{{ $trainee->cin }}</strong></span>
                  <span class="me-3"><i class="fas fa-fingerprint me-1 text-primary"></i>CEF : <strong>{{ $trainee->cef }}</strong></span>
                  @if($trainee->filiere)
                    <span><i class="fas fa-graduation-cap me-1 text-primary"></i>{{ $trainee->filiere->nom_filiere }}</span>
                  @endif
                </div>
              </div>
            </div>
          </div>
        </div>

        {{-- Formulaire --}}
        <div class="card card-md shadow-sm border-0" style="border-radius:12px;">
          <div class="card-body">
            <div class="text-center mb-4">
                <div class="mb-2">
                    <span class="bg-primary-lt rounded-circle d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;">
                        <i class="fas fa-lock text-primary" style="font-size:1.4rem;"></i>
                    </span>
                </div>
                <h2 class="h3 mb-1">Première connexion</h2>
                <p class="text-muted small mb-0">Définissez un mot de passe personnel pour sécuriser votre compte.</p>
            </div>

            @if(session('warning'))
                <div class="alert alert-warning text-center p-2 mb-3">{{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger p-2 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('trainee.password.store') }}" method="post" autocomplete="off">
              @csrf
              <div class="mb-3">
                <label class="form-label fw-semibold">Nouveau mot de passe <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Minimum 6 caractères" required minlength="6" autofocus>
              </div>
              <div class="mb-4">
                <label class="form-label fw-semibold">Confirmer le mot de passe <span class="text-danger">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Répétez le mot de passe" required minlength="6">
              </div>
              <button type="submit" class="btn btn-primary w-100 fw-bold">
                <i class="fas fa-check-circle me-2"></i>Confirmer et accéder à mon espace
              </button>
            </form>
          </div>
        </div>

      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
</body>
</html>
