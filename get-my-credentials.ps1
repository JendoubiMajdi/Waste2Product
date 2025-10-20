# 🔐 Get My Credentials for GitHub Secrets
# Run this script to gather all information needed for deployment to YOUR machine

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  GATHERING YOUR CREDENTIALS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# 1. Get Username
Write-Host "1️⃣  YOUR WINDOWS USERNAME (PROD_USERNAME):" -ForegroundColor Yellow
$username = $env:USERNAME
Write-Host "   Value: $username" -ForegroundColor Green
Write-Host ""

# 2. Get Computer Name
Write-Host "2️⃣  YOUR COMPUTER NAME:" -ForegroundColor Yellow
$computerName = $env:COMPUTERNAME
Write-Host "   Value: $computerName" -ForegroundColor Green
Write-Host ""

# 3. Get Local IP Address
Write-Host "3️⃣  YOUR LOCAL IP ADDRESS (PROD_HOST):" -ForegroundColor Yellow
$ipAddress = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notlike "*Loopback*" -and $_.IPAddress -notlike "169.254.*" } | Select-Object -First 1).IPAddress
Write-Host "   Value: $ipAddress" -ForegroundColor Green
Write-Host "   Note: Use 'localhost' or '127.0.0.1' if deploying on same machine" -ForegroundColor Gray
Write-Host ""

# 4. Check if OpenSSH is installed
Write-Host "4️⃣  CHECKING OPENSSH STATUS:" -ForegroundColor Yellow
try {
    $sshVersion = ssh -V 2>&1
    Write-Host "   ✅ OpenSSH is installed: $sshVersion" -ForegroundColor Green
    
    # Check if SSH Server is running
    $sshService = Get-Service -Name sshd -ErrorAction SilentlyContinue
    if ($sshService) {
        if ($sshService.Status -eq "Running") {
            Write-Host "   ✅ OpenSSH Server is running" -ForegroundColor Green
        } else {
            Write-Host "   ⚠️  OpenSSH Server is installed but NOT running" -ForegroundColor Yellow
            Write-Host "   Run: Start-Service sshd" -ForegroundColor Gray
        }
    } else {
        Write-Host "   ⚠️  OpenSSH Server is NOT installed" -ForegroundColor Yellow
        Write-Host "   Run: Add-WindowsCapability -Online -Name OpenSSH.Server~~~~0.0.1.0" -ForegroundColor Gray
    }
} catch {
    Write-Host "   ❌ OpenSSH is NOT installed" -ForegroundColor Red
    Write-Host "   Run: Add-WindowsCapability -Online -Name OpenSSH.Client~~~~0.0.1.0" -ForegroundColor Gray
}
Write-Host ""

# 5. Check for existing SSH keys
Write-Host "5️⃣  CHECKING FOR EXISTING SSH KEYS:" -ForegroundColor Yellow
$sshPath = "$HOME\.ssh"
if (Test-Path $sshPath) {
    $keys = Get-ChildItem $sshPath -Filter "id_*" -Exclude "*.pub" -ErrorAction SilentlyContinue
    if ($keys.Count -gt 0) {
        Write-Host "   ✅ Found existing SSH keys:" -ForegroundColor Green
        foreach ($key in $keys) {
            Write-Host "      - $($key.Name)" -ForegroundColor Cyan
        }
    } else {
        Write-Host "   ⚠️  No existing SSH keys found" -ForegroundColor Yellow
    }
} else {
    Write-Host "   ⚠️  .ssh folder doesn't exist" -ForegroundColor Yellow
}
Write-Host ""

# 6. SSH Port
Write-Host "6️⃣  SSH PORT (PROD_PORT):" -ForegroundColor Yellow
Write-Host "   Value: 22 (default)" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  SUMMARY - SEND THIS TO OWNER" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "PROD_HOST: " -NoNewline -ForegroundColor White
Write-Host "$ipAddress" -ForegroundColor Green
Write-Host "   (Or use 'localhost' if owner will test on your machine directly)" -ForegroundColor Gray
Write-Host ""

Write-Host "PROD_USERNAME: " -NoNewline -ForegroundColor White
Write-Host "$username" -ForegroundColor Green
Write-Host ""

Write-Host "PROD_PORT: " -NoNewline -ForegroundColor White
Write-Host "22" -ForegroundColor Green
Write-Host ""

Write-Host "PROD_SSH_KEY: " -NoNewline -ForegroundColor White
Write-Host "(Generate below ⬇️)" -ForegroundColor Yellow
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  NEXT STEPS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "📋 Step 1: Install OpenSSH Server (if not installed)" -ForegroundColor Yellow
Write-Host "   Run this command as Administrator:" -ForegroundColor Gray
Write-Host "   Add-WindowsCapability -Online -Name OpenSSH.Server~~~~0.0.1.0" -ForegroundColor Cyan
Write-Host ""

Write-Host "📋 Step 2: Start OpenSSH Server" -ForegroundColor Yellow
Write-Host "   Start-Service sshd" -ForegroundColor Cyan
Write-Host "   Set-Service -Name sshd -StartupType 'Automatic'" -ForegroundColor Cyan
Write-Host ""

Write-Host "📋 Step 3: Generate SSH Key" -ForegroundColor Yellow
Write-Host "   ssh-keygen -t ed25519 -C 'github-actions' -f $HOME\.ssh\github_deploy" -ForegroundColor Cyan
Write-Host "   (Press Enter for all prompts)" -ForegroundColor Gray
Write-Host ""

Write-Host "📋 Step 4: Get Private Key (for PROD_SSH_KEY)" -ForegroundColor Yellow
Write-Host "   Get-Content $HOME\.ssh\github_deploy | Set-Clipboard" -ForegroundColor Cyan
Write-Host "   (This copies the private key to your clipboard)" -ForegroundColor Gray
Write-Host ""

Write-Host "📋 Step 5: Add Public Key to Your Machine" -ForegroundColor Yellow
Write-Host "   New-Item -ItemType Directory -Path '$HOME\.ssh' -Force" -ForegroundColor Cyan
Write-Host "   Get-Content $HOME\.ssh\github_deploy.pub | Out-File -Append $HOME\.ssh\authorized_keys" -ForegroundColor Cyan
Write-Host ""

Write-Host "📋 Step 6: Send These Values to Owner" -ForegroundColor Yellow
Write-Host "   PROD_HOST: $ipAddress" -ForegroundColor Green
Write-Host "   PROD_USERNAME: $username" -ForegroundColor Green
Write-Host "   PROD_SSH_KEY: (The content from Step 4)" -ForegroundColor Green
Write-Host "   PROD_PORT: 22" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  ⚠️  IMPORTANT NOTES" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Your computer must be ON and connected when deployment happens" -ForegroundColor Yellow
Write-Host "2. Your firewall must allow SSH connections (port 22)" -ForegroundColor Yellow
Write-Host "3. If behind a router, you may need port forwarding" -ForegroundColor Yellow
Write-Host "4. For testing, consider using 'localhost' instead of IP" -ForegroundColor Yellow
Write-Host ""

Write-Host "Press any key to continue..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
