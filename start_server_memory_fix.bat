@echo off
echo ========================================
echo   CORRECTION ERREUR MEMOIRE PHP
echo ========================================
echo.

echo Augmentation de la limite de memoire PHP...
echo.

REM Augmenter la limite de memoire PHP
php -d memory_limit=4G artisan serve --host=127.0.0.1 --port=8000

echo.
echo Serveur demarre avec limite de memoire augmentee.
echo.
pause

