# One-off: pre-fetch every archive Setup-MoodleNative.ps1 needs. No elevation required.
$ErrorActionPreference = 'Continue'
$ProgressPreference = 'SilentlyContinue'
. "$PSScriptRoot\_env.ps1"
try { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12 -bor [Net.SecurityProtocolType]::Tls13 }
catch { [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12 }

$UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
$dl = Join-Path $EapInstallRoot '_downloads'
New-Item -ItemType Directory -Force -Path $dl | Out-Null

function Grab {
    param($Url, $Name)
    $out = Join-Path $dl $Name
    if ((Test-Path $out) -and (Get-Item $out).Length -gt 0) {
        "{0,-34} cached  {1:N1} MB" -f $Name, ((Get-Item $out).Length / 1MB); return
    }
    for ($t = 1; $t -le 3; $t++) {
        try {
            Invoke-WebRequest -Uri $Url -OutFile $out -UserAgent $UA -TimeoutSec 180 -UseBasicParsing
            "{0,-34} OK      {1:N1} MB" -f $Name, ((Get-Item $out).Length / 1MB); return
        } catch {
            "{0,-34} try {1}/3 FAILED  {2}" -f $Name, $t, $_.Exception.Message
            Start-Sleep ($t * 3)
        }
    }
    "{0,-34} *** GIVE UP ***" -f $Name
}

# PHP - resolve newest 8.3 vs16 x64 TS
$phpName = 'php-8.3.33-Win32-vs16-x64.zip'
try {
    $idx = (Invoke-WebRequest 'https://downloads.php.net/~windows/releases/' -UserAgent $UA -TimeoutSec 30 -UseBasicParsing).Content
    $m = [regex]::Matches($idx, 'php-(8\.3\.\d+)-Win32-vs16-x64\.zip') |
         Sort-Object { [version]$_.Groups[1].Value } -Descending | Select-Object -First 1
    if ($m) { $phpName = $m.Value }
} catch { }
"PHP resolved to: $phpName"

Grab "https://downloads.php.net/~windows/releases/$phpName" $phpName
Grab 'https://archive.mariadb.org/mariadb-11.4.13/winx64-packages/mariadb-11.4.13-winx64.zip' 'mariadb-11.4.13-winx64.zip'
Grab 'https://packaging.moodle.org/stable405/moodle-latest-405.zip' 'moodle-latest-405.zip'
Grab 'https://www.apachelounge.com/download/VS17/binaries/httpd-2.4.66-251206-Win64-VS17.zip' 'httpd-2.4.66-251206-Win64-VS17.zip'
Grab 'https://github.com/moodle-an-hochschulen/moodle-theme_boost_union/archive/refs/heads/MOODLE_405_STABLE.zip' 'plugin_theme_boost_union.zip'
Grab 'https://github.com/eLearning-BS23/moodle-quizaccess_proctoring/archive/refs/heads/master.zip' 'plugin_quizaccess_proctoring.zip'
Grab 'https://github.com/jcrodriguez-dis/moodle-mod_vpl/archive/refs/heads/master.zip' 'plugin_mod_vpl.zip'
Grab 'https://curl.se/ca/cacert.pem' 'cacert.pem'

"`n--- staged in $dl ---"
Get-ChildItem $dl | ForEach-Object { "{0,-34} {1:N1} MB" -f $_.Name, ($_.Length / 1MB) }
