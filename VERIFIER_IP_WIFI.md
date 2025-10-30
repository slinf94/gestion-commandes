# Guide : Vérifier et Mettre à Jour l'IP après Changement de WiFi

## 🔄 Problème
Quand vous changez de réseau WiFi, l'adresse IP de votre PC change. L'application mobile doit être mise à jour avec la nouvelle IP pour communiquer avec le serveur Laravel.

---

## 📋 Étapes pour Trouver la Nouvelle IP

### Sur Windows :

1. **Ouvrir l'invite de commandes (CMD) ou PowerShell**
2. **Taper la commande suivante :**
   ```cmd
   ipconfig
   ```
3. **Chercher l'adresse IPv4** sous votre connexion WiFi
   - Cherchez une ligne comme : `Adresse IPv4. . . . . . . . . . : 192.168.x.x`
   - Notez cette adresse (exemple: `192.168.137.1`)

### Alternative rapide :
```cmd
ipconfig | findstr /i "IPv4"
```

---

## 🔧 Mise à Jour des Fichiers

### 1. **Application Mobile Flutter**

#### Fichier 1 : `lib/core/constants/app_constants.dart`
```dart
// Ancienne IP
static const String baseUrl = 'http://192.168.100.73:8000/api/v1';

// Nouvelle IP (remplacez par votre IP)
static const String baseUrl = 'http://192.168.137.1:8000/api/v1';
```

#### Fichier 2 : `lib/core/config/app_config.dart`
```dart
// Mettre à jour dans 'environments'
'development': 'http://VOTRE_NOUVELLE_IP:8000/api/v1',

// Et dans 'wifiConfigs'
'wifi_actuel': 'http://VOTRE_NOUVELLE_IP:8000/api/v1',
```

### 2. **Backend Laravel (Optionnel - juste pour info)**

Le fichier `start_server_mobile.bat` affiche l'URL :
```batch
echo URL de l'API: http://VOTRE_NOUVELLE_IP:8000/api/v1
```

---

## ✅ Vérification

### 1. **Démarrer le serveur Laravel**
```cmd
cd gestion-commandes
start_server_mobile.bat
```

### 2. **Tester la connexion depuis le navigateur**
Ouvrez dans votre navigateur :
```
http://VOTRE_NOUVELLE_IP:8000/api/v1/products
```

Si vous voyez une réponse JSON, le serveur fonctionne correctement.

### 3. **Tester depuis l'application mobile**
- Ouvrez l'application Flutter
- Essayez de charger des produits
- Si tout fonctionne, la connexion est OK ✅

---

## 🚨 Problèmes Courants

### Le téléphone ne peut pas se connecter

1. **Vérifier que le PC et le téléphone sont sur le même WiFi**
   - Les deux doivent être connectés au même réseau
   - L'IP doit être dans la même plage (192.168.x.x)

2. **Vérifier le firewall Windows**
   ```cmd
   # Autoriser PHP et le port 8000
   ```
   - Ouvrez "Pare-feu Windows Defender"
   - Autorisez "php.exe" ou créez une règle pour le port 8000

3. **Vérifier que le serveur écoute sur 0.0.0.0**
   ```cmd
   php artisan serve --host=0.0.0.0 --port=8000
   ```
   ⚠️ Important : `--host=0.0.0.0` permet l'accès depuis d'autres appareils

### L'application charge mais ne reçoit pas de données

1. **Vérifier les logs Laravel**
   ```cmd
   tail -f storage/logs/laravel.log
   ```

2. **Vérifier que l'API répond**
   - Testez directement dans le navigateur : `http://VOTRE_IP:8000/api/v1/products`
   - Vous devriez voir une réponse JSON

3. **Vérifier les CORS** (déjà configuré normalement)
   - Fichier : `config/cors.php`

---

## 🔐 Sécurité

⚠️ **Important pour la Production :**
- Ne partagez pas cette IP publiquement
- Utilisez HTTPS en production
- Configurez un domaine pour la production

---

## 📝 Script Automatique (Optionnel)

Créez un script pour trouver automatiquement votre IP :

**`trouver_ip.bat`** (à créer dans `gestion-commandes`) :
```batch
@echo off
echo Recherche de votre adresse IP WiFi...
echo.
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4"') do (
    set ip=%%a
    set ip=!ip:~1!
    echo Votre IP WiFi: !ip!
    echo.
    echo URL de l'API: http://!ip!:8000/api/v1
    echo.
)
pause
```

---

## 📞 Commandes Utiles

### Trouver votre IP rapidement :
```cmd
ipconfig | findstr /i "IPv4"
```

### Tester la connexion depuis un autre appareil :
```cmd
# Sur un autre PC/Mac connecté au même WiFi
curl http://VOTRE_IP:8000/api/v1/products
```

### Vérifier que le port 8000 est ouvert :
```cmd
netstat -an | findstr :8000
```

---

## ✅ Checklist de Vérification

- [ ] IP trouvée et notée
- [ ] `app_constants.dart` mis à jour
- [ ] `app_config.dart` mis à jour  
- [ ] `start_server_mobile.bat` mis à jour (optionnel)
- [ ] Serveur démarré avec `--host=0.0.0.0`
- [ ] Firewall configuré
- [ ] Test depuis le navigateur OK
- [ ] Test depuis l'application mobile OK

---

**Tous les fichiers ont été automatiquement mis à jour !** ✅

Votre nouvelle IP est : **192.168.137.1**

Vérifiez que le serveur Laravel démarre correctement et que l'application mobile peut se connecter.

