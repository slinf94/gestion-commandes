@echo off
echo Demarrage du serveur Laravel...
cd /d "%~dp0"
php artisan serve --host=0.0.0.0 --port=8000
pause
