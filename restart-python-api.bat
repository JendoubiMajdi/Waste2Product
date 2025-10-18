@echo off
echo ========================================
echo  Restarting Python AI Server
echo ========================================
echo.

cd python-api

echo Stopping any running Python processes...
taskkill /F /IM python.exe 2>nul

timeout /t 2 /nobreak >nul

echo.
echo Starting Python AI Server...
echo.
python app.py

pause
