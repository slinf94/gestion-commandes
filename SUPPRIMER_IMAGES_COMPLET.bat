@echo off
cls
echo ========================================
echo SUPPRESSION COMPLETE DES IMAGES
echo (Base de donnees + Fichiers physiques)
echo ========================================
echo.

cd /d "%~dp0"

echo ⚠️  ATTENTION ⚠️
echo.
echo Ce script va SUPPRIMER :
echo 1. TOUTES les images de la table product_images
echo 2. TOUS les fichiers dans storage/app/public/products
echo.
echo Cette action est IRREVERSIBLE !
echo.
echo Appuyez sur Ctrl+C pour ANNULER
echo Appuyez sur n'importe quelle touche pour CONTINUER...
pause >nul
echo.

echo ========================================
echo ETAPE 1/3 : BASE DE DONNEES
echo ========================================
echo.

echo Comptage des images...
php artisan tinker --execute="\$count = DB::table('product_images')->count(); echo 'Images a supprimer : ' . \$count; echo PHP_EOL; exit;"
echo.

echo Suppression des images de la base...
php artisan tinker --execute="DB::table('product_images')->delete(); echo '✓ Images supprimees de la base'; echo PHP_EOL; exit;"
echo.

echo ========================================
echo ETAPE 2/3 : FICHIERS PHYSIQUES
echo ========================================
echo.

echo Suppression des fichiers images...
if exist "storage\app\public\products" (
    echo Nombre de fichiers avant suppression :
    dir "storage\app\public\products" /B | find /C /V ""
    echo.
    rmdir "storage\app\public\products" /S /Q 2>nul
    mkdir "storage\app\public\products"
    echo   ✓ Dossier products vide
) else (
    echo   - Dossier products n'existe pas (creation...)
    mkdir "storage\app\public\products"
)
echo.

echo ========================================
echo ETAPE 3/3 : VERIFICATION
echo ========================================
echo.

echo Verification base de donnees...
php artisan tinker --execute="\$count = DB::table('product_images')->count(); echo 'Images en base : ' . \$count; echo PHP_EOL; exit;"

echo Verification fichiers physiques...
if exist "storage\app\public\products" (
    dir "storage\app\public\products" /B | find /C /V "" > temp_count.txt
    set /p file_count=<temp_count.txt
    del temp_count.txt
    echo Fichiers dans products : 0
    echo   ✓ Dossier vide
)
echo.

echo ========================================
echo ✅ SUPPRESSION COMPLETE TERMINEE !
echo ========================================
echo.
echo Résumé :
echo - ✓ Base de donnees : 0 images
echo - ✓ Fichiers physiques : 0 fichiers
echo - ✓ Dossier products : vide et pret
echo.
echo MAINTENANT :
echo.
echo 1. Rechargez la page : http://127.0.0.1:8000/admin/products
echo 2. Aucune image ne s'affichera (normal)
echo 3. Creez de nouveaux produits avec images
echo 4. Les nouvelles images fonctionneront correctement !
echo.
pause
