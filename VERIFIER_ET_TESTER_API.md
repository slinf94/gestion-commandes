# 🔍 VÉRIFICATION ET TEST DE LA COMMUNICATION API MOBILE

## 📋 INFORMATIONS ACTUELLES

- **IP du serveur** : `10.193.46.8`
- **Port** : `8000`
- **URL API** : `http://10.193.46.8:8000/api/v1`
- **Status serveur** : ✅ Écoute sur `0.0.0.0:8000` (accessible depuis le réseau)

## ✅ TESTS EFFECTUÉS

### 1. Test de connectivité de base
```powershell
# Test depuis PowerShell
Invoke-WebRequest -Uri "http://10.193.46.8:8000/api/ping" -Method GET
```

### 2. Test de l'endpoint API
```powershell
# Test endpoint API
Invoke-WebRequest -Uri "http://10.193.46.8:8000/api/v1/ping" -Method GET
```

### 3. Test CORS
```powershell
# Test CORS (requête OPTIONS)
$headers = @{
    'Origin' = 'http://localhost'
    'Access-Control-Request-Method' = 'POST'
}
Invoke-WebRequest -Uri "http://10.193.46.8:8000/api/v1/ping" -Method OPTIONS -Headers $headers
```

## 🔧 CORRECTIONS APPLIQUÉES

### 1. Timeout PHP
- ✅ `max_execution_time` : 300 secondes (5 minutes)
- ✅ `memory_limit` : 512MB
- ✅ `ignore_user_abort(true)` : Pour continuer même si le client se déconnecte

### 2. Notification asynchrone
- ✅ Les notifications admin sont envoyées en arrière-plan
- ✅ N'affecte plus le temps de réponse

### 3. CORS configuré
- ✅ Headers CORS optimisés
- ✅ Support des requêtes OPTIONS (preflight)

### 4. Routes de test
- ✅ `/api/ping` : Test de connectivité
- ✅ `/api/v1/ping` : Test API

## 📱 CONFIGURATION APPLICATION MOBILE

### Fichiers à modifier

**1. `gestion_commandes_mobile/lib/core/constants/app_constants.dart`**

```dart
// AVANT
static const String baseUrl = 'http://10.152.173.8:8000/api/v1';

// APRÈS
static const String baseUrl = 'http://10.193.46.8:8000/api/v1';
```

**2. `gestion_commandes_mobile/lib/core/config/app_config.dart`**

```dart
// AVANT
'development': 'http://10.152.173.8:8000/api/v1',

// APRÈS
'development': 'http://10.193.46.8:8000/api/v1',
```

## 🚀 DÉMARRAGE DU SERVEUR

### Option 1 : Script automatique
```bash
start_server_mobile.bat
```

### Option 2 : Commande manuelle
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

**Important** : Utilisez `--host=0.0.0.0` pour permettre l'accès depuis le réseau local.

## 🔥 CONFIGURATION FIREWALL

### Ajouter une règle pour le port 8000

```powershell
# Exécuter en tant qu'administrateur
netsh advfirewall firewall add rule name="Laravel API Port 8000" dir=in action=allow protocol=TCP localport=8000
```

## 🧪 TEST MANUEL

### Test 1 : Depuis le navigateur
Ouvrir : `http://10.193.46.8:8000/api/v1/ping`

Résultat attendu :
```json
{
  "success": true,
  "message": "API is running",
  "timestamp": "2025-01-31T...",
  "server_ip": "10.193.46.8"
}
```

### Test 2 : Depuis PowerShell
```powershell
Invoke-WebRequest -Uri "http://10.193.46.8:8000/api/v1/ping" -Method GET | Select-Object -ExpandProperty Content
```

### Test 3 : Test d'inscription (simulation)
```powershell
$body = @{
    nom = "Test"
    prenom = "User"
    email = "test@example.com"
    numero_telephone = "123456789"
    quartier = "Test"
    password = "password123"
    password_confirmation = "password123"
} | ConvertTo-Json

Invoke-WebRequest -Uri "http://10.193.46.8:8000/api/v1/auth/register" -Method POST -Body $body -ContentType "application/json"
```

## 📊 VÉRIFICATION DES LOGS

### Consulter les logs en temps réel
```powershell
Get-Content storage\logs\laravel.log -Tail 50 -Wait
```

### Rechercher les erreurs de timeout
```powershell
Select-String -Path storage\logs\laravel.log -Pattern "timeout|TIMEOUT|Timeout" -Context 2,2
```

## ⚠️ PROBLÈMES COURANTS ET SOLUTIONS

### Problème 1 : Timeout de connexion
**Cause** : IP incorrecte dans l'application mobile
**Solution** : Mettre à jour l'IP dans les fichiers de configuration

### Problème 2 : Serveur inaccessible
**Cause** : Serveur non démarré ou firewall bloquant
**Solution** : 
1. Démarrer le serveur avec `--host=0.0.0.0`
2. Vérifier le firewall Windows

### Problème 3 : CORS erreur
**Cause** : Headers CORS manquants
**Solution** : Vérifier que le middleware CORS est actif dans `bootstrap/app.php`

### Problème 4 : Timeout PHP
**Cause** : Script trop long
**Solution** : Les timeouts sont déjà configurés à 300 secondes

## ✅ CHECKLIST FINALE

- [ ] IP mise à jour dans l'application mobile (`10.193.46.8`)
- [ ] Serveur démarré avec `--host=0.0.0.0 --port=8000`
- [ ] Firewall configuré pour le port 8000
- [ ] Test `/api/v1/ping` fonctionne
- [ ] Application mobile redémarrée
- [ ] Test de création de compte depuis l'application mobile

## 📞 SUPPORT

Si le problème persiste après toutes ces vérifications :

1. Vérifier les logs : `storage/logs/laravel.log`
2. Vérifier que le téléphone et le PC sont sur le même réseau WiFi
3. Tester avec l'outil `test_api_connection.ps1` :
   ```powershell
   powershell -ExecutionPolicy Bypass -File test_api_connection.ps1
   ```

