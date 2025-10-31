# 👥 Identifiants des Clients de Test

Voici tous les identifiants des clients de test déjà créés dans votre application.

---

## 🔵 Clients de Test (CreateTestClientsSeeder)

Ces clients sont créés avec le seeder `CreateTestClientsSeeder`.
**Mot de passe pour tous :** `password123`

| # | Nom | Prénom | Email | Téléphone | Quartier |
|---|-----|--------|-------|-----------|----------|
| 1 | Dupont | Jean | `jean.dupont@test.com` | +237651234567 | Akwa |
| 2 | Martin | Marie | `marie.martin@test.com` | +237652345678 | Bonanjo |
| 3 | Kouam | Achille | `achille.kouam@test.com` | +237653456789 | Makepe |
| 4 | Ndi | Sophie | `sophie.ndi@test.com` | +237654567890 | New-Bell |
| 5 | Tchoupo | Pierre | `pierre.tchoupo@test.com` | +237655678901 | Bali |
| 6 | Nkem | Claire | `claire.nkem@test.com` | +237656789012 | Deïdo |
| 7 | Ngouo | Franck | `franck.ngouo@test.com` | +237657890123 | Pk8 |
| 8 | Mbang | Emilie | `emilie.mbang@test.com` | +237658901234 | Logpom |

---

## 🔵 Clients de Test Slimat/Dra (CreateTestUsersSlimatDraSeeder)

Ces clients sont créés avec le seeder `CreateTestUsersSlimatDraSeeder`.
**Mot de passe pour tous :** `password123`

| # | Nom | Prénom | Email | Téléphone | Quartier |
|---|-----|--------|-------|-----------|----------|
| 1 | Slimat | Test | `slimat@test.com` | +226 70 11 22 33 | Secteur 30 |
| 2 | Dra | Test | `dra@test.com` | +226 70 44 55 66 | Secteur 30 |

---

## 🔵 Client de Test (AddMissingUserSeeder)

Ce client est créé avec le seeder `AddMissingUserSeeder`.
**Mot de passe :** `password123`

| Nom | Prénom | Email | Téléphone |
|-----|--------|-------|-----------|
| Moukouls | Soumatao | `moukoulssoumatao@gmail.com` | +22612345678 |

---

## 📋 Résumé Rapide

### **Pour tous les clients de test :**
- **Mot de passe :** `password123`
- **Rôle :** `client`
- **Statut :** `active`

### **Exemples d'utilisation :**

1. **Client 1 :** 
   - Email : `jean.dupont@test.com`
   - Password : `password123`

2. **Client 2 :**
   - Email : `slimat@test.com`
   - Password : `password123`

3. **Client 3 :**
   - Email : `moukoulssoumatao@gmail.com`
   - Password : `password123`

---

## 🔧 Créer ces clients si nécessaire

Si vous n'avez pas encore créé ces clients, exécutez ces commandes :

```powershell
# Créer les 8 clients de test (CreateTestClientsSeeder)
php artisan db:seed --class=CreateTestClientsSeeder

# Créer les clients Slimat et Dra
php artisan db:seed --class=CreateTestUsersSlimatDraSeeder

# Créer le client Moukouls Soumatao
php artisan db:seed --class=AddMissingUserSeeder
```

---

## 📱 Connexion via l'Application Mobile

Ces identifiants peuvent être utilisés pour se connecter via l'API mobile de l'application.

**Endpoint de connexion :** `/api/auth/login`

**Format de la requête :**
```json
{
  "email": "jean.dupont@test.com",
  "password": "password123"
}
```

---

## ⚠️ Notes Importantes

- Ces comptes sont destinés uniquement aux **tests**
- Le mot de passe `password123` est faible, ne l'utilisez pas en production
- Tous ces comptes ont le statut `active`, donc ils peuvent se connecter immédiatement
- Les comptes sont déjà vérifiés (`email_verified_at` est défini)

