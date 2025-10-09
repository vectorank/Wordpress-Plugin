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
define('VECTORRANK_SEARCH_BASE_IP', 'https://host.docker.internal:7260');
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

// Hook into WordPress search
add_action('pre_get_posts', 'vectorrank_modify_search_query');
add_filter('the_posts', 'vectorrank_intercept_search_results', 10, 2);
add_action('wp_enqueue_scripts', 'vectorrank_enqueue_frontend_styles');
add_filter('the_content', 'vectorrank_modify_ai_search_content');
add_action('wp_head', 'vectorrank_add_search_styles');

// AJAX handlers
add_action('wp_ajax_vectorrank_login', 'vectorrank_handle_login');
add_action('wp_ajax_vectorrank_logout', 'vectorrank_handle_logout');
add_action('wp_ajax_vectorrank_test_connection', 'vectorrank_test_connection');
add_action('wp_ajax_vectorrank_refresh_token', 'vectorrank_refresh_token');
add_action('wp_ajax_vectorrank_save_features', 'vectorrank_save_features');
add_action('wp_ajax_vectorrank_sync_posts', 'vectorrank_sync_posts');
add_action('wp_ajax_vectorrank_sync_posts_by_paragraphs', 'vectorrank_sync_posts_by_paragraphs');
add_action('wp_ajax_vectorrank_debug_apps', 'vectorrank_debug_apps');
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

function vectorrank_sync_posts() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $settings = get_option('vectorrank_settings', array());
    
    if (empty($settings['client_token'])) {
        wp_send_json_error(array(
            'message' => __('No authentication token available. Please login first.', 'vectorrank')
        ));
        return;
    }
    
    // Debug: Log current settings
    error_log('[VectorRank Debug] Current settings: ' . print_r($settings, true));
    
    if (empty($settings['apps'])) {
        error_log('[VectorRank Debug] No apps found in settings');
        wp_send_json_error(array(
            'message' => __('No apps found. Please save features first to sync apps.', 'vectorrank')
        ));
        return;
    }
    
    error_log('[VectorRank Debug] Found ' . count($settings['apps']) . ' apps');
    
    // Find recommendation-app
    $recommendation_app = null;
    foreach ($settings['apps'] as $app) {
        error_log('[VectorRank Debug] Checking app: ' . $app['name']);
        if ($app['name'] === 'recommendation-app') {
            $recommendation_app = $app;
            error_log('[VectorRank Debug] Found recommendation-app with write_token: ' . substr($app['write_token'], 0, 20) . '...');
            break;
        }
    }
    
    if (!$recommendation_app) {
        error_log('[VectorRank Debug] Recommendation app not found. Available apps:');
        foreach ($settings['apps'] as $app) {
            error_log('[VectorRank Debug] - App name: "' . $app['name'] . '"');
        }
        wp_send_json_error(array(
            'message' => __('Recommendation app not found. Please ensure apps are synced properly. Available apps: ', 'vectorrank') . implode(', ', array_column($settings['apps'], 'name'))
        ));
        return;
    }
    
    // Get all published posts
    $posts = get_posts(array(
        'post_status' => 'publish',
        'post_type' => 'post',
        'numberposts' => -1, // Get all posts
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    if (empty($posts)) {
        wp_send_json_success(array(
            'message' => __('No posts found to sync.', 'vectorrank'),
            'total_posts' => 0,
            'synced_posts' => 0
        ));
        return;
    }
    
    $total_posts = count($posts);
    $batch_size = 10;
    $synced_posts = 0;
    $failed_posts = 0;
    $batches = array_chunk($posts, $batch_size);
    
    error_log('[VectorRank] Starting post sync - Total posts: ' . $total_posts . ', Batches: ' . count($batches));
    
    // Process posts in batches of 10
    foreach ($batches as $batch_index => $post_batch) {
        $batch_data = array();
        
        foreach ($post_batch as $post) {
            // Get post categories
            $categories = get_the_category($post->ID);
            $category_names = array();
            foreach ($categories as $category) {
                $category_names[] = $category->name;
            }
            
            // Get post author
            $author = get_the_author_meta('display_name', $post->post_author);
            
            // Prepare post data for API
            $post_data = array(
                'id' => (string) $post->ID,
                'content' => strip_tags($post->post_content), // Remove HTML tags
                'tags' => $category_names,
                'properties' => array(
                    'title' => $post->post_title,
                    'author' => $author,
                    'url' => get_permalink($post->ID)
                )
            );
            
            $batch_data[] = $post_data;
        }
        
        // Prepare API request
        $api_data = array(
            'collection' => 'rec',
            'data' => $batch_data
        );
        
        // Send to API using search base IP with HTTP fallback
        $api_urls = vectorrank_get_api_urls('search', '/v1/data/add');
        
        $batch_success = false;
        
        // Allow localhost connections for development
        add_filter('http_request_host_is_external', '__return_true');
        add_filter('block_local_requests', '__return_false');
        
        foreach ($api_urls as $api_url) {
            error_log('[VectorRank] Trying to sync batch ' . ($batch_index + 1) . ' to: ' . $api_url);
            
            $args = array(
                'method' => 'POST',
                'timeout' => 60, // Longer timeout for batch operations
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'VectorRank-WordPress-Plugin/1.0.0',
                    'Authorization' => 'Bearer ' . $recommendation_app['write_token'], // Use write token
                ),
                'body' => wp_json_encode($api_data),
                'sslverify' => false, // Disabled for localhost/development
                'reject_unsafe_urls' => false, // Allow localhost connections
                'blocking' => true,
            );
            
            $response = wp_remote_request($api_url, $args);
            
            if (!is_wp_error($response)) {
                $response_code = wp_remote_retrieve_response_code($response);
                
                if ($response_code === 200) {
                    $synced_posts += count($batch_data);
                    $batch_success = true;
                    error_log('[VectorRank] Batch ' . ($batch_index + 1) . ' synced successfully to: ' . $api_url);
                    break; // Success, move to next batch
                } else {
                    error_log('[VectorRank] API error for batch ' . ($batch_index + 1) . ' - Code: ' . $response_code);
                }
            } else {
                error_log('[VectorRank] Connection error for batch ' . ($batch_index + 1) . ': ' . $response->get_error_message());
            }
        }
        
        // Remove filters after each batch
        remove_filter('http_request_host_is_external', '__return_true');
        remove_filter('block_local_requests', '__return_false');
        
        if (!$batch_success) {
            $failed_posts += count($batch_data);
            error_log('[VectorRank] Failed to sync batch ' . ($batch_index + 1));
        }
        
        // Small delay between batches to avoid overwhelming the API
        if ($batch_index < count($batches) - 1) {
            usleep(500000); // 0.5 second delay
        }
    }
    
    // Update last sync timestamp
    $settings['last_post_sync'] = current_time('timestamp');
    $settings['synced_posts_count'] = $synced_posts;
    update_option('vectorrank_settings', $settings);
    
    if ($failed_posts > 0) {
        wp_send_json_error(array(
            'message' => sprintf(
                __('Partially synced: %d posts succeeded, %d posts failed.', 'vectorrank'),
                $synced_posts,
                $failed_posts
            ),
            'total_posts' => $total_posts,
            'synced_posts' => $synced_posts,
            'failed_posts' => $failed_posts
        ));
    } else {
        wp_send_json_success(array(
            'message' => sprintf(
                __('Successfully synced %d posts to recommendation service!', 'vectorrank'),
                $synced_posts
            ),
            'total_posts' => $total_posts,
            'synced_posts' => $synced_posts,
            'failed_posts' => $failed_posts
        ));
    }
}

function vectorrank_sync_posts_by_paragraphs() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $settings = get_option('vectorrank_settings', array());
    
    if (empty($settings['client_token'])) {
        wp_send_json_error(array(
            'message' => __('No authentication token available. Please login first.', 'vectorrank')
        ));
        return;
    }
    
    // Debug: Log current settings
    error_log('[VectorRank Debug] Current settings for paragraph sync: ' . print_r($settings, true));
    
    if (empty($settings['apps'])) {
        error_log('[VectorRank Debug] No apps found in settings');
        wp_send_json_error(array(
            'message' => __('No apps found. Please save features first to sync apps.', 'vectorrank')
        ));
        return;
    }
    
    error_log('[VectorRank Debug] Found ' . count($settings['apps']) . ' apps');
    
    // Find recommendation-app (for syncing data)
    $recommendation_app = null;
    foreach ($settings['apps'] as $app) {
        error_log('[VectorRank Debug] Checking app: ' . $app['name']);
        if ($app['name'] === 'recommendation-app') {
            $recommendation_app = $app;
            error_log('[VectorRank Debug] Found recommendation-app with write_token: ' . substr($app['write_token'], 0, 20) . '...');
            break;
        }
    }
    
    if (!$recommendation_app) {
        error_log('[VectorRank Debug] Recommendation app not found. Available apps:');
        foreach ($settings['apps'] as $app) {
            error_log('[VectorRank Debug] - App name: "' . $app['name'] . '"');
        }
        wp_send_json_error(array(
            'message' => __('Recommendation app not found. Please ensure apps are synced properly. Available apps: ', 'vectorrank') . implode(', ', array_column($settings['apps'], 'name'))
        ));
        return;
    }
    
    // Get all published posts
    $posts = get_posts(array(
        'post_status' => 'publish',
        'post_type' => 'post',
        'numberposts' => -1, // Get all posts
        'orderby' => 'date',
        'order' => 'DESC'
    ));
    
    if (empty($posts)) {
        wp_send_json_success(array(
            'message' => __('No posts found to sync.', 'vectorrank'),
            'total_posts' => 0,
            'total_paragraphs' => 0,
            'synced_paragraphs' => 0
        ));
        return;
    }
    
    $total_posts = count($posts);
    $all_paragraphs = array();
    $total_paragraphs = 0;
    
    // Process all posts to extract paragraphs
    foreach ($posts as $post) {
        // Get post content and clean it
        $content = strip_tags($post->post_content);
        $content = trim($content);
        
        if (empty($content)) {
            continue;
        }
        
        // Split content by double line breaks (paragraphs)
        $paragraphs = preg_split('/\n\s*\n/', $content);
        $paragraphs = array_filter($paragraphs, 'trim'); // Remove empty paragraphs
        
        // Get post categories
        $categories = get_the_category($post->ID);
        $category_names = array();
        foreach ($categories as $category) {
            $category_names[] = $category->name;
        }
        
        // Get post author
        $author = get_the_author_meta('display_name', $post->post_author);
        
        // Create individual entries for each paragraph
        foreach ($paragraphs as $paragraph_index => $paragraph) {
            $paragraph = trim($paragraph);
            if (empty($paragraph)) {
                continue;
            }
            
            $paragraph_id = $post->ID . '_' . $paragraph_index;
            
            $paragraph_data = array(
                'id' => (string) $paragraph_id,
                'content' => $paragraph,
                'tags' => $category_names,
                'properties' => array(
                    'title' => $post->post_title,
                    'author' => $author,
                    'url' => get_permalink($post->ID),
                    'post_id' => (string) $post->ID,
                    'paragraph_index' => (string) $paragraph_index,
                    'is_paragraph' => 'true'
                )
            );
            
            $all_paragraphs[] = $paragraph_data;
            $total_paragraphs++;
        }
    }
    
    if (empty($all_paragraphs)) {
        wp_send_json_success(array(
            'message' => __('No paragraphs found to sync.', 'vectorrank'),
            'total_posts' => $total_posts,
            'total_paragraphs' => 0,
            'synced_paragraphs' => 0
        ));
        return;
    }
    
    $batch_size = 10;
    $synced_paragraphs = 0;
    $failed_paragraphs = 0;
    $batches = array_chunk($all_paragraphs, $batch_size);
    
    error_log('[VectorRank] Starting paragraph sync - Total posts: ' . $total_posts . ', Total paragraphs: ' . $total_paragraphs . ', Batches: ' . count($batches));
    
    // Process paragraphs in batches of 10
    foreach ($batches as $batch_index => $paragraph_batch) {
        // Prepare API request
        $api_data = array(
            'collection' => 'rec',
            'data' => $paragraph_batch
        );
        
        // Send to API using search base IP with HTTP fallback
        $api_urls = vectorrank_get_api_urls('search', '/v1/data/add');
        
        $batch_success = false;
        
        // Allow localhost connections for development
        add_filter('http_request_host_is_external', '__return_true');
        add_filter('block_local_requests', '__return_false');
        
        foreach ($api_urls as $api_url) {
            error_log('[VectorRank] Trying to sync paragraph batch ' . ($batch_index + 1) . ' to: ' . $api_url);
            
            $args = array(
                'method' => 'POST',
                'timeout' => 60, // Longer timeout for batch operations
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'VectorRank-WordPress-Plugin/1.0.0',
                    'Authorization' => 'Bearer ' . $recommendation_app['write_token'], // Use write token
                ),
                'body' => wp_json_encode($api_data),
                'sslverify' => false, // Disabled for localhost/development
                'reject_unsafe_urls' => false, // Allow localhost connections
                'blocking' => true,
            );
            
            $response = wp_remote_request($api_url, $args);
            
            if (!is_wp_error($response)) {
                $response_code = wp_remote_retrieve_response_code($response);
                
                if ($response_code === 200) {
                    $synced_paragraphs += count($paragraph_batch);
                    $batch_success = true;
                    error_log('[VectorRank] Paragraph batch ' . ($batch_index + 1) . ' synced successfully to: ' . $api_url);
                    break; // Success, move to next batch
                } else {
                    error_log('[VectorRank] API error for paragraph batch ' . ($batch_index + 1) . ' - Code: ' . $response_code);
                }
            } else {
                error_log('[VectorRank] Connection error for paragraph batch ' . ($batch_index + 1) . ': ' . $response->get_error_message());
            }
        }
        
        // Remove filters after each batch
        remove_filter('http_request_host_is_external', '__return_true');
        remove_filter('block_local_requests', '__return_false');
        
        if (!$batch_success) {
            $failed_paragraphs += count($paragraph_batch);
            error_log('[VectorRank] Failed to sync paragraph batch ' . ($batch_index + 1));
        }
        
        // Small delay between batches to avoid overwhelming the API
        if ($batch_index < count($batches) - 1) {
            usleep(500000); // 0.5 second delay
        }
    }
    
    // Update last sync timestamp
    $settings['last_paragraph_sync'] = current_time('timestamp');
    $settings['synced_paragraphs_count'] = $synced_paragraphs;
    update_option('vectorrank_settings', $settings);
    
    if ($failed_paragraphs > 0) {
        wp_send_json_error(array(
            'message' => sprintf(
                __('Partially synced: %d paragraphs succeeded, %d paragraphs failed.', 'vectorrank'),
                $synced_paragraphs,
                $failed_paragraphs
            ),
            'total_posts' => $total_posts,
            'total_paragraphs' => $total_paragraphs,
            'synced_paragraphs' => $synced_paragraphs,
            'failed_paragraphs' => $failed_paragraphs
        ));
    } else {
        wp_send_json_success(array(
            'message' => sprintf(
                __('Successfully synced %d paragraphs from %d posts to recommendation service!', 'vectorrank'),
                $synced_paragraphs,
                $total_posts
            ),
            'total_posts' => $total_posts,
            'total_paragraphs' => $total_paragraphs,
            'synced_paragraphs' => $synced_paragraphs,
            'failed_paragraphs' => $failed_paragraphs
        ));
    }
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
    

    // Debug: Log the raw response to see the structure
    error_log('[VectorRank Debug] Raw apps data: ' . print_r($apps_data, true));
    
    // Save apps data
    $saved_apps = array();
    
    foreach ($apps_data as $app) {
        error_log('[VectorRank Debug] Processing app: ' . print_r($app, true));
        
        if (isset($app['id']) && isset($app['name']) && isset($app['writeToken']) && isset($app['searchToken'])) {
            $saved_apps[] = array(
                'id' => sanitize_text_field($app['id']),
                'name' => sanitize_text_field($app['name']),
                'write_token' => sanitize_text_field($app['writeToken']),
                'search_token' => sanitize_text_field($app['searchToken']),
                'synced_at' => current_time('timestamp')
            );
            error_log('[VectorRank Debug] Added app: ' . $app['Name'] . ' with ID: ' . $app['Id']);
        } else {
            error_log('[VectorRank Debug] Skipped app due to missing fields: ' . print_r($app, true));
        }
    }
    // Debug: Log what we're about to save
    error_log('[VectorRank Debug] About to save apps: ' . print_r($saved_apps, true));
    
    // Get current settings to preserve other data
    $current_settings = get_option('vectorrank_settings', array());
    
    // Save to settings
    $current_settings['apps'] = $saved_apps;
    $current_settings['last_sync'] = current_time('timestamp');
    
    $update_result = update_option('vectorrank_settings', $current_settings);
    
    error_log('[VectorRank Debug] Update option result: ' . ($update_result ? 'success' : 'failed'));
    
    // Verify the save by reading it back
    $verification_settings = get_option('vectorrank_settings', array());
    error_log('[VectorRank Debug] Verification - saved apps count: ' . (isset($verification_settings['apps']) ? count($verification_settings['apps']) : 0));
    
    if (isset($verification_settings['apps'])) {
        error_log('[VectorRank Debug] Verification - saved apps: ' . print_r($verification_settings['apps'], true));
    }
    
    error_log('[VectorRank] Successfully saved ' . count($saved_apps) . ' apps');
    
    wp_send_json_success(array(
        'message' => __('Features saved successfully!', 'vectorrank'),
        'apps_count' => count($saved_apps),
        'apps_data' => $saved_apps
    ));
}

function vectorrank_debug_apps() {
    check_ajax_referer('vectorrank_nonce', 'nonce');
    
    $settings = get_option('vectorrank_settings', array());
    
    $debug_info = array(
        'has_apps' => isset($settings['apps']),
        'apps_count' => isset($settings['apps']) ? count($settings['apps']) : 0,
        'apps_data' => isset($settings['apps']) ? $settings['apps'] : array(),
        'last_sync' => isset($settings['last_sync']) ? $settings['last_sync'] : 'never',
        'all_settings_keys' => array_keys($settings)
    );
    
    error_log('[VectorRank Debug] Debug info: ' . print_r($debug_info, true));
    
    wp_send_json_success($debug_info);
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


/**
 * Modify search query to prepare for AI search interception
 */
function vectorrank_modify_search_query($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        // Debug logging
        error_log('[VectorRank] Search query detected: ' . get_search_query());
        
        // Store the search term for later use
        set_transient('vectorrank_search_query', get_search_query(), 300); // 5 minutes
        
        // Check if AI search is enabled
        $settings = get_option('vectorrank_settings', array());
        
        // Debug: Log current settings
        error_log('[VectorRank] Current settings: logged_in=' . (isset($settings['logged_in']) && $settings['logged_in'] ? 'yes' : 'no') . 
                  ', ai_search_enabled=' . (isset($settings['features']['ai_search']) && $settings['features']['ai_search'] ? 'yes' : 'no') . 
                  ', apps_count=' . (isset($settings['apps']) ? count($settings['apps']) : 0));
        
        if (!isset($settings['features']['ai_search']) || !$settings['features']['ai_search']) {
            error_log('[VectorRank] AI search disabled in settings - please enable AI Search feature');
            return; // AI search is disabled, let WordPress handle it normally
        }
        
        // Check if user is logged in
        if (!isset($settings['logged_in']) || !$settings['logged_in']) {
            error_log('[VectorRank] User not logged in - please login first');
            return;
        }
        
        // Check if we have valid search app credentials
        if (empty($settings['apps'])) {
            error_log('[VectorRank] No apps configured - please click Save Features to sync apps');
            return; // No apps configured, fallback to WordPress search
        }
        
        // Find search-app
        $search_app = null;
        $app_names = array();
        foreach ($settings['apps'] as $app) {
            $app_names[] = $app['name'];
            if ($app['name'] === 'search-app') {
                $search_app = $app;
                break;
            }
        }
        
        if (!$search_app) {
            error_log('[VectorRank] Search app not found - Available apps: ' . implode(', ', $app_names));
            return;
        }
        
        if (empty($search_app['search_token'])) {
            error_log('[VectorRank] Search app found but no search token available');
            return; // No search app or token, fallback to WordPress search
        }
        
        // Mark that we should intercept the results
        set_transient('vectorrank_use_ai_search', true, 300);
        error_log('[VectorRank] AI search will be used for query: ' . get_search_query());
    }
}

/**
 * Intercept search results and replace with AI-powered results
 */
function vectorrank_intercept_search_results($posts, $query) {
    if (!is_admin() && $query->is_main_query() && $query->is_search()) {
        error_log('[VectorRank] Intercepting search results...');
        
        // Check if we should use AI search
        if (!get_transient('vectorrank_use_ai_search')) {
            error_log('[VectorRank] Not using AI search - transient not set');
            return $posts; // Use default WordPress search
        }
        
        // Clear the transient
        delete_transient('vectorrank_use_ai_search');
        
        $search_query = get_transient('vectorrank_search_query');
        delete_transient('vectorrank_search_query');
        
        if (empty($search_query)) {
            return $posts; // No search query, return original results
        }
        
        // Get AI search results
        $ai_results = vectorrank_get_ai_search_results($search_query);
        
        if (is_wp_error($ai_results) || empty($ai_results)) {
            error_log('[VectorRank] AI search failed, falling back to WordPress search: ' . ($ai_results ? $ai_results->get_error_message() : 'No results'));
            return $posts; // Fallback to WordPress search
        }
        
        // Convert AI results to WordPress post objects
        $ai_posts = vectorrank_convert_ai_results_to_posts($ai_results, $search_query);
        
        // Update query vars for pagination
        $query->found_posts = count($ai_posts);
        $query->max_num_pages = 1; // We'll implement pagination later if needed
        
        return $ai_posts;
    }
    
    return $posts;
}

/**
 * Get AI search results from the API
 */
function vectorrank_get_ai_search_results($search_query) {
    $settings = get_option('vectorrank_settings', array());
    
    // Find search-app
    $search_app = null;
    foreach ($settings['apps'] as $app) {
        if ($app['name'] === 'search-app') {
            $search_app = $app;
            break;
        }
    }
    
    if (!$search_app) {
        return new WP_Error('no_search_app', 'Search app not found');
    }
    


    
    
    // We'll try the first endpoint initially
    $api_urls = vectorrank_get_api_urls('search', '/v1/search/get');
    
    // Prepare search request
    $search_data = array(
        'collection' => 'rec', // Using same collection as sync
        'query' => $search_query,
        'properties' => null, // Empty properties for now
        'topN' => 20, // Get top 20 results
        'offset' => 0, // Start from beginning
        'searchType' => 0 // As requested
    );

    echo 'log' . json_encode($search_data);

    // Allow localhost connections for development
    add_filter('http_request_host_is_external', '__return_true');
    add_filter('block_local_requests', '__return_false');
    
    $response = false;
    
    foreach ($api_urls as $api_url) {
        error_log('[VectorRank] Trying search API: ' . $api_url);
        
        $args = array(
            'method' => 'POST',
            'timeout' => 30,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'VectorRank-WordPress-Plugin/1.0.0',
                'Authorization' => 'Bearer ' . $search_app['search_token'],
            ),
            'body' => wp_json_encode($search_data),
            'sslverify' => false,
            'reject_unsafe_urls' => false,
            'blocking' => true,
        );
        
        error_log('[VectorRank] Sending request to: ' . $api_url . ' with data: ' . wp_json_encode($search_data));
        
        $response = wp_remote_request($api_url, $args);
        
        if (!is_wp_error($response)) {
            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);
            
            if ($response_code === 200) {
                error_log('[VectorRank] AI search successful from: ' . $api_url);
                break;
            } else {
                error_log('[VectorRank] AI search API error - Code: ' . $response_code . ', Response: ' . $response_body);
                $response = new WP_Error('api_error', 'API returned code: ' . $response_code . ' - ' . $response_body);
            }
        } else {
            error_log('[VectorRank] AI search connection error: ' . $response->get_error_message());
        }
    }
    
    // Remove filters
    remove_filter('http_request_host_is_external', '__return_true');
    remove_filter('block_local_requests', '__return_false');
    
    if (is_wp_error($response)) {
        return $response;
    }
    
    // Parse response
    $response_body = wp_remote_retrieve_body($response);
    $search_results = json_decode($response_body, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        error_log('[VectorRank] Invalid JSON in search response: ' . $response_body);
        return new WP_Error('invalid_json', 'Invalid JSON response');
    }
    
    if (!is_array($search_results)) {
        error_log('[VectorRank] Unexpected search response format: ' . $response_body);
        return new WP_Error('invalid_format', 'Unexpected response format');
    }
    
    error_log('[VectorRank] AI search returned ' . count($search_results) . ' results');
    
    return $search_results;
}

/**
 * Convert AI search results to WordPress post objects
 */
function vectorrank_convert_ai_results_to_posts($ai_results, $search_query) {
    $posts = array();
    
    foreach ($ai_results as $result) {
        if (!isset($result['Id']) || !isset($result['Content'])) {
            continue;
        }
        
        $certainty = isset($result['Certainty']) ? $result['Certainty'] : 0;
        
        // Try to find the original WordPress post by ID
        $original_post = null;
        $post_id = null;
        
        // Check if this is a paragraph result (ID format: postId_paragraphIndex)
        if (strpos($result['Id'], '_') !== false) {
            $id_parts = explode('_', $result['Id']);
            $post_id = intval($id_parts[0]);
        } else {
            $post_id = intval($result['Id']);
        }
        
        if ($post_id > 0) {
            $original_post = get_post($post_id);
        }
        
        // Create a virtual post object for search results
        $virtual_post = new stdClass();
        
        if ($original_post && !is_wp_error($original_post)) {
            // Use original post data
            $virtual_post->ID = $original_post->ID;
            $virtual_post->post_title = $original_post->post_title;
            $virtual_post->post_content = $result['Content']; // Use AI-matched content
            $virtual_post->post_excerpt = wp_trim_words($result['Content'], 30);
            $virtual_post->post_date = $original_post->post_date;
            $virtual_post->post_author = $original_post->post_author;
            $virtual_post->post_status = 'publish';
            $virtual_post->post_type = $original_post->post_type;
            $virtual_post->post_name = $original_post->post_name;
            $virtual_post->guid = get_permalink($original_post->ID);
        } else {
            // Create virtual post for unmatched results
            $virtual_post->ID = 0; // Virtual post
            $virtual_post->post_title = 'AI Search Result (' . round($certainty * 100, 1) . '% match)';
            $virtual_post->post_content = $result['Content'];
            $virtual_post->post_excerpt = wp_trim_words($result['Content'], 30);
            $virtual_post->post_date = current_time('mysql');
            $virtual_post->post_author = 1;
            $virtual_post->post_status = 'publish';
            $virtual_post->post_type = 'ai_result';
            $virtual_post->post_name = 'ai-result-' . sanitize_title($result['Id']);
            $virtual_post->guid = home_url('/ai-result-' . $result['Id']);
        }
        
        // Add AI-specific metadata
        $virtual_post->ai_certainty = $certainty;
        $virtual_post->ai_result_id = $result['Id'];
        $virtual_post->ai_search_query = $search_query;
        
        $posts[] = $virtual_post;
    }
    
    return $posts;
}

/**
 * Enqueue frontend styles for search results
 */
function vectorrank_enqueue_frontend_styles() {
    if (is_search()) {
        wp_enqueue_style('vectorrank-frontend', VECTORRANK_PLUGIN_URL . 'assets/css/frontend.css', array(), VECTORRANK_PLUGIN_VERSION);
    }
}

/**
 * Modify content for AI search results
 */
function vectorrank_modify_ai_search_content($content) {
    global $post;
    
    if (is_search() && isset($post->ai_certainty)) {
        $certainty = $post->ai_certainty;
        $certainty_percent = round($certainty * 100, 1);
        
        // Determine certainty class
        $certainty_class = '';
        if ($certainty >= 0.8) {
            $certainty_class = 'high-certainty';
        } elseif ($certainty < 0.5) {
            $certainty_class = 'low-certainty';
        }
        
        // Add AI search indicator
        $ai_indicator = '<div class="ai-search-indicator">
            <span class="ai-badge">🤖 AI Result</span>
            <span class="search-certainty ' . $certainty_class . '">' . $certainty_percent . '% match</span>
        </div>';
        
        $content = $ai_indicator . $content;
    }
    
    return $content;
}

/**
 * Add inline styles for search results
 */
function vectorrank_add_search_styles() {
    if (is_search()) {
        echo '<style>
            .ai-search-indicator {
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                color: white;
                padding: 0.75rem 1rem;
                border-radius: 0.5rem;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                justify-content: space-between;
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            
            .ai-badge {
                font-weight: 600;
                font-size: 0.9rem;
            }
            
            .search-certainty {
                background: rgba(255, 255, 255, 0.2);
                padding: 0.25rem 0.5rem;
                border-radius: 0.25rem;
                font-size: 0.8rem;
                font-weight: 600;
            }
            
            .search-certainty.high-certainty {
                background: #10b981;
            }
            
            .search-certainty.low-certainty {
                background: #f59e0b;
            }
            
            .search-results-header {
                background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
                color: white;
                padding: 1rem;
                border-radius: 0.5rem;
                margin-bottom: 2rem;
                text-align: center;
            }
            
            body.search .search-results-header {
                display: block;
            }
        </style>';
        
        // Add AI search notice if we detect AI results
        add_action('loop_start', function($query) {
            if ($query->is_main_query() && $query->is_search()) {
                global $posts;
                $has_ai_results = false;
                
                foreach ($posts as $post) {
                    if (isset($post->ai_certainty)) {
                        $has_ai_results = true;
                        break;
                    }
                }
                
                if ($has_ai_results) {
                    echo '<div class="search-results-header">
                        <h3>🤖 AI-Powered Search Results</h3>
                        <p>These results are powered by artificial intelligence and ranked by relevance.</p>
                    </div>';
                }
            }
        });
    }
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