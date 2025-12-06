@echo off
cls
echo ========================================
echo SUPPRESSION DES ANCIENS PRODUITS
echo ========================================
echo.

cd /d "%~dp0"

echo ⚠️  ATTENTION ⚠️
echo.
echo Ce script va SUPPRIMER TOUS les produits existants
echo ainsi que leurs images et données associées.
echo.
echo Cette action est IRREVERSIBLE !
echo.
echo Appuyez sur Ctrl+C pour ANNULER
echo Appuyez sur n'importe quelle touche pour CONTINUER...
pause >nul
echo.

echo ETAPE 1/6 : Suppression des images de produits...
php artisan tinker --execute="DB::table('product_images')->delete(); echo 'Images supprimees: ' . DB::table('product_images')->count(); exit;"
echo   ✓ Images de la base supprimees
echo.

echo ETAPE 2/6 : Suppression des favoris...
php artisan tinker --execute="DB::table('favorites')->delete(); echo 'Favoris supprimes'; exit;"
echo   ✓ Favoris supprimes
echo.

echo ETAPE 3/6 : Suppression des prix par quantité...
php artisan tinker --execute="if (Schema::hasTable('product_prices')) { DB::table('product_prices')->delete(); echo 'Prix supprimes'; } else { echo 'Table product_prices inexistante'; } exit;"
echo   ✓ Prix par quantité supprimes
echo.

echo ETAPE 4/6 : Suppression des variantes et attributs...
php artisan tinker --execute="DB::table('product_attribute_values')->delete(); echo 'Attributs supprimes'; exit;"
echo   ✓ Attributs supprimes
echo.

echo ETAPE 5/6 : Suppression des PRODUITS...
php artisan tinker --execute="\$count = DB::table('products')->count(); DB::table('products')->delete(); echo 'Produits supprimes: ' . \$count; exit;"
echo   ✓ Produits supprimes
echo.

echo ETAPE 6/6 : Nettoyage des fichiers images physiques...
if exist "storage\app\public\products" (
    rmdir "storage\app\public\products" /S /Q 2>nul
    mkdir "storage\app\public\products"
    echo   ✓ Dossier images nettoye
) else (
    echo   - Pas de dossier images
)
echo.

echo ========================================
echo ✅ SUPPRESSION TERMINEE !
echo ========================================
echo.
echo Résumé :
echo - Tous les produits ont été supprimés
echo - Toutes les images ont été supprimées
echo - Toutes les données associées ont été nettoyées
echo.
echo PROCHAINES ETAPES :
echo.
echo 1. Executez FIX_IMAGES_TOTAL.bat (en administrateur)
echo    pour créer le lien symbolique
echo.
echo 2. Redémarrez le serveur : php artisan serve
echo.
echo 3. Créez de NOUVEAUX produits avec des images
echo    http://127.0.0.1:8000/admin/products/create
echo.
pause
