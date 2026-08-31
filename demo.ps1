#Requires -Version 5.1
<#
  demo.ps1  --  put the ITM exam portal on a public URL for a live demo,
                using an ngrok tunnel from this laptop (no cloud hosting).

  The portal is Moodle: it hard-codes its address in $CFG->wwwroot, so this
  script temporarily points wwwroot at the tunnel URL (and turns on sslproxy,
  since the tunnel serves HTTPS while Apache still speaks plain HTTP on :8080).
  Run .\demo-stop.ps1 afterwards to put it back to http://localhost:8080.

  ---------------------------------------------------------------------------
  ONE-TIME SETUP
    winget install ngrok.ngrok
    ngrok config add-authtoken <token from dashboard.ngrok.com>
    # then claim a free static domain at dashboard.ngrok.com  ->  Domains
  ---------------------------------------------------------------------------

  USAGE
    .\demo.ps1 -Domain my-name.ngrok-free.app
    .\demo.ps1 -Domain my-name.ngrok-free.app -BasicAuth "demo:Show2026!"
    .\demo.ps1 -Domain my-name.ngrok-free.app -NoAuth
    .\demo.ps1 -Domain xxx.trycloudflare.com -NoTunnel   # you run cloudflared yourself

  After it prints the URL, keep this window open. Ctrl+C stops the tunnel;
  then run  .\demo-stop.ps1  to restore the local config.
#>
[CmdletBinding()]
param(
    [Parameter(Mandatory)][string]$Domain,
    [string]$BasicAuth = 'demo:Show2026!',
    [switch]$NoAuth,
    [switch]$NoTunnel,
    [int]$Port = 8080
)
$ErrorActionPreference = 'Stop'
$root = $PSScriptRoot
$cfg  = Join-Path $root 'native\stack\moodle\config.php'
$bak  = "$cfg.demobak"
$php  = Join-Path $root 'native\stack\php\php.exe'

function Save-Utf8NoBom([string]$path, [string]$text) {
    [System.IO.File]::WriteAllText($path, $text, (New-Object System.Text.UTF8Encoding $false))
}
function Purge-MoodleCaches {
    $inc = ($cfg -replace '\\', '/')
    & $php -r "define('CLI_SCRIPT',true); require('$inc'); purge_all_caches();" *> $null
}

# --- normalise the host: accept 'x.ngrok-free.app' or 'https://x.ngrok-free.app/' ---
$publicHost = $Domain -replace '^https?://', '' -replace '/.*$', ''
$publicUrl  = "https://$publicHost"

# --- 1. is Apache listening on the port? (any HTTP reply counts - during a
#        re-point the portal is up but wwwroot points elsewhere, so a 200 is
#        not guaranteed; a raw TCP connect is the right check) ---
$up = $false
try {
    $c = New-Object System.Net.Sockets.TcpClient
    $c.Connect('127.0.0.1', $Port); $up = $c.Connected; $c.Close()
} catch { $up = $false }
if (-not $up) {
    Write-Host ""
    Write-Host "  Nothing is listening on http://localhost:$Port" -ForegroundColor Red
    Write-Host "  Start it first:  .\start.bat" -ForegroundColor Yellow
    exit 1
}

# --- 2. back up config.php once ---
if (-not (Test-Path $bak)) {
    Copy-Item $cfg $bak
    Write-Host "  backed up config.php  ->  config.php.demobak"
} else {
    Write-Host "  config.php.demobak already exists (kept) - re-pointing wwwroot only"
}

# --- 3. rewrite wwwroot + sslproxy ---
$src = Get-Content -Raw -LiteralPath $cfg
$src = [regex]::Replace($src, "(?m)^\`$CFG->wwwroot\s*=\s*'[^']*';",
                        "`$CFG->wwwroot   = '$publicUrl';")
$src = [regex]::Replace($src, "(?m)^\`$CFG->sslproxy\s*=\s*(?:true|false);",
                        "`$CFG->sslproxy          = true;")
Save-Utf8NoBom $cfg $src
Write-Host "  config.php: wwwroot = $publicUrl , sslproxy = true"

# --- 4. purge caches ---
Purge-MoodleCaches
Write-Host "  Moodle caches purged"

Write-Host ""
Write-Host "  ===================================================================" -ForegroundColor Cyan
Write-Host "   Public URL : $publicUrl/" -ForegroundColor Cyan
if (-not $NoAuth -and -not $NoTunnel) {
    Write-Host "   Gate       : $BasicAuth   (browser will prompt for this)" -ForegroundColor Cyan
}
Write-Host "   Admin      : examadmin  /  44BDUbI!1wF*i+evVKrG@C" -ForegroundColor Cyan
Write-Host "   Local now shows an 'incorrect access' notice - that's expected." -ForegroundColor DarkGray
Write-Host "   PHP opcache picks up the new address within ~60s." -ForegroundColor DarkGray
Write-Host "  ===================================================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "  When done:  Ctrl+C here, then  .\demo-stop.ps1" -ForegroundColor Yellow
Write-Host ""

if ($NoTunnel) {
    Write-Host "  -NoTunnel: not starting ngrok. Point your own tunnel at port $Port now."
    exit 0
}

# --- 5. start ngrok (foreground) ---
$ngrok = (Get-Command ngrok -ErrorAction SilentlyContinue)
if (-not $ngrok) {
    Write-Host "  ngrok not found. Install it:  winget install ngrok.ngrok" -ForegroundColor Red
    Write-Host "  (config is already pointed at the tunnel - run .\demo-stop.ps1 to undo)" -ForegroundColor Yellow
    exit 1
}

$args = @('http', "--url=$publicHost")
if (-not $NoAuth) { $args += "--basic-auth=$BasicAuth" }
$args += "$Port"

Write-Host "  > ngrok $($args -join ' ')" -ForegroundColor DarkGray
Write-Host ""
& ngrok @args

# ngrok exited (Ctrl+C)
Write-Host ""
Write-Host "  Tunnel stopped. Run  .\demo-stop.ps1  to restore http://localhost:$Port" -ForegroundColor Yellow
