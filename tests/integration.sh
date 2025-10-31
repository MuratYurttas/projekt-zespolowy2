#!/bin/bash
set -e

echo "1) Bad payload → 400"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE/api/submit.php" -H "Content-Type: application/json" -d '{"name":""}')
if [ "$STATUS" -ne 400 ]; then
  echo "Expected 400 but got $STATUS"
  exit 1
fi

echo "2) Duplicate entry → 409"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE/api/submit.php" -H "Content-Type: application/json" -d '{"name":"Test","email":"test@test.com","price":10,"birthDate":"2000-01-01","code":"AA11"}')
if [ "$STATUS" -ne 409 ]; then
  echo "Expected 409 but got $STATUS"
  exit 1
fi

echo "3) Not found → 404"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/api/notexists.php")
if [ "$STATUS" -ne 404 ]; then
  echo "Expected 404 but got $STATUS"
  exit 1
fi

echo "✅ All integration tests passed."
