# 🌐 Configuration des URLs d'Accès

## ⚠️ IMPORTANT : 0.0.0.0 n'est PAS accessible !

**`0.0.0.0`** est une adresse spéciale utilisée par le serveur pour **écouter sur toutes les interfaces réseau**. Ce n'est **PAS** une adresse accessible depuis un navigateur web.

---

## ✅ URLs Correctes pour Accéder à l'Application

### 📱 Pour le Navigateur (depuis votre PC)

**Option 1 - Localhost (recommandé) :**
```
http://127.0.0.1:8000/admin
```
OU
```
http://localhost:8000/admin
```

**Option 2 - IP Réseau :**
```
http://10.152.173.8:8000/admin
```

---

### 📱 Pour l'Application Mobile

L'application mobile utilise automatiquement :
```
http://10.152.173.8:8000/api/v1
```

Cette configuration est déjà faite dans :
- `gestion_commandes_mobile/lib/core/constants/app_constants.dart`
- `gestion_commandes_mobile/lib/core/config/app_config.dart`

---

## 🔍 Pages Principales de l'Admin

### Connexion
```
http://127.0.0.1:8000/admin/login
```

### Tableau de Bord
```
http://127.0.0.1:8000/admin/dashboard
```

### Utilisateurs
```
http://127.0.0.1:8000/admin/users
```

### Commandes
```
http://127.0.0.1:8000/admin/orders
```

### Produits
```
http://127.0.0.1:8000/admin/products
```

### Catégories
```
http://127.0.0.1:8000/admin/categories
```

---

## 📝 Explication Technique

### Adresses IP Explicitées

| Adresse | Usage | Accessible Depuis |
|---------|-------|-------------------|
| **0.0.0.0** | Écoute serveur (toutes interfaces) | ❌ Non (adresse technique) |
| **127.0.0.1** | Localhost (PC uniquement) | ✅ Oui (PC local) |
| **localhost** | Alias de 127.0.0.1 | ✅ Oui (PC local) |
| **10.152.173.8** | IP réseau WiFi | ✅ Oui (PC + réseau local) |

### Pourquoi `--host=0.0.0.0` ?

Le paramètre `--host=0.0.0.0` permet au serveur d'écouter sur **toutes les interfaces réseau**, ce qui rend l'API accessible depuis :
- Le PC (via 127.0.0.1 ou l'IP réseau)
- D'autres appareils sur le même réseau (via l'IP réseau)

**Mais vous ne pouvez PAS vous connecter directement à `0.0.0.0` !**

---

## 🎯 Raccourci Recommandé

### Pour le Navigateur

**Utilisez toujours :**
```
http://127.0.0.1:8000/admin
```

C'est le plus simple et ça fonctionne toujours depuis votre PC ! ✅

### Pour Créer un Raccourci

Vous pouvez créer un raccourci sur votre bureau avec cette URL.

---

## 🔧 Si l'IP Change

Si votre IP WiFi change :

1. **Trouver la nouvelle IP :**
   ```cmd
   ipconfig | findstr /i "IPv4"
   ```

2. **Mettre à jour l'application mobile :**
   - `gestion_commandes_mobile/lib/core/constants/app_constants.dart`
   - `gestion_commandes_mobile/lib/core/config/app_config.dart`

3. **Utiliser la nouvelle IP dans l'URL :**
   ```
   http://NOUVELLE_IP:8000/admin
   ```

**Mais pour le navigateur, `127.0.0.1` fonctionnera toujours !**

---

## ✅ Résumé

- ✅ **Navigateur PC :** Utilisez `http://127.0.0.1:8000/admin`
- ✅ **Application Mobile :** Utilise `http://10.152.173.8:8000/api/v1` (configuré automatiquement)
- ❌ **Ne jamais utiliser :** `http://0.0.0.0:8000` (c'est une erreur)

---

**Utilisez `http://127.0.0.1:8000/admin` pour accéder à l'interface d'administration !** ✅

