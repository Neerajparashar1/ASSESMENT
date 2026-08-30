#!/usr/bin/env bash
# Log in as a student and hit local/sebkiosk/finish.php the way the
# quiz page's sendBeacon() would, to prove leaving SEB auto-submits.
#   bash native/_hit_finish.sh s2026002 4 <attemptid>
set -e
USER_="${1:-s2026002}"; CMID="${2:-4}"; ATTEMPT="${3:-0}"
PASS="${STU_PASS:-Exam@2026}"
BASE="http://localhost:8080"
J="$(mktemp)"

LT=$(curl -s -c "$J" "$BASE/login/index.php" | grep -oP 'name="logintoken" value="\K[^"]+' | head -1)
curl -s -b "$J" -c "$J" --data-urlencode "username=$USER_" --data-urlencode "password=$PASS" \
  --data-urlencode "logintoken=$LT" -o /dev/null -w "login=%{http_code}\n" "$BASE/login/index.php"

# scrape this session's sesskey
SK=$(curl -s -b "$J" "$BASE/my/" | grep -oP 'sesskey=\K[0-9A-Za-z]+' | head -1)
echo "sesskey=$SK"

echo "--- POST finish.php (beacon path) ---"
curl -s -b "$J" -o /dev/null -w "beacon POST: %{http_code}\n" \
  --data-urlencode "beacon=1" --data-urlencode "sesskey=$SK" \
  --data-urlencode "attempt=$ATTEMPT" --data-urlencode "cmid=$CMID" \
  "$BASE/local/sebkiosk/finish.php"

echo "--- GET finish.php (SEB quitURL path) ---"
curl -s -b "$J" -w "\nquitURL GET: %{http_code}\n" "$BASE/local/sebkiosk/finish.php?cmid=$CMID" | tail -3
rm -f "$J"
