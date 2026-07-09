<?php

defined('ABSPATH') || exit;

define('CT_VERSION', '1.0.0');
define('CT_THEME_DIR', get_template_directory());
define('CT_THEME_URL', get_template_directory_uri());

// Theme setup
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('align-wide');
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('responsive-embeds');

    load_theme_textdomain('consultoria-theme', CT_THEME_DIR . '/languages');

    register_nav_menus([
        'primary'   => __('Menu Principal', 'consultoria-theme'),
        'footer'    => __('Menu Rodapé', 'consultoria-theme'),
    ]);
});

// Enqueue assets
add_action('wp_enqueue_scripts', function () {
    // Styles
    wp_enqueue_style('google-fonts-inter', 'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap', [], null);
    wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css', [], '5.3.0');
    wp_enqueue_style('bootstrap-icons', 'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css', [], '1.10.5');
    wp_enqueue_style('ct-main', CT_THEME_URL . '/assets/css/main.css', ['bootstrap'], CT_VERSION);

    // Scripts
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js', ['jquery'], '5.3.0', true);
    wp_enqueue_script('ct-main', CT_THEME_URL . '/assets/js/main.js', ['jquery', 'bootstrap-js'], CT_VERSION, true);

    wp_localize_script('ct-main', 'ctData', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'restUrl'  => rest_url('consultoria/v1'),
        'nonce'    => wp_create_nonce('wp_rest'),
        'userId'   => get_current_user_id(),
        'themeUri' => CT_THEME_URL,
    ]);
});

// Register widget areas
add_action('widgets_init', function () {
    register_sidebar([
        'name'          => __('Sidebar', 'consultoria-theme'),
        'id'            => 'sidebar-1',
        'description'   => __('Widgets da sidebar', 'consultoria-theme'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
});

// Register Elementor locations
add_action('elementor/theme/register_locations', function ($locationManager) {
    $locationManager->register_core_location('header');
    $locationManager->register_core_location('footer');
});

// Body classes
add_filter('body_class', function ($classes) {
    if (is_user_logged_in()) {
        $classes[] = 'logged-in';
        $user = wp_get_current_user();
        if (in_array('cp_consultant', $user->roles)) {
            $classes[] = 'user-role-consultant';
        } elseif (in_array('cp_client', $user->roles)) {
            $classes[] = 'user-role-client';
        }
    }
    return $classes;
});

// Custom login redirect
add_filter('login_redirect', function ($redirect_to, $request, $user) {
    if (isset($user->roles) && is_array($user->roles)) {
        if (in_array('cp_consultant', $user->roles)) {
            return home_url('/dashboard-consultor/');
        }
        if (in_array('cp_client', $user->roles)) {
            return home_url('/dashboard-cliente/');
        }
    }
    return $redirect_to;
}, 10, 3);

// Register shortcodes from platform plugin
add_action('init', function () {
    $shortcodes = apply_filters('cp_shortcodes', []);

    foreach ($shortcodes as $tag => $callback) {
        if (!shortcode_exists($tag)) {
            add_shortcode($tag, $callback);
        }
    }
});

// Custom body open
add_action('wp_body_open', function () {
    if (function_exists('wp_body_open')) {
        wp_body_open();
    }
});

// Theme setup - configure image sizes
add_action('init', function () {
    set_post_thumbnail_size(400, 300, true);
    add_image_size('cp-avatar', 150, 150, true);
    add_image_size('cp-cover', 1200, 400, true);
    add_image_size('cp-portfolio', 600, 400, true);
});

// Disable admin bar for client and consultant roles
add_action('after_setup_theme', function () {
    if (current_user_can('cp_client') || current_user_can('cp_consultant')) {
        show_admin_bar(false);
    }
});

// Custom login page styles
add_action('login_enqueue_scripts', function () {
    wp_enqueue_style('ct-login', CT_THEME_URL . '/assets/css/login.css', [], CT_VERSION);
});

// Custom login logo URL
add_filter('login_headerurl', function () {
    return home_url();
});

// Dashboard redirect for non-admin roles
add_action('admin_init', function () {
    if (!current_user_can('manage_options') && !current_user_can('cp_support') && !wp_doing_ajax()) {
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        wp_redirect(home_url());
        exit;
    }
});

// Register Elementor widgets
add_action('elementor/widgets/widgets_registered', function () {
    // Custom Elementor widgets would be registered here
});

// Elementor editor support - reorder panels
add_action('elementor/editor/after_enqueue_scripts', function () {
    wp_enqueue_script('ct-elementor', CT_THEME_URL . '/assets/js/elementor.js', ['jquery'], CT_VERSION, true);
});

// Pagination helper
function ct_pagination(): void {
    global $wp_query;
    $big = 999999999;
    $pages = paginate_links([
        'base'      => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format'    => '?paged=%#%',
        'current'   => max(1, get_query_var('paged')),
        'total'     => $wp_query->max_num_pages,
        'type'      => 'array',
        'prev_text' => '<i class="bi bi-chevron-left"></i>',
        'next_text' => '<i class="bi bi-chevron-right"></i>',
    ]);

    if (is_array($pages)) {
        echo '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        foreach ($pages as $page) {
            $class = strpos($page, 'current') !== false ? ' active' : '';
            echo '<li class="page-item' . $class . '">' . str_replace('page-numbers', 'page-numbers page-link', $page) . '</li>';
        }
        echo '</ul></nav>';
    }
}
