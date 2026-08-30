# Safe Exam Browser (SEB) — Kiosk Lockdown Layer

**Tier 1 of the 3-tier anti-cheating architecture.**
Native Moodle plugin: `quizaccess_seb` (ships with Moodle core — nothing to install).

---

## What `exam-default.seb` enforces

| Control | Setting |
|---|---|
| Full-screen kiosk, no window chrome | `browserViewMode=1`, `showMenuBar=false`, `showReloadButton=false` |
| Block **Alt+Tab / app switching** | `allowSwitchToApplications=false`, `enableAltTab=false` |
| Block **Task Manager / Explorer** (Windows) | `createNewDesktop=true`, `killExplorerShell=true`, `enableCtrlEsc=false` |
| Block **Alt+F4 / Esc / PrintScreen / F-keys** | `enableAltF4/​enableEsc/​enablePrintScreen=false` |
| **Single monitor only** | `allowedDisplaysMaxNumber=1`, `allowDisplayMirroring=false` |
| **URL whitelist** — portal only, everything else denied | `URLFilterEnable=true` + `URLFilterRules` |
| No downloads / uploads / PDF plugin | `allowDownUploads=false`, `allowPDFPlugIn=false` |
| No VM, no screen-sharing | `allowVirtualMachine=false`, `allowScreenSharing=false` |
| **Quit password** (SHA-256 hashed) | `hashedQuitPassword` = SHA256 of `SEB_QUIT_PASSWORD` in `.env` |
| Quit link back to Moodle | `quitURL` |

The current quit-password hash in `exam-default.seb` corresponds to the
`SEB_QUIT_PASSWORD` value generated in `.env`. Change the password → regenerate:

```bash
./scripts/make-seb.sh  https://exams.example.edu  'NewQuitPass!23'  midterm-2026
# -> seb/generated/midterm-2026.seb
```

---

## Attaching SEB to a quiz (per exam)

1. Edit the quiz → **Safe Exam Browser** section.
2. **Require the use of Safe Exam Browser** →
   * **“Yes – Use SEB client config”** and paste settings manually, **or**
   * **“Yes – Upload my own config”** → upload `seb/exam-default.seb` (recommended — everything above is pre-set).
3. Set **Quit password** (same as `SEB_QUIT_PASSWORD`) so invigilators can exit for a candidate.
4. Optionally add allowed **Browser exam keys / Config keys** for extra integrity binding.
5. Save. Students now see a **“Launch Safe Exam Browser”** button and cannot start the attempt in a normal browser.

### Student workflow
* Install SEB (free): <https://safeexambrowser.org> (Windows / macOS / iPad).
* Click **Launch Safe Exam Browser** on the quiz page → SEB opens the exam in kiosk mode.
* At the end, **Submit all and finish** → use the quit password to leave SEB.

### Running the SAME quiz in Normal Browser mode
Set **Require the use of Safe Exam Browser = No** on that quiz. Webcam proctoring
(Tier 2) and question shuffling (Tier 3) still apply, so a low-stakes practice
quiz can run in any browser while the final runs locked down.
