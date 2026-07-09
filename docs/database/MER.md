# MODELO ENTIDADE-RELACIONAMENTO (MER)

## DIAGRAMA CONCEITUAL

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
        1:N ┌────────────────────────────────────────────┘ created_at       │
    ┌───────┴──────────┐                                │ updated_at       │
    │   ORDERS          │                                └──────────────────┘
    │───────────────────│
    │ id (PK)           │                     ┌────────────────────┐
    │ order_id (WC FK)  │                     │   CONTRACTS         │
    │ user_id (FK)      │                     │────────────────────│
    │ plan_id (FK)      │                     │ id (PK)            │
    │ status            │                     │ project_id (FK)    │
    │ total             │                     │ document_url       │
    │ hours             │                     │ signed_by_client   │
    │ hours_used        │                     │ signed_by_consult  │
    │ hours_remaining   │                     │ signed_at          │
    │ expires_at        │                     │ status             │
    │ created_at        │                     │ created_at         │
    │ updated_at        │                     └────────────────────┘
    └───────────────────┘

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
│ xp_next_level      │       │ type               │       │                  │
│ total_xp_earned    │       │ criteria           │  N:N  └──────────────────┘
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

## CONVENÇÕES

- **Prefixo das tabelas:** `cp_` (consultoria platform)
- **Chaves primárias:** `id` (BIGINT UNSIGNED, AUTO_INCREMENT)
- **Chaves estrangeiras:** `{tabela}_id` (BIGINT UNSIGNED)
- **Timestamps:** `created_at`, `updated_at` (DATETIME)
- **Status:** ENUM ou VARCHAR(20) com CHECK constraint
- **Engine:** InnoDB (transações, FK, row-level locking)
- **Charset:** utf8mb4
- **Collation:** utf8mb4_unicode_ci

## RELACIONAMENTOS PRINCIPAIS

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
