# DIAGRAMAS UML

## 1. DIAGRAMA DE CASOS DE USO

```
┌─────────────────────────────────────────────────────────────────────────┐
│                    SISTEMA CONSULTORIA SAAS                             │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                         CLIENTE                                 │   │
│  │                                                                  │   │
│  │     ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐    │   │
│  │     │ Cadastrar    │  │ Comprar      │  │ Publicar         │    │   │
│  │     │ Conta        │  │ Pacote Horas │  │ Demanda          │    │   │
│  │     └──────┬───────┘  └──────┬───────┘  └────────┬─────────┘    │   │
│  │            │                 │                    │              │   │
│  │     ┌──────┴───────┐  ┌──────┴───────┐  ┌────────┴─────────┐    │   │
│  │     │ Avaliar      │  │ Aprovar      │  │ Escolher         │    │   │
│  │     │ Consultor    │  │ Entregas     │  │ Consultor        │    │   │
│  │     └──────────────┘  └──────────────┘  └──────────────────┘    │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                       CONSULTOR                                 │   │
│  │                                                                  │   │
│  │     ┌─────────────┐  ┌──────────────┐  ┌──────────────────┐    │   │
│  │     │ Completar   │  │ Enviar       │  │ Registrar        │    │   │
│  │     │ Onboarding  │  │ Proposta     │  │ Horas            │    │   │
│  │     └─────────────┘  └──────┬───────┘  └────────┬─────────┘    │   │
│  │                             │                    │              │   │
│  │                    ┌────────┴────────┐  ┌────────┴─────────┐    │   │
│  │                    │ Solicitar      │  │ Criar            │    │   │
│  │                    │ Saque          │  │ Milestones       │    │   │
│  │                    └─────────────────┘  └──────────────────┘    │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                       ADMINISTRADOR                             │   │
│  │                                                                  │   │
│  │   ┌──────────┐ ┌───────────┐ ┌──────────┐ ┌──────────┐        │   │
│  │   │ Aprovar  │ │ Gerenciar │ │ Gerenciar│ │ Visualizar│        │   │
│  │   │Consultor │ │ Planos    │ │ Saques   │ │Relatórios │        │   │
│  │   └──────────┘ └───────────┘ └──────────┘ └──────────┘        │   │
│  └──────────────────────────────────────────────────────────────────┘   │
│                                                                         │
│  ┌──────────────────────────────────────────────────────────────────┐   │
│  │                         SUPORTE                                │   │
│  │                                                                  │   │
│  │   ┌──────────┐ ┌───────────┐ ┌──────────┐                      │   │
│  │   │ Atender  │ │ Consultar │ │ Escalar  │                      │   │
│  │   │ Tickets  │ │ Pedidos   │ │ Chamados │                      │   │
│  │   └──────────┘ └───────────┘ └──────────┘                      │   │
│  └──────────────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────────────┘
```

## 2. DIAGRAMA DE SEQUÊNCIA — FLUXO DE CONTRATAÇÃO

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

## 3. DIAGRAMA DE CLASSES (DOMÍNIO PRINCIPAL)

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

## 4. DIAGRAMA DE ATIVIDADES — FLUXO DE PAGAMENTO

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

## 5. DIAGRAMA DE ESTADOS - PROJETO

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

## 6. FLUXOGRAMA — MATCHING IA

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
