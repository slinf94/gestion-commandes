@echo off
cls
echo ========================================
echo SUPPRESSION DE TOUS LES PRODUITS
echo ========================================
echo.

cd /d "%~dp0"

echo ⚠️  ATTENTION DANGER ⚠️
echo.
echo Ce script va SUPPRIMER TOUS LES PRODUITS !
echo.
echo Seront supprimes :
echo - TOUS les produits
echo - Toutes les images associees
echo - Tous les attributs de produits
echo - Toutes les variantes de produits
echo - Tous les prix par quantite
echo - Tous les favoris
echo - TOUS les fichiers images physiques
echo.
echo Seront CONSERVES :
echo - Les categories
echo - Les utilisateurs
echo - Les commandes
echo.
echo Cette action est IRREVERSIBLE !
echo.
echo Appuyez sur Ctrl+C pour ANNULER
echo Appuyez sur n'importe quelle touche pour CONTINUER...
pause >nul
echo.

echo ========================================
echo ETAPE 1/7 : COMPTAGE
echo ========================================
echo.

echo Nombre de produits a supprimer...
php artisan tinker --execute="\$count = DB::table('products')->whereNull('deleted_at')->count(); echo 'Produits : ' . \$count; echo PHP_EOL; exit;"
echo.

echo Nombre d'images a supprimer...
php artisan tinker --execute="\$count = DB::table('product_images')->count(); echo 'Images : ' . \$count; echo PHP_EOL; exit;"
echo.

echo ========================================
echo ETAPE 2/7 : IMAGES DE PRODUITS
echo ========================================
echo.

php artisan tinker --execute="DB::table('product_images')->delete(); echo '✓ Images supprimees'; echo PHP_EOL; exit;"
echo.

echo ========================================
echo ETAPE 3/7 : FAVORIS
echo ========================================
echo.

php artisan tinker --execute="DB::table('favorites')->delete(); echo '✓ Favoris supprimes'; echo PHP_EOL; exit;"
echo.

echo ========================================
echo ETAPE 4/7 : PRIX PAR QUANTITE
echo ========================================
echo.

php artisan tinker --execute="if (Schema::hasTable('product_prices')) { DB::table('product_prices')->delete(); echo '✓ Prix supprimes'; } else { echo '- Table inexistante'; } echo PHP_EOL; exit;"
echo.

echo ========================================
echo ETAPE 5/7 : ATTRIBUTS ET VARIANTES
echo ========================================
echo.

php artisan tinker --execute="DB::table('product_attribute_values')->delete(); echo '✓ Attributs supprimes'; echo PHP_EOL; exit;"

php artisan tinker --execute="if (Schema::hasTable('product_variants')) { DB::table('product_variants')->delete(); echo '✓ Variantes supprimees'; } else { echo '- Table variantes inexistante'; } echo PHP_EOL; exit;"
echo.

echo ========================================
echo ETAPE 6/7 : PRODUITS
echo ========================================
echo.

php artisan tinker --execute="\$count = DB::table('products')->whereNull('deleted_at')->count(); DB::table('products')->delete(); echo '✓ ' . \$count . ' produits supprimes'; echo PHP_EOL; exit;"
echo.

echo ========================================
echo ETAPE 7/7 : FICHIERS PHYSIQUES
echo ========================================
echo.

if exist "storage\app\public\products" (
    echo Suppression des fichiers images...
    rmdir "storage\app\public\products" /S /Q 2>nul
    mkdir "storage\app\public\products"
    echo   ✓ Dossier vide
) else (
    echo   - Pas de dossier images
)
echo.

echo ========================================
echo ✅ SUPPRESSION COMPLETE TERMINEE !
echo ========================================
echo.
echo Verification finale...
php artisan tinker --execute="echo 'Produits restants : ' . DB::table('products')->whereNull('deleted_at')->count(); echo PHP_EOL; echo 'Images restantes : ' . DB::table('product_images')->count(); echo PHP_EOL; exit;"
echo.
echo ========================================
echo RESULTAT
echo ========================================
echo.
echo ✓ Base de donnees nettoyee
echo ✓ Fichiers images supprimes
echo ✓ Pret a recommencer
echo.
echo PROCHAINES ETAPES :
echo.
echo 1. Rechargez : http://127.0.0.1:8000/admin/products
echo    Resultat : Liste vide (normal)
echo.
echo 2. Creez de nouveaux produits :
echo    http://127.0.0.1:8000/admin/products/create
echo.
echo 3. Les nouvelles images fonctionneront correctement !
echo.
pause
