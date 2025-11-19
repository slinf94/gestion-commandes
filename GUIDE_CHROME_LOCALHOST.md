# 🌐 GUIDE : DÉMARRER LE SERVEUR POUR CHROME

## ✅ CONFIGURATION APPLIQUÉE

L'IP réseau a été **désactivée** et remplacée par **localhost** pour Chrome.

### Fichiers modifiés :
- ✅ `gestion_commandes_mobile/lib/core/config/backend_config.dart`
  - IP changée : `http://192.168.100.73:8000` → `http://127.0.0.1:8000`

### Scripts créés :
- ✅ `demarrer_serveur_chrome.ps1` - Script PowerShell pour Chrome
- ✅ `demarrer_serveur_localhost.bat` - Script Batch pour Chrome

---

## 🚀 DÉMARRER LE SERVEUR POUR CHROME

### Option 1 : Script PowerShell (Recommandé)

```powershell
cd gestion-commandes
.\demarrer_serveur_chrome.ps1
```

### Option 2 : Script Batch

Double-cliquez sur :
```
gestion-commandes\demarrer_serveur_localhost.bat
```

### Option 3 : Commande manuelle

```powershell
cd gestion-commandes
php artisan serve --host=127.0.0.1 --port=8000
```

---

## 🌐 ACCÉDER À L'APPLICATION DANS CHROME

Une fois le serveur démarré, ouvrez Chrome et allez sur :

```
http://localhost:8000
```

ou

```
http://127.0.0.1:8000
```

---

## ⚠️ IMPORTANT

### Pour Chrome/Navigateur :
- ✅ Utilisez : `http://127.0.0.1:8000` (localhost)
- ✅ Commande : `php artisan serve --host=127.0.0.1 --port=8000`

### Pour Application Mobile :
- ⚠️ Vous devrez changer l'IP dans `backend_config.dart` vers votre IP réseau
- ⚠️ Utilisez : `php artisan serve --host=0.0.0.0 --port=8000`
- ⚠️ Utilisez le script : `demarrer_serveur_reseau.ps1`

---

## 🔍 VÉRIFIER QUE LE SERVEUR TOURNE

Dans PowerShell :
```powershell
netstat -an | findstr "127.0.0.1:8000"
```

Vous devriez voir :
```
TCP    127.0.0.1:8000         0.0.0.0:0              LISTENING
```

---

## ❌ SI LE SERVEUR NE DÉMARRE PAS

1. **Vérifier que le port 8000 est libre** :
   ```powershell
   netstat -an | findstr ":8000"
   ```

2. **Arrêter les processus PHP existants** :
   ```powershell
   Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
   ```

3. **Vérifier que PHP est installé** :
   ```powershell
   php -v
   ```

4. **Vérifier que Laravel est dans le bon répertoire** :
   ```powershell
   cd gestion-commandes
   php artisan --version
   ```

---

## 📋 RÉSUMÉ

✅ **IP réseau désactivée**  
✅ **Configuration localhost activée**  
✅ **Scripts de démarrage créés**  
✅ **Serveur prêt pour Chrome**

**Prochaine étape** : Démarrer le serveur avec `demarrer_serveur_chrome.ps1` et ouvrir Chrome sur `http://localhost:8000` 🚀





