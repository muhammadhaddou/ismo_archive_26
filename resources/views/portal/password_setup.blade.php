<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Espace Stagiaire | Configuration du mot de passe</title>
    <!-- CSS files -->
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
      }
      .institution-titles {
          text-align: center;
          margin-bottom: 30px;
          padding: 0 15px;
      }
      .institution-titles h5 {
          color: #0056b3;
          font-weight: 700;
          font-size: 1.1rem;
          margin-bottom: 8px;
          line-height: 1.4;
      }
      .institution-titles h6 {
          color: #555;
          font-weight: 600;
          font-size: 0.95rem;
          line-height: 1.4;
      }
    </style>
</head>
<body class="d-flex flex-column bg-white">
    <div class="page page-center">
      <div class="container container-tight py-4">
        
        <div class="institution-titles">
            <h5>
                مكتب التكوين المهني وإنعاش الشغل<br>
                <span class="text-muted" style="font-size: 0.85em;">Office de la Formation Professionnelle et de la Promotion du Travail</span>
            </h5>
            <h6>
                المعهد المتخصص في مهن الأوفشورينغ<br>
                <span class="text-muted" style="font-size: 0.85em;">Institut Spécialisé dans les Métiers de l'Offshoring</span>
            </h6>
        </div>

        <div class="card card-md shadow-sm border-0" style="border-radius: 12px; background-color: #f8fafc;">
          <div class="card-body">
            <h2 class="h3 text-center mb-4"><i class="fas fa-lock me-2 text-primary"></i>Sécurisation</h2>
            <p class="text-center text-muted mb-4">Créez votre mot de passe pour protéger votre espace.</p>
            
            @if(session('warning'))
                <div class="alert alert-warning text-center p-2 mb-3">
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger p-2 mb-3">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('trainee.password.store') }}" method="post" autocomplete="off">
              @csrf
              <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <div class="input-group input-group-flat">
                  <input type="password" name="password" class="form-control" placeholder="Entrez un mot de passe (min. 6 caractères)" required minlength="6" autofocus>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label">Confirmer le mot de passe</label>
                <div class="input-group input-group-flat">
                  <input type="password" name="password_confirmation" class="form-control" placeholder="Répétez le mot de passe" required minlength="6">
                </div>
              </div>
              
              <div class="form-footer">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Enregistrer <i class="fas fa-save ms-2"></i></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- Libs JS -->
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
  </body>
</html>
