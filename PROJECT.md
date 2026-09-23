# Mock API Server

## Objetivo

Desenvolver uma pequena aplicação **Mock API Server + Request Inspector**, inspirada no conceito do Beeceptor.

A aplicação será hospedada em uma VPS própria e disponibilizada como projeto no GitHub.

O objetivo é criar uma ferramenta simples para desenvolvedores criarem endpoints HTTP temporários ou permanentes, configurarem respostas mock e inspecionarem as requisições recebidas.

A aplicação deve priorizar:

* simplicidade;
* baixo consumo de recursos;
* segurança;
* facilidade de instalação;
* facilidade de manutenção;
* boa organização do código;
* possibilidade de evolução futura.

Não tentar reproduzir todas as funcionalidades do Beeceptor.

A primeira versão deve ser um MVP funcional e bem estruturado.

---

# 1. Stack

Utilizar:

* PHP 8.3+
* Laravel
* MySQL ou MariaDB
* Blade
* JavaScript apenas quando necessário
* CSS simples e responsivo
* Composer
* Nginx
* PHP-FPM

Evitar frameworks JavaScript pesados.

A aplicação deve funcionar em uma VPS Linux com poucos recursos.

---

# 2. Arquitetura

Utilizar boas práticas do Laravel.

Estrutura esperada:

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Middleware/
├── Models/
├── Services/
└── Providers/

database/
├── migrations/
├── factories/
└── seeders/

resources/
├── views/
├── css/
└── js/

routes/
├── web.php
└── api.php

tests/
├── Feature/
└── Unit/
```

Princípios:

* Controllers pequenos.
* Regras de negócio em Services.
* Validação através de Form Requests.
* Models com responsabilidades claras.
* Migrations bem estruturadas.
* Testes automatizados.
* Configurações através de `.env`.
* Evitar abstrações desnecessárias.
* Evitar overengineering.

Seguir KISS, DRY e SOLID quando fizer sentido.

---

# 3. Conceito principal

A aplicação possui **Endpoints**.

Um usuário pode criar:

```text
payment-test
```

O sistema disponibiliza:

```text
https://mock.example.com/payment-test
```

Todas as requisições dentro desse namespace devem ser capturadas.

Exemplos:

```text
GET    /payment-test/users
POST   /payment-test/users
GET    /payment-test/orders/123
POST   /payment-test/webhook
DELETE /payment-test/users/123
```

O primeiro segmento da URL identifica o endpoint.

---

# 4. Estrutura da URL

Para o MVP utilizar:

```text
https://mock.example.com/{endpoint}/{path}
```

Exemplos:

```text
https://mock.example.com/payment-test/users
https://mock.example.com/payment-test/orders/123
https://mock.example.com/payment-test/webhook
```

Não implementar inicialmente subdomínios dinâmicos como:

```text
https://payment-test.mock.example.com/users
```

Essa possibilidade pode ser adicionada futuramente.

---

# 5. Endpoints

Cada endpoint deve possuir:

```text
id
name
slug
description
is_active
fallback_status
fallback_headers
fallback_body
created_at
updated_at
```

O `slug` deve ser único.

Exemplo:

```text
name: Payment Test
slug: payment-test
```

URL:

```text
https://mock.example.com/payment-test
```

---

# 6. Fallback Response

O endpoint deve funcionar como um **catch-all**.

Quando uma requisição chegar e nenhuma regra corresponder, o sistema deve retornar uma resposta padrão.

Exemplo:

```text
GET /payment-test/users
```

Se não existir regra correspondente:

```text
HTTP 200 OK
Content-Type: text/plain; charset=UTF-8
```

Body:

```text
Hey ya! Great to see you here. Btw, nothing is configured for this request path. Create a rule and start building a mock API.
```

Esse comportamento é inspirado no funcionamento observado em endpoints públicos do Beeceptor.

O fallback deve ser configurável por endpoint.

Valores padrão:

```text
status: 200
content-type: text/plain
body:
Hey ya! Great to see you here. Btw, nothing is configured for this request path. Create a rule and start building a mock API.
```

---

# 7. Fluxo de uma requisição

O processamento deve seguir:

```text
Request
   │
   ▼
Identificar endpoint
   │
   ├── Endpoint não existe
   │       │
   │       ▼
   │      404
   │
   ▼
Endpoint existe
   │
   ▼
Endpoint está ativo?
   │
   ├── Não
   │    │
   │    ▼
   │   404 ou 410
   │
   ▼
Buscar regras ativas
   │
   ▼
Avaliar regras por prioridade
   │
   ├── Regra encontrada
   │       │
   │       ▼
   │   Response configurada
   │
   └── Nenhuma regra
           │
           ▼
      Fallback Response
```

Importante:

```text
endpoint inexistente
```

é diferente de:

```text
endpoint existente sem regra
```

O primeiro deve retornar `404`.

O segundo deve retornar o fallback configurado.

---

# 8. Mock Rules

Cada endpoint pode possuir várias regras.

Tabela:

```text
mock_rules
```

Campos:

```text
id
endpoint_id
name
priority
method
path_pattern
path_match_type
query_conditions
header_conditions
body_conditions
response_status
response_headers
response_body
response_delay
is_active
created_at
updated_at
```

Relacionamento:

```text
Endpoint
    │
    └── hasMany MockRule
```

---

# 9. Prioridade das regras

As regras devem possuir prioridade.

Exemplo:

```text
priority 1
priority 2
priority 3
```

A menor prioridade numérica deve ser avaliada primeiro.

Exemplo:

```text
Regra 1 → priority 1
Regra 2 → priority 2
Regra 3 → priority 3
```

A aplicação deve avaliar:

```text
1 → 2 → 3
```

A primeira regra compatível deve ser utilizada.

O comportamento deve ser determinístico.

---

# 10. HTTP Methods

Suportar:

```text
GET
POST
PUT
PATCH
DELETE
OPTIONS
```

Opcionalmente permitir:

```text
HEAD
```

---

# 11. Path Matching

No MVP implementar:

### Exact

Exemplo:

```text
/users
```

corresponde somente a:

```text
/users
```

### Starts With

Exemplo:

```text
/users
```

corresponde a:

```text
/users
/users/1
/users/123/profile
```

### Contains

Exemplo:

```text
users
```

corresponde a paths contendo:

```text
/users
/admin/users
/api/users/list
```

Não implementar inicialmente regex ou sistemas complexos de expressão.

---

# 12. Query Parameters

Permitir condições simples.

Exemplo:

```text
status=active
```

Requisição:

```text
GET /users?status=active
```

A regra pode verificar:

```text
status = active
```

Suportar inicialmente:

* equals;
* not equals;
* exists.

---

# 13. Headers

Permitir condições simples.

Exemplo:

```text
Authorization = Bearer test
```

ou:

```text
X-Test = true
```

Suportar inicialmente:

* equals;
* not equals;
* exists.

Os nomes dos headers devem ser tratados de forma case-insensitive.

---

# 14. Body Matching

Para requisições JSON, permitir condições simples baseadas em propriedades.

Exemplo:

```json
{
  "email": "john@example.com"
}
```

Regra:

```text
email = john@example.com
```

Suportar inicialmente:

* propriedade existe;
* propriedade igual;
* propriedade diferente.

Não implementar inicialmente um sistema complexo de JSONPath ou expressões customizadas.

---

# 15. Response

Cada regra deve permitir configurar:

* HTTP status;
* headers;
* content-type;
* body;
* delay.

Exemplo:

```text
Status: 201
Content-Type: application/json
```

Body:

```json
{
  "id": 123,
  "name": "John"
}
```

Suportar:

```text
application/json
text/plain
text/html
application/xml
text/xml
```

---

# 16. Delay

Permitir configurar um atraso artificial na resposta.

Exemplo:

```text
response_delay = 1000
```

Significa:

```text
1 segundo
```

O valor deve possuir limite máximo configurável para evitar abuso.

Por exemplo:

```text
MAX_MOCK_DELAY=10000
```

Não permitir delays arbitrariamente grandes.

---

# 17. Request Inspector

Toda requisição recebida deve ser registrada.

Tabela:

```text
request_logs
```

Campos:

```text
id
endpoint_id
method
url
path
query_parameters
headers
body
ip_address
user_agent
matched_rule_id
response_status
response_headers
response_body
duration_ms
created_at
```

---

# 18. Request Log

Registrar:

### Request

* método;
* URL;
* path;
* query parameters;
* headers;
* body;
* IP;
* User Agent.

### Matching

* regra encontrada;
* ID da regra;
* prioridade;
* fallback utilizado ou não.

### Response

* status;
* headers;
* body;
* tempo de processamento.

---

# 19. Retenção dos logs

Não armazenar logs indefinidamente.

Configuração:

```env
REQUEST_LOG_RETENTION_DAYS=7
```

Criar comando:

```bash
php artisan requests:prune
```

O comando deve remover registros antigos.

Também preparar a execução através do scheduler do Laravel.

---

# 20. Limite de logs

Para evitar crescimento excessivo do banco:

* limitar tamanho do body;
* limitar tamanho dos headers;
* limitar tamanho da response;
* limitar quantidade de logs quando necessário.

Criar configurações no `.env`.

Exemplo:

```env
MAX_REQUEST_BODY_SIZE=1048576
MAX_RESPONSE_BODY_SIZE=1048576
REQUEST_LOG_RETENTION_DAYS=7
```

---

# 21. Dashboard

Criar uma área administrativa.

URL:

```text
/admin
```

A tela inicial deve mostrar:

* quantidade de endpoints;
* quantidade de requisições;
* requisições nas últimas 24 horas;
* endpoints mais utilizados;
* últimas requisições.

---

# 22. Lista de Endpoints

Mostrar:

* nome;
* slug;
* status;
* URL pública;
* quantidade de regras;
* quantidade de requisições;
* data de criação.

Ações:

* visualizar;
* editar;
* excluir;
* ativar/desativar.

---

# 23. Endpoint Detail

Mostrar:

```text
Nome
Descrição
Status
URL pública
Fallback
```

E:

```text
Rules
Requests
```

Permitir:

* criar regra;
* editar regra;
* excluir regra;
* ativar/desativar regra;
* visualizar requisições.

---

# 24. Request Detail

A tela deve separar claramente:

## Request

```text
Method
URL
Path
Query Parameters
Headers
Body
IP
User Agent
```

## Matching

```text
Matched Rule
Priority
Fallback Used
```

## Response

```text
Status
Headers
Body
Duration
```

Quando o conteúdo for JSON, formatar visualmente.

---

# 25. Autenticação

A área administrativa deve exigir autenticação.

Utilizar a solução recomendada/nativa do Laravel.

A API pública não deve exigir autenticação.

Exemplo:

```text
/admin
```

exige login.

Enquanto:

```text
/payment-test/users
```

é público.

---

# 26. Segurança

Implementar:

* CSRF na área administrativa;
* validação de inputs;
* escape de conteúdo;
* rate limiting;
* limite de body;
* limite de response;
* limite de delay;
* headers seguros;
* proteção contra mass assignment;
* autorização adequada;
* proteção contra SQL Injection através do ORM/query builder;
* proteção contra XSS no dashboard.

Nunca executar conteúdo enviado pelo usuário.

O body das respostas deve ser tratado como texto/dado.

Nunca permitir que um usuário execute:

```text
PHP
JavaScript no servidor
Shell
SQL arbitrário
```

através de uma configuração de mock.

---

# 27. CORS

Implementar suporte básico a CORS.

Responder corretamente a:

```text
OPTIONS
```

Permitir configuração de:

```env
CORS_ALLOWED_ORIGINS=*
CORS_ALLOWED_METHODS=*
CORS_ALLOWED_HEADERS=*
```

A configuração deve ser segura e fácil de alterar em produção.

---

# 28. Rate Limiting

As URLs públicas devem possuir rate limiting.

Exemplo inicial:

```text
60 requests/minute/IP
```

Tornar configurável:

```env
MOCK_RATE_LIMIT=60
MOCK_RATE_LIMIT_WINDOW=1
```

O sistema deve retornar:

```text
HTTP 429
```

quando o limite for excedido.

---

# 29. API Administrativa

Criar uma API administrativa.

Rotas:

```text
POST   /api/endpoints
GET    /api/endpoints
GET    /api/endpoints/{endpoint}
PUT    /api/endpoints/{endpoint}
DELETE /api/endpoints/{endpoint}
```

Rules:

```text
POST   /api/endpoints/{endpoint}/rules
GET    /api/endpoints/{endpoint}/rules
PUT    /api/rules/{rule}
DELETE /api/rules/{rule}
```

Requests:

```text
GET /api/endpoints/{endpoint}/requests
GET /api/requests/{request}
```

Essa API deve exigir autenticação.

---

# 30. Banco de Dados

Criar pelo menos:

```text
users
endpoints
mock_rules
request_logs
```

Relacionamentos:

```text
User
 │
 └── endpoints

Endpoint
 ├── mock_rules
 └── request_logs

MockRule
 └── endpoint

RequestLog
 ├── endpoint
 └── matched_rule
```

Criar:

* foreign keys;
* indexes;
* unique constraints;
* timestamps.

Índices importantes:

```text
endpoints.slug
mock_rules.endpoint_id
mock_rules.priority
request_logs.endpoint_id
request_logs.created_at
request_logs.method
request_logs.response_status
request_logs.path
```

---

# 31. Exemplo de utilização

Criar endpoint:

```text
payment-test
```

URL:

```text
https://mock.example.com/payment-test
```

Enviar:

```bash
curl -X POST \
  https://mock.example.com/payment-test/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John"}'
```

Sem regra:

```text
HTTP 200
```

Response:

```text
Hey ya! Great to see you here. Btw, nothing is configured for this request path. Create a rule and start building a mock API.
```

Criar regra:

```text
Method: POST
Path: /users
Status: 201
```

Response:

```json
{
  "id": 1,
  "name": "John"
}
```

Enviar novamente:

```bash
curl -X POST \
  https://mock.example.com/payment-test/users \
  -H "Content-Type: application/json" \
  -d '{"name":"John"}'
```

Agora retornar:

```http
HTTP/1.1 201
Content-Type: application/json
```

```json
{
  "id": 1,
  "name": "John"
}
```

A requisição deve aparecer no Request Inspector.

---

# 32. Endpoint inexistente

Se o usuário acessar:

```text
https://mock.example.com/does-not-exist/users
```

e `does-not-exist` não existir como endpoint:

```http
HTTP 404
```

Response:

```json
{
  "message": "Mock endpoint not found."
}
```

Isso é diferente do fallback.

---

# 33. Endpoint desativado

Se um endpoint existir, mas estiver desativado:

```text
is_active = false
```

não processar as regras.

Retornar:

```http
HTTP 404
```

ou:

```http
HTTP 410
```

Escolher uma estratégia e documentá-la.

---

# 34. Docker

Criar suporte opcional a Docker.

Arquivos:

```text
Dockerfile
docker-compose.yml
.dockerignore
```

O projeto também deve funcionar sem Docker.

Instalação direta:

```text
Nginx
PHP-FPM
MySQL/MariaDB
Composer
```

---

# 35. VPS

A aplicação será executada em uma VPS Linux.

Documentar instalação para Ubuntu/Debian.

Documentar:

* PHP;
* extensões necessárias;
* Composer;
* MySQL/MariaDB;
* Nginx;
* PHP-FPM;
* SSL;
* Let's Encrypt;
* permissões;
* `.env`;
* migrations;
* cache;
* scheduler;
* cron;
* logs.

Exemplo:

```text
https://mock.example.com
```

Nginx deve encaminhar as requisições para:

```text
public/index.php
```

O Laravel deve receber corretamente:

```text
/mock-endpoint/*
```

---

# 36. Scheduler

Utilizar o scheduler do Laravel para limpeza dos logs.

Exemplo:

```text
requests:prune
```

Executar diariamente.

Documentar configuração do cron:

```text
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

# 37. GitHub

O projeto deve estar pronto para publicação.

Criar:

```text
README.md
.env.example
.gitignore
LICENSE
```

Não versionar:

```text
.env
/vendor
/node_modules
/storage/logs
```

Nunca incluir:

* senhas;
* tokens;
* credenciais;
* dados reais;
* arquivos da VPS;
* logs reais.

---

# 38. README

O README deve explicar:

* o que é o projeto;
* funcionalidades;
* stack;
* requisitos;
* instalação;
* configuração;
* execução local;
* criação de endpoint;
* criação de regra;
* utilização com curl;
* deploy na VPS;
* configuração Nginx;
* configuração SSL;
* scheduler;
* testes.

Incluir exemplos reais de utilização.

---

# 39. Testes

Criar testes automatizados.

Testar:

### Endpoints

* criação;
* edição;
* exclusão;
* slug único;
* ativação;
* desativação.

### Rules

* criação;
* edição;
* exclusão;
* prioridade;
* ativação;
* matching.

### Matching

Testar:

* método;
* path exact;
* path starts with;
* path contains;
* query parameter;
* header;
* body;
* combinação de condições;
* prioridade.

### Responses

Testar:

* status;
* headers;
* body;
* content-type;
* delay.

### Fallback

Testar:

```text
endpoint existe
+
nenhuma regra corresponde
=
HTTP 200 + fallback
```

### Endpoint inexistente

Testar:

```text
endpoint não existe
=
HTTP 404
```

### Logs

Testar:

* request;
* response;
* regra encontrada;
* fallback;
* duração.

### Segurança

Testar:

* rate limiting;
* autenticação;
* autorização;
* tamanho máximo do body.

Priorizar Feature Tests para os fluxos HTTP.

---

# 40. Estrutura sugerida de Services

Criar Services somente quando houver responsabilidade real.

Possível estrutura:

```text
app/Services/
├── MockRequestService.php
├── MockRuleMatcher.php
├── MockResponseService.php
└── RequestLogService.php
```

Responsabilidades:

### MockRequestService

Orquestrar o processamento da requisição.

### MockRuleMatcher

Encontrar a primeira regra compatível.

### MockResponseService

Construir a resposta HTTP.

### RequestLogService

Persistir informações da requisição e resposta.

Não criar Services artificiais apenas para seguir um padrão.

---

# 41. Request Lifecycle

O fluxo interno recomendado:

```text
HTTP Request
     │
     ▼
Identify Endpoint
     │
     ▼
Validate Endpoint
     │
     ▼
Load Active Rules
     │
     ▼
MockRuleMatcher
     │
     ├── Match
     │     │
     │     ▼
     │  Build Response
     │
     └── No Match
           │
           ▼
      Build Fallback
           │
           ▼
      Save RequestLog
           │
           ▼
       HTTP Response
```

O sistema deve garantir que o logging não altere o comportamento esperado da resposta.

---

# 42. Performance

A aplicação deve ser adequada para uma VPS pequena.

Priorizar:

* queries simples;
* índices;
* eager loading quando necessário;
* paginação;
* limites de payload;
* retenção de logs;
* cache quando realmente necessário;
* baixo número de dependências.

Evitar:

* serviços externos;
* filas sem necessidade;
* workers permanentes;
* processamento pesado;
* frameworks adicionais.

---

# 43. Interface

A interface deve ser simples e responsiva.

Priorizar:

* clareza;
* velocidade;
* legibilidade;
* navegação simples;
* boa visualização de JSON;
* boa visualização de headers;
* destaque para método HTTP e status.

Não utilizar animações desnecessárias.

Não criar um frontend SPA sem necessidade.

Blade é suficiente para o MVP.

---

# 44. Fora do escopo do MVP

Não implementar inicialmente:

* GraphQL;
* SOAP;
* gRPC;
* OpenAPI import;
* IA;
* geração automática de dados;
* proxy reverso;
* túnel localhost;
* CRUD stateful;
* billing;
* pagamentos;
* planos comerciais;
* multi-tenancy avançado;
* subdomínios dinâmicos;
* regex avançado;
* scripting de respostas;
* execução de JavaScript/PHP;
* integração com serviços externos.

Esses recursos podem ser considerados posteriormente.

---

# 45. Roadmap futuro

Possíveis versões futuras:

## V2

* subdomínios dinâmicos;
* autenticação por API token;
* múltiplos projetos;
* respostas dinâmicas;
* variáveis;
* templates;
* regex;
* filtros avançados.

## V3

* OpenAPI import;
* geração automática de mocks;
* proxy;
* histórico avançado;
* exportação de requests;
* compartilhamento de endpoints.

Esses recursos não devem influenciar negativamente a arquitetura do MVP.

---

# 46. Desenvolvimento por fases

Não implementar todo o projeto de uma vez.

## Fase 1 — Base

Implementar:

* Laravel;
* banco;
* autenticação;
* layout;
* dashboard;
* migrations;
* estrutura inicial.

Validar antes de continuar.

---

## Fase 2 — Endpoints

Implementar:

* CRUD de endpoints;
* slug;
* status;
* URL pública;
* fallback.

Validar:

```text
GET /endpoint
```

e:

```text
GET /endpoint/anything
```

---

## Fase 3 — Request Capture

Implementar:

* captura das requisições;
* request logs;
* método;
* path;
* query;
* headers;
* body;
* IP;
* User Agent.

---

## Fase 4 — Mock Rules

Implementar:

* CRUD de regras;
* prioridade;
* method matching;
* path matching;
* query matching;
* header matching;
* body matching.

---

## Fase 5 — Responses

Implementar:

* status;
* headers;
* body;
* content-type;
* delay.

---

## Fase 6 — Inspector

Implementar:

* lista de requests;
* detalhes;
* request;
* matching;
* response;
* JSON formatting.

---

## Fase 7 — Segurança

Implementar:

* rate limiting;
* limites de payload;
* limites de response;
* limite de delay;
* CORS;
* validações;
* autorização.

---

## Fase 8 — Testes

Implementar todos os testes descritos.

Executar:

```bash
php artisan test
```

Todos os testes devem passar.

---

## Fase 9 — Deploy

Implementar documentação e validar:

```text
VPS
  ↓
Nginx
  ↓
PHP-FPM
  ↓
Laravel
  ↓
MySQL
```

Configurar:

* domínio;
* SSL;
* scheduler;
* permissões;
* cache;
* logs.

---

# 47. Critérios de conclusão

O MVP será considerado concluído quando:

* a aplicação funcionar localmente;
* a aplicação funcionar em uma VPS;
* o dashboard exigir autenticação;
* for possível criar um endpoint;
* o endpoint possuir URL pública;
* requisições externas forem capturadas;
* requisições forem registradas;
* for possível criar regras;
* regras forem avaliadas por prioridade;
* method matching funcionar;
* path matching funcionar;
* query matching funcionar;
* header matching funcionar;
* body matching funcionar;
* responses configuradas forem retornadas;
* fallback funcionar;
* endpoint inexistente retornar 404;
* endpoint sem regra retornar fallback 200;
* Request Inspector funcionar;
* rate limiting funcionar;
* limpeza de logs funcionar;
* testes automatizados passarem;
* README estiver completo;
* `.env.example` estiver disponível;
* Docker estiver disponível;
* deploy em VPS estiver documentado.

---

# 48. Regras para o LLM durante a implementação

Não implementar funcionalidades fora do escopo sem solicitar autorização.

Antes de cada fase:

1. explicar brevemente o que será implementado;
2. explicar decisões arquiteturais relevantes;
3. listar arquivos que serão criados ou modificados;
4. implementar;
5. executar/verificar os testes;
6. explicar como validar manualmente.

Ao modificar o projeto, mostrar somente as alterações relevantes.

Não substituir arquivos completos quando apenas pequenas alterações forem necessárias.

Não criar código fictício ou pseudocódigo quando for possível implementar a funcionalidade real.

Não assumir serviços externos.

Não adicionar dependências sem justificar sua necessidade.

Sempre considerar segurança, performance e manutenção.

Ao encontrar uma decisão arquitetural ambígua, escolher a solução mais simples e documentar a decisão.

O resultado deve ser um projeto Laravel pequeno, funcional, seguro, auto-hospedável e adequado para ser publicado no GitHub.
