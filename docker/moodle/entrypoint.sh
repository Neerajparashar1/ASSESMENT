#!/bin/bash
# =====================================================================
#  Moodle container entrypoint
#  Roles (env CONTAINER_ROLE):
#    web      -> seed code, install/upgrade, harden config, run Apache
#    cron     -> wait for install, then run admin/cli/cron.php every 60s
#  First arg "cron-loop" also selects the cron role.
# =====================================================================
set -euo pipefail

MOODLE_DIR=/var/www/html/moodle
SRC_DIR=/usr/src/moodle
DATA_DIR=/var/moodledata
PHP=/usr/local/bin/php
ROLE="${CONTAINER_ROLE:-web}"
[ "${1:-}" = "cron-loop" ] && ROLE="cron"

log() { echo "[entrypoint $(date -u +%H:%M:%S)] $*"; }

# ---------------------------------------------------------------
seed_code() {
  if [ ! -f "$MOODLE_DIR/version.php" ]; then
    log "Seeding Moodle code into volume ..."
    mkdir -p "$MOODLE_DIR"
    cp -a "$SRC_DIR/." "$MOODLE_DIR/"
  else
    # image rebuilt with new/updated plugins -> refresh plugin + core code
    log "Refreshing code from image layer (preserving config.php) ..."
    rsync -a --delete \
      --exclude '/config.php' \
      "$SRC_DIR/" "$MOODLE_DIR/" 2>/dev/null || {
        # rsync not present: fall back to cp (no delete)
        cp -a "$SRC_DIR/." "$MOODLE_DIR/"
      }
  fi
  mkdir -p "$DATA_DIR"
  chown -R www-data:www-data "$MOODLE_DIR" "$DATA_DIR"
  chmod 2770 "$DATA_DIR" || true
}

wait_for_db() {
  log "Waiting for database ${DB_HOST} ..."
  local ping="mysqladmin"
  command -v mariadb-admin >/dev/null 2>&1 && ping="mariadb-admin"
  for i in $(seq 1 60); do
    if "$ping" ping -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" --silent 2>/dev/null; then
      log "Database is up."; return 0
    fi
    sleep 3
  done
  log "ERROR: database not reachable"; return 1
}

install_moodle() {
  log "Running non-interactive CLI installation ..."
  runuser -u www-data -- "$PHP" "$MOODLE_DIR/admin/cli/install.php" \
    --lang=en \
    --wwwroot="$MOODLE_WWWROOT" \
    --dataroot="$DATA_DIR" \
    --dbtype=mariadb \
    --dbhost="$DB_HOST" \
    --dbname="$DB_NAME" \
    --dbuser="$DB_USER" \
    --dbpass="$DB_PASS" \
    --dbport=3306 \
    --prefix=mdl_ \
    --fullname="$MOODLE_SITE_FULLNAME" \
    --shortname="$MOODLE_SITE_SHORTNAME" \
    --summary="AI-Proctored Online Assessment Platform" \
    --adminuser="$MOODLE_ADMIN_USER" \
    --adminpass="$MOODLE_ADMIN_PASS" \
    --adminemail="$MOODLE_ADMIN_EMAIL" \
    --non-interactive \
    --agree-license
  log "Core installation finished."
}

harden_config() {
  # Idempotently inject managed settings just before lib/setup.php require.
  local cfg="$MOODLE_DIR/config.php"
  [ -f "$cfg" ] || { log "WARN config.php missing, cannot harden"; return 0; }
  grep -q "EAP MANAGED CONFIG" "$cfg" 2>/dev/null && { log "config.php already hardened."; return 0; }
  log "Injecting managed hardening / performance config ..."

  cat > /tmp/eap-managed.php <<'PHPBLOCK'
// ==== EAP MANAGED CONFIG (managed by entrypoint - do not hand edit) ==
$CFG->sslproxy      = (getenv('MOODLE_SSLPROXY') === 'true');
$CFG->reverseproxy  = (getenv('MOODLE_REVERSEPROXY') === 'true');

// ---- Redis: session handler + application cache (network-drop safe) --
$CFG->session_handler_class = '\core\session\redis';
$CFG->session_redis_host    = 'redis';
$CFG->session_redis_port    = 6379;
$CFG->session_redis_prefix  = 'eap_sess_';
$CFG->session_redis_acquire_lock_timeout = 120;
$CFG->session_redis_lock_expire          = 7200;

// ---- Performance -------------------------------------------------
$CFG->cachejs        = true;
$CFG->enablestats    = false;
$CFG->pathtophp      = '/usr/local/bin/php';
$CFG->pathtodu       = '/usr/bin/du';
$CFG->pathtodot      = '/usr/bin/dot';
$CFG->pathtogs       = '/usr/bin/gs';
$CFG->pathtopython   = '/usr/bin/python3';

// ---- Exam resilience: frequent client auto-save (network drops) -----
$CFG->autosavefrequency = 15;

// ---- Anti-cheat: block copy/cut/paste/right-click on quiz attempts --
$CFG->additionalhtmlfooter = '<script>(function(){var b=document.body;if(!b||b.id!=="page-mod-quiz-attempt")return;var f=document.getElementById("responseform")||b;["copy","cut","paste","contextmenu","dragstart"].forEach(function(e){f.addEventListener(e,function(ev){ev.preventDefault();ev.stopPropagation();},true);});})();</script>';
// ==== /EAP MANAGED CONFIG ==========================================
PHPBLOCK

  python3 - "$cfg" /tmp/eap-managed.php <<'PYEOF'
import sys
cfg_path, block_path = sys.argv[1], sys.argv[2]
marker = "require_once(__DIR__ . '/lib/setup.php');"
block = open(block_path, encoding='utf-8').read().rstrip() + "\n\n"
src = open(cfg_path, encoding='utf-8').read()
if marker in src:
    src = src.replace(marker, block + marker, 1)
else:
    src = src.rstrip() + "\n\n" + block
open(cfg_path, 'w', encoding='utf-8').write(src)
print("config.php patched")
PYEOF
  rm -f /tmp/eap-managed.php
  chown www-data:www-data "$cfg"
}

post_install_tuning() {
  if [ -f /opt/eap/scripts/post_install.php ]; then
    log "Applying theme + anti-cheat + role tuning ..."
    runuser -u www-data -- "$PHP" /opt/eap/scripts/post_install.php || \
      log "WARN post_install.php reported issues (non-fatal)"
  fi
}

upgrade_moodle() {
  log "Running admin/cli/upgrade.php --non-interactive ..."
  runuser -u www-data -- "$PHP" "$MOODLE_DIR/admin/cli/upgrade.php" \
      --non-interactive --allow-unstable || \
      log "WARN upgrade.php returned non-zero"
}

purge() {
  runuser -u www-data -- "$PHP" "$MOODLE_DIR/admin/cli/purge_caches.php" || true
}

# =====================================================================
if [ "$ROLE" = "cron" ]; then
  log "CRON runner starting."
  # Wait until the web container has completed installation.
  for i in $(seq 1 120); do
    [ -f "$MOODLE_DIR/config.php" ] && break
    sleep 5
  done
  log "config.php present - entering 60s cron loop."
  while true; do
    runuser -u www-data -- "$PHP" "$MOODLE_DIR/admin/cli/cron.php" >/dev/null 2>&1 || \
      log "cron.php returned non-zero (will retry)"
    sleep 60
  done
fi

# ---- web role -------------------------------------------------
seed_code
wait_for_db

if [ ! -f "$MOODLE_DIR/config.php" ]; then
  install_moodle
  harden_config
  upgrade_moodle          # installs the bundled contrib plugins non-interactively
  post_install_tuning
else
  log "Existing installation detected."
  harden_config
  upgrade_moodle
  post_install_tuning
fi

purge
chown -R www-data:www-data "$DATA_DIR"

log "Starting: $*"
exec "$@"
