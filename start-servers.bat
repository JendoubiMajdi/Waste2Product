@echo off
echo ================================================
echo Starting Waste2Product Application
echo ================================================
echo.

REM Start Python AI API in a new window
echo [1/2] Starting Python AI API Server on port 5000...
start "Python AI API" cmd /k "cd python-api && python app.py"

REM Wait a moment for Python to start
timeout /t 3 /nobreak > nul

REM Start Laravel server in a new window
echo [2/2] Starting Laravel Server on port 8000...
start "Laravel Server" cmd /k "php artisan serve"

echo.
echo ================================================
echo Both servers are starting!
echo ================================================
echo.
echo Python AI API: http://localhost:5000
echo Laravel App:   http://localhost:8000
echo.
echo Close the terminal windows to stop the servers.
echo ================================================
