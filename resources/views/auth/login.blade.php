<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <title>Connexion | ISMO Archive</title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

      * { box-sizing: border-box; margin: 0; padding: 0; }

      body {
          font-family: 'Inter', sans-serif;
          min-height: 100vh;
          background: linear-gradient(135deg, #0f4c81 0%, #1a7abf 40%, #0f4c81 100%);
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 20px;
          position: relative;
          overflow: hidden;
      }

      /* Animated background circles */
      body::before, body::after {
          content: '';
          position: absolute;
          border-radius: 50%;
          background: rgba(255,255,255,0.05);
          animation: float 8s ease-in-out infinite;
      }
      body::before {
          width: 500px; height: 500px;
          top: -150px; right: -100px;
          animation-delay: 0s;
      }
      body::after {
          width: 350px; height: 350px;
          bottom: -100px; left: -80px;
          animation-delay: 3s;
      }
      @keyframes float {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50%       { transform: translateY(-20px) rotate(5deg); }
      }

      .login-wrapper {
          width: 100%;
          max-width: 440px;
          position: relative;
          z-index: 10;
          animation: slideUp 0.6s ease-out;
      }
      @keyframes slideUp {
          from { opacity: 0; transform: translateY(30px); }
          to   { opacity: 1; transform: translateY(0); }
      }

      /* Header branding */
      .brand-header {
          text-align: center;
          margin-bottom: 28px;
      }
      .brand-logo {
          width: 90px;
          height: 90px;
          border-radius: 50%;
          object-fit: contain;
          background: white;
          padding: 8px;
          box-shadow: 0 8px 32px rgba(0,0,0,0.25);
          margin-bottom: 16px;
          transition: transform 0.3s ease;
      }
      .brand-logo:hover { transform: scale(1.05); }
      .brand-title-ar {
          color: white;
          font-size: 0.95rem;
          font-weight: 600;
          line-height: 1.5;
          margin-bottom: 6px;
          text-shadow: 0 1px 4px rgba(0,0,0,0.3);
          direction: rtl;
      }
      .brand-title-fr {
          color: rgba(255,255,255,0.82);
          font-size: 0.78rem;
          font-weight: 400;
          line-height: 1.5;
      }

      /* Card */
      .login-card {
          background: white;
          border-radius: 20px;
          padding: 40px 36px;
          box-shadow: 0 20px 60px rgba(0,0,0,0.25);
          border: none;
      }
      .login-card-title {
          text-align: center;
          font-size: 1.5rem;
          font-weight: 700;
          color: #1a2744;
          margin-bottom: 6px;
      }
      .login-card-subtitle {
          text-align: center;
          color: #8b95a5;
          font-size: 0.875rem;
          margin-bottom: 28px;
      }

      /* Form controls */
      .form-label {
          font-weight: 600;
          font-size: 0.875rem;
          color: #374151;
          margin-bottom: 6px;
      }
      .form-control {
          border: 1.5px solid #e5e7eb;
          border-radius: 10px;
          padding: 11px 14px;
          font-size: 0.9rem;
          transition: border-color 0.2s, box-shadow 0.2s;
          color: #1f2937;
      }
      .form-control:focus {
          border-color: #1a7abf;
          box-shadow: 0 0 0 3px rgba(26,122,191,0.15);
          outline: none;
      }
      .input-icon-wrapper {
          position: relative;
      }
      .input-icon-wrapper .field-icon {
          position: absolute;
          left: 13px;
          top: 50%;
          transform: translateY(-50%);
          color: #9ca3af;
          font-size: 0.9rem;
          pointer-events: none;
      }
      .input-icon-wrapper .form-control {
          padding-left: 38px;
      }
      .toggle-pw {
          position: absolute;
          right: 12px;
          top: 50%;
          transform: translateY(-50%);
          background: none;
          border: none;
          color: #9ca3af;
          cursor: pointer;
          padding: 0;
          font-size: 0.9rem;
      }
      .toggle-pw:hover { color: #1a7abf; }

      /* Submit button */
      .btn-login {
          background: linear-gradient(135deg, #0f4c81, #1a7abf);
          color: white;
          border: none;
          border-radius: 10px;
          padding: 13px;
          font-size: 0.95rem;
          font-weight: 600;
          width: 100%;
          cursor: pointer;
          transition: transform 0.15s, box-shadow 0.15s;
          letter-spacing: 0.3px;
      }
      .btn-login:hover {
          transform: translateY(-1px);
          box-shadow: 0 6px 20px rgba(15,76,129,0.4);
          color: white;
      }
      .btn-login:active { transform: translateY(0); }

      /* Remember me */
      .form-check-input:checked {
          background-color: #1a7abf;
          border-color: #1a7abf;
      }

      /* Alerts */
      .alert { border-radius: 10px; font-size: 0.875rem; }

      /* Footer */
      .login-footer {
          text-align: center;
          margin-top: 20px;
          color: rgba(255,255,255,0.6);
          font-size: 0.75rem;
      }
    </style>
</head>
<body>
    <div class="login-wrapper">

        {{-- Branding OFPPT --}}
        <div class="brand-header">
            <img src="/images/ofppt_logo.png" alt="OFPPT" class="brand-logo">
            <div class="brand-title-ar">
                مكتب التكوين المهني وإنعاش الشغل<br>
                <span style="font-size:0.8em; opacity:0.85">المعهد المتخصص في مهن الأوفشورينغ</span>
            </div>
            <div class="brand-title-fr">
                Office de la Formation Professionnelle et de la Promotion du Travail<br>
                Institut Spécialisé dans les Métiers de l'Offshoring
            </div>
        </div>

        {{-- Login Card --}}
        <div class="login-card">
            <h2 class="login-card-title">
                <i class="fas fa-shield-alt me-2" style="color:#1a7abf;font-size:1.2rem;"></i>
                Administration
            </h2>
            <p class="login-card-subtitle">Connectez-vous à votre session</p>

            @if (session('status'))
                <div class="alert alert-success text-center p-2 mb-3">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger p-2 mb-3">
                    <i class="fas fa-exclamation-circle me-1"></i>
                    @foreach ($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="off">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Adresse Email</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-envelope field-icon"></i>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}"
                               placeholder="exemple@ofppt.ma"
                               required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Mot de passe</label>
                    <div class="input-icon-wrapper">
                        <i class="fas fa-lock field-icon"></i>
                        <input type="password" name="password" id="passwordInput"
                               class="form-control"
                               placeholder="••••••••"
                               required autocomplete="off">
                        <button type="button" class="toggle-pw" onclick="togglePassword()">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember"/>
                        <span class="form-check-label text-muted" style="font-size:0.875rem;">Se souvenir de moi</span>
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt me-2"></i> Connexion
                </button>
            </form>
        </div>

        <p class="login-footer">
            &copy; {{ date('Y') }} ISMO Archive &mdash; OFPPT
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    <script>
    function togglePassword() {
        const input = document.getElementById('passwordInput');
        const icon  = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fas fa-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'fas fa-eye';
        }
    }
    </script>
</body>
</html>
