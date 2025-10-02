
<?php
/*
Plugin Name: VectoRank
Plugin URI: https://vectorank.com
Description: AI-powered product search, recommendations, and customer-product matching with SaaS integration and multilingual support.
Version: 1.0.0
Author: VectoRank
Author URI: https://vectorank.com
Text Domain: vectorank
Domain Path: /includes/i18n/languages
*/


if ( ! defined( 'ABSPATH' ) ) exit;

// Autoload includes
foreach (glob(plugin_dir_path(__FILE__) . 'includes/**/*.php') as $file) {
    require_once $file;
}

// Activation/Deactivation hooks
register_activation_hook(__FILE__, 'vectorank_activate');
register_deactivation_hook(__FILE__, 'vectorank_deactivate');

function vectorank_activate() {
    // Activation logic
}

function vectorank_deactivate() {
    // Deactivation logic
}

// Main plugin class
class Vectorank_Plugin {
    public function __construct() {
        add_action('admin_menu', [ $this, 'add_admin_menu' ]);
        add_action('admin_init', [ $this, 'maybe_redirect_to_onboarding' ]);
    }

    public function add_admin_menu() {
        add_menu_page(
            __('VectoRank', 'vectorank'),
            __('VectoRank', 'vectorank'),
            'manage_options',
            'vectorank',
            [ $this, 'render_dashboard' ],
            'dashicons-search',
            3
        );
        add_submenu_page(
            'vectorank',
            __('Onboarding', 'vectorank'),
            __('Onboarding', 'vectorank'),
            'manage_options',
            'vectorank-onboarding',
            [ $this, 'render_onboarding' ]
        );
    }

    public function render_dashboard() {
        include plugin_dir_path(__FILE__) . 'templates/admin/dashboard.php';
    }

    public function render_onboarding() {
        include plugin_dir_path(__FILE__) . 'templates/admin/onboarding.php';
    }

    public function maybe_redirect_to_onboarding() {
        if (get_option('vectorank_show_onboarding', false)) {
            delete_option('vectorank_show_onboarding');
            wp_safe_redirect(admin_url('admin.php?page=vectorank-onboarding'));
            exit;
        }
    }
}

register_activation_hook(__FILE__, function() {
    add_option('vectorank_show_onboarding', true);
});

new Vectorank_Plugin();
