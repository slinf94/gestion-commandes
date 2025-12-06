@echo off
cls
echo ========================================
echo SUPPRESSION DES IMAGES - BASE DE DONNEES
echo ========================================
echo.

cd /d "%~dp0"

echo ⚠️  ATTENTION ⚠️
echo.
echo Ce script va SUPPRIMER TOUTES les images
echo de la table product_images dans la base de donnees.
echo.
echo Les fichiers physiques NE seront PAS supprimes.
echo Seules les references en base seront effacees.
echo.
echo Cette action est IRREVERSIBLE !
echo.
echo Appuyez sur Ctrl+C pour ANNULER
echo Appuyez sur n'importe quelle touche pour CONTINUER...
pause >nul
echo.

echo ETAPE 1 : Comptage des images actuelles...
php artisan tinker --execute="\$count = DB::table('product_images')->count(); echo 'Nombre d images a supprimer : ' . \$count; echo PHP_EOL; exit;"
echo.

echo ETAPE 2 : Suppression des images...
php artisan tinker --execute="DB::table('product_images')->delete(); echo 'Toutes les images ont ete supprimees de la base'; echo PHP_EOL; exit;"
echo.

echo ETAPE 3 : Verification...
php artisan tinker --execute="\$count = DB::table('product_images')->count(); echo 'Images restantes : ' . \$count; echo PHP_EOL; if(\$count == 0) { echo '✓ Suppression reussie !'; } else { echo '✗ Erreur : il reste des images'; } echo PHP_EOL; exit;"
echo.

echo ========================================
echo ✅ SUPPRESSION TERMINEE !
echo ========================================
echo.
echo Résumé :
echo - Toutes les references d'images ont ete supprimees
echo - Les fichiers physiques sont toujours presents dans storage/app/public/products
echo - Les produits n'ont plus d'images associees
echo.
echo PROCHAINES ETAPES :
echo.
echo 1. Rechargez la page des produits
echo    http://127.0.0.1:8000/admin/products
echo.
echo 2. Les produits n'afficheront plus d'images
echo.
echo 3. Vous pouvez maintenant :
echo    - Creer de nouveaux produits avec images
echo    - Modifier les produits existants pour ajouter des images
echo.
pause
