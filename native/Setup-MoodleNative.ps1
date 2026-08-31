#Requires -Version 5.1
<#
.SYNOPSIS
  Native-Windows bring-up for the AI-Proctored Assessment Platform.
  Moodle 4.5 LTS on Apache 2.4 + mod_php (PHP 8.3 TS) + MariaDB 11.4 LTS. No Docker.

.DESCRIPTION
  1.  Ensures the Visual C++ 2015-2022 x64 runtime is present.
  2.  Downloads pinned portable builds of PHP, Apache (Apache Lounge VS17),
      MariaDB and Moodle 4.5 into <InstallRoot>.
  3.  Generates php.ini, Apache vhost + hardening, and my.ini from the
      repo's existing config\ files (Windows-adapted).
  4.  Initialises MariaDB, creates the moodle database + user.
  5.  Runs Moodle's non-interactive CLI installer with
      dataroot = E:\ASSESMENT\moodledata  (override with -DataRoot).
  6.  Best-effort installs contrib plugins: theme_boost_union,
      quizaccess_proctoring, mod_vpl  (skip with -SkipPlugins).
  7.  Injects the EAP managed config block (anti-cheat footer, autosave,
      sslproxy, pathtophp) into config.php.
  8.  Runs scripts\moodle\post_install.php -> Boost Union theme + custom SCSS,
      SEB + proctoring defaults, quiz hardening, Invigilator role.
  9.  Registers Apache + MariaDB as Windows services and a 1-minute
      Moodle cron scheduled task.
  10. Generates a default Safe Exam Browser config from .env.

  Re-runnable. Already-done steps are skipped unless -Reinstall is given.

.EXAMPLE
  powershell -ExecutionPolicy Bypass -File .\native\Setup-MoodleNative.ps1

.EXAMPLE
  # Apache Lounge blocks scripted downloads behind Cloudflare from time to
  # time. Download the VS17 httpd zip by hand and pass it in:
  .\native\Setup-MoodleNative.ps1 -ApacheZip C:\Users\Hp\Downloads\httpd-2.4.66-251206-Win64-VS17.zip

.NOTES
  Must run elevated (it self-elevates via UAC if it is not).
#>
[CmdletBinding()]
param(
    [string]$InstallRoot,
    [string]$DataRoot,
    [int]$Port = 0,
    [ValidateSet('8.3', '8.2')][string]$PhpSeries = '8.3',
    [switch]$SkipDownloads,
    [switch]$SkipPlugins,
    [switch]$SkipServices,
    [switch]$Reinstall,
    [switch]$RefreshBinaries,
    [string]$ApacheZip,
    [string]$PhpZip,
    [string]$MariaDbZip,
    [string]$MoodleZip
)

$ErrorActionPreference = 'Stop'
$ProgressPreference = 'SilentlyContinue'
. "$PSScriptRoot\_env.ps1"

function Invoke-Native {
    # Run a native .exe without its stderr chatter raising a terminating error.
    # PowerShell 5.1 wraps native stderr as NativeCommandError under -Stop, so
    # tools that write normal output to stderr (httpd -t -> "Syntax OK",
    # mariadb-install-db, php CLI notices) would otherwise kill the script.
    # Returns the process exit code; all output is streamed to the host.
    param([Parameter(Mandatory)][string]$FilePath, [string[]]$ArgumentList = @())
    $old = $ErrorActionPreference
    $ErrorActionPreference = 'Continue'
    try {
        & $FilePath @ArgumentList 2>&1 | ForEach-Object { Write-Host "   $_" }
        return $LASTEXITCODE
    } finally { $ErrorActionPreference = $old }
}

# ---- honour parameter overrides ------------------------------------
if ($InstallRoot) { $EapInstallRoot = $InstallRoot }
if ($DataRoot)    { $EapDataRoot    = $DataRoot }
if ($Port -gt 0)  { $EapPort = $Port; $EapWwwRoot = "http://localhost:$Port" }
# re-derive dependent paths in case InstallRoot changed
$EapMoodleDir = Join-Path $EapInstallRoot 'moodle'
$EapPhpDir    = Join-Path $EapInstallRoot 'php'
$EapPhpExe    = Join-Path $EapPhpDir 'php.exe'
$EapApacheDir = Join-Path $EapInstallRoot 'Apache24'
$EapMariaDir  = Join-Path $EapInstallRoot 'mariadb'
$EapMariaData = Join-Path $EapInstallRoot 'mariadb-data'
$EapLogDir    = Join-Path $EapInstallRoot 'logs'
$EapTmpDir    = Join-Path $EapInstallRoot 'tmp'
$EapDlDir     = Join-Path $EapInstallRoot '_downloads'
$EapMyIni     = Join-Path $EapInstallRoot 'my.ini'

$UA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'

# ---- pinned versions (verified 2026-08-30) ------------------------
$PIN = @{
    PhpVer   = @{ '8.3' = '8.3.33'; '8.2' = '8.2.33' }[$PhpSeries]
    ApacheZ  = 'httpd-2.4.66-251206-Win64-VS17'
    MariaVer = '11.4.13'
}
$URL = @{
    PhpIndex = 'https://downloads.php.net/~windows/releases/'
    PhpBase  = 'https://downloads.php.net/~windows/releases'
    Apache   = "https://www.apachelounge.com/download/VS17/binaries/$($PIN.ApacheZ).zip"
    Maria    = "https://archive.mariadb.org/mariadb-$($PIN.MariaVer)/winx64-packages/mariadb-$($PIN.MariaVer)-winx64.zip"
    Moodle   = 'https://packaging.moodle.org/stable405/moodle-latest-405.zip'
    Cacert   = 'https://curl.se/ca/cacert.pem'
    VCRedist = 'https://aka.ms/vs/17/release/vc_redist.x64.exe'
    PlgTheme = 'https://github.com/moodle-an-hochschulen/moodle-theme_boost_union/archive/refs/heads/MOODLE_405_STABLE.zip'
    PlgProct = 'https://github.com/eLearning-BS23/moodle-quizaccess_proctoring/archive/refs/heads/master.zip'
    PlgVpl   = 'https://github.com/jcrodriguez-dis/moodle-mod_vpl/archive/refs/heads/master.zip'
}

# =====================================================================
#  helpers
# =====================================================================
function Assert-Admin {
    $id = [Security.Principal.WindowsIdentity]::GetCurrent()
    $pr = New-Object Security.Principal.WindowsPrincipal($id)
    if ($pr.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) { return }
    Write-EapWarn 'Not elevated - relaunching via UAC...'
    $argList = @('-NoProfile', '-ExecutionPolicy', 'Bypass', '-File', "`"$PSCommandPath`"")
    foreach ($k in $PSBoundParameters.Keys) {
        $v = $PSBoundParameters[$k]
        if ($v -is [switch]) { if ($v.IsPresent) { $argList += "-$k" } }
        else { $argList += "-$k"; $argList += "`"$v`"" }
    }
    Start-Process powershell.exe -Verb RunAs -ArgumentList $argList
    exit
}

function Set-Tls {
    try {
        [Net.ServicePointManager]::SecurityProtocol =
            [Net.SecurityProtocolType]::Tls12 -bor [Net.SecurityProtocolType]::Tls13
    } catch {
        [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    }
}

function Get-File {
    param([string]$Url, [string]$OutFile, [switch]$Optional)
    if ((Test-Path -LiteralPath $OutFile) -and (Get-Item $OutFile).Length -gt 0) {
        Write-EapOk "cached  $(Split-Path -Leaf $OutFile)"
        return $true
    }
    New-Item -ItemType Directory -Force -Path (Split-Path -Parent $OutFile) | Out-Null
    for ($try = 1; $try -le 3; $try++) {
        try {
            Write-EapInfo "download ($try/3)  $Url"
            Invoke-WebRequest -Uri $Url -OutFile $OutFile -UserAgent $UA -TimeoutSec 120 -UseBasicParsing
            if ((Get-Item $OutFile).Length -gt 0) { Write-EapOk "saved   $(Split-Path -Leaf $OutFile)"; return $true }
        } catch {
            Write-EapWarn "  $($_.Exception.Message)"
            Start-Sleep -Seconds ($try * 3)
        }
    }
    Remove-Item -Force -ErrorAction SilentlyContinue $OutFile
    if ($Optional) { Write-EapWarn "optional download failed, continuing: $Url"; return $false }
    throw "Download failed after 3 attempts: $Url"
}

function Expand-Into {
    param([string]$Zip, [string]$Dest, [switch]$Flatten)
    $tmp = Join-Path $env:TEMP ("eapx_" + [guid]::NewGuid().ToString('N'))
    New-Item -ItemType Directory -Force -Path $tmp | Out-Null
    try {
        Expand-Archive -LiteralPath $Zip -DestinationPath $tmp -Force
        $roots = @(Get-ChildItem -Force $tmp)
        $rootDirs = @($roots | Where-Object PSIsContainer)
        # Flatten when there is exactly one directory at the archive root, even if
        # stray files sit beside it (Apache Lounge ships ReadMe.txt + a marker file).
        $src = if ($Flatten -and $rootDirs.Count -eq 1) { $rootDirs[0].FullName } else { $tmp }
        New-Item -ItemType Directory -Force -Path $Dest | Out-Null
        Copy-Item -Path (Join-Path $src '*') -Destination $Dest -Recurse -Force
    } finally {
        Remove-Item -Recurse -Force -ErrorAction SilentlyContinue $tmp
    }
}

function Resolve-PhpZipName {
    $name = "php-$($PIN.PhpVer)-Win32-vs16-x64.zip"
    try {
        $html = (Invoke-WebRequest -Uri $URL.PhpIndex -UserAgent $UA -TimeoutSec 30 -UseBasicParsing).Content
        $rx = [regex]("php-(" + [regex]::Escape($PhpSeries) + "\.\d+)-Win32-vs16-x64\.zip")
        $hit = $rx.Matches($html) |
            Sort-Object { [version]$_.Groups[1].Value } -Descending |
            Select-Object -First 1
        if ($hit) { $name = $hit.Value }
    } catch { Write-EapWarn "PHP index lookup failed; using pinned $name" }
    return $name
}

function Test-VCRuntime {
    $k = 'HKLM:\SOFTWARE\Microsoft\VisualStudio\14.0\VC\Runtimes\x64'
    return (Test-Path $k) -and ((Get-ItemProperty $k -ErrorAction SilentlyContinue).Installed -eq 1)
}

function Invoke-Sql {
    param([string]$Sql, [string]$RootPw = $EapDbRootPass, [switch]$NoPassword)
    $exe = Join-Path $EapMariaDir 'bin\mariadb.exe'
    if (-not (Test-Path $exe)) { $exe = Join-Path $EapMariaDir 'bin\mysql.exe' }
    $a = @('--host=127.0.0.1', '--port=3306', '--user=root', '--batch', '--skip-column-names')
    if (-not $NoPassword) { $a += "--password=$RootPw" }
    $a += @('-e', $Sql)
    $old = $ErrorActionPreference; $ErrorActionPreference = 'Continue'
    try {
        $out = & $exe @a 2>&1
        $code = $LASTEXITCODE
    } finally { $ErrorActionPreference = $old }
    # mariadb.exe prints an insecure-password warning to stderr; only fail on a real error
    $real = $out | Where-Object { "$_" -notmatch 'Using a password on the command line' }
    if ($code -ne 0) { throw "SQL failed (exit $code): $Sql`n$($real -join "`n")" }
    return $real
}

function Wait-Tcp {
    param([int]$Port, [int]$TimeoutSec = 60)
    for ($i = 0; $i -lt $TimeoutSec; $i++) {
        if ((Test-NetConnection -ComputerName 127.0.0.1 -Port $Port -WarningAction SilentlyContinue).TcpTestSucceeded) { return $true }
        Start-Sleep -Seconds 1
    }
    return $false
}

# =====================================================================
#  0. preflight
# =====================================================================
Assert-Admin
Set-Tls
if (-not [Environment]::Is64BitOperatingSystem) { throw 'This stack requires 64-bit Windows.' }
if (-not (Test-Path (Join-Path $EapProjectRoot '.env'))) { throw "Missing $EapProjectRoot\.env" }
if (-not $EapDbPass -or -not $EapDbRootPass -or -not $EapAdminPass) {
    throw '.env must define DB_PASS, DB_ROOT_PASS and MOODLE_ADMIN_PASS'
}

Write-Host ''
Write-EapInfo "InstallRoot : $EapInstallRoot"
Write-EapInfo "DataRoot    : $EapDataRoot"
Write-EapInfo "Portal      : $EapWwwRoot   (Apache on port $EapPort)"
Write-EapInfo "PHP         : $PhpSeries  |  MariaDB : $($PIN.MariaVer)  |  Apache : $($PIN.ApacheZ)"
Write-Host ''

foreach ($d in @($EapInstallRoot, $EapDataRoot, $EapLogDir, $EapTmpDir, $EapDlDir)) {
    New-Item -ItemType Directory -Force -Path $d | Out-Null
}

# VC++ runtime -------------------------------------------------------
if (Test-VCRuntime) {
    Write-EapOk 'Visual C++ 2015-2022 x64 runtime present'
} else {
    $vc = Join-Path $EapDlDir 'vc_redist.x64.exe'
    Get-File -Url $URL.VCRedist -OutFile $vc | Out-Null
    Write-EapInfo 'Installing Visual C++ runtime (quiet)...'
    Start-Process -Wait -FilePath $vc -ArgumentList '/install', '/quiet', '/norestart'
    if (-not (Test-VCRuntime)) { Write-EapWarn 'VC++ runtime still not detected - Apache/PHP may fail to start.' }
}

# =====================================================================
#  1. download
# =====================================================================
$phpZipPath   = if ($PhpZip)     { $PhpZip }     else { Join-Path $EapDlDir (Resolve-PhpZipName) }
$apacheZipPath = if ($ApacheZip) { $ApacheZip }  else { Join-Path $EapDlDir "$($PIN.ApacheZ).zip" }
$mariaZipPath = if ($MariaDbZip) { $MariaDbZip } else { Join-Path $EapDlDir "mariadb-$($PIN.MariaVer)-winx64.zip" }
$moodleZipPath = if ($MoodleZip) { $MoodleZip }  else { Join-Path $EapDlDir 'moodle-latest-405.zip' }
$cacertPath   = Join-Path $EapPhpDir 'extras\ssl\cacert.pem'

# The repository ships the EXTRACTED stack under native\stack\. When a component
# is already present we neither download nor require its archive - so a fresh
# clone installs with no internet for the server tier (the VC++ runtime, if it
# is missing, is the only component still fetched). Pass -RefreshBinaries to
# force a clean re-download + re-extract of every component.
$havePhp    = (Test-Path (Join-Path $EapPhpDir    'php.exe'))          -and -not $RefreshBinaries
$haveApache = (Test-Path (Join-Path $EapApacheDir 'bin\httpd.exe'))    -and -not $RefreshBinaries
$haveMaria  = (Test-Path (Join-Path $EapMariaDir  'bin\mysqld.exe'))   -and -not $RefreshBinaries
$haveMoodle = (Test-Path (Join-Path $EapMoodleDir 'version.php'))      -and -not $RefreshBinaries
if ($havePhp -and $haveApache -and $haveMaria -and $haveMoodle) {
    Write-EapOk 'Bundled stack detected (php / Apache / MariaDB / Moodle) - no downloads needed'
}

if (-not $SkipDownloads) {
    if (-not $havePhp -and -not $PhpZip) { $phpUrl = "$($URL.PhpBase)/$(Split-Path -Leaf $phpZipPath)"
        if (-not (Get-File -Url $phpUrl -OutFile $phpZipPath -Optional)) {
            Get-File -Url "$($URL.PhpBase)/archives/$(Split-Path -Leaf $phpZipPath)" -OutFile $phpZipPath | Out-Null } }
    if (-not $haveMaria  -and -not $MariaDbZip) { Get-File -Url $URL.Maria  -OutFile $mariaZipPath | Out-Null }
    if (-not $haveMoodle -and -not $MoodleZip)  { Get-File -Url $URL.Moodle -OutFile $moodleZipPath | Out-Null }
    if (-not $haveApache -and -not $ApacheZip) {
        if (-not (Get-File -Url $URL.Apache -OutFile $apacheZipPath -Optional)) {
            Write-EapErr @"
Apache Lounge download was blocked (Cloudflare). Do this once, by hand:
  1. Open  $($URL.Apache)
  2. Save the zip anywhere, e.g. your Downloads folder
  3. Re-run:  .\native\Setup-MoodleNative.ps1 -ApacheZip <path-to-that-zip>
"@
            exit 1
        }
    }
} else {
    Write-EapWarn '-SkipDownloads: expecting archives to already be in _downloads\ (or the extracted stack to be present)'
}
# Only require an archive for a component that is NOT already extracted.
$needZips = @()
if (-not $havePhp)    { $needZips += $phpZipPath }
if (-not $haveApache) { $needZips += $apacheZipPath }
if (-not $haveMaria)  { $needZips += $mariaZipPath }
if (-not $haveMoodle) { $needZips += $moodleZipPath }
foreach ($z in $needZips) {
    if (-not (Test-Path -LiteralPath $z)) { throw "Required archive not found: $z" }
}

# =====================================================================
#  2. extract
# =====================================================================
# Extract only what is missing (or everything, with -RefreshBinaries). A plain
# -Reinstall keeps the bundled/extracted stack and rebuilds just the database,
# config.php and moodledata further down.
if (-not $havePhp) {
    Write-EapInfo 'Extracting PHP...';    Remove-Item -Recurse -Force -ErrorAction SilentlyContinue $EapPhpDir
    Expand-Into -Zip $phpZipPath -Dest $EapPhpDir
}
if (-not $haveApache) {
    Write-EapInfo 'Extracting Apache...'; Remove-Item -Recurse -Force -ErrorAction SilentlyContinue $EapApacheDir
    Expand-Into -Zip $apacheZipPath -Dest $EapApacheDir -Flatten
}
if (-not $haveMaria) {
    Write-EapInfo 'Extracting MariaDB...'; Remove-Item -Recurse -Force -ErrorAction SilentlyContinue $EapMariaDir
    Expand-Into -Zip $mariaZipPath -Dest $EapMariaDir -Flatten
}
if (-not $haveMoodle) {
    Write-EapInfo 'Extracting Moodle 4.5 (this takes a minute)...'
    Remove-Item -Recurse -Force -ErrorAction SilentlyContinue $EapMoodleDir
    Expand-Into -Zip $moodleZipPath -Dest $EapMoodleDir -Flatten
}
Get-File -Url $URL.Cacert -OutFile $cacertPath -Optional | Out-Null

# =====================================================================
#  3a. php.ini   (tuning mirrors config\php\moodle-php.ini, Windows-adapted)
# =====================================================================
$phpFwd    = ($EapPhpDir  -replace '\\', '/')
$tmpFwd    = ($EapTmpDir  -replace '\\', '/')
$logFwd    = ($EapLogDir  -replace '\\', '/')
$cacertFwd = ($cacertPath -replace '\\', '/')

$phpIni = @"
; ===================================================================
;  PHP runtime for Moodle assessment workloads  (native Windows / mod_php)
;  Generated by native\Setup-MoodleNative.ps1 - safe to regenerate.
; ===================================================================
engine = On
short_open_tag = Off
expose_php = Off
max_execution_time = 300
max_input_time = 300
max_input_vars = 5000
memory_limit = 512M
error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT
display_errors = Off
log_errors = On
error_log = "$logFwd/php_error.log"
default_charset = "UTF-8"
default_socket_timeout = 300
file_uploads = On
upload_max_filesize = 100M
post_max_size = 100M
max_file_uploads = 50
upload_tmp_dir = "$tmpFwd"
sys_temp_dir  = "$tmpFwd"
date.timezone = "$EapTz"
realpath_cache_size = 4096k
realpath_cache_ttl = 600

extension_dir = "$phpFwd/ext"
extension=curl
extension=gd
extension=intl
extension=mbstring
extension=exif
extension=fileinfo
extension=mysqli
extension=pdo_mysql
extension=openssl
extension=soap
extension=sodium
extension=zip
extension=ldap
zend_extension=opcache

curl.cainfo = "$cacertFwd"
openssl.cafile = "$cacertFwd"

[opcache]
opcache.enable = 1
opcache.enable_cli = 0
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 16
opcache.max_accelerated_files = 10000
opcache.revalidate_freq = 60
opcache.validate_timestamps = 1
opcache.save_comments = 1
opcache.fast_shutdown = 1

[session]
session.save_handler = files
session.save_path = "$tmpFwd"
session.gc_maxlifetime = 14400
"@
Set-Content -LiteralPath (Join-Path $EapPhpDir 'php.ini') -Value $phpIni -Encoding ASCII
Write-EapOk 'php.ini written'

# =====================================================================
#  3b. Apache config  (vhost + hardening from config\apache\*.conf)
# =====================================================================
$apacheFwd = ($EapApacheDir -replace '\\', '/')
$moodleFwd = ($EapMoodleDir -replace '\\', '/')
$httpdConf = Join-Path $EapApacheDir 'conf\httpd.conf'
# always start from the pristine config so edits are deterministic on re-run
$origConf = Join-Path $EapApacheDir 'conf\original\httpd.conf'
if (Test-Path $origConf) { Copy-Item -Force $origConf $httpdConf }
$conf = Get-Content -Raw -LiteralPath $httpdConf

# Apache Lounge builds hard-code an install path (C:/Apache24, C:/Apache24-64,
# or ${SRVROOT}); repoint every variant at our extracted tree.
$conf = $conf -replace '(?i)c:/Apache24(-64)?', $apacheFwd
$conf = $conf -replace '(?m)^Define SRVROOT ".*"', "Define SRVROOT `"$apacheFwd`""
$conf = $conf -replace '(?m)^ServerRoot ".*"',      "ServerRoot `"$apacheFwd`""
$conf = $conf -replace '(?m)^Listen 80\b',          "Listen $EapPort"
# uncomment required modules (this build writes "# LoadModule", with a space)
foreach ($mod in 'rewrite', 'headers', 'expires', 'deflate', 'setenvif') {
    $conf = $conf -replace "(?m)^#\s*(LoadModule ${mod}_module )", '$1'
}
if ($conf -notmatch 'httpd-eap\.conf') {
    $conf = $conf.TrimEnd() + "`r`n`r`nInclude conf/extra/httpd-eap.conf`r`n"
}
Set-Content -LiteralPath $httpdConf -Value $conf -Encoding ASCII

$phpApacheDll = (Get-ChildItem -Path $EapPhpDir -Filter 'php*apache2_4.dll' | Select-Object -First 1).Name
if (-not $phpApacheDll) { throw "mod_php DLL (php*apache2_4.dll) not found in $EapPhpDir - is this a Thread-Safe PHP build?" }

$eapConf = @"
# ===================================================================
#  EAP vhost + hardening  (generated - mirrors config\apache\*.conf)
# ===================================================================
ServerName localhost:$EapPort
LoadModule php_module "$phpFwd/$phpApacheDll"
PHPIniDir "$phpFwd"
<FilesMatch "\.php$">
    SetHandler application/x-httpd-php
</FilesMatch>
DirectoryIndex index.php index.html
AcceptPathInfo On

ServerTokens Prod
ServerSignature Off
TraceEnable Off

DocumentRoot "$moodleFwd"
<Directory "$moodleFwd">
    Options FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
<DirectoryMatch "$moodleFwd/(\.git|\.github|node_modules|tests)">
    Require all denied
</DirectoryMatch>
<FilesMatch "^\.">
    Require all denied
</FilesMatch>

<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
    Header always set X-XSS-Protection "0"
    Header always set Permissions-Policy "camera=(self), microphone=(self), geolocation=()"
    Header always set Strict-Transport-Security "max-age=15552000; includeSubDomains" "expr=%{HTTPS} == 'on'"
    Header always unset X-Powered-By
</IfModule>

<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css application/javascript application/json application/xml image/svg+xml
</IfModule>
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/png "access plus 30 days"
    ExpiresByType image/jpeg "access plus 30 days"
    ExpiresByType image/svg+xml "access plus 30 days"
    ExpiresByType text/css "access plus 30 days"
    ExpiresByType application/javascript "access plus 30 days"
    ExpiresByType font/woff2 "access plus 30 days"
</IfModule>

ErrorLog "$logFwd/moodle_error.log"
CustomLog "$logFwd/moodle_access.log" common
"@
New-Item -ItemType Directory -Force -Path (Join-Path $EapApacheDir 'conf\extra') | Out-Null
Set-Content -LiteralPath (Join-Path $EapApacheDir 'conf\extra\httpd-eap.conf') -Value $eapConf -Encoding ASCII

$httpdExe = Join-Path $EapApacheDir 'bin\httpd.exe'
if ((Invoke-Native $httpdExe @('-t')) -ne 0) { throw 'Apache config test failed (see above).' }
Write-EapOk 'Apache config OK'

# =====================================================================
#  3c. my.ini   (from config\mariadb\moodle.cnf, Windows-adapted)
# =====================================================================
$mariaDataFwd = ($EapMariaData -replace '\\', '/')
$myIni = @"
# Generated by native\Setup-MoodleNative.ps1 - mirrors config\mariadb\moodle.cnf
[mysqld]
datadir                        = $mariaDataFwd
port                           = 3306
bind-address                   = 127.0.0.1
character-set-server           = utf8mb4
collation-server               = utf8mb4_unicode_ci
skip-character-set-client-handshake
innodb_file_per_table          = 1
innodb_default_row_format      = dynamic
innodb_flush_log_at_trx_commit = 2
innodb_log_file_size           = 256M
innodb_io_capacity             = 400
innodb_buffer_pool_size        = $EapInnoBuf
max_connections                = 300
wait_timeout                   = 28800
interactive_timeout            = 28800
tmp_table_size                 = 64M
max_allowed_packet             = 128M
transaction-isolation          = READ-COMMITTED

[client]
default-character-set = utf8mb4
port = 3306

[mysql]
default-character-set = utf8mb4
"@
Set-Content -LiteralPath $EapMyIni -Value $myIni -Encoding ASCII
Write-EapOk 'my.ini written'

# =====================================================================
#  4. MariaDB init + service + database
# =====================================================================
$mariaSvc = Get-Service -Name $EapMariaSvc -ErrorAction SilentlyContinue
$dataInit = (Test-Path (Join-Path $EapMariaData 'mysql')) -or (Test-Path (Join-Path $EapMariaData 'ibdata1'))

if ($Reinstall -and $mariaSvc) {
    Write-EapWarn 'Reinstall: dropping MariaDB service + data dir'
    Stop-Service $EapMariaSvc -Force -ErrorAction SilentlyContinue
    & sc.exe delete $EapMariaSvc | Out-Null
    Start-Sleep 2
    Remove-Item -Recurse -Force -ErrorAction SilentlyContinue $EapMariaData
    $mariaSvc = $null; $dataInit = $false
}

if (-not $dataInit) {
    New-Item -ItemType Directory -Force -Path $EapMariaData | Out-Null
    $installDb = Join-Path $EapMariaDir 'bin\mariadb-install-db.exe'
    if (-not (Test-Path $installDb)) { $installDb = Join-Path $EapMariaDir 'bin\mysql_install_db.exe' }
    Write-EapInfo 'Initialising MariaDB data directory + service...'
    $rc = Invoke-Native $installDb @("--datadir=$EapMariaData", "--service=$EapMariaSvc",
        "--password=$EapDbRootPass", "--default-user", "--port=3306")
    if ($rc -ne 0) { throw "mariadb-install-db failed (exit $rc)" }
    # make the service use our tuned my.ini
    $svcBin = "`"$(Join-Path $EapMariaDir 'bin\mysqld.exe')`" --defaults-file=`"$EapMyIni`" $EapMariaSvc"
    & sc.exe config $EapMariaSvc binPath= $svcBin start= auto | Out-Null
} elseif (-not $mariaSvc) {
    $svcBin = "`"$(Join-Path $EapMariaDir 'bin\mysqld.exe')`" --defaults-file=`"$EapMyIni`" $EapMariaSvc"
    & sc.exe create $EapMariaSvc binPath= $svcBin start= auto DisplayName= 'EAP MariaDB' | Out-Null
}

Start-Service $EapMariaSvc
if (-not (Wait-Tcp -Port 3306 -TimeoutSec 45)) { throw 'MariaDB did not open port 3306' }
Write-EapOk 'MariaDB running'

# root password may be unset on a brand-new --default-user init depending on build:
$rootPwWorks = $true
try { Invoke-Sql 'SELECT 1;' | Out-Null } catch { $rootPwWorks = $false }
if (-not $rootPwWorks) {
    Write-EapInfo 'Setting MariaDB root password...'
    Invoke-Sql -NoPassword "ALTER USER 'root'@'localhost' IDENTIFIED BY '$EapDbRootPass'; FLUSH PRIVILEGES;"
}

Write-EapInfo "Ensuring database '$EapDbName' + user '$EapDbUser'..."
$ddl = @"
CREATE DATABASE IF NOT EXISTS ``$EapDbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$EapDbUser'@'localhost'  IDENTIFIED BY '$EapDbPass';
CREATE USER IF NOT EXISTS '$EapDbUser'@'127.0.0.1'  IDENTIFIED BY '$EapDbPass';
GRANT ALL PRIVILEGES ON ``$EapDbName``.* TO '$EapDbUser'@'localhost';
GRANT ALL PRIVILEGES ON ``$EapDbName``.* TO '$EapDbUser'@'127.0.0.1';
FLUSH PRIVILEGES;
"@
Invoke-Sql $ddl
Write-EapOk 'Database ready'

# =====================================================================
#  5. Moodle CLI install
# =====================================================================
$configPhp = Join-Path $EapMoodleDir 'config.php'
if ($Reinstall -and (Test-Path $configPhp)) {
    Write-EapWarn 'Reinstall: dropping config.php + moodle schema + moodledata'
    Remove-Item -Force $configPhp
    Invoke-Sql "DROP DATABASE IF EXISTS ``$EapDbName``; CREATE DATABASE ``$EapDbName`` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    Get-ChildItem -Force $EapDataRoot | Remove-Item -Recurse -Force -ErrorAction SilentlyContinue
}

if (-not (Test-Path $configPhp)) {
    Write-EapInfo 'Running Moodle non-interactive CLI install...'
    $rc = Invoke-Native $EapPhpExe @(
        (Join-Path $EapMoodleDir 'admin\cli\install.php'),
        '--lang=en',
        "--wwwroot=$EapWwwRoot",
        "--dataroot=$EapDataRoot",
        '--dbtype=mariadb',
        '--dbhost=127.0.0.1',
        "--dbname=$EapDbName",
        "--dbuser=$EapDbUser",
        "--dbpass=$EapDbPass",
        '--dbport=3306',
        '--prefix=mdl_',
        "--fullname=$EapSiteFull",
        "--shortname=$EapSiteShort",
        '--summary=AI-Proctored Online Assessment Platform',
        "--adminuser=$EapAdminUser",
        "--adminpass=$EapAdminPass",
        "--adminemail=$EapAdminEmail",
        '--non-interactive',
        '--agree-license')
    if ($rc -ne 0) { throw "install.php failed (exit $rc)" }
    Write-EapOk 'Moodle core installed'
} else {
    Write-EapOk 'config.php already present - skipping core install'
}

# =====================================================================
#  6. contrib plugins (best-effort)
# =====================================================================
if (-not $SkipPlugins) {
    $plugins = @(
        @{ Name = 'theme_boost_union';     Url = $URL.PlgTheme; Dest = (Join-Path $EapMoodleDir 'theme\boost_union') },
        @{ Name = 'quizaccess_proctoring'; Url = $URL.PlgProct; Dest = (Join-Path $EapMoodleDir 'mod\quiz\accessrule\proctoring') },
        @{ Name = 'mod_vpl';               Url = $URL.PlgVpl;   Dest = (Join-Path $EapMoodleDir 'mod\vpl') }
    )
    foreach ($p in $plugins) {
        if (Test-Path (Join-Path $p.Dest 'version.php')) { Write-EapOk "plugin present: $($p.Name)"; continue }
        $zp = Join-Path $EapDlDir ("plugin_" + $p.Name + ".zip")
        if (Get-File -Url $p.Url -OutFile $zp -Optional) {
            try { Expand-Into -Zip $zp -Dest $p.Dest -Flatten; Write-EapOk "plugin installed: $($p.Name)" }
            catch { Write-EapWarn "plugin extract failed: $($p.Name) - $_" }
        } else {
            Write-EapWarn "plugin download skipped: $($p.Name) (install later from Moodle plugin admin)"
        }
    }
} else {
    Write-EapWarn '-SkipPlugins: theme falls back to core Boost'
}

# =====================================================================
#  7. EAP managed config block  (mirrors docker entrypoint harden_config)
# =====================================================================
$sslProxy = if ($EapEnv.MOODLE_SSLPROXY -eq 'true') { 'true' } else { 'false' }
$revProxy = if ($EapEnv.MOODLE_REVERSEPROXY -eq 'true') { 'true' } else { 'false' }
$footerJs = '<script>(function(){var b=document.body;if(!b||b.id!=="page-mod-quiz-attempt")return;var f=document.getElementById("responseform")||b;["copy","cut","paste","contextmenu","dragstart"].forEach(function(e){f.addEventListener(e,function(ev){ev.preventDefault();ev.stopPropagation();},true);});})();</script>'

$block = @'
// ==== EAP MANAGED CONFIG (managed by Setup-MoodleNative.ps1 - do not hand edit) ==
$CFG->sslproxy          = __SSLPROXY__;
$CFG->reverseproxy      = __REVPROXY__;
$CFG->cachejs           = true;
$CFG->enablestats       = false;
$CFG->pathtophp         = '__PHPEXE__';
$CFG->autosavefrequency = 15;
$CFG->additionalhtmlfooter = '__FOOTERJS__';
// ==== /EAP MANAGED CONFIG ==========================================
'@
$block = $block.Replace('__SSLPROXY__', $sslProxy).
                Replace('__REVPROXY__', $revProxy).
                Replace('__PHPEXE__', ($EapPhpExe -replace '\\', '/')).
                Replace('__FOOTERJS__', $footerJs)

$cfgTxt = Get-Content -Raw -LiteralPath $configPhp
if ($cfgTxt -notmatch 'EAP MANAGED CONFIG') {
    $marker = "require_once(__DIR__ . '/lib/setup.php');"
    if ($cfgTxt.Contains($marker)) { $cfgTxt = $cfgTxt.Replace($marker, "$block`r`n`r`n$marker") }
    else { $cfgTxt = $cfgTxt.TrimEnd() + "`r`n`r`n$block`r`n" }
    # MUST be BOM-less: a UTF-8 BOM in config.php is emitted before any HTTP
    # header -> "headers already sent" -> Moodle "Couldn't start session".
    # (PowerShell 5.1 'Set-Content -Encoding UTF8' writes a BOM - do not use it.)
    [System.IO.File]::WriteAllText($configPhp, $cfgTxt, (New-Object System.Text.UTF8Encoding($false)))
    Write-EapOk 'config.php hardened (EAP managed block injected)'
} else {
    Write-EapOk 'config.php already hardened'
}

# =====================================================================
#  8. upgrade + post-install tuning (theme / SCSS / SEB / proctoring / role)
# =====================================================================
Write-EapInfo 'Running admin\cli\upgrade.php (installs plugin schemas)...'
$rc = Invoke-Native $EapPhpExe @((Join-Path $EapMoodleDir 'admin\cli\upgrade.php'), '--non-interactive', '--allow-unstable')
if ($rc -ne 0) { Write-EapWarn "upgrade.php returned $rc (continuing)" }

Copy-Item -Force (Join-Path $EapProjectRoot 'config\moodle\custom.scss') (Join-Path $EapInstallRoot 'custom.scss')
$env:EAP_MOODLE_CONFIG = $configPhp
$env:EAP_CUSTOM_SCSS   = (Join-Path $EapInstallRoot 'custom.scss')
$env:SEB_QUIT_PASSWORD = $EapSebQuitPw
$env:TZ                = $EapTz
Write-EapInfo 'Running scripts\moodle\post_install.php ...'
$rc = Invoke-Native $EapPhpExe @((Join-Path $EapProjectRoot 'scripts\moodle\post_install.php'))
if ($rc -ne 0) { Write-EapWarn "post_install.php returned $rc (non-fatal)" }

Invoke-Native $EapPhpExe @((Join-Path $EapMoodleDir 'admin\cli\purge_caches.php')) | Out-Null

# =====================================================================
#  9. Apache service + cron scheduled task + firewall
# =====================================================================
if (-not $SkipServices) {
    if (Get-Service -Name $EapApacheSvc -ErrorAction SilentlyContinue) {
        Stop-Service $EapApacheSvc -Force -ErrorAction SilentlyContinue
        Invoke-Native $httpdExe @('-k', 'uninstall', '-n', $EapApacheSvc) | Out-Null
        Start-Sleep 1
    }
    $rc = Invoke-Native $httpdExe @('-k', 'install', '-n', $EapApacheSvc, '-f', "$($EapApacheDir -replace '\\','/')/conf/httpd.conf")
    if ($rc -ne 0) { throw "httpd -k install failed (exit $rc)" }
    Set-Service -Name $EapApacheSvc -StartupType Automatic

    # mod_php loads php_curl/php_openssl/php_sodium/php_intl/php_ldap, which in turn
    # need PHP's bundled dependency DLLs (libcrypto-3-x64, libsodium, icu*, libssh2,
    # libsasl). httpd.exe's own dir does not contain them and copying is unsafe
    # (Apache ships its own libcrypto/libssl). Give just this service a PATH that
    # starts with the PHP dir - the SCM applies it on next start, no reboot needed.
    $sysPath = [Environment]::GetEnvironmentVariable('Path', 'Machine')
    New-ItemProperty -Path "HKLM:\SYSTEM\CurrentControlSet\Services\$EapApacheSvc" `
        -Name Environment -PropertyType MultiString -Force -Value @("PATH=$EapPhpDir;$sysPath") | Out-Null
    if ($sysPath -notlike "*$EapPhpDir*") {
        [Environment]::SetEnvironmentVariable('Path', "$($sysPath.TrimEnd(';'));$EapPhpDir", 'Machine')
    }

    Start-Service $EapApacheSvc
    if (Wait-Tcp -Port $EapPort -TimeoutSec 30) { Write-EapOk "Apache service '$EapApacheSvc' listening on $EapPort" }
    else { Write-EapWarn "Apache service started but port $EapPort not answering yet" }

    # 1-minute Moodle cron - schtasks.exe is far more reliable here than the
    # ScheduledTasks cmdlets (the Repetition-clone trick can silently produce an
    # invalid task).
    # No inner quotes: default paths under native\stack have no spaces, and nested
    # quoting through PowerShell -> schtasks.exe is unreliable. (If -InstallRoot has
    # a space, register the cron task by hand - see README-NATIVE.md.)
    $cronTr = "$EapPhpExe $(Join-Path $EapMoodleDir 'admin\cli\cron.php')"
    $rc = Invoke-Native 'schtasks.exe' @('/create', '/tn', $EapCronTask, '/tr', $cronTr,
        '/sc', 'minute', '/mo', '1', '/ru', 'SYSTEM', '/rl', 'HIGHEST', '/f')
    if ($rc -eq 0) { Write-EapOk "cron scheduled task '$EapCronTask' registered (every 1 min)" }
    else { Write-EapWarn "cron task registration returned $rc - create it manually (see README-NATIVE.md)" }

    New-NetFirewallRule -DisplayName "EAP Moodle HTTP $EapPort" -Direction Inbound -Action Allow `
        -Protocol TCP -LocalPort $EapPort -ErrorAction SilentlyContinue | Out-Null
} else {
    Write-EapWarn '-SkipServices: Apache/cron not registered'
}

# =====================================================================
#  10. default Safe Exam Browser config
# =====================================================================
try {
    & (Join-Path $EapNativeRoot 'Make-Seb.ps1') -Url $EapWwwRoot -QuitPassword $EapSebQuitPw -Name 'exam-default-generated'
} catch { Write-EapWarn "SEB generation skipped: $_" }

# =====================================================================
#  summary
# =====================================================================
Write-Host ''
Write-Host '======================================================================' -ForegroundColor Green
Write-Host ' NATIVE MOODLE STACK - READY' -ForegroundColor Green
Write-Host '======================================================================' -ForegroundColor Green
Write-Host "  Portal URL      : $EapWwwRoot"
Write-Host "  Admin login     : $EapAdminUser  /  (see .env MOODLE_ADMIN_PASS)"
Write-Host "  Moodle code     : $EapMoodleDir"
Write-Host "  Moodle data     : $EapDataRoot"
Write-Host "  PHP             : $EapPhpExe"
Write-Host "  Services        : $EapApacheSvc , $EapMariaSvc   (Automatic)"
Write-Host "  Cron task       : $EapCronTask (every 1 min)"
Write-Host "  SEB config      : native\..\seb\generated\exam-default-generated.seb"
Write-Host ''
Write-Host "  Manage          : .\native\Manage-Moodle.ps1 status | stop | start | restart | audit"
Write-Host "  Health check    : .\native\Audit-MoodleNative.ps1"
Write-Host "  Import students : .\native\Import-Students.ps1 -Csv .\scripts\students_sample.csv"
Write-Host ''
