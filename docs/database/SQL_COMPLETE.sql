-- ==========================================================
-- PLATAFORMA DE CONSULTORIA SAAS
-- Estrutura Completa do Banco de Dados
-- Versão: 1.0.0
-- Motor: MySQL 8+ / MariaDB 10.6+
-- Charset: utf8mb4 | Collation: utf8mb4_unicode_ci
-- ==========================================================

-- ----------------------------------------------------------
-- 1. USUÁRIOS (extends wp_users)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_users` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `wp_user_id` BIGINT UNSIGNED NOT NULL,
    `role` ENUM('client', 'consultant', 'support', 'admin') NOT NULL DEFAULT 'client',
    `status` ENUM('active', 'inactive', 'blocked', 'pending_approval') NOT NULL DEFAULT 'pending_approval',
    `avatar` VARCHAR(500) DEFAULT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `language` VARCHAR(10) DEFAULT 'pt_BR',
    `timezone` VARCHAR(50) DEFAULT 'America/Sao_Paulo',
    `twofa_enabled` TINYINT(1) NOT NULL DEFAULT 0,
    `twofa_secret` VARCHAR(255) DEFAULT NULL,
    `email_verified_at` DATETIME DEFAULT NULL,
    `last_login_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_wp_user_id` (`wp_user_id`),
    KEY `idx_role` (`role`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_users_wp_user` FOREIGN KEY (`wp_user_id`) REFERENCES `wp_users` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 2. CLIENTES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_clients` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `company_name` VARCHAR(255) DEFAULT NULL,
    `cnpj` VARCHAR(18) DEFAULT NULL,
    `ie` VARCHAR(20) DEFAULT NULL,
    `company_size` VARCHAR(50) DEFAULT NULL,
    `industry` VARCHAR(100) DEFAULT NULL,
    `website` VARCHAR(500) DEFAULT NULL,
    `billing_address_line1` VARCHAR(255) DEFAULT NULL,
    `billing_address_line2` VARCHAR(255) DEFAULT NULL,
    `billing_city` VARCHAR(100) DEFAULT NULL,
    `billing_state` VARCHAR(50) DEFAULT NULL,
    `billing_zipcode` VARCHAR(10) DEFAULT NULL,
    `billing_country` VARCHAR(2) DEFAULT 'BR',
    `stripe_customer_id` VARCHAR(100) DEFAULT NULL,
    `total_spent` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_orders` INT NOT NULL DEFAULT 0,
    `notes` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_id` (`user_id`),
    UNIQUE KEY `uk_cnpj` (`cnpj`),
    KEY `idx_stripe_customer` (`stripe_customer_id`),
    CONSTRAINT `fk_cp_clients_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 3. CONSULTORES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_consultants` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `professional_title` VARCHAR(200) DEFAULT NULL,
    `bio` TEXT DEFAULT NULL,
    `short_bio` VARCHAR(500) DEFAULT NULL,
    `linkedin_url` VARCHAR(500) DEFAULT NULL,
    `github_url` VARCHAR(500) DEFAULT NULL,
    `portfolio_url` VARCHAR(500) DEFAULT NULL,
    `languages` JSON DEFAULT NULL,
    `hourly_rate` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'BRL',
    `experience_years` INT NOT NULL DEFAULT 0,
    `rating` DECIMAL(3,2) NOT NULL DEFAULT 0.00,
    `rating_count` INT NOT NULL DEFAULT 0,
    `level` ENUM('iniciante', 'especialista', 'senior', 'master', 'elite', 'diamond') NOT NULL DEFAULT 'iniciante',
    `total_hours_worked` INT NOT NULL DEFAULT 0,
    `total_projects` INT NOT NULL DEFAULT 0,
    `total_revenue` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `avg_response_time` INT DEFAULT NULL COMMENT 'Em minutos',
    `avg_delivery_time` INT DEFAULT NULL COMMENT 'Em horas',
    `completion_rate` DECIMAL(5,2) DEFAULT 100.00,
    `stripe_account_id` VARCHAR(100) DEFAULT NULL,
    `stripe_account_status` ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    `availability` ENUM('full_time', 'part_time', 'custom') NOT NULL DEFAULT 'full_time',
    `availability_hours` JSON DEFAULT NULL,
    `status` ENUM('pending', 'active', 'blocked', 'suspended') NOT NULL DEFAULT 'pending',
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `onboarding_completed` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_id` (`user_id`),
    KEY `idx_status` (`status`),
    KEY `idx_rating` (`rating`),
    KEY `idx_level` (`level`),
    KEY `idx_hourly_rate` (`hourly_rate`),
    KEY `idx_featured` (`featured`),
    CONSTRAINT `fk_cp_consultants_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 4. ESPECIALIDADES DOS CONSULTORES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_expertise` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `category` VARCHAR(100) NOT NULL,
    `subcategory` VARCHAR(100) DEFAULT NULL,
    `experience_years` INT NOT NULL DEFAULT 0,
    `description` TEXT DEFAULT NULL,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_category` (`category`),
    KEY `idx_subcategory` (`subcategory`),
    CONSTRAINT `fk_cp_expertise_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 5. CERTIFICAÇÕES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_certifications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(200) NOT NULL,
    `issuer` VARCHAR(200) NOT NULL,
    `credential_url` VARCHAR(500) DEFAULT NULL,
    `credential_id` VARCHAR(100) DEFAULT NULL,
    `issued_at` DATE NOT NULL,
    `expires_at` DATE DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_consultant_id` (`consultant_id`),
    CONSTRAINT `fk_cp_certifications_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 6. PORTFÓLIO
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_portfolio_items` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `image_url` VARCHAR(500) DEFAULT NULL,
    `project_url` VARCHAR(500) DEFAULT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `technologies` JSON DEFAULT NULL,
    `featured` TINYINT(1) NOT NULL DEFAULT 0,
    `sort_order` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_consultant_id` (`consultant_id`),
    CONSTRAINT `fk_cp_portfolio_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 7. PLANOS DE SERVIÇO (Pacotes de Horas)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_service_plans` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `hours` INT NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `price_per_hour` DECIMAL(10,2) GENERATED ALWAYS AS (price / hours) STORED,
    `validity_days` INT NOT NULL DEFAULT 365,
    `features` JSON DEFAULT NULL,
    `highlighted` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive', 'archived') NOT NULL DEFAULT 'active',
    `sort_order` INT NOT NULL DEFAULT 0,
    `woocommerce_product_id` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`),
    KEY `idx_status` (`status`),
    KEY `idx_sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 8. PEDIDOS (integração WooCommerce)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_orders` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `woocommerce_order_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `plan_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('pending', 'processing', 'completed', 'cancelled', 'refunded') NOT NULL DEFAULT 'pending',
    `total` DECIMAL(10,2) NOT NULL,
    `hours` INT NOT NULL,
    `hours_used` INT NOT NULL DEFAULT 0,
    `hours_remaining` INT GENERATED ALWAYS AS (hours - hours_used) STORED,
    `expires_at` DATETIME NOT NULL,
    `stripe_payment_intent_id` VARCHAR(100) DEFAULT NULL,
    `stripe_transfer_id` VARCHAR(100) DEFAULT NULL,
    `paid_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_woocommerce_order` (`woocommerce_order_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_plan_id` (`plan_id`),
    KEY `idx_status` (`status`),
    KEY `idx_expires_at` (`expires_at`),
    CONSTRAINT `fk_cp_orders_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_orders_plan` FOREIGN KEY (`plan_id`) REFERENCES `cp_service_plans` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 9. PROJETOS (Demandas)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_projects` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id` BIGINT UNSIGNED NOT NULL,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `consultant_id` BIGINT UNSIGNED DEFAULT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `category` VARCHAR(100) NOT NULL,
    `subcategory` VARCHAR(100) DEFAULT NULL,
    `scope` ENUM('consultoria', 'desenvolvimento', 'implantacao', 'suporte', 'treinamento') NOT NULL DEFAULT 'consultoria',
    `estimated_hours` INT DEFAULT NULL,
    `status` ENUM('open', 'proposals', 'in_progress', 'review', 'completed', 'cancelled', 'disputed') NOT NULL DEFAULT 'open',
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `total_hours_used` INT NOT NULL DEFAULT 0,
    `budget` DECIMAL(10,2) DEFAULT NULL,
    `start_date` DATETIME DEFAULT NULL,
    `end_date` DATETIME DEFAULT NULL,
    `contract_id` BIGINT UNSIGNED DEFAULT NULL,
    `sla_id` BIGINT UNSIGNED DEFAULT NULL,
    `stripe_transfer_group` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_order_id` (`order_id`),
    KEY `idx_client_id` (`client_id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_status` (`status`),
    KEY `idx_category` (`category`),
    KEY `idx_priority` (`priority`),
    CONSTRAINT `fk_cp_projects_order` FOREIGN KEY (`order_id`) REFERENCES `cp_orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_projects_client` FOREIGN KEY (`client_id`) REFERENCES `cp_clients` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_projects_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 10. PROPOSTAS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_proposals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `value` DECIMAL(10,2) NOT NULL,
    `estimated_hours` INT NOT NULL,
    `message` TEXT DEFAULT NULL,
    `delivery_estimate` INT DEFAULT NULL COMMENT 'Dias estimados',
    `status` ENUM('pending', 'accepted', 'rejected', 'withdrawn', 'countered') NOT NULL DEFAULT 'pending',
    `client_notes` TEXT DEFAULT NULL,
    `viewed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_proposals_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_proposals_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE,
    UNIQUE KEY `uk_project_consultant` (`project_id`, `consultant_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 11. CONTRATOS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_contracts` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `proposal_id` BIGINT UNSIGNED NOT NULL,
    `document_url` VARCHAR(500) DEFAULT NULL,
    `document_id` VARCHAR(100) DEFAULT NULL COMMENT 'ID no Docuseal/OpenSign',
    `signed_by_client` TINYINT(1) NOT NULL DEFAULT 0,
    `signed_by_consultant` TINYINT(1) NOT NULL DEFAULT 0,
    `client_signed_at` DATETIME DEFAULT NULL,
    `consultant_signed_at` DATETIME DEFAULT NULL,
    `signed_at` DATETIME DEFAULT NULL,
    `status` ENUM('pending', 'partial', 'signed', 'cancelled', 'expired') NOT NULL DEFAULT 'pending',
    `contract_content` LONGTEXT DEFAULT NULL COMMENT 'Texto completo do contrato',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_project_id` (`project_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_contracts_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_contracts_proposal` FOREIGN KEY (`proposal_id`) REFERENCES `cp_proposals` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 12. MARCOS (Milestones)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_milestones` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `hours_estimated` INT NOT NULL DEFAULT 0,
    `due_date` DATE DEFAULT NULL,
    `status` ENUM('pending', 'in_progress', 'completed', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `sort_order` INT NOT NULL DEFAULT 0,
    `completed_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_milestones_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 13. ENTREGAS (Deliverables)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_deliverables` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `milestone_id` BIGINT UNSIGNED DEFAULT NULL,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `file_url` VARCHAR(500) DEFAULT NULL,
    `file_type` VARCHAR(50) DEFAULT NULL,
    `file_size` INT DEFAULT NULL COMMENT 'Em bytes',
    `status` ENUM('pending', 'submitted', 'approved', 'rejected', 'revision') NOT NULL DEFAULT 'pending',
    `feedback` TEXT DEFAULT NULL,
    `approved_by` BIGINT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `version` INT NOT NULL DEFAULT 1,
    `submitted_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_milestone_id` (`milestone_id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_deliverables_milestone` FOREIGN KEY (`milestone_id`) REFERENCES `cp_milestones` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_cp_deliverables_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_deliverables_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `cp_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 14. LANÇAMENTOS DE HORAS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_time_entries` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `milestone_id` BIGINT UNSIGNED DEFAULT NULL,
    `date` DATE NOT NULL,
    `start_time` TIME DEFAULT NULL,
    `end_time` TIME DEFAULT NULL,
    `hours` DECIMAL(5,2) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `billable` TINYINT(1) NOT NULL DEFAULT 1,
    `status` ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    `approved_by` BIGINT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `rejection_reason` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_milestone_id` (`milestone_id`),
    KEY `idx_date` (`date`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_time_entries_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_time_entries_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_time_entries_milestone` FOREIGN KEY (`milestone_id`) REFERENCES `cp_milestones` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_cp_time_entries_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `cp_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 15. MENSAGENS (Chat)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_messages` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `sender_id` BIGINT UNSIGNED NOT NULL,
    `content` TEXT NOT NULL,
    `message_type` ENUM('text', 'image', 'file', 'audio', 'video', 'system') NOT NULL DEFAULT 'text',
    `file_url` VARCHAR(500) DEFAULT NULL,
    `file_name` VARCHAR(255) DEFAULT NULL,
    `file_size` INT DEFAULT NULL,
    `read_at` DATETIME DEFAULT NULL,
    `edited_at` DATETIME DEFAULT NULL,
    `parent_id` BIGINT UNSIGNED DEFAULT NULL COMMENT 'Resposta a mensagem',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_sender_id` (`sender_id`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_cp_messages_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_messages_sender` FOREIGN KEY (`sender_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_messages_parent` FOREIGN KEY (`parent_id`) REFERENCES `cp_messages` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 16. AGENDAMENTOS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_appointments` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `client_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `type` ENUM('videoconference', 'phone', 'in_person', 'chat') NOT NULL DEFAULT 'videoconference',
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME NOT NULL,
    `timezone` VARCHAR(50) DEFAULT 'America/Sao_Paulo',
    `meeting_url` VARCHAR(500) DEFAULT NULL,
    `meeting_provider` VARCHAR(50) DEFAULT NULL,
    `meeting_id` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('scheduled', 'confirmed', 'in_progress', 'completed', 'cancelled', 'rescheduled') NOT NULL DEFAULT 'scheduled',
    `cancellation_reason` TEXT DEFAULT NULL,
    `reminder_sent` TINYINT(1) NOT NULL DEFAULT 0,
    `google_event_id` VARCHAR(255) DEFAULT NULL,
    `outlook_event_id` VARCHAR(255) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_client_id` (`client_id`),
    KEY `idx_start_time` (`start_time`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_appointments_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_appointments_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_appointments_client` FOREIGN KEY (`client_id`) REFERENCES `cp_clients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 17. AVALIAÇÕES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_reviews` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `reviewer_id` BIGINT UNSIGNED NOT NULL COMMENT 'Quem avaliou (cliente ou consultor)',
    `target_id` BIGINT UNSIGNED NOT NULL COMMENT 'Quem foi avaliado',
    `rating` TINYINT UNSIGNED NOT NULL CHECK (rating BETWEEN 1 AND 5),
    `quality` TINYINT UNSIGNED DEFAULT NULL CHECK (quality BETWEEN 1 AND 5),
    `communication` TINYINT UNSIGNED DEFAULT NULL CHECK (communication BETWEEN 1 AND 5),
    `deadline` TINYINT UNSIGNED DEFAULT NULL CHECK (deadline BETWEEN 1 AND 5),
    `comment` TEXT DEFAULT NULL,
    `public_response` TEXT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_project_reviewer` (`project_id`, `reviewer_id`),
    KEY `idx_reviewer_id` (`reviewer_id`),
    KEY `idx_target_id` (`target_id`),
    KEY `idx_rating` (`rating`),
    CONSTRAINT `fk_cp_reviews_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_reviews_reviewer` FOREIGN KEY (`reviewer_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_reviews_target` FOREIGN KEY (`target_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 18. CARTEIRA DIGITAL
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_wallets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `blocked_balance` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `available_balance` DECIMAL(12,2) GENERATED ALWAYS AS (balance - blocked_balance) STORED,
    `total_earned` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_withdrawn` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_spent` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'BRL',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_id` (`user_id`),
    CONSTRAINT `fk_cp_wallets_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 19. TRANSAÇÕES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_transactions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `wallet_id` BIGINT UNSIGNED NOT NULL,
    `type` ENUM('credit', 'debit', 'fee', 'refund', 'transfer', 'commission', 'cashback', 'bonus') NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `balance_before` DECIMAL(12,2) NOT NULL,
    `balance_after` DECIMAL(12,2) NOT NULL,
    `fee_platform` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `fee_stripe` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `reference_id` BIGINT UNSIGNED DEFAULT NULL,
    `description` VARCHAR(500) DEFAULT NULL,
    `status` ENUM('pending', 'completed', 'failed', 'cancelled') NOT NULL DEFAULT 'completed',
    `stripe_transaction_id` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_wallet_id` (`wallet_id`),
    KEY `idx_type` (`type`),
    KEY `idx_reference` (`reference_type`, `reference_id`),
    KEY `idx_status` (`status`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_cp_transactions_wallet` FOREIGN KEY (`wallet_id`) REFERENCES `cp_wallets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 20. SAQUES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_withdrawals` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `wallet_id` BIGINT UNSIGNED NOT NULL,
    `amount` DECIMAL(12,2) NOT NULL,
    `fee` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `net_amount` DECIMAL(12,2) GENERATED ALWAYS AS (amount - fee) STORED,
    `pix_key` VARCHAR(100) DEFAULT NULL,
    `pix_key_type` ENUM('cpf', 'cnpj', 'email', 'phone', 'random') DEFAULT NULL,
    `bank_info` JSON DEFAULT NULL,
    `status` ENUM('pending', 'processing', 'approved', 'rejected', 'cancelled', 'completed') NOT NULL DEFAULT 'pending',
    `rejection_reason` TEXT DEFAULT NULL,
    `approved_by` BIGINT UNSIGNED DEFAULT NULL,
    `approved_at` DATETIME DEFAULT NULL,
    `paid_at` DATETIME DEFAULT NULL,
    `stripe_transfer_id` VARCHAR(100) DEFAULT NULL,
    `requested_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_wallet_id` (`wallet_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_withdrawals_wallet` FOREIGN KEY (`wallet_id`) REFERENCES `cp_wallets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_withdrawals_approver` FOREIGN KEY (`approved_by`) REFERENCES `cp_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 21. REGRAS SLA
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_sla_rules` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `scope` ENUM('consultoria', 'desenvolvimento', 'implantacao', 'suporte', 'treinamento') NOT NULL DEFAULT 'consultoria',
    `response_time_hours` INT NOT NULL DEFAULT 24,
    `accept_time_hours` INT NOT NULL DEFAULT 48,
    `delivery_time_hours` INT NOT NULL DEFAULT 168,
    `review_time_hours` INT NOT NULL DEFAULT 48,
    `close_time_hours` INT NOT NULL DEFAULT 24,
    `auto_escalation` TINYINT(1) NOT NULL DEFAULT 1,
    `escalation_delay_hours` INT NOT NULL DEFAULT 24,
    `escalation_to` ENUM('support', 'admin') NOT NULL DEFAULT 'admin',
    `penalty_percentage` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `is_default` TINYINT(1) NOT NULL DEFAULT 0,
    `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_category` (`category`),
    KEY `idx_scope` (`scope`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 22. MONITORAMENTO SLA
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_sla_monitor` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `rule_id` BIGINT UNSIGNED NOT NULL,
    `status` ENUM('active', 'breached', 'warning', 'completed') NOT NULL DEFAULT 'active',
    `responded_at` DATETIME DEFAULT NULL,
    `response_deadline` DATETIME DEFAULT NULL,
    `response_breached` TINYINT(1) NOT NULL DEFAULT 0,
    `accepted_at` DATETIME DEFAULT NULL,
    `accept_deadline` DATETIME DEFAULT NULL,
    `accept_breached` TINYINT(1) NOT NULL DEFAULT 0,
    `delivered_at` DATETIME DEFAULT NULL,
    `delivery_deadline` DATETIME DEFAULT NULL,
    `delivery_breached` TINYINT(1) NOT NULL DEFAULT 0,
    `reviewed_at` DATETIME DEFAULT NULL,
    `review_deadline` DATETIME DEFAULT NULL,
    `review_breached` TINYINT(1) NOT NULL DEFAULT 0,
    `closed_at` DATETIME DEFAULT NULL,
    `close_deadline` DATETIME DEFAULT NULL,
    `close_breached` TINYINT(1) NOT NULL DEFAULT 0,
    `escalated` TINYINT(1) NOT NULL DEFAULT 0,
    `escalated_at` DATETIME DEFAULT NULL,
    `escalated_to` BIGINT UNSIGNED DEFAULT NULL,
    `penalty_applied` TINYINT(1) NOT NULL DEFAULT 0,
    `penalty_amount` DECIMAL(10,2) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_project_rule` (`project_id`, `rule_id`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_sla_monitor_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_sla_monitor_rule` FOREIGN KEY (`rule_id`) REFERENCES `cp_sla_rules` (`id`) ON DELETE RESTRICT,
    CONSTRAINT `fk_cp_sla_monitor_escalated` FOREIGN KEY (`escalated_to`) REFERENCES `cp_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 23. TICKETS (Suporte)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_tickets` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `assigned_to` BIGINT UNSIGNED DEFAULT NULL,
    `subject` VARCHAR(255) NOT NULL,
    `description` TEXT NOT NULL,
    `priority` ENUM('low', 'medium', 'high', 'urgent') NOT NULL DEFAULT 'medium',
    `category` VARCHAR(100) DEFAULT NULL,
    `status` ENUM('open', 'in_progress', 'waiting_client', 'waiting_support', 'resolved', 'closed') NOT NULL DEFAULT 'open',
    `internal_notes` TEXT DEFAULT NULL,
    `closed_at` DATETIME DEFAULT NULL,
    `closed_by` BIGINT UNSIGNED DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_assigned_to` (`assigned_to`),
    KEY `idx_status` (`status`),
    KEY `idx_priority` (`priority`),
    CONSTRAINT `fk_cp_tickets_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_tickets_assigned` FOREIGN KEY (`assigned_to`) REFERENCES `cp_users` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_cp_tickets_closed_by` FOREIGN KEY (`closed_by`) REFERENCES `cp_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 24. RESPOSTAS DE TICKETS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_ticket_replies` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `ticket_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `content` TEXT NOT NULL,
    `is_internal` TINYINT(1) NOT NULL DEFAULT 0,
    `attachments` JSON DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_ticket_id` (`ticket_id`),
    KEY `idx_user_id` (`user_id`),
    CONSTRAINT `fk_cp_ticket_replies_ticket` FOREIGN KEY (`ticket_id`) REFERENCES `cp_tickets` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_ticket_replies_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 25. GAMIFICAÇÃO
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_gamification` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `level` ENUM('iniciante', 'especialista', 'senior', 'master', 'elite', 'diamond') NOT NULL DEFAULT 'iniciante',
    `xp` INT NOT NULL DEFAULT 0,
    `xp_next_level` INT NOT NULL DEFAULT 1000,
    `total_xp_earned` INT NOT NULL DEFAULT 0,
    `ranking_position` INT DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_id` (`user_id`),
    KEY `idx_level` (`level`),
    KEY `idx_xp` (`xp`),
    KEY `idx_ranking` (`ranking_position`),
    CONSTRAINT `fk_cp_gamification_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 26. BADGES / ACHIEVEMENTS (Conquistas)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_achievements` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `icon_url` VARCHAR(500) DEFAULT NULL,
    `type` ENUM('rating', 'projects', 'hours', 'revenue', 'speed', 'special') NOT NULL,
    `criteria` VARCHAR(50) NOT NULL,
    `criteria_value` INT NOT NULL,
    `xp_reward` INT NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_slug` (`slug`),
    KEY `idx_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 27. BADGES DOS USUÁRIOS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_user_badges` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `achievement_id` BIGINT UNSIGNED NOT NULL,
    `earned_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_achievement` (`user_id`, `achievement_id`),
    KEY `idx_achievement_id` (`achievement_id`),
    CONSTRAINT `fk_cp_user_badges_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_user_badges_achievement` FOREIGN KEY (`achievement_id`) REFERENCES `cp_achievements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 28. AFILIADOS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_affiliates` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `code` VARCHAR(50) NOT NULL,
    `commission_rate` DECIMAL(5,2) NOT NULL DEFAULT 10.00,
    `total_clicks` INT NOT NULL DEFAULT 0,
    `total_conversions` INT NOT NULL DEFAULT 0,
    `total_revenue` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `total_commission` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    `status` ENUM('active', 'inactive', 'blocked') NOT NULL DEFAULT 'active',
    `payment_method` VARCHAR(50) DEFAULT NULL,
    `payment_info` JSON DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_user_id` (`user_id`),
    UNIQUE KEY `uk_code` (`code`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_affiliates_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 29. CLIQUES DE AFILIADOS
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_affiliate_clicks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `affiliate_id` BIGINT UNSIGNED NOT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `referrer_url` VARCHAR(500) DEFAULT NULL,
    `landing_url` VARCHAR(500) DEFAULT NULL,
    `converted` TINYINT(1) NOT NULL DEFAULT 0,
    `converted_at` DATETIME DEFAULT NULL,
    `order_id` BIGINT UNSIGNED DEFAULT NULL,
    `commission_earned` DECIMAL(10,2) DEFAULT 0.00,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_affiliate_id` (`affiliate_id`),
    KEY `idx_converted` (`converted`),
    CONSTRAINT `fk_cp_affiliate_clicks_affiliate` FOREIGN KEY (`affiliate_id`) REFERENCES `cp_affiliates` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_affiliate_clicks_order` FOREIGN KEY (`order_id`) REFERENCES `cp_orders` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 30. REGRAS DE CASHBACK
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_cashback_rules` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `percentage` DECIMAL(5,2) NOT NULL,
    `min_order_value` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `max_cashback` DECIMAL(10,2) DEFAULT NULL,
    `valid_from` DATETIME DEFAULT NULL,
    `valid_until` DATETIME DEFAULT NULL,
    `status` ENUM('active', 'inactive', 'expired') NOT NULL DEFAULT 'active',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_status` (`status`),
    KEY `idx_valid_from` (`valid_from`),
    KEY `idx_valid_until` (`valid_until`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 31. NOTIFICAÇÕES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_notifications` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `type` VARCHAR(50) NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT DEFAULT NULL,
    `reference_type` VARCHAR(50) DEFAULT NULL,
    `reference_id` BIGINT UNSIGNED DEFAULT NULL,
    `read_at` DATETIME DEFAULT NULL,
    `sent_via` JSON DEFAULT NULL COMMENT '["email", "push", "whatsapp", "internal"]',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_type` (`type`),
    KEY `idx_read_at` (`read_at`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_cp_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 32. DISPONIBILIDADE DOS CONSULTORES
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_consultant_availability` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `day_of_week` TINYINT UNSIGNED NOT NULL CHECK (day_of_week BETWEEN 0 AND 6),
    `start_time` TIME NOT NULL,
    `end_time` TIME NOT NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_day_of_week` (`day_of_week`),
    CONSTRAINT `fk_cp_consultant_availability_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 33. FERIADOS / BLOQUEIOS NA AGENDA
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_availability_blocks` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `title` VARCHAR(255) DEFAULT NULL,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NOT NULL,
    `reason` VARCHAR(100) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_start_date` (`start_date`),
    CONSTRAINT `fk_cp_availability_blocks_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 34. REGISTRO DE LOGS DE AUDITORIA
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_audit_logs` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT UNSIGNED DEFAULT NULL,
    `action` VARCHAR(100) NOT NULL,
    `entity_type` VARCHAR(50) NOT NULL,
    `entity_id` BIGINT UNSIGNED DEFAULT NULL,
    `old_value` JSON DEFAULT NULL,
    `new_value` JSON DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `user_agent` VARCHAR(500) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_action` (`action`),
    KEY `idx_entity` (`entity_type`, `entity_id`),
    KEY `idx_created_at` (`created_at`),
    CONSTRAINT `fk_cp_audit_logs_user` FOREIGN KEY (`user_id`) REFERENCES `cp_users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 35. MÉTRICAS DE MATCHING IA (Score)
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_matching_scores` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `project_id` BIGINT UNSIGNED NOT NULL,
    `consultant_id` BIGINT UNSIGNED NOT NULL,
    `score` DECIMAL(5,2) NOT NULL COMMENT '0.00 a 100.00',
    `expertise_score` DECIMAL(5,2) DEFAULT 0.00,
    `rating_score` DECIMAL(5,2) DEFAULT 0.00,
    `availability_score` DECIMAL(5,2) DEFAULT 0.00,
    `budget_score` DECIMAL(5,2) DEFAULT 0.00,
    `language_score` DECIMAL(5,2) DEFAULT 0.00,
    `history_score` DECIMAL(5,2) DEFAULT 0.00,
    `proximity_score` DECIMAL(5,2) DEFAULT 0.00,
    `model_version` VARCHAR(20) DEFAULT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_project_id` (`project_id`),
    KEY `idx_consultant_id` (`consultant_id`),
    KEY `idx_score` (`score`),
    CONSTRAINT `fk_cp_matching_scores_project` FOREIGN KEY (`project_id`) REFERENCES `cp_projects` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_cp_matching_scores_consultant` FOREIGN KEY (`consultant_id`) REFERENCES `cp_consultants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 36. SESSÕES DE VIDEOCONFERÊNCIA
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_video_sessions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `appointment_id` BIGINT UNSIGNED NOT NULL,
    `provider` VARCHAR(50) NOT NULL,
    `session_id` VARCHAR(255) NOT NULL,
    `room_url` VARCHAR(500) DEFAULT NULL,
    `recording_url` VARCHAR(500) DEFAULT NULL,
    `duration_seconds` INT DEFAULT NULL,
    `participant_count` INT DEFAULT 0,
    `status` ENUM('created', 'active', 'ended', 'recorded') NOT NULL DEFAULT 'created',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_appointment_id` (`appointment_id`),
    KEY `idx_provider` (`provider`),
    KEY `idx_status` (`status`),
    CONSTRAINT `fk_cp_video_sessions_appointment` FOREIGN KEY (`appointment_id`) REFERENCES `cp_appointments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- 37. CONFIGURAÇÕES DA PLATAFORMA
-- ----------------------------------------------------------
CREATE TABLE IF NOT EXISTS `cp_settings` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `key_name` VARCHAR(100) NOT NULL,
    `key_value` LONGTEXT DEFAULT NULL,
    `autoload` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_key_name` (`key_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ----------------------------------------------------------
-- ÍNDICES ADICIONAIS PARA PERFORMANCE
-- ----------------------------------------------------------
CREATE INDEX idx_projects_created_at ON cp_projects (created_at DESC);
CREATE INDEX idx_orders_created_at ON cp_orders (created_at DESC);
CREATE INDEX idx_transactions_created_at ON cp_transactions (created_at DESC);
CREATE INDEX idx_messages_project_created ON cp_messages (project_id, created_at);
CREATE INDEX idx_notifications_user_read ON cp_notifications (user_id, read_at);
CREATE INDEX idx_reviews_target_rating ON cp_reviews (target_id, rating);
CREATE INDEX idx_matching_scores_project_score ON cp_matching_scores (project_id, score DESC);
CREATE INDEX idx_portfolio_featured ON cp_portfolio_items (consultant_id, featured);

-- ----------------------------------------------------------
-- DADOS INICIAIS - PLANOS
-- ----------------------------------------------------------
INSERT INTO `cp_service_plans` (`name`, `slug`, `description`, `hours`, `price`, `validity_days`, `highlighted`, `sort_order`) VALUES
('Bronze', 'bronze', 'Pacote ideal para consultorias pontuais e diagnósticos rápidos.', 10, 1500.00, 90, 0, 1),
('Prata', 'prata', 'Pacote recomendado para projetos de médio porte com acompanhamento contínuo.', 20, 2800.00, 180, 0, 2),
('Ouro', 'ouro', 'Pacote ideal para projetos estruturados com entregas semanais.', 50, 6500.00, 270, 1, 3),
('Enterprise', 'enterprise', 'Pacote completo para grandes projetos com suporte dedicado e prioridade máxima.', 100, 12000.00, 365, 0, 4);

-- ----------------------------------------------------------
-- DADOS INICIAIS - ACHIEVEMENTS
-- ----------------------------------------------------------
INSERT INTO `cp_achievements` (`name`, `slug`, `description`, `type`, `criteria`, `criteria_value`, `xp_reward`) VALUES
('Top Avaliado', 'top-avaliado', 'Manter média de avaliação acima de 4.5 por 30 dias', 'rating', 'avg_rating', 45, 500),
('Especialista Verificado', 'especialista-verificado', 'Completar o onboarding com todas as certificações', 'special', 'onboarding_complete', 1, 200),
('Resposta Rápida', 'resposta-rapida', 'Responder propostas em menos de 1 hora', 'speed', 'response_time', 60, 300),
('100 Projetos', '100-projetos', 'Completar 100 projetos na plataforma', 'projects', 'total_projects', 100, 1000),
('500 Horas', '500-horas', 'Acumular 500 horas trabalhadas', 'hours', 'total_hours', 500, 800),
('Mestre do Marketplace', 'mestre-marketplace', 'Faturar mais de R$ 50.000 na plataforma', 'revenue', 'total_revenue', 50000, 1500),
('Primeiro Projeto', 'primeiro-projeto', 'Completar o primeiro projeto com sucesso', 'projects', 'total_projects', 1, 100),
('Cinco Estrelas', 'cinco-estrelas', 'Receber 10 avaliações 5 estrelas consecutivas', 'rating', 'five_star_count', 10, 400);

-- ----------------------------------------------------------
-- DADOS INICIAIS - CONFIGURAÇÕES
-- ----------------------------------------------------------
INSERT INTO `cp_settings` (`key_name`, `key_value`, `autoload`) VALUES
('platform_name', 'Consultoria SaaS', 1),
('platform_commission_rate', '20.00', 1),
('platform_withdrawal_fee', '5.00', 1),
('min_withdrawal_amount', '50.00', 1),
('max_withdrawal_amount', '50000.00', 1),
('default_sla_rule_id', '1', 1),
('cashback_default_percentage', '5.00', 1),
('affiliate_default_commission', '10.00', 1),
('matching_min_score', '30.00', 1),
('matching_top_n', '5', 1),
('trial_hours_enabled', '1', 1),
('trial_hours_amount', '2', 1),
('maintenance_mode', '0', 0),
('stripe_connect_fee_percentage', '2.90', 1),
('stripe_connect_fee_fixed', '0.50', 1),
('platform_support_email', 'suporte@consultoriasaas.com.br', 1);
