<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Accès Non Autorisé | Allo Mobile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
        }

        /* Particules animées en arrière-plan */
        .particles {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 15s infinite ease-in-out;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Conteneur principal */
        .error-container {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Carte principale */
        .error-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: scaleIn 0.6s ease-out;
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.9);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Numéro d'erreur */
        .error-code {
            font-size: 120px;
            font-weight: 900;
            background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 20px;
            line-height: 1;
            animation: pulse 2s ease-in-out infinite;
            position: relative;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        .error-code::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%);
            border-radius: 2px;
        }

        /* Icône */
        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin-bottom: 30px;
            animation: shake 0.5s ease-in-out infinite alternate;
        }

        @keyframes shake {
            0% {
                transform: rotate(-5deg);
            }
            100% {
                transform: rotate(5deg);
            }
        }

        /* Titre */
        .error-title {
            font-size: 32px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            animation: slideIn 0.8s ease-out 0.2s both;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Message */
        .error-message {
            font-size: 18px;
            color: #7f8c8d;
            margin-bottom: 40px;
            line-height: 1.6;
            animation: slideIn 0.8s ease-out 0.4s both;
        }

        /* Boutons */
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            animation: slideIn 0.8s ease-out 0.6s both;
        }

        .btn-modern {
            padding: 14px 32px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 50px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-modern:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #38B04A 0%, #2d8f3a 100%);
            color: white;
        }

        .btn-primary-modern:hover {
            background: linear-gradient(135deg, #2d8f3a 0%, #38B04A 100%);
        }

        .btn-secondary-modern {
            background: white;
            color: #38B04A;
            border: 2px solid #38B04A;
        }

        .btn-secondary-modern:hover {
            background: #38B04A;
            color: white;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .error-code {
                font-size: 80px;
            }

            .error-icon {
                font-size: 60px;
            }

            .error-title {
                font-size: 24px;
            }

            .error-message {
                font-size: 16px;
            }

            .error-card {
                padding: 40px 30px;
            }

            .btn-modern {
                padding: 12px 24px;
                font-size: 14px;
            }
        }

        /* Effet de brillance */
        .shimmer {
            position: relative;
            overflow: hidden;
        }

        .shimmer::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Particules animées -->
    <div class="particles" id="particles"></div>

    <!-- Conteneur principal -->
    <div class="error-container">
        <div class="error-card shimmer">
            <!-- Icône -->
            <div class="error-icon">
                <i class="fas fa-shield-alt"></i>
            </div>

            <!-- Code d'erreur -->
            <div class="error-code">403</div>

            <!-- Titre -->
            <h1 class="error-title">Accès Non Autorisé</h1>

            <!-- Message -->
            <p class="error-message">
                Désolé, vous n'avez pas les permissions nécessaires pour accéder à cette ressource.
                <br>
                Veuillez contacter votre administrateur si vous pensez que c'est une erreur.
            </p>

            <!-- Actions -->
            <div class="error-actions">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="btn-modern btn-primary-modern">
                        <i class="fas fa-home"></i>
                        Retour au Tableau de Bord
                    </a>
                @else
                    <a href="{{ route('admin.login') }}" class="btn-modern btn-primary-modern">
                        <i class="fas fa-sign-in-alt"></i>
                        Se Connecter
                    </a>
                @endauth
                <a href="javascript:history.back()" class="btn-modern btn-secondary-modern">
                    <i class="fas fa-arrow-left"></i>
                    Page Précédente
                </a>
            </div>
        </div>
    </div>

    <script>
        // Créer les particules animées
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 20;

            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                const size = Math.random() * 8 + 4;
                const startX = Math.random() * 100;
                const delay = Math.random() * 15;
                const duration = Math.random() * 10 + 10;
                
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = startX + '%';
                particle.style.animationDelay = delay + 's';
                particle.style.animationDuration = duration + 's';
                
                particlesContainer.appendChild(particle);
            }
        }

        // Initialiser les particules au chargement
        document.addEventListener('DOMContentLoaded', createParticles);

        // Animation au survol des boutons
        document.querySelectorAll('.btn-modern').forEach(btn => {
            btn.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-3px) scale(1.05)';
            });
            
            btn.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    </script>
</body>
</html>

