# Quick Setup Script - Run as Administrator
# This script will set up SSH on your machine for GitHub Actions deployment

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  SETTING UP SSH FOR DEPLOYMENT" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

# Check if running as Administrator
$isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)

if (-not $isAdmin) {
    Write-Host "ERROR: This script must be run as Administrator!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Right-click PowerShell and select 'Run as Administrator'" -ForegroundColor Yellow
    Write-Host ""
    pause
    exit
}

Write-Host "Running as Administrator" -ForegroundColor Green
Write-Host ""

# Step 1: Install OpenSSH Server
Write-Host "Step 1/6: Installing OpenSSH Server..." -ForegroundColor Yellow
try {
    $sshServer = Get-WindowsCapability -Online | Where-Object Name -like 'OpenSSH.Server*'
    if ($sshServer.State -eq "Installed") {
        Write-Host "   OpenSSH Server already installed" -ForegroundColor Green
    } else {
        Write-Host "   Installing OpenSSH Server..." -ForegroundColor Gray
        Add-WindowsCapability -Online -Name OpenSSH.Server~~~~0.0.1.0
        Write-Host "   OpenSSH Server installed successfully" -ForegroundColor Green
    }
} catch {
    Write-Host "   Failed to install OpenSSH Server: $_" -ForegroundColor Red
    exit
}
Write-Host ""

# Step 2: Start SSH Service
Write-Host "Step 2/6: Starting SSH Service..." -ForegroundColor Yellow
try {
    Start-Service sshd -ErrorAction SilentlyContinue
    Set-Service -Name sshd -StartupType 'Automatic'
    Write-Host "   SSH Service started and set to automatic" -ForegroundColor Green
} catch {
    Write-Host "   Warning: $_" -ForegroundColor Yellow
}
Write-Host ""

# Step 3: Configure Firewall
Write-Host "Step 3/6: Configuring Firewall..." -ForegroundColor Yellow
try {
    $firewallRule = Get-NetFirewallRule -Name "sshd" -ErrorAction SilentlyContinue
    if ($firewallRule) {
        Write-Host "   Firewall rule already exists" -ForegroundColor Green
    } else {
        New-NetFirewallRule -Name sshd -DisplayName 'OpenSSH Server (sshd)' -Enabled True -Direction Inbound -Protocol TCP -Action Allow -LocalPort 22
        Write-Host "   Firewall rule added" -ForegroundColor Green
    }
} catch {
    Write-Host "   Warning: $_" -ForegroundColor Yellow
}
Write-Host ""

# Step 4: Generate SSH Key
Write-Host "Step 4/6: Generating SSH Key..." -ForegroundColor Yellow
$sshKeyPath = "$HOME\.ssh\github_deploy"
if (Test-Path $sshKeyPath) {
    Write-Host "   SSH key already exists at $sshKeyPath" -ForegroundColor Yellow
    $overwrite = Read-Host "   Do you want to overwrite it? (y/n)"
    if ($overwrite -ne "y") {
        Write-Host "   Skipping key generation..." -ForegroundColor Gray
    } else {
        Remove-Item $sshKeyPath -Force
        Remove-Item "$sshKeyPath.pub" -Force -ErrorAction SilentlyContinue
        & ssh-keygen -t ed25519 -C "github-actions" -f $sshKeyPath -N '""'
        Write-Host "   SSH key generated" -ForegroundColor Green
    }
} else {
    New-Item -ItemType Directory -Path "$HOME\.ssh" -Force | Out-Null
    & ssh-keygen -t ed25519 -C "github-actions" -f $sshKeyPath -N '""'
    Write-Host "   SSH key generated at $sshKeyPath" -ForegroundColor Green
}
Write-Host ""

# Step 5: Configure authorized_keys
Write-Host "Step 5/6: Configuring authorized_keys..." -ForegroundColor Yellow
try {
    New-Item -ItemType Directory -Path "$HOME\.ssh" -Force | Out-Null
    
    $publicKey = Get-Content "$sshKeyPath.pub"
    $authorizedKeysPath = "$HOME\.ssh\authorized_keys"
    
    # Check if key already exists in authorized_keys
    if (Test-Path $authorizedKeysPath) {
        $existingKeys = Get-Content $authorizedKeysPath
        if ($existingKeys -contains $publicKey) {
            Write-Host "   Public key already in authorized_keys" -ForegroundColor Green
        } else {
            Add-Content -Path $authorizedKeysPath -Value $publicKey
            Write-Host "   Public key added to authorized_keys" -ForegroundColor Green
        }
    } else {
        Set-Content -Path $authorizedKeysPath -Value $publicKey
        Write-Host "   authorized_keys created with public key" -ForegroundColor Green
    }
    
    # Set permissions
    icacls $authorizedKeysPath /inheritance:r | Out-Null
    icacls $authorizedKeysPath /grant:r "$env:USERNAME:(F)" | Out-Null
    Write-Host "   Permissions set on authorized_keys" -ForegroundColor Green
} catch {
    Write-Host "   Failed to configure authorized_keys: $_" -ForegroundColor Red
}
Write-Host ""

# Step 6: Get credentials
Write-Host "Step 6/6: Gathering your credentials..." -ForegroundColor Yellow
$username = $env:USERNAME
$ipAddress = (Get-NetIPAddress -AddressFamily IPv4 | Where-Object { $_.InterfaceAlias -notlike "*Loopback*" -and $_.IPAddress -notlike "169.254.*" } | Select-Object -First 1).IPAddress
Write-Host "   Credentials gathered" -ForegroundColor Green
Write-Host ""

# Display Summary
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  SETUP COMPLETE!" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "SEND THESE VALUES TO REPOSITORY OWNER:" -ForegroundColor Yellow
Write-Host ""
Write-Host "PROD_HOST: " -NoNewline
Write-Host "$ipAddress" -ForegroundColor Green
Write-Host ""
Write-Host "PROD_USERNAME: " -NoNewline
Write-Host "$username" -ForegroundColor Green
Write-Host ""
Write-Host "PROD_PORT: " -NoNewline
Write-Host "22" -ForegroundColor Green
Write-Host ""
Write-Host "PROD_SSH_KEY:" -ForegroundColor White
Write-Host "(Private key copied to clipboard - paste it in the message below)" -ForegroundColor Gray
Write-Host ""

# Copy private key to clipboard
Get-Content $sshKeyPath | Set-Clipboard
Write-Host "Private key copied to clipboard!" -ForegroundColor Green
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  MESSAGE TO SEND TO OWNER" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

$message = @"
Hi JendoubiMajdi,

Here are my machine credentials for GitHub Secrets.
Please add these to: https://github.com/JendoubiMajdi/Waste2Product/settings/secrets/actions

PROD_HOST
Value: $ipAddress

PROD_USERNAME
Value: $username

PROD_PORT
Value: 22

PROD_SSH_KEY
Value: (Paste the private key from my clipboard below)

(Paste private key here - it starts with -----BEGIN OPENSSH PRIVATE KEY-----)

Notes:
* My computer will be ready to receive deployments
* My computer needs to be ON when deployment runs
* All setup is complete on my end

Let me know when you have added these secrets!
"@

Write-Host $message
Write-Host ""

# Save message to file
$messageFile = Join-Path $PSScriptRoot "CREDENTIALS_TO_SEND.txt"
$message | Out-File -FilePath $messageFile -Encoding UTF8
Write-Host "Message saved to: CREDENTIALS_TO_SEND.txt" -ForegroundColor Green
Write-Host ""

# Test SSH
Write-Host "Testing SSH connection..." -ForegroundColor Yellow
Write-Host "   Command: ssh -i $sshKeyPath $username@localhost" -ForegroundColor Gray
Write-Host ""

Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "  NEXT STEPS" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "1. Open: CREDENTIALS_TO_SEND.txt" -ForegroundColor White
Write-Host "2. Press Ctrl+V to paste the private key in the file" -ForegroundColor White
Write-Host "3. Send the complete message to JendoubiMajdi" -ForegroundColor White
Write-Host "4. Wait for owner to add secrets to GitHub" -ForegroundColor White
Write-Host "5. Keep your computer ON when deployment happens" -ForegroundColor White
Write-Host ""

Write-Host "Press any key to exit..." -ForegroundColor Gray
$host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown") | Out-Null
