<?php

namespace Consultoria;

class Router {

    private array $routes = [];

    public function registerRoutes(): void {
        $this->registerAuthRoutes();
        $this->registerUserRoutes();
        $this->registerConsultantRoutes();
        $this->registerProjectRoutes();
        $this->registerProposalRoutes();
        $this->registerOrderRoutes();
        $this->registerWalletRoutes();
        $this->registerWithdrawalRoutes();
        $this->registerMessageRoutes();
        $this->registerAppointmentRoutes();
        $this->registerReviewRoutes();
        $this->registerContractRoutes();
        $this->registerTicketRoutes();
        $this->registerSLARoutes();
        $this->registerGamificationRoutes();
        $this->registerAffiliateRoutes();
        $this->registerNotificationRoutes();
        $this->registerMarketplaceRoutes();
        $this->registerDashboardRoutes();

        do_action('cp_register_routes', $this);
    }

    private function registerRoute(string $namespace, string $route, array $args): void {
        register_rest_route($namespace, $route, $args);
    }

    private function registerAuthRoutes(): void {
        $this->registerRoute('consultoria/v1', '/auth/login', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleLogin'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/auth/register', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRegister'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/auth/refresh', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRefreshToken'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/auth/me', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetCurrentUser'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerUserRoutes(): void {
        $this->registerRoute('consultoria/v1', '/users/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetUser'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/users/(?P<id>\d+)', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'handleUpdateUser'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerConsultantRoutes(): void {
        $this->registerRoute('consultoria/v1', '/consultants', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetConsultants'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/consultants/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetConsultant'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/consultants/profile', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'handleUpdateConsultantProfile'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/consultants/expertise', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleAddExpertise'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/consultants/certifications', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleAddCertification'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/consultants/availability', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'handleUpdateAvailability'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
    }

    private function registerProjectRoutes(): void {
        $this->registerRoute('consultoria/v1', '/projects', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetProjects'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateProject'],
            'permission_callback' => [$this, 'checkClient'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetProject'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'handleUpdateProject'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)/milestones', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetMilestones'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)/milestones', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateMilestone'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)/time-entries', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetTimeEntries'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)/time-entries', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateTimeEntry'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)/deliverables', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetDeliverables'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)/deliverables', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateDeliverable'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
    }

    private function registerProposalRoutes(): void {
        $this->registerRoute('consultoria/v1', '/proposals', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateProposal'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/proposals/(?P<id>\d+)/accept', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleAcceptProposal'],
            'permission_callback' => [$this, 'checkClient'],
        ]);
        $this->registerRoute('consultoria/v1', '/proposals/(?P<id>\d+)/reject', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRejectProposal'],
            'permission_callback' => [$this, 'checkClient'],
        ]);
    }

    private function registerOrderRoutes(): void {
        $this->registerRoute('consultoria/v1', '/orders', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetOrders'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/plans', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetPlans'],
            'permission_callback' => '__return_true',
        ]);
    }

    private function registerWalletRoutes(): void {
        $this->registerRoute('consultoria/v1', '/wallet', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetWallet'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/wallet/transactions', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetTransactions'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerWithdrawalRoutes(): void {
        $this->registerRoute('consultoria/v1', '/withdrawals', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateWithdrawal'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/withdrawals', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetWithdrawals'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/withdrawals/(?P<id>\d+)/approve', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleApproveWithdrawal'],
            'permission_callback' => [$this, 'checkAdmin'],
        ]);
        $this->registerRoute('consultoria/v1', '/withdrawals/(?P<id>\d+)/reject', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRejectWithdrawal'],
            'permission_callback' => [$this, 'checkAdmin'],
        ]);
    }

    private function registerMessageRoutes(): void {
        $this->registerRoute('consultoria/v1', '/projects/(?P<project_id>\d+)/messages', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetMessages'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<project_id>\d+)/messages', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleSendMessage'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerAppointmentRoutes(): void {
        $this->registerRoute('consultoria/v1', '/appointments', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetAppointments'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/appointments', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateAppointment'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/appointments/(?P<id>\d+)', [
            'methods'             => 'PUT',
            'callback'            => [$this, 'handleUpdateAppointment'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerReviewRoutes(): void {
        $this->registerRoute('consultoria/v1', '/reviews', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateReview'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/consultants/(?P<id>\d+)/reviews', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetConsultantReviews'],
            'permission_callback' => '__return_true',
        ]);
    }

    private function registerContractRoutes(): void {
        $this->registerRoute('consultoria/v1', '/contracts/(?P<id>\d+)/sign', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleSignContract'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/contracts/(?P<id>\d+)/download', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleDownloadContract'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerTicketRoutes(): void {
        $this->registerRoute('consultoria/v1', '/tickets', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetTickets'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/tickets', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateTicket'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/tickets/(?P<id>\d+)/replies', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleReplyTicket'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerNotificationRoutes(): void {
        $this->registerRoute('consultoria/v1', '/notifications', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetNotifications'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/notifications/(?P<id>\d+)/read', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleMarkNotificationRead'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/notifications/read-all', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleMarkAllRead'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerMarketplaceRoutes(): void {
        $this->registerRoute('consultoria/v1', '/marketplace/search', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleMarketplaceSearch'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/marketplace/categories', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetCategories'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/marketplace/matching/(?P<project_id>\d+)', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetMatchingConsultants'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerDashboardRoutes(): void {
        $this->registerRoute('consultoria/v1', '/dashboard/client', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleClientDashboard'],
            'permission_callback' => [$this, 'checkClient'],
        ]);
        $this->registerRoute('consultoria/v1', '/dashboard/consultant', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleConsultantDashboard'],
            'permission_callback' => [$this, 'checkConsultant'],
        ]);
        $this->registerRoute('consultoria/v1', '/dashboard/admin', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleAdminDashboard'],
            'permission_callback' => [$this, 'checkAdmin'],
        ]);
    }

    private function registerSLARoutes(): void {
        $this->registerRoute('consultoria/v1', '/sla/rules', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetSLARules'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/sla/rules', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleCreateSLARule'],
            'permission_callback' => [$this, 'checkAdmin'],
        ]);
        $this->registerRoute('consultoria/v1', '/projects/(?P<id>\d+)/sla', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetProjectSLA'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    private function registerGamificationRoutes(): void {
        $this->registerRoute('consultoria/v1', '/gamification', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetGamification'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/gamification/achievements', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetAchievements'],
            'permission_callback' => '__return_true',
        ]);
        $this->registerRoute('consultoria/v1', '/gamification/ranking', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleGetRanking'],
            'permission_callback' => '__return_true',
        ]);
    }

    private function registerAffiliateRoutes(): void {
        $this->registerRoute('consultoria/v1', '/affiliates/dashboard', [
            'methods'             => 'GET',
            'callback'            => [$this, 'handleAffiliateDashboard'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
        $this->registerRoute('consultoria/v1', '/affiliates/register', [
            'methods'             => 'POST',
            'callback'            => [$this, 'handleRegisterAffiliate'],
            'permission_callback' => [$this, 'checkAuth'],
        ]);
    }

    public function checkAuth(): bool {
        return is_user_logged_in();
    }

    public function checkClient(): bool {
        return $this->checkAuth() && current_user_can('cp_client');
    }

    public function checkConsultant(): bool {
        return $this->checkAuth() && current_user_can('cp_consultant');
    }

    public function checkAdmin(): bool {
        return current_user_can('manage_options');
    }

    public function checkSupport(): bool {
        return $this->checkAuth() && (current_user_can('cp_support') || $this->checkAdmin());
    }

    // --- HANDLERS (stubs that delegate to services) ---

    public function handleLogin(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('auth');
        if ($service) return $service->login($request);
        return \Consultoria\Helpers\Response::error('Auth service not available', 503);
    }

    public function handleRegister(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('auth');
        if ($service) return $service->register($request);
        return \Consultoria\Helpers\Response::error('Auth service not available', 503);
    }

    public function handleRefreshToken(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('auth');
        if ($service) return $service->refreshToken($request);
        return \Consultoria\Helpers\Response::error('Auth service not available', 503);
    }

    public function handleGetCurrentUser(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('user');
        if ($service) return $service->getCurrentUser($request);
        return \Consultoria\Helpers\Response::error('User service not available', 503);
    }

    public function handleGetUser(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('user');
        if ($service) return $service->getUser($request);
        return \Consultoria\Helpers\Response::error('User service not available', 503);
    }

    public function handleUpdateUser(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('user');
        if ($service) return $service->updateUser($request);
        return \Consultoria\Helpers\Response::error('User service not available', 503);
    }

    public function handleGetConsultants(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('consultant');
        if ($service) return $service->getConsultants($request);
        return \Consultoria\Helpers\Response::error('Consultant service not available', 503);
    }

    public function handleGetConsultant(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('consultant');
        if ($service) return $service->getConsultant($request);
        return \Consultoria\Helpers\Response::error('Consultant service not available', 503);
    }

    public function handleUpdateConsultantProfile(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('consultant');
        if ($service) return $service->updateProfile($request);
        return \Consultoria\Helpers\Response::error('Consultant service not available', 503);
    }

    public function handleAddExpertise(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('consultant');
        if ($service) return $service->addExpertise($request);
        return \Consultoria\Helpers\Response::error('Consultant service not available', 503);
    }

    public function handleAddCertification(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('consultant');
        if ($service) return $service->addCertification($request);
        return \Consultoria\Helpers\Response::error('Consultant service not available', 503);
    }

    public function handleUpdateAvailability(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('scheduling');
        if ($service) return $service->updateAvailability($request);
        return \Consultoria\Helpers\Response::error('Scheduling service not available', 503);
    }

    public function handleGetProjects(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->getProjects($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleCreateProject(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->createProject($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleGetProject(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->getProject($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleUpdateProject(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->updateProject($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleGetMilestones(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->getMilestones($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleCreateMilestone(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->createMilestone($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleGetTimeEntries(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->getTimeEntries($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleCreateTimeEntry(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->createTimeEntry($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleGetDeliverables(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->getDeliverables($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleCreateDeliverable(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('project');
        if ($service) return $service->createDeliverable($request);
        return \Consultoria\Helpers\Response::error('Project service not available', 503);
    }

    public function handleCreateProposal(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('proposal');
        if ($service) return $service->createProposal($request);
        return \Consultoria\Helpers\Response::error('Proposal service not available', 503);
    }

    public function handleAcceptProposal(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('proposal');
        if ($service) return $service->acceptProposal($request);
        return \Consultoria\Helpers\Response::error('Proposal service not available', 503);
    }

    public function handleRejectProposal(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('proposal');
        if ($service) return $service->rejectProposal($request);
        return \Consultoria\Helpers\Response::error('Proposal service not available', 503);
    }

    public function handleGetOrders(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('order');
        if ($service) return $service->getOrders($request);
        return \Consultoria\Helpers\Response::error('Order service not available', 503);
    }

    public function handleGetPlans(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('plan');
        if ($service) return $service->getPlans($request);
        return \Consultoria\Helpers\Response::error('Plan service not available', 503);
    }

    public function handleGetWallet(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('wallet');
        if ($service) return $service->getWallet($request);
        return \Consultoria\Helpers\Response::error('Wallet service not available', 503);
    }

    public function handleGetTransactions(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('wallet');
        if ($service) return $service->getTransactions($request);
        return \Consultoria\Helpers\Response::error('Wallet service not available', 503);
    }

    public function handleCreateWithdrawal(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('wallet');
        if ($service) return $service->createWithdrawal($request);
        return \Consultoria\Helpers\Response::error('Wallet service not available', 503);
    }

    public function handleGetWithdrawals(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('wallet');
        if ($service) return $service->getWithdrawals($request);
        return \Consultoria\Helpers\Response::error('Wallet service not available', 503);
    }

    public function handleApproveWithdrawal(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('wallet');
        if ($service) return $service->approveWithdrawal($request);
        return \Consultoria\Helpers\Response::error('Wallet service not available', 503);
    }

    public function handleRejectWithdrawal(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('wallet');
        if ($service) return $service->rejectWithdrawal($request);
        return \Consultoria\Helpers\Response::error('Wallet service not available', 503);
    }

    public function handleGetMessages(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('chat');
        if ($service) return $service->getMessages($request);
        return \Consultoria\Helpers\Response::error('Chat service not available', 503);
    }

    public function handleSendMessage(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('chat');
        if ($service) return $service->sendMessage($request);
        return \Consultoria\Helpers\Response::error('Chat service not available', 503);
    }

    public function handleGetAppointments(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('scheduling');
        if ($service) return $service->getAppointments($request);
        return \Consultoria\Helpers\Response::error('Scheduling service not available', 503);
    }

    public function handleCreateAppointment(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('scheduling');
        if ($service) return $service->createAppointment($request);
        return \Consultoria\Helpers\Response::error('Scheduling service not available', 503);
    }

    public function handleUpdateAppointment(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('scheduling');
        if ($service) return $service->updateAppointment($request);
        return \Consultoria\Helpers\Response::error('Scheduling service not available', 503);
    }

    public function handleCreateReview(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('review');
        if ($service) return $service->createReview($request);
        return \Consultoria\Helpers\Response::error('Review service not available', 503);
    }

    public function handleGetConsultantReviews(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('review');
        if ($service) return $service->getConsultantReviews($request);
        return \Consultoria\Helpers\Response::error('Review service not available', 503);
    }

    public function handleSignContract(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('contract');
        if ($service) return $service->signContract($request);
        return \Consultoria\Helpers\Response::error('Contract service not available', 503);
    }

    public function handleDownloadContract(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('contract');
        if ($service) return $service->downloadContract($request);
        return \Consultoria\Helpers\Response::error('Contract service not available', 503);
    }

    public function handleGetTickets(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('ticket');
        if ($service) return $service->getTickets($request);
        return \Consultoria\Helpers\Response::error('Ticket service not available', 503);
    }

    public function handleCreateTicket(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('ticket');
        if ($service) return $service->createTicket($request);
        return \Consultoria\Helpers\Response::error('Ticket service not available', 503);
    }

    public function handleReplyTicket(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('ticket');
        if ($service) return $service->replyTicket($request);
        return \Consultoria\Helpers\Response::error('Ticket service not available', 503);
    }

    public function handleGetNotifications(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('notification');
        if ($service) return $service->getNotifications($request);
        return \Consultoria\Helpers\Response::error('Notification service not available', 503);
    }

    public function handleMarkNotificationRead(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('notification');
        if ($service) return $service->markAsRead($request);
        return \Consultoria\Helpers\Response::error('Notification service not available', 503);
    }

    public function handleMarkAllRead(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('notification');
        if ($service) return $service->markAllRead($request);
        return \Consultoria\Helpers\Response::error('Notification service not available', 503);
    }

    public function handleMarketplaceSearch(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('marketplace');
        if ($service) return $service->search($request);
        return \Consultoria\Helpers\Response::error('Marketplace service not available', 503);
    }

    public function handleGetCategories(\WP_REST_Request $request): \WP_REST_Response {
        return \Consultoria\Helpers\Response::success(['categories' => $this->getCategoriesList()]);
    }

    public function handleGetMatchingConsultants(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('matching');
        if ($service) return $service->getMatchingConsultants($request);
        return \Consultoria\Helpers\Response::error('Matching service not available', 503);
    }

    public function handleClientDashboard(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('dashboard');
        if ($service) return $service->clientDashboard($request);
        return \Consultoria\Helpers\Response::error('Dashboard service not available', 503);
    }

    public function handleConsultantDashboard(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('dashboard');
        if ($service) return $service->consultantDashboard($request);
        return \Consultoria\Helpers\Response::error('Dashboard service not available', 503);
    }

    public function handleAdminDashboard(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('dashboard');
        if ($service) return $service->adminDashboard($request);
        return \Consultoria\Helpers\Response::error('Dashboard service not available', 503);
    }

    public function handleGetSLARules(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('sla');
        if ($service) return $service->getRules($request);
        return \Consultoria\Helpers\Response::error('SLA service not available', 503);
    }

    public function handleCreateSLARule(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('sla');
        if ($service) return $service->createRule($request);
        return \Consultoria\Helpers\Response::error('SLA service not available', 503);
    }

    public function handleGetProjectSLA(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('sla');
        if ($service) return $service->getProjectSLA($request);
        return \Consultoria\Helpers\Response::error('SLA service not available', 503);
    }

    public function handleGetGamification(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('gamification');
        if ($service) return $service->getGamification($request);
        return \Consultoria\Helpers\Response::error('Gamification service not available', 503);
    }

    public function handleGetAchievements(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('gamification');
        if ($service) return $service->getAchievements($request);
        return \Consultoria\Helpers\Response::error('Gamification service not available', 503);
    }

    public function handleGetRanking(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('gamification');
        if ($service) return $service->getRanking($request);
        return \Consultoria\Helpers\Response::error('Gamification service not available', 503);
    }

    public function handleAffiliateDashboard(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('affiliate');
        if ($service) return $service->dashboard($request);
        return \Consultoria\Helpers\Response::error('Affiliate service not available', 503);
    }

    public function handleRegisterAffiliate(\WP_REST_Request $request): \WP_REST_Response {
        $service = \ConsultoriaPlatform::getInstance()->getService('affiliate');
        if ($service) return $service->register($request);
        return \Consultoria\Helpers\Response::error('Affiliate service not available', 503);
    }

    private function getCategoriesList(): array {
        return [
            ['slug' => 'negocios', 'name' => 'Negócios', 'subcategories' => ['Estratégia', 'Processos', 'Governança', 'Inovação']],
            ['slug' => 'tecnologia', 'name' => 'Tecnologia', 'subcategories' => ['Arquitetura', 'Desenvolvimento', 'Infraestrutura', 'Banco de Dados']],
            ['slug' => 'agilidade', 'name' => 'Agilidade', 'subcategories' => ['Scrum', 'Kanban', 'Lean', 'OKR']],
            ['slug' => 'erp', 'name' => 'ERP', 'subcategories' => ['SAP', 'Oracle', 'TOTVS', 'Microsoft Dynamics']],
            ['slug' => 'crm', 'name' => 'CRM', 'subcategories' => ['Salesforce', 'HubSpot', 'RD Station', 'Zoho']],
            ['slug' => 'bi', 'name' => 'Business Intelligence', 'subcategories' => ['Power BI', 'Tableau', 'Looker', 'Data Analytics']],
            ['slug' => 'cloud', 'name' => 'Cloud', 'subcategories' => ['AWS', 'Azure', 'Google Cloud', 'OCI']],
            ['slug' => 'devops', 'name' => 'DevOps', 'subcategories' => ['CI/CD', 'Kubernetes', 'Docker', 'Terraform']],
            ['slug' => 'seguranca', 'name' => 'Segurança', 'subcategories' => ['LGPD', 'Pentest', 'ISO 27001', 'Compliance']],
            ['slug' => 'marketing', 'name' => 'Marketing', 'subcategories' => ['Growth', 'Mídia Paga', 'SEO', 'Conteúdo']],
            ['slug' => 'ux-ui', 'name' => 'UX/UI', 'subcategories' => ['UX Research', 'UI Design', 'Prototipação', 'Design System']],
            ['slug' => 'ia', 'name' => 'IA & Machine Learning', 'subcategories' => ['LLMs', 'Computer Vision', 'NLP', 'Deep Learning']],
            ['slug' => 'product', 'name' => 'Product Management', 'subcategories' => ['Estratégia', 'Roadmap', 'Descoberta', 'Métricas']],
            ['slug' => 'financeiro', 'name' => 'Financeiro', 'subcategories' => ['FP&A', 'Contabilidade', 'Tributário', 'Tesouraria']],
            ['slug' => 'rh', 'name' => 'RH', 'subcategories' => ['Recrutamento', 'CLT', 'Benefícios', 'Cultura']],
        ];
    }
}
