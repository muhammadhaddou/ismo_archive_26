<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Espace Stagiaire | ISMO Archive</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        /* --- LEFT SIDE: FORM & TEXT --- */
        .left-side {
            background: linear-gradient(135deg, #e0f7fa 0%, #fce4ec 100%);
            padding: 10px 5%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .main-heading {
            color: #1a237e;
            font-weight: 700;
            font-size: 1.6rem;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .sub-text {
            color: #555;
            font-size: 0.85rem;
            margin-bottom: 15px;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 20px;
            border: none;
        }

        .form-control {
            border-radius: 8px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            height: auto;
            background-color: #f8f9fa;
            font-size: 0.9rem;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
            background-color: #fff;
        }
        .input-group-text {
            border-radius: 8px 0 0 8px;
            background: transparent;
            border-right: none;
            color: #007bff;
        }
        .form-control.border-left-0 {
            border-left: none;
            border-radius: 0 8px 8px 0;
        }

        .btn-primary-custom {
            background-color: #007bff;
            border-color: #007bff;
            border-radius: 8px;
            padding: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .btn-primary-custom:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,123,255,0.4);
        }

        /* --- RIGHT SIDE: HERO IMAGE & STATS --- */
        .right-side {
            background-color: #fff;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .hero-bg-circle {
            position: absolute;
            width: 80%;
            height: 80%;
            background: #fff3e0;
            border-radius: 50%;
            z-index: 1;
            right: -10%;
        }

        .hero-img {
            max-width: 75%;
            max-height: 55vh;
            object-fit: cover;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0,0,0,0.15);
            z-index: 2;
            position: relative;
        }

        /* Floating Cards */
        .floating-card {
            position: absolute;
            background: white;
            padding: 15px 25px;
            border-radius: 12px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 15px;
            animation: float 4s ease-in-out infinite;
        }
        
        .floating-card .icon-box {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .floating-card .text-box h4 {
            margin: 0;
            font-weight: 700;
            color: #333;
            font-size: 1.2rem;
        }
        .floating-card .text-box p {
            margin: 0;
            color: #777;
            font-size: 0.75rem;
        }

        .card-1 { top: 20%; left: 10%; animation-delay: 0s; }
        .card-2 { bottom: 20%; right: 10%; animation-delay: 2s; }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }

        /* --- BOTTOM STATS ROW --- */
        .bottom-stats {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            display: flex;
            z-index: 4;
        }
        .stat-box {
            flex: 1;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        .stat-box .icon { font-size: 1.4rem; }
        .stat-box.bg-1 { background-color: #e8f5e9; color: #2e7d32; }
        .stat-box.bg-2 { background-color: #e3f2fd; color: #1565c0; }
        .stat-box.bg-3 { background-color: #f3e5f5; color: #6a1b9a; }
        .stat-box.bg-4 { background-color: #fff3e0; color: #e65100; }

        .stat-box h4 { margin: 0; font-weight: 700; font-size: 1rem; }
        .stat-box p { margin: 0; font-size: 0.75rem; opacity: 0.8; }

        /* Logos */
        .logos {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 30px;
        }
        .logos img { height: 35px; }
        
        .inst-title {
            font-size: 0.8rem;
            color: #555;
            font-weight: 600;
            line-height: 1.2;
        }

        @media (max-width: 991px) {
            .right-side { display: none !important; }
            .left-side { height: 100vh; padding: 30px 15px; }
            .main-heading { font-size: 1.5rem; }
            .sub-text { font-size: 0.85rem; }
            .bottom-stats { display: none; }
            .login-card { padding: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        }
    </style>
</head>
<body>

<div class="container-fluid h-100 p-0">
    <div class="row no-gutters h-100">
        
        <!-- LEFT SIDE -->
        <div class="col-lg-6 left-side">
            
            <div class="logos">
                <img src="{{ asset('images/ofppt_logo.png') }}" alt="OFPPT">
                <div class="inst-title">
                    Institut Spécialisé dans les<br>Métiers de l'Offshoring
                </div>
            </div>

            <h1 class="main-heading">ISMO Archive :<br>Le choix idéal pour vos documents !</h1>
            
            <p class="sub-text">
                Un espace 100% digitalisé pour consulter, demander et suivre le statut de vos diplômes et documents administratifs en toute sécurité.
            </p>

            <div class="login-card">
                <h6 class="font-weight-bold mb-3 text-center">Connexion à votre espace</h6>
                
                @if(session('error'))
                    <div class="alert alert-danger text-center p-2 mb-3">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('trainee.login') }}" method="post">
                    @csrf
                    <div class="form-group mb-2">
                        <label class="small text-muted font-weight-bold">Numéro CEF</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                            </div>
                            <input type="text" name="cef" class="form-control border-left-0" placeholder="Ex: Z123456" required autofocus>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="small text-muted font-weight-bold">Date de naissance</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                            <input type="date" name="date_naissance" class="form-control border-left-0" required>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small text-muted font-weight-bold">Mot de passe (CIN)</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                            </div>
                            <input type="password" name="cin" class="form-control border-left-0" placeholder="••••••••" required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary-custom btn-block text-white">
                        EXPLOREZ VOTRE ESPACE <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </form>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-6 right-side d-none d-lg-flex">
            
            <div class="hero-bg-circle"></div>
            
            <!-- Image Etudiante -->
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Student" class="hero-img">

            <!-- Floating Cards -->
            <div class="floating-card card-1">
                <div class="icon-box" style="background: #e3f2fd; color: #1976d2;">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="text-box">
                    <h4>7500+</h4>
                    <p>Stagiaires actifs</p>
                </div>
            </div>

            <div class="floating-card card-2">
                <div class="icon-box" style="background: #fbe9e7; color: #d84315;">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <div class="text-box">
                    <h4>100%</h4>
                    <p>Espace Sécurisé</p>
                </div>
            </div>

            <!-- Bottom Stats -->
            <div class="bottom-stats shadow-lg">
                <div class="stat-box bg-1">
                    <i class="fas fa-file-alt icon"></i>
                    <div>
                        <h4>134</h4>
                        <p>Demandes en ligne</p>
                    </div>
                </div>
                <div class="stat-box bg-2">
                    <i class="fas fa-clock icon"></i>
                    <div>
                        <h4>24h</h4>
                        <p>Délai de traitement</p>
                    </div>
                </div>
                <div class="stat-box bg-3">
                    <i class="fas fa-qrcode icon"></i>
                    <div>
                        <h4>684</h4>
                        <p>Documents certifiés</p>
                    </div>
                </div>
                <div class="stat-box bg-4">
                    <i class="fas fa-users icon"></i>
                    <div>
                        <h4>941</h4>
                        <p>Stagiaires diplômés</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
