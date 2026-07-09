<?php

namespace Consultoria\Modules\Chat;

use Consultoria\Modules\BaseModule;

class ChatModule extends BaseModule {

    protected string $name = 'chat';

    public function init(): void {
        $service = new ChatService();
        $this->registerService('chat', $service);
    }
}

class ChatService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function getMessages(\WP_REST_Request $request): \WP_REST_Response {
        $projectId = (int) $request->get_param('project_id');
        $this->verifyProjectAccess($projectId);

        $before = $request->get_param('before');
        $limit = min(100, max(1, (int) ($request->get_param('limit') ?? 50)));

        $where = "project_id = %d";
        $params = [$projectId];

        if ($before) {
            $where .= " AND id < %d";
            $params[] = (int) $before;
        }

        $messages = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT m.*, u.display_name as sender_name, u.user_email as sender_email
             FROM {$this->getDb()->prefix}cp_messages m
             INNER JOIN {$this->getDb()->prefix}cp_users u ON u.id = m.sender_id
             WHERE {$where}
             ORDER BY m.created_at DESC
             LIMIT %d",
            array_merge($params, [$limit])
        ));

        // Mark as read
        $this->getDb()->query($this->getDb()->prepare(
            "UPDATE {$this->getDb()->prefix}cp_messages
             SET read_at = %s
             WHERE project_id = %d AND sender_id != %d AND read_at IS NULL",
            current_time('mysql'), $projectId, get_current_user_id()
        ));

        return \Consultoria\Helpers\Response::success([
            'messages' => array_reverse($messages),
        ]);
    }

    public function sendMessage(\WP_REST_Request $request): \WP_REST_Response {
        $projectId = (int) $request->get_param('project_id');
        $content = $request->get_param('content');
        $messageType = $request->get_param('message_type') ?? 'text';

        $this->verifyProjectAccess($projectId);

        $validator = new \Consultoria\Helpers\Validator();
        if (!$validator->validate(['content' => $content], ['content' => 'required'])) {
            return \Consultoria\Helpers\Response::validationError($validator->getErrors());
        }

        $data = [
            'project_id'    => $projectId,
            'sender_id'     => get_current_user_id(),
            'content'       => wp_kses_post($content),
            'message_type'  => $messageType,
            'created_at'    => current_time('mysql'),
        ];

        if ($request->has_param('file_url')) {
            $data['file_url'] = esc_url_raw($request->get_param('file_url'));
            $data['file_name'] = sanitize_text_field($request->get_param('file_name') ?? '');
        }

        if ($request->has_param('parent_id')) {
            $data['parent_id'] = (int) $request->get_param('parent_id');
        }

        $result = $this->getDb()->insert($this->getDb()->prefix . 'cp_messages', $data);
        if (!$result) {
            return \Consultoria\Helpers\Response::error('Erro ao enviar mensagem');
        }

        $messageId = $this->getDb()->insert_id;
        $message = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT m.*, u.display_name as sender_name
             FROM {$this->getDb()->prefix}cp_messages m
             INNER JOIN {$this->getDb()->prefix}cp_users u ON u.id = m.sender_id
             WHERE m.id = %d",
            $messageId
        ));

        // Trigger notification
        do_action('cp_message_sent', $projectId, $messageId, get_current_user_id());

        return \Consultoria\Helpers\Response::created(['message' => $message]);
    }

    private function verifyProjectAccess(int $projectId): void {
        global $wpdb;
        $userId = get_current_user_id();

        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.user_id as client_user_id, co.user_id as consultant_user_id
             FROM {$wpdb->prefix}cp_projects p
             LEFT JOIN {$wpdb->prefix}cp_clients c ON c.id = p.client_id
             LEFT JOIN {$wpdb->prefix}cp_consultants co ON co.id = p.consultant_id
             WHERE p.id = %d",
            $projectId
        ));

        if (!$project) {
            throw new \Consultoria\Exceptions\NotFoundException('Projeto não encontrado');
        }

        $canAccess = $userId === (int) $project->client_user_id
            || $userId === (int) $project->consultant_user_id
            || current_user_can('manage_options')
            || current_user_can('cp_support');

        if (!$canAccess) {
            throw new \Consultoria\Exceptions\ForbiddenException();
        }
    }
}
