# Resposta ao Chamado dos Reinos da Terra-média

**Ao Conselho de Elrond e aos Representantes dos Reinos Livres da Terra-média**

Recebemos e analisamos o chamado formal solicitando a criação de uma API REST oficial do Conselho para o gerenciamento de expedições. Apresentamos abaixo nossa análise técnica e proposta de solução.

---

## 1. Classificação da Solicitação

**Tipo de Demanda:** Desenvolvimento de Sistema Crítico de Infraestrutura

- **Categoria:** Infraestrutura de Comunicação e Processamento
- **Prioridade:** Alta (afeta operações críticas dos reinos)
- **Escopo:** Sistema completo de gerenciamento de expedições
- **Natureza:** Projeto greenfield com integração a sistemas existentes

A solução representa uma modernização fundamental do processo de comunicação entre os reinos e o Conselho, migrando de correspondência física para um modelo digital estruturado e automatizado.

---

## 2. Impacto e Complexidade da Implementação

### 2.1 Impacto

**Organizacional:**
- Redução significativa do tempo de tramitação (de dias/semanas para minutos/horas)
- Melhoria na rastreabilidade e transparência dos processos
- Eliminação de riscos de extravio e perda de informações

**Técnico:**
- Necessidade de infraestrutura de servidores, banco de dados e sistemas de autenticação
- Estabelecimento de processos de monitoramento e manutenção
- Treinamento de equipes técnicas dos reinos

**Estratégico:**
- Fortalecimento da comunicação inter-reinos
- Padronização de processos de solicitação
- Criação de base histórica estruturada para análises futuras

### 2.2 Complexidade

**Nível:** Média a Alta

**Fatores principais:**
1. Arquitetura distribuída para múltiplos reinos simultâneos
2. Processamento assíncrono para análise de expedições
3. Segurança robusta para proteção de dados sensíveis
4. Integridade e rastreabilidade completa dos dados
5. Escalabilidade para suportar crescimento no volume de solicitações
6. Disponibilidade 24/7 para os reinos

---

## 3. Pontos Críticos e Riscos Técnicos

### 3.1 Pontos Críticos

1. **Segurança e Autenticação**
   - Proteção contra acesso não autorizado
   - Mecanismos robustos de autenticação e autorização
   - Proteção contra ataques de força bruta e DDoS

2. **Integridade e Rastreabilidade**
   - Garantia de que nenhuma solicitação seja perdida ou alterada
   - Histórico completo e imutável de todas as operações
   - Auditoria completa de todas as ações

3. **Disponibilidade e Performance**
   - Sistema crítico que não pode estar indisponível
   - Alta disponibilidade e redundância
   - Performance adequada sob carga elevada

4. **Processamento Assíncrono**
   - Garantia de processamento de todas as solicitações
   - Tratamento adequado de falhas durante o processamento
   - Notificações confiáveis sobre mudanças de status

5. **Migração e Compatibilidade**
   - Transição do sistema atual para o novo sistema
   - Compatibilidade com diferentes sistemas dos reinos
   - Treinamento e adoção pelos usuários

### 3.2 Riscos Técnicos

| Risco | Probabilidade | Impacto | Mitigação |
|-------|--------------|---------|-----------|
| Falhas de autenticação | Média | Alto | OAuth 2.0/JWT com rate limiting |
| Perda de dados | Baixa | Crítico | Backups automáticos e replicação |
| Indisponibilidade | Média | Alto | Infraestrutura redundante e monitoramento 24/7 |
| Processamento incompleto | Média | Médio | Sistema de filas com retry e dead-letter queue |
| Ataques de segurança | Média | Alto | WAF, rate limiting, validação rigorosa |
| Problemas de escalabilidade | Baixa | Médio | Arquitetura horizontalmente escalável |

---

## 4. Plano de Resolução

### 4.1 Arquitetura Geral da API REST

**Princípios:**
- RESTful para máxima interoperabilidade
- Stateless (cada requisição contém todas as informações necessárias)
- Arquitetura em camadas para separação de responsabilidades
- Respostas cacheáveis quando apropriado

**Arquitetura em Camadas:**

```
Camada de Apresentação: API REST, Rate Limit, WAF/CDN
         ↓
Camada de Aplicação: Controllers, Middleware, Validators
         ↓
Camada de Negócio: Services, Business Logic, Events Handler
         ↓
Camada de Dados: Repository, Database, Queue System
         ↓
Infraestrutura: Database (Replica), Cache (Redis), Logging & Monitor
```

**Stack Tecnológico:**
- **Framework:** Laravel (PHP)
- **Banco de Dados:** PostgreSQL
- **Cache/Filas:** Redis
- **Autenticação:** Laravel Sanctum/Passport (OAuth 2.0)
- **Documentação:** OpenAPI/Swagger

### 4.2 Principais Endpoints

**Autenticação:**
```
POST   /api/kingdom/login       #login do reino
POST   /api/council/login       #login do conselheiro
```

**Gerenciamento de Expedições:**
```
POST   /api/expeditions              # Registrar nova expedição
GET    /api/expeditions/{protocol}   # Consultar expedição por protocolo
```

**Acompanhamento de Status:**
```
GET    /api/expeditions/{protocol}/status      # Status atual
```

**Endpoints Administrativos (Conselho):**
```
GET    /api/expeditions/listAll        # Listar todas expedições
PATCH  /api/expeditions/{protocol}/decision         #decidir a expedição
```

**Estrutura de Dados - Expedição (exemplo):**
```json
{
  "kingdom_id": 2,
  "journey_description": "Expedição ao Deserto de Zhar para recuperar o Orbe Solar",
  "participants": [
    {
      "name": "Lyra Windfall",
      "race": "Humana"
    },
    {
      "name": "Drogan Firebeard",
      "race": "Anão"
    },
    {
      "name": "Seraphiel",
      "race": "Elfo"
    },
    {
      "name": "Morgath",
      "race": "Orc"
    }
  ],
  "artifacts": [
    {
      "name": "Orbe Solar",
      "type": "Relic"
    },
    {
      "name": "Espada do Crepúsculo",
      "type": "Weapon"
    },
    {
      "name": "Amuleto de Proteção Arcana",
      "type": "Accessory"
    }
  ]
}
```

### 4.3 Fluxo de Autenticação

**Método:** Auth com Sanctum e token nativo

**Fluxo:**

1. **Login:** Reino envia credenciais → API valida → Retorna access_token
2. **Requisições:** Reino inclui `Authorization: Bearer {access_token}` no header
3. **Middleware:** Verifica token, valida expiração e permissões

**Segurança:**
- HTTPS obrigatório (versão final da API em produção)
- Token expiration: Access tokens (1 hora)

**Modelo de Permissões:**
- **Reino:** Criar e consultar apenas suas expedições
- **Conselheiro:** Consultar todas expedições e aprovar/rejeitar
- **Conselheiro Administrador:** Acesso completo ao sistema

### 4.4 Tratamento do Processamento Assíncrono

**Fluxo:**

```
1. Reino envia requisição para criar Expedição
2. API valida dados básicos e cria registro (status: PENDING)
3. Sistema gera protocolo único
4. API retorna imediatamente o protocolo ao Reino
5. Job é enviado para a fila (Queue)
6. Worker processa validações e regras internas
7. Conselho analisa e decide (APPROVED ou REJECTED)
8. Sistema registra decisão, auditoria e notificações
```

**Tratamento de Falhas:**
- Notificação imediata para equipe técnica
- Interface administrativa para reprocessamento manual

**Monitoramento:**
- Métricas: Jobs em processamento, taxa de sucesso/falha, tempo médio
- Alertas: Fila > 100 jobs, taxa de falha > 5%, tempo acima do esperado


**Códigos de Status HTTP:**
- `200` - Sucesso
- `201` - Criado (Expedição registrada)
- `400` - Bad Request (Dados inválidos)
- `401` - Unauthorized (Token inválido/expirado)
- `403` - Forbidden (Sem permissão)
- `404` - Not Found (Expedição não encontrada)
- `422` - Unprocessable Entity (Validação falhou)
- `429` - Too Many Requests (Rate limit excedido)
- `500` - Internal Server Error
- `503` - Service Unavailable


**Categorias de Erros:**

1. **Validação (400/422):** Dados obrigatórios ausentes, formato inválido, regras violadas
2. **Autenticação (401):** Token ausente/inválido/expirado, credenciais incorretas
3. **Autorização (403):** Tentativa de acessar expedição de outro reino, operação não permitida

**Rejeição de Expedição (exemplo):**
```json
{
	"message": "Os dados enviados são inválidos.",
	"errors": {
		"kingdom_id": [
			"The selected kingdom id is invalid."
		]
	}
}
```

**Categorias de Rejeição:**
- `KINGDOM_ID_REQUIRED`: kingdom_id não foi enviado (422)
- `KINGDOM_ID_INVALID_TYPE`: kingdom_id não é inteiro (422)
- `KINGDOM_NOT_FOUND`: kingdom_id não existe na tabela kingdoms (422)
- `JOURNEY_DESCRIPTION_REQUIRED`: journey_description não foi enviado (422)
- `JOURNEY_DESCRIPTION_INVALID_TYPE`: journey_description não é texto (422)
- `PARTICIPANTS_REQUIRED`: participants não foi enviado (422)
- `PARTICIPANTS_INVALID_TYPE`: participants não é array (422)
- `PARTICIPANTS_MIN_ITEMS`: participants enviado vazio (mínimo 1) (422)
- `PARTICIPANT_NAME_REQUIRED`: algum item em participants sem name (422)
- `PARTICIPANT_NAME_INVALID_TYPE`: algum participants.*.name não é texto (422)
- `PARTICIPANT_RACE_REQUIRED`: algum item em participants sem race (422)
- `PARTICIPANT_RACE_INVALID_TYPE`: algum participants.*.race não é texto (422)
- `ARTIFACTS_REQUIRED`: artifacts não foi enviado (422)
- `ARTIFACTS_INVALID_TYPE`: artifacts não é array (422)
- `ARTIFACTS_MIN_ITEMS`: artifacts enviado vazio (mínimo 1) (422)
- `ARTIFACT_NAME_REQUIRED`: algum item em artifacts sem name (422)
- `ARTIFACT_NAME_INVALID_TYPE`: algum artifacts.*.name não é texto (422)
- `ARTIFACT_TYPE_REQUIRED`: algum item em artifacts sem type (422)
- `ARTIFACT_TYPE_INVALID_TYPE`: algum artifacts.*.type não é texto (422)
- `UNEXPECTED_INTERNAL_ERROR`: erro interno inesperado durante transação/salvamento (500)

**Logging e Auditoria:**
- Logs estruturados com timestamp, nível, service, request_id, protocol, realm, erro e contexto
- Eventos auditados: criação, mudanças de status, aprovações/rejeições, acessos sensíveis, tentativas de autenticação, erros críticos

**Recuperação:**
- Endpoint `POST /api/expeditions/{protocol}/retry` permite reinos corrigirem dados e solicitarem reprocessamento

---

## 5. Considerações Finais

A implementação desta API REST prioriza:

- **Segurança:** Múltiplas camadas de proteção e autenticação robusta
- **Confiabilidade:** Processamento assíncrono com tratamento robusto de falhas
- **Rastreabilidade:** Histórico completo e auditoria de todas as operações
- **Usabilidade:** Endpoints claros e documentação completa
- **Escalabilidade:** Arquitetura limpa e preparada para crescimento futuro

**Recomendação de Implementação em Fases:**

- **Fase 1 (MVP):** Autenticação, registro e consulta básica de expedições
- **Fase 2:** Processamento assíncrono e notificações
- **Fase 3:** Webhooks e integrações avançadas
- **Fase 4 (final):** Dashboard administrativo e relatórios

Estamos à disposição para esclarecimentos adicionais e para iniciar a implementação assim que aprovada pelo Conselho.

**Que esta solução fortaleça os laços entre os povos livres da Terra-média.**

---

*Documento preparado pela Equipe Técnica do Conselho de Elrond*
