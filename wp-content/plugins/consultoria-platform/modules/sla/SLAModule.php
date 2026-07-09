<?php

namespace Consultoria\Modules\SLA;

use Consultoria\Modules\BaseModule;

class SLAModule extends BaseModule {

    protected string $name = 'sla';

    public function init(): void {
        $service = new SLAService();
        $this->registerService('sla', $service);

        $this->addAction('cp_check_sla', [$service, 'checkAllProjects']);
        $this->addAction('cp_project_started', [$service, 'startMonitoring'], 10, 1);
    }
}

class SLAService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function getRules(\WP_REST_Request $request): \WP_REST_Response {
        $rules = $this->getDb()->get_results(
            "SELECT * FROM {$this->getDb()->prefix}cp_sla_rules WHERE status = 'active' ORDER BY scope, name"
        );
        return \Consultoria\Helpers\Response::success(['rules' => $rules]);
    }

    public function createRule(\WP_REST_Request $request): \WP_REST_Response {
        $data = [
            'name'                  => sanitize_text_field($request->get_param('name')),
            'category'              => sanitize_text_field($request->get_param('category')),
            'scope'                 => $request->get_param('scope'),
            'response_time_hours'   => (int) $request->get_param('response_time_hours'),
            'accept_time_hours'     => (int) $request->get_param('accept_time_hours'),
            'delivery_time_hours'   => (int) $request->get_param('delivery_time_hours'),
            'review_time_hours'     => (int) $request->get_param('review_time_hours'),
            'close_time_hours'      => (int) $request->get_param('close_time_hours'),
            'auto_escalation'       => (int) ($request->get_param('auto_escalation') ?? 1),
            'escalation_delay_hours' => (int) ($request->get_param('escalation_delay_hours') ?? 24),
            'penalty_percentage'    => (float) ($request->get_param('penalty_percentage') ?? 0),
            'status'                => 'active',
        ];

        $validator = new \Consultoria\Helpers\Validator();
        if (!$validator->validate($data, [
            'name' => 'required',
            'scope' => 'required|in:consultoria,desenvolvimento,implantacao,suporte,treinamento',
        ])) {
            return \Consultoria\Helpers\Response::validationError($validator->getErrors());
        }

        $result = $this->getDb()->insert($this->getDb()->prefix . 'cp_sla_rules', $data);
        if (!$result) {
            return \Consultoria\Helpers\Response::error('Erro ao criar regra SLA');
        }

        return \Consultoria\Helpers\Response::created(['rule_id' => $this->getDb()->insert_id]);
    }

    public function getProjectSLA(\WP_REST_Request $request): \WP_REST_Response {
        $projectId = (int) $request->get_param('id');

        $monitor = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT m.*, r.name as rule_name, r.scope
             FROM {$this->getDb()->prefix}cp_sla_monitor m
             INNER JOIN {$this->getDb()->prefix}cp_sla_rules r ON r.id = m.rule_id
             WHERE m.project_id = %d",
            $projectId
        ));

        return \Consultoria\Helpers\Response::success(['sla' => $monitor]);
    }

    public function startMonitoring(int $projectId): void {
        $project = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_projects WHERE id = %d",
            $projectId
        ));

        if (!$project) return;

        // Find matching SLA rule
        $rule = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_sla_rules
             WHERE (category = %s OR category IS NULL)
             AND scope = %s
             AND status = 'active'
             ORDER BY is_default DESC
             LIMIT 1",
            $project->category,
            $project->scope
        ));

        if (!$rule) {
            // Use default rule
            $rule = $this->getDb()->get_row(
                "SELECT * FROM {$this->getDb()->prefix}cp_sla_rules WHERE is_default = 1 AND status = 'active' LIMIT 1"
            );
        }

        if (!$rule) return;

        $now = current_time('mysql');
        $this->getDb()->insert($this->getDb()->prefix . 'cp_sla_monitor', [
            'project_id'       => $projectId,
            'rule_id'          => $rule->id,
            'status'           => 'active',
            'response_deadline' => date('Y-m-d H:i:s', strtotime("+{$rule->response_time_hours} hours")),
            'accept_deadline'  => date('Y-m-d H:i:s', strtotime("+{$rule->accept_time_hours} hours")),
            'delivery_deadline' => date('Y-m-d H:i:s', strtotime("+{$rule->delivery_time_hours} hours")),
            'review_deadline'  => date('Y-m-d H:i:s', strtotime("+{$rule->review_time_hours} hours")),
            'close_deadline'   => date('Y-m-d H:i:s', strtotime("+{$rule->close_time_hours} hours")),
            'created_at'       => $now,
        ]);
    }

    public function checkAllProjects(): void {
        $now = current_time('mysql');

        // Check response deadline
        $this->getDb()->query(
            "UPDATE {$this->getDb()->prefix}cp_sla_monitor
             SET response_breached = 1, status = 'breached'
             WHERE response_deadline < '{$now}' AND responded_at IS NULL AND status = 'active'"
        );

        // Check delivery deadline
        $this->getDb()->query(
            "UPDATE {$this->getDb()->prefix}cp_sla_monitor
             SET delivery_breached = 1, status = 'breached'
             WHERE delivery_deadline < '{$now}' AND delivered_at IS NULL AND status = 'active'"
        );

        // Escalate breached projects
        $breached = $this->getDb()->get_results(
            "SELECT m.*, r.auto_escalation, r.escalation_delay_hours, r.escalation_to
             FROM {$this->getDb()->prefix}cp_sla_monitor m
             INNER JOIN {$this->getDb()->prefix}cp_sla_rules r ON r.id = m.rule_id
             WHERE m.status = 'breached' AND m.escalated = 0
             AND r.auto_escalation = 1
             AND m.escalated_at IS NULL"
        );

        foreach ($breached as $monitor) {
            $escalationTime = $monitor->response_deadline ?: $monitor->delivery_deadline;
            if (strtotime($escalationTime) + ($monitor->escalation_delay_hours * 3600) < time()) {
                $this->getDb()->update(
                    $this->getDb()->prefix . 'cp_sla_monitor',
                    [
                        'escalated'    => 1,
                        'escalated_at' => $now,
                    ],
                    ['id' => $monitor->id]
                );

                do_action('cp_sla_escalated', $monitor->project_id, $monitor->escalation_to);
            }
        }
    }
}
