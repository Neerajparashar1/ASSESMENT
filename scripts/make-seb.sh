#!/usr/bin/env bash
# =====================================================================
#  PHASE 4.1 - Safe Exam Browser config generator
#  Produces a locked-down .seb (kiosk) file for a given portal URL and
#  quit password.  Output goes to seb/generated/<name>.seb
#
#  Usage:
#    ./scripts/make-seb.sh                       # uses .env values
#    ./scripts/make-seb.sh https://exams.edu 'QuitPass123!' final-exam
# =====================================================================
set -euo pipefail
cd "$(dirname "$0")/.."

# --- inputs ---------------------------------------------------------
if [ -f .env ]; then set -a; . ./.env; set +a; fi
URL="${1:-${MOODLE_WWWROOT:-http://localhost:8080}}"
QUITPW="${2:-${SEB_QUIT_PASSWORD:-changeme}}"
NAME="${3:-exam-$(date +%Y%m%d-%H%M%S)}"
HOSTPORT="$(printf '%s' "$URL" | sed -E 's#^https?://##; s#/.*$##')"

# --- hash the quit password (SHA256 hex, as SEB expects) -----------
if command -v sha256sum >/dev/null 2>&1; then
  HASH="$(printf '%s' "$QUITPW" | sha256sum | awk '{print $1}')"
else
  HASH="$(printf '%s' "$QUITPW" | shasum -a 256 | awk '{print $1}')"
fi

mkdir -p seb/generated
OUT="seb/generated/${NAME}.seb"

sed \
  -e "s#http://localhost:8080/login/index.php#${URL%/}/login/index.php#g" \
  -e "s#http://localhost:8080/mod/quiz/accessrule/seb/config.php#${URL%/}/mod/quiz/accessrule/seb/config.php#g" \
  -e "s#da833dc36caf876a515fe4c574612c2988a87799e478e3335ed63d05e25d43a2#${HASH}#g" \
  -e "s#localhost:8080/\*;127.0.0.1:8080/\*#${HOSTPORT}/*#g" \
  -e "s#<string>localhost:8080/\*</string>#<string>${HOSTPORT}/*</string>#g" \
  seb/exam-default.seb > "$OUT"

echo "Generated: $OUT"
echo "  start URL : ${URL%/}/login/index.php"
echo "  quit host : ${HOSTPORT}"
echo "  quit hash : ${HASH}"
echo
echo "Next: in the Moodle quiz -> 'Safe Exam Browser' section ->"
echo "      'Require the use of Safe Exam Browser' = 'Yes - Upload my own config'"
echo "      then upload $OUT"
