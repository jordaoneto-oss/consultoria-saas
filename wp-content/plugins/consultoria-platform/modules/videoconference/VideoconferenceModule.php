<?php

namespace Consultoria\Modules\Videoconference;

use Consultoria\Modules\BaseModule;

class VideoconferenceModule extends BaseModule {

    protected string $name = 'videoconference';

    public function init(): void {
        $service = new VideoconferenceService();
        $this->registerService('videoconference', $service);

        $this->addAction('cp_appointment_created', [$service, 'createRoomForAppointment'], 10, 1);
        $this->addAction('cp_before_appointment_start', [$service, 'sendMeetingReminder'], 10, 1);

        add_filter('cp_shortcodes', function ($shortcodes) {
            $shortcodes['cp_videoconference'] = [$this, 'renderVideoRoom'];
            return $shortcodes;
        });
    }

    public function renderVideoRoom(): string {
        if (!is_user_logged_in()) return '';
        ob_start();
        $templatePath = CP_PLUGIN_DIR . 'modules/videoconference/templates/room.php';
        if (file_exists($templatePath)) {
            include $templatePath;
        }
        return ob_get_clean();
    }
}

class VideoconferenceService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function createRoom(\WP_REST_Request $request): \WP_REST_Response {
        $appointmentId = (int) $request->get_param('appointment_id');
        $provider = sanitize_text_field($request->get_param('provider') ?? 'daily');

        $appointment = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_appointments WHERE id = %d",
            $appointmentId
        ));

        if (!$appointment) {
            return \Consultoria\Helpers\Response::error('Agendamento não encontrado.', 404);
        }

        $roomData = $this->createRoomWithProvider($provider, $appointment);

        if (!$roomData) {
            return \Consultoria\Helpers\Response::error('Erro ao criar sala de videoconferência.', 500);
        }

        $this->getDb()->insert($this->getDb()->prefix . 'cp_video_sessions', [
            'appointment_id' => $appointmentId,
            'provider'       => $provider,
            'session_id'     => $roomData['session_id'],
            'room_url'       => $roomData['room_url'],
            'status'         => 'created',
            'created_at'     => current_time('mysql'),
        ]);

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_appointments',
            ['meeting_url' => $roomData['room_url'], 'meeting_provider' => $provider, 'meeting_id' => $roomData['session_id']],
            ['id' => $appointmentId]
        );

        return \Consultoria\Helpers\Response::success([
            'room_url'  => $roomData['room_url'],
            'session_id' => $roomData['session_id'],
        ], 201);
    }

    public function getRoom(\WP_REST_Request $request): \WP_REST_Response {
        $sessionId = (int) $request->get_param('id');

        $session = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_video_sessions WHERE id = %d",
            $sessionId
        ));

        if (!$session) {
            return \Consultoria\Helpers\Response::error('Sessão não encontrada.', 404);
        }

        return \Consultoria\Helpers\Response::success(['session' => $session]);
    }

    public function endRoom(\WP_REST_Request $request): \WP_REST_Response {
        $sessionId = (int) $request->get_param('id');

        $this->getDb()->update(
            $this->getDb()->prefix . 'cp_video_sessions',
            [
                'status'     => 'ended',
                'updated_at' => current_time('mysql'),
            ],
            ['id' => $sessionId]
        );

        return \Consultoria\Helpers\Response::success(['message' => 'Sessão encerrada.']);
    }

    public function createRoomForAppointment(int $appointmentId): void {
        $appointment = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_appointments WHERE id = %d",
            $appointmentId
        ));

        if (!$appointment || !empty($appointment->meeting_url)) return;

        $provider = 'daily';
        $roomData = $this->createRoomWithProvider($provider, $appointment);

        if ($roomData) {
            $this->getDb()->insert($this->getDb()->prefix . 'cp_video_sessions', [
                'appointment_id' => $appointmentId,
                'provider'       => $provider,
                'session_id'     => $roomData['session_id'],
                'room_url'       => $roomData['room_url'],
                'status'         => 'created',
                'created_at'     => current_time('mysql'),
            ]);

            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_appointments',
                ['meeting_url' => $roomData['room_url'], 'meeting_provider' => $provider, 'meeting_id' => $roomData['session_id']],
                ['id' => $appointmentId]
            );
        }
    }

    public function sendMeetingReminder(int $appointmentId): void {
        $appointment = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT a.*, c.user_id as client_user_id, co.user_id as consultant_user_id
             FROM {$this->getDb()->prefix}cp_appointments a
             INNER JOIN {$this->getDb()->prefix}cp_clients c ON c.id = a.client_id
             INNER JOIN {$this->getDb()->prefix}cp_consultants co ON co.id = a.consultant_id
             WHERE a.id = %d",
            $appointmentId
        ));

        if (!$appointment || empty($appointment->meeting_url)) return;

        $message = "Sua reunião começa em breve: {$appointment->title}<br>Acesse: <a href='{$appointment->meeting_url}'>{$appointment->meeting_url}</a>";

        do_action('cp_send_notification', $appointment->client_user_id, 'meeting_reminder', [
            'title'          => "Reunião em breve: {$appointment->title}",
            'message'        => $message,
            'reference_type' => 'appointment',
            'reference_id'   => $appointmentId,
        ]);

        do_action('cp_send_notification', $appointment->consultant_user_id, 'meeting_reminder', [
            'title'          => "Reunião em breve: {$appointment->title}",
            'message'        => $message,
            'reference_type' => 'appointment',
            'reference_id'   => $appointmentId,
        ]);
    }

    private function createRoomWithProvider(string $provider, object $appointment): ?array {
        $apiKey = defined('DAILY_API_KEY') ? DAILY_API_KEY : '';
        $domain = defined('DAILY_DOMAIN') ? DAILY_DOMAIN : 'consultoria.daily.co';

        if (empty($apiKey)) {
            return [
                'session_id' => uniqid('room_', true),
                'room_url'   => "https://{$domain}/" . uniqid('meet_', true),
            ];
        }

        $response = wp_remote_post("https://api.daily.co/v1/rooms", [
            'headers' => [
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ],
            'body' => json_encode([
                'name'        => 'cp-' . $appointment->id . '-' . uniqid(),
                'privacy'     => 'private',
                'properties'  => [
                    'exp'        => strtotime($appointment->end_time),
                    'nbf'        => strtotime($appointment->start_time) - 300,
                    'max_participants' => 10,
                    'enable_chat'      => true,
                    'enable_screenshare' => true,
                    'start_video_off'   => false,
                    'start_audio_off'   => false,
                ],
            ]),
        ]);

        if (is_wp_error($response)) {
            return null;
        }

        $body = json_decode(wp_remote_retrieve_body($response), true);

        if (!isset($body['url'])) {
            return null;
        }

        return [
            'session_id' => $body['name'] ?? $body['id'] ?? '',
            'room_url'   => $body['url'],
        ];
    }
}
