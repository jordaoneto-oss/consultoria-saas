# PLANO DE TESTES

## 1. ESTRATÉGIA

### Níveis de Teste
1. **Unitário** — Funções e métodos isolados (PHPUnit)
2. **Integração** — Comunicação entre módulos e banco
3. **Funcional** — Fluxos completos do usuário
4. **Aceitação** — Validação com stakeholders
5. **Performance** — Carga e estresse
6. **Segurança** — OWASP Top 10

### Ferramentas
- **PHPUnit** — Testes unitários e de integração
- **WP_Mock** — Mocking de funções WordPress
- **Selenium/Cypress** — Testes E2E (futuro)
- **Lighthouse** — Performance web
- **OWASP ZAP** — Segurança

## 2. TESTES UNITÁRIOS

### Estrutura
```
tests/
├── Unit/
│   ├── Helpers/
│   │   ├── ValidatorTest.php
│   │   ├── FunctionsTest.php
│   │   └── ResponseTest.php
│   ├── Services/
│   │   ├── AuthServiceTest.php
│   │   ├── WalletServiceTest.php
│   │   └── MarketplaceServiceTest.php
│   └── Models/
│       └── UserTest.php
├── Integration/
│   ├── Database/
│   │   └── SchemaTest.php
│   ├── Api/
│   │   ├── AuthEndpointTest.php
│   │   └── ProjectEndpointTest.php
│   └── Modules/
│       └── CashbackTest.php
└── Feature/
    ├── MarketplaceFlowTest.php
    └── PaymentFlowTest.php
```

### Cobertura Mínima
- Services: 90%
- Helpers: 95%
- Controllers: 85%
- Models: 90%

## 3. CASOS DE TESTE FUNCIONAIS

### Regressão — Fluxos Críticos

| ID | Caso de Teste | Pré-condição | Passos | Resultado Esperado |
|----|---------------|--------------|--------|-------------------|
| FT-01 | Registro Cliente | Não logado | 1. Preencher formulário 2. Enviar | Conta criada, email de confirmação |
| FT-02 | Registro Consultor | Não logado | 1. Preencher formulário 2. Enviar | Conta criada, status pending |
| FT-03 | Login | Usuário existe | 1. Email+senha 2. Submit | Token JWT gerado |
| FT-04 | Compra Pacote | Cliente logado | 1. Escolher plano 2. Checkout 3. Pagar | Pedido criado, horas creditadas |
| FT-05 | Publicar Demanda | Cliente logado | 1. Formulário 2. Submit | Projeto criado, status 'open' |
| FT-06 | Enviar Proposta | Consultor logado | 1. Ver demanda 2. Escrever 3. Enviar | Proposta registrada |
| FT-07 | Aceitar Proposta | Cliente logado | 1. Ver proposta 2. Aceitar | Contrato gerado, status 'in_progress' |
| FT-08 | Assinar Contrato | Ambos logados | 1. Clicar em assinar | Contrato signed |
| FT-09 | Registrar Horas | Consultor logado | 1. Preencher 2. Enviar | Horas pendentes aprovação |
| FT-10 | Aprovar Horas | Cliente logado | 1. Ver horas 2. Aprovar | Pagamento liberado |
| FT-11 | Solicitar Saque | Consultor logado | 1. Valor+PIX 2. Enviar | Saque pending |
| FT-12 | Aprovar Saque | Admin logado | 1. Ver 2. Aprovar | Transferência realizada |
| FT-13 | Chat Mensagem | Ambos logados | 1. Digitar 2. Enviar | Mensagem aparece em tempo real |
| FT-14 | Agendar Reunião | Qualquer usuário | 1. Escolher data 2. Salvar | Appointment criado |
| FT-15 | Avaliar Projeto | Cliente logado | 1. Nota+comentário 2. Enviar | Review registrada |

## 4. TESTES DE SEGURANÇA

### Checklist OWASP

| ID | Vulnerabilidade | Teste |
|----|----------------|-------|
| S-01 | SQL Injection | Testar inputs com ' OR 1=1 |
| S-02 | XSS | Injetar <script>alert(1)</script> |
| S-03 | CSRF | Verificar nonce em todos os forms |
| S-04 | Broken Auth | Tentar acessar sem token |
| S-05 | Sensitive Data | Verificar criptografia de senhas |
| S-06 | Rate Limiting | Enviar 1000 requisições em 1 min |
| S-07 | JWT Security | Tentar usar token expirado |
| S-08 | Role Escalation | Cliente tentar rota de admin |
| S-09 | IDOR | Cliente tentar ver projeto alheio |
| S-10 | File Upload | Upload de arquivo malicioso |

## 5. TESTES DE CARGA

### Cenários

| Cenário | Usuários Concorrentes | Duração | Métrica Alvo |
|---------|----------------------|---------|--------------|
| Pico de login | 1.000 | 5 min | < 2s resposta |
| Marketplace search | 500 | 10 min | < 1s resposta |
| Checkout simultâneo | 200 | 5 min | 100% completados |
| Chat em massa | 1.000 conexões | 10 min | < 500ms latência |

### Métricas
- **Response Time:** p95 < 2s
- **Throughput:** > 100 req/s
- **Error Rate:** < 1%
- **CPU:** < 70%
- **Memory:** < 80%

## 6. RELATÓRIO DE TESTES

Formato padrão:

```
------------------------------------------------------------------
TESTE: FT-04 - Compra Pacote
STATUS: PASSED / FAILED / BLOCKED
DATA: 2026-07-09
RESPONSÁVEL: [Nome]
TEMPO: 5 min
OBSERVAÇÕES: [Se falhou, descrever o erro]
------------------------------------------------------------------
```
