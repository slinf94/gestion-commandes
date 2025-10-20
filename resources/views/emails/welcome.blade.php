<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bienvenue - Allo Mobile</title>
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
            border-bottom: 2px solid #4CAF50;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4CAF50;
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
        .welcome-box {
            background-color: #e8f5e8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .features {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .features ul {
            margin: 0;
            padding-left: 20px;
        }
        .features li {
            margin-bottom: 8px;
            font-size: 15px;
        }
        .cta-button {
            text-align: center;
            margin: 30px 0;
        }
        .cta-button a {
            background-color: #4CAF50;
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
            background-color: #45a049;
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
            <h1 style="color: #4CAF50; margin: 0;">🎉 Bienvenue !</h1>
        </div>

        <!-- Salutation -->
        <div class="greeting">
            Bonjour <strong>{{ $user->prenom }} {{ $user->nom }}</strong> !
        </div>

        <!-- Contenu principal -->
        <div class="content">
            <div class="welcome-box">
                <p><strong>🎊 Félicitations !</strong></p>
                <p>Votre compte Allo Mobile a été créé avec succès. Nous sommes ravis de vous accueillir dans notre communauté !</p>
            </div>

            <p>Découvrez tous les services que nous vous proposons :</p>

            <div class="features">
                <ul>
                    <li>🛍️ Parcourir notre catalogue de produits</li>
                    <li>❤️ Ajouter des produits à vos favoris</li>
                    <li>🛒 Passer des commandes en toute simplicité</li>
                    <li>📦 Suivre l'état de vos commandes en temps réel</li>
                    <li>💬 Contacter notre service client</li>
                </ul>
            </div>

            <p><strong>📱 Informations de connexion :</strong></p>
            <p>Email : <strong>{{ $user->email }}</strong></p>
            <p>Téléphone : <strong>{{ $user->numero_telephone }}</strong></p>
        </div>

        <!-- Bouton d'action -->
        <div class="cta-button">
            <a href="{{ url('/') }}">🚀 Commencer mes achats</a>
        </div>

        <!-- Message de remerciement -->
        <div class="content">
            <p>Merci de nous faire confiance et bienvenue dans la famille Allo Mobile !</p>
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
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
















