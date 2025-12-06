@echo off
cls
echo ========================================
echo VERIFICATION DES IMAGES
echo ========================================
echo.

cd /d "%~dp0"

echo ETAPE 1 : Nombre d'images dans la base...
php artisan tinker --execute="echo 'Total images : ' . DB::table('product_images')->count(); echo PHP_EOL; exit;"
echo.

echo ETAPE 2 : Exemples d'URLs d'images...
php artisan tinker --execute="\$images = DB::table('product_images')->limit(3)->get(['id', 'product_id', 'url']); foreach (\$images as \$img) { echo 'ID: ' . \$img->id . ' | Produit: ' . \$img->product_id . ' | URL: ' . \$img->url . PHP_EOL; } exit;"
echo.

echo ETAPE 3 : Test Storage::url()...
php artisan tinker --execute="\$img = DB::table('product_images')->first(); if (\$img) { echo 'URL brute : ' . \$img->url . PHP_EOL; echo 'Storage::url() : ' . Storage::url(\$img->url) . PHP_EOL; } else { echo 'Aucune image trouvee'; } exit;"
echo.

echo ETAPE 4 : Verification du lien symbolique...
if exist "public\storage" (
    echo   ✓ Lien symbolique existe
    dir "public\storage" | find "<"
) else (
    echo   ✗ PROBLEME : Lien symbolique n'existe pas !
    echo.
    echo   SOLUTION : Executez FIX_IMAGES_TOTAL.bat en administrateur
)
echo.

echo ETAPE 5 : Verification des fichiers physiques...
if exist "storage\app\public\products" (
    echo   ✓ Dossier products existe
    dir "storage\app\public\products" /B
) else (
    echo   ✗ PROBLEME : Dossier products n'existe pas !
)
echo.

pause
