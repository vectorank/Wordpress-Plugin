<?php
/**
 * VectorRank API Manager
 * 
 * Central API management class for handling all communications with VectorRank SaaS
 * Provides secure, reliable send/receive methods with error handling and logging
 * 
 * @package VectorRank
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VectorRankApiManager {
    
    /**
     * API Configuration
     */
    private $api_base_url = 'https://api.vectorrank.com/v1/';
    private $api_timeout = 30;
    private $max_retries = 3;
    private $retry_delay = 1; // seconds
    
    /**
     * Plugin settings
     */
    private $settings;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->settings = get_option('vectorrank_settings', array());
        
        // Allow API URL override via settings
        if (!empty($this->settings['api_url'])) {
            $this->api_base_url = trailingslashit($this->settings['api_url']);
        }
        
        // Allow timeout override via settings
        if (!empty($this->settings['api_timeout'])) {
            $this->api_timeout = intval($this->settings['api_timeout']);
        }
    }
    
    /**
     * Send POST request to API
     * 
     * @param string $endpoint API endpoint (without base URL)
     * @param array $data Data to send
     * @param array $headers Additional headers
     * @return array|WP_Error Response data or error
     */
    public function post($endpoint, $data = array(), $headers = array()) {
        return $this->send_request('POST', $endpoint, $data, $headers);
    }
    
    /**
     * Send GET request to API
     * 
     * @param string $endpoint API endpoint (without base URL)
     * @param array $params URL parameters
     * @param array $headers Additional headers
     * @return array|WP_Error Response data or error
     */
    public function get($endpoint, $params = array(), $headers = array()) {
        // Add params to URL for GET requests
        if (!empty($params)) {
            $endpoint = add_query_arg($params, $endpoint);
        }
        
        return $this->send_request('GET', $endpoint, null, $headers);
    }
    
    /**
     * Send PUT request to API
     * 
     * @param string $endpoint API endpoint (without base URL)
     * @param array $data Data to send
     * @param array $headers Additional headers
     * @return array|WP_Error Response data or error
     */
    public function put($endpoint, $data = array(), $headers = array()) {
        return $this->send_request('PUT', $endpoint, $data, $headers);
    }
    
    /**
     * Send DELETE request to API
     * 
     * @param string $endpoint API endpoint (without base URL)
     * @param array $headers Additional headers
     * @return array|WP_Error Response data or error
     */
    public function delete($endpoint, $headers = array()) {
        return $this->send_request('DELETE', $endpoint, null, $headers);
    }
    
    /**
     * Core method to send HTTP requests with retry logic
     * 
     * @param string $method HTTP method
     * @param string $endpoint API endpoint
     * @param array|null $data Request data
     * @param array $headers Additional headers
     * @return array|WP_Error Response data or error
     */
    private function send_request($method, $endpoint, $data = null, $headers = array()) {
        $url = $this->api_base_url . ltrim($endpoint, '/');
        $attempt = 0;
        
        while ($attempt < $this->max_retries) {
            $attempt++;
            
            // Prepare request arguments
            $args = array(
                'method' => $method,
                'timeout' => $this->api_timeout,
                'headers' => $this->get_default_headers($headers),
                'user-agent' => $this->get_user_agent(),
                'sslverify' => true,
            );
            
            // Add body data for POST/PUT requests
            if ($data !== null && in_array($method, array('POST', 'PUT', 'PATCH'))) {
                if (isset($args['headers']['Content-Type']) && $args['headers']['Content-Type'] === 'application/json') {
                    $args['body'] = wp_json_encode($data);
                } else {
                    $args['body'] = $data;
                }
            }
            
            // Log the request (for debugging)
            $this->log_request($method, $url, $args, $attempt);
            
            // Send the request
            $response = wp_remote_request($url, $args);
            
            // Check for WP errors (connection issues, timeouts, etc.)
            if (is_wp_error($response)) {
                $this->log_error('WP_Error', $response->get_error_message(), $url, $attempt);
                
                if ($attempt < $this->max_retries) {
                    sleep($this->retry_delay * $attempt); // Exponential backoff
                    continue;
                }
                
                return $response;
            }
            
            // Get response data
            $response_code = wp_remote_retrieve_response_code($response);
            $response_body = wp_remote_retrieve_body($response);
            $response_headers = wp_remote_retrieve_headers($response);
            
            // Log the response
            $this->log_response($response_code, $response_body, $url, $attempt);
            
            // Handle different response codes
            if ($response_code >= 200 && $response_code < 300) {
                // Success - parse and return data
                $parsed_data = $this->parse_response($response_body, $response_headers);
                
                if ($parsed_data === false) {
                    return new WP_Error('parse_error', 'Failed to parse API response', array(
                        'response_code' => $response_code,
                        'response_body' => $response_body
                    ));
                }
                
                return $parsed_data;
                
            } elseif ($response_code >= 400 && $response_code < 500) {
                // Client errors - don't retry
                $error_data = $this->parse_response($response_body, $response_headers);
                $error_message = isset($error_data['message']) ? $error_data['message'] : 'Client error occurred';
                
                return new WP_Error('client_error', $error_message, array(
                    'response_code' => $response_code,
                    'response_data' => $error_data
                ));
                
            } elseif ($response_code >= 500) {
                // Server errors - retry
                $this->log_error('Server Error', "HTTP {$response_code}", $url, $attempt);
                
                if ($attempt < $this->max_retries) {
                    sleep($this->retry_delay * $attempt);
                    continue;
                }
                
                return new WP_Error('server_error', "Server error (HTTP {$response_code})", array(
                    'response_code' => $response_code,
                    'response_body' => $response_body
                ));
                
            } else {
                // Unexpected response codes
                return new WP_Error('unexpected_response', "Unexpected response code: {$response_code}", array(
                    'response_code' => $response_code,
                    'response_body' => $response_body
                ));
            }
        }
        
        // All retries exhausted
        return new WP_Error('max_retries_exceeded', 'Maximum retry attempts exceeded');
    }
    
    /**
     * Get default headers for API requests
     * 
     * @param array $additional_headers Additional headers to merge
     * @return array Complete headers array
     */
    private function get_default_headers($additional_headers = array()) {
        $default_headers = array(
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'X-VectorRank-Plugin-Version' => VECTORRANK_PLUGIN_VERSION,
            'X-WordPress-Version' => get_bloginfo('version'),
            'X-PHP-Version' => PHP_VERSION,
        );
        
        // Add API key if available
        $api_key = $this->get_api_key();
        if (!empty($api_key)) {
            $default_headers['Authorization'] = 'Bearer ' . $api_key;
        }
        
        // Add site identifier
        $site_id = $this->get_site_identifier();
        if (!empty($site_id)) {
            $default_headers['X-Site-ID'] = $site_id;
        }
        
        return array_merge($default_headers, $additional_headers);
    }
    
    /**
     * Get user agent string
     * 
     * @return string User agent
     */
    private function get_user_agent() {
        return sprintf(
            'VectorRank-WordPress/%s (WordPress/%s; PHP/%s; %s)',
            VECTORRANK_PLUGIN_VERSION,
            get_bloginfo('version'),
            PHP_VERSION,
            home_url()
        );
    }
    
    /**
     * Parse API response
     * 
     * @param string $body Response body
     * @param array $headers Response headers
     * @return array|false Parsed data or false on failure
     */
    private function parse_response($body, $headers) {
        // Check if response is JSON
        $content_type = isset($headers['content-type']) ? $headers['content-type'] : '';
        
        if (strpos($content_type, 'application/json') !== false) {
            $decoded = json_decode($body, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }
        
        // Return raw body if not JSON or JSON decode failed
        return array('raw_response' => $body);
    }
    
    /**
     * Get API key from settings
     * 
     * @return string API key
     */
    private function get_api_key() {
        // First try to use the client_token (JWT) from SaaS login
        if (isset($this->settings['client_token']) && !empty($this->settings['client_token'])) {
            return $this->settings['client_token'];
        }
        
        // Fallback to api_key if client_token is not available
        return isset($this->settings['api_key']) ? $this->settings['api_key'] : '';
    }
    
    /**
     * Get unique site identifier
     * 
     * @return string Site identifier
     */
    private function get_site_identifier() {
        $site_url = home_url();
        return md5($site_url . ABSPATH);
    }
    
    /**
     * Log API request (for debugging)
     * 
     * @param string $method HTTP method
     * @param string $url Request URL
     * @param array $args Request arguments
     * @param int $attempt Attempt number
     */
    private function log_request($method, $url, $args, $attempt) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                '[VectorRank API] Request #%d: %s %s',
                $attempt,
                $method,
                $url
            ));
        }
    }
    
    /**
     * Log API response (for debugging)
     * 
     * @param int $code Response code
     * @param string $body Response body
     * @param string $url Request URL
     * @param int $attempt Attempt number
     */
    private function log_response($code, $body, $url, $attempt) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log(sprintf(
                '[VectorRank API] Response #%d: HTTP %d for %s (body length: %d)',
                $attempt,
                $code,
                $url,
                strlen($body)
            ));
        }
    }
    
    /**
     * Log API errors
     * 
     * @param string $type Error type
     * @param string $message Error message
     * @param string $url Request URL
     * @param int $attempt Attempt number
     */
    private function log_error($type, $message, $url, $attempt) {
        error_log(sprintf(
            '[VectorRank API] %s #%d: %s for %s',
            $type,
            $attempt,
            $message,
            $url
        ));
    }
    
    /**
     * Test API connection
     * 
     * @return array Test results
     */
    public function test_connection() {
        $start_time = microtime(true);
        
        $response = $this->get('health');
        
        $end_time = microtime(true);
        $response_time = round(($end_time - $start_time) * 1000, 2); // ms
        
        if (is_wp_error($response)) {
            return array(
                'success' => false,
                'error' => $response->get_error_message(),
                'response_time' => $response_time
            );
        }
        
        return array(
            'success' => true,
            'response_time' => $response_time,
            'data' => $response
        );
    }
    
    /**
     * Get API status and limits
     * 
     * @return array|WP_Error API status data or error
     */
    public function get_api_status() {
        return $this->get('status');
    }
    
    /**
     * Common API endpoints for easy access
     */
    
    /**
     * Authenticate user with API
     * 
     * @param string $username Username
     * @param string $password Password
     * @return array|WP_Error Authentication response or error
     */
    public function authenticate($username, $password) {
        return $this->post('auth/login', array(
            'username' => $username,
            'password' => $password,
            'site_url' => home_url(),
            'site_id' => $this->get_site_identifier()
        ));
    }
    
    /**
     * Get user profile
     * 
     * @return array|WP_Error User profile data or error
     */
    public function get_user_profile() {
        return $this->get('user/profile');
    }
    
    /**
     * Get usage statistics
     * 
     * @param string $period Period (daily, weekly, monthly)
     * @param int $days Number of days to fetch
     * @return array|WP_Error Usage data or error
     */
    public function get_usage_stats($period = 'daily', $days = 30) {
        return $this->get('usage/stats', array(
            'period' => $period,
            'days' => $days
        ));
    }
    
    /**
     * Get billing information
     * 
     * @return array|WP_Error Billing data or error
     */
    public function get_billing_info() {
        return $this->get('billing/info');
    }
    
    /**
     * Search products using AI
     * 
     * @param string $query Search query
     * @param array $filters Additional filters
     * @return array|WP_Error Search results or error
     */
    public function search_products($query, $filters = array()) {
        return $this->post('search', array(
            'query' => $query,
            'filters' => $filters,
            'site_id' => $this->get_site_identifier()
        ));
    }
    
    /**
     * Get product recommendations
     * 
     * @param int $product_id Product ID
     * @param int $limit Number of recommendations
     * @return array|WP_Error Recommendations or error
     */
    public function get_recommendations($product_id, $limit = 10) {
        return $this->get('recommendations', array(
            'product_id' => $product_id,
            'limit' => $limit,
            'site_id' => $this->get_site_identifier()
        ));
    }
    
    /**
     * Track user behavior
     * 
     * @param string $event Event type
     * @param array $data Event data
     * @return array|WP_Error Tracking response or error
     */
    public function track_event($event, $data = array()) {
        return $this->post('analytics/track', array(
            'event' => $event,
            'data' => $data,
            'timestamp' => current_time('timestamp'),
            'site_id' => $this->get_site_identifier(),
            'user_id' => get_current_user_id(),
            'session_id' => session_id()
        ));
    }
    
    /**
     * Update plugin settings on server
     * 
     * @param array $settings Settings to update
     * @return array|WP_Error Update response or error
     */
    public function update_settings($settings) {
        return $this->put('settings', array(
            'settings' => $settings,
            'site_id' => $this->get_site_identifier()
        ));
    }
    
    /**
     * Report plugin error to server
     * 
     * @param string $error_type Error type
     * @param string $error_message Error message
     * @param array $context Additional context
     * @return array|WP_Error Report response or error
     */
    public function report_error($error_type, $error_message, $context = array()) {
        return $this->post('errors/report', array(
            'error_type' => $error_type,
            'error_message' => $error_message,
            'context' => $context,
            'site_id' => $this->get_site_identifier(),
            'plugin_version' => VECTORRANK_PLUGIN_VERSION,
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => PHP_VERSION,
            'timestamp' => current_time('timestamp')
        ));
    }
    
    /**
     * Test API call using stored client token
     * Use this to verify that the client token is working after login
     * 
     * @param string $endpoint Test endpoint (default: 'test')
     * @return array|WP_Error Test response or error
     */
    public function test_authenticated_call($endpoint = 'test') {
        $client_token = $this->get_api_key();
        
        if (empty($client_token)) {
            return new WP_Error('no_token', 'No client token available. Please login first.');
        }
        
        // Make a test call to verify the token works
        return $this->get($endpoint, array(
            'test_param' => 'client_token_verification',
            'timestamp' => current_time('timestamp')
        ));
    }
}

/**
 * Global function to get API manager instance
 * 
 * @return VectorRankApiManager
 */
function vectorrank_api() {
    static $instance = null;
    
    if ($instance === null) {
        $instance = new VectorRankApiManager();
    }
    
    return $instance;
}