<#
  Convenience wrapper for the AI-Proctored Assessment Platform.
  Usage:  .\manage.ps1 <command>
  Commands:
    build      docker compose build
    up         docker compose up -d   (+ waits for health)
    down       docker compose down
    destroy    docker compose down -v  (DELETES the database + moodledata)
    logs       follow moodle logs
    audit      python scripts\audit.py
    shell      bash inside the moodle container
    cron       run one cron cycle now
    purge      purge Moodle caches
    seb        regenerate SEB config from .env
    vpl        start the VPL coding-jail service
    backup     dump database + moodledata to .\backups\
#>
param([Parameter(Position = 0)][string]$cmd = "help")

$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

function Wait-Health {
  Write-Host "Waiting for moodle container to become healthy..."
  for ($i = 0; $i -lt 60; $i++) {
    $h = docker inspect --format '{{.State.Health.Status}}' eap-moodle 2>$null
    if ($h -eq "healthy") { Write-Host "moodle is healthy."; return }
    Start-Sleep 10
  }
  Write-Warning "Timed out waiting for health; check: docker compose logs moodle"
}

switch ($cmd) {
  "build"   { docker compose build }
  "up"      { docker compose up -d; Wait-Health; Write-Host "`nPortal: $((Get-Content .env | Select-String '^MOODLE_WWWROOT=') -replace 'MOODLE_WWWROOT=','')" }
  "down"    { docker compose down }
  "destroy" {
    $a = Read-Host "This DELETES all data (db + moodledata). Type 'yes' to confirm"
    if ($a -eq "yes") { docker compose down -v } else { Write-Host "aborted" }
  }
  "logs"    { docker compose logs -f moodle }
  "audit"   { python scripts\audit.py }
  "shell"   { docker compose exec moodle bash }
  "cron"    { docker compose exec -T moodle runuser -u www-data -- php admin/cli/cron.php }
  "purge"   { docker compose exec -T moodle runuser -u www-data -- php admin/cli/purge_caches.php }
  "seb"     { bash scripts/make-seb.sh }
  "vpl"     { docker compose --profile vpl up -d vpl-jail }
  "backup"  {
    New-Item -ItemType Directory -Force backups | Out-Null
    $ts = Get-Date -Format "yyyyMMdd-HHmmss"
    docker compose exec -T mariadb sh -c 'exec mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction --routines moodle' |
      Out-File -Encoding utf8 "backups\moodle-$ts.sql"
    docker run --rm -v assesment_moodledata:/data -v "${PWD}\backups:/out" alpine tar czf "/out/moodledata-$ts.tgz" -C /data .
    Write-Host "backups\moodle-$ts.sql  +  backups\moodledata-$ts.tgz"
  }
  default   { Get-Help $PSCommandPath -Detailed }
}
