# 🚀 GUIDE DE DÉPLOIEMENT - ALLO MOBILE

## 📋 TABLE DES MATIÈRES
1. [Préparation Backend Laravel](#backend-preparation)
2. [Hébergement Backend](#backend-hosting)
3. [Configuration Base de Données](#database-config)
4. [Déploiement Application Mobile](#mobile-deployment)
5. [Configuration Production](#production-config)
6. [Tests et Validation](#testing-validation)

---

## 🛠️ BACKEND PREPARATION {#backend-preparation}

### 1. Optimisation du Code Laravel

#### A. Configuration Production
```bash
# Fichier .env pour production
APP_NAME="Allo Mobile"
APP_ENV=production
APP_KEY=base64:VOTRE_CLE_GENEREE
APP_DEBUG=false
APP_URL=https://votre-domaine.com

# Base de données production
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=allo_mobile_prod
DB_USERNAME=votre_user
DB_PASSWORD=votre_password_securise

# Configuration Email Production
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=alnoreply48@gmail.com
MAIL_PASSWORD="votre_app_password"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=alnoreply48@gmail.com
MAIL_FROM_NAME="Allo Mobile"
```

#### B. Optimisations Laravel
```bash
# Installation des dépendances
composer install --optimize-autoloader --no-dev

# Cache de configuration
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache

# Optimisation de l'autoloader
composer dump-autoload --optimize
```

### 2. Sécurisation

#### A. Permissions fichiers
```bash
# Permissions sécurisées
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 644 .env
```

#### B. Configuration serveur web
```nginx
# Configuration Nginx pour Laravel
server {
    listen 80;
    server_name votre-domaine.com;
    root /var/www/allo-mobile/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

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
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

---

## 🌐 HÉBERGEMENT BACKEND {#backend-hosting}

### Option 1: Serveur VPS/Dédié (Recommandé)

#### A. Serveurs recommandés
- **DigitalOcean**: $5-20/mois
- **Linode**: $5-20/mois  
- **Vultr**: $3.50-20/mois
- **OVH**: €3-15/mois
- **Scaleway**: €3-15/mois

#### B. Configuration serveur
```bash
# Ubuntu 20.04/22.04 LTS
sudo apt update && sudo apt upgrade -y

# Installation LAMP Stack
sudo apt install nginx mysql-server php8.1-fpm php8.1-mysql php8.1-xml php8.1-mbstring php8.1-curl php8.1-zip unzip -y

# Installation Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Installation Node.js (pour les assets)
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt-get install -y nodejs
```

#### C. Déploiement du code
```bash
# Cloner le projet
git clone https://github.com/votre-repo/allo-mobile-backend.git
cd allo-mobile-backend

# Installer les dépendances
composer install --optimize-autoloader --no-dev

# Configuration
cp .env.example .env
nano .env  # Configurer pour production

# Générer la clé
php artisan key:generate

# Migrations et seeds
php artisan migrate --force
php artisan db:seed --force

# Optimisations
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Option 2: Hébergement partagé

#### A. Hébergeurs compatibles Laravel
- **Hostinger**: €2-8/mois
- **IONOS**: €1-10/mois
- **OVH**: €3-15/mois
- **PlanetHoster**: €5-20/mois

#### B. Upload du code
1. **Zipper le projet** (sans vendor/, node_modules/, .git/)
2. **Upload via FTP/cPanel**
3. **Extraire dans le dossier public_html**
4. **Configurer .env**
5. **Installer via Composer** (si disponible)

---

## 🗄️ CONFIGURATION BASE DE DONNÉES {#database-config}

### 1. MySQL/MariaDB

#### A. Création base de données
```sql
CREATE DATABASE allo_mobile_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'allo_user'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
GRANT ALL PRIVILEGES ON allo_mobile_prod.* TO 'allo_user'@'localhost';
FLUSH PRIVILEGES;
```

#### B. Import des données
```bash
# Export depuis développement
mysqldump -u root -p allo_mobile_dev > backup.sql

# Import en production
mysql -u allo_user -p allo_mobile_prod < backup.sql
```

### 2. Sauvegarde automatique
```bash
#!/bin/bash
# Script de sauvegarde quotidienne
DATE=$(date +%Y%m%d_%H%M%S)
mysqldump -u allo_user -p allo_mobile_prod > /backups/allo_mobile_$DATE.sql
find /backups -name "*.sql" -mtime +7 -delete
```

---

## 📱 DÉPLOIEMENT APPLICATION MOBILE {#mobile-deployment}

### 1. Configuration pour Production

#### A. Mise à jour API URL
```dart
// lib/core/services/api_service.dart
class ApiService {
  static const String baseUrl = 'https://votre-domaine.com/api';
  // static const String baseUrl = 'http://localhost:8000/api'; // DEV
}
```

#### B. Configuration de build
```yaml
# android/app/build.gradle
android {
    compileSdkVersion 34
    
    defaultConfig {
        applicationId "com.allomobile.app"
        minSdkVersion 21
        targetSdkVersion 34
        versionCode 1
        versionName "1.0.0"
    }
    
    signingConfigs {
        release {
            storeFile file('upload-keystore.jks')
            storePassword 'votre_store_password'
            keyAlias 'upload'
            keyPassword 'votre_key_password'
        }
    }
    
    buildTypes {
        release {
            signingConfig signingConfigs.release
            minifyEnabled true
            shrinkResources true
            proguardFiles getDefaultProguardFile('proguard-android.txt'), 'proguard-rules.pro'
        }
    }
}
```

### 2. Génération de la clé de signature

#### A. Création du keystore
```bash
keytool -genkey -v -keystore upload-keystore.jks -keyalg RSA -keysize 2048 -validity 10000 -alias upload
```

#### B. Configuration key.properties
```properties
# android/key.properties
storePassword=votre_store_password
keyPassword=votre_key_password
keyAlias=upload
storeFile=../upload-keystore.jks
```

### 3. Build de production

#### A. Build APK
```bash
# Dans le dossier gestion_commandes_mobile
flutter clean
flutter pub get
flutter build apk --release
```

#### B. Build App Bundle (recommandé pour Google Play)
```bash
flutter build appbundle --release
```

### 4. Distribution

#### Option A: Google Play Store
1. **Créer compte développeur** ($25 unique)
2. **Préparer les assets** (icônes, screenshots, description)
3. **Upload du AAB** (Android App Bundle)
4. **Configuration store listing**
5. **Publication en production**

#### Option B: Distribution directe (APK)
1. **Héberger l'APK** sur votre site
2. **QR Code** pour téléchargement
3. **Instructions d'installation**

---

## ⚙️ CONFIGURATION PRODUCTION {#production-config}

### 1. SSL/HTTPS (Obligatoire)

#### A. Certificat Let's Encrypt (Gratuit)
```bash
# Installation Certbot
sudo apt install certbot python3-certbot-nginx

# Génération certificat
sudo certbot --nginx -d votre-domaine.com

# Renouvellement automatique
sudo crontab -e
# Ajouter: 0 12 * * * /usr/bin/certbot renew --quiet
```

### 2. Monitoring et logs

#### A. Logs Laravel
```bash
# Configuration logs
tail -f storage/logs/laravel.log

# Rotation des logs
sudo nano /etc/logrotate.d/laravel
```

#### B. Monitoring serveur
```bash
# Installation htop pour monitoring
sudo apt install htop

# Surveillance espace disque
df -h
```

### 3. Sécurité

#### A. Firewall
```bash
# Configuration UFW
sudo ufw enable
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw status
```

#### B. Mise à jour système
```bash
# Mise à jour automatique
sudo apt install unattended-upgrades
sudo dpkg-reconfigure unattended-upgrades
```

---

## 🧪 TESTS ET VALIDATION {#testing-validation}

### 1. Tests Backend

#### A. Tests API
```bash
# Test endpoints principaux
curl -X GET https://votre-domaine.com/api/products
curl -X POST https://votre-domaine.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password"}'
```

#### B. Tests de charge
```bash
# Installation Apache Bench
sudo apt install apache2-utils

# Test de charge
ab -n 100 -c 10 https://votre-domaine.com/api/products
```

### 2. Tests Application Mobile

#### A. Tests sur différents appareils
- **Émulateurs Android** (différentes versions)
- **Appareils physiques** (Android 5.0+)
- **Tests de performance**

#### B. Tests de fonctionnalités
- ✅ **Connexion/Déconnexion**
- ✅ **Inscription/Activation**
- ✅ **Navigation**
- ✅ **Commandes**
- ✅ **Notifications push**

---

## 💰 COÛTS ESTIMATIFS

### Hébergement Backend
- **VPS**: $5-20/mois
- **Domaine**: $10-15/an
- **SSL**: Gratuit (Let's Encrypt)

### Application Mobile
- **Google Play Store**: $25 (unique)
- **Certificat de signature**: Gratuit (auto-généré)

### **TOTAL**: ~$85-275/an

---

## 🚀 ÉTAPES DE DÉPLOIEMENT

### Phase 1: Backend (1-2 jours)
1. ✅ **Choisir hébergeur**
2. ✅ **Configurer serveur**
3. ✅ **Déployer code Laravel**
4. ✅ **Configurer base de données**
5. ✅ **Tester API**

### Phase 2: Mobile (1-2 jours)
1. ✅ **Configurer production**
2. ✅ **Générer keystore**
3. ✅ **Build APK/AAB**
4. ✅ **Tests sur appareils**
5. ✅ **Upload sur store**

### Phase 3: Validation (1 jour)
1. ✅ **Tests complets**
2. ✅ **Monitoring**
3. ✅ **Documentation**
4. ✅ **Formation utilisateurs**

---

## 📞 SUPPORT ET MAINTENANCE

### Monitoring continu
- **Uptime**: 99.9%
- **Logs**: Surveillance quotidienne
- **Sauvegardes**: Automatiques
- **Mises à jour**: Mensuelles

### Support technique
- **Documentation**: Complète
- **Formation**: Équipe
- **Hotline**: Disponible
- **Maintenance**: Préventive

---

**🎯 VOTRE APPLICATION SERA PRÊTE POUR LA PRODUCTION !**

Pour toute question ou assistance, n'hésitez pas à me contacter.
