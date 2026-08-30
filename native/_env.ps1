# =====================================================================
#  Shared configuration for the native-Windows Moodle stack.
#  Dot-sourced by every other native\*.ps1 script:  . "$PSScriptRoot\_env.ps1"
#  When dot-sourced, $PSScriptRoot below resolves to E:\ASSESMENT\native.
# =====================================================================

$EapNativeRoot  = $PSScriptRoot
$EapProjectRoot = Split-Path -Parent $PSScriptRoot          # E:\ASSESMENT

function Read-EapEnv {
    param([string]$Path = (Join-Path $EapProjectRoot '.env'))
    $h = @{}
    if (Test-Path -LiteralPath $Path) {
        foreach ($line in Get-Content -LiteralPath $Path) {
            $t = $line.Trim()
            if ($t -and -not $t.StartsWith('#') -and $t.Contains('=')) {
                $kv = $t -split '=', 2
                $h[$kv[0].Trim()] = $kv[1].Trim()
            }
        }
    }
    return $h
}

$EapEnv = Read-EapEnv

# ---- Layout (override InstallRoot / DataRoot via Setup-MoodleNative.ps1) ----
$EapInstallRoot = Join-Path $EapNativeRoot  'stack'
$EapDataRoot    = Join-Path $EapProjectRoot 'moodledata'
$EapMoodleDir   = Join-Path $EapInstallRoot 'moodle'
$EapPhpDir      = Join-Path $EapInstallRoot 'php'
$EapPhpExe      = Join-Path $EapPhpDir      'php.exe'
$EapApacheDir   = Join-Path $EapInstallRoot 'Apache24'
$EapMariaDir    = Join-Path $EapInstallRoot 'mariadb'
$EapMariaData   = Join-Path $EapInstallRoot 'mariadb-data'
$EapLogDir      = Join-Path $EapInstallRoot 'logs'
$EapTmpDir      = Join-Path $EapInstallRoot 'tmp'
$EapDlDir       = Join-Path $EapInstallRoot '_downloads'

# ---- Service names ----
$EapApacheSvc = 'eap-apache'
$EapMariaSvc  = 'eap-mariadb'
$EapCronTask  = 'EAP-Moodle-Cron'

# ---- Values from .env (with sane fallbacks) ----
$EapPort       = if ($EapEnv.HTTP_PORT)          { [int]$EapEnv.HTTP_PORT } else { 8080 }
$EapWwwRoot    = if ($EapEnv.MOODLE_WWWROOT)     { $EapEnv.MOODLE_WWWROOT } else { "http://localhost:$EapPort" }
$EapDbName     = if ($EapEnv.DB_NAME)            { $EapEnv.DB_NAME }        else { 'moodle' }
$EapDbUser     = if ($EapEnv.DB_USER)            { $EapEnv.DB_USER }        else { 'moodleuser' }
$EapDbPass     = $EapEnv.DB_PASS
$EapDbRootPass = $EapEnv.DB_ROOT_PASS
$EapAdminUser  = if ($EapEnv.MOODLE_ADMIN_USER)  { $EapEnv.MOODLE_ADMIN_USER }  else { 'examadmin' }
$EapAdminPass  = $EapEnv.MOODLE_ADMIN_PASS
$EapAdminEmail = if ($EapEnv.MOODLE_ADMIN_EMAIL) { $EapEnv.MOODLE_ADMIN_EMAIL } else { 'admin@example.com' }
$EapSiteFull   = if ($EapEnv.MOODLE_SITE_FULLNAME)  { $EapEnv.MOODLE_SITE_FULLNAME }  else { 'Enterprise Assessment Portal' }
$EapSiteShort  = if ($EapEnv.MOODLE_SITE_SHORTNAME) { $EapEnv.MOODLE_SITE_SHORTNAME } else { 'EAP' }
$EapSebQuitPw  = if ($EapEnv.SEB_QUIT_PASSWORD)  { $EapEnv.SEB_QUIT_PASSWORD }  else { 'changeme' }
$EapInnoBuf    = if ($EapEnv.INNODB_BUFFER_POOL_SIZE) { $EapEnv.INNODB_BUFFER_POOL_SIZE } else { '1G' }
$EapTz         = if ($EapEnv.TZ)                 { $EapEnv.TZ }             else { 'UTC' }

function Write-EapInfo  { param($m) Write-Host "[eap] $m"            -ForegroundColor Cyan }
function Write-EapOk    { param($m) Write-Host "[ ok] $m"            -ForegroundColor Green }
function Write-EapWarn  { param($m) Write-Host "[warn] $m"           -ForegroundColor Yellow }
function Write-EapErr   { param($m) Write-Host "[FAIL] $m"           -ForegroundColor Red }
