# BACKLOG COMPLETO

## PRIORIZAÇÃO: MoSCoW

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

## USER STORIES

### EPICO: Core Platform Setup

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-001 | Como administrador, quero instalar o plugin da plataforma via WordPress, para que todas as funcionalidades fiquem disponíveis. | Plugin ativa sem erros, tabelas criadas, páginas configuradas automaticamente |
| US-002 | Como administrador, quero configurar as integrações (Stripe, Daily.co, etc) via painel, para que a plataforma funcione corretamente. | Formulário de configuração salva corretamente, conexões testadas |
| US-003 | Como administrador, quero gerenciar planos de serviço, para que clientes possam contratar pacotes de horas. | CRUD completo de planos, sincronia com WooCommerce |

### EPICO: Authentication & Roles

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-004 | Como usuário, quero me cadastrar como cliente ou consultor, para acessar a plataforma. | Registro com email, validação, escolha de perfil |
| US-005 | Como usuário, quero fazer login com email e senha, para acessar minha conta. | Autenticação JWT, refresh token, 2FA opcional |
| US-006 | Como consultor, quero completar meu onboarding (perfil, especialidades, certificações), para ficar disponível no marketplace. | Formulário multi-etapas, aprovação do admin |
| US-007 | Como administrador, quero aprovar ou rejeitar cadastros de consultores, para garantir qualidade. | Lista de pendências, aprovação com um clique |
| US-008 | Como administrador, quero bloquear/desbloquear usuários, para moderar a plataforma. | Ação imediata, notificação ao usuário |

### EPICO: Marketplace

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-009 | Como cliente, quero publicar uma demanda de consultoria, para receber propostas de consultores. | Formulário com categorias, descrição, orçamento estimado |
| US-010 | Como cliente, quero buscar consultores por especialidade, avaliação e preço, para encontrar o melhor profissional. | Filtros, busca fulltext, ordenação |
| US-011 | Como consultor, quero receber notificações de novas demandas compatíveis com meu perfil, para enviar propostas. | Matching básico por categoria, push/email |
| US-012 | Como consultor, quero enviar propostas para demandas abertas, para oferecer meus serviços. | Valor, horas estimadas, mensagem personalizada |
| US-013 | Como cliente, quero visualizar propostas recebidas e aceitar a melhor, para iniciar o projeto. | Comparativo de propostas, aceite com um clique |
| US-014 | Como cliente, quero escolher manualmente um consultor do marketplace, mesmo sem publicar demanda. | Busca, filtros, perfil, contratação direta |

### EPICO: Service Plans & Checkout

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-015 | Como cliente, quero visualizar os planos de horas disponíveis, para escolher o melhor para minha necessidade. | Página de planos com comparativo |
| US-016 | Como cliente, quero comprar um pacote de horas pagando com cartão, boleto ou pix, para começar a usar. | Integração Stripe, confirmação imediata |
| US-017 | Como cliente, quero ver meu saldo de horas restantes, para saber quanto ainda posso utilizar. | Dashboard, indicador no header |
| US-018 | Como cliente, quero recarregar meu saldo comprando outro pacote, para continuar utilizando. | Mesmo fluxo de checkout |

### EPICO: Wallet & Payments

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-019 | Como consultor, quero visualizar minha conta corrente com saldo disponível e bloqueado, para acompanhar meus ganhos. | Dashboard financeiro, valores atualizados |
| US-020 | Como consultor, quero solicitar saque do meu saldo disponível via PIX, para receber meus pagamentos. | Formulário de saque, aprovação admin/automática |
| US-021 | Como administrador, quero aprovar saques de consultores, para liberar os pagamentos. | Lista de saques pendentes, aprovação com transferência Stripe |
| US-022 | Como sistema, quero dividir automaticamente o pagamento entre plataforma e consultor via Stripe Connect, para garantir o fluxo financeiro. | Split automático, retenção em escrow |

### EPICO: Projects & Proposals

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-023 | Como cliente, quero acompanhar o andamento do meu projeto em tempo real, para saber o que está sendo entregue. | Timeline, milestones, horas consumidas |
| US-024 | Como consultor, quero criar marcos (milestones) para o projeto, para organizar as entregas. | CRUD de milestones, notificação ao cliente |
| US-025 | Como consultor, quero registrar horas trabalhadas em cada projeto, para que o cliente aprove. | Timer ou lançamento manual, aprovação do cliente |
| US-026 | Como cliente, quero aprovar ou rejeitar horas lançadas, para controlar o uso do meu pacote. | Lista de horas pendentes, aprovação/rejeição |

### EPICO: Chat

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-027 | Como cliente e consultor, quero conversar via chat dentro do projeto, para trocar informações rapidamente. | Mensagens de texto, imagens, arquivos, em tempo real |
| US-028 | Como usuário, quero receber notificações de novas mensagens, para não perder prazos. | Push notification, badge de não lidas |
| US-029 | Como usuário, quero ver status online e confirmação de leitura, para saber se a mensagem foi vista. | Indicadores de presença, ✓✓ |

### EPICO: Contracts

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-030 | Como cliente e consultor, quero assinar digitalmente o contrato de prestação de serviços, para formalizar o projeto. | Integração Docuseal, assinatura com token |
| US-031 | Como usuário, quero baixar o contrato assinado em PDF, para manter registro. | Geração de PDF, link para download |
| US-032 | Como sistema, quero gerar contrato automaticamente ao aceitar proposta, para agilizar o processo. | Template padronizado, dados preenchidos |

### EPICO: SLA

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-033 | Como administrador, quero configurar regras de SLA por categoria, para definir prazos de resposta e entrega. | CRUD de regras SLA |
| US-034 | Como sistema, quero monitorar o SLA de cada projeto, para disparar alertas de violação. | Relógio SLA, notificações de deadline |
| US-035 | Como sistema, quero escalonar automaticamente projetos com SLA violado, para que o admin tome ação. | Escalonamento para suporte/admin |

### EPICO: Dashboards

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-036 | Como cliente, quero um dashboard com meus projetos, horas, agenda e pagamentos, para ter visão geral. | Cards de resumo, gráficos, listas |
| US-037 | Como consultor, quero um dashboard com receita, projetos ativos, conta corrente e avaliações, para gerenciar minha carreira. | Indicadores financeiros, ranking, próximos passos |
| US-038 | Como administrador, quero um dashboard com GMV, receita, CAC, LTV, NPS e Churn, para tomar decisões estratégicas. | Gráficos, tabelas, exportação |

### EPICO: Gamification

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-039 | Como consultor, quero ganhar XP e subir de nível, para ser reconhecido na plataforma. | Pontuação automática, níveis visíveis |
| US-040 | Como consultor, quero conquistar badges (selos) por desempenho, para destacar meu perfil. | Badges automáticos, exibição no perfil |
| US-041 | Como consultor, quero aparecer no ranking da plataforma, para atrair mais clientes. | Ranking semanal/mensal, categorias |

### EPICO: Matching IA

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-042 | Como sistema, quero calcular score de matching entre demanda e consultores, para recomendar os melhores. | Algoritmo multi-critério, aprendizado contínuo |
| US-043 | Como cliente, quero receber recomendações automáticas de consultores ao publicar demanda, para escolher mais rápido. | Top 5 recomendados, score visível |
| US-044 | Como consultor, quero ser notificado apenas de demandas com alto score de matching, para otimizar meu tempo. | Notificação seletiva por score |

### EPICO: Affiliates

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-045 | Como usuário, quero me cadastrar como afiliado e gerar meu link único, para indicar clientes. | Geração de link, código, QR code |
| US-046 | Como afiliado, quero acompanhar cliques, conversões e comissões no dashboard, para monitorar resultados. | Dashboard de afiliado |
| US-047 | Como afiliado, quero receber comissão sobre vendas indicadas, para ser remunerado. | Comissão automática, pagamento mensal |

### EPICO: Cashback

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-048 | Como cliente, quero receber cashback ao comprar planos, para ter desconto em futuras contratações. | Percentual configurável, crédito automático |
| US-049 | Como cliente, quero utilizar meu cashback para comprar novos planos ou pagar consultorias, para economizar. | Aplicação no checkout |

### EPICO: Notifications

| ID | História | Critérios de Aceite |
|----|----------|-------------------|
| US-050 | Como usuário, quero receber notificações sobre eventos importantes (nova proposta, mensagem, pagamento), para não perder nada. | Email, push, notificação interna |
| US-051 | Como usuário, quero configurar quais notificações desejo receber, para controlar o que me é enviado. | Preferências de notificação |
