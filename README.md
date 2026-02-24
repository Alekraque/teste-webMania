## WebMania Expeditions API

API REST desenvolvida em Laravel para o teste técnico da WebMania, responsável por:

- **Autenticar reinos e conselheiros**
- **Registrar expedições**
- **Permitir que o Conselho aprove ou rejeite expedições**
- **Consultar o status de uma expedição por protocolo**

A modelagem completa da solução (análise, arquitetura, riscos, etc.) está descrita em `ANALISE.MD`.

---

## Resumo do Projeto

- **Contexto:** Responder ao chamado dos Reinos da Terra-média ao Conselho de Elrond, substituindo o modelo de cartas por uma API REST estruturada.
- **Objetivo principal:** Padronizar o registro, decisão e acompanhamento de expedições oficiais.
- **Pilares da solução:**
  - Segurança via autenticação com tokens (Laravel Sanctum)
  - Rastreamento por protocolo único de expedição
  - Decisão explícita de expedições (APPROVED / REJECTED)
  - Documentação formal via OpenAPI (Swagger)

---

## Documentação da API (Swagger)

- **URL local da documentação Swagger (interface):**  
  `http://127.0.0.1:8000/docs`

- **Especificação OpenAPI (arquivo YAML servido pela aplicação):**  
  `http://127.0.0.1:8000/openapi.yaml`

- **Servidor base definido na especificação:**  
  `http://127.0.0.1:8000/api`

Abra `http://127.0.0.1:8000/docs` após subir o servidor Laravel para visualizar, testar as rotas e inspecionar os contratos de request/response.

---

## Rotas HTTP da API

Base das rotas de API: `http://127.0.0.1:8000/api`

### Autenticação

- **Registrar reino**
  - **POST** `/api/kingdom/register`  
  - **Descrição:** Cria um novo reino e retorna um token de autenticação.

- **Login do reino**
  - **POST** `/api/kingdom/login`  
  - **Descrição:** Autentica um reino e retorna um `access_token` (Bearer).

- **Login do conselho**
  - **POST** `/api/council/login`  
  - **Descrição:** Autentica um conselheiro do Conselho de Elrond e retorna `access_token`.

- **Registrar conselheiro (apenas admin)**
  - **POST** `/api/council/register`  
  - **Descrição:** Cria um novo conselheiro (perfil admin ou member).  
  - **Auth:** Bearer token de um conselheiro administrador.

### Expedições

- **Criar expedição**
  - **POST** `/api/expeditions`  
  - **Descrição:** Cria uma nova expedição vinculada a um reino.  
  - **Body (exemplo simplificado):**
    ```json
    {
      "kingdom_id": 2,
      "journey_description": "Expedição ao Deserto de Zhar para recuperar o Orbe Solar",
      "participants": [
        { "name": "Lyra Windfall", "race": "Humana" }
      ],
      "artifacts": [
        { "name": "Orbe Solar", "type": "Relic" }
      ]
    }
    ```

- **Decidir expedição (aprovar/rejeitar)**
  - **PATCH** `/api/expeditions/{protocol}/decision`  
  - **Descrição:** Permite ao Conselho aprovar ou rejeitar uma expedição existente.  
  - **Auth:** Bearer token de conselheiro.  
  - **Path params:** `protocol` – identificador único da expedição.  

- **Consultar status da expedição por protocolo**
  - **GET** `/api/expeditions/{protocol}/status`  
  - **Descrição:** Retorna o status atual da expedição (ex.: `PENDING`, `APPROVED`, `REJECTED`) e informações relacionadas.  
  - **Auth:** Bearer token (reino dono da expedição ou conselheiro).

> Para detalhes completos de payloads, códigos de resposta e exemplos, consulte o Swagger em `http://127.0.0.1:8000/docs` ou o arquivo `public/openapi.yaml`.

---

## Como rodar o projeto (local)

- **1. Instalar dependências PHP**
  ```bash
  composer install
  ```

- **2. Configurar ambiente**
  ```bash
  cp .env.example .env
  php artisan key:generate
  # Ajuste as credenciais de banco no .env
  ```

### Banco de Dados (PostgreSQL) e Variáveis de Ambiente

- **Banco de dados utilizado:** PostgreSQL
- **Driver Laravel:** `DB_CONNECTION=pgsql`

As variáveis de ambiente de banco de dados seguem o padrão definido em `.env.example` (linhas 23–28) e **devem apontar para uma instância PostgreSQL válida**:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=seu_banco_postgres
DB_USERNAME=seu_usuario_postgres
DB_PASSWORD=sua_senha_postgres
```

**Alertas importantes sobre configuração:**

- **`DB_CONNECTION`**  
  - Deve ser `pgsql`. Se estiver como `mysql` (padrão do Laravel) ou outro driver, as migrations e a aplicação **não funcionarão corretamente**.

- **`DB_PORT`**  
  - Em ambientes padrão PostgreSQL use `5432`. Se a porta do seu servidor for diferente, atualize aqui, mas **garanta que o valor bate com a configuração real do Postgres**.

- **`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`**  
  - Devem existir no servidor PostgreSQL. Caso estejam incorretos, você verá erros de conexão ao rodar `php artisan migrate` ou ao subir a aplicação.

- **`SESSION_DRIVER` (no `.env`)**  
  - Para este projeto, o valor esperado é:
    ```env
    SESSION_DRIVER=file,
    CACHE_STORE=file
    ```
  - Se você alterar para `database`, `redis` ou outro driver, será necessário configurar as respectivas tabelas/serviços. Caso contrário, **login, autenticação e rotas protegidas poderão falhar de forma imprevisível**.


- **3. Rodar migrations**
  ```bash
  php artisan migrate
  ```

## Ordem Recomendada das Migrations (por dependência de FK)
1. `database/migrations/2026_02_20_211926_create_kingdoms_table.php`
2. `database/migrations/2026_02_20_212723_create_council_members_table.php`
3. `database/migrations/2026_02_20_211136_create_expeditions_table.php`
4. `database/migrations/2026_02_20_231127_create_expedition_participants_table.php`
5. `database/migrations/2026_02_20_231346_create_expedition_artifacts_table.php`
6. `database/migrations/2026_02_22_052737_create_participants_table.php`
7. `database/migrations/2026_02_22_052923_create_artifacts_table.php`
8. `database/migrations/2026_02_22_094930_create_personal_access_tokens_table.php`
9. `database/migrations/2026_02_23_023837_add_role_to_council_members_table.php`


- **4. Subir o servidor**
  ```bash
  php artisan serve
  ```

Com o servidor rodando:
- API disponível em: `http://127.0.0.1:8000/api`
- Swagger UI em: `http://127.0.0.1:8000/docs`
