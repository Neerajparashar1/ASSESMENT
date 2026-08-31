# ITM Group of Institutions — Online Examination Portal

### Moodle 4.5 LTS · Apache 2.4 · PHP 8.3 (mod_php) · MariaDB 11.4 LTS — self-hosted, AI-proctored, **no Docker**

A secured, invigilated online-exam platform: question banks, an exam builder, Safe Exam
Browser kiosk lockdown, webcam proctoring, live invigilation, and results export — all
from one Windows machine with no cloud hosting.

> **Runtime.** The platform runs natively on Windows as two services (`eap-apache`,
> `eap-mariadb`) plus a one-minute cron task. It is installed and operated through the
> scripts in `native/`. The `docker/` + `docker-compose.yml` files are kept for
> reference only — Docker Desktop's Linux engine does not start on the target host, so
> the stack was ported to run natively.

**Full reference document:** [`docs/ITM-GOI-Examination-Portal.html`](docs/ITM-GOI-Examination-Portal.html)
· [`docs/ITM-GOI-Examination-Portal.docx`](docs/ITM-GOI-Examination-Portal.docx)

---

## 1. Setup

Everything the platform needs — PHP, Apache, MariaDB, Moodle — ships inside this
repository. Setup is a clone plus one script; the script derives every path from the
folder it sits in, so the project can live on any drive.

**You need:** Windows 10 (1809+) or 11 · local administrator · Windows PowerShell 5.1 ·
Git for Windows · ~2 GB free disk · port 8080 free (or use `-Port`).

```powershell
# 1. get the code (long paths + a short, non-OneDrive folder)
git config --global core.longpaths true
git clone https://github.com/Neerajparashar1/ASSESMENT.git
cd ASSESMENT

# 2. add the folder to your antivirus exclusions
#    Windows Security -> Virus & threat protection -> Exclusions -> Add -> Folder
#    (Defender sometimes quarantines a bundled MariaDB/PHP .exe otherwise)

# 3. secrets
copy .env.example .env
notepad .env        # set DB_ROOT_PASS, DB_PASS, MOODLE_ADMIN_*, SEB_QUIT_PASSWORD, TZ

# 4. install  --  ALWAYS use -Reinstall on any machine other than the one the
#    repo was built on (it carries a machine-specific config.php + no database)
powershell -ExecutionPolicy Bypass -File .\native\Setup-MoodleNative.ps1 -Reinstall

# 5. exam-specific config (roles + theme are already covered by -Reinstall)
set PHP=native\stack\php\php.exe
%PHP% native\Setup-SebLockdownTemplate.php --all
%PHP% native\Setup-InvigilatorRoles.php
%PHP% native\apply_itm_theme.php
```

Then double-click **`start.bat`** (self-elevating; starts both services, registers the
cron task, waits for the portal, opens it) and go to **<http://localhost:8080>**.
`stop.bat` takes it down. The services are set to start automatically, so after a
reboot the portal is already running.

**Verify:** `native\Manage-Moodle.ps1 status` should show both services `Running`, the
cron task present, and `HTTP :8080  200 OK`. *Site administration → Notifications*
should report no problems.

Common setup problems (port in use, `php8apache2_4.dll` missing, "accessible only via…",
antivirus quarantine, `git clone` "filename too long") are covered in the reference
document, §3.10.

---

## 2. Access & credentials

| | Value |
|---|---|
| **Portal URL** | `http://localhost:8080` (`MOODLE_WWWROOT` + `HTTP_PORT` in `.env`) |
| **Site administrator** | `examadmin` — password `MOODLE_ADMIN_PASS` |
| **Database** | `moodle` / `moodleuser` — password `DB_PASS`; root password `DB_ROOT_PASS` |
| **SEB quit password** | `SEB_QUIT_PASSWORD` |

`.env` is git-ignored — treat it as a secret store.

---

## 3. Architecture

| Layer | Component | Notes |
|---|---|---|
| Application | Moodle 4.5 LTS | `native/stack/moodle` |
| Web server | Apache 2.4 + `mod_php` | service `eap-apache` · port 8080 |
| Language | PHP 8.3, Thread-Safe | OPcache, 512 MB limit, 100 MB uploads |
| Database | MariaDB 11.4 LTS | service `eap-mariadb` · `utf8mb4_unicode_ci` |
| Scheduler | Windows Task `EAP-Moodle-Cron` | Moodle cron every minute as SYSTEM |
| Theme | Boost Union + custom Raw SCSS | compiled from `config/moodle/custom.scss` |

Data lives at `<project>\moodledata`; secrets in `.env`.

---

## 4. What's in the box

| Plugin | Purpose |
|---|---|
| **`local_examwizard`** (custom) | Exam-cell control centre: question uploader (CSV/XLSX/GIFT/XML), 4-step exam builder, **Exam Control** hub, live exam control, results + CSV export, bulk student onboarding, SEB quit-password manager. |
| **`local_sebkiosk`** (custom) | Leaving Safe Exam Browser finalises the attempt; carries the front-end theme runtime (`exam-ui.js`). |
| `quizaccess_seb` | Safe Exam Browser access rule (template mode + the *EAP Kiosk Lockdown* template). |
| `quizaccess_proctoring` | Periodic webcam snapshots (~30 s) + a review report. |
| `theme_boost_union` | Base theme, skinned in the ITM GOI identity. |

---

## 5. Anti-cheating

1. **SEB kiosk lockdown** — new isolated desktop, Explorer shell killed (no Task
   Manager / Start menu), every escape hotkey disabled, single display, no VM / screen
   share, URL-filtered to the portal. Quit is password-protected and is the only
   sanctioned exit.
2. **Exit = auto-submit** — the SEB quit link and a page-unload beacon both finalise
   every in-progress attempt for that student (`local_sebkiosk`). A hard kill is caught
   by the time limit + automatic overdue submission + the one-minute cron task.
3. **Webcam proctoring** — `quizaccess_proctoring` snapshots, reviewed under
   *Quiz → Results → Proctoring report*. No paid cloud face-match is enabled.
4. **Shuffled, paged, copy-protected** — one attempt, questions shuffled, one per page,
   free navigation; copy / cut / paste / right-click disabled on the attempt page;
   autosave every 15 s; SEB config-key checked on every request.

### Running an exam
- **High-stakes:** build it with the Exam Wizard (SEB is wired in automatically), or on
  a quiz set *Safe Exam Browser → Require… = Configure manually* and pick the
  *EAP Kiosk Lockdown* template. Students get a **Launch Safe Exam Browser** button.
- **Practice / low-stakes:** *Require SEB = No* — shuffling, paging and proctoring still
  apply.

---

## 6. Roles

| Role | Scope |
|---|---|
| **Site administrator** (`examadmin`) | Full platform + server settings. The only admin. |
| **Invigilator / Proctor** (`invigilator`) | Course / module: build quizzes & question banks, manage SEB, run proctoring review, grade, run live control. No site admin. |
| **Exam Hall Invigilator** (`examinvigilator`) | Watch + intervene only — monitor report, proctoring feed, live control (pause / reopen / extend / force-submit / resume). **Cannot** edit exams, edit questions, grade, delete attempts. Assignable site-wide so one login oversees every session. |
| **Student** | Sit assigned exams only. |

Backed by a dedicated capability `local/examwizard:control`, kept separate from
`mod/quiz:manage`. Assign via *Course → Participants → Enrol users* or
`native\Setup-InvigilatorRoles.php`.

---

## 7. Bulk operations

```powershell
# students
native\Import-Students.ps1 -Csv .\roll_2026.csv            # add new
native\Import-Students.ps1 -Csv .\roll_2026.csv -Update    # add + update
native\Import-Students.ps1 -Csv .\roll_2026.csv -DryRun    # validate only
```
Or use the Exam Wizard: *Exam Control → Add students* (CSV/XLSX preview → create + enrol).

**Questions:** *Exam Control → Upload questions* — CSV/XLSX with a validated card preview,
or hand a `.gift` / `.xml` straight to Moodle's importers.

**Results:** *Exam Control → your exam → Results → Download CSV*, or gradebook export to
ODS / XLS / TXT / XML.

---

## 8. Sharing a live demo (no cloud hosting)

```powershell
demo-cf.bat            # Cloudflare quick tunnel — double-click, no account.
                       #   prints + copies a https://*.trycloudflare.com link
demo-cf-stop.bat       # end it, restore http://localhost:8080

# or, for a stable reusable URL (needs a free ngrok account + static domain):
.\demo.ps1 -Domain <name>.ngrok-free.app
.\demo-stop.ps1
```
Both repoint `$CFG->wwwroot` at the public address and turn on proxied-SSL; the stop
script reverts. **SEB exams do not work over a tunnel** — demo the portal, admin and
non-SEB flows.

---

## 9. Common operations

```powershell
native\Manage-Moodle.ps1 status        # services + HTTP probe
native\Manage-Moodle.ps1 restart       # (elevated) restart both services
native\Manage-Moodle.ps1 cron          # run one cron cycle now
native\Manage-Moodle.ps1 purge         # purge all caches
native\Manage-Moodle.ps1 upgrade       # after adding / updating plugins
native\Manage-Moodle.ps1 backup        # DB dump + moodledata archive -> backups\
```
After editing `config/moodle/custom.scss` or `local/sebkiosk/exam-ui.js`:
`native\stack\php\php.exe native\apply_itm_theme.php` (recompiles the theme, bumps the
revision, purges caches). Bump the `?v=` on `exam-ui.js` in `config.php` **and**
`native\harden_seb_quiz.php` after any JS change.

---

## 10. File map

```
ASSESMENT\
├─ start.bat / stop.bat                run / stop the stack
├─ demo-cf.bat / demo-cf-stop.bat      Cloudflare demo tunnel
├─ demo.ps1 / demo-stop.ps1            ngrok / manual demo tunnel
├─ .env / .env.example                 secrets + tunables (git-ignored)
├─ docs\                               reference document (HTML + DOCX) + generator
├─ config\moodle\custom.scss           all portal styling
├─ native\
│  ├─ Setup-MoodleNative.ps1           full native install
│  ├─ Manage-Moodle.ps1                day-to-day operations
│  ├─ Setup-SebLockdownTemplate.php    create / apply the kiosk SEB template
│  ├─ Setup-InvigilatorRoles.php       create the two invigilation roles
│  ├─ Import-Students.ps1              bulk student import (CLI)
│  ├─ apply_itm_theme.php              compile custom.scss into the theme
│  ├─ harden_seb_quiz.php              per-quiz SEB / auto-submit wiring
│  ├─ README-NATIVE.md                 native stack notes + manual install
│  └─ stack\                           bundled Apache / PHP / MariaDB / Moodle
├─ scripts\moodle\post_install.php     first-run config — roles, SEB, proctoring, theme
├─ seb\                                SEB templates + notes
└─ docker\ , docker-compose.yml        reference only — not used
```
