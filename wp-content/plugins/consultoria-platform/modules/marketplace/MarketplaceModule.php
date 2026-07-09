<?php

namespace Consultoria\Modules\Marketplace;

use Consultoria\Modules\BaseModule;

class MarketplaceModule extends BaseModule {

    protected string $name = 'marketplace';

    public function init(): void {
        $this->addAction('rest_api_init', [$this, 'registerRoutes']);
        $this->addFilter('cp_shortcodes', [$this, 'registerShortcodes']);

        $service = new MarketplaceService();
        $this->registerService('marketplace', $service);
    }

    public function registerRoutes(): void {
        // Routes are registered centrally in Router.php
    }

    public function registerShortcodes(array $shortcodes): array {
        $shortcodes['cp_marketplace'] = [$this, 'renderMarketplace'];
        $shortcodes['cp_plans'] = [$this, 'renderPlans'];
        return $shortcodes;
    }

    public function renderMarketplace(): string {
        if (!is_user_logged_in()) {
            return $this->renderTemplate('marketplace-login');
        }
        return $this->renderTemplate('marketplace');
    }

    public function renderPlans(): string {
        return $this->renderTemplate('plans');
    }

    private function renderTemplate(string $template): string {
        $templatePath = CP_PLUGIN_DIR . "modules/marketplace/templates/{$template}.php";
        if (file_exists($templatePath)) {
            ob_start();
            include $templatePath;
            return ob_get_clean();
        }
        return '<p>Template não encontrado.</p>';
    }
}

class MarketplaceService {

    public function search(\WP_REST_Request $request): \WP_REST_Response {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $query = $request->get_param('q') ?? '';
        $category = $request->get_param('category') ?? '';
        $minRate = (float) ($request->get_param('min_rate') ?? 0);
        $maxRate = (float) ($request->get_param('max_rate') ?? 9999);
        $minRating = (float) ($request->get_param('min_rating') ?? 0);
        $sortBy = $request->get_param('sort_by') ?? 'rating';
        $page = max(1, (int) ($request->get_param('page') ?? 1));
        $perPage = min(50, max(1, (int) ($request->get_param('per_page') ?? 12)));
        $offset = ($page - 1) * $perPage;

        $where = ["c.status = 'active'"];
        $join = '';
        $params = [];

        if (!empty($query)) {
            $where[] = "(c.short_bio LIKE %s OR c.professional_title LIKE %s)";
            $likeQ = '%' . $wpdb->esc_like($query) . '%';
            $params[] = $likeQ;
            $params[] = $likeQ;
        }

        if (!empty($category)) {
            $join .= " INNER JOIN {$prefix}cp_expertise e ON e.consultant_id = c.id";
            $where[] = "e.category = %s";
            $params[] = $category;
        }

        if ($minRate > 0) {
            $where[] = "c.hourly_rate >= %f";
            $params[] = $minRate;
        }

        if ($maxRate < 9999) {
            $where[] = "c.hourly_rate <= %f";
            $params[] = $maxRate;
        }

        if ($minRating > 0) {
            $where[] = "c.rating >= %f";
            $params[] = $minRating;
        }

        $whereClause = implode(' AND ', $where);

        // Count
        $countSql = "SELECT COUNT(DISTINCT c.id) FROM {$prefix}cp_consultants c {$join} WHERE {$whereClause}";
        $total = (int) $wpdb->get_var($wpdb->prepare($countSql, $params));

        // SORT
        $orderBy = match ($sortBy) {
            'rating' => 'c.rating DESC',
            'price_asc' => 'c.hourly_rate ASC',
            'price_desc' => 'c.hourly_rate DESC',
            'projects' => 'c.total_projects DESC',
            'revenue' => 'c.total_revenue DESC',
            default => 'c.rating DESC',
        };

        $sql = "SELECT DISTINCT c.*, u.user_email, u.display_name
                FROM {$prefix}cp_consultants c
                INNER JOIN {$prefix}cp_users u ON u.id = c.user_id
                {$join}
                WHERE {$whereClause}
                ORDER BY {$orderBy}
                LIMIT %d OFFSET %d";

        $params[] = $perPage;
        $params[] = $offset;

        $consultants = $wpdb->get_results($wpdb->prepare($sql, $params));

        // Load expertise for each consultant
        if (!empty($consultants)) {
            $ids = array_map(fn($c) => $c->id, $consultants);
            $idsStr = implode(',', $ids);
            $expertise = $wpdb->get_results(
                "SELECT * FROM {$prefix}cp_expertise WHERE consultant_id IN ({$idsStr}) ORDER BY sort_order"
            );
            $expertiseByConsultant = [];
            foreach ($expertise as $exp) {
                $expertiseByConsultant[$exp->consultant_id][] = $exp;
            }
            foreach ($consultants as $c) {
                $c->expertise = $expertiseByConsultant[$c->id] ?? [];
            }
        }

        return \Consultoria\Helpers\Response::paginated($consultants, $total, $page, $perPage);
    }
}
