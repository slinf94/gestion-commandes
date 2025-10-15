<?php

echo "🚀 PRÉPARATION POUR LA PRODUCTION - ALLO MOBILE\n";
echo "=" . str_repeat("=", 50) . "\n\n";

// Vérifications préalables
echo "📋 1. VÉRIFICATIONS PRÉALABLES\n";
echo "-" . str_repeat("-", 30) . "\n";

// Vérifier PHP version
$phpVersion = PHP_VERSION;
echo "✅ Version PHP: {$phpVersion}\n";
if (version_compare($phpVersion, '8.1.0', '<')) {
    echo "⚠️  ATTENTION: PHP 8.1+ recommandé pour Laravel\n";
}

// Vérifier extensions nécessaires
$requiredExtensions = ['pdo', 'pdo_mysql', 'mbstring', 'xml', 'openssl', 'curl', 'zip'];
$missingExtensions = [];

foreach ($requiredExtensions as $ext) {
    if (extension_loaded($ext)) {
        echo "✅ Extension {$ext}: Installée\n";
    } else {
        echo "❌ Extension {$ext}: Manquante\n";
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    echo "\n⚠️  Extensions manquantes: " . implode(', ', $missingExtensions) . "\n";
    echo "   Installez-les avant de continuer.\n\n";
}

// Vérifier Composer
echo "\n📦 2. VÉRIFICATION COMPOSER\n";
echo "-" . str_repeat("-", 30) . "\n";

if (file_exists('composer.phar') || shell_exec('which composer')) {
    echo "✅ Composer: Installé\n";
} else {
    echo "❌ Composer: Non installé\n";
    echo "   Installez Composer: https://getcomposer.org/\n";
}

// Vérifier Node.js
echo "\n🟢 3. VÉRIFICATION NODE.JS\n";
echo "-" . str_repeat("-", 30) . "\n";

$nodeVersion = shell_exec('node --version 2>/dev/null');
if ($nodeVersion) {
    echo "✅ Node.js: " . trim($nodeVersion) . "\n";
} else {
    echo "❌ Node.js: Non installé\n";
    echo "   Installez Node.js: https://nodejs.org/\n";
}

// Configuration de production
echo "\n⚙️ 4. GÉNÉRATION FICHIER .env PRODUCTION\n";
echo "-" . str_repeat("-", 30) . "\n";

$envProduction = 'APP_NAME="Allo Mobile"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://votre-domaine.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=allo_mobile_prod
DB_USERNAME=votre_user
DB_PASSWORD=votre_password_securise

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alnoreply48@gmail.com
MAIL_PASSWORD="votre_app_password"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=alnoreply48@gmail.com
MAIL_FROM_NAME="Allo Mobile"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"';

file_put_contents('.env.production', $envProduction);
echo "✅ Fichier .env.production créé\n";
echo "⚠️  IMPORTANT: Modifiez les valeurs selon votre configuration!\n";

// Scripts de déploiement
echo "\n📜 5. GÉNÉRATION SCRIPTS DE DÉPLOIEMENT\n";
echo "-" . str_repeat("-", 30) . "\n";

// Script de déploiement Laravel
$deployScript = '#!/bin/bash

echo "🚀 DÉPLOIEMENT ALLO MOBILE BACKEND"
echo "=================================="

# Arrêter les services si nécessaire
# sudo systemctl stop nginx
# sudo systemctl stop php8.1-fpm

# Sauvegarder la version actuelle
echo "📦 Sauvegarde de la version actuelle..."
sudo cp -r /var/www/allo-mobile /var/www/allo-mobile-backup-$(date +%Y%m%d_%H%M%S)

# Mettre à jour le code
echo "📥 Mise à jour du code..."
cd /var/www/allo-mobile
git pull origin main

# Installer les dépendances
echo "📦 Installation des dépendances..."
composer install --optimize-autoloader --no-dev

# Configuration
echo "⚙️ Configuration..."
cp .env.production .env
php artisan key:generate

# Migrations
echo "🗄️ Migrations de base de données..."
php artisan migrate --force

# Cache
echo "💾 Optimisation du cache..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
echo "🔐 Configuration des permissions..."
sudo chown -R www-data:www-data /var/www/allo-mobile
sudo chmod -R 755 /var/www/allo-mobile
sudo chmod -R 775 /var/www/allo-mobile/storage
sudo chmod -R 775 /var/www/allo-mobile/bootstrap/cache

# Redémarrer les services
echo "🔄 Redémarrage des services..."
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm

echo "✅ Déploiement terminé avec succès!"
echo "🌐 Site disponible sur: https://votre-domaine.com"';

file_put_contents('deploy.sh', $deployScript);
chmod('deploy.sh', 0755);
echo "✅ Script deploy.sh créé\n";

// Script de sauvegarde
$backupScript = '#!/bin/bash

echo "💾 SAUVEGARDE ALLO MOBILE"
echo "========================"

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/allo-mobile"
PROJECT_DIR="/var/www/allo-mobile"

# Créer le dossier de sauvegarde
mkdir -p $BACKUP_DIR

# Sauvegarde base de données
echo "🗄️ Sauvegarde base de données..."
mysqldump -u allo_user -p allo_mobile_prod > $BACKUP_DIR/database_$DATE.sql

# Sauvegarde fichiers
echo "📁 Sauvegarde fichiers..."
tar -czf $BACKUP_DIR/files_$DATE.tar.gz -C /var/www allo-mobile

# Nettoyage anciennes sauvegardes (plus de 7 jours)
echo "🧹 Nettoyage anciennes sauvegardes..."
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "✅ Sauvegarde terminée: $DATE"';

file_put_contents('backup.sh', $backupScript);
chmod('backup.sh', 0755);
echo "✅ Script backup.sh créé\n";

// Configuration Nginx
echo "\n🌐 6. CONFIGURATION NGINX\n";
echo "-" . str_repeat("-", 30) . "\n";

$nginxConfig = 'server {
    listen 80;
    server_name votre-domaine.com www.votre-domaine.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name votre-domaine.com www.votre-domaine.com;
    root /var/www/allo-mobile/public;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/votre-domaine.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/votre-domaine.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src \'self\' http: https: data: blob: \'unsafe-inline\'" always;

    index index.php;
    charset utf-8;

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate auth;
    gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}';

file_put_contents('nginx.conf', $nginxConfig);
echo "✅ Configuration nginx.conf créée\n";

// Checklist de déploiement
echo "\n📋 7. CHECKLIST DE DÉPLOIEMENT\n";
echo "-" . str_repeat("-", 30) . "\n";

$checklist = [
    "☐ Acheter un domaine (ex: allomobile.com)",
    "☐ Configurer un serveur VPS (Ubuntu 20.04+)",
    "☐ Installer LAMP Stack (Linux, Apache/Nginx, MySQL, PHP)",
    "☐ Configurer SSL avec Let's Encrypt",
    "☐ Créer la base de données MySQL",
    "☐ Uploader le code Laravel",
    "☐ Configurer le fichier .env",
    "☐ Exécuter les migrations",
    "☐ Tester l'API",
    "☐ Configurer les sauvegardes automatiques",
    "☐ Mettre à jour l'URL API dans l'app mobile",
    "☐ Générer la clé de signature Android",
    "☐ Build de l'application mobile",
    "☐ Upload sur Google Play Store",
    "☐ Tests de validation",
    "☐ Mise en production"
];

foreach ($checklist as $item) {
    echo $item . "\n";
}

echo "\n💰 8. COÛTS ESTIMATIFS\n";
echo "-" . str_repeat("-", 30) . "\n";
echo "💳 Domaine: $10-15/an\n";
echo "🖥️ VPS: $5-20/mois\n";
echo "🔒 SSL: Gratuit (Let's Encrypt)\n";
echo "📱 Google Play: $25 (unique)\n";
echo "📊 TOTAL: ~$85-275/an\n";

echo "\n🎯 RÉSUMÉ\n";
echo "=" . str_repeat("=", 50) . "\n";
echo "✅ Fichiers de configuration générés\n";
echo "✅ Scripts de déploiement créés\n";
echo "✅ Configuration Nginx prête\n";
echo "✅ Checklist de déploiement fournie\n";
echo "✅ Coûts estimés calculés\n\n";

echo "📖 Consultez le fichier DEPLOYMENT_GUIDE.md pour les détails complets\n";
echo "🚀 Votre application est prête pour le déploiement!\n\n";

?>
