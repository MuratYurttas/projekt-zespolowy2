# Zadanie: Walidacje + CI/CD + Testy

## Uruchomienie lokalne
- XAMPP: `http://localhost/proje/public/index.html`
- API: `public/../api/*.php`
- Health: `/api/health.php`

## Testy
- `php -S 127.0.0.1:8000 -t . &`
- `chmod +x tests/integration.sh && ./tests/integration.sh`

## CI/CD
- GitHub Actions: `.github/workflows/ci.yml`
- Testy uruchamiane automatycznie po pushu.
- Deploy (opcjonalnie) przez hook: `DEPLOY_HOOK_URL`.

## Token
- `Authorization: Bearer SECRET123`
