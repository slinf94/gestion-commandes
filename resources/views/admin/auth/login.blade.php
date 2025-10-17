<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Connexion Admin - Allo Mobile</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #38B04A;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 400px;
        }

        .header {
            background-color: #38B04A;
            padding: 40px 30px 30px;
            text-align: center;
            color: white;
        }

        .header .icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }

        .header h1 {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .form-section {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .input-container {
            position: relative;
        }

        .input-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            background-color: white;
        }

        .form-control:focus {
            outline: none;
            border-color: #38B04A;
            box-shadow: 0 0 0 3px rgba(56, 176, 74, 0.1);
        }

        .btn-login {
            width: 100%;
            background-color: #38B04A;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: bold;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-login:hover {
            background-color: #2d8f3a;
            transform: translateY(-1px);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .footer-text {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.8rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
        }

        .footer-text i {
            font-size: 0.7rem;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .test-credentials {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            margin-top: 20px;
        }

        .test-credentials h6 {
            color: #495057;
            margin-bottom: 10px;
            font-size: 0.85rem;
        }

        .test-credentials .credentials {
            display: flex;
            justify-content: space-between;
            font-size: 0.8rem;
        }

        .test-credentials .credentials .label {
            color: #6c757d;
            font-weight: 500;
        }

        .test-credentials .credentials .value {
            color: #495057;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Header Section -->
        <div class="header">
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <h1>Allo Mobile</h1>
            <p>Interface d'Administration</p>
        </div>

        <!-- Form Section -->
        <div class="form-section">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erreur de connexion !</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.authenticate') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-container">
                        <i class="fas fa-envelope"></i>
                        <input type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               id="email"
                               name="email"
                               value="{{ old('email', 'admin@admin.com') }}"
                               required
                               autofocus>
                    </div>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <div class="input-container">
                        <i class="fas fa-lock"></i>
                        <input type="password"
                               class="form-control @error('password') is-invalid @enderror"
                               id="password"
                               name="password"
                               required>
                    </div>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-arrow-right"></i>
                    SE CONNECTER
                </button>
            </form>

            <div class="footer-text">
                <i class="fas fa-shield-alt"></i>
                Accès réservé aux administrateurs
            </div>

            <!-- Test Credentials -->
            <div class="test-credentials">
                <h6><i class="fas fa-key me-2"></i>Identifiants de test :</h6>
                <div class="credentials">
                    <div>
                        <span class="label">Email:</span><br>
                        <span class="value">admin@admin.com</span>
                    </div>
                    <div>
                        <span class="label">Mot de passe:</span><br>
                        <span class="value">admin123</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus sur le champ mot de passe si email est rempli
        document.addEventListener('DOMContentLoaded', function() {
            const emailField = document.getElementById('email');
            const passwordField = document.getElementById('password');

            if (emailField.value) {
                passwordField.focus();
            }
        });
    </script>
</body>
</html>
