# GUIA DE EVOLUÇÃO PARA MICROSERVIÇOS

## 1. VISÃO GERAL

Estratégia de evolução gradual de um monólito modular (WordPress) para uma arquitetura de microserviços completa.

## 2. FASES DE EVOLUÇÃO

### Fase 1 — Monólito Modular (MVP)
**Duração:** 0-6 meses
**Arquitetura:**
- WordPress + Plugin próprio + WooCommerce
- Toda lógica em plugin único mas modular
- Cache com Redis
- CDN Cloudflare

**Neste estágio:**
- 37 tabelas no banco MySQL
- 13 módulos no plugin
- API REST para consumo externo
- Suporte a até 10.000 usuários simultâneos

### Fase 2 — Extração de Serviços Críticos
**Duração:** 6-12 meses
**Serviços a extrair primeiro:**

```
1. Serviço de Matching IA
   - Stack: Python/Node.js + ML
   - Motivo: Processamento intensivo, modelo ML próprio
   - Comunicação: REST API + Fila (RabbitMQ)

2. Serviço de Chat
   - Stack: Node.js + WebSocket
   - Motivo: Tempo real, conexões persistentes
   - Comunicação: WebSocket + REST

3. Serviço de Notificações
   - Stack: Node.js
   - Motivo: Múltiplos canais, fila de processamento
   - Comunicação: RabbitMQ + REST
```

### Fase 3 — Aplicativo Mobile
**Duração:** 6-9 meses
- App Flutter (iOS + Android)
- Consome APIs REST (agora independentes)
- Funcionalidades: Chat, Agenda, Dashboard, Notificações Push

### Fase 4 — Microserviços Plenos
**Duração:** 12-18 meses
**Arquitetura Final:**

```
┌─────────────────────────────────────────────────────────────────────┐
│                         API Gateway (Kong)                          │
│                    (Rate Limit, Auth, Routing, Caching)              │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│  │  Auth    │ │  Mktplce │ │ Payment  │ │  Chat    │ │  Matching│ │
│  │  Service │ │  Service │ │  Service │ │  Service │ │  Service │ │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘ └──────────┘ │
│                                                                     │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌──────────┐ │
│  │  Project │ │  Wallet  │ │  Notifs  │ │  SLA     │ │  Gamific │ │
│  │  Service │ │  Service │ │  Service │ │  Service │ │  Service  │ │
│  └──────────┘ └──────────┘ └──────────┘ └──────────┘ └──────────┘ │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────────────────────────────────────────────────────────┐   │
│  │          Message Broker (RabbitMQ / Kafka)                    │   │
│  │     (Eventos: order.created, payment.confirmed, etc)          │   │
│  └──────────────────────────────────────────────────────────────┘   │
│                                                                     │
├─────────────────────────────────────────────────────────────────────┤
│                                                                     │
│  ┌──────────┐ ┌──────────┐ ┌──────────┐ ┌────────────────────┐   │
│  │  MySQL   │ │  Redis   │ │ Elastic │ │  Object Storage    │   │
│  │ (sharded)│ │ (cluster)│ │ Search  │ │  (S3/MinIO)        │   │
│  └──────────┘ └──────────┘ └──────────┘ └────────────────────┘   │
│                                                                     │
└─────────────────────────────────────────────────────────────────────┘
```

## 3. DECISÕES DE EXTRAÇÃO

### Critérios para extrair um serviço
1. **Acoplamento baixo com WordPress** — O módulo usa poucos hooks do WP
2. **Carga alta/isolável** — Pode escalar independentemente
3. **Tecnologia diferente** — Beneficia-se de outra stack
4. **Time dedicado** — Módulo pode ter seu próprio squad

### Ordem de Extração Recomendada
```
1. Matching IA (processamento intensivo)
2. Chat (WebSocket, tempo real)
3. Notificações (múltiplos canais)
4. Pagamentos (segurança, PCI)
5. Agenda (sync calendário)
6. Search (Elasticsearch)
7. Demais serviços
```

## 4. COMUNICAÇÃO ENTRE SERVIÇOS

### Síncrona (REST/GraphQL)
- Consultas de dados atuais
- Operações CRUD simples
- Baixa latência necessária

### Assíncrona (Eventos)
- Notificações
- Atualizações de cache
- Processamento em background
- Integrações externas

### Schema de Eventos

```json
{
    "event": "order.completed",
    "version": "1.0",
    "id": "evt_123abc",
    "timestamp": "2026-07-09T12:00:00Z",
    "data": {
        "order_id": 456,
        "user_id": 789,
        "total": 6500.00,
        "plan": "ouro",
        "hours": 50
    },
    "metadata": {
        "trace_id": "trace_xyz",
        "source": "wordpress-service"
    }
}
```

## 5. PADRÕES DE RESILIÊNCIA

| Padrão | Descrição | Uso |
|--------|-----------|-----|
| Circuit Breaker | Evita chamadas a serviço falho | Gateway → Serviços |
| Retry with Backoff | Repetição exponencial | Chamadas REST |
| Bulkhead | Isolamento de recursos | Thread pools separados |
| Saga | Transação distribuída | Orquestração de pedidos |
| CQRS | Separação leitura/escrita | Relatórios vs operações |
| Event Sourcing | Histórico completo | Wallet, transações |
| SAGA Coreografada | Cada serviço publica evento | Fluxo de contratação |

## 6. KUBERNETES

### Cluster Setup
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
        env:
        - name: DB_HOST
          value: mysql-cluster
        - name: REDIS_HOST
          value: redis-cluster
        resources:
          requests:
            memory: "256Mi"
            cpu: "250m"
          limits:
            memory: "512Mi"
            cpu: "500m"
        livenessProbe:
          httpGet:
            path: /health
            port: 3000
          periodSeconds: 10
        readinessProbe:
          httpGet:
            path: /ready
            port: 3000
          periodSeconds: 5
---
apiVersion: v1
kind: Service
metadata:
  name: marketplace-service
spec:
  selector:
    app: marketplace
  ports:
  - port: 3000
    targetPort: 3000
```

### Helm Charts
Cada serviço terá seu próprio Helm chart para deploy simplificado.

## 7. SEGURANÇA EM MICROSERVIÇOS

- **mTLS** entre serviços
- **JWT** para comunicação externa
- **API Gateway** como único ponto de entrada
- **Rate Limiting** por serviço e por cliente
- **Service Mesh** (Istio/Linkerd) para observabilidade
- **Secrets Management** (Vault/K8s Secrets)

## 8. MIGRAÇÃO SEM DOWNTIME

### Estratégia Strangler Fig
1. Rota request para novo serviço via feature flag
2. Novo serviço processa em paralelo com o legado
3. Comparar resultados
4. Migrar tráfego gradualmente (10% → 50% → 100%)
5. Remover código legado

### Exemplo: Migração do Matching
```
Semana 1-2: Implementar serviço Python em paralelo
Semana 3-4: Rota 10% do tráfego para o novo serviço
Semana 5:   Comparar scores, ajustar algoritmo
Semana 6:   Rota 50% do tráfego
Semana 7:   Rota 100%
Semana 8:   Remover módulo legado do WordPress
```

## 9. CUSTOS ESTIMADOS

| Fase | Infra/mês | Time | Total/mês |
|------|-----------|------|-----------|
| Fase 1 (MVP) | R$ 2.000 | 3 devs | R$ 35.000 |
| Fase 2 | R$ 5.000 | 6 devs | R$ 75.000 |
| Fase 3 | R$ 10.000 | 10 devs | R$ 140.000 |
| Fase 4 | R$ 25.000 | 15+ devs | R$ 250.000+ |
