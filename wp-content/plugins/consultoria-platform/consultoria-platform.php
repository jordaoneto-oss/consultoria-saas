<?php
/**
 * Plugin Name:     Consultoria Platform
 * Plugin URI:      https://consultoriasaas.com.br
 * Description:     Plataforma SaaS completa para marketplace de consultoria de negócios e tecnologia
 * Version:         1.0.0
 * Author:          Consultoria SaaS
 * Text Domain:     consultoria-platform
 * Domain Path:     /languages
 * Requires PHP:    8.0
 * Requires WP:     6.0
 * WC requires at least: 7.0
 * WC tested up to: 8.0
 */

defined('ABSPATH') || exit;

define('CP_VERSION', '1.0.0');
define('CP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('CP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('CP_PLUGIN_FILE', __FILE__);
define('CP_MIN_PHP_VERSION', '8.0');
define('CP_MIN_WP_VERSION', '6.0');
define('CP_DB_VERSION', '1.0.0');

require_once CP_PLUGIN_DIR . 'src/Interfaces/ContainerInterface.php';
require_once CP_PLUGIN_DIR . 'src/Helpers/Logger.php';
require_once CP_PLUGIN_DIR . 'src/Helpers/Functions.php';
require_once CP_PLUGIN_DIR . 'src/Exceptions/BaseException.php';

final class ConsultoriaPlatform {

    private static ?ConsultoriaPlatform $instance = null;
    private array $services = [];
    private array $modules = [];
    private bool $initialized = false;

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->registerAutoloader();
    }

    public function init(): void {
        if ($this->initialized) return;

        $this->checkRequirements();
        $this->loadDependencies();
        $this->registerHooks();
        $this->initModules();

        $this->initialized = true;

        do_action('cp_initialized');
    }

    public function activar(): void {
        $this->checkRequirements();
        require_once CP_PLUGIN_DIR . 'src/Database/Schema.php';
        Schema::create();
        require_once CP_PLUGIN_DIR . 'src/Database/Seed.php';
        Seed::run();
        $this->createPages();
        $this->createRoles();
        flush_rewrite_rules();
    }

    public function desactivar(): void {
        $this->clearScheduledHooks();
        flush_rewrite_rules();
    }

    public function getService(string $id): ?object {
        return $this->services[$id] ?? null;
    }

    public function getModule(string $id): ?object {
        return $this->modules[$id] ?? null;
    }

    public function registerService(string $id, object $service): void {
        $this->services[$id] = $service;
    }

    private function registerAutoloader(): void {
        spl_autoload_register(function (string $class) {
            $prefix = 'Consultoria\\';
            if (strpos($class, $prefix) !== 0) return;

            $relativeClass = substr($class, strlen($prefix));
            $file = CP_PLUGIN_DIR . 'src/' . str_replace('\\', '/', $relativeClass) . '.php';

            if (file_exists($file)) require_once $file;
        });
    }

    private function checkRequirements(): void {
        if (version_compare(PHP_VERSION, CP_MIN_PHP_VERSION, '<')) {
            throw new \RuntimeException(
                sprintf('Consultoria Platform requer PHP %s ou superior.', CP_MIN_PHP_VERSION)
            );
        }
        if (version_compare($GLOBALS['wp_version'], CP_MIN_WP_VERSION, '<')) {
            throw new \RuntimeException(
                sprintf('Consultoria Platform requer WordPress %s ou superior.', CP_MIN_WP_VERSION)
            );
        }
    }

    private function loadDependencies(): void {
        require_once CP_PLUGIN_DIR . 'src/Database/Schema.php';
        require_once CP_PLUGIN_DIR . 'src/Database/Seed.php';
        require_once CP_PLUGIN_DIR . 'src/Router.php';
        require_once CP_PLUGIN_DIR . 'src/Middleware/AuthMiddleware.php';
        require_once CP_PLUGIN_DIR . 'src/Middleware/LoggingMiddleware.php';
        require_once CP_PLUGIN_DIR . 'src/Helpers/Validator.php';
        require_once CP_PLUGIN_DIR . 'src/Helpers/Response.php';
    }

    private function registerHooks(): void {
        add_action('init', [$this, 'registerPostTypes']);
        add_action('init', [$this, 'registerShortcodes']);
        add_action('rest_api_init', [$this, 'registerRoutes']);
        add_action('admin_menu', [$this, 'registerAdminMenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueueAdminAssets']);
        add_action('wp_enqueue_scripts', [$this, 'enqueuePublicAssets']);
        add_filter('woocommerce_checkout_fields', [$this, 'customizeCheckoutFields']);
        add_filter('woocommerce_thankyou', [$this, 'handleOrderComplete'], 10, 1);
        add_action('cp_daily_cron', [$this, 'executeDailyTasks']);
        add_action('cp_hourly_cron', [$this, 'executeHourlyTasks']);

        if (wp_doing_ajax()) {
            add_action('wp_ajax_cp_ajax_handler', [$this, 'handleAjax']);
            add_action('wp_ajax_nopriv_cp_ajax_handler', [$this, 'handleAjax']);
        }

        register_activation_hook(__FILE__, [$this, 'activar']);
        register_deactivation_hook(__FILE__, [$this, 'desactivar']);
    }

    private function initModules(): void {
        $moduleList = [
            'marketplace'    => 'Consultoria\\Modules\\Marketplace\\MarketplaceModule',
            'wallet'         => 'Consultoria\\Modules\\Wallet\\WalletModule',
            'scheduling'     => 'Consultoria\\Modules\\Scheduling\\SchedulingModule',
            'chat'           => 'Consultoria\\Modules\\Chat\\ChatModule',
            'videoconference' => 'Consultoria\\Modules\\Videoconference\\VideoconferenceModule',
            'contracts'      => 'Consultoria\\Modules\\Contracts\\ContractsModule',
            'dashboard'      => 'Consultoria\\Modules\\Dashboard\\DashboardModule',
            'sla'            => 'Consultoria\\Modules\\SLA\\SLAModule',
            'gamification'   => 'Consultoria\\Modules\\Gamification\\GamificationModule',
            'cashback'       => 'Consultoria\\Modules\\Cashback\\CashbackModule',
            'affiliates'     => 'Consultoria\\Modules\\Affiliates\\AffiliatesModule',
            'notifications'  => 'Consultoria\\Modules\\Notifications\\NotificationsModule',
            'tickets'        => 'Consultoria\\Modules\\Tickets\\TicketsModule',
        ];

        foreach ($moduleList as $id => $moduleClass) {
            if (class_exists($moduleClass)) {
                $module = new $moduleClass();
                $module->init();
                $this->modules[$id] = $module;
            }
        }
    }

    public function registerPostTypes(): void {
        register_post_type('cp_project', [
            'labels' => [
                'name'          => __('Projetos', 'consultoria-platform'),
                'singular_name' => __('Projeto', 'consultoria-platform'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'capabilities' => ['create_posts' => 'do_not_allow'],
            'map_meta_cap' => true,
            'supports'     => ['title', 'editor', 'author', 'custom-fields'],
        ]);

        register_post_type('cp_proposal', [
            'labels' => [
                'name'          => __('Propostas', 'consultoria-platform'),
                'singular_name' => __('Proposta', 'consultoria-platform'),
            ],
            'public'       => false,
            'show_ui'      => true,
            'show_in_menu' => false,
            'capability_type' => 'post',
            'capabilities' => ['create_posts' => 'do_not_allow'],
            'map_meta_cap' => true,
            'supports'     => ['title', 'author', 'custom-fields'],
        ]);
    }

    public function registerRoutes(): void {
        $router = new Consultoria\Router();
        $router->registerRoutes();
    }

    public function registerAdminMenu(): void {
        add_menu_page(
            __('Consultoria', 'consultoria-platform'),
            __('Consultoria', 'consultoria-platform'),
            'manage_options',
            'consultoria-platform',
            [$this, 'renderAdminPage'],
            'dashicons-businessman',
            30
        );

        add_submenu_page(
            'consultoria-platform',
            __('Dashboard', 'consultoria-platform'),
            __('Dashboard', 'consultoria-platform'),
            'manage_options',
            'consultoria-platform',
            [$this, 'renderAdminPage']
        );

        add_submenu_page(
            'consultoria-platform',
            __('Planos', 'consultoria-platform'),
            __('Planos', 'consultoria-platform'),
            'manage_options',
            'cp-plans',
            [$this, 'renderAdminPage']
        );

        add_submenu_page(
            'consultoria-platform',
            __('Consultores', 'consultoria-platform'),
            __('Consultores', 'consultoria-platform'),
            'manage_options',
            'cp-consultants',
            [$this, 'renderAdminPage']
        );

        add_submenu_page(
            'consultoria-platform',
            __('Projetos', 'consultoria-platform'),
            __('Projetos', 'consultoria-platform'),
            'manage_options',
            'cp-projects',
            [$this, 'renderAdminPage']
        );

        add_submenu_page(
            'consultoria-platform',
            __('Financeiro', 'consultoria-platform'),
            __('Financeiro', 'consultoria-platform'),
            'manage_options',
            'cp-financial',
            [$this, 'renderAdminPage']
        );

        add_submenu_page(
            'consultoria-platform',
            __('Saques', 'consultoria-platform'),
            __('Saques', 'consultoria-platform'),
            'manage_options',
            'cp-withdrawals',
            [$this, 'renderAdminPage']
        );

        add_submenu_page(
            'consultoria-platform',
            __('Configurações', 'consultoria-platform'),
            __('Configurações', 'consultoria-platform'),
            'manage_options',
            'cp-settings',
            [$this, 'renderAdminPage']
        );
    }

    public function renderAdminPage(): void {
        $page = $_GET['page'] ?? 'consultoria-platform';
        $adminView = CP_PLUGIN_DIR . 'admin/' . str_replace('cp-', '', $page) . '.php';
        if (file_exists($adminView)) {
            include $adminView;
        } else {
            include CP_PLUGIN_DIR . 'admin/dashboard.php';
        }
    }

    public function enqueueAdminAssets(string $hook): void {
        if (strpos($hook, 'cp-') !== false || strpos($hook, 'consultoria-platform') !== false) {
            wp_enqueue_style('cp-admin', CP_PLUGIN_URL . 'admin/assets/css/admin.css', [], CP_VERSION);
            wp_enqueue_script('cp-admin', CP_PLUGIN_URL . 'admin/assets/js/admin.js', ['jquery'], CP_VERSION, true);
            wp_localize_script('cp-admin', 'cpAdmin', [
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce'   => wp_create_nonce('cp_admin_nonce'),
                'i18n'    => $this->getAdminTranslations(),
            ]);
        }
    }

    public function enqueuePublicAssets(): void {
        if (!is_admin()) {
            wp_enqueue_style('cp-public', CP_PLUGIN_URL . 'public/assets/css/public.css', [], CP_VERSION);
            wp_enqueue_script('cp-public', CP_PLUGIN_URL . 'public/assets/js/public.js', ['jquery'], CP_VERSION, true);
            wp_localize_script('cp-public', 'cpData', [
                'ajaxUrl'  => admin_url('admin-ajax.php'),
                'restUrl'  => rest_url('consultoria/v1'),
                'nonce'    => wp_create_nonce('wp_rest'),
                'userId'   => get_current_user_id(),
                'i18n'     => $this->getPublicTranslations(),
            ]);
        }
    }

    public function customizeCheckoutFields(array $fields): array {
        $fields['billing']['cp_document'] = [
            'label'       => __('CPF/CNPJ', 'consultoria-platform'),
            'required'    => true,
            'class'       => ['form-row-wide'],
            'priority'    => 25,
        ];
        $fields['billing']['cp_person_type'] = [
            'type'        => 'select',
            'label'       => __('Tipo de Pessoa', 'consultoria-platform'),
            'required'    => true,
            'options'     => ['pf' => 'Pessoa Física', 'pj' => 'Pessoa Jurídica'],
            'class'       => ['form-row-wide'],
            'priority'    => 20,
        ];
        return $fields;
    }

    public function handleOrderComplete(int $orderId): void {
        $order = wc_get_order($orderId);
        if (!$order) return;

        $planId = $order->get_meta('_cp_plan_id');
        if (!$planId) return;

        $cpOrderId = $this->createCpOrder($order, $planId);
        if ($cpOrderId) {
            $order->update_meta_data('_cp_order_id', $cpOrderId);
            $order->save();
        }
    }

    private function createCpOrder(\WC_Order $order, int $planId): ?int {
        global $wpdb;

        $plan = $this->getServicePlan($planId);
        if (!$plan) return null;

        $user = get_user_by('email', $order->get_billing_email());
        if (!$user) return null;

        $cpUser = $wpdb->get_row($wpdb->prepare(
            "SELECT id FROM {$wpdb->prefix}cp_users WHERE wp_user_id = %d",
            $user->ID
        ));
        if (!$cpUser) return null;

        $result = $wpdb->insert($wpdb->prefix . 'cp_orders', [
            'woocommerce_order_id' => $order->get_id(),
            'user_id'              => $cpUser->id,
            'plan_id'              => $planId,
            'status'               => 'completed',
            'total'                => $order->get_total(),
            'hours'                => $plan->hours,
            'expires_at'           => date('Y-m-d H:i:s', strtotime("+{$plan->validity_days} days")),
            'paid_at'              => current_time('mysql'),
        ]);

        if ($result) {
            $cpOrderId = $wpdb->insert_id;
            $this->getModule('cashback')?->processCashback($cpOrderId, $cpUser->id, $order->get_total());
            $this->getModule('gamification')?->addXP($cpUser->id, 50, 'Compra de plano');
            return $cpOrderId;
        }

        return null;
    }

    private function getServicePlan(int $planId): ?object {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}cp_service_plans WHERE id = %d AND status = 'active'",
            $planId
        ));
    }

    public function handleAjax(): void {
        check_ajax_referer('cp_admin_nonce', 'nonce');
        $action = $_POST['cp_action'] ?? '';
        // Route to appropriate handler
        wp_send_json_error(['message' => 'Unknown action']);
    }

    public function executeDailyTasks(): void {
        // Expirar ordens vencidas
        global $wpdb;
        $wpdb->query("UPDATE {$wpdb->prefix}cp_orders SET status = 'expired' WHERE expires_at < NOW() AND status = 'completed'");

        // Processar saques automáticos
        do_action('cp_process_auto_withdrawals');

        // Limpar logs antigos
        do_action('cp_cleanup_old_logs');

        Logger::info('Tarefas diárias executadas');
    }

    public function executeHourlyTasks(): void {
        // Verificar SLA
        do_action('cp_check_sla');

        // Enviar lembretes de agendamento
        do_action('cp_send_appointment_reminders');

        // Processar fila de notificações
        do_action('cp_process_notification_queue');
    }

    private function createPages(): void {
        $pages = [
            'marketplace' => [
                'title'   => __('Marketplace', 'consultoria-platform'),
                'content' => '[cp_marketplace]',
            ],
            'dashboard-cliente' => [
                'title'   => __('Meu Painel', 'consultoria-platform'),
                'content' => '[cp_client_dashboard]',
            ],
            'dashboard-consultor' => [
                'title'   => __('Painel do Consultor', 'consultoria-platform'),
                'content' => '[cp_consultant_dashboard]',
            ],
            'perfil-consultor' => [
                'title'   => __('Perfil do Consultor', 'consultoria-platform'),
                'content' => '[cp_consultant_profile]',
            ],
            'checkout-planos' => [
                'title'   => __('Planos', 'consultoria-platform'),
                'content' => '[cp_plans]',
            ],
            'minha-carteira' => [
                'title'   => __('Minha Carteira', 'consultoria-platform'),
                'content' => '[cp_wallet]',
            ],
        ];

        foreach ($pages as $slug => $pageData) {
            if (!get_page_by_path($slug)) {
                wp_insert_post([
                    'post_title'   => $pageData['title'],
                    'post_content' => $pageData['content'],
                    'post_status'  => 'publish',
                    'post_type'    => 'page',
                    'post_name'    => $slug,
                ]);
            }
        }
    }

    private function createRoles(): void {
        add_role('cp_client', __('Cliente', 'consultoria-platform'), [
            'read'                      => true,
            'upload_files'              => true,
            'level_0'                   => true,
        ]);

        add_role('cp_consultant', __('Consultor', 'consultoria-platform'), [
            'read'                      => true,
            'upload_files'              => true,
            'level_0'                   => true,
        ]);

        add_role('cp_support', __('Suporte', 'consultoria-platform'), [
            'read'                      => true,
            'upload_files'              => true,
            'level_1'                   => true,
            'edit_posts'                => true,
            'edit_others_posts'         => true,
        ]);
    }

    private function clearScheduledHooks(): void {
        wp_clear_scheduled_hook('cp_daily_cron');
        wp_clear_scheduled_hook('cp_hourly_cron');
    }

    public function setupCronJobs(): void {
        if (!wp_next_scheduled('cp_daily_cron')) {
            wp_schedule_event(time(), 'daily', 'cp_daily_cron');
        }
        if (!wp_next_scheduled('cp_hourly_cron')) {
            wp_schedule_event(time(), 'hourly', 'cp_hourly_cron');
        }
    }

    private function getAdminTranslations(): array {
        return [
            'confirmDelete'  => __('Tem certeza que deseja excluir?', 'consultoria-platform'),
            'saved'          => __('Salvo com sucesso!', 'consultoria-platform'),
            'error'          => __('Erro ao salvar.', 'consultoria-platform'),
            'loading'        => __('Carregando...', 'consultoria-platform'),
        ];
    }

    private function getPublicTranslations(): array {
        return [
            'confirmAction'  => __('Confirma esta ação?', 'consultoria-platform'),
            'success'        => __('Operação realizada com sucesso!', 'consultoria-platform'),
            'error'          => __('Ocorreu um erro.', 'consultoria-platform'),
            'loading'        => __('Carregando...', 'consultoria-platform'),
            'noResults'      => __('Nenhum resultado encontrado.', 'consultoria-platform'),
        ];
    }
    public function registerShortcodes(): void {
        add_shortcode('cp_marketplace', [$this, 'renderMarketplace']);
        add_shortcode('cp_client_dashboard', [$this, 'renderClientDashboard']);
        add_shortcode('cp_consultant_dashboard', [$this, 'renderConsultantDashboard']);
        add_shortcode('cp_consultant_profile', [$this, 'renderConsultantProfile']);
        add_shortcode('cp_plans', [$this, 'renderPlans']);
        add_shortcode('cp_wallet', [$this, 'renderWallet']);
    }

    public function renderMarketplace(): string {
        ob_start();
        $template = CP_PLUGIN_DIR . 'modules/marketplace/templates/marketplace.php';
        if (file_exists($template)) require $template;
        return ob_get_clean();
    }

    public function renderClientDashboard(): string {
        ob_start();
        echo '<div class="container py-4"><h1 class="h3 fw-bold mb-4">Meu Painel</h1><div class="row g-4">';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-projects">-</div><div class="stat-label">Projetos</div></div></div>';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-proposals">-</div><div class="stat-label">Propostas</div></div></div>';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-spent">R$ -</div><div class="stat-label">Total Gasto</div></div></div>';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-tickets">-</div><div class="stat-label">Tickets</div></div></div>';
        echo '</div></div>';
        return ob_get_clean();
    }

    public function renderConsultantDashboard(): string {
        ob_start();
        echo '<div class="container py-4"><h1 class="h3 fw-bold mb-4">Painel do Consultor</h1><div class="row g-4">';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-projects">-</div><div class="stat-label">Projetos Ativos</div></div></div>';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-earnings">R$ -</div><div class="stat-label">Ganhos do Mês</div></div></div>';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-rating">-</div><div class="stat-label">Avaliação</div></div></div>';
        echo '<div class="col-md-3"><div class="dashboard-card text-center"><div class="stat-value" id="cd-hours">-</div><div class="stat-label">Horas Registradas</div></div></div>';
        echo '</div></div>';
        return ob_get_clean();
    }

    public function renderConsultantProfile(): string {
        ob_start();
        echo '<div class="container py-4">';
        echo '<h1 class="h3 fw-bold mb-4">Perfil do Consultor</h1>';
        echo '<div id="consultant-profile"><div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p class="mt-2 text-muted">Carregando perfil...</p></div></div>';
        echo '</div>';
        return ob_get_clean();
    }

    public function renderPlans(): string {
        ob_start();
        $plans = [
            'basico' => ['name' => 'Básico', 'price' => 49.90, 'features' => ['1 consulta/mês', 'Suporte por e-mail', 'Relatório mensal']],
            'profissional' => ['name' => 'Profissional', 'price' => 99.90, 'features' => ['3 consultas/mês', 'Suporte prioritário', 'Relatórios semanais', 'Dashboard']],
            'enterprise' => ['name' => 'Enterprise', 'price' => 199.90, 'features' => ['Consultas ilimitadas', 'Suporte 24/7', 'Relatórios em tempo real', 'API dedicada', 'Gerente de conta']],
        ];
        echo '<div class="container py-4"><h1 class="h3 fw-bold mb-4 text-center">Planos</h1><div class="row g-4 justify-content-center">';
        foreach ($plans as $slug => $plan) {
            echo '<div class="col-md-4"><div class="card text-center h-100"><div class="card-body">';
            echo '<h5 class="fw-bold">' . $plan['name'] . '</h5>';
            echo '<h2 class="text-primary fw-bold">R$ ' . number_format($plan['price'], 2, ',', '.') . '</h2>';
            echo '<p class="text-muted">por mês</p><ul class="list-unstyled mt-3 mb-4">';
            foreach ($plan['features'] as $f) {
                echo '<li><i class="bi bi-check-circle text-success me-2"></i>' . $f . '</li>';
            }
            echo '</ul><a href="/checkout/?plan=' . $slug . '" class="btn btn-primary w-100">Assinar</a>';
            echo '</div></div></div>';
        }
        echo '</div></div>';
        return ob_get_clean();
    }

    public function renderWallet(): string {
        ob_start();
        $template = CP_PLUGIN_DIR . 'modules/wallet/templates/wallet.php';
        if (file_exists($template)) {
            $wallet = null;
            $transactions = [];
            require $template;
        } else {
            echo '<div class="container py-4"><h1 class="h3 fw-bold mb-4">Minha Carteira</h1><p class="text-muted">Carteira disponível em breve.</p></div>';
        }
        return ob_get_clean();

    }
}



function Consultoria(): ConsultoriaPlatform {
    return ConsultoriaPlatform::getInstance();
}

add_action('plugins_loaded', function () {
    if (!class_exists('WooCommerce')) {
        add_action('admin_notices', function () {
            echo '<div class="error"><p><strong>Consultoria Platform</strong> requer WooCommerce ativo.</p></div>';
        });
        return;
    }
    Consultoria()->init();
});

add_action('init', function () {
    Consultoria()->setupCronJobs();
}, 20);
