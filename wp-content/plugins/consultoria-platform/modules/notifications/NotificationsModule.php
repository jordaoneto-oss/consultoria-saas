<?php

namespace Consultoria\Modules\Notifications;

use Consultoria\Modules\BaseModule;

class NotificationsModule extends BaseModule {

    protected string $name = 'notifications';

    public function init(): void {
        $service = new NotificationService();
        $this->registerService('notification', $service);

        $this->addAction('cp_send_notification', [$service, 'send'], 10, 3);
        $this->addAction('cp_message_sent', [$service, 'onNewMessage'], 10, 3);
        $this->addAction('cp_proposal_received', [$service, 'onNewProposal'], 10, 2);
        $this->addAction('cp_appointment_created', [$service, 'onNewAppointment'], 10, 1);

        $this->addAction('cp_process_notification_queue', [$service, 'processQueue']);

        add_filter('cp_shortcodes', function ($shortcodes) {
            $shortcodes['cp_notifications'] = [$this, 'renderNotifications'];
            return $shortcodes;
        });
    }

    public function renderNotifications(): string {
        if (!is_user_logged_in()) return '';
        ob_start();
        include CP_PLUGIN_DIR . 'modules/notifications/templates/notifications.php';
        return ob_get_clean();
    }
}

class NotificationService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function getNotifications(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $page = max(1, (int) ($request->get_param('page') ?? 1));
        $perPage = min(50, max(1, (int) ($request->get_param('per_page') ?? 20)));
        $offset = ($page - 1) * $perPage;

        $total = (int) $this->getDb()->get_var($this->getDb()->prepare(
            "SELECT COUNT(*) FROM {$this->getDb()->prefix}cp_notifications WHERE user_id = %d",
            $userId
        ));

        $notifications = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_notifications WHERE user_id = %d ORDER BY created_at DESC LIMIT %d OFFSET %d",
            $userId, $perPage, $offset
        ));

        $unreadCount = (int) $this->getDb()->get_var($this->getDb()->prepare(
            "SELECT COUNT(*) FROM {$this->getDb()->prefix}cp_notifications WHERE user_id = %d AND read_at IS NULL",
            $userId
        ));

        return \Consultoria\Helpers\Response::success([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    public function markAsRead(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $notificationId = (int) $request->get_param('id');

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_notifications',
            ['read_at' => current_time('mysql')],
            ['id' => $notificationId, 'user_id' => $userId]
        );

        return \Consultoria\Helpers\Response::success(['message' => 'Notificação marcada como lida']);
    }

    public function markAllRead(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();

        $this->getDb()->query($this->getDb()->prepare(
            "UPDATE {$this->getDb()->prefix}cp_notifications SET read_at = %s WHERE user_id = %d AND read_at IS NULL",
            current_time('mysql'), $userId
        ));

        return \Consultoria\Helpers\Response::success(['message' => 'Todas notificações marcadas como lidas']);
    }

    public function send(int $userId, string $type, array $data): void {
        $channels = ['internal'];

        // Determine channels based on user preferences
        $userPrefs = get_user_meta($userId, 'cp_notification_prefs', true) ?: [];
        if (!empty($userPrefs)) {
            $channels = $userPrefs[$type] ?? ['internal'];
        }

        $this->getDb()->insert($this->getDb()->prefix . 'cp_notifications', [
            'user_id'        => $userId,
            'type'           => $type,
            'title'          => $data['title'] ?? '',
            'message'        => $data['message'] ?? '',
            'reference_type' => $data['reference_type'] ?? null,
            'reference_id'   => $data['reference_id'] ?? null,
            'sent_via'       => json_encode($channels),
            'created_at'     => current_time('mysql'),
        ]);

        // Send email if enabled
        if (in_array('email', $channels)) {
            $this->sendEmail($userId, $data['title'] ?? '', $data['message'] ?? '');
        }
    }

    public function sendEmail(int $userId, string $subject, string $message): void {
        $user = get_userdata($userId);
        if (!$user) return;

        $headers = ['Content-Type: text/html; charset=UTF-8'];
        $htmlMessage = wpautop($message);

        wp_mail($user->user_email, $subject, $htmlMessage, $headers);
    }

    public function onNewMessage(int $projectId, int $messageId, int $senderId): void {
        global $wpdb;

        // Get project participants
        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.user_id as client_user_id, co.user_id as consultant_user_id
             FROM {$wpdb->prefix}cp_projects p
             INNER JOIN {$wpdb->prefix}cp_clients c ON c.id = p.client_id
             INNER JOIN {$wpdb->prefix}cp_consultants co ON co.id = p.consultant_id
             WHERE p.id = %d",
            $projectId
        ));

        if (!$project) return;

        $recipientIds = [$project->client_user_id, $project->consultant_user_id];
        $sender = get_userdata($senderId);

        foreach ($recipientIds as $recipientId) {
            if ((int) $recipientId === $senderId) continue;

            $this->send($recipientId, 'new_message', [
                'title'          => "Nova mensagem de {$sender->display_name}",
                'message'        => "Você recebeu uma nova mensagem no projeto {$project->title}",
                'reference_type' => 'project',
                'reference_id'   => $projectId,
            ]);
        }
    }

    public function onNewProposal(int $projectId, int $proposalId): void {
        global $wpdb;

        $project = $wpdb->get_row($wpdb->prepare(
            "SELECT p.*, c.user_id as client_user_id
             FROM {$wpdb->prefix}cp_projects p
             INNER JOIN {$wpdb->prefix}cp_clients c ON c.id = p.client_id
             WHERE p.id = %d",
            $projectId
        ));

        if (!$project) return;

        $this->send($project->client_user_id, 'new_proposal', [
            'title'          => 'Nova proposta recebida',
            'message'        => "Você recebeu uma nova proposta para o projeto {$project->title}",
            'reference_type' => 'project',
            'reference_id'   => $projectId,
        ]);
    }

    public function onNewAppointment(int $appointmentId): void {
        global $wpdb;

        $appointment = $wpdb->get_row($wpdb->prepare(
            "SELECT a.*, c.user_id as client_user_id, co.user_id as consultant_user_id
             FROM {$wpdb->prefix}cp_appointments a
             INNER JOIN {$wpdb->prefix}cp_clients c ON c.id = a.client_id
             INNER JOIN {$wpdb->prefix}cp_consultants co ON co.id = a.consultant_id
             WHERE a.id = %d",
            $appointmentId
        ));

        if (!$appointment) return;

        $this->send($appointment->client_user_id, 'new_appointment', [
            'title'          => 'Nova reunião agendada',
            'message'        => "Uma reunião foi agendada: {$appointment->title} em " . date('d/m/Y H:i', strtotime($appointment->start_time)),
            'reference_type' => 'appointment',
            'reference_id'   => $appointmentId,
        ]);

        $this->send($appointment->consultant_user_id, 'new_appointment', [
            'title'          => 'Nova reunião agendada',
            'message'        => "Uma reunião foi agendada: {$appointment->title} em " . date('d/m/Y H:i', strtotime($appointment->start_time)),
            'reference_type' => 'appointment',
            'reference_id'   => $appointmentId,
        ]);
    }

    public function processQueue(): void {
        // Process pending notifications
        global $wpdb;
        $pending = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}cp_notifications WHERE sent_via LIKE '%email%' AND read_at IS NULL AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR) LIMIT 50"
        );

        foreach ($pending as $notification) {
            $this->sendEmail($notification->user_id, $notification->title, $notification->message);
        }
    }
}
