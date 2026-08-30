#Requires -Version 5.1
<#
.SYNOPSIS
  Safe Exam Browser config generator (native-Windows port of scripts\make-seb.sh).
  Produces a locked-down kiosk .seb file for a given portal URL + quit password.

.EXAMPLE
  .\native\Make-Seb.ps1
  # uses .env values, writes seb\generated\exam-<timestamp>.seb

.EXAMPLE
  .\native\Make-Seb.ps1 -Url https://exams.example.edu -QuitPassword 'S3cr3t!' -Name final-exam
#>
[CmdletBinding()]
param(
    [string]$Url,
    [string]$QuitPassword,
    [string]$Name
)
$ErrorActionPreference = 'Stop'
. "$PSScriptRoot\_env.ps1"

if (-not $Url)          { $Url = $EapWwwRoot }
if (-not $QuitPassword) { $QuitPassword = $EapSebQuitPw }
if (-not $Name)         { $Name = "exam-$(Get-Date -Format 'yyyyMMdd-HHmmss')" }

$Url = $Url.TrimEnd('/')
$hostPort = ($Url -replace '^https?://', '') -replace '/.*$', ''

# SHA-256 hex of the quit password, as SEB stores it
$sha = [System.Security.Cryptography.SHA256]::Create()
$hash = ($sha.ComputeHash([Text.Encoding]::UTF8.GetBytes($QuitPassword)) |
         ForEach-Object { $_.ToString('x2') }) -join ''

$template = Join-Path $EapProjectRoot 'seb\exam-default.seb'
if (-not (Test-Path $template)) { throw "Template not found: $template" }
$xml = Get-Content -Raw -LiteralPath $template

$placeholderHash = 'da833dc36caf876a515fe4c574612c2988a87799e478e3335ed63d05e25d43a2'
$xml = $xml.
    Replace('http://localhost:8080/login/index.php',                        "$Url/login/index.php").
    Replace('http://localhost:8080/mod/quiz/accessrule/seb/config.php',     "$Url/mod/quiz/accessrule/seb/config.php").
    Replace($placeholderHash, $hash).
    Replace('localhost:8080/*;127.0.0.1:8080/*',                            "$hostPort/*").
    Replace('localhost:8080/*',                                             "$hostPort/*")

$outDir = Join-Path $EapProjectRoot 'seb\generated'
New-Item -ItemType Directory -Force -Path $outDir | Out-Null
$out = Join-Path $outDir "$Name.seb"
Set-Content -LiteralPath $out -Value $xml -Encoding UTF8

Write-Host "Generated: $out"
Write-Host "  start URL : $Url/login/index.php"
Write-Host "  quit host : $hostPort"
Write-Host "  quit hash : $hash"
Write-Host ''
Write-Host "Next: in the Moodle quiz -> 'Safe Exam Browser' section ->"
Write-Host "      'Require the use of Safe Exam Browser' = 'Yes - Upload my own config'"
Write-Host "      then upload the file above."
