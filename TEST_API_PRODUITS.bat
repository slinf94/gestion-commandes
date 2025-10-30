@echo off
chcp 65001 >nul
echo ========================================
echo   TEST DE L'API PRODUITS
echo ========================================
echo.

echo Test de l'endpoint products...
echo URL: http://127.0.0.1:8000/api/v1/products
echo.

powershell -Command "try { $response = Invoke-WebRequest -Uri 'http://127.0.0.1:8000/api/v1/products' -Headers @{'X-Mobile-App'='true'} -UseBasicParsing; Write-Host 'Status:' $response.StatusCode; $json = $response.Content | ConvertFrom-Json; Write-Host 'Success:' $json.success; Write-Host 'Total produits:' $json.pagination.total; Write-Host 'Nombre produits retournes:' $json.data.Count } catch { Write-Host 'ERREUR:' $_.Exception.Message }"

echo.
echo ========================================
echo.

pause

