# 🔧 Dépannage : Timeout de Connexion

## ❌ Problème Identifié

L'application mobile affiche **"Timeout de connexion"** lors de la tentative de connexion.

---

## 🔍 Cause Racine

Le serveur Laravel écoute actuellement sur `127.0.0.1:8000` (localhost uniquement) au lieu de `0.0.0.0:8000` (toutes les interfaces réseau).

**Cela signifie :**
- ✅ Le serveur est accessible depuis le PC
- ❌ Le serveur N'EST PAS accessible depuis le téléphone mobile
- ❌ Les requêtes depuis l'app mobile ne peuvent pas atteindre le serveur

---

## ✅ Solution : Redémarrer le Serveur Correctement

### Option 1 : Utiliser le Script Automatique (Recommandé)

```cmd
cd gestion-commandes
start_server_mobile.bat
```

Ce script démarre automatiquement le serveur avec `--host=0.0.0.0`

### Option 2 : Démarrer Manuellement

1. **Arrêter le serveur actuel** (Ctrl+C dans le terminal)

2. **Redémarrer avec la bonne commande :**
   ```cmd
   php artisan serve --host=0.0.0.0 --port=8000
   ```

   ⚠️ **IMPORTANT :** `--host=0.0.0.0` est obligatoire pour permettre l'accès depuis d'autres appareils !

---

## 🔍 Vérification

### Vérifier que le serveur écoute correctement :

```cmd
netstat -an | findstr :8000
```

**Résultat attendu :**
```
TCP    0.0.0.0:8000           0.0.0.0:0              LISTENING
```

**Si vous voyez :**
```
TCP    127.0.0.1:8000         0.0.0.0:0              LISTENING
```

❌ Le serveur n'est pas accessible depuis le réseau. Redémarrez-le avec `--host=0.0.0.0`

---

## 🧪 Test Rapide

### Test 1 : Depuis le navigateur (PC)
```
http://192.168.137.1:8000/api/v1/products
```

### Test 2 : Test avec curl (PC)
```cmd
curl http://192.168.137.1:8000/api/v1/products -H "X-Mobile-App: true"
```

### Test 3 : Depuis l'application mobile
1. Redémarrer l'application Flutter complètement
2. Essayer de se connecter avec `dra@test.com` / `password123`

---

## 📋 Checklist de Résolution

- [ ] Serveur Laravel arrêté
- [ ] Serveur redémarré avec `--host=0.0.0.0 --port=8000`
- [ ] Vérification `netstat` montre `0.0.0.0:8000`
- [ ] Test navigateur OK (192.168.137.1:8000)
- [ ] Application Flutter redémarrée
- [ ] Test connexion mobile OK

---

## 🛠️ Autres Causes Possibles

Si le timeout persiste après avoir corrigé le serveur :

### 1. Firewall Windows
- Autoriser PHP dans le pare-feu
- Créer une règle pour le port 8000

### 2. Même Réseau WiFi
- Vérifier que PC et téléphone sont sur le même WiFi
- Vérifier que l'IP est correcte (192.168.137.1)

### 3. Timeout Trop Court
Les timeouts actuels sont :
- Connection: 60 secondes
- Receive: 120 secondes

Ces valeurs sont suffisantes. Si le problème persiste, c'est probablement un problème de connexion réseau.

---

## 🔐 Informations de Test

Utilisateurs créés pour tester :
- **Email :** `dra@test.com` / Mot de passe : `password123`
- **Email :** `slimat@test.com` / Mot de passe : `password123`

---

**Une fois le serveur redémarré correctement, l'application mobile devrait se connecter sans problème !** ✅

