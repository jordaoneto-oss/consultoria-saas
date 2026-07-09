# PLATAFORMA DE CONSULTORIA SAAS — DOCUMENTAÇÃO COMPLETA

> **Versão:** 1.0.0
> **Última atualização:** Julho 2026
> **Stack:** WordPress + WooCommerce + PHP 8.0+ + MySQL 8+ + Redis + Cloudflare + Stripe Connect

---

## SUMÁRIO

1. [Product Vision](#1-product-vision)
2. [Lean Canvas](#2-lean-canvas)
3. [Business Model Canvas](#3-business-model-canvas)
4. [Arquitetura da Solução](#4-arquitetura-da-solução)
5. [Modelo Entidade-Relacionamento](#5-modelo-entidade-relacionamento)
6. [SQL Completo](#6-sql-completo)
7. [Wireframes — UX/UI](#7-wireframes--uxui)
8. [Design System](#8-design-system)
9. [Backlog e User Stories](#9-backlog-e-user-stories)
10. [Diagramas UML](#10-diagramas-uml)
11. [API REST — Documentação Completa](#11-api-rest--documentação-completa)
12. [Plano de Testes](#12-plano-de-testes)
13. [Plano de DevOps e Infraestrutura](#13-plano-de-devops-e-infraestrutura)
14. [Guia de Evolução para Microserviços](#14-guia-de-evolução-para-microserviços)

---

## 1. PRODUCT VISION

### Título
**Consultoria SaaS Platform** — Marketplace de Consultoria de Negócios e Tecnologia

### Visão
Ser a maior plataforma da América Latina conectando empresas a consultores especializados em negócios e tecnologia, com experiência completa de contratação, execução, pagamento e gestão 100% digital.

### Público-Alvo
- **Clientes:** PMEs, startups, grandes empresas que precisam de consultoria pontual ou recorrente
- **Consultores:** Profissionais autônomos, especialistas, ex-consultores de grandes firmas
- **Afiliados:** Influenciadores, escolas, associações que indicam a plataforma

### Proposta de Valor
- **Para Clientes:** Acesso rápido a especialistas verificados, contratação por horas, gestão unificada
- **Para Consultores:** Autonomia, precificação própria, ferramentas de gestão, pagamento garantido
- **Para Afiliados:** Comissão recorrente sobre indicações

### Diferenciais Competitivos
1. **Matching Inteligente com IA** — Algoritmo proprietário que recomenda o melhor consultor
2. **Pagamento Garantido via Stripe Connect** — Split automático, retenção em escrow
3. **Experiência Premium** — UX inspirada em Stripe, Linear, Notion
4. **Gamificação e Selos** — Engajamento de consultores via ranking e reconhecimento
5. **Programa de Afiliados** — Canal de aquisição escalável com comissões recorrentes

### Métricas de Sucesso (OKRs)
- **Q1:** 1.000 clientes cadastrados, 200 consultores ativos, GMV de R$ 1M
- **Q2:** 5.000 clientes, 1.000 consultores, GMV de R$ 5M, NPS > 80
- **Q3:** 20.000 clientes, 3.000 consultores, GMV de R$ 20M
- **Q4:** 50.000 clientes, 5.000 consultores, GMV de R$ 50M

---

## 2. LEAN CANVAS

| Seção | Descrição |
|-------|-----------|
| **Problema** | 1. Difícil encontrar consultores especializados sob demanda 2. Processo de contratação lento e burocrático 3. Gestão de horas, entregas e pagamentos fragmentada 4. Ausência de garantia de pagamento para consultores |
| **Segmento de Clientes** | 1. Empresas (PMEs a Corporações) 2. Consultores autônomos e especialistas 3. Afiliados e parceiros |
| **Proposta de Valor Única** | Marketplace completo com matching por IA, pagamento garantido, gestão integrada e experiência premium |
| **Solução** | 1. Marketplace com pacotes de horas 2. Stripe Connect com split automático 3. Dashboard integrado 4. IA de matching 5. Gamificação 6. Programa de afiliados |
| **Canais** | 1. SEO 2. Google Ads 3. LinkedIn Ads 4. Programa de afiliados 5. Parcerias institucionais 6. Marketing de conteúdo |
| **Fontes de Receita** | 1. Comissão sobre cada hora contratada (15-25%) 2. Taxa de saque 3. Planos premium para consultores 4. Taxa de afiliados |
| **Estrutura de Custo** | 1. Stripe Connect (2.9% + $0.30) 2. Infraestrutura (Cloudflare, Redis, Servidores) 3. Desenvolvimento 4. Marketing 5. Suporte |
| **Métricas Chave** | GMV, Receita Líquida, CAC, LTV, Churn, NPS, Consultores Ativos, Clientes Ativos, Tempo médio de matching |
| **Vantagem Competitiva** | Algoritmo proprietário de matching, experiência integrada, ecossistema gamificado |

---

## 3. BUSINESS MODEL CANVAS

### 1. Segmentos de Clientes
- Empresas contratantes de consultoria (B2B)
- Consultores especializados (B2C / Profissionais)
- Afiliados e parceiros de indicação

### 2. Proposta de Valor
- **Clientes:** Marketplace confiável, consultores verificados, pagamento seguro, gestão integrada
- **Consultores:** Autonomia, ferramentas profissionais, receita recorrente, pagamento garantido
- **Afiliados:** Comissão recorrente sem investimento inicial

### 3. Canais
- Plataforma web (WordPress + Plugin customizado)
- Aplicativo mobile (Flutter — Fase 2)
- API pública para integrações

### 4. Relacionamento com Clientes
- Onboarding guiado
- Suporte via chat e tickets
- Comunidade de consultores
- Programas de fidelidade (cashback, gamificação)

### 5. Fontes de Receita
| Fonte | Descrição | Margem |
|-------|-----------|--------|
| Comissão Marketplace | 15-25% sobre valor das horas | 70-80% |
| Taxa de Saque | R$ 5,00 por saque | 100% |
| Planos Premium Consultor | R$ 49-199/mês | 90% |
| Taxa de Afiliados | 5-10% sobre indicações | 50% |
| Consultoria Premium | 30% sobre projetos enterprise | 80% |

### 6. Recursos Principais
- Plataforma tecnológica (WordPress + Plugin próprio)
- Algoritmo de matching com IA
- Base de consultores qualificados
- Reputação e trust signals
- Equipe de suporte e operações

### 7. Atividades-Chave
- Desenvolvimento e manutenção da plataforma
- Curadoria e onboarding de consultores
- Marketing e aquisição de clientes
- Suporte e mediação de conflitos
- Melhoria contínua do algoritmo de matching

### 8. Parcerias Principais
- Stripe (processamento de pagamentos)
- Cloudflare (CDN e segurança)
- Daily.co / Zoom (videoconferência)
- Docuseal / OpenSign (assinatura digital)
- Google / Microsoft (calendar sync)

### 9. Estrutura de Custos
| Categoria | % Receita |
|-----------|-----------|
| Processamento (Stripe) | 2.9% + $0.30 |
| Infraestrutura | 5-8% |
| Pessoal | 20-30% |
| Marketing | 15-25% |
| Suporte | 5-10% |
| Administrativo | 5-8% |

---

## 4. ARQUITETURA DA SOLUÇÃO

### 1. Visão Geral

```
┌─────────────────────────────────────────────────────────────────────┐
│                        CLIENTES (Browser/Mobile)                    │
├─────────────────────────────────────────────────────────────────────┤
│                           Cloudflare (CDN + WAF)                    │
├─────────────────────────────────────────────────────────────────────┤
│                         Load Balancer (HAProxy/Nginx)               │
├─────────────────────────────────────────────────────────────────────┤
│                        WordPress (Apresentação)                     │
│   ┌─────────────────────────────────────────────────────────────┐   │
│   │                    TEMA CONSULTORIA                          │   │
│   │  (Elementor Pro + Templates Customizados)                   │   │
│   ├─────────────────────────────────────────────────────────────┤   │
│   │              PLUGIN CONSULTORIA PLATFORM                     │   │
│   │  ┌──────────┐ ┌─────────┐ ┌──────────┐ ┌──────────────┐   │   │
│   │  │Rest API  │ │Modules  │ │Services  │ │Integrations  │   │   │
│   │  │(JWT Auth)│ │(13 mod) │ │(24 serv) │ │(Stripe, etc) │   │   │
│   │  └──────────┘ └─────────┘ └──────────┘ └──────────────┘   │   │
│   ├─────────────────────────────────────────────────────────────┤   │
│   │                  WooCommerce (Catálogo + Checkout)           │   │
│   └─────────────────────────────────────────────────────────────┘   │
├─────────────────────────────────────────────────────────────────────┤
│                             Redis (Cache)                           │
├─────────────────────────────────────────────────────────────────────┤
│                             MySQL (MariaDB)                         │
├─────────────────────────────────────────────────────────────────────┤
│                    Stripe Connect (Pagamentos)                      │
└─────────────────────────────────────────────────────────────────────┘
```

### 2. Decisões Arquiteturais

#### 2.1 WordPress como Framework Core
**Decisão:** Utilizar WordPress como base do MVP
**Justificativa:**
- Curva de aprendizado reduzida para o time
- Ecossistema maduro (WooCommerce, plugins)
- Baixo custo operacional inicial
- PHP 8+ oferece performance adequada
- Possibilidade de evolução progressiva

**Risco:** WordPress não é ideal para lógica de negócio complexa
**Mitigação:** Plugin próprio desacoplado, Clean Architecture, preparação para migração

#### 2.2 WooCommerce apenas para Catálogo e Checkout
**Decisão:** WooCommerce gerencia apenas produtos (pacotes de horas) e checkout
**Justificativa:**
- WooCommerce já possui gateway Stripe, subscriptions, gerenciamento de pedidos
- Evita retrabalho com carrinho e fluxo de pagamento
- Toda regra de negócio fica no plugin próprio

#### 2.3 Plugin Próprio com Clean Architecture
**Decisão:** Todo o domínio da plataforma em plugin separado
**Justificativa:**
- Desacoplamento total do WooCommerce e do tema
- Possibilidade de testar isoladamente
- Preparação para extração futura para microserviços

#### 2.4 Stripe Connect
**Decisão:** Stripe Connect como única plataforma de pagamento
**Justificativa:**
- Split automático de pagamentos (plataforma + consultor)
- Onboarding de consultores via Stripe Express
- Escrow (retenção temporária) até conclusão do serviço
- Suporte a múltiplos países e moedas

#### 2.5 API REST como Camada de Comunicação
**Decisão:** Toda comunicação frontend-backend via REST API
**Justificativa:**
- Preparação para frontend React/Vue/Flutter no futuro
- Independência entre camadas
- Versionamento de API

### 3. Módulos do Plugin

| Módulo | Responsabilidade | Prioridade |
|--------|-----------------|------------|
| Marketplace | Publicação de demandas, propostas, seleção | MVP |
| Wallet | Carteira digital, saldo, transações | MVP |
| Scheduling | Agenda, disponibilidade, sync calendário | MVP |
| Chat | Comunicação em tempo real | MVP |
| Videoconference | Integração Daily.co/Zoom | MVP |
| Contracts | Geração e assinatura digital | MVP |
| Dashboard | Métricas e relatórios | MVP |
| SLA | Controle de prazos e escalonamento | MVP |
| Matching IA | Algoritmo de recomendação | Fase 2 |
| Gamification | Selos, ranking, níveis | Fase 2 |
| Cashback | Créditos e cashback | Fase 2 |
| Affiliates | Programa de afiliados | Fase 2 |
| Notifications | Email, push, WhatsApp | MVP |

### 4. Padrões de Arquitetura

#### 4.1 Clean Architecture no Plugin

```
┌─────────────────────────────────────────────────────────────┐
│                    Controllers (HTTP)                        │
│   Recebe requisições, valida input, retorna response        │
├─────────────────────────────────────────────────────────────┤
│                    Services (Use Cases)                      │
│   Lógica de negócio, orquestração de domínio                │
├─────────────────────────────────────────────────────────────┤
│                    Repositories (Data)                       │
│   Acesso a dados, abstração do WordPress DB                 │
├─────────────────────────────────────────────────────────────┤
│                    Adapters (External)                       │
│   Stripe, Daily.co, Google Calendar, etc.                   │
└─────────────────────────────────────────────────────────────┘
```

#### 4.2 Repository Pattern
- Abstração completa do banco de dados
- Interfaces para cada repositório
- Implementação concreta usando $wpdb

#### 4.3 Service Layer
- Serviços anêmicos (sem estado)
- Injeção de dependência via construtor
- Responsabilidade única

#### 4.4 Event Driven
- Eventos internos para desacoplamento entre módulos
- Ex: `ContractSignedEvent` → dispara notificações, libera agenda, inicia SLA
- Webhooks para eventos externos (Stripe, etc)

### 5. Segurança

#### 5.1 Autenticação
- JWT para API REST
- Refresh tokens
- OAuth2 para integrações externas
- 2FA opcional para consultores

#### 5.2 Autorização
- Role-Based Access Control (RBAC)
- 4 perfis: admin, support, consultant, client
- Capabilities customizadas do WordPress

#### 5.3 Proteções
- Rate limiting por IP e por usuário
- Input validation e sanitization
- Prepared statements em todas as queries
- Nonce para forms
- CORS configurado
- Headers de segurança (HSTS, CSP, X-Frame-Options)

### 6. Cache e Performance

#### 6.1 Redis
- Cache de sessão
- Cache de queries frequentes
- Cache de objetos serializados
- Rate limiting counters
- Filas de processamento assíncrono

#### 6.2 Cloudflare
- CDN para assets estáticos
- WAF (Web Application Firewall)
- Minificação automática
- HTTP/2 e HTTP/3
- Argo Smart Routing

### 7. Banco de Dados

#### 7.1 Estratégia
- MySQL 8+ / MariaDB 10.6+
- Tabelas customizadas no plugin (prefixo `cp_`)
- WooCommerce mantém tabelas próprias (`wp_posts`, `wp_postmeta`, `wp_woocommerce_*`)
- Índices estratégicos para busca e filtros

### 8. Observabilidade

#### 8.1 Logs
- Structured logging (JSON)
- Níveis: debug, info, warning, error, critical
- Logs centralizados via arquivos rotacionados

#### 8.2 Monitoramento (Fase 2+)
- New Relic ou Datadog
- APM para WordPress
- RUM (Real User Monitoring)

### 9. Estratégia de Evolução

#### Fase 1 — Monólito Modular (MVP)
WordPress + Plugin próprio + WooCommerce

#### Fase 2 — Aplicativo Mobile
Flutter + Consumo da REST API

#### Fase 3 — Extração de Serviços
- Módulo de Matching → Serviço Python/Node.js
- Chat → Serviço Node.js + WebSocket
- Notificações → Serviço Node.js

#### Fase 4 — Microserviços
- WordPress apenas como CMS
- API Gateway (Kong/KrakenD)
- Serviços independentes em Docker/Kubernetes
- Mensageria com RabbitMQ/Kafka
- Deploy em AWS/Azure/GCP

---

## 5. MODELO ENTIDADE-RELACIONAMENTO (MER)

### Diagrama Conceitual

```
┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│      USERS        │       │   CONSULTANTS       │       │    CLIENTS       │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │──1:1──│ id (PK)             │       │ id (PK)          │
│ email             │       │ user_id (FK)        │       │ user_id (FK)     │
│ password_hash     │       │ status              │       │ company_name     │
│ display_name      │       │ rating              │       │ cnpj             │
│ role (enum)       │       │ level               │       │ phone            │
│ status            │       │ total_hours         │       │ billing_address  │
│ avatar            │       │ total_revenue       │       │                  │
│ created_at        │       │ created_at          │       │                  │
│ updated_at        │       └────────┬───────────┘       └──────────────────┘
│ last_login        │                │
│ 2fa_enabled       │                │ 1:N
│ 2fa_secret        │                │
└───────────────────┘                │
                                     │
         1:N ┌────────────────────────┴───────────────┐
     ┌───────┴──────────┐              ┌──────────────┴──────────┐
     │   EXPERTISE       │              │   CERTIFICATIONS        │
     │───────────────────│              │─────────────────────────│
     │ id (PK)           │              │ id (PK)                 │
     │ consultant_id(FK) │              │ consultant_id (FK)      │
     │ category          │              │ title                   │
     │ subcategory       │              │ issuer                  │
     │ experience_years  │              │ credential_url          │
     │ description       │              │ issued_at               │
     │ rate_per_hour     │              │ expires_at              │
     └───────────────────┘              └─────────────────────────┘

┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│   SERVICE_PLANS   │       │     PROPOSALS      │       │   PROJECTS       │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │──1:N──│ id (PK)            │──1:N──│ id (PK)          │
│ name              │       │ project_id (FK)    │       │ proposal_id (FK) │
│ slug              │       │ consultant_id (FK) │       │ client_id (FK)   │
│ description       │       │ value              │       │ consultant_id(FK)│
│ hours             │       │ estimated_hours    │       │ status           │
│ price             │       │ message            │       │ total_hours      │
│ validity_days     │       │ status             │       │ used_hours       │
│ status            │       │ created_at         │       │ start_date       │
│ sort_order        │       │ updated_at         │       │ end_date         │
└───────────────────┘       └────────────────────┘       │ contract_id(FK)  │
                                                          │ sla_id (FK)      │
                                                          │ created_at       │
                                                          │ updated_at       │
                                                          └──────────────────┘

┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│  TIME_ENTRIES      │       │   MILESTONES        │       │   DELIVERABLES   │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │       │ id (PK)            │       │ id (PK)          │
│ project_id (FK)   │       │ project_id (FK)    │       │ milestone_id(FK) │
│ consultant_id(FK) │       │ title              │       │ title            │
│ date              │       │ description        │       │ description      │
│ hours             │       │ due_date           │       │ file_url         │
│ description       │       │ status             │       │ status           │
│ status            │       │ created_at         │       │ approved_at      │
│ approved_by(FK)   │       │ updated_at         │       │ feedback         │
│ approved_at       │       └────────────────────┘       │ created_at       │
│ created_at        │                                     └──────────────────┘
└───────────────────┘

┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│  MESSAGES          │       │  APPOINTMENTS       │       │  REVIEWS          │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │       │ id (PK)            │       │ id (PK)          │
│ project_id (FK)   │       │ project_id (FK)    │       │ project_id (FK)  │
│ sender_id (FK)    │       │ consultant_id(FK)  │       │ reviewer_id (FK) │
│ content            │       │ client_id (FK)     │       │ rating           │
│ message_type       │       │ start_time         │       │ comment          │
│ file_url           │       │ end_time           │       │ criteria_1       │
│ read_at            │       │ type               │       │ criteria_2       │
│ created_at         │       │ status             │       │ criteria_3       │
└───────────────────┘       │ meeting_url        │       │ created_at       │
                             │ created_at         │       └──────────────────┘
                             │ updated_at         │
                             └────────────────────┘

┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│   WALLET            │       │  TRANSACTIONS       │       │  WITHDRAWALS      │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │       │ id (PK)            │       │ id (PK)          │
│ user_id (FK)      │──1:N──│ wallet_id (FK)     │       │ wallet_id (FK)   │
│ balance            │       │ type               │──1:N──│ amount            │
│ blocked_balance    │       │ amount             │       │ status            │
│ total_earned       │       │ fee_platform       │       │ fee               │
│ total_withdrawn    │       │ fee_stripe         │       │ pix_key           │
│ created_at         │       │ reference_type     │       │ bank_info         │
│ updated_at         │       │ reference_id       │       │ requested_at      │
└───────────────────┘       │ description        │       │ approved_at       │
                             │ status             │       │ approved_by(FK)   │
                             │ created_at         │       │ paid_at           │
                             └────────────────────┘       └──────────────────┘

┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│  SLA_RULES         │       │   SLA_MONITOR       │       │  TICKETS          │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │       │ id (PK)            │       │ id (PK)          │
│ category           │       │ project_id (FK)    │       │ user_id (FK)     │
│ response_time_h    │       │ rule_id (FK)       │       │ assigned_to(FK)  │
│ accept_time_h      │       │ status             │       │ subject           │
│ delivery_time_h    │       │ responded_at       │       │ description       │
│ review_time_h      │       │ accepted_at        │       │ priority          │
│ close_time_h       │       │ delivered_at       │       │ status            │
│ auto_escalation    │       │ reviewed_at        │       │ category          │
│ escalation_to      │       │ closed_at          │       │ created_at        │
│ penalty_percentage │       │ escalated          │       │ updated_at        │
│ created_at         │       │ escalation_at      │       │ closed_at         │
│ updated_at         │       │ created_at         │       └──────────────────┘
└───────────────────┘       └────────────────────┘

┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│  GAMIFICATION      │       │  ACHIEVEMENTS       │       │  USER_BADGES      │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │       │ id (PK)            │       │ id (PK)          │
│ user_id (FK)      │       │ name               │       │ user_id (FK)     │
│ level              │       │ description        │       │ badge_id (FK)    │
│ xp                 │       │ icon_url           │       │ earned_at         │
│ xp_next_level      │       │ type               │  N:N  └──────────────────┘
│ total_xp_earned    │       │ criteria           │
│ ranking_position   │       │ criteria_value     │
│ created_at         │       │ created_at         │
│ updated_at         │       └────────────────────┘
└───────────────────┘

┌───────────────────┐       ┌────────────────────┐       ┌──────────────────┐
│  AFFILIATES        │       │  AFFILIATE_CLICKS   │       │ CASHBACK_RULES    │
│───────────────────│       │────────────────────│       │──────────────────│
│ id (PK)           │       │ id (PK)            │       │ id (PK)          │
│ user_id (FK)      │──1:N──│ affiliate_id (FK)  │       │ name              │
│ code               │       │ ip_address         │       │ percentage        │
│ commission_rate    │       │ user_agent         │       │ min_order_value   │
│ total_clicks       │       │ converted          │       │ max_cashback      │
│ total_conversions  │       │ converted_at       │       │ valid_from        │
│ total_revenue      │       │ created_at         │       │ valid_until       │
│ total_commission   │       └────────────────────┘       │ status            │
│ status             │                                     │ created_at        │
│ created_at         │                                     └──────────────────┘
│ updated_at         │
└───────────────────┘         ┌────────────────────┐
                              │  NOTIFICATIONS      │
                              │────────────────────│
                              │ id (PK)            │
                              │ user_id (FK)       │
                              │ type               │
                              │ title              │
                              │ message            │
                              │ reference_type     │
                              │ reference_id       │
                              │ read_at            │
                              │ sent_via           │
                              │ created_at         │
                              └────────────────────┘
```

### Convenções

- **Prefixo das tabelas:** `cp_` (consultoria platform)
- **Chaves primárias:** `id` (BIGINT UNSIGNED, AUTO_INCREMENT)
- **Chaves estrangeiras:** `{tabela}_id` (BIGINT UNSIGNED)
- **Timestamps:** `created_at`, `updated_at` (DATETIME)
- **Status:** ENUM ou VARCHAR(20) com CHECK constraint
- **Engine:** InnoDB (transações, FK, row-level locking)
- **Charset:** utf8mb4
- **Collation:** utf8mb4_unicode_ci

### Relacionamentos Principais

| Origem | Destino | Tipo | Regra |
|--------|---------|------|-------|
| users | consultants | 1:1 | Um usuário pode ser consultor |
| users | clients | 1:1 | Um usuário pode ser cliente |
| consultants | expertise | 1:N | Um consultor tem N especialidades |
| consultants | certifications | 1:N | Um consultor tem N certificações |
| service_plans | orders | 1:N | Um plano gera N pedidos |
| orders | projects | 1:1 | Um pedido gera um projeto |
| projects | proposals | 1:N | Um projeto tem N propostas |
| projects | contracts | 1:1 | Um projeto tem um contrato |
| projects | time_entries | 1:N | Um projeto tem N lançamentos |
| projects | milestones | 1:N | Um projeto tem N marcos |
| milestones | deliverables | 1:N | Um marco tem N entregas |
| projects | messages | 1:N | Um projeto tem N mensagens |
| projects | appointments | 1:N | Um projeto tem N agendamentos |
| projects | reviews | 1:N | Um projeto tem N avaliações |
| users | wallet | 1:1 | Um usuário tem uma carteira |
| wallet | transactions | 1:N | Uma carteira tem N transações |
| wallet | withdrawals | 1:N | Uma carteira tem N saques |
| users | gamification | 1:1 | Um usuário tem gamificação |
| achievements | user_badges | 1:N | Um achievement tem N badges |
| users | user_badges | 1:N | Um usuário tem N badges |
| users | affiliates | 1:1 | Um usuário pode ser afiliado |
| affiliates | affiliate_clicks | 1:N | Um afiliado tem N cliques |
| users | notifications | 1:N | Um usuário tem N notificações |
| projects | sla_monitor | 1:N | Um projeto tem N monitoramentos SLA |
| sla_rules | sla_monitor | 1:N | Uma regra SLA tem N monitoramentos |
| users | tickets | 1:N | Um usuário tem N tickets |

---

## 6. SQL COMPLETO

O arquivo completo com todas as 37 tabelas, índices, FKs e dados iniciais está em `database/SQL_COMPLETE.sql`.

### Tabelas Criadas

| # | Tabela | Descrição |
|---|--------|-----------|
| 1 | `cp_users` | Usuários estendidos (extends wp_users) |
| 2 | `cp_clients` | Clientes (pessoa jurídica) |
| 3 | `cp_consultants` | Consultores |
| 4 | `cp_expertise` | Especialidades dos consultores |
| 5 | `cp_certifications` | Certificações |
| 6 | `cp_portfolio_items` | Portfólio |
| 7 | `cp_service_plans` | Planos de serviço (pacotes de horas) |
| 8 | `cp_orders` | Pedidos (integração WooCommerce) |
| 9 | `cp_projects` | Projetos (demandas) |
| 10 | `cp_proposals` | Propostas de consultores |
| 11 | `cp_contracts` | Contratos |
| 12 | `cp_milestones` | Marcos do projeto |
| 13 | `cp_deliverables` | Entregas |
| 14 | `cp_time_entries` | Lançamentos de horas |
| 15 | `cp_messages` | Mensagens (chat) |
| 16 | `cp_appointments` | Agendamentos |
| 17 | `cp_reviews` | Avaliações |
| 18 | `cp_wallets` | Carteira digital |
| 19 | `cp_transactions` | Transações financeiras |
| 20 | `cp_withdrawals` | Saques |
| 21 | `cp_sla_rules` | Regras SLA |
| 22 | `cp_sla_monitor` | Monitoramento SLA |
| 23 | `cp_tickets` | Tickets de suporte |
| 24 | `cp_ticket_replies` | Respostas de tickets |
| 25 | `cp_gamification` | Gamificação |
| 26 | `cp_achievements` | Badges / conquistas |
| 27 | `cp_user_badges` | Badges dos usuários |
| 28 | `cp_affiliates` | Afiliados |
| 29 | `cp_affiliate_clicks` | Cliques de afiliados |
| 30 | `cp_cashback_rules` | Regras de cashback |
| 31 | `cp_notifications` | Notificações |
| 32 | `cp_consultant_availability` | Disponibilidade dos consultores |
| 33 | `cp_availability_blocks` | Feriados / bloqueios na agenda |
| 34 | `cp_audit_logs` | Logs de auditoria |
| 35 | `cp_matching_scores` | Métricas de matching IA |
| 36 | `cp_video_sessions` | Sessões de videoconferência |
| 37 | `cp_settings` | Configurações da plataforma |

### Dados Iniciais

**Planos de Serviço:**

| Nome | Horas | Preço | Validade | Destaque |
|------|-------|-------|----------|----------|
| Bronze | 10 | R$ 1.500 | 90 dias | Não |
| Prata | 20 | R$ 2.800 | 180 dias | Não |
| Ouro | 50 | R$ 6.500 | 270 dias | Sim |
| Enterprise | 100 | R$ 12.000 | 365 dias | Não |

**Achievements (8 badges):** Top Avaliado, Especialista Verificado, Resposta Rápida, 100 Projetos, 500 Horas, Mestre do Marketplace, Primeiro Projeto, Cinco Estrelas.

**Configurações Iniciais (18):** platform_name, commission_rate (20%), withdrawal_fee (R$5), min/max withdrawal, default SLA, cashback (5%), affiliate commission (10%), matching settings, Stripe fees, support email.

---

## 7. WIREFRAMES — UX/UI

### 1. Landing Page

```
┌─────────────────────────────────────────────────────────────────────┐
│ [LOGO]  [Serviços] [Consultores] [Planos] [Entrar] [Cadastrar]     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌───────────────────────────────────────────────────────────────┐  │
│  │  ★ CONSULTORIA SAAS ★                                       │  │
│  │                                                              │  │
│  │  A consultoria especializada que sua empresa merece          │  │
│  │  Contrate os melhores consultores por hora, sem burocracia   │  │
│  │                                                              │  │
│  │  [🔍 Buscar consultores...]          [📋 Publicar Demanda]   │  │
│  └───────────────────────────────────────────────────────────────┘  │
│                                                                     │
│  ┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐                                │
│  │ 10h  │ │ 20h  │ │ 50h  │ │100h  │                                │
│  │Bronze│ │Prata │ │ Ouro │ │Enter.│                                │
│  │R$1500│ │R$2800│ │R$6500│ │R$12k │                                │
│  └──────┘ └──────┘ └──────┘ └──────┘                                │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════     │
│  "Contratamos um consultor SAP em 24h. Nota 10!" — Cliente         │
│  ═══════════════════════════════════════════════════════════════     │
│                                                                     │
│  [Especialidades] [Como Funciona] [FAQ] [Contato]                   │
│                                                                     │
│  © 2026 Consultoria SaaS                                           │
└─────────────────────────────────────────────────────────────────────┘
```

### 2. Marketplace (Busca de Consultores)

```
┌─────────────────────────────────────────────────────────────────────┐
│ [LOGO]  🔍 Buscar...  [📋 Publicar]  [👤 Perfil]  [💰 Carteira]   │
├────────────────────┬────────────────────────────────────────────────┤
│                    │                                                │
│  FILTROS           │  RESULTADOS (12 encontrados)                   │
│  ─────────         │  ┌──────────────────────────────────────────┐  │
│  Categoria         │  │ [Foto] João Silva                       │  │
│  ● Negócios        │  │ ★★★★★ 4.9 (42 avaliações)              │  │
│  ○ Tecnologia      │  │ Especialista SAP | 12 anos de XP        │  │
│  ○ Agilidade       │  │ R$ 180/h | 95% conclusão               │  │
│  ○ ERP             │  │ 🏆 Top Avaliado | Resposta Rápida       │  │
│                    │  │ [Ver Perfil] [Contratar]                 │  │
│  Preço/Hora        │  └──────────────────────────────────────────┘  │
│  [———●—————] R$180 │  ┌──────────────────────────────────────────┐  │
│                    │  │ [Foto] Maria Santos                      │  │
│  Disponibilidade   │  │ ★★★★☆ 4.7 (38 avaliações)              │  │
│  ○ imediato        │  │ Power BI | Data Analytics | 8 anos      │  │
│  ● esta semana     │  │ R$ 150/h | 98% conclusão               │  │
│  ○ este mês        │  │ 🏆 500 Horas | Especialista Verificado  │  │
│                    │  │ [Ver Perfil] [Contratar]                 │  │
│  Avaliação         │  └──────────────────────────────────────────┘  │
│  [★ ★ ★ ★ ★]      │                                                │
│                    │  [1] [2] [3] ... [12]                         │
│  Ordenar           │                                                │
│  [Melhor Avaliado ▼]│                                                │
│                    │                                                │
└────────────────────┴────────────────────────────────────────────────┘
```

### 3. Perfil do Consultor

```
┌─────────────────────────────────────────────────────────────────────┐
│ [Voltar]                                              [Contratar]   │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  [Foto]  João Silva  ★★★★★ 4.9  (42 avaliações)                   │
│  Especialista SAP Sênior                                           │
│  São Paulo, SP  ●  Disponível esta semana                          │
│                                                                     │
│  ┌──────┬──────┬──────┬──────┬──────┬──────┐                        │
│  │ 12   │  98  │ 4.9  │ 2h   │ 8h   │ 42  │                        │
│  │ Anos │  %   │ Rating│Resp. │Entre.│Aval. │                        │
│  │  XP  │Concl.│       │Média │Média │      │                        │
│  └──────┴──────┴──────┴──────┴──────┴──────┘                        │
│                                                                     │
│  ═══════════════════════════════════════════════════════════════     │
│  SOBRE                                                            │
│  Consultor SAP com 12 anos de experiência em implementação...     │
│  ═══════════════════════════════════════════════════════════════     │
│                                                                     │
│  ESPECIALIDADES                                                     │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ SAP S/4HANA  ●  SAP FI/CO  ●  SAP MM  ●  SAP SD            │   │
│  │ SAP BTP  ●  SAP Fiori  ●  ABAP  ●  SAP Cloud               │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  CERTIFICAÇÕES                                                      │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ ✅ SAP Certified Application Associate - S/4HANA (2023)     │   │
│  │ ✅ SAP Certified Technology Professional (2024)              │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  PORTFÓLIO                                                          │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐                           │
│  │Projeto A │ │Projeto B │ │Projeto C │                           │
│  │Indústria │ │Varejo    │ │Finanças  │                           │
│  │200h      │ │350h      │ │150h      │                           │
│  └──────────┘ └──────────┘ └──────────┘                           │
│                                                                     │
│  AVALIAÇÕES                                                         │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ ★★★★★ "Excelente profissional, entregou antes do prazo."    │   │
│  │ ★★★★★ "Conhecimento profundo de SAP, recomendo."            │   │
│  │ ★★★★☆ "Boa comunicação, mas poderia ter mais disponibilidade"│   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

### 4. Dashboard Cliente

```
┌─────────────────────────────────────────────────────────────────────┐
│ [LOGO]  [📋 Projetos] [👨‍🔧 Consultores] [📅 Agenda] [💰 Finanças]│
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Olá, Empresa XYZ!  Saldo: 42h restantes | ⚡ 3 notificações       │
│                                                                     │
│  ┌──────────┬──────────┬──────────┬──────────┐                     │
│  │  42h     │  3       │ R$ 8.400 │ R$ 420   │                     │
│  │Restantes │ Projetos │Investido │Cashback  │                     │
│  └──────────┴──────────┴──────────┴──────────┘                     │
│                                                                     │
│  MEUS PROJETOS                                                      │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ ● Implantação SAP    João Silva   85%  ████████░  [Acessar]  │   │
│  │ ● Dashboard Power BI Maria Santos 30%  ███░░░░░░  [Acessar]  │   │
│  │ ● Consultoria LGPD   Carlos Lima  Pendente    [Acompanhar]  │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  PRÓXIMOS AGENDAMENTOS                                              │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ 📅 Hoje 14h - Revisão S/4HANA    [Entrar na Chamada]        │   │
│  │ 📅 Amanhã 10h - Workshop Power BI  [Confirmar]              │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ULTIMAS MENSAGENS                                                   │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ João: Entreguei o relatório de análise... 10:32 📎          │   │
│  │ Você: Vou revisar e te dou feedback...    09:15              │   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

### 5. Dashboard Consultor

```
┌─────────────────────────────────────────────────────────────────────┐
│ [LOGO]  [📋 Projetos] [📅 Agenda] [💰 Conta] [⭐ Avaliações]      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Olá, João!  💰 Saldo: R$ 8.420 | ⭐ 4.9 | 🏆 Nível: Master       │
│                                                                     │
│  ┌──────────┬──────────┬──────────┬──────────┐                     │
│  │  R$ 8.420│  R$ 2.100│  4       │  #3      │                     │
│  │Disponível│ Bloqueado│Projetos  │ Ranking  │                     │
│  └──────────┴──────────┴──────────┴──────────┘                     │
│                                                                     │
│  MEUS PROJETOS ATIVOS                                               │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ ● Implantação SAP - Empresa XYZ   85%  ████████░  [▶]       │   │
│  │ ● Consultoria FI/CO - Empresa ABC 45%  ████░░░░░  [▶]       │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  NOVAS PROPOSTAS                                                    │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ 🆕 Consultoria Power BI - Startup Tech    R$ 3.000  [Ver]   │   │
│  │ 🆕 Projeto SAP MM - Indústria ABC        R$ 5.400  [Ver]   │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  CONTA CORRENTE                                                     │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ Receita do Mês        R$ 12.450                               │   │
│  │ Comissão Plataforma  -R$ 2.490  (20%)                         │   │
│  │ Taxas Stripe         -R$ 285,00                               │   │
│  │ ─────────────────────────────────                             │   │
│  │ Líquido Disponível    R$ 9.675                               │   │
│  │                                                [Solicitar Saque]│   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

### 6. Dashboard Admin

```
┌─────────────────────────────────────────────────────────────────────┐
│ [LOGO]  [Users] [Consultores] [Projetos] [Pagtos] [Relatórios] ⚙️ │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────┬──────────┬──────────┬──────────┬──────────┐          │
│  │R$ 1.2M   │  15%     │ R$ 180k  │  8.5%   │   78     │          │
│  │  GMV     │Comissão  │ Receita  │  Churn  │   NPS    │          │
│  │  Mês     │  Média   │  Líquida │  Mensal │          │          │
│  └──────────┴──────────┴──────────┴──────────┴──────────┘          │
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ GRÁFICO: Receita Mensal                                      │   │
│  │  ██                                                          │   │
│  │  ██ ██                                                       │   │
│  │  ██ ██ ██   ██                                               │   │
│  │  ██ ██ ██ ██ ██ ██                                           │   │
│  │  ──────────────────                                          │   │
│  │  Jan Fev Mar Abr Mai Jun Jul Ago Set Out Nov Dez             │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  APROVAÇÕES PENDENTES                                                │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ [👤] Maria Santos - Consultora    Cadastro    [✅] [❌]     │   │
│  │ [👤] Carlos Lima - Consultor      Documentos  [✅] [❌]     │   │
│  │ [💰] João Silva - Saque R$ 5.000  Pagamento   [✅] [❌]     │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ÚLTIMAS ATIVIDADES                                                 │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ 10:32  Novo Cadastro: Empresa Tech Ltda                     │   │
│  │ 10:15  Saque Aprovado: João Silva - R$ 3.200                │   │
│  │ 09:50  Projeto Concluído: Implantação SAP - Indústria ABC   │   │
│  │ 09:30  Disputa Aberta: Entrega não aprovada - Projeto #42   │   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

### 7. Checkout (Pacote de Horas)

```
┌─────────────────────────────────────────────────────────────────────┐
│ [LOGO]  Planos > Checkout                                           │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  PLANO SELECIONADO: OURO                                           │
│  ┌────────────────────────────────────┬──────────────────────────┐  │
│  │                                    │                          │  │
│  │  Pacote Ouro                       │  Resumo do Pedido        │  │
│  │  50 horas de consultoria           │                          │  │
│  │  Validade: 270 dias               │  50h Consultoria...R$6.500│  │
│  │                                    │  ─────────────────────── │  │
│  │  ✔ Horas avulsas                   │  Subtotal      R$ 6.500  │  │
│  │  ✔ Suporte prioritário             │  Cashback (-5%) -R$ 325  │  │
│  │  ✔ Relatórios mensais              │  ─────────────────────── │  │
│  │  ✔ 5% de cashback                  │  Total         R$ 6.175  │  │
│  │                                    │                          │  │
│  │                                    │  Cupom: [________] [Aplicar]│
│  │                                    │                          │  │
│  │                                    │  Forma de Pagamento      │  │
│  │                                    │  ○ Cartão de Crédito     │  │
│  │                                    │  ● Boleto                │  │
│  │                                    │  ○ Pix                   │  │
│  │                                    │                          │  │
│  │                                    │  [Finalizar Compra]      │  │
│  └────────────────────────────────────┴──────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

### 8. Chat (Projeto)

```
┌─────────────────────────────────────────────────────────────────────┐
│ [← Voltar]  Implantação SAP - João Silva    ● Online   📞 🎥      │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐     │
│  │  📎 João anexou: relatorio_analise.pdf                     │     │
│  │  João Silva - 10:32                                       │     │
│  └────────────────────────────────────────────────────────────┘     │
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐     │
│  │  Obrigado, João! Vou revisar e te dou retorno até amanhã. │     │
│  │  Você - 10:35                       ✓✓ Lida               │     │
│  └────────────────────────────────────────────────────────────┘     │
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐     │
│  │  Perfeito! Aguardo seu feedback.                           │     │
│  │  João Silva - 10:36                       ✓✓ Lida          │     │
│  └────────────────────────────────────────────────────────────┘     │
│                                                                     │
│  ┌────────────────────────────────────────────────────────────┐     │
│  │  ⏳ João está digitando...                                 │     │
│  └────────────────────────────────────────────────────────────┘     │
│                                                                     │
│  ┌────────────────────────────────────┬──────────────────────────┐  │
│  │ 📎 😊 📷 🎤  [ Digite sua mensagem...]  [➤]                  │  │
│  └────────────────────────────────────┴──────────────────────────┘  │
└─────────────────────────────────────────────────────────────────────┘
```

### 9. Conta Corrente

```
┌─────────────────────────────────────────────────────────────────────┐
│ [LOGO]  [📊 Dashboard] [💳 Conta Corrente] [📜 Extrato] [🏦 Saques]│
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  SALDO                                                              │
│  ┌──────────────┬──────────────┬──────────────┐                    │
│  │  R$ 8.420    │  R$ 2.100    │  R$ 6.320    │                    │
│  │  Disponível  │  Bloqueado   │  Total       │                    │
│  │              │  (em análise)│              │                    │
│  │              │              │              │                    │
│  │                    [Solicitar Saque]       │                    │
│  └──────────────┴──────────────┴──────────────┘                    │
│                                                                     │
│  RECEITA DO MÊS                                                     │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ Descrição                    Valor      Plataforma   Líquido │   │
│  │ ──────────────────────────────────────────────────────────── │   │
│  │ Implantação SAP - 20h     R$ 3.600     -R$ 720     R$ 2.880 │   │
│  │ Consultoria FI/CO - 15h   R$ 2.400     -R$ 480     R$ 1.920 │   │
│  │ Power BI - 10h            R$ 1.500     -R$ 300     R$ 1.200 │   │
│  │ ──────────────────────────────────────────────────────────── │   │
│  │ Total                     R$ 7.500     -R$ 1.500   R$ 6.000 │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
│  ÚLTIMOS SAQUES                                                     │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │ 15/06 - R$ 3.000 - PIX - Concluído                          │   │
│  │ 01/06 - R$ 5.000 - PIX - Concluído                          │   │
│  │ 15/05 - R$ 2.500 - PIX - Concluído                          │   │
│  └──────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

### 10. Agenda

```
┌─────────────────────────────────────────────────────────────────────┐
│ [← Voltar]  Minha Agenda    ◀ Junho 2026 ▶    [Sync Google Calendar]│
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  Dom  Seg  Ter  Qua  Qui  Sex  Sab                                  │
│            1    2    3    4    5                                     │
│   6    7    8    9   10   11   12                                    │
│  13   14   15  ▶16◀  17   18   19                                    │
│  20   21   22   23   24   25   26                                    │
│  27   28   29   30                                                   │
│                                                                     │
│  ─────────────── 16 de Junho ───────────────                        │
│                                                                     │
│  09:00 ┌────────────────────────────────────────────────────────┐  │
│        │ Implantação SAP - Revisão Semanal                       │  │
│  10:00 │ Empresa XYZ                                     [▶ Entrar]│  │
│        └────────────────────────────────────────────────────────┘  │
│                                                                     │
│  11:00 ┌────────────────────────────────────────────────────────┐  │
│        │ Workshop Power BI - Módulo 3                            │  │
│  12:00 │ Startup Tech                                    [▶ Entrar]│  │
│        └────────────────────────────────────────────────────────┘  │
│                                                                     │
│  14:00 ┌────────────────────────────────────────────────────────┐  │
│        │ Consultoria SAP FI/CO - Análise de Requisitos           │  │
│  15:00 │ Indústria ABC                                           │  │
│        └────────────────────────────────────────────────────────┘  │
│                                                                     │
│  16:00 Livre                                                        │
│  17:00 Livre                                                        │
└─────────────────────────────────────────────────────────────────────┘
```

---

## 8. DESIGN SYSTEM

### 1. Marca

```
Nome: Consultoria SaaS
Tagline: A consultoria especializada que sua empresa merece
Tipo: Premium · Profissional · Tecnológico
```

### 2. Paleta de Cores

#### Light Mode

| Token | Cor | Hex | Uso |
|-------|-----|-----|-----|
| primary | Blue 600 | #2563EB | Botões primários, links, destaques |
| primary-dark | Blue 700 | #1D4ED8 | Hover |
| primary-light | Blue 50 | #EFF6FF | Backgrounds sutis |
| secondary | Slate 600 | #475569 | Textos secundários |
| accent | Emerald 500 | #10B981 | Sucesso, cashback, badges |
| warning | Amber 500 | #F59E0B | Alertas |
| danger | Red 500 | #EF4444 | Erros, cancelamentos |
| surface | White | #FFFFFF | Cards, modais |
| background | Gray 50 | #F8FAFC | Background principal |
| text-primary | Slate 900 | #0F172A | Títulos |
| text-secondary | Slate 500 | #64748B | Corpo |
| border | Gray 200 | #E2E8F0 | Bordas |
| muted | Gray 100 | #F1F5F9 | Backgrounds secundários |

#### Dark Mode

| Token | Cor | Hex |
|-------|-----|-----|
| surface | Slate 800 | #1E293B |
| background | Slate 900 | #0F172A |
| text-primary | Gray 50 | #F8FAFC |
| text-secondary | Slate 400 | #94A3B8 |
| border | Slate 700 | #334155 |
| muted | Slate 800 | #1E293B |

### 3. Tipografia

| Elemento | Fonte | Peso | Tamanho | Altura |
|----------|-------|------|---------|--------|
| Display | Inter | Bold | 48px | 1.2 |
| H1 | Inter | Bold | 36px | 1.25 |
| H2 | Inter | SemiBold | 28px | 1.3 |
| H3 | Inter | SemiBold | 22px | 1.35 |
| Body LG | Inter | Regular | 16px | 1.5 |
| Body SM | Inter | Regular | 14px | 1.5 |
| Caption | Inter | Regular | 12px | 1.4 |
| Label | Inter | Medium | 14px | 1.4 |
| Button | Inter | SemiBold | 15px | 1.4 |

### 4. Espaçamentos

| Token | px | rem |
|-------|-----|-----|
| xs | 4px | 0.25rem |
| sm | 8px | 0.5rem |
| md | 16px | 1rem |
| lg | 24px | 1.5rem |
| xl | 32px | 2rem |
| 2xl | 48px | 3rem |
| 3xl | 64px | 4rem |

### 5. Bordas e Sombras

| Token | Valor |
|-------|-------|
| radius-sm | 6px |
| radius-md | 10px |
| radius-lg | 16px |
| radius-xl | 24px |
| radius-full | 9999px |
| shadow-sm | 0 1px 2px rgba(0,0,0,0.05) |
| shadow-md | 0 4px 6px rgba(0,0,0,0.07) |
| shadow-lg | 0 10px 25px rgba(0,0,0,0.1) |

### 6. Componentes

**Botões:**
```
Primary   [█████████████]
Secondary [█████████████]
Outline   [█████████████]
Ghost     [█████████████]
Danger    [█████████████]
Sizes: sm [█], md [███], lg [█████]
```

**Inputs:**
```
Label
┌──────────────────────────────┐
│ Placeholder                   │
└──────────────────────────────┘
Helper text

Erro:
┌──────────────────────────────┐
│ Valor inválido               │
└──────────────────────────────┘
❌ Mensagem de erro
```

**Cards:**
```
┌─────────────────────────────────────┐
│                                     │
│  Título do Card                     │
│  Descrição ou conteúdo              │
│                                     │
│  [Ação]                             │
└─────────────────────────────────────┘
```

**Tabelas:**
```
┌─────────┬──────────┬──────────┬──────────┐
│ Header  │ Header   │ Header   │ Header   │
├─────────┼──────────┼──────────┼──────────┤
│ Dado    │ Dado     │ Dado     │ Dado     │
│ Dado    │ Dado     │ Dado     │ Dado     │
└─────────┴──────────┴──────────┴──────────┘
```

**Badges / Tags:**
```
[Sucesso]  [Atenção]  [Erro]  [Info]
[Premium]  [Novo]     [Beta]  [Em Breve]
```

### 7. Grid

```
Desktop: 12 colunas, gutter 24px, max-width 1280px
Tablet:  12 colunas, gutter 16px, max-width 1024px
Mobile:  4 colunas, gutter 16px, max-width 640px
```

### 8. Animações

| Tipo | Duração | Easing | Uso |
|------|---------|--------|-----|
| Fade | 200ms | ease | Aparecer/desaparecer |
| Slide | 300ms | ease-out | Drawers, modais |
| Scale | 150ms | ease-in-out | Hover em cards |
| Spin | 1s | linear | Loading |

### 9. Ícones

Lucide Icons (conjunto compatível com o design minimalista).

### 10. Tokens (CSS Custom Properties)

```css
:root {
  --color-primary: #2563EB;
  --color-primary-dark: #1D4ED8;
  --color-primary-light: #EFF6FF;
  --color-surface: #FFFFFF;
  --color-background: #F8FAFC;
  --color-text-primary: #0F172A;
  --color-text-secondary: #64748B;
  --color-border: #E2E8F0;
  --color-success: #10B981;
  --color-warning: #F59E0B;
  --color-danger: #EF4444;

  --font-family: 'Inter', sans-serif;
  --font-display: 700 48px/1.2 var(--font-family);
  --font-h1: 700 36px/1.25 var(--font-family);
  --font-h2: 600 28px/1.3 var(--font-family);
  --font-h3: 600 22px/1.35 var(--font-family);
  --font-body: 400 16px/1.5 var(--font-family);
  --font-body-sm: 400 14px/1.5 var(--font-family);
  --font-label: 500 14px/1.4 var(--font-family);
  --font-button: 600 15px/1.4 var(--font-family);

  --space-xs: 4px;
  --space-sm: 8px;
  --space-md: 16px;
  --space-lg: 24px;
  --space-xl: 32px;
  --space-2xl: 48px;
  --space-3xl: 64px;

  --radius-sm: 6px;
  --radius-md: 10px;
  --radius-lg: 16px;
  --radius-xl: 24px;

  --shadow-sm: 0 1px 2px rgba(0,0,0,0.05);
  --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
  --shadow-lg: 0 10px 25px rgba(0,0,0,0.1);
}

[data-theme="dark"] {
  --color-surface: #1E293B;
  --color-background: #0F172A;
  --color-text-primary: #F8FAFC;
  --color-text-secondary: #94A3B8;
  --color-border: #334155;
}
```

---

## 9. BACKLOG E USER STORIES

### Priorização MoSCoW

| Épico | Prioridade | Story Points | Release |
|-------|-----------|--------------|---------|
| Core Platform Setup | Must | 8 | MVP |
| Authentication & Roles | Must | 13 | MVP |
| Marketplace | Must | 34 | MVP |
| Service Plans & Checkout | Must | 21 | MVP |
| Wallet & Payments | Must | 34 | MVP |
| Projects & Proposals | Must | 34 | MVP |
| Time Tracking | Must | 13 | MVP |
| Chat | Must | 21 | MVP |
| Videoconference | Must | 13 | MVP |
| Contracts | Must | 21 | MVP |
| Reviews & Ratings | Must | 8 | MVP |
| SLA | Must | 13 | MVP |
| Notifications | Must | 13 | MVP |
| Dashboards | Must | 21 | MVP |
| Admin Panel | Must | 21 | MVP |
| Support Tickets | Must | 13 | MVP |
| Gamification | Should | 21 | V2 |
| Affiliates Program | Should | 21 | V2 |
| Cashback | Should | 13 | V2 |
| Matching IA | Should | 34 | V2 |
| Mobile App | Could | 55 | V3 |
| Microservices Migration | Won't | 89 | V4 |

### User Stories (51 no total)

**Épico: Core Platform Setup**
- US-001: Instalar plugin via WordPress → Plugin ativa sem erros, tabelas criadas
- US-002: Configurar integrações via painel → Formulário salva, conexões testadas
- US-003: Gerenciar planos de serviço → CRUD completo, sincronia WooCommerce

**Épico: Authentication & Roles**
- US-004: Cadastro como cliente ou consultor → Registro, validação, escolha de perfil
- US-005: Login com email e senha → JWT, refresh token, 2FA opcional
- US-006: Onboarding do consultor → Formulário multi-etapas, aprovação admin
- US-007: Aprovar/rejeitar cadastros → Lista de pendências, aprovação 1 clique
- US-008: Bloquear/desbloquear usuários → Ação imediata, notificação

**Épico: Marketplace**
- US-009: Publicar demanda → Formulário com categorias, orçamento estimado
- US-010: Buscar consultores → Filtros, busca fulltext, ordenação
- US-011: Notificar consultores de novas demandas → Push/email
- US-012: Enviar proposta → Valor, horas, mensagem personalizada
- US-013: Visualizar e aceitar propostas → Comparativo, aceite 1 clique
- US-014: Contratação direta → Busca, filtros, perfil, contratar

**Épico: Service Plans & Checkout**
- US-015: Visualizar planos → Página com comparativo
- US-016: Comprar pacote → Stripe, confirmação imediata
- US-017: Ver saldo de horas → Dashboard, header
- US-018: Recarregar saldo → Mesmo fluxo de checkout

**Épico: Wallet & Payments**
- US-019: Visualizar conta corrente → Dashboard financeiro
- US-020: Solicitar saque via PIX → Formulário, aprovação
- US-021: Aprovar saques (admin) → Lista, transferência Stripe
- US-022: Split automático Stripe Connect → Escrow

**Épico: Projects & Proposals**
- US-023: Acompanhar projeto → Timeline, milestones, horas
- US-024: Criar milestones → CRUD, notificação
- US-025: Registrar horas → Timer ou manual
- US-026: Aprovar/rejeitar horas → Controle de consumo

**Épico: Chat**
- US-027: Conversar no projeto → Tempo real, arquivos
- US-028: Notificações de mensagens → Push, badge
- US-029: Status online e confirmação de leitura → ✓✓

**Épico: Contracts**
- US-030: Assinar contrato digital → Docuseal, token
- US-031: Baixar contrato PDF → Link para download
- US-032: Gerar contrato automático → Template padronizado

**Épico: SLA**
- US-033: Configurar regras SLA → CRUD
- US-034: Monitorar SLA → Relógio, alertas
- US-035: Escalonamento automático → Suporte/admin

**Épico: Dashboards**
- US-036: Dashboard cliente → Cards, gráficos, listas
- US-037: Dashboard consultor → Financeiro, ranking
- US-038: Dashboard admin → GMV, receita, CAC, LTV, NPS, Churn

**Épico: Gamification**
- US-039: Ganhar XP e subir nível → Pontuação automática
- US-040: Conquistar badges → Automáticos, perfil
- US-041: Ranking da plataforma → Semanal/mensal

**Épico: Matching IA**
- US-042: Calcular score de matching → Algoritmo multi-critério
- US-043: Recomendações automáticas → Top 5, score visível
- US-044: Notificação seletiva por score → Otimização

**Épico: Affiliates**
- US-045: Cadastro como afiliado → Link único, QR code
- US-046: Dashboard de afiliado → Cliques, conversões
- US-047: Comissão sobre vendas → Automática, mensal

**Épico: Cashback**
- US-048: Receber cashback → Percentual configurável
- US-049: Utilizar cashback → Aplicação no checkout

**Épico: Notifications**
- US-050: Receber notificações → Email, push, interna
- US-051: Configurar preferências → Controle do usuário

---

## 10. DIAGRAMAS UML

### 1. Diagrama de Casos de Uso

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    SISTEMA CONSULTORIA SAAS                             │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                         CLIENTE                                 │   │
│  │     ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐    │   │
│  │     │ Cadastrar    │  │ Comprar      │  │ Publicar         │    │   │
│  │     │ Conta        │  │ Pacote Horas │  │ Demanda          │    │   │
│  │     └──────┬───────┘  └──────┬───────┘  └────────┬─────────┘    │   │
│  │     ┌──────┴───────┐  ┌──────┴───────┐  ┌────────┴─────────┐    │   │
│  │     │ Avaliar      │  │ Aprovar      │  │ Escolher         │    │   │
│  │     │ Consultor    │  │ Entregas     │  │ Consultor        │    │   │
│  │     └──────────────┘  └──────────────┘  └──────────────────┘    │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                       CONSULTOR                                 │   │
│  │     ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐    │   │
│  │     │ Completar   │  │ Enviar       │  │ Registrar        │    │   │
│  │     │ Onboarding  │  │ Proposta     │  │ Horas            │    │   │
│  │     └─────────────┘  └──────┬───────┘  └────────┬─────────┘    │   │
│  │                    ┌────────┴────────┐  ┌────────┴─────────┐    │   │
│  │                    │ Solicitar      │  │ Criar            │    │   │
│  │                    │ Saque          │  │ Milestones       │    │   │
│  │                    └─────────────────┘  └──────────────────┘    │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                       ADMINISTRADOR                             │   │
│  │   ┌──────────┐ ┌───────────┐ ┌──────────┐ ┌──────────┐        │   │
│  │   │ Aprovar  │ │ Gerenciar │ │ Gerenciar│ │ Visualizar│        │   │
│  │   │Consultor │ │ Planos    │ │ Saques   │ │Relatórios │        │   │
│  │   └──────────┘ └───────────┘ └──────────┘ └──────────┘        │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                         SUPORTE                                │   │
│  │   ┌──────────┐ ┌───────────┐ ┌──────────┐                      │   │
│  │   │ Atender  │ │ Consultar │ │ Escalar  │                      │   │
│  │   │ Tickets  │ │ Pedidos   │ │ Chamados │                      │   │
│  │   └──────────┘ └───────────┘ └──────────┘                      │   │
│  └──────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
```

### 2. Diagrama de Sequência — Fluxo de Contratação

```
Cliente              Sistema              Consultores          Stripe
   │                    │                     │                  │
   │  1. Publica        │                     │                  │
   │   Demanda          │                     │                  │
   │──────────────────▶│                     │                  │
   │                    │  2. Calcula         │                  │
   │                    │     Matching Score  │                  │
   │                    │─────────────────────│                 │
   │                    │  3. Notifica Top 5  │                  │
   │                    │────────────────────▶│                  │
   │                    │                     │                  │
   │                    │  4. Enviam          │                  │
   │                    │     Propostas       │                  │
   │                    │◀────────────────────│                  │
   │  5. Visualiza      │                     │                  │
   │     Propostas      │                     │                  │
   │◀───────────────────│                     │                  │
   │                    │                     │                  │
   │  6. Aceita         │                     │                  │
   │     Proposta       │                     │                  │
   │──────────────────▶│                     │                  │
   │                    │  7. Gera Contrato   │                  │
   │                    │─────────────────────│                  │
   │                    │  8. Envia para      │                  │
   │                    │     Assinatura      │                  │
   │                    │◀────────────────────│                  │
   │  9. Assina         │                     │                  │
   │◀───────────────────│                     │                  │
   │  10. Contrato      │                     │                  │
   │      Assinado      │                     │                  │
   │──────────────────▶│                     │                  │
   │                    │  11. Libera         │                  │
   │                    │      Projeto        │                  │
   │                    │────────────────────▶│                  │
   │                    │                     │                  │
   │  12. Consultor     │                     │                  │
   │      Registra      │                     │                  │
   │      Horas         │                     │                  │
   │                    │◀────────────────────│                  │
   │  13. Cliente       │                     │                  │
   │      Aprova        │                     │                  │
   │──────────────────▶│                     │                  │
   │                    │  14. Libera         │                  │
   │                    │      Pagamento      │                  │
   │                    │───────────────────────────────────────▶│
   │                    │                     │  15. Split      │
   │                    │                     │      Automático │
   │                    │                     │◀─────────────────│
   │                    │  16. Notifica       │                  │
   │                    │     Pagamento       │                  │
   │                    │────────────────────▶│                  │
```

### 3. Diagrama de Classes (Domínio Principal)

```
┌─────────────────────────────────────────────────────────────────────────┐
│                           User (abstract)                                │
│  ┌─────────────────────────────────────────────────────────────────┐   │
│  │ - id: BIGINT                                                    │   │
│  │ - email: string                                                 │   │
│  │ - password: string (hashed)                                     │   │
│  │ - displayName: string                                           │   │
│  │ - role: Role enum {client, consultant, support, admin}          │   │
│  │ - status: UserStatus enum                                       │   │
│  │ - avatar: string                                                │   │
│  │ - phone: string                                                 │   │
│  │ + login(): JWT                                                  │   │
│  │ + logout(): void                                                │   │
│  └──────────────┬──────────────────────────────────────────────────┘   │
│                 │                                                       │
│        ┌────────┴────────┐                                              │
│        │                  │                                              │
│  ┌─────┴──────┐    ┌─────┴──────┐                                       │
│  │   Client    │    │ Consultant  │                                       │
│  ├─────────────┤    ├────────────┤                                       │
│  │ - company   │    │ - title    │                                       │
│  │ - cnpj      │    │ - bio      │                                       │
│  │ - totalSpent│    │ - rating   │                                       │
│  │ + buyPlan() │    │ - level    │                                       │
│  │ + postJob() │    │ - hourlyRate                                      │
│  │ + review()  │    │ + submitProposal()                                 │
│  └─────────────┘    │ + logTime() │                                       │
│                     │ + withdraw()│                                       │
│                     └──────┬──────┘                                       │
│                            │                                              │
│                     ┌──────┴──────┐                                       │
│                     │  Expertise   │                                       │
│                     ├─────────────┤                                       │
│                     │ - category  │                                       │
│                     │ - subcategory                                      │
│                     │ - years     │                                       │
│                     └─────────────┘                                       │
│                                                                           │
│  ┌────────────────┐  ┌─────────────┐  ┌──────────────────┐              │
│  │   ServicePlan   │  │   Order     │  │    Project       │              │
│  ├────────────────┤  ├─────────────┤  ├──────────────────┤              │
│  │ - name         │  │ - total     │  │ - title          │              │
│  │ - hours        │  │ - hours     │  │ - status         │              │
│  │ - price        │  │ - status    │  │ - category       │              │
│  │ - validityDays │  │ + approve() │  │ + addMilestone() │              │
│  └────────┬───────┘  └──────┬──────┘  │ + approveHours() │              │
│           │                 │         │ + complete()     │              │
│           │     1:N        │         └────────┬─────────┘              │
│           └─────────────────┘                  │                        │
│                                        ┌───────┴────────┐              │
│                                        │                │              │
│                                   ┌────┴─────┐   ┌─────┴────┐        │
│                                   │ Proposal  │   │  Contract │        │
│                                   ├──────────┤   ├───────────┤        │
│                                   │ - value  │   │ - status  │        │
│                                   │ - hours  │   │ - docUrl  │        │
│                                   │ - status │   │ + sign()  │        │
│                                   └──────────┘   └───────────┘        │
│                                                                         │
│  ┌──────────┐  ┌──────────────┐  ┌────────────┐                       │
│  │  Wallet   │  │  Transaction │  │ Withdrawal │                       │
│  ├──────────┤  ├──────────────┤  ├────────────┤                       │
│  │ - balance│  │ - type       │  │ - amount   │                       │
│  │ - blocked│  │ - amount     │  │ - status   │                       │
│  │ + add()  │  │ - reference  │  │ - pixKey   │                       │
│  │ + remove()│  │ + process()  │  │ + approve()│                       │
│  └──────────┘  └──────────────┘  └────────────┘                       │
│                                                                         │
│  ┌──────────┐  ┌──────────────┐  ┌────────────┐                       │
│  │ Message  │  │ Appointment  │  │  SLA Rule  │                       │
│  ├──────────┤  ├──────────────┤  ├────────────┤                       │
│  │ - content│  │ - startTime  │  │ - respTime │                       │
│  │ - type   │  │ - endTime    │  │ - delivTime│                       │
│  │ - readAt │  │ - meetingUrl │  │ + evaluate()│                       │
│  └──────────┘  └──────────────┘  └────────────┘                       │
└─────────────────────────────────────────────────────────────────────────┘
```

### 4. Diagrama de Atividades — Fluxo de Pagamento

```
   Cliente              Stripe              Plataforma          Consultor
      │                   │                     │                  │
      ▼                   │                     │                  │
  ┌──────────┐            │                     │                  │
  │ Compra   │            │                     │                  │
  │ Pacote   │            │                     │                  │
  └────┬─────┘            │                     │                  │
       │                  │                     │                  │
       ▼                  │                     │                  │
  ┌──────────┐            │                     │                  │
  │ Pagamento│───────────▶│                     │                  │
  │ Stripe   │            │                     │                  │
  └──────────┘            │                     │                  │
                          │                     │                  │
                     ┌────┴─────┐               │                  │
                     │ Cobra    │               │                  │
                     │ 2.9%+R$0.50            │                  │
                     └────┬─────┘               │                  │
                          │                     │                  │
                          ▼                     │                  │
                     ┌──────────────┐            │                  │
                     │ Envia para  │────────────▶│                  │
                     │ Plataforma  │            │                  │
                     │ (100%)      │            │                  │
                     └─────────────┘            │                  │
                                                 │                  │
                                           ┌─────┴──────┐           │
                                           │ Retém      │           │
                                           │ Comissão   │           │
                                           │ Plataforma │           │
                                           │ (20%)      │           │
                                           └─────┬──────┘           │
                                                 │                  │
                                           ┌─────┴──────┐           │
                                           │ Transfere  │──────────▶│
                                           │ Saldo      │           │
                                           │ Líquido    │           │
                                           │ p/Consultor│           │
                                           └────────────┘           │
                                                                     │
                                                              ┌──────┴──────┐
                                                              │ Saldo       │
                                                              │ Disponível  │
                                                              │ Wallet      │
                                                              └─────────────┘
```

### 5. Diagrama de Estados — Projeto

```
                  ┌─────────────┐
                  │    Open     │
                  └──────┬──────┘
                         │
                         ▼
                  ┌─────────────┐
           ┌─────│  Proposals  │──────┐
           │     └─────────────┘      │
           │         │                │
           │         ▼                │
           │  ┌─────────────┐         │
           │  │ In Progress │         │
           │  └──────┬──────┘         │
           │         │                │
           │         ▼                │
           │  ┌─────────────┐         │
           │  │   Review    │         │
           │  └──────┬──────┘         │
           │         │                │
           │    ┌────┴────┐           │
           │    ▼         ▼           │
           │ ┌────┐   ┌────────┐     │
           │ │Com.│   │Rejeit. │     │
           │ └──┬─┘   └───┬────┘     │
           │    │          │          │
           ▼    ▼          ▼          ▼
     ┌────────┐      ┌────────┐  ┌────────┐
     │Cancelled│    │Completed│  │Disputed│
     └─────────┘     └────────┘  └────────┘
```

### 6. Fluxograma — Matching IA

```
         Início
           │
           ▼
  ┌──────────────────┐
  │ Cliente Publica  │
  │ Demanda          │
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────┐
  │ Extrair          │
  │ Características  │
  │ da Demanda       │
  │ - Categoria      │
  │ - Subcategoria   │
  │ - Orçamento      │
  │ - Urgência       │
  └────────┬─────────┘
           │
           ▼
  ┌──────────────────────────────────┐
  │ Filtrar Consultores Elegíveis    │
  │ - Status = active                │
  │ - Disponibilidade compatível     │
  │ - Especialidade correspondente   │
  └────────┬─────────────────────────┘
           │
           ▼
  ┌──────────────────────────────────┐
  │ Calcular Score Individual        │
  │                                  │
  │ Peso 1: Expertise Match (30%)    │
  │ Peso 2: Rating (20%)             │
  │ Peso 3: Disponibilidade (15%)    │
  │ Peso 4: Compatibilidade          │
  │         Orçamento (10%)          │
  │ Peso 5: Idiomas (5%)             │
  │ Peso 6: Histórico (10%)          │
  │ Peso 7: Proximidade (5%)         │
  │ Peso 8: Tempo Resposta (5%)      │
  └────────┬─────────────────────────┘
           │
           ▼
  ┌──────────────────────────────────┐
  │ Ordenar por Score (desc)         │
  │ Selecionar Top 5                 │
  └────────┬─────────────────────────┘
           │
           ▼
  ┌──────────────────────────────────┐
  │ Notificar Consultores            │
  │ Enviar Push/Email/WhatsApp       │
  └────────┬─────────────────────────┘
           │
           ▼
  ┌──────────────────────────────────┐
  │ Registrar Score no Banco         │
  │ (cp_matching_scores)             │
  └────────┬─────────────────────────┘
           │
           ▼
  ┌──────────────────────────────────┐
  │ Feedback Loop:                   │
  │ Se consultor for contratado →    │
  │   Aprender e ajustar pesos       │
  │ Se consultor ignorar →           │
  │   Reduzir score futuro           │
  └──────────────────────────────────┘
           │
           ▼
          Fim
```

---

## 11. API REST — DOCUMENTAÇÃO COMPLETA

### Informações Gerais

- **Base URL:** `https://consultoriasaas.com.br/wp-json/consultoria/v1`
- **Formato:** JSON
- **Autenticação:** JWT Bearer Token
- **Timezone:** UTC

### Autenticação

**POST /auth/login**
Login do usuário.
```json
// Request
{ "email": "usuario@exemplo.com", "password": "123456" }
// Response (200)
{ "success": true, "data": { "token": "eyJ...", "refresh_token": "a1b2...", "user": { "id": 1, "email": "...", "display_name": "João Silva", "role": "consultant" } } }
```

**POST /auth/register** — Registro de novo usuário.
**POST /auth/refresh** — Renova token JWT.
**GET /auth/me** — Retorna dados do usuário autenticado (Bearer token).

### Consultores

**GET /consultants** — Lista consultores disponíveis.
- Query: q, category, min_rate, max_rate, min_rating, sort_by, page, per_page

**GET /consultants/{id}** — Perfil completo do consultor.
**PUT /consultants/profile** — Atualiza perfil do consultor autenticado.
**POST /consultants/expertise** — Adiciona especialidade.
**POST /consultants/certifications** — Adiciona certificação.
**PUT /consultants/availability** — Atualiza disponibilidade semanal.

### Projetos

**GET /projects** — Lista projetos do usuário autenticado.
**POST /projects** — Cria nova demanda.
```json
{ "title": "Implantação SAP S/4HANA", "description": "...", "category": "ERP", "subcategory": "SAP", "scope": "consultoria", "estimated_hours": 50, "budget": 8000.00 }
```
**GET /projects/{id}** — Detalhes do projeto.
**PUT /projects/{id}** — Atualiza projeto.
**GET /projects/{id}/milestones** — Lista marcos.
**POST /projects/{id}/milestones** — Cria marco.
**GET /projects/{id}/time-entries** — Lista horas lançadas.
**POST /projects/{id}/time-entries** — Registra horas.
```json
{ "hours": 4.5, "description": "Análise de requisitos", "date": "2026-07-09" }
```
**GET /projects/{id}/deliverables** — Lista entregas.
**POST /projects/{id}/deliverables** — Registra entrega.

### Propostas

**POST /proposals** — Envia proposta.
```json
{ "project_id": 1, "value": 7500.00, "estimated_hours": 50, "message": "...", "delivery_estimate": 30 }
```
**POST /proposals/{id}/accept** — Aceita proposta (cliente).
**POST /proposals/{id}/reject** — Rejeita proposta (cliente).

### Carteira

**GET /wallet** — Saldo da carteira.
**GET /wallet/transactions** — Lista transações.
**POST /withdrawals** — Solicita saque.
```json
{ "amount": 1000.00, "pix_key": "joao@email.com", "pix_key_type": "email" }
```
**GET /withdrawals** — Lista saques.
**POST /withdrawals/{id}/approve** — Aprova saque (admin).
**POST /withdrawals/{id}/reject** — Rejeita saque (admin).

### Mensagens

**GET /projects/{project_id}/messages** — Lista mensagens.
**POST /projects/{project_id}/messages** — Envia mensagem.

### Agendamentos

**GET /appointments** — Lista agendamentos.
**POST /appointments** — Cria agendamento.
**PUT /appointments/{id}** — Atualiza agendamento.

### Avaliações

**POST /reviews** — Cria avaliação.
**GET /consultants/{id}/reviews** — Lista avaliações do consultor.

### Contratos

**POST /contracts/{id}/sign** — Assina contrato digital.
**GET /contracts/{id}/download** — Download do contrato.

### Tickets

**GET /tickets** — Lista tickets de suporte.
**POST /tickets** — Abre ticket.
**POST /tickets/{id}/replies** — Responde ticket.

### Dashboard

**GET /dashboard/client** — Dashboard do cliente.
**GET /dashboard/consultant** — Dashboard do consultor.
**GET /dashboard/admin** — Dashboard do admin (requer permissão).

### Marketplace

**GET /marketplace/search** — Busca consultores.
**GET /marketplace/categories** — Lista categorias.
**GET /marketplace/matching/{project_id}** — Matching score.

### Webhooks

Webhooks são enviados via POST para URLs configuradas no admin.

| Evento | Descrição |
|--------|-----------|
| `order.completed` | Pedido concluído |
| `project.started` | Projeto iniciado |
| `project.completed` | Projeto concluído |
| `contract.signed` | Contrato assinado |
| `withdrawal.requested` | Saque solicitado |
| `withdrawal.completed` | Saque concluído |
| `payment.received` | Pagamento recebido |

### Códigos de Erro

| Código | Descrição |
|--------|-----------|
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |

---

## 12. PLANO DE TESTES

### 1. Estratégia

**Níveis de Teste:**
1. Unitário — Funções e métodos isolados (PHPUnit)
2. Integração — Comunicação entre módulos e banco
3. Funcional — Fluxos completos do usuário
4. Aceitação — Validação com stakeholders
5. Performance — Carga e estresse
6. Segurança — OWASP Top 10

**Ferramentas:** PHPUnit, WP_Mock, Selenium/Cypress, Lighthouse, OWASP ZAP

### 2. Estrutura de Testes

```
tests/
├── Unit/
│   ├── Helpers/ (ValidatorTest, FunctionsTest, ResponseTest)
│   ├── Services/ (AuthServiceTest, WalletServiceTest, MarketplaceServiceTest)
│   └── Models/ (UserTest)
├── Integration/
│   ├── Database/ (SchemaTest)
│   ├── Api/ (AuthEndpointTest, ProjectEndpointTest)
│   └── Modules/ (CashbackTest)
└── Feature/
    ├── MarketplaceFlowTest.php
    └── PaymentFlowTest.php
```

**Cobertura Mínima:** Services 90%, Helpers 95%, Controllers 85%, Models 90%

### 3. Casos de Teste Funcionais

| ID | Caso | Resultado Esperado |
|----|------|--------------------|
| FT-01 | Registro Cliente | Conta criada, email confirmação |
| FT-02 | Registro Consultor | Conta criada, status pending |
| FT-03 | Login | Token JWT gerado |
| FT-04 | Compra Pacote | Pedido criado, horas creditadas |
| FT-05 | Publicar Demanda | Projeto criado, status open |
| FT-06 | Enviar Proposta | Proposta registrada |
| FT-07 | Aceitar Proposta | Contrato gerado, in_progress |
| FT-08 | Assinar Contrato | Contract signed |
| FT-09 | Registrar Horas | Horas pendentes aprovação |
| FT-10 | Aprovar Horas | Pagamento liberado |
| FT-11 | Solicitar Saque | Saque pending |
| FT-12 | Aprovar Saque | Transferência realizada |
| FT-13 | Chat Mensagem | Mensagem em tempo real |
| FT-14 | Agendar Reunião | Appointment criado |
| FT-15 | Avaliar Projeto | Review registrada |

### 4. Testes de Segurança (OWASP)

| ID | Vulnerabilidade | Teste |
|----|----------------|-------|
| S-01 | SQL Injection | Inputs com ' OR 1=1 |
| S-02 | XSS | <script>alert(1)</script> |
| S-03 | CSRF | Verificar nonce |
| S-04 | Broken Auth | Acessar sem token |
| S-05 | Sensitive Data | Criptografia de senhas |
| S-06 | Rate Limiting | 1000 req/min |
| S-07 | JWT Security | Token expirado |
| S-08 | Role Escalation | Cliente em rota admin |
| S-09 | IDOR | Projeto alheio |
| S-10 | File Upload | Arquivo malicioso |

### 5. Testes de Carga

| Cenário | Usuários Concorrentes | Métrica Alvo |
|---------|----------------------|--------------|
| Pico de login | 1.000 | < 2s resposta |
| Marketplace search | 500 | < 1s resposta |
| Checkout simultâneo | 200 | 100% completados |
| Chat em massa | 1.000 conexões | < 500ms latência |

**Métricas:** Response Time p95 < 2s, Throughput > 100 req/s, Error Rate < 1%, CPU < 70%, Memory < 80%

---

## 13. PLANO DE DEVOPS E INFRAESTRUTURA

### 1. Arquitetura de Infraestrutura (MVP)

```
┌──────────────────────────────────────────────────────────────┐
│                   Cloudflare (DNS + CDN + WAF)               │
├──────────────────────────────────────────────────────────────┤
│                   Load Balancer (HAProxy)                    │
├──────────────────────────────────────────────────────────────┤
│                                                              │
│   ┌──────────────┐    ┌──────────────┐    ┌──────────────┐  │
│   │  Web Server 1 │    │  Web Server 2 │    │  Web Server 3│  │
│   │   (Nginx)     │    │   (Nginx)     │    │   (Nginx)    │  │
│   │   PHP 8.2     │    │   PHP 8.2     │    │   PHP 8.2    │  │
│   └──────┬───────┘    └──────┬───────┘    └──────┬───────┘  │
│          │                   │                   │           │
│   ┌──────┴───────────────────┴───────────────────┴───────┐  │
│   │                   Redis (Cache/Session)               │  │
│   └───────────────────────────────────────────────────────┘  │
│                                                              │
│   ┌───────────────────────────────────────────────────────┐  │
│   │                MySQL 8+ (Galera Cluster)              │  │
│   │              (Primary + 2 Replicas)                   │  │
│   └───────────────────────────────────────────────────────┘  │
│                                                              │
│   ┌───────────────────────────────────────────────────────┐  │
│   │               Object Storage (S3/MinIO)               │  │
│   │          (Arquivos, contratos, avatars)               │  │
│   └───────────────────────────────────────────────────────┘  │
└──────────────────────────────────────────────────────────────┘
```

### 2. Ambientes

| Ambiente | URL | Propósito |
|----------|-----|-----------|
| Desenvolvimento | dev.consultoriasaas.com.br | Desenvolvimento diário |
| Staging | staging.consultoriasaas.com.br | Homologação e testes |
| Produção | consultoriasaas.com.br | Produção |

### 3. CI/CD Pipeline

**Ferramentas:** GitHub Actions, Docker, SonarQube, Sentry

```
[Push/PR] → [Lint] → [PHPUnit] → [SonarQube] → [Build] → [Deploy Staging] → [Tests] → [Deploy Prod]
```

### 4. Estratégia de Backup

| Item | Frequência | Retenção | Destino |
|------|------------|----------|---------|
| Banco MySQL | 6h | 30 dias | S3 + Local |
| Uploads | Diário | 7 dias | S3 |
| Plugins/Themes | A cada deploy | 3 versões | S3 |
| Configurações | Semanal | 3 meses | S3 |

### 5. Monitoramento e Alertas

**Fase 1 (Gratuito):** Uptime Robot, New Relic Free Tier, Sentry, WP Health
**Fase 2+ (Pago):** Datadog, Grafana + Prometheus, ELK Stack, PagerDuty

**Alertas Críticos:**
- Site offline > 2 min → Notificar equipe
- Erro 5xx > 1% em 5 min → Rollback automático
- CPU > 80% por 10 min → Escalar horizontalmente
- DB Connection > 80% → Aumentar pool

### 6. Escalabilidade

**Horizontal Scaling:** Web servers auto-scaling (3-20 instâncias), read replicas, Redis cluster
**Cache:** Nginx FastCGI / Cloudflare (page), Redis (object), Transients (query), CDN (assets)
**Cloudflare:** Cache HTML bypass, assets 1 ano, minify, Brotli, HTTP/3, Argo

### 7. Processo de Incidentes

1. Detecção → 2. Classificação → 3. Resposta → 4. Resolução → 5. Post-mortem

**SLAs Internos:** Crítico 15min/2h, Alto 30min/4h, Médio 2h/24h, Baixo 24h/72h

---

## 14. GUIA DE EVOLUÇÃO PARA MICROSERVIÇOS

### 1. Visão Geral

Estratégia de evolução gradual de monólito modular (WordPress) para microserviços completos.

### 2. Fases de Evolução

**Fase 1 — Monólito Modular (MVP):** 0-6 meses, WordPress + Plugin + WooCommerce, 37 tabelas, 13 módulos, suporte a 10K usuários simultâneos.

**Fase 2 — Extração de Serviços Críticos:** 6-12 meses
1. Matching IA → Python/Node.js + ML (REST + RabbitMQ)
2. Chat → Node.js + WebSocket
3. Notificações → Node.js + RabbitMQ

**Fase 3 — Aplicativo Mobile:** 6-9 meses, Flutter (iOS + Android)

**Fase 4 — Microserviços Plenos:** 12-18 meses

```
┌─────────────────────────────────────────────────────────────────────┐
│                         API Gateway (Kong)                          │
├─────────────────────────────────────────────────────────────────────┤
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│  │  Auth    │ │  Mktplce │ │ Payment  │ │  Chat    │ │  Matching│ │
│  │  Service │ │  Service │ │  Service │ │  Service │ │  Service │ │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘ └──────────┘ │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│  │  Project │ │  Wallet  │ │  Notifs  │ │  SLA     │ │  Gamific │ │
│  │  Service │ │  Service │ │  Service │ │  Service │ │  Service  │ │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘ └──────────┘ │
├─────────────────────────────────────────────────────────────────────┤
│                  Message Broker (RabbitMQ / Kafka)                   │
├─────────────────────────────────────────────────────────────────────┤
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────────────────┐   │
│  │  MySQL   │ │  Redis   │ │ Elastic │ │  Object Storage    │   │
│  │ (sharded)│ │ (cluster)│ │ Search  │ │  (S3/MinIO)        │   │
│  └──────────┘ └──────────┘ └──────────┘ └────────────────────┘   │
└─────────────────────────────────────────────────────────────────────┘
```

### 3. Critérios para Extrair Serviço
1. Acoplamento baixo com WordPress
2. Carga alta/isolável
3. Beneficia-se de outra stack
4. Time dedicado

**Ordem Recomendada:** Matching IA → Chat → Notificações → Pagamentos → Agenda → Search → Demais

### 4. Comunicação entre Serviços

**Síncrona (REST/GraphQL):** Consultas CRUD, baixa latência
**Assíncrona (Eventos):** Notificações, cache, background, integrações

### 5. Padrões de Resiliência

| Padrão | Uso |
|--------|-----|
| Circuit Breaker | Gateway → Serviços |
| Retry with Backoff | Chamadas REST |
| Bulkhead | Thread pools separados |
| Saga | Transação distribuída |
| CQRS | Relatórios vs operações |
| Event Sourcing | Wallet, transações |

### 6. Kubernetes (Deployment Exemplo)

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: marketplace-service
spec:
  replicas: 3
  selector:
    matchLabels:
      app: marketplace
  template:
    metadata:
      labels:
        app: marketplace
    spec:
      containers:
      - name: marketplace
        image: consultoria/marketplace-service:latest
        ports:
        - containerPort: 3000
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
```

### 7. Segurança em Microserviços
- mTLS entre serviços
- JWT para comunicação externa
- API Gateway como único ponto de entrada
- Rate Limiting por serviço/cliente
- Service Mesh (Istio/Linkerd)
- Secrets Management (Vault/K8s Secrets)

### 8. Migração sem Downtime (Strangler Fig)

1. Feature flag rota para novo serviço
2. Processamento paralelo (novo + legado)
3. Comparar resultados
4. Migrar tráfego gradualmente (10% → 50% → 100%)
5. Remover código legado

### 9. Custos Estimados

| Fase | Infra/mês | Time | Total/mês |
|------|-----------|------|-----------|
| Fase 1 (MVP) | R$ 2.000 | 3 devs | R$ 35.000 |
| Fase 2 | R$ 5.000 | 6 devs | R$ 75.000 |
| Fase 3 | R$ 10.000 | 10 devs | R$ 140.000 |
| Fase 4 | R$ 25.000 | 15+ devs | R$ 250.000+ |

---

> **Fim da Documentação Consolidada**
>
> Este documento reúne toda a especificação da Plataforma de Consultoria SaaS.
> Consulte os arquivos individuais em `docs/` para detalhes específicos de cada área.
> Para o código-fonte do plugin, consulte `wp-content/plugins/consultoria-platform/`.
> Para o tema, consulte `wp-content/themes/consultoria-theme/`.
