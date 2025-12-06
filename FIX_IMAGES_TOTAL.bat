@echo off
cls
echo ========================================
echo CORRECTION COMPLETE DES IMAGES
echo ========================================
echo.

cd /d "%~dp0"

echo ETAPE 1/5 : Creation du dossier products...
if not exist "storage\app\public\products" mkdir "storage\app\public\products"
echo   ✓ Dossier cree
echo.

echo ETAPE 2/5 : Suppression de l'ancien lien symbolique...
if exist "public\storage" (
    rmdir "public\storage" /S /Q 2>nul
    del "public\storage" /F /Q 2>nul
    echo   ✓ Ancien lien supprime
) else (
    echo   - Pas d'ancien lien
)
echo.

echo ETAPE 3/5 : Creation du NOUVEAU lien symbolique...
echo   ATTENTION : Necessite les droits administrateur !
echo.
mklink /D "public\storage" "..\storage\app\public"
if %errorlevel% == 0 (
    echo   ✓ Lien symbolique cree avec succes !
) else (
    echo   ✗ ECHEC - Executez ce fichier en tant qu'ADMINISTRATEUR
    echo.
    echo   Clic droit sur FIX_IMAGES_TOTAL.bat ^> Executer en tant qu'administrateur
    pause
    exit /b 1
)
echo.

echo ETAPE 4/5 : Verification...
if exist "public\storage\products" (
    echo   ✓ Le lien fonctionne correctement !
) else (
    echo   ✗ Le lien ne fonctionne pas
)
echo.

echo ETAPE 5/5 : Nettoyage des anciennes images S3 dans la base...
php artisan tinker --execute="DB::table('product_images')->where('url', 'like', '%%s3://%%')->delete(); DB::table('product_images')->where('url', 'like', '%%https://%%')->delete(); echo 'Images S3 supprimees de la base'; exit;"
echo.

echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo PROCHAINES ETAPES :
echo.
echo 1. Arretez le serveur (Ctrl+C dans l'autre terminal)
echo 2. Redemarrez : php artisan serve
echo 3. Allez sur un produit et AJOUTEZ UNE NOUVELLE IMAGE
echo.
echo Les anciennes images ne s'afficheront pas car elles
echo pointaient vers S3. Il faut RE-UPLOADER les images.
echo.
pause
