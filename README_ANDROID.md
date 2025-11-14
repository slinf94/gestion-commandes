# Configuration pour Android Physique

## 📱 Configuration de l'IP pour Android

L'application Flutter est configurée pour fonctionner sur un appareil Android physique.

### IP Actuelle
- **IP détectée**: `10.77.168.8`
- **URL Backend**: `http://10.77.168.8:8000`
- **URL API**: `http://10.77.168.8:8000/api/v1`

### Fichier de Configuration
Le fichier `gestion_commandes_mobile/lib/core/config/backend_config.dart` contient la configuration.

## 🚀 Démarrage du Serveur Laravel

### Option 1: Script automatique (Recommandé)
```bash
# Depuis le dossier gestion-commandes
start_server_android.bat
```

### Option 2: Commande manuelle
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

⚠️ **Important**: Utilisez `--host=0.0.0.0` pour permettre les connexions depuis d'autres appareils sur le réseau.

## 📋 Vérifications Requises

### 1. Même Réseau WiFi
- ✅ Votre ordinateur et votre téléphone Android doivent être sur le **même réseau WiFi**
- ✅ Vérifiez que les deux appareils peuvent se voir sur le réseau

### 2. Pare-feu Windows
Autorisez le port 8000 dans le pare-feu Windows:
```powershell
# Exécuter en tant qu'administrateur
New-NetFirewallRule -DisplayName "Laravel Dev Server" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow
```

Ou manuellement:
1. Ouvrir "Pare-feu Windows Defender"
2. Paramètres avancés
3. Règles de trafic entrant > Nouvelle règle
4. Port > TCP > 8000 > Autoriser la connexion

### 3. Test de Connexion
Testez depuis votre téléphone Android:
- Ouvrir le navigateur
- Aller à: `http://192.168.11.100:8000`
- Vous devriez voir la page Laravel

## 🔧 Changer l'IP

Si votre IP change (nouveau réseau WiFi), suivez ces étapes:

### Méthode 1: Script automatique
```powershell
cd gestion_commandes_mobile
powershell -ExecutionPolicy Bypass -File scripts/configure_android_ip.ps1
```

### Méthode 2: Manuellement
1. Trouver votre IP:
   ```bash
   ipconfig | findstr /i "IPv4"
   ```

2. Modifier `gestion_commandes_mobile/lib/core/config/backend_config.dart`:
   ```dart
   static const String baseHost = 'http://VOTRE_IP:8000';
   ```

3. Redémarrer l'application Flutter

## 🐛 Dépannage

### Erreur: "Connection refused" ou "Timeout"
- ✅ Vérifiez que le serveur Laravel est démarré avec `--host=0.0.0.0`
- ✅ Vérifiez que l'IP dans `backend_config.dart` correspond à votre IP actuelle
- ✅ Vérifiez que les deux appareils sont sur le même WiFi
- ✅ Vérifiez le pare-feu Windows

### Erreur: "Network unreachable"
- ✅ Vérifiez votre connexion WiFi
- ✅ Vérifiez que l'IP est correcte (pas 127.0.0.1)

### L'application ne se connecte pas
1. Vérifiez les logs Flutter: `flutter run -v`
2. Vérifiez les logs Laravel dans `storage/logs/laravel.log`
3. Testez l'API depuis le navigateur: `http://10.77.168.8:8000/api/v1/products`

## 📝 Notes

- Pour Chrome/Windows: Changez `baseHost` en `http://127.0.0.1:8000`
- Pour Android: Utilisez votre IP réseau (ex: `http://192.168.11.100:8000`)
- L'IP peut changer si vous changez de réseau WiFi

