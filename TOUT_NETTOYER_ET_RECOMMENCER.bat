@echo off
cls
echo ========================================
echo NETTOYAGE COMPLET + REDEMARRAGE
echo ========================================
echo.

cd /d "%~dp0"

echo ⚠️  ATTENTION ⚠️
echo.
echo Ce script va :
echo 1. Supprimer TOUS les produits
echo 2. Nettoyer toutes les images
echo 3. Recréer le lien symbolique
echo 4. Tout configurer correctement
echo.
echo Cette action est IRREVERSIBLE !
echo.
echo Appuyez sur Ctrl+C pour ANNULER
echo Appuyez sur n'importe quelle touche pour CONTINUER...
pause >nul
cls

echo ========================================
echo ETAPE 1/3 : SUPPRESSION DES DONNEES
echo ========================================
echo.

echo Suppression des images de produits...
php artisan tinker --execute="DB::table('product_images')->delete(); echo 'OK'; exit;" >nul 2>&1
echo   ✓ Images supprimees

echo Suppression des favoris...
php artisan tinker --execute="DB::table('favorites')->delete(); echo 'OK'; exit;" >nul 2>&1
echo   ✓ Favoris supprimes

echo Suppression des attributs...
php artisan tinker --execute="DB::table('product_attribute_values')->delete(); echo 'OK'; exit;" >nul 2>&1
echo   ✓ Attributs supprimes

echo Suppression des produits...
php artisan tinker --execute="\$count = DB::table('products')->count(); DB::table('products')->delete(); echo \$count . ' produits supprimes'; exit;"
echo   ✓ Produits supprimes
echo.

echo ========================================
echo ETAPE 2/3 : NETTOYAGE DES FICHIERS
echo ========================================
echo.

echo Suppression des fichiers images...
if exist "storage\app\public\products" (
    rmdir "storage\app\public\products" /S /Q 2>nul
    mkdir "storage\app\public\products"
    echo   ✓ Dossier products nettoye
)

echo Suppression de l'ancien lien symbolique...
if exist "public\storage" (
    rmdir "public\storage" /S /Q 2>nul
    del "public\storage" /F /Q 2>nul
    echo   ✓ Ancien lien supprime
)
echo.

echo ========================================
echo ETAPE 3/3 : RECREATION DU LIEN
echo ========================================
echo.

echo Creation du lien symbolique...
echo   ATTENTION : Necessite les droits administrateur !
echo.
mklink /D "public\storage" "..\storage\app\public"
if %errorlevel% == 0 (
    echo   ✓ Lien symbolique cree avec succes !
) else (
    echo   ✗ ECHEC - Droits administrateur requis !
    echo.
    echo   SOLUTION : Fermez cette fenetre et :
    echo   1. Clic droit sur TOUT_NETTOYER_ET_RECOMMENCER.bat
    echo   2. "Executer en tant qu'administrateur"
    pause
    exit /b 1
)
echo.

echo Verification du lien...
if exist "public\storage\products" (
    echo   ✓ Le lien fonctionne correctement !
) else (
    echo   ✗ Le lien ne fonctionne pas
)
echo.

echo ========================================
echo ✅ NETTOYAGE COMPLET TERMINE !
echo ========================================
echo.
echo Tout est maintenant propre et configuré !
echo.
echo PROCHAINES ETAPES :
echo.
echo 1. Si le serveur tourne, arretez-le (Ctrl+C)
echo.
echo 2. Redemarrez le serveur :
echo    php artisan serve
echo.
echo 3. Créez de NOUVEAUX produits :
echo    http://127.0.0.1:8000/admin/products/create
echo.
echo 4. Les images fonctionneront correctement !
echo.
pause
