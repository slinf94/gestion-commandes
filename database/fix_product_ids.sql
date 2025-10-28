-- Script SQL pour réorganiser les IDs des produits
-- ATTENTION: À exécuter uniquement sur un environnement de développement ou après backup

-- 1. Créer une table temporaire
DROP TABLE IF EXISTS products_backup;
CREATE TABLE products_backup LIKE products;

-- 2. Copier les données sans les IDs supprimés
INSERT INTO products_backup
SELECT * FROM products WHERE deleted_at IS NULL ORDER BY id ASC;

-- 3. Vider la table originale
TRUNCATE TABLE products;

-- 4. Réinsérer avec des IDs réorganisés (1, 2, 3...)
SET @row_number = 0;
INSERT INTO products
SELECT
    (@row_number := @row_number + 1) as id,
    name, description, price, cost_price, wholesale_price, retail_price,
    min_wholesale_quantity, stock_quantity, min_stock_alert, status,
    sku, barcode, category_id, product_type_id, meta_title, meta_description,
    tags, images, created_at, updated_at, deleted_at
FROM products_backup
ORDER BY id ASC;

-- 5. Réinitialiser l'auto-increment
ALTER TABLE products AUTO_INCREMENT = 1;

-- 6. Nettoyer
DROP TABLE products_backup;


