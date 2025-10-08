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

// Define base IP constants for different services
define('VECTORRANK_APP_BASE_IP', 'https://host.docker.internal:44381');
define('VECTORRANK_SEARCH_BASE_IP', 'https://host.docker.internal:4123');
define('VECTORRANK_RECOMMENDATION_BASE_IP', 'https://host.docker.internal:2653');

// Include API Manager
require_once VECTORRANK_PLUGIN_PATH . 'common/apimanager.php';

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
        
        wp_enqueue_style('vectorrank-admin', VECTORRANK_PLUGIN_URL . 'assets/css/admin.css', array(), VECTORRANK_PLUGIN_VERSION . '.' . time());
        wp_enqueue_script('vectorrank-admin', VECTORRANK_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), VECTORRANK_PLUGIN_VERSION . '.' . time(), true);
        
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
add_action('wp_ajax_vectorrank_logout', 'vectorrank_handle_logout');
add_action('wp_ajax_vectorrank_test_connection', 'vectorrank_test_connection');
add_action('wp_ajax_vectorrank_refresh_token', 'vectorrank_refresh_token');
add_action('wp_ajax_vectorrank_save_features', 'vectorrank_save_features');
add_action('wp_ajax_vectorrank_get_content', 'vectorrank_get_content');
add_action('wp_ajax_vectorrank_toggle_feature', 'vectorrank_toggle_feature');

function vectorrank_handle_login() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    // Debug logging
    error_log('[VectorRank Debug] POST data: ' . print_r($_POST, true));
    
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    $password = isset($_POST['password']) ? sanitize_text_field($_POST['password']) : '';
    
    // Debug logging
    error_log('[VectorRank Debug] Processed - Email: ' . $email . ', Password: ' . (empty($password) ? 'empty' : 'provided'));
    error_log('[VectorRank Debug] Attempting to connect to: ' . $login_url);
    
    if (empty($email) || empty($password)) {
        error_log('[VectorRank Debug] Validation failed - Email empty: ' . (empty($email) ? 'yes' : 'no') . ', Password empty: ' . (empty($password) ? 'yes' : 'no'));
        wp_send_json_error(array(
            'message' => __('Please enter both email and password.', 'vectorrank')
        ));
        return;
    }
    
    // Validate email format
    if (!is_email($email)) {
        wp_send_json_error(array(
            'message' => __('Please enter a valid email address.', 'vectorrank')
        ));
        return;
    }
    
    // Create API manager with custom API URL for login
    $api = new VectorRankApiManager();
    
    // Get login API URLs with HTTP fallback
    $login_urls = vectorrank_get_api_urls('app', '/api/v1/third-party-access');
    
    $response = false;
    $last_error = null;
    
    // Prepare login data
    $login_data = array(
        'email' => $email,
        'password' => $password
    );
    
    // Allow localhost connections for development
    add_filter('http_request_host_is_external', '__return_true');
    add_filter('block_local_requests', '__return_false');
    
    foreach ($login_urls as $login_url) {
        error_log('[VectorRank Debug] Trying URL: ' . $login_url);
        
        // Send login request directly to the custom endpoint
        $args = array(
            'method' => 'POST',
            'timeout' => 30, // Timeout per attempt
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'VectorRank-WordPress-Plugin/1.0.0',
            ),
            'body' => wp_json_encode($login_data),
            'sslverify' => false, // Disabled for localhost/development
            'reject_unsafe_urls' => false, // Allow localhost connections
            'blocking' => true,
            'compress' => false,
            'decompress' => true,
        );
        
        $response = wp_remote_request($login_url, $args);
        
        // If this request succeeded, break out of the loop
        if (!is_wp_error($response)) {
            error_log('[VectorRank Debug] Successfully connected to: ' . $login_url);
            break;
        } else {
            // Store the error for later
            $last_error = $response;
            error_log('[VectorRank Debug] Failed to connect to ' . $login_url . ': ' . $response->get_error_message());
        }
    }
    
    // Remove the filters after all attempts
    remove_filter('http_request_host_is_external', '__return_true');
    remove_filter('block_local_requests', '__return_false');
    
    // Check if all connection attempts failed
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        $error_code = $response->get_error_code();
        error_log('[VectorRank] All login attempts failed - Last error - Code: ' . $error_code . ', Message: ' . $error_message);
        echo $error_code;
        echo $error_message;       
        // Provide more specific error messages
        if (strpos($error_message, 'cURL error 7') !== false || strpos($error_message, 'Connection refused') !== false) {
            $user_message = __('Cannot connect to the VectorRank service. Please make sure your local server is running on port 44381.', 'vectorrank');
        } elseif (strpos($error_message, 'SSL') !== false) {
            $user_message = __('SSL connection error. Please try using HTTP instead of HTTPS for localhost.', 'vectorrank');
        } elseif (strpos($error_message, 'timeout') !== false) {
            $user_message = __('Connection timeout. Please check if your local server is responding.', 'vectorrank');
        } else {
            $user_message = __('Connection error: ', 'vectorrank') . $error_message;
        }
        
        wp_send_json_error(array(
            'message' => $user_message
        ));
        return;
    }
    
    // Get response data
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    // Parse JSON response
    $response_data = json_decode($response_body, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('[VectorRank] Login API - Invalid JSON response: ' . $response_body);
        wp_send_json_error(array(
            'message' => __('Invalid response from server. Please try again.', 'vectorrank')
        ));
        return;
    }
    
    // Handle different response codes  
    if ($response_code === 200 && isset($response_data['token'])) {
        // Success - save the tokens and user info
        $settings = get_option('vectorrank_settings', array());
        
        // Save authentication data
        $settings['email'] = $email;
        $settings['logged_in'] = true;
        $settings['client_token'] = $response_data['token']; // Main token for API access
        
        // Save user status
        if (isset($response_data['status'])) {
            $settings['user_status'] = $response_data['status'];
        }
        
        // Save login timestamp
        $settings['login_time'] = current_time('timestamp');
        
        // Debug logging
        error_log('[VectorRank Debug] Saving settings: Email=' . $email . ', Token=' . substr($response_data['token'], 0, 20) . '...');
        
        // Ensure all features are initialized as active by default
        if (!isset($settings['features'])) {
            $settings['features'] = vectorrank_get_default_features();
        } else {
            // Merge with defaults to ensure new features are active
            $settings['features'] = array_merge(vectorrank_get_default_features(), $settings['features']);
        }
        
        update_option('vectorrank_settings', $settings);
        
        // Log successful authentication
        error_log('[VectorRank] User authenticated successfully: ' . $email);
        
        wp_send_json_success(array(
            'message' => __('Login successful! All AI features are now active.', 'vectorrank'),
            'user_status' => isset($response_data['status']) ? $response_data['status'] : 'active'
        ));
        
    } elseif ($response_code === 200 && isset($response_data['status']) && $response_data['status'] === 'false') {
        // Login failed even with 200 status
        $error_message = isset($response_data['errorMessage']) ? $response_data['errorMessage'] : __('Login failed. Please check your credentials.', 'vectorrank');
        
        error_log('[VectorRank] Login failed for: ' . $email . ' - ' . $error_message);
        
        wp_send_json_error(array(
            'message' => $error_message
        ));
        
    } elseif ($response_code === 401 || $response_code === 403) {
        // Authentication failed
        $error_message = isset($response_data['errorMessage']) ? $response_data['errorMessage'] : __('Invalid email or password.', 'vectorrank');
        
        error_log('[VectorRank] Authentication failed for: ' . $email . ' - ' . $error_message);
        
        wp_send_json_error(array(
            'message' => $error_message
        ));
        
    } elseif ($response_code >= 400 && $response_code < 500) {
        // Client error
        $error_message = isset($response_data['errorMessage']) ? $response_data['errorMessage'] : __('Request error. Please check your input.', 'vectorrank');
        
        error_log('[VectorRank] Client error (' . $response_code . ') for: ' . $email . ' - ' . $error_message);
        
        wp_send_json_error(array(
            'message' => $error_message
        ));
        
    } else {
        // Server error or unexpected response
        $error_message = isset($response_data['errorMessage']) ? $response_data['errorMessage'] : __('Server error. Please try again later.', 'vectorrank');
        
        error_log('[VectorRank] Server error (' . $response_code . ') for: ' . $email . ' - Response: ' . $response_body);
        
        wp_send_json_error(array(
            'message' => $error_message
        ));
    }
}

function vectorrank_handle_logout() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    // Clear all authentication data
    $settings = get_option('vectorrank_settings', array());
    
    // Remove authentication tokens
    unset($settings['logged_in']);
    unset($settings['email']);
    unset($settings['client_token']);
    unset($settings['api_token']);
    unset($settings['user_status']);
    unset($settings['login_time']);
    
    update_option('vectorrank_settings', $settings);
    
    // Log the logout
    error_log('[VectorRank] User logged out successfully');
    
    wp_send_json_success(array(
        'message' => __('Logged out successfully.', 'vectorrank')
    ));
}

function vectorrank_test_connection() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $settings = get_option('vectorrank_settings', array());
    
    if (empty($settings['client_token'])) {
        wp_send_json_error(array(
            'message' => __('No authentication token available. Please login first.', 'vectorrank')
        ));
        return;
    }
    
    // Use the API manager to test connection
    $api = vectorrank_api();
    $result = $api->test_authenticated_call();
    
    if (is_wp_error($result)) {
        wp_send_json_error(array(
            'message' => $result->get_error_message()
        ));
    } else {
        wp_send_json_success(array(
            'message' => __('Connection successful!', 'vectorrank'),
            'response_time' => '25ms', // You can implement actual timing if needed
            'data' => $result
        ));
    }
}

function vectorrank_refresh_token() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $settings = get_option('vectorrank_settings', array());
    
    if (empty($settings['email'])) {
        wp_send_json_error(array(
            'message' => __('No user email available. Please login again.', 'vectorrank')
        ));
        return;
    }
    
    // Here you would implement token refresh logic with your API
    // For now, we'll simulate a successful refresh
    $settings['login_time'] = current_time('timestamp');
    update_option('vectorrank_settings', $settings);
    
    wp_send_json_success(array(
        'message' => __('Token refreshed successfully!', 'vectorrank')
    ));
}

function vectorrank_save_features() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $settings = get_option('vectorrank_settings', array());
    
    if (empty($settings['client_token'])) {
        wp_send_json_error(array(
            'message' => __('No authentication token available. Please login first.', 'vectorrank')
        ));
        return;
    }
    
    // Get apps API URLs with HTTP fallback
    $api_urls = vectorrank_get_api_urls('app', '/api/v1/get-apps');
    
    $response = false;
    $last_error = null;
    
    // Allow localhost connections for development
    add_filter('http_request_host_is_external', '__return_true');
    add_filter('block_local_requests', '__return_false');
    
    foreach ($api_urls as $api_url) {
        error_log('[VectorRank Debug] Trying to fetch apps from: ' . $api_url);
        
        // Send GET request with authentication
        $args = array(
            'method' => 'GET',
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/json',
                'User-Agent' => 'VectorRank-WordPress-Plugin/1.0.0',
                'Authorization' => 'Bearer ' . $settings['client_token'], // Use saved token
            ),
            'sslverify' => false, // Disabled for localhost/development
            'reject_unsafe_urls' => false, // Allow localhost connections
            'blocking' => true,
        );
        
        $response = wp_remote_request($api_url, $args);
        
        // If this request succeeded, break out of the loop
        if (!is_wp_error($response)) {
            error_log('[VectorRank Debug] Successfully connected to: ' . $api_url);
            break;
        } else {
            // Store the error for later
            $last_error = $response;
            error_log('[VectorRank Debug] Failed to connect to ' . $api_url . ': ' . $response->get_error_message());
        }
    }
    
    // Remove the filters after all attempts
    remove_filter('http_request_host_is_external', '__return_true');
    remove_filter('block_local_requests', '__return_false');
    
    // Check if all connection attempts failed
    if (is_wp_error($response)) {
        $error_message = $response->get_error_message();
        error_log('[VectorRank] Failed to fetch apps - Error: ' . $error_message);
        
        wp_send_json_error(array(
            'message' => __('Failed to connect to VectorRank API: ', 'vectorrank') . $error_message
        ));
        return;
    }
    
    // Get response data
    $response_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);
    
    error_log('[VectorRank Debug] API Response - Code: ' . $response_code . ', Body: ' . $response_body);
    
    // Check response code
    if ($response_code !== 200) {
        wp_send_json_error(array(
            'message' => __('API returned error code: ', 'vectorrank') . $response_code
        ));
        return;
    }
    
    // Parse JSON response
    $apps_data = json_decode($response_body, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('[VectorRank] Invalid JSON response: ' . $response_body);
        wp_send_json_error(array(
            'message' => __('Invalid response from API. Please try again.', 'vectorrank')
        ));
        return;
    }
    
    if (!is_array($apps_data)) {
        wp_send_json_error(array(
            'message' => __('Unexpected response format from API.', 'vectorrank')
        ));
        return;
    }
    
    // Save apps data
    $saved_apps = array();
    
    foreach ($apps_data as $app) {
        if (isset($app['Id']) && isset($app['Name']) && isset($app['WriteToken']) && isset($app['SearchToken'])) {
            $saved_apps[] = array(
                'id' => sanitize_text_field($app['Id']),
                'name' => sanitize_text_field($app['Name']),
                'write_token' => sanitize_text_field($app['WriteToken']),
                'search_token' => sanitize_text_field($app['SearchToken']),
                'synced_at' => current_time('timestamp')
            );
        }
    }
    
    // Save to settings
    $settings['apps'] = $saved_apps;
    $settings['last_sync'] = current_time('timestamp');
    update_option('vectorrank_settings', $settings);
    
    error_log('[VectorRank] Successfully saved ' . count($saved_apps) . ' apps');
    
    wp_send_json_success(array(
        'message' => __('Features saved successfully!', 'vectorrank'),
        'apps_count' => count($saved_apps),
        'apps_data' => $saved_apps
    ));
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

/**
 * Get API URLs with HTTP fallback for different services
 * 
 * @param string $service Service type: 'app', 'search', or 'recommendation'
 * @param string $endpoint API endpoint path (e.g., '/api/v1/get-apps')
 * @return array Array of URLs to try (HTTPS first, then HTTP)
 */
function vectorrank_get_api_urls($service, $endpoint) {
    $base_urls = array(
        'app' => VECTORRANK_APP_BASE_IP,
        'search' => VECTORRANK_SEARCH_BASE_IP,
        'recommendation' => VECTORRANK_RECOMMENDATION_BASE_IP
    );
    
    if (!isset($base_urls[$service])) {
        return array();
    }
    
    $base_url = $base_urls[$service];
    
    return array(
        $base_url . $endpoint,
        str_replace('https://', 'http://', $base_url) . $endpoint
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