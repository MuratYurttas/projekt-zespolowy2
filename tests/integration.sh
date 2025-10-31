#!/usr/bin/env bash
set -e

BASE=${BASE:-http://127.0.0.1:8000}

echo "1) Bad payload → 400"
code=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE/api/submit.php" \
  -d "name=A&email=bad&price=-1&birthDate=2050-01-01&code=xx")
test "$code" = "400"

echo "2) Created → 201"
code=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE/api/submit.php" \
  -d "name=Adam Nowak&email=adam@test.pl&price=10&birthDate=2000-01-01&code=ABCD-1")
test "$code" = "201"

echo "3) Duplicate → 409"
code=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE/api/submit.php" \
  -d "name=Adam&email=adam@test.pl&price=20&birthDate=2000-01-01&code=ABCD-2")
test "$code" = "409"

echo "4) Missing resource → 404"
code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/api/get.php?email=notfound@test.pl")
test "$code" = "404"

echo "5) No token → 401"
code=$(curl -s -o /dev/null -w "%{http_code}" -X DELETE "$BASE/api/delete.php?email=adam@test.pl")
test "$code" = "401"

echo "6) Wrong token → 403"
code=$(curl -s -o /dev/null -w "%{http_code}" -H "Authorization: Bearer WRONG" -X DELETE "$BASE/api/delete.php?email=adam@test.pl")
test "$code" = "403"

echo "7) OK token but not found → 404"
code=$(curl -s -o /dev/null -w "%{http_code}" -H "Authorization: Bearer SECRET123" -X DELETE "$BASE/api/delete.php?email=notfound@test.pl")
test "$code" = "404"

echo "SMOKE) /health → 200"
code=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/api/health.php")
test "$code" = "200"

echo "All integration tests passed."
