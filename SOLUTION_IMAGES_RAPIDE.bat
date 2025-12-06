@echo off
echo ========================================
echo SOLUTION RAPIDE : IMAGES DES PRODUITS
echo ========================================
echo.

cd /d "%~dp0"

echo 1️⃣  Creation du lien symbolique storage...
php artisan storage:link
echo.

echo 2️⃣  Ajout d'images placeholder aux produits...
php add_placeholder_images.php
echo.

echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo 📋 PROCHAINES ETAPES :
echo.
echo 1. Allez sur : http://192.168.100.73:8000/admin/products
echo 2. Les produits devraient maintenant avoir des images placeholder
echo 3. Pour ajouter de vraies images :
echo    - Cliquez sur "Modifier" pour chaque produit
echo    - Uploadez une image
echo    - Sauvegardez
echo.
echo 📖 Pour plus d'infos, consultez : GUIDE_IMAGES_PRODUITS.md
echo.
pause

