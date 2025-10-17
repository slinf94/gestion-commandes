<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Allo Mobile - Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
        }
        .logo {
            font-size: 48px;
            margin-bottom: 20px;
        }
        .app-name {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            font-size: 18px;
            margin-bottom: 30px;
        }
        .message {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            border-left: 4px solid #4CAF50;
        }
        .message h3 {
            color: #4CAF50;
            margin-top: 0;
        }
        .download-section {
            margin: 30px 0;
        }
        .download-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .download-btn {
            background: #4CAF50;
            color: white;
            padding: 15px 25px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .download-btn:hover {
            background: #45a049;
            transform: translateY(-2px);
        }
        .instructions {
            background: #e3f2fd;
            padding: 20px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: left;
        }
        .instructions h4 {
            color: #1976d2;
            margin-top: 0;
        }
        .instructions ol {
            margin: 10px 0;
            padding-left: 20px;
        }
        .instructions li {
            margin: 8px 0;
            line-height: 1.5;
        }
        .qr-code {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border-radius: 10px;
            border: 2px dashed #ddd;
        }
        .qr-code img {
            max-width: 200px;
            height: auto;
        }
        .footer {
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        @media (max-width: 600px) {
            .container {
                margin: 20px;
                padding: 30px 20px;
            }
            .download-buttons {
                flex-direction: column;
                align-items: center;
            }
            .download-btn {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">📱</div>
        <div class="app-name">Allo Mobile</div>
        <div class="subtitle">Votre application de commandes en ligne</div>

        <div class="message">
            <h3>🎉 Votre compte est activé !</h3>
            <p>Vous pouvez maintenant télécharger et utiliser l'application Allo Mobile pour passer vos commandes.</p>
        </div>

        <div class="instructions">
            <h4>📲 Comment accéder à l'application :</h4>
            <ol>
                <li><strong>Téléchargez l'application</strong> depuis votre magasin d'applications</li>
                <li><strong>Ouvrez l'application</strong> sur votre téléphone</li>
                <li><strong>Connectez-vous</strong> avec vos identifiants</li>
                <li><strong>Commencez à commander</strong> vos produits favoris !</li>
            </ol>
        </div>

        <div class="download-section">
            <h4>📥 Télécharger l'application</h4>
            <div class="download-buttons">
                <a href="#" class="download-btn">
                    <span>🍎</span>
                    <span>App Store</span>
                </a>
                <a href="#" class="download-btn">
                    <span>🤖</span>
                    <span>Google Play</span>
                </a>
            </div>
        </div>

        <div class="qr-code">
            <p><strong>📱 Ou scannez ce QR code avec votre téléphone :</strong></p>
            <!-- QR Code sera généré ici -->
            <div style="width: 200px; height: 200px; background: #f0f0f0; margin: 20px auto; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #666;">
                QR Code<br>Application Mobile
            </div>
        </div>

        <div class="message">
            <h4>🔐 Vos informations de connexion :</h4>
            <p><strong>Email :</strong> {{ $user->email ?? 'votre@email.com' }}</p>
            <p><strong>Téléphone :</strong> {{ $user->numero_telephone ?? 'votre numéro' }}</p>
        </div>

        <div class="footer">
            <p>Merci de nous faire confiance !</p>
            <p>© {{ date('Y') }} Allo Mobile. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>
