#Requires -Version 5.1
<#
.SYNOPSIS
  Bulk student onboarding for the native stack (no Docker).
  Validates a CSV, then drives Moodle's supported CLI uploader:
      admin\tool\uploaduser\cli\uploaduser.php

.PARAMETER Csv       Path to the students CSV (required).
.PARAMETER Update    Add new AND update existing users (uutype=3). Default: add-new-only.
.PARAMETER DryRun    Validate the CSV and stop.
.PARAMETER MoodleDir Override the Moodle code directory.

.EXAMPLE
  .\native\Import-Students.ps1 -Csv .\scripts\students_sample.csv
.EXAMPLE
  .\native\Import-Students.ps1 -Csv .\roll.csv -Update
#>
[CmdletBinding()]
param(
    [Parameter(Mandatory)][string]$Csv,
    [switch]$Update,
    [switch]$DryRun,
    [switch]$AllowSuspend,
    [string]$MoodleDir
)
$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\_env.ps1"
if ($MoodleDir) { $EapMoodleDir = $MoodleDir }

$required = @('username', 'firstname', 'lastname', 'email')

Write-Host '== Native bulk student onboarding =='
if (-not (Test-Path -LiteralPath $Csv)) { throw "CSV not found: $Csv" }

# ---- validate -----------------------------------------------------
$rows = @(Import-Csv -LiteralPath $Csv)
if ($rows.Count -eq 0) { throw 'CSV has a header but no data rows.' }
$headers = $rows[0].PSObject.Properties.Name
$missing = $required | Where-Object { $_ -notin $headers }
if ($missing) { throw "CSV missing required column(s): $($missing -join ', ')" }

$problems = New-Object System.Collections.Generic.List[string]
$seen = New-Object System.Collections.Generic.HashSet[string]
$ln = 1
foreach ($r in $rows) {
    $ln++
    $u = ("$($r.username)").Trim().ToLower()
    if (-not $u) { $problems.Add("line ${ln}: empty username") }
    elseif (-not $seen.Add($u)) { $problems.Add("line ${ln}: duplicate username '$u'") }
    if (("$($r.email)") -notmatch '@') { $problems.Add("line ${ln}: invalid email '$($r.email)'") }
    $pw = ("$($r.password)").Trim()
    if ($pw -and $pw.ToLower() -ne 'changeme' -and $pw.Length -lt 8) {
        $problems.Add("line ${ln}: weak password (<8 chars) for '$u'")
    }
}
Write-Host "  parsed $($rows.Count) student row(s); headers: $($headers -join ', ')"
if ($problems.Count) {
    Write-Host 'VALIDATION ISSUES:' -ForegroundColor Red
    $problems | ForEach-Object { Write-Host "  $_" -ForegroundColor Red }
    exit 1
}
if ($DryRun) { Write-Host "[dry-run] $($rows.Count) row(s) look valid. Nothing imported."; return }

# ---- import -----------------------------------------------------
$cli = Join-Path $EapMoodleDir 'admin\tool\uploaduser\cli\uploaduser.php'
if (-not (Test-Path $cli)) { throw "uploaduser.php not found - is Moodle installed at $EapMoodleDir ?" }
if (-not (Test-Path $EapPhpExe)) { throw "php.exe not found at $EapPhpExe - run Setup-MoodleNative.ps1 first." }

$full = (Resolve-Path -LiteralPath $Csv).Path
$opts = @(
    "--file=$full"
    '--delimiter_name=comma'
    '--encoding=UTF-8'
    ($(if ($Update) { '--uutype=3' } else { '--uutype=1' }))
    '--uuallowrenames=0'
    '--uuallowdeletes=0'
    ("--uuallowsuspends=" + $(if ($AllowSuspend) { '1' } else { '0' }))
    '--uunoemailduplicates=1'
    '--uustandardusernames=1'
)
Write-Host "  $ php uploaduser.php $($opts -join ' ')"
& $EapPhpExe $cli @opts
$code = $LASTEXITCODE
if ($code -eq 0) {
    Write-Host "`n== DONE == Verify at: Site administration > Users > Browse list of users" -ForegroundColor Green
} else {
    Write-Host "`n== FAILED == uploaduser.php exit $code" -ForegroundColor Red
    exit $code
}
