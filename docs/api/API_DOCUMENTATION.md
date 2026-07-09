# API REST — Documentação Completa

## Informações Gerais

- **Base URL:** `https://consultoriasaas.com.br/wp-json/consultoria/v1`
- **Formato:** JSON
- **Autenticação:** JWT Bearer Token
- **Timezone:** UTC

## Autenticação

### POST /auth/login
Login do usuário.

**Request:**
```json
{
    "email": "usuario@exemplo.com",
    "password": "123456"
}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "token": "eyJhbGciOiJIUzI1NiIs...",
        "refresh_token": "a1b2c3d4e5f6...",
        "user": {
            "id": 1,
            "email": "usuario@exemplo.com",
            "display_name": "João Silva",
            "role": "consultant"
        }
    }
}
```

### POST /auth/register
Registro de novo usuário.

**Request:**
```json
{
    "email": "usuario@exemplo.com",
    "password": "123456",
    "display_name": "João Silva",
    "role": "consultant"
}
```

### POST /auth/refresh
Renova o token JWT usando refresh token.

**Request:**
```json
{
    "refresh_token": "a1b2c3d4e5f6..."
}
```

### GET /auth/me
Retorna dados do usuário autenticado.

**Headers:** `Authorization: Bearer {token}`

---

## Consultores

### GET /consultants
Lista consultores disponíveis.

**Query Parameters:**
| Parâmetro | Tipo | Descrição |
|-----------|------|-----------|
| q | string | Termo de busca |
| category | string | Categoria |
| min_rate | float | Valor mínimo por hora |
| max_rate | float | Valor máximo por hora |
| min_rating | float | Rating mínimo |
| sort_by | string | Ordenação (rating, price_asc, price_desc, projects) |
| page | int | Número da página |
| per_page | int | Itens por página |

### GET /consultants/{id}
Retorna perfil completo do consultor.

### PUT /consultants/profile
Atualiza perfil do consultor autenticado.

### POST /consultants/expertise
Adiciona especialidade.

### POST /consultants/certifications
Adiciona certificação.

### PUT /consultants/availability
Atualiza disponibilidade semanal.

---

## Projetos

### GET /projects
Lista projetos do usuário autenticado.

### POST /projects
Cria novo projeto/demanda.

**Request:**
```json
{
    "title": "Implantação SAP S/4HANA",
    "description": "Precisamos de consultor para...",
    "category": "ERP",
    "subcategory": "SAP",
    "scope": "consultoria",
    "estimated_hours": 50,
    "budget": 8000.00
}
```

### GET /projects/{id}
Retorna detalhes do projeto.

### PUT /projects/{id}
Atualiza projeto.

### GET /projects/{id}/milestones
Lista marcos do projeto.

### POST /projects/{id}/milestones
Cria novo marco.

### GET /projects/{id}/time-entries
Lista horas lançadas.

### POST /projects/{id}/time-entries
Registra horas.

**Request:**
```json
{
    "hours": 4.5,
    "description": "Análise de requisitos",
    "date": "2026-07-09"
}
```

### GET /projects/{id}/deliverables
Lista entregas.

### POST /projects/{id}/deliverables
Registra entrega.

---

## Propostas

### POST /proposals
Envia proposta para projeto.

**Request:**
```json
{
    "project_id": 1,
    "value": 7500.00,
    "estimated_hours": 50,
    "message": "Tenho 12 anos de experiência em SAP...",
    "delivery_estimate": 30
}
```

### POST /proposals/{id}/accept
Aceita proposta (cliente).

### POST /proposals/{id}/reject
Rejeita proposta (cliente).

---

## Carteira

### GET /wallet
Retorna saldo da carteira.

### GET /wallet/transactions
Lista transações financeiras.

### POST /withdrawals
Solicita saque.

**Request:**
```json
{
    "amount": 1000.00,
    "pix_key": "joao@email.com",
    "pix_key_type": "email"
}
```

### GET /withdrawals
Lista saques.

### POST /withdrawals/{id}/approve
Aprova saque (admin).

### POST /withdrawals/{id}/reject
Rejeita saque (admin).

---

## Mensagens

### GET /projects/{project_id}/messages
Lista mensagens do projeto.

### POST /projects/{project_id}/messages
Envia mensagem.

**Request:**
```json
{
    "content": "Olá, tudo bem?",
    "message_type": "text"
}
```

---

## Agendamentos

### GET /appointments
Lista agendamentos.

### POST /appointments
Cria agendamento.

**Request:**
```json
{
    "project_id": 1,
    "title": "Revisão Semanal",
    "start_time": "2026-07-10T14:00:00",
    "end_time": "2026-07-10T15:00:00",
    "type": "videoconference"
}
```

### PUT /appointments/{id}
Atualiza agendamento.

---

## Avaliações

### POST /reviews
Cria avaliação.

**Request:**
```json
{
    "project_id": 1,
    "target_id": 5,
    "rating": 5,
    "quality": 5,
    "communication": 4,
    "deadline": 5,
    "comment": "Excelente profissional!"
}
```

### GET /consultants/{id}/reviews
Lista avaliações do consultor.

---

## Contratos

### POST /contracts/{id}/sign
Assina contrato digital.

### GET /contracts/{id}/download
Download do contrato.

---

## Tickets

### GET /tickets
Lista tickets de suporte.

### POST /tickets
Abre ticket.

### POST /tickets/{id}/replies
Responde ticket.

---

## Dashboard

### GET /dashboard/client
Dashboard do cliente.

### GET /dashboard/consultant
Dashboard do consultor.

### GET /dashboard/admin
Dashboard do admin (requer permissão).

---

## Marketplaces

### GET /marketplace/search
Busca consultores.

### GET /marketplace/categories
Lista categorias.

### GET /marketplace/matching/{project_id}
Retorna matching score para consultores.

---

## Webhooks

### Configuração

Webhooks são enviados via POST para URLs configuradas no admin.

### Eventos

| Evento | Descrição |
|--------|-----------|
| `order.completed` | Pedido concluído |
| `project.started` | Projeto iniciado |
| `project.completed` | Projeto concluído |
| `contract.signed` | Contrato assinado |
| `withdrawal.requested` | Saque solicitado |
| `withdrawal.completed` | Saque concluído |
| `payment.received` | Pagamento recebido |

### Payload Padrão

```json
{
    "event": "order.completed",
    "timestamp": "2026-07-09T12:00:00Z",
    "data": {
        "order_id": 123,
        "total": 6500.00,
        "status": "completed"
    }
}
```

## Códigos de Erro

| Código | Descrição |
|--------|-----------|
| 400 | Bad Request |
| 401 | Unauthorized |
| 403 | Forbidden |
| 404 | Not Found |
| 422 | Validation Error |
| 429 | Too Many Requests |
| 500 | Internal Server Error |
