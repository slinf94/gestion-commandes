#!/bin/bash

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
echo "🌐 Site disponible sur: https://votre-domaine.com"