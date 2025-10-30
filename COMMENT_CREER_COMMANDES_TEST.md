# 📦 Guide : Créer des Commandes de Test

Ce guide vous explique comment créer des commandes de test pour tester votre système de gestion de commandes.

## 🎯 Objectif

Créer des commandes de test avec différents statuts pour pouvoir :
- ✅ Tester l'application mobile (création de commandes par les clients)
- ✅ Tester l'interface admin (validation et gestion des commandes)
- ✅ Voir les différents statuts de commandes en action

## 🚀 Méthodes de Création

### Méthode 1 : Commande Artisan (RECOMMANDÉE)

La façon la plus simple et rapide :

```bash
# Créer 15 commandes de test (par défaut)
php artisan orders:create-test

# Créer un nombre spécifique de commandes
php artisan orders:create-test --count=20

# Créer des clients ET des commandes
php artisan orders:create-test --count=10 --clients
```

### Méthode 2 : Utiliser les Seeders Directement

```bash
# 1. D'abord, créer des clients de test (si nécessaire)
php artisan db:seed --class=CreateTestClientsSeeder

# 2. Ensuite, créer les commandes
php artisan db:seed --class=OrderTestSeeder
```

## 📋 Prérequis

Avant de créer des commandes, assurez-vous d'avoir :

1. ✅ **Des clients actifs** dans la base de données
   - Si aucun client n'existe, utilisez : `php artisan orders:create-test --clients`
   - Ou : `php artisan db:seed --class=CreateTestClientsSeeder`

2. ✅ **Des produits actifs avec stock** dans la base de données
   - Les commandes nécessitent des produits disponibles
   - Créez des produits via l'interface admin ou les seeders existants

## 🎲 Ce qui sera créé

Les commandes de test incluent :

### Statuts variés
- ⏳ **En attente** (pending)
- ✅ **Confirmée** (confirmed)
- ⚙️ **En cours de traitement** (processing)
- 🚚 **Expédiée** (shipped)
- 🎉 **Livrée** (delivered)
- ❌ **Annulée** (cancelled)

### Données réalistes
- Clients aléatoires (parmi les clients actifs)
- 1 à 4 produits par commande
- Quantités aléatoires (selon le stock disponible)
- Totaux calculés automatiquement
- Adresses de livraison réalistes
- Dates de création variées (30 derniers jours)
- Notes client et admin aléatoires
- Historique des statuts

## 📊 Résultats

Après l'exécution, vous verrez :
- ✅ Le nombre de commandes créées
- ✅ Les détails de chaque commande (ID, client, statut, total)
- ✅ Les statistiques par statut

## 🧪 Comment Tester

### 1. Depuis l'Interface Admin

```
1. Allez sur : /admin/orders
2. Vous verrez toutes les commandes créées
3. Filtrez par statut pour tester différents cas
4. Cliquez sur une commande pour voir les détails
5. Testez le changement de statut
```

### 2. Depuis l'Application Mobile

```
1. Connectez-vous avec un compte client de test
2. Les commandes créées seront visibles dans l'historique
3. Créez de nouvelles commandes depuis l'app
4. Testez le flux complet : création → validation
```

## 📧 Comptes Clients de Test

Si vous utilisez `--clients`, voici les comptes créés :

| Email | Mot de passe | Nom |
|-------|-------------|-----|
| jean.dupont@test.com | password123 | Jean Dupont |
| marie.martin@test.com | password123 | Marie Martin |
| achille.kouam@test.com | password123 | Achille Kouam |
| sophie.ndi@test.com | password123 | Sophie Ndi |
| pierre.tchoupo@test.com | password123 | Pierre Tchoupo |
| claire.nkem@test.com | password123 | Claire Nkem |
| franck.ngouo@test.com | password123 | Franck Ngouo |
| emilie.mbang@test.com | password123 | Emilie Mbang |

## ⚠️ Notes Importantes

1. **Stock des produits** : Par défaut, le seeder **NE DIMINUE PAS** le stock réel des produits pour éviter de vider votre stock de test. Si vous voulez simuler vraiment, modifiez le seeder.

2. **Commandes existantes** : Si vous exécutez plusieurs fois, de nouvelles commandes seront ajoutées. Les anciennes ne sont pas supprimées.

3. **Validation** : Les commandes créées sont des commandes réelles dans la base de données. Vous pouvez les valider, modifier leur statut, etc.

## 🔄 Supprimer les Commandes de Test

Si vous voulez supprimer toutes les commandes de test :

```bash
# Dans tinker
php artisan tinker
>>> App\Models\Order::truncate();
>>> App\Models\OrderItem::truncate();
>>> App\Models\OrderStatusHistory::truncate();
```

⚠️ **ATTENTION** : Cela supprimera TOUTES les commandes, pas seulement celles de test !

## 📝 Fichiers Créés

- `database/seeders/OrderTestSeeder.php` - Seeder principal pour les commandes
- `database/seeders/CreateTestClientsSeeder.php` - Seeder pour les clients de test
- `app/Console/Commands/CreateTestOrdersCommand.php` - Commande artisan

## 🎉 Bon Test !

Une fois les commandes créées, vous pouvez tester :
- ✅ L'affichage des commandes dans l'admin
- ✅ Le changement de statut
- ✅ Les filtres et recherches
- ✅ L'historique des statuts
- ✅ La validation depuis l'app mobile

Bon courage ! 🚀

