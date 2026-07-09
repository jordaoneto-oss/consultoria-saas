<?php

namespace Consultoria\Modules\Scheduling;

use Consultoria\Modules\BaseModule;

class SchedulingModule extends BaseModule {

    protected string $name = 'scheduling';

    public function init(): void {
        $service = new SchedulingService();
        $this->registerService('scheduling', $service);

        $this->addAction('cp_hourly_cron', [$service, 'sendReminders']);
    }
}

class SchedulingService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function getAppointments(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $startDate = $request->get_param('start_date') ?? date('Y-m-d');
        $endDate = $request->get_param('end_date') ?? date('Y-m-d', strtotime('+30 days'));
        $status = $request->get_param('status') ?? '';

        $where = "(c.user_id = %d OR co.user_id = %d)";
        $params = [$userId, $userId];

        if (!empty($status)) {
            $where .= " AND a.status = %s";
            $params[] = $status;
        }

        $appointments = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT a.*,
                    cl.user_id as client_user_id,
                    con.user_id as consultant_user_id,
                    cli.display_name as client_name,
                    cons.display_name as consultant_name
             FROM {$this->getDb()->prefix}cp_appointments a
             INNER JOIN {$this->getDb()->prefix}cp_clients cl ON cl.id = a.client_id
             INNER JOIN {$this->getDb()->prefix}cp_users cli ON cli.id = cl.user_id
             INNER JOIN {$this->getDb()->prefix}cp_consultants con ON con.id = a.consultant_id
             INNER JOIN {$this->getDb()->prefix}cp_users cons ON cons.id = con.user_id
             WHERE {$where}
             AND a.start_time >= %s
             AND a.start_time <= %s
             ORDER BY a.start_time ASC",
            array_merge($params, [$startDate, $endDate])
        ));

        return \Consultoria\Helpers\Response::success(['appointments' => $appointments]);
    }

    public function createAppointment(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $projectId = (int) $request->get_param('project_id');
        $title = sanitize_text_field($request->get_param('title'));
        $startTime = $request->get_param('start_time');
        $endTime = $request->get_param('end_time');
        $type = $request->get_param('type') ?? 'videoconference';

        $validator = new \Consultoria\Helpers\Validator();
        if (!$validator->validate($request->get_params(), [
            'project_id'  => 'required|integer',
            'title'       => 'required',
            'start_time'  => 'required|date',
            'end_time'    => 'required|date',
        ])) {
            return \Consultoria\Helpers\Response::validationError($validator->getErrors());
        }

        // Get project participants
        $project = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT p.*, c.user_id as client_user_id, co.user_id as consultant_user_id
             FROM {$this->getDb()->prefix}cp_projects p
             INNER JOIN {$this->getDb()->prefix}cp_clients c ON c.id = p.client_id
             INNER JOIN {$this->getDb()->prefix}cp_consultants co ON co.id = p.consultant_id
             WHERE p.id = %d",
            $projectId
        ));

        if (!$project) {
            return \Consultoria\Helpers\Response::notFound('Projeto não encontrado');
        }

        // Determine client_id and consultant_id
        $client = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT id FROM {$this->getDb()->prefix}cp_clients WHERE user_id = %d",
            $project->client_user_id
        ));
        $consultant = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT id FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
            $project->consultant_user_id
        ));

        $result = $this->getDb()->insert($this->getDb()->prefix . 'cp_appointments', [
            'project_id'      => $projectId,
            'consultant_id'   => $consultant->id,
            'client_id'       => $client->id,
            'title'           => $title,
            'description'     => sanitize_textarea_field($request->get_param('description') ?? ''),
            'type'            => $type,
            'start_time'      => $startTime,
            'end_time'        => $endTime,
            'timezone'        => $request->get_param('timezone') ?? 'America/Sao_Paulo',
            'status'          => 'scheduled',
            'created_at'      => current_time('mysql'),
        ]);

        if (!$result) {
            return \Consultoria\Helpers\Response::error('Erro ao criar agendamento');
        }

        $appointmentId = $this->getDb()->insert_id;

        do_action('cp_appointment_created', $appointmentId);

        return \Consultoria\Helpers\Response::created(['appointment_id' => $appointmentId]);
    }

    public function updateAppointment(\WP_REST_Request $request): \WP_REST_Response {
        $appointmentId = (int) $request->get_param('id');
        $appointment = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_appointments WHERE id = %d",
            $appointmentId
        ));

        if (!$appointment) {
            return \Consultoria\Helpers\Response::notFound('Agendamento não encontrado');
        }

        $updateData = [];
        if ($request->has_param('title')) $updateData['title'] = sanitize_text_field($request->get_param('title'));
        if ($request->has_param('start_time')) $updateData['start_time'] = $request->get_param('start_time');
        if ($request->has_param('end_time')) $updateData['end_time'] = $request->get_param('end_time');
        if ($request->has_param('status')) $updateData['status'] = $request->get_param('status');
        if ($request->has_param('description')) $updateData['description'] = sanitize_textarea_field($request->get_param('description'));
        if ($request->has_param('meeting_url')) $updateData['meeting_url'] = esc_url_raw($request->get_param('meeting_url'));

        if (!empty($updateData)) {
            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_appointments',
                $updateData,
                ['id' => $appointmentId]
            );
        }

        return \Consultoria\Helpers\Response::success(['message' => 'Agendamento atualizado']);
    }

    public function updateAvailability(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();
        $consultant = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT id FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
            $userId
        ));

        if (!$consultant) {
            return \Consultoria\Helpers\Response::error('Perfil de consultor não encontrado');
        }

        $availability = $request->get_param('availability');
        if (!is_array($availability)) {
            return \Consultoria\Helpers\Response::error('Formato de disponibilidade inválido');
        }

        // Remove existing
        $this->getDb()->delete(
            $this->getDb()->prefix . 'cp_consultant_availability',
            ['consultant_id' => $consultant->id]
        );

        // Insert new
        foreach ($availability as $slot) {
            $this->getDb()->insert($this->getDb()->prefix . 'cp_consultant_availability', [
                'consultant_id' => $consultant->id,
                'day_of_week'   => (int) ($slot['day_of_week'] ?? 0),
                'start_time'    => $slot['start_time'] ?? '09:00:00',
                'end_time'      => $slot['end_time'] ?? '18:00:00',
                'is_available'  => $slot['is_available'] ?? 1,
            ]);
        }

        return \Consultoria\Helpers\Response::success(['message' => 'Disponibilidade atualizada']);
    }

    public function sendReminders(): void {
        $now = current_time('mysql');
        $inOneHour = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $appointments = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT a.*, c.user_id as client_user_id, co.user_id as consultant_user_id
             FROM {$this->getDb()->prefix}cp_appointments a
             INNER JOIN {$this->getDb()->prefix}cp_clients c ON c.id = a.client_id
             INNER JOIN {$this->getDb()->prefix}cp_consultants co ON co.id = a.consultant_id
             WHERE a.start_time BETWEEN %s AND %s
             AND a.reminder_sent = 0
             AND a.status IN ('scheduled', 'confirmed')",
            $now, $inOneHour
        ));

        foreach ($appointments as $appointment) {
            do_action('cp_send_notification', $appointment->client_user_id, 'appointment_reminder', [
                'title' => 'Lembrete de reunião',
                'message' => "Você tem uma reunião em 1 hora: {$appointment->title}",
                'appointment_id' => $appointment->id,
            ]);

            do_action('cp_send_notification', $appointment->consultant_user_id, 'appointment_reminder', [
                'title' => 'Lembrete de reunião',
                'message' => "Você tem uma reunião em 1 hora: {$appointment->title}",
                'appointment_id' => $appointment->id,
            ]);

            $this->getDb()->update(
                $this->getDb()->prefix . 'cp_appointments',
                ['reminder_sent' => 1],
                ['id' => $appointment->id]
            );
        }
    }
}
