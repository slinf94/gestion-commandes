# 🚀 Guide - Accès Interface Admin Laravel

## ✅ **SERVEUR DÉMARRÉ AVEC SUCCÈS !**

---

## 🌐 **Accès à l'Interface Admin**

### **URL d'Accès :**
```
http://127.0.0.1:8000/admin
```
ou
```
http://localhost:8000/admin
```

---

## 🔑 **Identifiants de Connexion**

### **Administrateur Principal :**
- **Email** : `admin@allomobile.com`
- **Mot de passe** : `admin123`

### **Alternative :**
- **Email** : `admin@example.com`
- **Mot de passe** : `password`

---

## 📋 **Étapes d'Accès**

### **1. Ouvrir le Navigateur**
- Ouvrez Chrome, Firefox, ou Edge
- Tapez dans la barre d'adresse : `http://127.0.0.1:8000/admin`

### **2. Page de Connexion**
- Vous verrez la page de connexion admin
- Entrez vos identifiants

### **3. Interface Admin**
- Une fois connecté, vous aurez accès à :
  - Gestion des utilisateurs
  - Gestion des produits
  - Gestion des commandes
  - Gestion des catégories
  - Statistiques

---

## 🛠️ **En Cas de Problème**

### **Si le serveur ne répond pas :**

1. **Vérifier que le serveur fonctionne :**
   ```bash
   cd C:\Users\ASUS\Desktop\ProjetSlimat\gestion-commandes
   php artisan serve
   ```

2. **Vérifier le port :**
   ```bash
   netstat -ano | findstr :8000
   ```

3. **Redémarrer le serveur :**
   - Appuyez sur `Ctrl+C` pour arrêter
   - Relancez avec `php artisan serve`

### **Si vous ne pouvez pas vous connecter :**

1. **Vérifier les identifiants** dans la base de données
2. **Réinitialiser le mot de passe admin** si nécessaire

---

## 📱 **Fonctionnalités Admin Disponibles**

### **Gestion des Utilisateurs :**
- ✅ Voir tous les utilisateurs
- ✅ Activer/désactiver les comptes
- ✅ Modifier les informations utilisateur
- ✅ Envoyer des notifications d'activation

### **Gestion des Produits :**
- ✅ Ajouter de nouveaux produits
- ✅ Modifier les informations produits
- ✅ Gérer les stocks
- ✅ Upload d'images

### **Gestion des Commandes :**
- ✅ Voir toutes les commandes
- ✅ Changer le statut des commandes
- ✅ Gérer les livraisons

### **Gestion des Catégories :**
- ✅ Créer des catégories
- ✅ Modifier les catégories
- ✅ Organiser les produits

---

## 🎯 **URLs Importantes**

### **Interface Admin :**
- **Connexion** : `http://127.0.0.1:8000/admin`
- **Dashboard** : `http://127.0.0.1:8000/admin/dashboard`

### **API Mobile :**
- **Base URL** : `http://127.0.0.1:8000/api`
- **Login** : `http://127.0.0.1:8000/api/login`
- **Produits** : `http://127.0.0.1:8000/api/products`

### **Interface Web (si disponible) :**
- **Accueil** : `http://127.0.0.1:8000`

---

## ⚡ **Démarrage Rapide**

### **Pour démarrer le serveur :**
```bash
cd C:\Users\ASUS\Desktop\ProjetSlimat\gestion-commandes
php artisan serve
```

### **Pour accéder à l'admin :**
1. Ouvrir navigateur
2. Aller à : `http://127.0.0.1:8000/admin`
3. Se connecter avec : `admin@allomobile.com` / `admin123`

---

## 🔧 **Commandes Utiles**

### **Vérifier le statut :**
```bash
php artisan --version
```

### **Voir les routes :**
```bash
php artisan route:list
```

### **Nettoyer le cache :**
```bash
php artisan cache:clear
php artisan config:clear
```

### **Voir les logs :**
```bash
tail -f storage/logs/laravel.log
```

---

## 🎉 **Résultat**

**✅ Votre interface admin Laravel est maintenant accessible !**

**URL :** `http://127.0.0.1:8000/admin`
**Identifiants :** `admin@allomobile.com` / `admin123`

Vous pouvez maintenant gérer votre application mobile depuis l'interface web admin !
