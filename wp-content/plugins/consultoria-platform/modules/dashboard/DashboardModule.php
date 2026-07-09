<?php

namespace Consultoria\Modules\Dashboard;

use Consultoria\Modules\BaseModule;

class DashboardModule extends BaseModule {

    protected string $name = 'dashboard';

    public function init(): void {
        $service = new DashboardService();
        $this->registerService('dashboard', $service);
    }
}

class DashboardService {

    private function getDb(): \wpdb {
        global $wpdb;
        return $wpdb;
    }

    public function clientDashboard(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();

        $client = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_clients WHERE user_id = %d",
            $userId
        ));

        if (!$client) {
            return \Consultoria\Helpers\Response::error('Perfil de cliente não encontrado');
        }

        // Hours summary
        $orders = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT SUM(hours) as total_hours, SUM(hours_used) as used_hours
             FROM {$this->getDb()->prefix}cp_orders WHERE user_id = %d AND status = 'completed'",
            $userId
        ));
        $totalHours = (int) ($orders[0]->total_hours ?? 0);
        $usedHours = (int) ($orders[0]->used_hours ?? 0);
        $remainingHours = $totalHours - $usedHours;

        // Projects
        $projects = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT p.*, u.display_name as consultant_name
             FROM {$this->getDb()->prefix}cp_projects p
             LEFT JOIN {$this->getDb()->prefix}cp_consultants c ON c.id = p.consultant_id
             LEFT JOIN {$this->getDb()->prefix}cp_users u ON u.id = c.user_id
             WHERE p.client_id = %d
             ORDER BY p.updated_at DESC
             LIMIT 10",
            $client->id
        ));

        // Upcoming appointments
        $appointments = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT a.*, u.display_name as consultant_name
             FROM {$this->getDb()->prefix}cp_appointments a
             INNER JOIN {$this->getDb()->prefix}cp_consultants c ON c.id = a.consultant_id
             INNER JOIN {$this->getDb()->prefix}cp_users u ON u.id = c.user_id
             WHERE a.client_id = %d AND a.start_time >= NOW() AND a.status IN ('scheduled', 'confirmed')
             ORDER BY a.start_time ASC
             LIMIT 5",
            $client->id
        ));

        // Recent messages
        $recentMessages = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT m.*, u.display_name as sender_name
             FROM {$this->getDb()->prefix}cp_messages m
             INNER JOIN {$this->getDb()->prefix}cp_projects p ON p.id = m.project_id
             INNER JOIN {$this->getDb()->prefix}cp_users u ON u.id = m.sender_id
             WHERE p.client_id = %d
             ORDER BY m.created_at DESC
             LIMIT 5",
            $client->id
        ));

        // Wallet
        $wallet = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT balance, total_spent FROM {$this->getDb()->prefix}cp_wallets WHERE user_id = %d",
            $userId
        ));

        return \Consultoria\Helpers\Response::success([
            'hours_remaining' => $remainingHours,
            'hours_used'      => $usedHours,
            'total_hours'     => $totalHours,
            'total_projects'  => count($projects),
            'total_spent'     => (float) ($wallet->total_spent ?? 0),
            'cashback_balance' => 0, // To be implemented
            'projects'        => $projects,
            'appointments'    => $appointments,
            'recent_messages' => $recentMessages,
            'wallet'          => $wallet,
        ]);
    }

    public function consultantDashboard(\WP_REST_Request $request): \WP_REST_Response {
        $userId = get_current_user_id();

        $consultant = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_consultants WHERE user_id = %d",
            $userId
        ));

        if (!$consultant) {
            return \Consultoria\Helpers\Response::error('Perfil de consultor não encontrado');
        }

        // Active projects
        $activeProjects = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT p.*, cl.company_name
             FROM {$this->getDb()->prefix}cp_projects p
             INNER JOIN {$this->getDb()->prefix}cp_clients cl ON cl.id = p.client_id
             WHERE p.consultant_id = %d AND p.status IN ('in_progress', 'review')
             ORDER BY p.updated_at DESC",
            $consultant->id
        ));

        // Pending proposals
        $pendingProposals = $this->getDb()->get_results($this->getDb()->prepare(
            "SELECT pr.*, p.title as project_title, p.status as project_status
             FROM {$this->getDb()->prefix}cp_proposals pr
             INNER JOIN {$this->getDb()->prefix}cp_projects p ON p.id = pr.project_id
             WHERE pr.consultant_id = %d AND pr.status = 'pending'
             ORDER BY pr.created_at DESC
             LIMIT 10",
            $consultant->id
        ));

        // Wallet
        $wallet = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_wallets WHERE user_id = %d",
            $userId
        ));

        // Monthly revenue
        $monthlyRevenue = $this->getDb()->get_var($this->getDb()->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM {$this->getDb()->prefix}cp_transactions t
             INNER JOIN {$this->getDb()->prefix}cp_wallets w ON w.id = t.wallet_id
             WHERE w.user_id = %d AND t.type = 'credit'
             AND t.created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')",
            $userId
        ));

        // Gamification
        $gamification = $this->getDb()->get_row($this->getDb()->prepare(
            "SELECT * FROM {$this->getDb()->prefix}cp_gamification WHERE user_id = %d",
            $userId
        ));

        return \Consultoria\Helpers\Response::success([
            'consultant'        => $consultant,
            'active_projects'   => $activeProjects,
            'pending_proposals' => $pendingProposals,
            'wallet'            => $wallet,
            'monthly_revenue'   => (float) $monthlyRevenue,
            'gamification'      => $gamification,
        ]);
    }

    public function adminDashboard(\WP_REST_Request $request): \WP_REST_Response {
        $prefix = $this->getDb()->prefix;

        // GMV
        $gmvMonth = (float) $this->getDb()->get_var(
            "SELECT COALESCE(SUM(total), 0) FROM {$prefix}cp_orders
             WHERE status = 'completed'
             AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"
        );

        $gmvYear = (float) $this->getDb()->get_var(
            "SELECT COALESCE(SUM(total), 0) FROM {$prefix}cp_orders
             WHERE status = 'completed'
             AND created_at >= DATE_FORMAT(NOW(), '%Y-01-01')"
        );

        // Platform revenue (commission)
        $revenueMonth = (float) $this->getDb()->get_var(
            "SELECT COALESCE(SUM(fee_platform), 0) FROM {$prefix}cp_transactions
             WHERE type IN ('fee', 'commission')
             AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"
        );

        // Active users
        $activeConsultants = (int) $this->getDb()->get_var(
            "SELECT COUNT(*) FROM {$prefix}cp_consultants WHERE status = 'active'"
        );

        $activeClients = (int) $this->getDb()->get_var(
            "SELECT COUNT(*) FROM {$prefix}cp_clients"
        );

        // Projects
        $totalProjects = (int) $this->getDb()->get_var(
            "SELECT COUNT(*) FROM {$prefix}cp_projects"
        );
        $activeProjects = (int) $this->getDb()->get_var(
            "SELECT COUNT(*) FROM {$prefix}cp_projects WHERE status NOT IN ('completed', 'cancelled')"
        );

        // Pending approvals
        $pendingConsultants = (int) $this->getDb()->get_var(
            "SELECT COUNT(*) FROM {$prefix}cp_consultants WHERE status = 'pending'"
        );

        $pendingWithdrawals = (int) $this->getDb()->get_var(
            "SELECT COUNT(*) FROM {$prefix}cp_withdrawals WHERE status = 'pending'"
        );

        // Monthly revenue chart (last 12 months)
        $monthlyChart = $this->getDb()->get_results(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') as month,
                    COALESCE(SUM(fee_platform), 0) as revenue,
                    COALESCE(SUM(amount), 0) as gmv
             FROM {$prefix}cp_orders o
             WHERE o.status = 'completed'
             AND o.created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
             GROUP BY DATE_FORMAT(created_at, '%Y-%m')
             ORDER BY month ASC"
        );

        // Recent activity
        $recentActivity = $this->getDb()->get_results(
            "SELECT 'new_user' as type, created_at, CONCAT('Novo cadastro') as description
             FROM {$prefix}cp_users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             UNION ALL
             SELECT 'project' as type, created_at, CONCAT('Projeto: ', title) as description
             FROM {$prefix}cp_projects WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             UNION ALL
             SELECT 'withdrawal' as type, requested_at, CONCAT('Saque solicitado: R$ ', amount) as description
             FROM {$prefix}cp_withdrawals WHERE requested_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             ORDER BY created_at DESC LIMIT 20"
        );

        return \Consultoria\Helpers\Response::success([
            'gmv_month'             => $gmvMonth,
            'gmv_year'              => $gmvYear,
            'revenue_month'         => $revenueMonth,
            'active_consultants'    => $activeConsultants,
            'active_clients'        => $activeClients,
            'total_projects'        => $totalProjects,
            'active_projects'       => $activeProjects,
            'pending_consultants'   => $pendingConsultants,
            'pending_withdrawals'   => $pendingWithdrawals,
            'monthly_chart'         => $monthlyChart,
            'recent_activity'       => $recentActivity,
        ]);
    }
}
