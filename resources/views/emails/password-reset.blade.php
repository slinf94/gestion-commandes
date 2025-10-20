<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation de Mot de Passe - Allo Mobile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #FF6B35;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #FF6B35;
            margin-bottom: 10px;
        }
        .greeting {
            font-size: 18px;
            color: #333;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 25px;
        }
        .content p {
            margin-bottom: 15px;
            font-size: 16px;
        }
        .warning-box {
            background-color: #fff3cd;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #FF6B35;
        }
        .warning-box strong {
            color: #856404;
        }
        .cta-button {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button a {
            background-color: #FF6B35;
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 25px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
            transition: background-color 0.3s;
        }
        .cta-button a:hover {
            background-color: #e55a2b;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
            font-size: 14px;
        }
        .signature {
            margin-top: 25px;
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- En-tête personnalisé -->
        <div class="header">
            <div class="logo">📱 Allo Mobile</div>
            <h1 style="color: #FF6B35; margin: 0;">🔐 Réinitialisation de Mot de Passe</h1>
        </div>

        <!-- Salutation -->
        <div class="greeting">
            Bonjour <strong>{{ $notifiable->prenom ?? $notifiable->first_name }}</strong> !
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <p>Vous avez demandé la réinitialisation de votre mot de passe pour votre compte Allo Mobile.</p>

            <p>Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
        </div>

        <!-- Bouton d'action -->
        <div class="cta-button">
            <a href="{{ $resetUrl }}">🔑 Réinitialiser mon mot de passe</a>
        </div>

        <!-- Avertissement -->
        <div class="warning-box">
            <p><strong>⚠️ Important :</strong></p>
            <p>• Ce lien de réinitialisation expirera dans 60 minutes</p>
            <p>• Si vous n'avez pas demandé cette réinitialisation, ignorez cet email</p>
            <p>• Votre mot de passe actuel reste valide jusqu'à ce que vous le changiez</p>
        </div>

        <!-- Signature -->
        <div class="signature">
            Cordialement,<br>
            L'équipe Allo Mobile
        </div>

        <!-- Footer minimal -->
        <div class="footer">
            <p>© {{ date('Y') }} Allo Mobile. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
















