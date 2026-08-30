# Enterprise AI-Proctored Online Assessment Platform
### Moodle 4.5 LTS · PHP 8.2 · MariaDB 11.4 · Redis 7 — 100 % containerised, zero licence cost

> Built to run on this **Windows 11 + Docker Desktop** host. The mission brief
> targeted an Ubuntu server; every Linux-server component instead runs inside
> Debian/Ubuntu-based containers, orchestrated by `docker-compose.yml`.

---

## 1. Quick start

```powershell
cd E:\ASSESMENT
# 1. review / edit secrets
notepad .env
# 2. build the Moodle image (Moodle core + plugins are fetched here, ~5-10 min)
docker compose build
# 3. launch the stack (first boot runs the non-interactive Moodle install, ~3-5 min)
docker compose up -d
# 4. watch the install finish
docker compose logs -f moodle      # wait for "Server listening" / healthcheck healthy
# 5. health check
python scripts\audit.py
```

Then open **<http://localhost:8080>**.

### Optional — coding-exam execution server (VPL)
```powershell
docker compose --profile vpl up -d vpl-jail
```

---

## 2. Portal access & credentials

| | Value |
|---|---|
| **Portal URL** | `http://localhost:8080` (change via `MOODLE_WWWROOT` + `HTTP_PORT` in `.env`) |
| **Super Admin (Exam Cell)** | user `examadmin` — password = `MOODLE_ADMIN_PASS` in `.env` |
| **Admin email** | `MOODLE_ADMIN_EMAIL` in `.env` |
| **DB name / user** | `moodle` / `moodleuser` — password = `DB_PASS` in `.env` |
| **DB root** | password = `DB_ROOT_PASS` in `.env` |
| **SEB quit password** | `SEB_QUIT_PASSWORD` in `.env` |

> `.env` is git-ignored. Treat it as a secret store. Rotate `MOODLE_ADMIN_PASS`
> after first login: *Preferences → Change password*.

---

## 3. Architecture

```
                 ┌──────────────────────────── docker network: eapnet ───────────────────────────┐
  browser :8080 ─┤  moodle  (Apache + mod_php 8.2, Moodle 4.5)  ── volume: moodle_code           │
                 │     │                                         ── volume: moodledata (0770)    │
                 │     ├── mariadb  11.4  (utf8mb4_unicode_ci, InnoDB tuned) ── volume: mariadb  │
                 │     ├── redis    7     (sessions + MUC application cache)  ── volume: redis    │
                 │     └── cron     (same image; admin/cli/cron.php every 60s)                    │
                 │  vpl-jail (profile: vpl, privileged) — C/C++/Java/Python sandbox              │
                 └──────────────────────────────────────────────────────────────────────────────┘
```

| Phase | Delivered by |
|---|---|
| **1 · LAMP provisioning** | `docker/moodle/Dockerfile`, `config/php/moodle-php.ini`, `config/mariadb/moodle.cnf`, `docker-compose.yml` |
| **2 · Moodle CLI install + cron** | `docker/moodle/entrypoint.sh` (non-interactive `admin/cli/install.php`), `cron` service |
| **3 · Frontend modernisation** | `theme_boost_union` + `config/moodle/custom.scss` (injected by `scripts/moodle/post_install.php`) |
| **4 · Anti-cheating** | SEB (`seb/`), `quizaccess_proctoring`, quiz-hardening defaults + copy/paste block (`post_install.php`, managed `config.php` block) |
| **5 · Bulk onboarding + RBAC** | `scripts/bulk_import_students.py`, `scripts/students_sample.csv`, "Invigilator / Proctor" role |
| **6 · Audit & launch report** | `scripts/audit.py`, this file |

---

## 4. The 3-tier anti-cheating architecture

1. **SEB kiosk lockdown** — `seb/exam-default.seb` blocks Alt+Tab, Task Manager,
   dual monitors, and filters URLs to the portal only. See `seb/README-SEB.md`.
2. **Webcam / AI proctoring** — `quizaccess_proctoring` captures periodic webcam
   snapshots (30 s interval, configured site-wide). Review under
   *Quiz → Results → Proctoring report*. No paid cloud API is enabled
   (`facematch=0`) — turn on AWS Rekognition face-match only if you add keys.
3. **Dynamic shuffling** — site defaults now force *shuffle questions* +
   *shuffle answers* + one-question-per-page + free navigation. Multichoice
   A/B/C/D options are scrambled per attempt. Copy / cut / paste / right-click
   are disabled on the quiz attempt page (`$CFG->additionalhtmlfooter`).

### Running an exam — SEB mode vs Normal Browser mode
* **SEB mode (high-stakes):** quiz settings → *Safe Exam Browser* → *Require… =
  “Yes – Upload my own config”* → upload `seb/exam-default.seb`; set the quit
  password. Students get a *Launch Safe Exam Browser* button and cannot start in
  a normal browser.
* **Normal Browser mode (practice / low-stakes):** *Require SEB = No*. Proctoring
  snapshots and question shuffling still apply.

---

## 5. Importing questions (Question Bank)

```powershell
# GIFT format
docker compose exec -T moodle runuser -u www-data -- `
  php admin/cli/import_questions.php --category-id=<CATID> --file=/tmp/bank.gift --format=gift
# Aiken format
docker compose exec -T moodle runuser -u www-data -- `
  php admin/cli/import_questions.php --category-id=<CATID> --file=/tmp/bank.aiken --format=aiken
```
Copy the file in first: `docker compose cp bank.gift moodle:/tmp/bank.gift`.
UI route: *Question bank → Import → choose GIFT / Aiken / Moodle XML*.
Get category ids with:
`docker compose exec -T moodle runuser -u www-data -- php admin/cli/import_questions.php --list-categories --courseid=<ID>`

---

## 6. Bulk student onboarding (Phase 5)

```powershell
python scripts\bulk_import_students.py scripts\students_sample.csv          # add new
python scripts\bulk_import_students.py roll_2026.csv --update               # add + update
python scripts\bulk_import_students.py roll_2026.csv --dry-run              # validate only
```
CSV headers: `username,password,firstname,lastname,email,idnumber,cohort1`.
The script validates the file, copies it into the container and calls Moodle's
native `admin/tool/uploaduser/cli/uploaduser.php`.

### RBAC — three clean tiers
| Tier | Moodle role | Scope |
|---|---|---|
| **System Administrator / Exam Cell** | `Site Administrator` (`examadmin`) | full site config |
| **Faculty / Invigilator** | **`Invigilator / Proctor`** (custom, created on install) | course + module: build quizzes & question banks, run live proctoring & SEB review — **no** site admin |
| **Student** | `Student` (auth user) | distraction-free exam portal only |

Assign faculty: *Course → Participants → Enrol users → role “Invigilator / Proctor”*.

---

## 7. Instant results → Excel (Phase 4)

```powershell
python scripts\export_grades.py --list                 # show course ids
python scripts\export_grades.py --course EXAM101        # -> E:\ASSESMENT\exports\*.xlsx
```
Produces a real `.xlsx` (Moodle's bundled `dataformat_excel`, no external libs);
falls back to CSV if unavailable. UI route: *Course → Grades → Export → Excel spreadsheet*.

---

## 8. Production readiness

* **Performance** — OPcache (128 MB / 10 000 files), Redis sessions + MUC cache,
  InnoDB buffer pool 1 GB (`INNODB_BUFFER_POOL_SIZE` in `.env`), Apache
  deflate + far-future asset caching, `$CFG->cachejs`.
* **Cron** — dedicated `cron` container runs `admin/cli/cron.php` every 60 s;
  `cronclionly=1` blocks web cron.
* **Network-drop resilience** — quiz auto-save every 15 s (`$CFG->autosavefrequency`);
  Redis session locks tuned (120 s acquire / 7200 s expire) so a dropped client
  can resume the same attempt.
* **HTTPS** — terminate TLS at a reverse proxy, then set in `.env`:
  `MOODLE_WWWROOT=https://exams.example.edu`, `MOODLE_SSLPROXY=true`,
  `MOODLE_REVERSEPROXY=true`, and `docker compose up -d`.
* **Hardening** — `expose_php=Off`, `ServerTokens Prod`, security headers
  (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`,
  `Permissions-Policy`, conditional HSTS), dotfile/VCS deny rules,
  `moodledata` outside web root at `0770 www-data:www-data`,
  password policy on (min 8), web services limited.

### Backups
```powershell
# database
docker compose exec -T mariadb sh -c 'exec mariadb-dump -uroot -p"$MARIADB_ROOT_PASSWORD" --single-transaction moodle' > backup_moodle.sql
# moodledata
docker run --rm -v assesment_moodledata:/data -v ${PWD}:/out alpine tar czf /out/moodledata.tgz -C /data .
```

---

## 9. Common operations

```powershell
docker compose ps                       # status
docker compose logs -f moodle           # app logs
docker compose exec moodle bash         # shell in the app container
docker compose exec -T moodle runuser -u www-data -- php admin/cli/purge_caches.php
docker compose exec -T moodle runuser -u www-data -- php admin/cli/cron.php
docker compose down                     # stop (keeps volumes/data)
docker compose down -v                  # DESTROY everything incl. database
docker compose build --no-cache moodle  # rebuild after changing Dockerfile / plugins
```
After editing `config/moodle/custom.scss`:
`docker compose restart moodle` (entrypoint re-applies SCSS + purges caches).

---

## 10. File map

```
E:\ASSESMENT\
├─ docker-compose.yml            stack definition
├─ .env / .env.example           secrets + tunables
├─ config\
│  ├─ php\moodle-php.ini         Phase 1 PHP tuning + OPcache
│  ├─ mariadb\moodle.cnf         Phase 1 UTF8MB4 + InnoDB
│  ├─ apache\000-moodle.conf     Phase 6 vhost, AllowOverride All
│  ├─ apache\security.conf       Phase 6 hardening headers
│  └─ moodle\custom.scss         Phase 3 SaaS theme (Inter/Jakarta, glass, quiz UX)
├─ docker\moodle\
│  ├─ Dockerfile                 Moodle 4.5 + PHP exts + plugins
│  └─ entrypoint.sh              non-interactive install / upgrade / harden
├─ scripts\
│  ├─ bulk_import_students.py    Phase 5 CSV onboarding
│  ├─ students_sample.csv        Phase 5 template
│  ├─ export_grades.py           Phase 4 1-click Excel export (wrapper)
│  ├─ audit.py                   Phase 6 health check
│  ├─ make-seb.sh               SEB config generator
│  └─ moodle\
│     ├─ post_install.php        theme + SEB + proctoring + RBAC tuning
│     └─ export_grades.php       gradebook -> xlsx (in-container)
└─ seb\
   ├─ exam-default.seb           kiosk lockdown template
   └─ README-SEB.md              SEB usage guide
```
