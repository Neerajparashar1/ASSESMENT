#Requires -Version 5.1
<#
.SYNOPSIS
  Operations wrapper for the native-Windows Moodle stack.

.EXAMPLE
  .\native\Manage-Moodle.ps1 status
  .\native\Manage-Moodle.ps1 stop | start | restart
  .\native\Manage-Moodle.ps1 cron        # run one cron cycle now
  .\native\Manage-Moodle.ps1 purge       # purge Moodle caches
  .\native\Manage-Moodle.ps1 upgrade     # admin/cli/upgrade.php --non-interactive
  .\native\Manage-Moodle.ps1 audit       # -> Audit-MoodleNative.ps1
  .\native\Manage-Moodle.ps1 seb         # regenerate SEB config from .env
  .\native\Manage-Moodle.ps1 import <csv>
  .\native\Manage-Moodle.ps1 backup      # DB dump + moodledata archive -> backups\
#>
[CmdletBinding()]
param(
    [Parameter(Position = 0)][ValidateSet('status', 'start', 'stop', 'restart', 'cron', 'purge', 'upgrade', 'audit', 'seb', 'import', 'backup', 'help')]
    [string]$Command = 'help',
    [Parameter(Position = 1, ValueFromRemainingArguments)][string[]]$Rest
)
$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\_env.ps1"

function Need-Admin {
    $p = New-Object Security.Principal.WindowsPrincipal([Security.Principal.WindowsIdentity]::GetCurrent())
    if (-not $p.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
        throw 'This command needs an elevated PowerShell (Run as administrator).'
    }
}
function Svc { param($n) Get-Service -Name $n -ErrorAction SilentlyContinue }

switch ($Command) {
    'status' {
        foreach ($n in @($EapMariaSvc, $EapApacheSvc)) {
            $s = Svc $n
            "{0,-14} {1}" -f $n, $(if ($s) { $s.Status } else { 'NOT INSTALLED' })
        }
        $t = Get-ScheduledTask -TaskName $EapCronTask -ErrorAction SilentlyContinue
        "{0,-14} {1}" -f $EapCronTask, $(if ($t) { $t.State } else { 'NOT REGISTERED' })
        try {
            $r = Invoke-WebRequest "http://localhost:$EapPort/login/index.php" -TimeoutSec 8 -UseBasicParsing
            "HTTP :$EapPort   $($r.StatusCode) OK   ($EapWwwRoot)"
        } catch { "HTTP :$EapPort   DOWN - $($_.Exception.Message)" }
    }
    'start'   { Need-Admin; Start-Service $EapMariaSvc; Start-Service $EapApacheSvc; & $PSCommandPath status }
    'stop'    { Need-Admin; Stop-Service $EapApacheSvc -Force -ErrorAction SilentlyContinue; Stop-Service $EapMariaSvc -Force -ErrorAction SilentlyContinue; & $PSCommandPath status }
    'restart' { Need-Admin; Restart-Service $EapMariaSvc -Force; Restart-Service $EapApacheSvc -Force; & $PSCommandPath status }
    'cron'    { & $EapPhpExe (Join-Path $EapMoodleDir 'admin\cli\cron.php') }
    'purge'   { & $EapPhpExe (Join-Path $EapMoodleDir 'admin\cli\purge_caches.php') }
    'upgrade' { & $EapPhpExe (Join-Path $EapMoodleDir 'admin\cli\upgrade.php') --non-interactive }
    'audit'   { & (Join-Path $PSScriptRoot 'Audit-MoodleNative.ps1') }
    'seb'     { & (Join-Path $PSScriptRoot 'Make-Seb.ps1') @Rest }
    'import'  {
        if (-not $Rest) { throw 'usage: Manage-Moodle.ps1 import <path-to-csv> [-Update]' }
        & (Join-Path $PSScriptRoot 'Import-Students.ps1') -Csv $Rest[0] @($Rest | Select-Object -Skip 1)
    }
    'backup'  {
        $ts = Get-Date -Format 'yyyyMMdd-HHmmss'
        $dir = Join-Path $EapProjectRoot 'backups'
        New-Item -ItemType Directory -Force -Path $dir | Out-Null
        $dump = Join-Path $EapMariaDir 'bin\mariadb-dump.exe'
        if (-not (Test-Path $dump)) { $dump = Join-Path $EapMariaDir 'bin\mysqldump.exe' }
        $sql = Join-Path $dir "moodle-$ts.sql"
        & $dump '--host=127.0.0.1' '--user=root' "--password=$EapDbRootPass" '--single-transaction' '--routines' $EapDbName |
            Out-File -LiteralPath $sql -Encoding utf8
        $arch = Join-Path $dir "moodledata-$ts.zip"
        Compress-Archive -Path (Join-Path $EapDataRoot '*') -DestinationPath $arch -Force
        Write-Host "  $sql"
        Write-Host "  $arch"
    }
    default {
        Get-Help -Detailed $PSCommandPath
    }
}
