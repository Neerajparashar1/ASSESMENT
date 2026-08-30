#!/usr/bin/env python3
# =====================================================================
#  PHASE 4 - 1-click grade export wrapper
#  Runs export_grades.php inside the container and copies the workbook
#  out to  E:\ASSESMENT\exports\
#
#  python scripts/export_grades.py --list
#  python scripts/export_grades.py --course EXAM101
#  python scripts/export_grades.py --courseid 4
# =====================================================================
import argparse
import subprocess
import sys
import time
from pathlib import Path

PROJECT_DIR = Path(__file__).resolve().parent.parent
SVC = "moodle"
CLI = "/opt/eap/scripts/export_grades.php"


def dc(*args, **kw):
    return subprocess.run(["docker", "compose", *args], cwd=PROJECT_DIR, **kw)


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument("--course", default="")
    ap.add_argument("--courseid", default="")
    ap.add_argument("--list", action="store_true")
    ap.add_argument("--service", default=SVC)
    args = ap.parse_args()

    base = ["exec", "-T", args.service, "runuser", "-u", "www-data", "--", "php", CLI]

    if args.list:
        dc(*base, "--list")
        return

    if not args.course and not args.courseid:
        sys.exit("Specify --course SHORTNAME or --courseid ID (or --list).")

    remote = f"/tmp/grade-export-{int(time.time())}.xlsx"
    cmd = base + [f"--out={remote}"]
    if args.courseid:
        cmd.append(f"--courseid={args.courseid}")
    else:
        cmd.append(f"--course={args.course}")

    r = dc(*cmd)
    if r.returncode != 0:
        sys.exit(r.returncode)

    # the php script may have appended .xlsx or .csv - find it
    find = dc("exec", "-T", args.service, "sh", "-c",
              f"ls {remote}* 2>/dev/null", capture_output=True, text=True)
    produced = find.stdout.strip().splitlines()
    if not produced:
        sys.exit("ERROR: no output file was produced inside the container.")

    outdir = PROJECT_DIR / "exports"
    outdir.mkdir(exist_ok=True)
    for rp in produced:
        local = outdir / Path(rp).name
        dc("cp", f"{args.service}:{rp}", str(local))
        dc("exec", "-T", args.service, "rm", "-f", rp)
        print(f"Saved: {local}")


if __name__ == "__main__":
    main()
