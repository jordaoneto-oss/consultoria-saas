# PLANO DE DEVOPS E INFRAESTRUTURA

## 1. ARQUITETURA DE INFRAESTRUTURA (MVP)

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

## 2. AMBIENTES

| Ambiente | URL | Propósito |
|----------|-----|-----------|
| Desenvolvimento | dev.consultoriasaas.com.br | Desenvolvimento diário |
| Staging | staging.consultoriasaas.com.br | Homologação e testes |
| Produção | consultoriasaas.com.br | Produção |

## 3. CI/CD PIPELINE

### Ferramentas
- **GitHub Actions** (CI/CD)
- **Docker** (containerização)
- **SonarQube** (qualidade de código)
- **Sentry** (error tracking)

### Pipeline

```
[Push/PR] → [Lint] → [PHPUnit] → [SonarQube] → [Build] → [Deploy Staging] → [Tests] → [Deploy Prod]
```

### GitHub Actions Workflow

```yaml
name: CI/CD Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  quality:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
      - name: Lint
        run: php -l wp-content/plugins/consultoria-platform/
      - name: PHPUnit
        run: vendor/bin/phpunit
      - name: SonarQube
        run: sonar-scanner

  deploy-staging:
    needs: quality
    if: github.ref == 'refs/heads/develop'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Staging
        run: |
          rsync -avz --delete ./ user@staging:/var/www/html/
          ssh user@staging "wp plugin activate consultoria-platform"

  deploy-production:
    needs: quality
    if: github.ref == 'refs/heads/main'
    runs-on: ubuntu-latest
    steps:
      - name: Deploy to Production
        run: |
          rsync -avz --delete ./ user@production:/var/www/html/
          ssh user@production "wp plugin activate consultoria-platform"
```

## 4. ESTRATÉGIA DE BACKUP

| Item | Frequência | Retenção | Destino |
|------|------------|----------|---------|
| Banco MySQL | 6h | 30 dias | S3 + Local |
| Uploads | Diário | 7 dias | S3 |
| Plugins/Themes | A cada deploy | 3 versões | S3 |
| Configurações | Semanal | 3 meses | S3 |

### Comandos Backup

```bash
# Backup MySQL
mysqldump -u root -p consultoria | gzip > backup_$(date +%Y%m%d_%H%M%S).sql.gz

# Restore
gunzip < backup.sql.gz | mysql -u root -p consultoria
```

## 5. MONITORAMENTO E OBSERVABILIDADE

### Ferramentas (Fase 1 — Gratuitas)
- **Uptime Robot** — Monitoramento de uptime
- **New Relic Free Tier** — APM básico
- **Sentry** — Error tracking
- **WP Health** — Saúde do WordPress

### Ferramentas (Fase 2+)
- **Datadog** — APM completo
- **Grafana + Prometheus** — Métricas customizadas
- **ELK Stack** — Logs centralizados
- **PagerDuty** — Alertas

### Dashboard de Métricas (Grafana)

```
┌──────────────┬──────────────┬──────────────┬──────────────┐
│  Requests/s  │  P95 Latency │  Error Rate  │   CPU %      │
│     450      │    820ms     │    0.5%      │     45%      │
├──────────────┼──────────────┼──────────────┼──────────────┤
│  Memória %   │   Disk %     │  DB Conns    │  Redis Hit   │
│     62%      │     55%      │     23       │     92%      │
└──────────────┴──────────────┴──────────────┴──────────────┘
```

### Alertas Críticos

| Alerta | Threshold | Ação |
|--------|-----------|------|
| Site offline | 2 min | Notificar equipe |
| Erro 5xx | > 1% em 5 min | Rollback automático |
| CPU > 80% | 10 min | Escalar horizontalmente |
| DB Connection | > 80% | Aumentar pool |

## 6. ESCALABILIDADE

### Horizontal Scaling
- Web servers: Auto-scaling group (3-20 instâncias)
- Database: Read replicas para queries pesadas
- Redis: Cluster mode

### Cache Strategy
- **Page Cache:** Nginx FastCGI Cache / Cloudflare
- **Object Cache:** Redis (WP Redis)
- **Query Cache:** Transients API
- **CDN:** Cloudflare (assets estáticos)

### CDN Configuration (Cloudflare)
- Cache HTML: Bypass (dinâmico)
- Cache Assets: 1 ano
- Minify: HTML, CSS, JS
- Brotli: Ativado
- HTTP/3: Ativado
- Argo Smart Routing: Ativado

## 7. PROCESSO DE INCIDENTES

```
1. Detecção (monitoramento/alerta)
2. Classificação (Crítico/Alto/Médio/Baixo)
3. Resposta (Conter o problema)
4. Resolução (Corrigir causa raiz)
5. Post-mortem (Documentar e prevenir)
```

### SLAs Internos
- Crítico: 15 min para resposta, 2h para resolução
- Alto: 30 min para resposta, 4h para resolução
- Médio: 2h para resposta, 24h para resolução
- Baixo: 24h para resposta, 72h para resolução
