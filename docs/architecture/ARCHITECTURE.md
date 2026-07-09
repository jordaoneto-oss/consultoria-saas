# ARQUITETURA DA SOLUÇÃO

## 1. VISÃO GERAL

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

## 2. DECISÕES ARQUITETURAIS

### 2.1 WordPress como Framework Core
**Decisão:** Utilizar WordPress como base do MVP
**Justificativa:**
- Curva de aprendizado reduzida para o time
- Ecossistema maduro (WooCommerce, plugins)
- Baixo custo operacional inicial
- PHP 8+ oferece performance adequada
- Possibilidade de evolução progressiva

**Risco:** WordPress não é ideal para lógica de negócio complexa
**Mitigação:** Plugin próprio desacoplado, Clean Architecture, preparação para migração

### 2.2 WooCommerce apenas para Catálogo e Checkout
**Decisão:** WooCommerce gerencia apenas produtos (pacotes de horas) e checkout
**Justificativa:**
- WooCommerce já possui gateway Stripe, subscriptions, gerenciamento de pedidos
- Evita retrabalho com carrinho e fluxo de pagamento
- Toda regra de negócio fica no plugin próprio

### 2.3 Plugin Próprio com Clean Architecture
**Decisão:** Todo o domínio da plataforma em plugin separado
**Justificativa:**
- Desacoplamento total do WooCommerce e do tema
- Possibilidade de testar isoladamente
- Preparação para extração futura para microserviços

### 2.4 Stripe Connect
**Decisão:** Stripe Connect como única plataforma de pagamento
**Justificativa:**
- Split automático de pagamentos (plataforma + consultor)
- Onboarding de consultores via Stripe Express
- Escrow (retenção temporária) até conclusão do serviço
- Suporte a múltiplos países e moedas

### 2.5 API REST como Camada de Comunicação
**Decisão:** Toda comunicação frontend-backend via REST API
**Justificativa:**
- Preparação para frontend React/Vue/Flutter no futuro
- Independência entre camadas
- Versionamento de API

## 3. MÓDULOS DO PLUGIN

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

## 4. PADRÕES DE ARQUITETURA

### 4.1 Clean Architecture no Plugin

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

### 4.2 Repository Pattern
- Abstração completa do banco de dados
- Interfaces para cada repositório
- Implementação concreta usando $wpdb

### 4.3 Service Layer
- Serviços anêmicos (sem estado)
- Injeção de dependência via construtor
- Responsabilidade única

### 4.4 Event Driven
- Eventos internos para desacoplamento entre módulos
- Ex: `ContractSignedEvent` → dispara notificações, libera agenda, inicia SLA
- Webhooks para eventos externos (Stripe, etc)

## 5. SEGURANÇA

### 5.1 Autenticação
- JWT para API REST
- Refresh tokens
- OAuth2 para integrações externas
- 2FA opcional para consultores

### 5.2 Autorização
- Role-Based Access Control (RBAC)
- 4 perfis: admin, support, consultant, client
- Capabilities customizadas do WordPress

### 5.3 Proteções
- Rate limiting por IP e por usuário
- Input validation e sanitization
- Prepared statements em todas as queries
- Nonce para forms
- CORS configurado
- Headers de segurança (HSTS, CSP, X-Frame-Options)

## 6. CACHE E PERFORMANCE

### 6.1 Redis
- Cache de sessão
- Cache de queries frequentes
- Cache de objetos serializados
- Rate limiting counters
- Filas de processamento assíncrono

### 6.2 Cloudflare
- CDN para assets estáticos
- WAF (Web Application Firewall)
- Minificação automática
- HTTP/2 e HTTP/3
- Argo Smart Routing

## 7. BANCO DE DADOS

### 7.1 Estratégia
- MySQL 8+ / MariaDB 10.6+
- Tabelas customizadas no plugin (prefixo `cp_`)
- WooCommerce mantém tabelas próprias (`wp_posts`, `wp_postmeta`, `wp_woocommerce_*`)
- Índices estratégicos para busca e filtros

### 7.2 Tabelas Principais
Ver documento `docs/database/MER.md`

## 8. OBSERVABILIDADE

### 8.1 Logs
- Structured logging (JSON)
- Níveis: debug, info, warning, error, critical
- Logs centralizados via arquivos rotacionados

### 8.2 Monitoramento (Fase 2+)
- New Relic ou Datadog
- APM para WordPress
- RUM (Real User Monitoring)

## 9. ESTRATÉGIA DE EVOLUÇÃO

### Fase 1 — Monólito Modular (MVP)
WordPress + Plugin próprio + WooCommerce

### Fase 2 — Aplicativo Mobile
Flutter + Consumo da REST API

### Fase 3 — Extração de Serviços
- Módulo de Matching → Serviço Python/Node.js
- Chat → Serviço Node.js + WebSocket
- Notificações → Serviço Node.js

### Fase 4 — Microserviços
- WordPress apenas como CMS
- API Gateway (Kong/KrakenD)
- Serviços independentes em Docker/Kubernetes
- Mensageria com RabbitMQ/Kafka
- Deploy em AWS/Azure/GCP
