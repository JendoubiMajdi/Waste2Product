@echo off
echo ========================================
echo  Starting Waste2Product Servers
echo ========================================
echo.

:: Start Python AI API in a new window
echo [1/2] Starting Python AI API Server...
start "Python AI API - Port 5000" cmd /k "cd python-api && python app.py"
timeout /t 3 /nobreak >nul

:: Start Laravel Server in a new window
echo [2/2] Starting Laravel Server...
start "Laravel Server - Port 8000" cmd /k "php artisan serve"

echo.
echo ========================================
echo  All Servers Started!
echo ========================================
echo  - Python AI API: http://localhost:5000
echo  - Laravel App:   http://localhost:8000
echo ========================================
echo.
echo Press any key to close this window...
pause >nul
