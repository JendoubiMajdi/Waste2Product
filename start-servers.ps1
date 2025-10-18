# PowerShell script to start both servers in parallel

Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Starting Waste2Product Application" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

# Start Python AI API in a new window
Write-Host "[1/2] Starting Python AI API Server on port 5000..." -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PSScriptRoot\python-api'; python app.py"

# Wait a moment for Python to start
Start-Sleep -Seconds 3

# Start Laravel server in a new window
Write-Host "[2/2] Starting Laravel Server on port 8000..." -ForegroundColor Green
Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$PSScriptRoot'; php artisan serve"

Write-Host ""
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "Both servers are starting!" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Python AI API: " -NoNewline -ForegroundColor Yellow
Write-Host "http://localhost:5000" -ForegroundColor White
Write-Host "Laravel App:   " -NoNewline -ForegroundColor Yellow
Write-Host "http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "Close the PowerShell windows to stop the servers." -ForegroundColor Gray
Write-Host "================================================" -ForegroundColor Cyan
