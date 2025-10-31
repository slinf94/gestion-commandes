# 🔐 Guide d'Accès à l'Interface Admin

## 📍 URL de Connexion

L'interface d'administration est accessible à l'adresse suivante :

```
http://localhost:8000/admin/login
```

OU si vous utilisez un serveur configuré :

```
http://votre-serveur:8000/admin/login
```

La page d'accueil (`/`) redirige automatiquement vers la page de connexion admin.

---

## 🔑 Créer un Compte Administrateur

Vous avez **3 méthodes** pour créer un compte administrateur :

### **Méthode 1 : Utiliser le Seeder (Recommandé)**

Exécutez cette commande dans votre terminal :

```bash
cd gestion-commandes
php artisan db:seed --class=AdminUserSeeder
```

**Identifiants créés :**
- **Email :** `admin@allomobile.com`
- **Mot de passe :** `admin123`

### **Méthode 2 : Utiliser la Commande Artisan**

Créez un compte admin personnalisé :

```bash
php artisan admin:create
```

La commande vous demandera :
- Nom complet
- Email
- Mot de passe (min 6 caractères)
- Numéro de téléphone (optionnel)

**Exemple complet :**
```bash
php artisan admin:create --name="Admin Principal" --email="admin@example.com" --password="votreMotDePasse123" --phone="+22670123456"
```

### **Méthode 3 : Autres Seeders Disponibles**

```bash
# Option A : Créer admin@monprojet.com
php artisan db:seed --class=UserSeeder

# Option B : Créer admin@admin.com
php artisan db:seed --class=FixAdminUserSeeder
```

---

## ✅ Étapes de Connexion

1. **Ouvrez votre navigateur** et allez sur `http://localhost:8000/admin/login`

2. **Entrez vos identifiants :**
   - Email : (celui créé via le seeder ou la commande)
   - Mot de passe : (celui défini)

3. **Cliquez sur "SE CONNECTER"**

4. **Après connexion réussie**, vous serez redirigé vers le dashboard admin (`/admin`)

---

## 🔒 Rôles Autorisés

Pour accéder à l'interface admin, votre compte doit avoir l'un de ces rôles :
- `super-admin`
- `admin`
- `gestionnaire`
- `vendeur`

Les comptes avec le rôle `client` ne peuvent **pas** accéder à l'interface admin.

---

## 🚨 Problèmes Courants

### **Erreur : "Accès non autorisé"**
- Vérifiez que votre compte a bien le rôle `admin` ou un des rôles autorisés
- Vérifiez que le statut du compte est `active`

### **Erreur : "Les identifiants ne correspondent pas"**
- Vérifiez que vous utilisez le bon email
- Réinitialisez le mot de passe avec le seeder :
  ```bash
  php artisan db:seed --class=AdminUserSeeder
  ```

### **Erreur 404 - Page non trouvée**
- Vérifiez que le serveur Laravel est démarré :
  ```bash
  php artisan serve
  ```
- Vérifiez que vous utilisez le bon port (généralement 8000)

---

## 📱 Vérifier si le Serveur est Démarré

Pour démarrer le serveur Laravel, exécutez :

```bash
cd gestion-commandes
php artisan serve
```

Le serveur sera accessible sur `http://localhost:8000`

---

## 🎯 Actions Après Connexion

Une fois connecté, vous aurez accès à :
- **Dashboard** : `/admin` - Vue d'ensemble avec statistiques
- **Utilisateurs** : `/admin/users` - Gestion des utilisateurs
- **Produits** : `/admin/products` - Gestion des produits
- **Commandes** : `/admin/orders` - Gestion des commandes
- **Profil** : `/admin/profile` - Modifier votre profil

---

## 💡 Conseil

Si vous oubliez vos identifiants, utilisez simplement le seeder pour les réinitialiser :

```bash
php artisan db:seed --class=AdminUserSeeder
```

Cela réinitialisera le mot de passe de l'admin existant à `admin123`.

