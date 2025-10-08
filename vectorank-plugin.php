<?php
/**
 * Plugin Name: VectorRank Plugin
 * Description: AI-powered search and recommendation system for WordPress with advanced analytics and personalization features.
 * Version: 1.0.0
 * Author: VectorRank
 * Text Domain: vectorrank
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('VECTORRANK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('VECTORRANK_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('VECTORRANK_PLUGIN_VERSION', '1.0.0');

/**
 * Main VectorRank Plugin Class
 */
class VectorRankPlugin {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Initialize plugin
        load_plugin_textdomain('vectorrank', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    public function add_admin_menu() {
        // Main menu page (Home)
        add_menu_page(
            __('VectorRank', 'vectorrank'),
            __('VectorRank', 'vectorrank'),
            'manage_options',
            'vectorrank-home',
            array($this, 'home_page'),
            'dashicons-chart-line',
            30
        );
        
        // Submenu pages
        add_submenu_page(
            'vectorrank-home',
            __('Home', 'vectorrank'),
            __('Home', 'vectorrank'),
            'manage_options',
            'vectorrank-home',
            array($this, 'home_page')
        );
        
        add_submenu_page(
            'vectorrank-home',
            __('Notifications', 'vectorrank'),
            __('Notifications', 'vectorrank'),
            'manage_options',
            'vectorrank-notifications',
            array($this, 'notifications_page')
        );
        
        add_submenu_page(
            'vectorrank-home',
            __('Analytics', 'vectorrank'),
            __('Analytics', 'vectorrank'),
            'manage_options',
            'vectorrank-analytics',
            array($this, 'analytics_page')
        );
        
        add_submenu_page(
            'vectorrank-home',
            __('Usage', 'vectorrank'),
            __('Usage', 'vectorrank'),
            'manage_options',
            'vectorrank-usage',
            array($this, 'usage_page')
        );
        
        add_submenu_page(
            'vectorrank-home',
            __('Settings', 'vectorrank'),
            __('Settings', 'vectorrank'),
            'manage_options',
            'vectorrank-settings',
            array($this, 'admin_page')
        );
    }
    
    public function enqueue_admin_scripts($hook) {
        // Check if we're on any VectorRank admin page
        $vectorrank_pages = array(
            'toplevel_page_vectorrank-home',
            'vectorrank_page_vectorrank-notifications',
            'vectorrank_page_vectorrank-analytics',
            'vectorrank_page_vectorrank-usage',
            'vectorrank_page_vectorrank-settings'
        );
        
        if (!in_array($hook, $vectorrank_pages)) {
            return;
        }
        
        wp_enqueue_style('vectorrank-admin', VECTORRANK_PLUGIN_URL . 'assets/css/admin.css', array(), VECTORRANK_PLUGIN_VERSION);
        wp_enqueue_script('vectorrank-admin', VECTORRANK_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), VECTORRANK_PLUGIN_VERSION, true);
        
        // Localize script for AJAX
        wp_localize_script('vectorrank-admin', 'vectorrank_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('vectorrank_nonce'),
        ));
    }
    
    public function home_page() {
        include_once VECTORRANK_PLUGIN_PATH . 'includes/home.php';
    }
    
    public function notifications_page() {
        include_once VECTORRANK_PLUGIN_PATH . 'includes/notifications.php';
    }
    
    public function analytics_page() {
        include_once VECTORRANK_PLUGIN_PATH . 'includes/analytics.php';
    }
    
    public function usage_page() {
        include_once VECTORRANK_PLUGIN_PATH . 'includes/usage.php';
    }
    
    public function admin_page() {
        include_once VECTORRANK_PLUGIN_PATH . 'includes/settings.php';
    }
    
    public function activate() {
        // Create necessary database tables or options
        $default_settings = array(
            'api_key' => '',
            'username' => '',
            'logged_in' => false,
            'features' => array(
                'ai_search' => true,
                'autocomplete' => true,
                'cross_sell' => true,
                'personalized_recommendations' => true,
                'favorite_popup' => true,
                'email_recommendations' => true,
            )
        );
        
        // Only add if doesn't exist, preserve existing settings
        if (!get_option('vectorrank_settings')) {
            add_option('vectorrank_settings', $default_settings);
        } else {
            // Ensure all features are set to true by default for existing installations
            $existing_settings = get_option('vectorrank_settings');
            if (!isset($existing_settings['features'])) {
                $existing_settings['features'] = array();
            }
            
            // Set all features to active by default
            $existing_settings['features'] = array_merge(array(
                'ai_search' => true,
                'autocomplete' => true,
                'cross_sell' => true,
                'personalized_recommendations' => true,
                'favorite_popup' => true,
                'email_recommendations' => true,
            ), $existing_settings['features']);
            
            update_option('vectorrank_settings', $existing_settings);
        }
    }
    
    public function deactivate() {
        // Cleanup if needed
    }
}

// Initialize the plugin
new VectorRankPlugin();

// AJAX handlers
add_action('wp_ajax_vectorrank_login', 'vectorrank_handle_login');
add_action('wp_ajax_vectorrank_get_content', 'vectorrank_get_content');
add_action('wp_ajax_vectorrank_toggle_feature', 'vectorrank_toggle_feature');

function vectorrank_handle_login() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $username = sanitize_text_field($_POST['username']);
    $password = sanitize_text_field($_POST['password']);
    
    // Here you would integrate with your SaaS API
    // For demo purposes, we'll simulate a successful login
    if (!empty($username) && !empty($password)) {
        $settings = get_option('vectorrank_settings');
        $settings['username'] = $username;
        $settings['logged_in'] = true;
        
        // Ensure all features are initialized as active by default
        if (!isset($settings['features'])) {
            $settings['features'] = vectorrank_get_default_features();
        } else {
            // Merge with defaults to ensure new features are active
            $settings['features'] = array_merge(vectorrank_get_default_features(), $settings['features']);
        }
        
        update_option('vectorrank_settings', $settings);
        
        wp_send_json_success(array(
            'message' => __('Login successful! All AI features are now active.', 'vectorrank')
        ));
    } else {
        wp_send_json_error(array(
            'message' => __('Please enter both username and password.', 'vectorrank')
        ));
    }
}

function vectorrank_get_content() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $content_type = sanitize_text_field($_POST['content_type']);
    $content = array();
    
    if ($content_type === 'posts') {
        $posts = get_posts(array('numberposts' => 20, 'post_status' => 'publish'));
        foreach ($posts as $post) {
            $content[] = array(
                'id' => $post->ID,
                'title' => $post->post_title,
                'type' => 'Post',
                'date' => get_the_date('Y-m-d', $post->ID),
                'status' => $post->post_status,
                'url' => get_permalink($post->ID)
            );
        }
    } elseif ($content_type === 'products' && class_exists('WooCommerce')) {
        $products = wc_get_products(array('limit' => 20));
        foreach ($products as $product) {
            $content[] = array(
                'id' => $product->get_id(),
                'title' => $product->get_name(),
                'type' => 'Product',
                'price' => $product->get_price_html(),
                'status' => $product->get_status(),
                'url' => get_permalink($product->get_id())
            );
        }
    }
    
    wp_send_json_success($content);
}

function vectorrank_get_default_features() {
    return array(
        'ai_search' => true,
        'autocomplete' => true,
        'cross_sell' => true,
        'personalized_recommendations' => true,
        'favorite_popup' => true,
        'email_recommendations' => true,
    );
}

function vectorrank_toggle_feature() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $feature_key = sanitize_text_field($_POST['feature_key']);
    $status = $_POST['status'] === 'true';
    
    $settings = get_option('vectorrank_settings');
    if (!isset($settings['features'])) {
        // Initialize with all features active by default
        $settings['features'] = vectorrank_get_default_features();
    }
    
    $settings['features'][$feature_key] = $status;
    update_option('vectorrank_settings', $settings);
    
    wp_send_json_success(array(
        'message' => sprintf(
            __('Feature %s successfully!', 'vectorrank'),
            $status ? __('activated', 'vectorrank') : __('deactivated', 'vectorrank')
        ),
        'status' => $status
    ));
}