# 👥 Utilisateurs de Test - Slimat et Dra

## ✅ Création Réussie

Deux utilisateurs de test ont été créés avec succès dans la base de données.

---

## 📋 Utilisateur 1 : Slimat

### Informations de Connexion :
- **👤 Nom complet :** Test Slimat
- **📧 Email :** `slimat@test.com`
- **🔑 Mot de passe :** `password123`
- **📱 Téléphone :** +226 70 11 22 33
- **👥 Rôle :** `client`
- **✅ Statut :** `active`
- **🆔 ID :** 7

### Connexion :
```
Email: slimat@test.com
Mot de passe: password123
```

---

## 📋 Utilisateur 2 : Dra

### Informations de Connexion :
- **👤 Nom complet :** Test Dra
- **📧 Email :** `dra@test.com`
- **🔑 Mot de passe :** `password123`
- **📱 Téléphone :** +226 70 44 55 66
- **👥 Rôle :** `client`
- **✅ Statut :** `active`
- **🆔 ID :** 8

### Connexion :
```
Email: dra@test.com
Mot de passe: password123
```

---

## 🔐 Notes Importantes

### Mot de Passe
- Les deux utilisateurs utilisent le même mot de passe temporaire : `password123`
- ⚠️ **Important :** Changez ces mots de passe en production !

### Rôle
- Les deux utilisateurs ont le rôle `client`
- Ils peuvent :
  - Se connecter à l'application mobile
  - Créer des commandes
  - Voir leurs commandes
  - Gérer leur profil

### Statut
- Les deux utilisateurs ont le statut `active`
- Ils peuvent se connecter immédiatement

---

## 🧪 Utilisation

### Connexion depuis l'Application Mobile

1. **Ouvrez l'application Flutter**
2. **Allez à l'écran de connexion**
3. **Utilisez l'un des emails ci-dessus :**
   - `slimat@test.com` avec le mot de passe `password123`
   - OU `dra@test.com` avec le mot de passe `password123`

### Connexion depuis l'Interface Admin

Vous pouvez aussi vous connecter en tant qu'administrateur et voir ces utilisateurs :
- URL : `http://192.168.137.1:8000/admin/users`
- Recherchez par email : `slimat@test.com` ou `dra@test.com`

---

## 🔄 Recréer les Utilisateurs

Si vous devez recréer ces utilisateurs (par exemple après avoir supprimé la base), exécutez :

```bash
php artisan db:seed --class=CreateTestUsersSlimatDraSeeder
```

⚠️ **Note :** Si les utilisateurs existent déjà, le seeder ne les recréera pas pour éviter les doublons.

---

## 📝 Seeder Créé

Un seeder personnalisé a été créé : `database/seeders/CreateTestUsersSlimatDraSeeder.php`

Vous pouvez modifier ce seeder pour changer les informations des utilisateurs de test.

---

## ✅ Statut de Création

✅ **Utilisateur 'slimat' :** Créé avec succès (ID: 7)  
✅ **Utilisateur 'dra' :** Créé avec succès (ID: 8)

---

**Les deux utilisateurs sont prêts à être utilisés pour tester l'application !** 🎉

