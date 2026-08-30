# Native Windows stack — Moodle 4.5 LTS without Docker

Docker Desktop's engine will not start inside WSL2 on this machine, so the platform
now runs directly on Windows:

| Layer | Build | Why |
|---|---|---|
| Web server | **Apache 2.4.66** (Apache Lounge, VS17 Win64) | one threaded service, handles a full exam hall; reuses `config/apache/*.conf` |
| PHP | **8.3.33** x64 **Thread‑Safe** (VS16) as `mod_php` | Moodle 4.5 supports PHP 8.1–8.3; TS build required for `mod_php` |
| Database | **MariaDB 11.4.13 LTS** (winx64 zip) | matches the Docker stack's engine + `utf8mb4_unicode_ci` tuning |
| App | **Moodle 4.5 LTS** (`MOODLE_405_STABLE`, latest 4.5.x) | unchanged from the Docker plan |
| Cron | Windows Scheduled Task, every 1 min | replaces the `cron` container |

Everything lands under `E:\ASSESMENT\native\stack\`, **`moodledata` is `E:\ASSESMENT\moodledata`**,
and all secrets/site settings are still read from `E:\ASSESMENT\.env`.

---

## 1. One‑command setup

Open **PowerShell as Administrator** (the script self‑elevates if you forget):

```powershell
cd E:\ASSESMENT
powershell -ExecutionPolicy Bypass -File .\native\Setup-MoodleNative.ps1
```

What it does, in order:

1. Installs the **Visual C++ 2015‑2022 x64** runtime if missing (`vc_redist.x64.exe /quiet`).
2. Downloads pinned PHP / Apache / MariaDB / Moodle zips into `native\stack\_downloads\`.
3. Extracts them to `native\stack\{php,Apache24,mariadb,moodle}`.
4. Generates, from the repo's existing `config\` files (Windows‑adapted):
   * `native\stack\php\php.ini` — 512M memory, 100M uploads, OPcache, `curl.cainfo`, all Moodle extensions.
   * `native\stack\Apache24\conf\extra\httpd-eap.conf` — vhost, `AllowOverride All`, `AcceptPathInfo On`, security headers, gzip, cache headers, dotfile/`.git`/`tests` denial.
   * `native\stack\my.ini` — `utf8mb4_unicode_ci`, InnoDB tuning, `innodb_buffer_pool_size` from `.env`.
5. Initialises MariaDB, registers the **`eap-mariadb`** service, sets the root password, creates the `moodle` DB + `moodleuser`.
6. Runs `admin\cli\install.php --non-interactive` with `--dataroot=E:\ASSESMENT\moodledata`.
7. Best‑effort installs `theme_boost_union`, `quizaccess_proctoring`, `mod_vpl` (skip with `-SkipPlugins`).
8. Injects the **EAP managed config** block into `config.php` (anti‑cheat copy/paste blocker, `autosavefrequency=15`, `sslproxy`, `pathtophp`).
9. Runs `scripts\moodle\post_install.php` → Boost Union theme + **`config/moodle/custom.scss`**, SEB + proctoring defaults, quiz hardening (shuffle + 1/page + free nav), **Invigilator / Proctor** role.
10. Registers the **`eap-apache`** service + the **`EAP-Moodle-Cron`** scheduled task + a firewall rule for the port.
11. Generates `seb\generated\exam-default-generated.seb` from `.env`.

When it finishes it prints the portal URL, the admin login, and the management commands.

### Useful switches

| Switch | Effect |
|---|---|
| `-ApacheZip <path>` | use a hand‑downloaded httpd zip (see [Troubleshooting](#apache-lounge-download-blocked)) |
| `-PhpZip / -MariaDbZip / -MoodleZip <path>` | same, for the other components |
| `-SkipDownloads` | archives are already in `native\stack\_downloads\` |
| `-SkipPlugins` | don't fetch contrib plugins (theme falls back to core Boost) |
| `-SkipServices` | wire everything but don't register Windows services / cron |
| `-Reinstall` | drop `config.php`, the `moodle` schema, `moodledata` **and** the MariaDB data dir, then rebuild from scratch |
| `-Port 8888` | serve on a different port (default: `HTTP_PORT` from `.env`, i.e. 8080) |
| `-DataRoot D:\moodledata` | override the data directory |
| `-PhpSeries 8.2` | use PHP 8.2 instead of 8.3 |

---

## 2. Day‑to‑day operations

```powershell
.\native\Manage-Moodle.ps1 status      # services + HTTP probe
.\native\Manage-Moodle.ps1 stop        # (admin) stop Apache + MariaDB
.\native\Manage-Moodle.ps1 start       # (admin) start them
.\native\Manage-Moodle.ps1 restart     # (admin)
.\native\Manage-Moodle.ps1 cron        # run one cron cycle now
.\native\Manage-Moodle.ps1 purge       # purge Moodle caches
.\native\Manage-Moodle.ps1 upgrade     # after adding/updating plugins
.\native\Manage-Moodle.ps1 audit       # full health check
.\native\Manage-Moodle.ps1 backup      # DB dump + moodledata zip -> backups\
```

Services are `Automatic`, so the stack comes back on reboot. The scheduled task
runs cron as `SYSTEM` every minute.

---

## 3. Theme, SEB templates, student import

### Modern theme + CSS overrides
Applied automatically in step 9. To re‑apply after editing
`config\moodle\custom.scss`:

```powershell
$env:EAP_MOODLE_CONFIG = 'E:\ASSESMENT\native\stack\moodle\config.php'
$env:EAP_CUSTOM_SCSS   = 'E:\ASSESMENT\native\stack\custom.scss'
Copy-Item E:\ASSESMENT\config\moodle\custom.scss $env:EAP_CUSTOM_SCSS -Force
& E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\scripts\moodle\post_install.php
.\native\Manage-Moodle.ps1 purge
```

### Safe Exam Browser config

```powershell
.\native\Make-Seb.ps1                                   # from .env -> seb\generated\exam-<timestamp>.seb
.\native\Make-Seb.ps1 -Url https://exams.example.edu -QuitPassword 'S3cr3t!' -Name final-exam
```

Then in the quiz: **Safe Exam Browser → Require the use of Safe Exam Browser →
"Yes – Upload my own config"** and upload the generated `.seb`.
The kiosk lockdown template is `seb\exam-default.seb` (unchanged from the Docker plan).

### Bulk student onboarding

```powershell
.\native\Import-Students.ps1 -Csv .\scripts\students_sample.csv            # add new users
.\native\Import-Students.ps1 -Csv .\roll.csv -Update                      # add + update
.\native\Import-Students.ps1 -Csv .\roll.csv -DryRun                      # validate only
```

CSV format is unchanged (`scripts\students_sample.csv`): required columns
`username,firstname,lastname,email`; optional `password,idnumber,cohort1,…`.
Under the hood it calls Moodle's supported
`admin\tool\uploaduser\cli\uploaduser.php`.

---

## 4. Manual step‑by‑step (if you don't want to run the script)

Paths assume `INSTALL=E:\ASSESMENT\native\stack`, `DATA=E:\ASSESMENT\moodledata`.

1. **VC++ runtime** — install `https://aka.ms/vs/17/release/vc_redist.x64.exe`.
2. **PHP** — unzip `php-8.3.33-Win32-vs16-x64.zip` → `%INSTALL%\php`. Copy
   `php.ini-production` → `php.ini`; set `extension_dir="ext"`, enable
   `curl gd intl mbstring exif fileinfo iconv mysqli pdo_mysql openssl soap sodium zip ldap`,
   `zend_extension=opcache`, `date.timezone=UTC`, `memory_limit=512M`,
   `upload_max_filesize=100M`, `post_max_size=100M`, `max_execution_time=300`,
   `max_input_vars=5000`, `curl.cainfo` / `openssl.cafile` → a downloaded
   `https://curl.se/ca/cacert.pem`.
3. **Apache** — unzip the VS17 `httpd-2.4.66-*-Win64-VS17.zip` → `%INSTALL%\Apache24`.
   In `conf\httpd.conf` set `Define SRVROOT` to that path, `Listen 8080`, uncomment
   `mod_rewrite/headers/expires/deflate`, add:
   ```
   LoadModule php_module "E:/ASSESMENT/native/stack/php/php8apache2_4.dll"
   PHPIniDir "E:/ASSESMENT/native/stack/php"
   AddHandler application/x-httpd-php .php
   DirectoryIndex index.php
   AcceptPathInfo On
   DocumentRoot "E:/ASSESMENT/native/stack/moodle"
   <Directory "E:/ASSESMENT/native/stack/moodle">
       Options FollowSymLinks
       AllowOverride All
       Require all granted
   </Directory>
   ```
   Test: `bin\httpd.exe -t`. Install service: `bin\httpd.exe -k install -n eap-apache`.
4. **MariaDB** — unzip `mariadb-11.4.13-winx64.zip` → `%INSTALL%\mariadb`. Write
   `%INSTALL%\my.ini` (`[mysqld]` with `datadir`, `character-set-server=utf8mb4`,
   `collation-server=utf8mb4_unicode_ci`, `skip-character-set-client-handshake`,
   `innodb_file_per_table=1`, `innodb_default_row_format=dynamic`,
   `innodb_buffer_pool_size=1G`, `transaction-isolation=READ-COMMITTED`).
   Init + service:
   ```
   bin\mariadb-install-db.exe --datadir=%INSTALL%\mariadb-data --service=eap-mariadb --password=<DB_ROOT_PASS> --port=3306
   net start eap-mariadb
   bin\mariadb.exe -uroot -p<DB_ROOT_PASS> -e "CREATE DATABASE moodle CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; CREATE USER 'moodleuser'@'127.0.0.1' IDENTIFIED BY '<DB_PASS>'; GRANT ALL ON moodle.* TO 'moodleuser'@'127.0.0.1'; FLUSH PRIVILEGES;"
   ```
5. **Moodle** — unzip `moodle-latest-405.zip` → `%INSTALL%\moodle`. `mkdir %DATA%`.
   ```
   php\php.exe moodle\admin\cli\install.php --lang=en --wwwroot=http://localhost:8080 ^
     --dataroot=E:\ASSESMENT\moodledata --dbtype=mariadb --dbhost=127.0.0.1 --dbname=moodle ^
     --dbuser=moodleuser --dbpass=<DB_PASS> --dbport=3306 --prefix=mdl_ ^
     --fullname="Enterprise Assessment Portal" --shortname=EAP --adminuser=examadmin ^
     --adminpass=<MOODLE_ADMIN_PASS> --adminemail=<email> --non-interactive --agree-license
   ```
6. **Plugins** — unzip into `moodle\theme\boost_union`,
   `moodle\mod\quiz\accessrule\proctoring`, `moodle\mod\vpl`, then
   `php\php.exe moodle\admin\cli\upgrade.php --non-interactive`.
7. **Hardening + theme** — add the EAP managed block to `config.php`, then run
   `post_install.php` with `EAP_MOODLE_CONFIG` / `EAP_CUSTOM_SCSS` set (see §3).
8. **Cron** — `schtasks /create /tn EAP-Moodle-Cron /sc minute /mo 1 /ru SYSTEM /rl HIGHEST ^`
   `/tr "E:\ASSESMENT\native\stack\php\php.exe E:\ASSESMENT\native\stack\moodle\admin\cli\cron.php"`.

---

## 5. Troubleshooting

### Apache Lounge download blocked
Apache Lounge sits behind Cloudflare and sometimes refuses scripted downloads
(the script prints a clear message and stops). Fix:

1. Open <https://www.apachelounge.com/download/VS17/binaries/httpd-2.4.66-251206-Win64-VS17.zip> in a browser.
2. Save it anywhere.
3. `.\native\Setup-MoodleNative.ps1 -ApacheZip C:\Users\Hp\Downloads\httpd-2.4.66-251206-Win64-VS17.zip`

### `php8apache2_4.dll` missing / Apache won't start
You unzipped a **Non‑Thread‑Safe** PHP build. `mod_php` needs the **TS** zip
(`php-8.3.33-Win32-vs16-x64.zip`, *not* `...-nts-...`). Re‑run with
`-PhpZip <ts-zip> -Reinstall`.

### Port 8080 already in use
`Get-NetTCPListener -LocalPort 8080` to find the owner, or run setup with
`-Port 8888` (also updates `wwwroot`).

### `dmlconnectionexception` during install
MariaDB not up yet, or the `moodleuser` host doesn't match. The script creates
the user for both `localhost` and `127.0.0.1`; if you did it by hand, add the
`'moodleuser'@'127.0.0.1'` grant.

### Reset everything
```powershell
.\native\Setup-MoodleNative.ps1 -Reinstall
```
Drops `config.php`, the `moodle` database, `moodledata`, and the MariaDB data
directory, then rebuilds. Services and downloads are reused.

### Uninstall the services
```powershell
Stop-Service eap-apache, eap-mariadb -Force
E:\ASSESMENT\native\stack\Apache24\bin\httpd.exe -k uninstall -n eap-apache
sc.exe delete eap-mariadb
Unregister-ScheduledTask -TaskName EAP-Moodle-Cron -Confirm:$false
```

---

## 6. Relationship to the Docker files

The Docker assets (`docker-compose.yml`, `docker/`, `manage.ps1`, `scripts/audit.py`,
`scripts/bulk_import_students.py`, `scripts/make-seb.sh`) are **kept** — if the
Docker/WSL2 problem is ever fixed that path still works. The native scripts are
additive and live entirely under `native\`. The only shared file changed is
`scripts/moodle/post_install.php`, which now honours `EAP_MOODLE_CONFIG` /
`EAP_CUSTOM_SCSS` env vars and falls back to the original Docker paths when they
are unset — so it behaves identically inside the container.
