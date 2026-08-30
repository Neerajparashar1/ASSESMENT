#Requires -Version 5.1
<#
.SYNOPSIS
  Phase-6 health check for the native-Windows stack (port of scripts\audit.py).
  Exits non-zero if any hard check fails.
#>
[CmdletBinding()] param()
$ErrorActionPreference = 'Continue'
. "$PSScriptRoot\_env.ps1"

$script:PASS = 0; $script:FAIL = 0; $script:WARN = 0
function chk  { param($label, $ok, $detail = '')
    if ($ok) { $script:PASS++; $m = 'PASS' } else { $script:FAIL++; $m = 'FAIL' }
    Write-Host ("  [{0}] {1}{2}" -f $m, $label, $(if ($detail) { "  -- $detail" })) `
        -ForegroundColor $(if ($ok) { 'Green' } else { 'Red' })
}
function wrn  { param($label, $detail = '') $script:WARN++
    Write-Host ("  [WARN] {0}{1}" -f $label, $(if ($detail) { "  -- $detail" })) -ForegroundColor Yellow }
function sect { param($t) Write-Host "`n=== $t ===" }

Write-Host '======================================================================'
Write-Host ' AI-PROCTORED ASSESSMENT PLATFORM -- NATIVE STACK AUDIT'
Write-Host '======================================================================'

# 1. services -----------------------------------------------------
sect '1. Windows services'
foreach ($s in @($EapApacheSvc, $EapMariaSvc)) {
    $svc = Get-Service -Name $s -ErrorAction SilentlyContinue
    chk "service '$s' running" ($svc -and $svc.Status -eq 'Running') $($svc.Status)
}
$task = Get-ScheduledTask -TaskName $EapCronTask -ErrorAction SilentlyContinue
if (-not $task) {
    # fall back to schtasks.exe (Get-ScheduledTask can be blind to a /ru SYSTEM task)
    $st = & schtasks.exe /query /tn $EapCronTask /fo LIST 2>$null
    if ($LASTEXITCODE -eq 0 -and $st) { $task = [pscustomobject]@{ State = 'Ready (via schtasks)' } }
}
$elevated = ([Security.Principal.WindowsPrincipal][Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
if ($task) {
    chk "cron task '$EapCronTask' present" $true "$($task.State)"
} elseif (-not $elevated) {
    wrn "cron task '$EapCronTask' - cannot read a SYSTEM task from a non-elevated shell; re-run this audit elevated to verify"
} else {
    chk "cron task '$EapCronTask' present" $false 'not found'
}
if ($task -and $task.State -notmatch 'schtasks') {
    $info = Get-ScheduledTaskInfo -TaskName $EapCronTask -ErrorAction SilentlyContinue
    if ($info) { wrn "cron last run: $($info.LastRunTime)  result: 0x$('{0:X}' -f $info.LastTaskResult)" }
}

# 2. PHP --------------------------------------------------------
sect '2. PHP runtime'
if (Test-Path $EapPhpExe) {
    $pv = (& $EapPhpExe -r 'echo PHP_VERSION;')
    $maj = [int]($pv.Split('.')[0]); $min = [int]($pv.Split('.')[1])
    chk 'PHP >= 8.1' (($maj -gt 8) -or ($maj -eq 8 -and $min -ge 1)) $pv
    $mods = (& $EapPhpExe -m) -join "`n" | ForEach-Object { $_.ToLower() }
    foreach ($e in 'curl', 'gd', 'intl', 'mbstring', 'soap', 'zip', 'sodium', 'openssl', 'mysqli', 'exif', 'iconv', 'fileinfo') {
        chk "php ext: $e" ($mods -match "(?m)^$e$")
    }
    # php -m reports OPcache as "Zend OPcache"
    chk 'php ext: opcache' (($mods -match '(?m)^opcache$') -or ($mods -match 'zend opcache'))
    # NOTE: read these from php.ini, not `php -r` - the CLI SAPI forces
    # max_execution_time=0 regardless of the ini, giving a false negative.
    $iniFile = Join-Path $EapPhpDir 'php.ini'
    $iniTxt = if (Test-Path $iniFile) { Get-Content -Raw $iniFile } else { '' }
    function IniVal($k) { if ($iniTxt -match "(?m)^\s*$([regex]::Escape($k))\s*=\s*(.+?)\s*$") { $Matches[1] } else { '(unset)' } }
    chk 'memory_limit = 512M'        ((IniVal 'memory_limit') -eq '512M')            (IniVal 'memory_limit')
    chk 'upload_max_filesize = 100M' ((IniVal 'upload_max_filesize') -eq '100M')     (IniVal 'upload_max_filesize')
    chk 'post_max_size = 100M'       ((IniVal 'post_max_size') -eq '100M')           (IniVal 'post_max_size')
    chk 'max_execution_time = 300'   ((IniVal 'max_execution_time') -eq '300')       (IniVal 'max_execution_time')
    chk 'max_input_vars = 5000'      ((IniVal 'max_input_vars') -eq '5000')          (IniVal 'max_input_vars')
    $oe = & $EapPhpExe -r "echo ini_get('opcache.enable');"
    chk 'opcache.enable = 1'         ($oe -in '1', 'On')                             $oe
} else { chk 'php.exe present' $false $EapPhpExe }

# 3. Apache ----------------------------------------------------
sect '3. Apache'
$httpd = Join-Path $EapApacheDir 'bin\httpd.exe'
if (Test-Path $httpd) {
    $t = (& $httpd -t 2>&1) -join ' '
    chk 'apache config syntax OK' ($t -match 'Syntax OK') $t.Trim()
    $mods = (& $httpd -M 2>&1) -join ' '
    chk 'mod_rewrite loaded' ($mods -match 'rewrite_module')
    chk 'mod_headers loaded' ($mods -match 'headers_module')
    chk 'mod_php loaded'     ($mods -match 'php_module')
} else { chk 'httpd.exe present' $false }

# 4. Database ---------------------------------------------------
sect '4. Database (MariaDB)'
$mysql = Join-Path $EapMariaDir 'bin\mariadb.exe'
if (-not (Test-Path $mysql)) { $mysql = Join-Path $EapMariaDir 'bin\mysql.exe' }
if ((Test-Path $mysql) -and $EapDbRootPass) {
    $q = & $mysql '--host=127.0.0.1' '--user=root' "--password=$EapDbRootPass" '--batch' '--skip-column-names' `
        '-e' 'SELECT @@character_set_server, @@collation_server, @@innodb_file_per_table, @@innodb_buffer_pool_size;'
    Write-Host "    $q"
    chk 'charset = utf8mb4'                 ($q -match 'utf8mb4')
    chk 'collation = utf8mb4_unicode_ci'    ($q -match 'utf8mb4_unicode_ci')
    chk 'innodb_file_per_table = 1'         ($q -match '\b1\b')
    $nt = & $mysql '--host=127.0.0.1' '--user=root' "--password=$EapDbRootPass" '--batch' '--skip-column-names' `
        '-e' "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='$EapDbName';"
    chk "database '$EapDbName' populated" ([int]$nt -gt 50) "$nt tables"
} else { wrn 'cannot introspect DB (mysql client or DB_ROOT_PASS missing)' }

# 5. Moodle config / data ------------------------------------------
sect '5. Moodle config / data dir'
$cfg = Join-Path $EapMoodleDir 'config.php'
if (Test-Path $cfg) {
    $c = Get-Content -Raw $cfg
    chk 'config.php exists'                 ($c -match 'wwwroot')
    chk 'EAP managed config injected'       ($c -match 'EAP MANAGED CONFIG')
    chk 'anti-cheat footer present'         ($c -match 'additionalhtmlfooter')
    chk 'pathtophp set'                     ($c -match 'pathtophp')
    chk "dataroot -> $EapDataRoot"          ($c -replace '\\\\', '\' -match [regex]::Escape($EapDataRoot))
} else { chk 'config.php exists' $false }
chk 'moodledata dir exists' (Test-Path $EapDataRoot)
try {
    $probe = Join-Path $EapDataRoot ('.w_' + [guid]::NewGuid().ToString('N'))
    Set-Content -LiteralPath $probe -Value 'x'; Remove-Item $probe
    chk 'moodledata writable' $true
} catch { chk 'moodledata writable' $false }

# 6. plugins / anti-cheat ----------------------------------------
sect '6. Plugins / anti-cheat'
$mustHave = @{
    'theme/boost_union'                = 'theme_boost_union'
    'mod/quiz/accessrule/seb'          = 'quizaccess_seb (core)'
}
$optional = @{
    'mod/quiz/accessrule/proctoring'   = 'quizaccess_proctoring'
    'mod/vpl'                          = 'mod_vpl (coding lab)'
}
foreach ($k in $mustHave.Keys) { chk "plugin present: $($mustHave[$k])" (Test-Path (Join-Path $EapMoodleDir "$k\version.php")) }
foreach ($k in $optional.Keys) {
    if (Test-Path (Join-Path $EapMoodleDir "$k\version.php")) { chk "plugin present: $($optional[$k])" $true }
    else { wrn "optional plugin not installed: $($optional[$k])" }
}

# 7. Moodle environment check ----------------------------------
sect '7. admin\cli\checks.php'
if (Test-Path $EapPhpExe) {
    $out = (& $EapPhpExe (Join-Path $EapMoodleDir 'admin\cli\checks.php') 2>&1) -join "`n"
    $out.Split("`n") | Select-Object -Last 20 | ForEach-Object { Write-Host "    $_" }
    chk 'no CRITICAL environment checks' ($out.ToUpper() -notmatch 'CRITICAL')
}

# 8. HTTP endpoint -------------------------------------------
sect '8. HTTP endpoint'
$u = "http://localhost:$EapPort/login/index.php"
try {
    $r = Invoke-WebRequest -Uri $u -TimeoutSec 15 -UseBasicParsing
    chk "GET $u -> $($r.StatusCode)" ($r.StatusCode -eq 200)
    chk 'login page renders Moodle'   ($r.Content -match '(?i)login')
} catch { chk "GET $u" $false $_.Exception.Message }

Write-Host "`n======================================================================"
Write-Host (" RESULT:  {0} passed   {1} failed   {2} warnings" -f $PASS, $FAIL, $WARN)
Write-Host '======================================================================'
exit $(if ($FAIL) { 1 } else { 0 })
