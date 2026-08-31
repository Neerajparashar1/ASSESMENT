#Requires -Version 5.1
<#
  demo-stop.ps1  --  undo demo.ps1: restore config.php so the portal serves
                     at http://localhost:8080 again.

  Run this AFTER you have stopped the tunnel (Ctrl+C in the demo.ps1 window,
  or closed the ngrok/cloudflared process).
#>
$ErrorActionPreference = 'Stop'
$root = $PSScriptRoot
$cfg  = Join-Path $root 'native\stack\moodle\config.php'
$bak  = "$cfg.demobak"
$php  = Join-Path $root 'native\stack\php\php.exe'
$Port = 8080

function Save-Utf8NoBom([string]$path, [string]$text) {
    [System.IO.File]::WriteAllText($path, $text, (New-Object System.Text.UTF8Encoding $false))
}

if (Test-Path $bak) {
    # exact restore from the backup demo.ps1 made
    $orig = Get-Content -Raw -LiteralPath $bak
    Save-Utf8NoBom $cfg $orig
    Remove-Item $bak
    Write-Host "  config.php restored from config.php.demobak"
} else {
    # no backup - best-effort revert
    $src = Get-Content -Raw -LiteralPath $cfg
    $src = [regex]::Replace($src, "(?m)^\`$CFG->wwwroot\s*=\s*'[^']*';",
                            "`$CFG->wwwroot   = 'http://localhost:$Port';")
    $src = [regex]::Replace($src, "(?m)^\`$CFG->sslproxy\s*=\s*(?:true|false);",
                            "`$CFG->sslproxy          = false;")
    Save-Utf8NoBom $cfg $src
    Write-Host "  no backup found - reverted wwwroot to http://localhost:$Port , sslproxy = false"
}

# purge caches
$inc = ($cfg -replace '\\', '/')
& $php -r "define('CLI_SCRIPT',true); require('$inc'); purge_all_caches();" *> $null
Write-Host "  Moodle caches purged"

# verify (opcache may lag up to ~60s)
Start-Sleep -Seconds 2
try {
    $r = Invoke-WebRequest "http://localhost:$Port/login/index.php" -TimeoutSec 8 -UseBasicParsing
    Write-Host "  http://localhost:$Port  ->  $($r.StatusCode) OK"
} catch {
    Write-Host "  http://localhost:$Port not clean yet - wait ~60s (PHP opcache) and refresh." -ForegroundColor Yellow
}

Write-Host ""
Write-Host "  Done. If a tunnel is still running, stop it (Ctrl+C / close its window)." -ForegroundColor Yellow
