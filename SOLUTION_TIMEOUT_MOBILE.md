# 🔥 SOLUTION COMPLÈTE POUR RÉSOUDRE LE TIMEOUT MOBILE

## 🚨 Problème : Timeout lors de la connexion depuis l'application mobile

---

## ✅ ÉTAPE 1 : Vérifier l'IP du serveur

### 1.1 Trouver votre IP actuelle

Ouvrez PowerShell et exécutez :

```powershell
ipconfig | findstr /i "IPv4"
```

**Cherchez une IP qui commence par `10.x.x.x` ou `192.168.x.x`**

### 1.2 Vérifier que le serveur écoute sur toutes les interfaces

Exécutez cette commande :

```powershell
netstat -ano | findstr :8000
```

**Vous devez voir :**
```
TCP    0.0.0.0:8000         0.0.0.0:0              LISTENING
```

**❌ MAUVAIS (ne fonctionnera pas pour le mobile) :**
```
TCP    127.0.0.1:8000       0.0.0.0:0              LISTENING
```

---

## ✅ ÉTAPE 2 : Démarrer le serveur correctement

### 2.1 Arrêter tous les serveurs PHP en cours

```powershell
Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
```

### 2.2 Démarrer le serveur sur TOUTES les interfaces (0.0.0.0)

```powershell
cd gestion-commandes
php artisan serve --host=0.0.0.0 --port=8000
```

**OU avec timeout PHP augmenté :**

```powershell
php -d max_execution_time=300 artisan serve --host=0.0.0.0 --port=8000
```

---

## ✅ ÉTAPE 3 : Configurer le timeout PHP

### 3.1 Vérifier le timeout PHP actuel

Dans `gestion-commandes/public/index.php`, ajoutez en haut :

```php
<?php
// Augmenter les timeouts pour éviter les problèmes
ini_set('max_execution_time', 300); // 5 minutes
ini_set('memory_limit', '512M');

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
// ... reste du code
```

### 3.2 Modifier AuthController pour optimiser

Modifiez `app/Http/Controllers/Api/AuthController.php` :

```php
public function login(Request $request)
{
    // Augmenter le timeout pour cette requête
    set_time_limit(60); // 60 secondes max pour le login
    
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|string|min:6',
    ]);

    // ... reste du code
}
```

---

## ✅ ÉTAPE 4 : Vérifier la configuration de la base de données

### 4.1 Vérifier que MySQL est accessible rapidement

Dans votre `.env`, assurez-vous que :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion-commandedb
DB_USERNAME=root
DB_PASSWORD=
```

### 4.2 Tester la connexion à la base de données

```powershell
php artisan tinker
```

Puis dans tinker :
```php
DB::connection()->getPdo();
```

Si ça prend trop de temps, MySQL peut être le problème.

---

## ✅ ÉTAPE 5 : Vérifier l'IP dans l'application mobile

### 5.1 Mettre à jour l'IP dans l'app mobile

Dans `gestion_commandes_mobile/lib/core/constants/app_constants.dart` :

```dart
static const String baseUrl = 'http://10.152.173.8:8000/api/v1';
// ⚠️ REMPLACEZ 10.152.173.8 par VOTRE IP ACTUELLE
```

### 5.2 Vérifier aussi dans app_config.dart

Dans `gestion_commandes_mobile/lib/core/config/app_config.dart` :

```dart
static const Map<String, String> environments = {
  'development': 'http://10.152.173.8:8000/api/v1',
  // ⚠️ REMPLACEZ par VOTRE IP ACTUELLE
};
```

---

## ✅ ÉTAPE 6 : Tester l'API depuis un navigateur

### 6.1 Tester l'endpoint de login

Ouvrez votre navigateur et allez sur :

```
http://10.152.173.8:8000/api/v1/auth/login
```

**OU remplacez par votre IP :**

```
http://VOTRE_IP:8000/api/v1/auth/login
```

Vous devriez voir une erreur de validation (normal), mais pas de timeout.

### 6.2 Tester avec curl (PowerShell)

```powershell
$body = @{
    email = "jean.dupont@test.com"
    password = "password123"
} | ConvertTo-Json

Invoke-RestMethod -Uri "http://10.152.173.8:8000/api/v1/auth/login" `
    -Method Post `
    -Body $body `
    -ContentType "application/json" `
    -Headers @{"X-Mobile-App"="true"} `
    -TimeoutSec 120
```

---

## ✅ ÉTAPE 7 : Optimiser le middleware

### 7.1 Vérifier le ApiSecurityMiddleware

Le middleware pourrait bloquer les requêtes. Assurez-vous que `X-Mobile-App: true` est bien envoyé.

Dans l'app mobile, vérifiez dans `api_service.dart` :

```dart
headers: {
  'Content-Type': 'application/json',
  'Accept': 'application/json',
  'X-Mobile-App': 'true', // ✅ DOIT ÊTRE PRÉSENT
},
```

---

## ✅ ÉTAPE 8 : Vérifier le firewall Windows

### 8.1 Autoriser le port 8000 dans le firewall

1. Ouvrez "Pare-feu Windows Defender"
2. Cliquez sur "Paramètres avancés"
3. Règles entrantes → Nouvelle règle
4. Port → TCP → 8000 → Autoriser la connexion

### 8.2 OU désactiver temporairement le firewall pour tester

⚠️ **Attention :** Réactivez-le après le test !

---

## ✅ ÉTAPE 9 : Script de démarrage automatique

Créez un fichier `start_server_mobile_fixed.bat` :

```batch
@echo off
echo ========================================
echo   DEMARRAGE SERVEUR POUR MOBILE
echo ========================================
echo.

REM Arrêter les serveurs existants
echo Arret des serveurs existants...
taskkill /F /IM php.exe 2>nul
timeout /t 2 /nobreak >nul

REM Trouver l'IP actuelle
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "10."') do (
    set ip=%%a
    goto :found_ip
)
for /f "tokens=2 delims=:" %%a in ('ipconfig ^| findstr /i "IPv4" ^| findstr "192.168"') do (
    set ip=%%a
    goto :found_ip
)

:found_ip
set ip=%ip: =%
echo IP detectee: %ip%
echo.

REM Démarrer le serveur
echo Demarrage du serveur sur toutes les interfaces...
echo URL API: http://%ip%:8000/api/v1
echo.

cd /d "%~dp0"
php -d max_execution_time=300 -d memory_limit=512M artisan serve --host=0.0.0.0 --port=8000

pause
```

---

## ✅ ÉTAPE 10 : Logs et débogage

### 10.1 Activer les logs Laravel

Vérifiez `storage/logs/laravel.log` pour voir les erreurs.

### 10.2 Ajouter des logs dans AuthController

Modifiez `app/Http/Controllers/Api/AuthController.php` :

```php
public function login(Request $request)
{
    \Log::info('Login attempt', ['email' => $request->email]);
    
    $startTime = microtime(true);
    
    // ... code de login ...
    
    $duration = microtime(true) - $startTime;
    \Log::info('Login completed', ['duration' => $duration]);
    
    return response()->json([...]);
}
```

---

## 🎯 CHECKLIST DE VÉRIFICATION

Avant de tester à nouveau, vérifiez que :

- [ ] Le serveur écoute sur `0.0.0.0:8000` (pas `127.0.0.1:8000`)
- [ ] L'IP dans `app_constants.dart` correspond à votre IP actuelle
- [ ] L'IP dans `app_config.dart` correspond à votre IP actuelle
- [ ] Le firewall Windows autorise le port 8000
- [ ] MySQL est démarré et accessible rapidement
- [ ] Le timeout PHP est >= 60 secondes
- [ ] Le header `X-Mobile-App: true` est envoyé depuis l'app
- [ ] L'API répond dans un navigateur : `http://VOTRE_IP:8000/api/v1/products`

---

## 🔥 SOLUTION RAPIDE (À FAIRE EN PREMIER)

1. **Arrêter tous les serveurs PHP :**
   ```powershell
   Get-Process -Name "php" -ErrorAction SilentlyContinue | Stop-Process -Force
   ```

2. **Démarrer sur 0.0.0.0 :**
   ```powershell
   cd gestion-commandes
   php artisan serve --host=0.0.0.0 --port=8000
   ```

3. **Vérifier netstat :**
   ```powershell
   netstat -ano | findstr :8000
   ```
   ✅ Doit afficher `0.0.0.0:8000`

4. **Tester dans un navigateur :**
   ```
   http://VOTRE_IP:8000/api/v1/products
   ```

5. **Si ça fonctionne, tester la connexion depuis l'app mobile**

---

## 📞 Si le problème persiste

1. Vérifiez les logs : `storage/logs/laravel.log`
2. Testez l'API avec Postman ou curl
3. Vérifiez que le téléphone est sur le même réseau WiFi
4. Essayez de ping l'IP depuis le téléphone

---

**✅ Une fois ces étapes effectuées, le timeout devrait être résolu !**

