#!/usr/bin/env python3
# =====================================================================
#  PHASE 5 - CSV BULK STUDENT ONBOARDING ENGINE
#  Wraps Moodle's native CLI user uploader inside the running container:
#      admin/tool/uploaduser/cli/uploaduser.php
#  (the brief's "admin/cli/upload_users.php" does not exist in core;
#   this is the supported equivalent.)
#
#  Usage:
#    python scripts/bulk_import_students.py scripts/students_sample.csv
#    python scripts/bulk_import_students.py roll.csv --update      # add + update
#    python scripts/bulk_import_students.py roll.csv --dry-run     # validate only
#    python scripts/bulk_import_students.py roll.csv --service docker
#
#  Requires: Docker Desktop running and `docker compose up -d` already done.
# =====================================================================
import argparse
import csv
import subprocess
import sys
import time
from pathlib import Path

PROJECT_DIR = Path(__file__).resolve().parent.parent
CONTAINER_SVC = "moodle"
MOODLE_CLI = "/var/www/html/moodle/admin/tool/uploaduser/cli/uploaduser.php"
REQUIRED = ["username", "firstname", "lastname", "email"]
KNOWN = REQUIRED + ["password", "idnumber", "auth", "lang", "timezone",
                    "institution", "department", "phone1", "city", "country"]
# cohortN / course N / group N / roleN / type N are also accepted by Moodle.


def sh(cmd, **kw):
    print("  $ " + " ".join(cmd))
    return subprocess.run(cmd, cwd=PROJECT_DIR, **kw)


def validate_csv(path: Path) -> int:
    if not path.is_file():
        sys.exit(f"ERROR: file not found: {path}")
    with path.open(newline="", encoding="utf-8-sig") as fh:
        reader = csv.DictReader(fh)
        headers = [h.strip() for h in (reader.fieldnames or [])]
        missing = [c for c in REQUIRED if c not in headers]
        if missing:
            sys.exit(f"ERROR: CSV missing required column(s): {', '.join(missing)}")
        rows = list(reader)
    if not rows:
        sys.exit("ERROR: CSV has a header but no data rows.")

    problems = []
    seen = set()
    for i, r in enumerate(rows, start=2):
        u = (r.get("username") or "").strip().lower()
        if not u:
            problems.append(f"  line {i}: empty username")
        elif u in seen:
            problems.append(f"  line {i}: duplicate username '{u}'")
        seen.add(u)
        if "@" not in (r.get("email") or ""):
            problems.append(f"  line {i}: invalid email '{r.get('email')}'")
        pw = (r.get("password") or "").strip()
        if pw and pw.lower() != "changeme" and len(pw) < 8:
            problems.append(f"  line {i}: weak password (<8 chars) for '{u}'")

    extra = [h for h in headers if h not in KNOWN and not h[:-1].rstrip().lower()
             in ("cohort", "course", "group", "role", "type", "enrolstatus", "enrolperiod")
             and not h.rstrip("0123456789") in ("cohort", "course", "group", "role", "type")]
    if extra:
        print(f"  note: non-standard columns (Moodle may ignore): {', '.join(extra)}")

    print(f"  parsed {len(rows)} student row(s), headers OK: {', '.join(headers)}")
    if problems:
        print("VALIDATION ISSUES:")
        print("\n".join(problems))
        sys.exit(1)
    return len(rows)


def container_running(svc: str) -> bool:
    r = subprocess.run(["docker", "compose", "ps", "-q", svc],
                       cwd=PROJECT_DIR, capture_output=True, text=True)
    return bool(r.stdout.strip())


def main():
    ap = argparse.ArgumentParser(description="Bulk-import students into Moodle from CSV.")
    ap.add_argument("csv", type=Path, help="path to the students CSV")
    ap.add_argument("--service", default=CONTAINER_SVC, help="compose service name (default: moodle)")
    ap.add_argument("--update", action="store_true",
                    help="add new AND update existing users (uutype=3); default is add-new-only")
    ap.add_argument("--allow-suspend", action="store_true",
                    help="suspend users that are absent from the file")
    ap.add_argument("--dry-run", action="store_true", help="validate the CSV and stop")
    ap.add_argument("--moodle-opt", action="append", default=[],
                    metavar="key=value", help="extra raw option for uploaduser.php (repeatable)")
    args = ap.parse_args()

    print("== PHASE 5 : Bulk student onboarding ==")
    print(f"[1/4] Validating {args.csv} ...")
    count = validate_csv(args.csv)
    if args.dry_run:
        print(f"[dry-run] {count} row(s) look valid. Nothing imported.")
        return

    print(f"[2/4] Checking container '{args.service}' ...")
    if not container_running(args.service):
        sys.exit(f"ERROR: service '{args.service}' is not running. Run:  docker compose up -d")

    remote = f"/tmp/import_{int(time.time())}.csv"
    print(f"[3/4] Copying CSV into container -> {remote}")
    cp = sh(["docker", "compose", "cp", str(args.csv), f"{args.service}:{remote}"])
    if cp.returncode != 0:
        sys.exit("ERROR: docker compose cp failed.")
    sh(["docker", "compose", "exec", "-T", args.service,
        "chown", "www-data:www-data", remote])

    opts = [
        f"--file={remote}",
        "--delimiter_name=comma",
        "--encoding=UTF-8",
    ]
    # uutype: 1=add new only  3=add new + update existing
    opts.append("--uutype=3" if args.update else "--uutype=1")
    opts.append("--uuallowrenames=0")
    opts.append("--uuallowdeletes=0")
    opts.append(f"--uuallowsuspends={'1' if args.allow_suspend else '0'}")
    opts.append("--uunoemailduplicates=1")
    opts.append("--uustandardusernames=1")
    for kv in args.moodle_opt:
        opts.append(f"--{kv}")

    print(f"[4/4] Running Moodle CLI uploader ...")
    run = sh(["docker", "compose", "exec", "-T", args.service,
              "runuser", "-u", "www-data", "--",
              "php", MOODLE_CLI, *opts])
    sh(["docker", "compose", "exec", "-T", args.service, "rm", "-f", remote])

    if run.returncode == 0:
        print("\n== DONE ==  Students imported. Verify at:  Site administration > Users > Browse list of users")
    else:
        print("\n== FAILED ==  uploaduser.php returned a non-zero exit code (see output above).")
        sys.exit(run.returncode)


if __name__ == "__main__":
    main()
