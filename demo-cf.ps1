#Requires -Version 5.1
<#
  demo-cf.ps1  --  start a Cloudflare quick tunnel, point the portal at it,
                   print + copy the public link. Called by demo-cf.bat.
                   No account, no admin rights needed.

  End it with demo-cf-stop.bat (kills cloudflared + runs demo-stop.ps1).
#>
$ErrorActionPreference = 'Stop'
$root = $PSScriptRoot
$log  = Join-Path $env:TEMP 'itm-cloudflared.err.log'
$out  = Join-Path $env:TEMP 'itm-cloudflared.out.log'
$Port = 8080

# --- locate cloudflared ---
$cf = @(
    'C:\Program Files (x86)\cloudflared\cloudflared.exe',
    'C:\Program Files\cloudflared\cloudflared.exe'
) | Where-Object { Test-Path $_ } | Select-Object -First 1
if (-not $cf) { $cf = (Get-Command cloudflared -ErrorAction SilentlyContinue).Source }
if (-not $cf) {
    Write-Host ""
    Write-Host "  cloudflared not found." -ForegroundColor Red
    Write-Host "  Install once:  winget install --id Cloudflare.cloudflared" -ForegroundColor Yellow
    exit 1
}

# --- clean slate: undo any leftover demo (restores config.php -> localhost) ---
& powershell -NoProfile -ExecutionPolicy Bypass -File (Join-Path $root 'demo-stop.ps1') | Out-Null

# --- Apache listening on the port? (raw TCP - a re-point may not give a 200) ---
$up = $false
try {
    $c = New-Object System.Net.Sockets.TcpClient
    $c.Connect('127.0.0.1', $Port); $up = $c.Connected; $c.Close()
} catch { $up = $false }
if (-not $up) {
    Write-Host ""
    Write-Host "  Nothing is listening on http://localhost:$Port  --  run  start.bat  first." -ForegroundColor Red
    exit 1
}

# --- clean any previous tunnel ---
Get-Process cloudflared -ErrorAction SilentlyContinue | Stop-Process -Force
Start-Sleep -Milliseconds 500
Remove-Item $log, $out -ErrorAction SilentlyContinue

Write-Host "  starting Cloudflare tunnel..."
$proc = Start-Process -FilePath $cf `
    -ArgumentList 'tunnel', '--url', "http://localhost:$Port", '--no-autoupdate' `
    -RedirectStandardError $log -RedirectStandardOutput $out `
    -WindowStyle Hidden -PassThru

# --- wait for the public URL to appear in the log (up to 45s) ---
$url = $null
for ($i = 0; $i -lt 45 -and -not $url; $i++) {
    Start-Sleep -Seconds 1
    foreach ($f in @($log, $out)) {
        if (Test-Path $f) {
            $m = Select-String -Path $f -Pattern 'https://[a-z0-9-]+\.trycloudflare\.com' -ErrorAction SilentlyContinue |
                 Select-Object -First 1
            if ($m) { $url = $m.Matches[0].Value; break }
        }
    }
}
if (-not $url) {
    Write-Host "  could not read a tunnel URL - see $log" -ForegroundColor Red
    if ($proc) { Stop-Process -Id $proc.Id -Force -ErrorAction SilentlyContinue }
    exit 1
}
$publicHost = $url -replace 'https://', ''
Write-Host "  tunnel URL: $url"

# --- point Moodle wwwroot at it (backs up config.php, sets sslproxy) ---
& powershell -NoProfile -ExecutionPolicy Bypass -File (Join-Path $root 'demo.ps1') `
    -Domain $publicHost -NoTunnel -NoAuth

# --- save + clipboard ---
Set-Content -Path (Join-Path $root 'demo-url.txt') -Value $url -Encoding utf8
try { Set-Clipboard -Value $url } catch {}

# --- wait until it actually serves (opcache warm-up, ~60-75s) ---
Write-Host "  warming up (up to ~75s)..."
$live = $false
for ($i = 0; $i -lt 25 -and -not $live; $i++) {
    Start-Sleep -Seconds 3
    try {
        $r = Invoke-WebRequest "$url/login/index.php" -TimeoutSec 12 -UseBasicParsing
        if ($r.StatusCode -eq 200 -and $r.Content -match 'ITM GOI Exams') { $live = $true }
    } catch { }
}

Write-Host ""
Write-Host "  ===================================================================" -ForegroundColor Green
Write-Host "   LIVE :  $url" -ForegroundColor Green
Write-Host "           (copied to clipboard + saved to demo-url.txt)"          -ForegroundColor DarkGray
Write-Host "   Login:  examadmin / 44BDUbI!1wF*i+evVKrG@C"                      -ForegroundColor Green
Write-Host "           s2026001 / Exam@2026   (student view)"                  -ForegroundColor Green
Write-Host "           invig01  / Hall#Invig2026   (invigilator view)"         -ForegroundColor Green
if (-not $live) {
    Write-Host "   (not answering yet - give it another minute, then share)"   -ForegroundColor Yellow
}
Write-Host "  ===================================================================" -ForegroundColor Green
Write-Host ""
Write-Host "  Tunnel runs in the background. End the demo with  demo-cf-stop.bat" -ForegroundColor Yellow
Write-Host "  (your local http://localhost:$Port shows 'incorrect access' until then)" -ForegroundColor DarkGray
