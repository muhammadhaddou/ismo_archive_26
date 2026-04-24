<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Stagiaire | Configuration du mot de passe</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        body {
            background-color: #f4f6f9;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            width: 100%;
            max-width: 400px;
            margin: 0 15px;
        }
        .portal-header {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            padding: 20px;
            border-radius: .25rem .25rem 0 0;
            text-align: center;
        }
        .portal-header h3 {
            margin: 0;
            font-weight: 600;
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
<body>

<div class="institution-titles">
    <h5>
        مكتب التكوين المهني وإنعاش الشغل<br>
        <span style="font-size: 0.85em; color: #444;">Office de la Formation Professionnelle et de la Promotion du Travail</span>
    </h5>
    <h6>
        المعهد المتخصص في مهن الأوفشورينغ<br>
        <span style="font-size: 0.85em;">Institut Spécialisé dans les Métiers de l'Offshoring</span>
    </h6>
</div>

<div class="login-box shadow rounded">
    <div class="portal-header">
        <h3><i class="fas fa-lock mr-2"></i>Sécurisation</h3>
        <p class="mb-0 mt-2">Créez votre mot de passe</p>
    </div>
    
    <div class="card card-outline card-primary mb-0" style="border-radius: 0 0 .25rem .25rem; border-top: none;">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Pour protéger votre espace, veuillez définir un mot de passe personnel.</p>

            @if(session('warning'))
                <div class="alert alert-warning text-center p-2 mb-3">
                    {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger p-2 mb-3">
                    <ul class="mb-0 pl-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('trainee.password.store') }}" method="post">
                @csrf
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Nouveau mot de passe" required minlength="6" autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmer le mot de passe" required minlength="6">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                
                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">Enregistrer <i class="fas fa-save ml-1"></i></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
</body>
</html>
