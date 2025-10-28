# 📋 UTILISATEURS DE TEST - IDENTIFIANTS

## ✅ RÔLES ASSIGNÉS AVEC SUCCÈS

Les rôles ont été assignés aux utilisateurs existants. Voici les identifiants de connexion:

---

### 👑 SUPER ADMINISTRATEUR

**Email:** `super@admin.com`  
**Mot de passe:** `password`  
**Rôle assigné:** ✅ super-admin  
**Capacités:** Accès complet à toutes les fonctionnalités

---

### 👔 ADMINISTRATEUR

**Email:** `admin@test.com`  
**Mot de passe:** `password`  
**Rôle assigné:** ✅ admin  
**Capacités:** Gestion complète sauf paramètres système

---

### 📊 GESTIONNAIRE

**Email:** `gestionnaire@test.com`  
**Mot de passe:** `password`  
**Rôle assigné:** ✅ gestionnaire  
**Capacités:** Gestion des produits, catégories et commandes

---

### 💼 VENDEUR

**Email:** `vendeur@test.com`  
**Mot de passe:** `password`  
**Rôle assigné:** ✅ vendeur  
**Capacités:** Voir les produits et gérer les commandes uniquement

---

## 🔗 URL DE CONNEXION

**URL:** `http://127.0.0.1:8000/admin/login`

---

## 🧪 COMMENT TESTER

1. Aller sur `http://127.0.0.1:8000/admin/login`
2. Se connecter avec l'un des identifiants ci-dessus
3. Vérifier que le menu de la sidebar correspond au rôle
4. Vérifier les permissions d'accès

### Test VENDEUR (pour vérifier les restrictions):

- ✅ Doit voir: Tableau de bord, Produits, Commandes
- ❌ Ne doit PAS voir: Utilisateurs, Catégories, Attributs, Types de Produits, Clients, Journal des Activités

### Test SUPER ADMIN:

- ✅ Doit voir: TOUT
- ✅ Doit pouvoir accéder à tous les modules

---

## 📝 NOTE IMPORTANTE

Les comptes utilisateurs existent déjà dans la base de données. Les rôles ont été réassignés correctement. Vous pouvez maintenant vous connecter avec n'importe lequel de ces comptes pour tester les permissions.

