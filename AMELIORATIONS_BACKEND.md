# 🚀 Améliorations Backend - Gestion Commandes

## ✅ Modifications Effectuées

### 1. **Table Images pour Produits** ✅
- **Table** : `product_images`
- **Colonnes** :
  - `id` : Identifiant unique
  - `product_id` : Référence au produit
  - `url` : URL de l'image
  - `type` : Type d'image (principale, secondaire, galerie)
  - `order` : Ordre d'affichage
  - `alt_text` : Texte alternatif pour SEO

- **Modèle** : `App\Models\ProductImage`
- **Relations** :
  - Un produit peut avoir plusieurs images
  - Récupération automatique de l'image principale via `$product->main_image`
  - Récupération de toutes les images via `$product->all_images`

### 2. **Prix en Gros et Détail** ✅
- **Nouvelles colonnes dans `products`** :
  - `wholesale_price` : Prix en gros (decimal 10,2)
  - `retail_price` : Prix détail (decimal 10,2)
  - `min_wholesale_quantity` : Quantité minimum pour prix en gros (default: 10)

- **Logique** :
  - Si quantité >= `min_wholesale_quantity` → Prix en gros
  - Sinon → Prix détail ou prix normal

### 3. **Quartiers de Ouagadougou** ✅
- **Fichier** : `config/quartiers.php`
- **Total** : 78 quartiers de Ouagadougou
- **Colonne** : `quartier` dans table `users` (string 100)
- **Utilisation** :
  - Sélection du quartier lors de l'inscription
  - Filtrage des utilisateurs par quartier dans l'admin
  - Statistiques par quartier

**Liste des quartiers** :
```
Bilbalogo, Saint Léon, Zangouettin, Tiedpalogo, Koulouba, Kamsonghin, Samandin, 
Gounghin Sud, Gandin, Kouritenga, Mankoudougou, Paspanga, Ouidi, Larlé, 
Kologh Naba, Dapoya II, Nemnin, Niogsin, Hamdalaye, Gounghin Nord, Baoghin,
Camp militaire, Naab Pougo, Kienbaoghin, Zongo, Koumdayonré, Nonsin, Rimkiéta, 
Tampouy, Kilwin, Tanghin, Sambin Barrage, Somgandé, Zone industrielle, Nioko II, 
Bendogo, Toukin, Zogona, Wemtenga, Dagnoën, Ronsin, Kalgondin, Cissin, 
Kouritenga, Pissy, Nagrin, Yaoghin, Sandogo, Kankamsin, Boassa, Zagtouli Nord, 
Zagtouli Sud, Zongo Nabitenga, Sogpèlcé, Bissighin, Bassinko, Dar-es-Salaam, 
Silmiougou, Gantin, Bangpooré, Larlé Wéogo, Marcoussis, Silmiyiri, Wobriguéré, 
Ouapassi, Kossodo, Wayalghin, Godin, Nioko I, Dassasgho, Taabtenga, Karpala, 
Balkuy, Lanoayiri, Dayongo, Ouidtenga, Patte d'Oie, Ouaga 2000, 
Trame d'accueil de Ouaga 2000
```

### 4. **Photo de Profil Utilisateur** ✅
- **Nouvelle colonne** : `photo` dans table `users`
- **Type** : string (URL ou chemin du fichier)
- **Utilisation** :
  - Upload de photo de profil
  - Modification dans l'espace utilisateur
  - Affichage dans l'admin

### 5. **Catégories Digitales** 📝 (À implémenter)
**Catégories suggérées** :
- Téléphones & Smartphones
- Ordinateurs & Laptops
- Télévisions & Écrans
- Audio & Radios
- Appareils Photo & Caméras
- Consoles de Jeux
- Tablettes & Liseuses
- Accessoires Électroniques
- Montres Connectées
- Drones & Gadgets

## 📊 Structure de la Base de Données

### Table `product_images`
```sql
CREATE TABLE product_images (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT NOT NULL,
    url VARCHAR(255) NOT NULL,
    type ENUM('principale', 'secondaire', 'galerie') DEFAULT 'galerie',
    order INT DEFAULT 0,
    alt_text VARCHAR(255) NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_type (product_id, type),
    INDEX idx_product_order (product_id, order)
);
```

### Table `products` (colonnes ajoutées)
```sql
ALTER TABLE products ADD COLUMN wholesale_price DECIMAL(10,2) NULL AFTER price;
ALTER TABLE products ADD COLUMN retail_price DECIMAL(10,2) NULL AFTER wholesale_price;
ALTER TABLE products ADD COLUMN min_wholesale_quantity INT DEFAULT 10 AFTER retail_price;
```

### Table `users` (colonnes ajoutées)
```sql
ALTER TABLE users ADD COLUMN photo VARCHAR(255) NULL AFTER email;
```

## 🔄 Prochaines Étapes

### Backend
1. ✅ Créer les migrations
2. ✅ Créer les modèles
3. ✅ Ajouter les relations
4. 📝 Mettre à jour les contrôleurs API
5. 📝 Créer le seeder pour les catégories digitales
6. 📝 Ajouter les endpoints pour upload d'images
7. 📝 Ajouter les endpoints pour profil utilisateur

### Mobile (Flutter)
1. 📝 Mettre à jour le modèle Product (wholesale_price, retail_price)
2. 📝 Mettre à jour le modèle User (photo, quartier)
3. 📝 Créer le modèle ProductImage
4. 📝 Ajouter la sélection de quartier à l'inscription
5. 📝 Ajouter l'upload de photo de profil
6. 📝 Afficher les images multiples des produits
7. 📝 Afficher les prix en gros/détail selon la quantité

## 🎯 Utilisation

### Récupérer l'image principale d'un produit
```php
$product = Product::with('productImages')->find(1);
$mainImage = $product->main_image; // Retourne l'URL de l'image principale
```

### Récupérer toutes les images
```php
$allImages = $product->all_images; // Retourne un tableau d'URLs
```

### Calculer le prix selon la quantité
```php
public function getPrice($quantity) {
    if ($quantity >= $this->min_wholesale_quantity && $this->wholesale_price) {
        return $this->wholesale_price;
    }
    return $this->retail_price ?? $this->price;
}
```

### Récupérer les quartiers
```php
$quartiers = config('quartiers.ouagadougou');
```

## 📝 Notes Importantes

1. **Images** : Les anciennes images dans la colonne `images` (JSON) sont conservées pour compatibilité
2. **Prix** : Le champ `price` reste le prix par défaut si retail_price n'est pas défini
3. **Quartiers** : La liste peut être étendue facilement dans `config/quartiers.php`
4. **Photo** : Prévoir un système de stockage (local ou cloud) pour les photos
