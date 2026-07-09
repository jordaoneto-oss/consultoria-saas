<?php

namespace Consultoria\Modules\Tickets;

use Consultoria\Modules\BaseModule;

class TicketsModule extends BaseModule {

    protected string $name = 'tickets';

    public function init(): void {
        $service = new TicketService();
        $this->registerService('ticket', $service);

        $this->addAction('cp_ticket_created', [$service, 'onTicketCreated'], 10, 1);
        $this->addAction('cp_ticket_resolved', [$service, 'onTicketResolved'], 10, 1);

        add_filter('cp_shortcodes', function ($shortcodes) {
            $shortcodes['cp_tickets'] = [$this, 'renderTickets'];
            return $shortcodes;
        });
    }

    public function renderTickets(): string {
        if (!is_user_logged_in()) return '';
        ob_start();
        $templatePath = CP_PLUGIN_DIR . 'modules/tickets/templates/tickets.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        }
        return ob_get_clean();
    }
}

class TicketService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function getTickets(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $role = $this->getUserRole();
        $page = max(1, (int) ($request->get_param('page') ?? 1));
        $perPage = min(50, max(1, (int) ($request->get_param('per_page') ?? 20)));
        $offset = ($page - 1) * $perPage;
        $status = $request->get_param('status') ?? '';
        $priority = $request->get_param('priority') ?? '';

        $where = [];
        $params = [];

        if ($role === 'client' || $role === 'consultant') {
            $where[] = 't.user_id = %d';
            $params[] = $userId;
        }

        if (!empty($status)) {
            $where[] = 't.status = %s';
            $params[] = $status;
        }

        if (!empty($priority)) {
            $where[] = 't.priority = %s';
            $params[] = $priority;
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $total = (int) $this->getDb()->get_var($this->getDb()->prepare(
            "SELECT COUNT(*) FROM {$this->getDb()->prefix}cp_tickets t {$whereClause}",
            $params
        ));

        $tickets = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT t.*, u.display_name as user_name, a.display_name as assigned_name
             FROM {$this->getDb()->prefix}cp_tickets t
             LEFT JOIN {$this->getDb()->prefix}cp_users u ON u.id = t.user_id
             LEFT JOIN {$this->getDb()->prefix}cp_users a ON a.id = t.assigned_to
             {$whereClause}
             ORDER BY 
                CASE t.priority 
                    WHEN 'urgent' THEN 0 
                    WHEN 'high' THEN 1 
                    WHEN 'medium' THEN 2 
                    WHEN 'low' THEN 3 
                END,
                t.created_at DESC
             LIMIT %d OFFSET %d",
            array_merge($params, [$perPage, $offset])
        ));

        return \Consultoria\Helpers\Response::paginated($tickets, $total, $page, $perPage);
    }

    public function createTicket(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $subject = sanitize_text_field($request->get_param('subject'));
        $description = sanitize_textarea_field($request->get_param('description'));
        $category = sanitize_text_field($request->get_param('category') ?? '');
        $priority = sanitize_text_field($request->get_param('priority') ?? 'medium');

        if (empty($subject) || empty($description)) {
            return \Consultoria\Helpers\Response::error('Assunto e descrição são obrigatórios.', 422);
        }

        $validPriorities = ['low', 'medium', 'high', 'urgent'];
        if (!in_array($priority, $validPriorities)) {
            $priority = 'medium';
        }

        $this->getDb()->insert($this->getDb()->prefix . 'cp_tickets', [
            'user_id'     => $userId,
            'subject'     => $subject,
            'description' => $description,
            'category'    => $category,
            'priority'    => $priority,
            'status'      => 'open',
            'created_at'  => current_time('mysql'),
        ]);

        $ticketId = $this->getDb()->insert_id;

        do_action('cp_ticket_created', $ticketId);

        return \Consultoria\Helpers\Response::success([
            'ticket_id' => $ticketId,
            'message'   => 'Ticket criado com sucesso.',
        ], 201);
    }

    public function replyTicket(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $ticketId = (int) $request->get_param('id');
        $content = sanitize_textarea_field($request->get_param('content'));
        $isInternal = (bool) ($request->get_param('is_internal') ?? false);

        if (empty($content)) {
            return \Consultoria\Helpers\Response::error('Conteúdo da resposta é obrigatório.', 422);
        }

        $ticket = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_tickets WHERE id = %d",
            $ticketId
        ));

        if (!$ticket) {
            return \Consultoria\Helpers\Response::error('Ticket não encontrado.', 404);
        }

        if (!$this->canAccessTicket($ticket, $userId)) {
            return \Consultoria\Helpers\Response::error('Sem permissão para responder este ticket.', 403);
        }

        $this->getDb()->insert($this->getDb()->prefix . 'cp_ticket_replies', [
            'ticket_id'   => $ticketId,
            'user_id'     => $userId,
            'content'     => $content,
            'is_internal' => $isInternal ? 1 : 0,
            'created_at'  => current_time('mysql'),
        ]);

        $newStatus = $this->determineNewStatus($ticket->status, $userId, $isInternal);
        if ($newStatus !== $ticket->status) {
            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_tickets',
                ['status' => $newStatus, 'updated_at' => current_time('mysql')],
                ['id' => $ticketId]
            );
        }

        return \Consultoria\Helpers\Response::success([
            'reply_id' => $this->getDb()->insert_id,
            'status'   => $newStatus,
            'message'  => 'Resposta registrada.',
        ]);
    }

    public function getTicketDetail(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $ticketId = (int) $request->get_param('id');

        $ticket = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT t.*, u.display_name as user_name, a.display_name as assigned_name
             FROM {$this->getDb()->prefix}cp_tickets t
             LEFT JOIN {$this->getDb()->prefix}cp_users u ON u.id = t.user_id
             LEFT JOIN {$this->getDb()->prefix}cp_users a ON a.id = t.assigned_to
             WHERE t.id = %d",
            $ticketId
        ));

        if (!$ticket) {
            return \Consultoria\Helpers\Response::error('Ticket não encontrado.', 404);
        }

        if (!$this->canAccessTicket($ticket, $userId)) {
            return \Consultoria\Helpers\Response::error('Sem permissão.', 403);
        }

        $replies = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT r.*, u.display_name as user_name
             FROM {$this->getDb()->prefix}cp_ticket_replies r
             LEFT JOIN {$this->getDb()->prefix}cp_users u ON u.id = r.user_id
             WHERE r.ticket_id = %d
             " . ($this->isSupportOrAdmin() ? '' : "AND r.is_internal = 0") . "
             ORDER BY r.created_at ASC",
            $ticketId
        ));

        return \Consultoria\Helpers\Response::success([
            'ticket'  => $ticket,
            'replies' => $replies,
        ]);
    }

    public function updateTicketStatus(\WP_REST_Request $request): \WP_REST_Response {
        $ticketId = (int) $request->get_param('id');
        $status = sanitize_text_field($request->get_param('status'));
        $userId = get_current_user_id();

        $validStatuses = ['open', 'in_progress', 'waiting_client', 'waiting_support', 'resolved', 'closed'];
        if (!in_array($status, $validStatuses)) {
            return \Consultoria\Helpers\Response::error('Status inválido.', 422);
        }

        $ticket = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_tickets WHERE id = %d",
            $ticketId
        ));

        if (!$ticket) {
            return \Consultoria\Helpers\Response::error('Ticket não encontrado.', 404);
        }

        $data = [
            'status'     => $status,
            'updated_at' => current_time('mysql'),
        ];

        if (in_array($status, ['resolved', 'closed'])) {
            $data['closed_at'] = current_time('mysql');
            $data['closed_by'] = $userId;
        }

        if ($status === 'in_progress' && !$ticket->assigned_to) {
            $data['assigned_to'] = $userId;
        }

        $this->getDb()->update($this->getDb()->prefix . 'cp_tickets', $data, ['id' => $ticketId]);

        if ($status === 'resolved') {
            do_action('cp_ticket_resolved', $ticketId);
        }

        return \Consultoria\Helpers\Response::success(['message' => 'Status atualizado.']);
    }

    public function onTicketCreated(int $ticketId): void {
        $ticket = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT t.*, u.display_name as user_name
             FROM {$this->getDb()->prefix}cp_tickets t
             LEFT JOIN {$this->getDb()->prefix}cp_users u ON u.id = t.user_id
             WHERE t.id = %d",
            $ticketId
        ));

        if (!$ticket) return;

        do_action('cp_send_notification', 1, 'new_ticket', [
            'title'          => "Novo ticket: {$ticket->subject}",
            'message'        => "{$ticket->user_name} abriu um novo ticket de prioridade {$ticket->priority}",
            'reference_type' => 'ticket',
            'reference_id'   => $ticketId,
        ]);
    }

    public function onTicketResolved(int $ticketId): void {
        $ticket = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_tickets WHERE id = %d",
            $ticketId
        ));

        if (!$ticket) return;

        do_action('cp_send_notification', $ticket->user_id, 'ticket_resolved', [
            'title'          => "Ticket resolvido: {$ticket->subject}",
            'message'        => 'Seu ticket foi resolvido. Avalie o atendimento.',
            'reference_type' => 'ticket',
            'reference_id'   => $ticketId,
        ]);
    }

    private function canAccessTicket(object $ticket, int $userId): bool {
        if ($this->isSupportOrAdmin()) return true;
        return (int) $ticket->user_id === $userId;
    }

    private function isSupportOrAdmin(): bool {
        $user = wp_get_current_user();
        if (!$user) return false;
        return in_array('administrator', $user->roles) || in_array('cp_support', $user->roles);
    }

    private function determineNewStatus(string $currentStatus, int $userId, bool $isInternal): string {
        if ($currentStatus === 'open' && !$isInternal) return 'in_progress';
        if ($currentStatus === 'waiting_client' && !$isInternal) return 'in_progress';
        if ($currentStatus === 'waiting_support' && $isInternal) return 'in_progress';
        return $currentStatus;
    }

    private function getUserRole(): string {
        $user = wp_get_current_user();
        if (!$user) return 'guest';
        if (in_array('administrator', $user->roles)) return 'admin';
        if (in_array('cp_support', $user->roles)) return 'support';
        if (in_array('cp_consultant', $user->roles)) return 'consultant';
        return 'client';
    }
}
