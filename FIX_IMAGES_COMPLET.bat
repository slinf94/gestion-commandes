@echo off
echo ========================================
echo CORRECTION COMPLETE DES IMAGES
echo ========================================
echo.

cd /d "%~dp0"

echo Etape 1/5 : Suppression ancien lien storage...
if exist "public\storage" (
    rmdir /S /Q "public\storage"
    echo   - Ancien lien supprime
)
echo.

echo Etape 2/5 : Creation lien symbolique Windows...
mklink /D "public\storage" "..\storage\app\public"
if %ERRORLEVEL% EQU 0 (
    echo   ✓ Lien symbolique cree avec succes
) else (
    echo   ✗ Erreur creation lien - Verifiez les permissions admin
    echo   SOLUTION : Executez ce script en tant qu'administrateur
    pause
    exit /b 1
)
echo.

echo Etape 3/5 : Creation dossier products...
if not exist "storage\app\public\products" (
    mkdir "storage\app\public\products"
    echo   ✓ Dossier products cree
) else (
    echo   ✓ Dossier products existe deja
)
echo.

echo Etape 4/5 : Verification lien symbolique...
if exist "public\storage\products" (
    echo   ✓ Le lien fonctionne correctement !
) else (
    echo   ✗ Le lien ne fonctionne pas
    echo   SOLUTION : Executez ce script en tant qu'administrateur
    pause
    exit /b 1
)
echo.

echo Etape 5/5 : Ajout images aux produits...
php add_placeholder_images.php
echo.

echo ========================================
echo ✅ TERMINE !
echo ========================================
echo.
echo Test : Allez sur http://127.0.0.1:8000/admin/products
echo.
echo Si les images ne s'affichent toujours pas :
echo 1. Fermez COMPLETEMENT Chrome/Edge
echo 2. Rouvrez et allez sur la page
echo 3. Appuyez sur Ctrl+F5 pour forcer le rechargement
echo.
pause

