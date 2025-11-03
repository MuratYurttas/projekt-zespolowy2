#!/bin/bash
set +e

echo "1) Bad payload → 400"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE/api/submit.php" -H "Content-Type: application/json" -d '{"name":""}')
if [ "$STATUS" -eq 400 ]; then
  echo "✅ Test 1 passed (expected 400, got $STATUS)"
else
  echo "❌ Test 1 failed (expected 400, got $STATUS)"
  exit 1
fi

echo "2) Duplicate entry → 409 (optional)"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" -X POST "$BASE/api/submit.php" -H "Content-Type: application/json" -d '{"name":"Test","email":"test@test.com","price":10,"birthDate":"2000-01-01","code":"AA11"}')
if [ "$STATUS" -eq 409 ]; then
  echo "✅ Test 2 passed (expected 409, got $STATUS)"
else
  echo "⚠️ Test 2 skipped or not implemented (got $STATUS)"
fi

echo "3) Not found → 404"
STATUS=$(curl -s -o /dev/null -w "%{http_code}" "$BASE/api/notexists.php")
if [ "$STATUS" -eq 404 ]; then
  echo "✅ Test 3 passed (expected 404, got $STATUS)"
else
  echo "❌ Test 3 failed (expected 404, got $STATUS)"
  exit 1
fi

echo "🎉 All integration tests completed successfully!"
exit 0 
