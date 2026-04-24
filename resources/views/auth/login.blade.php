<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration | ISMO Archive</title>

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
        .admin-header {
            background: linear-gradient(135deg, #343a40, #1d2124); /* Dark theme for Admin */
            color: white;
            padding: 20px;
            border-radius: .25rem .25rem 0 0;
            text-align: center;
        }
        .admin-header h3 {
            margin: 0;
            font-weight: 600;
        }
        .institution-titles {
            text-align: center;
            margin-bottom: 30px;
            padding: 0 15px;
        }
        .institution-titles h5 {
            color: #343a40;
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
    <div class="admin-header">
        <h3><i class="fas fa-user-shield mr-2"></i>Administration</h3>
        <p class="mb-0 mt-2">ISMO Archive - Gestion du Système</p>
    </div>
    
    <div class="card card-outline card-dark mb-0" style="border-radius: 0 0 .25rem .25rem; border-top: none;">
        <div class="card-body login-card-body">
            <p class="login-box-msg">Connectez-vous à votre session</p>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success text-center p-2 mb-3">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger text-center p-2 mb-3">
                    <ul class="mb-0 list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="input-group mb-3">
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="Adresse Email" required autofocus>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-envelope"></span>
                        </div>
                    </div>
                </div>
                
                <div class="input-group mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Mot de passe" required>
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-lock"></span>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-8">
                        <div class="icheck-primary">
                            <input type="checkbox" id="remember_me" name="remember">
                            <label for="remember_me" style="font-weight: normal;">
                                Se souvenir de moi
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-dark btn-block">Connexion Administrateur <i class="fas fa-sign-in-alt ml-1"></i></button>
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
