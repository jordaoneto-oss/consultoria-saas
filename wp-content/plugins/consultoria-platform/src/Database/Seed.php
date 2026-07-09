<?php

namespace Consultoria\Database;

class Seed {

    public static function run(): void {
        global $wpdb;
        $prefix = $wpdb->prefix;

        self::seedServicePlans($prefix);
        self::seedAchievements($prefix);
        self::seedSettings($prefix);
        self::seedSLARules($prefix);

        do_action('cp_seed_completed');
    }

    private static function seedServicePlans(string $prefix): void {
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}cp_service_plans");
        if ($existing > 0) return;

        $plans = [
            ['name' => 'Bronze', 'slug' => 'bronze', 'description' => 'Pacote ideal para consultorias pontuais e diagnósticos rápidos.', 'hours' => 10, 'price' => 1500.00, 'validity_days' => 90, 'highlighted' => 0, 'sort_order' => 1],
            ['name' => 'Prata', 'slug' => 'prata', 'description' => 'Pacote recomendado para projetos de médio porte com acompanhamento contínuo.', 'hours' => 20, 'price' => 2800.00, 'validity_days' => 180, 'highlighted' => 0, 'sort_order' => 2],
            ['name' => 'Ouro', 'slug' => 'ouro', 'description' => 'Pacote ideal para projetos estruturados com entregas semanais.', 'hours' => 50, 'price' => 6500.00, 'validity_days' => 270, 'highlighted' => 1, 'sort_order' => 3],
            ['name' => 'Enterprise', 'slug' => 'enterprise', 'description' => 'Pacote completo para grandes projetos com suporte dedicado e prioridade máxima.', 'hours' => 100, 'price' => 12000.00, 'validity_days' => 365, 'highlighted' => 0, 'sort_order' => 4],
        ];

        foreach ($plans as $plan) {
            $wpdb->insert($prefix . 'cp_service_plans', $plan);
        }
    }

    private static function seedAchievements(string $prefix): void {
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}cp_achievements");
        if ($existing > 0) return;

        $achievements = [
            ['name' => 'Top Avaliado', 'slug' => 'top-avaliado', 'description' => 'Manter média de avaliação acima de 4.5 por 30 dias', 'type' => 'rating', 'criteria' => 'avg_rating', 'criteria_value' => 45, 'xp_reward' => 500],
            ['name' => 'Especialista Verificado', 'slug' => 'especialista-verificado', 'description' => 'Completar o onboarding com todas as certificações', 'type' => 'special', 'criteria' => 'onboarding_complete', 'criteria_value' => 1, 'xp_reward' => 200],
            ['name' => 'Resposta Rápida', 'slug' => 'resposta-rapida', 'description' => 'Responder propostas em menos de 1 hora', 'type' => 'speed', 'criteria' => 'response_time', 'criteria_value' => 60, 'xp_reward' => 300],
            ['name' => '100 Projetos', 'slug' => '100-projetos', 'description' => 'Completar 100 projetos na plataforma', 'type' => 'projects', 'criteria' => 'total_projects', 'criteria_value' => 100, 'xp_reward' => 1000],
            ['name' => '500 Horas', 'slug' => '500-horas', 'description' => 'Acumular 500 horas trabalhadas', 'type' => 'hours', 'criteria' => 'total_hours', 'criteria_value' => 500, 'xp_reward' => 800],
            ['name' => 'Mestre do Marketplace', 'slug' => 'mestre-marketplace', 'description' => 'Faturar mais de R$ 50.000 na plataforma', 'type' => 'revenue', 'criteria' => 'total_revenue', 'criteria_value' => 50000, 'xp_reward' => 1500],
            ['name' => 'Primeiro Projeto', 'slug' => 'primeiro-projeto', 'description' => 'Completar o primeiro projeto com sucesso', 'type' => 'projects', 'criteria' => 'total_projects', 'criteria_value' => 1, 'xp_reward' => 100],
            ['name' => 'Cinco Estrelas', 'slug' => 'cinco-estrelas', 'description' => 'Receber 10 avaliações 5 estrelas consecutivas', 'type' => 'rating', 'criteria' => 'five_star_count', 'criteria_value' => 10, 'xp_reward' => 400],
        ];

        foreach ($achievements as $achievement) {
            $wpdb->insert($prefix . 'cp_achievements', $achievement);
        }
    }

    private static function seedSettings(string $prefix): void {
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}cp_settings");
        if ($existing > 0) return;

        $settings = [
            ['key_name' => 'platform_name', 'key_value' => 'Consultoria SaaS', 'autoload' => 1],
            ['key_name' => 'platform_commission_rate', 'key_value' => '20.00', 'autoload' => 1],
            ['key_name' => 'platform_withdrawal_fee', 'key_value' => '5.00', 'autoload' => 1],
            ['key_name' => 'min_withdrawal_amount', 'key_value' => '50.00', 'autoload' => 1],
            ['key_name' => 'max_withdrawal_amount', 'key_value' => '50000.00', 'autoload' => 1],
            ['key_name' => 'default_sla_rule_id', 'key_value' => '1', 'autoload' => 1],
            ['key_name' => 'cashback_default_percentage', 'key_value' => '5.00', 'autoload' => 1],
            ['key_name' => 'affiliate_default_commission', 'key_value' => '10.00', 'autoload' => 1],
            ['key_name' => 'matching_min_score', 'key_value' => '30.00', 'autoload' => 1],
            ['key_name' => 'matching_top_n', 'key_value' => '5', 'autoload' => 1],
            ['key_name' => 'trial_hours_enabled', 'key_value' => '1', 'autoload' => 1],
            ['key_name' => 'trial_hours_amount', 'key_value' => '2', 'autoload' => 1],
            ['key_name' => 'maintenance_mode', 'key_value' => '0', 'autoload' => 0],
            ['key_name' => 'stripe_connect_fee_percentage', 'key_value' => '2.90', 'autoload' => 1],
            ['key_name' => 'stripe_connect_fee_fixed', 'key_value' => '0.50', 'autoload' => 1],
            ['key_name' => 'platform_support_email', 'key_value' => 'suporte@consultoriasaas.com.br', 'autoload' => 1],
        ];

        foreach ($settings as $setting) {
            $wpdb->insert($prefix . 'cp_settings', $setting);
        }
    }

    private static function seedSLARules(string $prefix): void {
        $existing = $wpdb->get_var("SELECT COUNT(*) FROM {$prefix}cp_sla_rules");
        if ($existing > 0) return;

        $wpdb->insert($prefix . 'cp_sla_rules', [
            'name' => 'SLA Padrão',
            'category' => null,
            'scope' => 'consultoria',
            'response_time_hours' => 24,
            'accept_time_hours' => 48,
            'delivery_time_hours' => 168,
            'review_time_hours' => 48,
            'close_time_hours' => 24,
            'auto_escalation' => 1,
            'escalation_delay_hours' => 24,
            'escalation_to' => 'admin',
            'penalty_percentage' => 5.00,
            'is_default' => 1,
            'status' => 'active',
        ]);
    }
}
