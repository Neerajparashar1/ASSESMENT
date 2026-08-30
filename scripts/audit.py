#!/usr/bin/env python3
# =====================================================================
#  PHASE 6 - FINAL SYSTEM AUDIT / HEALTH CHECK
#  Cross-platform (runs from Windows host). Drives `docker compose exec`
#  to inspect the live stack and runs Moodle's own CLI checks.
#
#    python scripts/audit.py
# =====================================================================
import os
import re
import subprocess
import sys
from pathlib import Path

PROJECT_DIR = Path(__file__).resolve().parent.parent
ENV = {}
envf = PROJECT_DIR / ".env"
if envf.is_file():
    for line in envf.read_text(encoding="utf-8").splitlines():
        line = line.strip()
        if line and not line.startswith("#") and "=" in line:
            k, v = line.split("=", 1)
            ENV[k.strip()] = v.strip()

PORT = ENV.get("HTTP_PORT", "8080")
DB_ROOT = ENV.get("DB_ROOT_PASS", "")
PASS = 0
FAIL = 0
WARN = 0


def run(cmd, **kw):
    return subprocess.run(cmd, cwd=PROJECT_DIR, capture_output=True, text=True, **kw)


def dexec(svc, *args):
    return run(["docker", "compose", "exec", "-T", svc, *args])


def check(label, ok, detail=""):
    global PASS, FAIL
    mark = "PASS" if ok else "FAIL"
    if ok:
        PASS += 1
    else:
        FAIL += 1
    print(f"  [{mark}] {label}" + (f"  -- {detail}" if detail else ""))


def warn(label, detail=""):
    global WARN
    WARN += 1
    print(f"  [WARN] {label}" + (f"  -- {detail}" if detail else ""))


def section(t):
    print(f"\n=== {t} ===")


print("======================================================================")
print(" AI-PROCTORED ASSESSMENT PLATFORM  --  PHASE 6 SYSTEM AUDIT")
print("======================================================================")

# ---------------------------------------------------------------
section("1. Containers")
ps = run(["docker", "compose", "ps", "--format", "{{.Service}} {{.State}} {{.Health}}"])
print(ps.stdout.rstrip() or ps.stderr.rstrip())
for svc in ("mariadb", "redis", "moodle", "cron"):
    up = run(["docker", "compose", "ps", "-q", svc]).stdout.strip()
    check(f"service '{svc}' present", bool(up))

# ---------------------------------------------------------------
section("2. Database (MariaDB)")
if DB_ROOT:
    q = dexec("mariadb", "mariadb", "-uroot", f"-p{DB_ROOT}", "-N", "-e",
              "SELECT @@character_set_server, @@collation_server, @@innodb_file_per_table, "
              "@@innodb_buffer_pool_size, @@innodb_default_row_format;")
    out = q.stdout.strip()
    print("   ", out or q.stderr.strip())
    check("charset = utf8mb4", "utf8mb4" in out)
    check("collation = utf8mb4_unicode_ci", "utf8mb4_unicode_ci" in out)
    check("innodb_file_per_table = 1", re.search(r"\b1\b", out) is not None)
    check("innodb_buffer_pool_size >= 512M",
          any(int(x) >= 536870912 for x in re.findall(r"\b(\d{9,})\b", out)) or "1073741824" in out)
    dbname = ENV.get("DB_NAME", "moodle")
    tq = dexec("mariadb", "mariadb", "-uroot", f"-p{DB_ROOT}", "-N", "-e",
               f"SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='{dbname}';")
    ntables = tq.stdout.strip()
    check(f"database '{dbname}' populated", ntables.isdigit() and int(ntables) > 50,
          f"{ntables} tables")
else:
    warn("DB_ROOT_PASS not in .env - skipping DB introspection")

# ---------------------------------------------------------------
section("3. PHP runtime")
v = dexec("moodle", "php", "-r", "echo PHP_VERSION;")
check("PHP >= 8.1", tuple(map(int, v.stdout.split(".")[:2])) >= (8, 1), v.stdout.strip())
mods = dexec("moodle", "php", "-m").stdout.lower()
for ext in ("gd", "zip", "mbstring", "xml", "curl", "intl", "soap", "opcache",
            "bcmath", "mysqli", "redis", "exif"):
    check(f"php ext: {ext}", ext in mods)
ini = dexec("moodle", "php", "-r",
            "echo ini_get('memory_limit').'|'.ini_get('upload_max_filesize').'|'"
            ".ini_get('post_max_size').'|'.ini_get('max_execution_time').'|'"
            ".ini_get('opcache.enable').'|'.ini_get('opcache.memory_consumption');").stdout
parts = ini.split("|")
if len(parts) == 6:
    ml, uf, pm, met, oe, omc = parts
    check("memory_limit = 512M", ml == "512M", ml)
    check("upload_max_filesize = 100M", uf == "100M", uf)
    check("post_max_size = 100M", pm == "100M", pm)
    check("max_execution_time = 300", met == "300", met)
    check("opcache.enable = 1", oe in ("1", "On"), oe)
    check("opcache.memory_consumption = 128", omc == "128", omc)

# ---------------------------------------------------------------
section("4. Apache")
t = dexec("moodle", "apache2ctl", "-t")
check("apache config syntax OK", "Syntax OK" in (t.stdout + t.stderr), (t.stdout + t.stderr).strip())
m = dexec("moodle", "apache2ctl", "-M").stdout
check("mod_rewrite loaded", "rewrite_module" in m)
check("mod_headers loaded", "headers_module" in m)
conf = dexec("moodle", "sh", "-c",
             "grep -R 'AllowOverride' /etc/apache2/sites-enabled/ 2>/dev/null").stdout
check("AllowOverride All in vhost", "AllowOverride All" in conf, conf.strip())

# ---------------------------------------------------------------
section("5. Moodle config / data dir")
cfg = dexec("moodle", "sh", "-c",
            "cat /var/www/html/moodle/config.php 2>/dev/null").stdout
check("config.php exists", "wwwroot" in cfg)
check("EAP managed config injected", "EAP MANAGED CONFIG" in cfg)
check("Redis session handler configured", "session_redis_host" in cfg)
check("sslproxy directive present", "sslproxy" in cfg)
perm = dexec("moodle", "stat", "-c", "%a %U:%G", "/var/moodledata").stdout.strip()
check("moodledata owned by www-data", perm.endswith("www-data:www-data"), perm)
check("moodledata mode 27xx/077x", re.match(r"^(27\d0|0?77\d)", perm) is not None, perm)

# ---------------------------------------------------------------
section("6. Plugins / anti-cheat")
for path, name in [
    ("theme/boost_union", "theme_boost_union"),
    ("mod/quiz/accessrule/seb", "quizaccess_seb (core)"),
    ("mod/quiz/accessrule/proctoring", "quizaccess_proctoring"),
    ("mod/vpl", "mod_vpl (coding lab)"),
]:
    ex = dexec("moodle", "test", "-f", f"/var/www/html/moodle/{path}/version.php")
    (check if name.endswith("(core)") or "boost_union" in name else
     (lambda l, o, d="": check(l, o, d) if o else warn(l, "optional plugin not installed"))
     )(f"plugin present: {name}", ex.returncode == 0)

# ---------------------------------------------------------------
section("7. Moodle environment check (admin/cli/checks.php)")
chk = dexec("moodle", "runuser", "-u", "www-data", "--", "php",
            "/var/www/html/moodle/admin/cli/checks.php")
tail = (chk.stdout + chk.stderr).strip().splitlines()
for ln in tail[-25:]:
    print("   " + ln)
check("no CRITICAL environment checks", "CRITICAL" not in chk.stdout.upper(),
      "see output above" if "CRITICAL" in chk.stdout.upper() else "")

# ---------------------------------------------------------------
section("8. Cron")
cl = run(["docker", "compose", "logs", "--tail", "8", "cron"]).stdout
print("   " + "\n   ".join(cl.strip().splitlines()[-8:]))
check("cron container logging activity", "cron" in cl.lower() or "entrypoint" in cl.lower())

# ---------------------------------------------------------------
section("9. HTTP endpoint")
import urllib.request
url = f"http://localhost:{PORT}/login/index.php"
try:
    with urllib.request.urlopen(url, timeout=15) as r:
        body = r.read(4000).decode("utf-8", "ignore")
        check(f"GET {url} -> {r.status}", r.status == 200)
        check("login page renders Moodle", "login" in body.lower())
except Exception as e:
    check(f"GET {url}", False, str(e))

# ---------------------------------------------------------------
section("10. HTTPS / TLS readiness")
warn("TLS terminates at a reverse proxy (not in this stack)",
     "set MOODLE_SSLPROXY=true + MOODLE_WWWROOT=https://... then `docker compose up -d`")

print("\n======================================================================")
print(f" RESULT:  {PASS} passed   {FAIL} failed   {WARN} warnings")
print("======================================================================")
sys.exit(1 if FAIL else 0)
