<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?php echo esc_url(home_url()); ?>">
                <?php echo get_bloginfo('name'); ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'navbar-nav ms-auto',
                    'fallback_cb'    => false,
                    'depth'          => 2,
                    'walker'         => new \WP_Bootstrap_Navwalker(),
                ]);
                ?>

                <div class="d-flex ms-3 gap-2">
                    <?php if (is_user_logged_in()) : ?>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <?php echo esc_html(wp_get_current_user()->display_name); ?>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <?php if (current_user_can('cp_consultant')) : ?>
                                    <li><a class="dropdown-item" href="<?php echo home_url('/dashboard-consultor/'); ?>"><i class="bi bi-speedometer2"></i> Painel</a></li>
                                    <li><a class="dropdown-item" href="<?php echo home_url('/minha-carteira/'); ?>"><i class="bi bi-wallet2"></i> Carteira</a></li>
                                <?php elseif (current_user_can('cp_client')) : ?>
                                    <li><a class="dropdown-item" href="<?php echo home_url('/dashboard-cliente/'); ?>"><i class="bi bi-speedometer2"></i> Painel</a></li>
                                <?php endif; ?>
                                <?php if (current_user_can('manage_options')) : ?>
                                    <li><a class="dropdown-item" href="<?php echo admin_url('admin.php?page=consultoria-platform'); ?>"><i class="bi bi-gear"></i> Admin</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?php echo wp_logout_url(home_url()); ?>"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                            </ul>
                        </div>
                    <?php else : ?>
                        <a href="<?php echo wp_login_url(); ?>" class="btn btn-outline-primary btn-sm">Entrar</a>
                        <a href="<?php echo wp_registration_url(); ?>" class="btn btn-primary btn-sm">Cadastrar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<main class="main-content">
