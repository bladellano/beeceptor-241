# Mock API Server

Ferramenta auto-hospedável inspirada no conceito do Beeceptor: crie endpoints HTTP mock, configure regras de resposta e inspecione requisições recebidas.

## Funcionalidades

- Endpoints públicos em `/{slug}/{path}`
- Regras mock com prioridade, método, path (exact / starts with / contains), query, headers e body JSON
- Fallback configurável por endpoint
- Request Inspector (logs de request/response)
- Dashboard administrativo em `/admin` (autenticação Laravel)
- API administrativa em `/api/*` (mesma sessão autenticada)
- Rate limiting, limites de payload, CORS e limpeza de logs (`requests:prune`)

## Stack

- PHP 8.4+, Laravel, MySQL/MariaDB (ou SQLite em dev)
- Blade + CSS simples

## Requisitos

- PHP 8.4+ com extensões: `pdo`, `mbstring`, `openssl`, `tokenizer`, `xml`, `ctype`, `json`, `bcmath`
- Composer
- MySQL/MariaDB ou SQLite

## Instalação local

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Acesse `http://127.0.0.1:8002/login` com as credenciais do `.env` (`ADMIN_EMAIL` / `ADMIN_PASSWORD`, padrão `admin@example.com` / `password`).

## Uso rápido

1. Crie um endpoint com slug `payment-test` no admin.
2. Teste sem regra:

```bash
curl -i http://127.0.0.1:8002/payment-test/users
```

3. Crie uma regra `POST /users` com status `201` e body JSON.
4. Envie novamente:

```bash
curl -i -X POST http://127.0.0.1:8002/payment-test/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John"}'
```

Endpoint inexistente retorna `404` com `{"message":"Mock endpoint not found."}`. Endpoint desativado retorna `410`.

## Configuração

Principais variáveis (ver `.env.example`):

| Variável | Descrição |
|----------|-----------|
| `MOCK_RATE_LIMIT` | Requisições por IP na janela |
| `MAX_MOCK_DELAY` | Delay máximo (ms) |
| `REQUEST_LOG_RETENTION_DAYS` | Retenção dos logs |
| `CORS_*` | Cabeçalhos CORS das rotas mock |

## Scheduler

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Executa `requests:prune` diariamente.

## Docker (opcional)

```bash
docker compose up --build
```

App em `http://localhost:8080`. O container roda migrations, seed do admin e caches ao iniciar (`docker/entrypoint.sh`).

Em PaaS (Easypanel, Coolify etc.), use o build por **Dockerfile**, porta **80**. Com SQLite, monte um volume persistente e aponte `DB_DATABASE` para ele (ex.: `/data/database.sqlite`).

## Deploy VPS (Nginx + PHP-FPM)

- Document root: `public/`
- `try_files $uri $uri/ /index.php?$query_string;`
- PHP-FPM 8.4+, MySQL/MariaDB
- Permissões em `storage/` e `bootstrap/cache/`
- SSL via Let's Encrypt (Certbot)
- Cron do scheduler (acima)

## Testes

```bash
php artisan test
```

## Licença

MIT — veja [LICENSE](LICENSE).
