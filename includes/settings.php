<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include shared header
include_once VECTORRANK_PLUGIN_PATH . 'includes/header.php';
?>

    <div class="vectorrank-container">
        <div class="vectorrank-sidebar">
            <nav class="vectorrank-nav">
                <ul>
                    <li><a href="#login" class="nav-link <?php echo !$is_logged_in ? 'active' : ''; ?>" data-page="login">
                        <span class="nav-icon">🔐</span>
                        <?php _e('Login', 'vectorrank'); ?>
                    </a></li>
                    <li><a href="#features" class="nav-link <?php echo $is_logged_in ? 'active' : ''; ?>" data-page="features">
                        <span class="nav-icon">⚡</span>
                        <?php _e('Features', 'vectorrank'); ?>
                    </a></li>
                    <li><a href="#content" class="nav-link" data-page="content">
                        <span class="nav-icon">📄</span>
                        <?php _e('Content', 'vectorrank'); ?>
                    </a></li>
                </ul>
            </nav>
        </div>

        <div class="vectorrank-main">
            <!-- Login Page -->
            <div id="page-login" class="vectorrank-page <?php echo !$is_logged_in ? 'active' : ''; ?>">
                <?php if (!$is_logged_in): ?>
                    <!-- Login Form -->
                    <div class="login-container">
                        <div class="login-card">
                            <h2><?php _e('Connect to VectorRank', 'vectorrank'); ?></h2>
                            <p class="login-description"><?php _e('Sign in to access AI-powered search and recommendation features', 'vectorrank'); ?></p>
                            
                            <form id="vectorrank-login-form" class="login-form">
                                <div class="form-group">
                                    <label for="email"><?php _e('Email', 'vectorrank'); ?></label>
                                    <input type="text" id="email" name="email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="password"><?php _e('Password', 'vectorrank'); ?></label>
                                    <input type="password" id="password" name="password" required>
                                </div>
                                
                                <button type="submit" class="button button-primary button-large login-btn">
                                    <span class="btn-text"><?php _e('Login / Register', 'vectorrank'); ?></span>
                                    <span class="btn-loader"></span>
                                </button>
                            </form>
                            
                            <div class="login-footer">
                                <p><?php _e("Don't have an account?", 'vectorrank'); ?> <a href="#" target="_blank"><?php _e('Create one here', 'vectorrank'); ?></a></p>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- User Info Card -->
                    <div class="user-info-container">
                        <div class="user-info-card">
                            <div class="user-info-header">
                                <div class="user-avatar">
                                    <span class="avatar-icon">👤</span>
                                </div>
                                <div class="user-details">
                                    <h2><?php _e('Connected to VectorRank', 'vectorrank'); ?></h2>
                                    <p class="user-email">
                                        <?php 
                                        // Debug: Let's see what's in settings
                                        if (defined('WP_DEBUG') && WP_DEBUG) {
                                            error_log('[VectorRank Debug] Settings in user card: ' . print_r($settings, true));
                                        }
                                        
                                        $user_email = isset($settings['email']) ? $settings['email'] : __('No email available', 'vectorrank');
                                        echo esc_html($user_email); 
                                        ?>
                                    </p>
                                    <span class="connection-status active">
                                        <span class="status-dot"></span>
                                        <?php _e('Connected', 'vectorrank'); ?>
                                    </span>
                                </div>
                                <button id="vectorrank-logout" class="button button-secondary logout-btn">
                                    <?php _e('Logout', 'vectorrank'); ?>
                                </button>
                            </div>
                            
                            <div class="user-info-content">
                                <div class="info-grid">
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('Status', 'vectorrank'); ?></span>
                                        <span class="info-value status-<?php echo esc_attr($settings['user_status'] ?? 'active'); ?>">
                                            <?php echo esc_html(ucfirst($settings['user_status'] ?? 'active')); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('Connected Since', 'vectorrank'); ?></span>
                                        <span class="info-value">
                                            <?php 
                                            if (isset($settings['login_time'])) {
                                                echo date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $settings['login_time']);
                                            } else {
                                                echo __('Just now', 'vectorrank');
                                            }
                                            ?>
                                        </span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('Active Features', 'vectorrank'); ?></span>
                                        <span class="info-value">
                                            <?php echo count(array_filter($features)); ?>/<?php echo count($features); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <span class="info-label"><?php _e('API Token', 'vectorrank'); ?></span>
                                        <span class="info-value">
                                            <?php if (isset($settings['client_token']) && !empty($settings['client_token'])): ?>
                                                <span class="status-active">✓ <?php _e('Valid', 'vectorrank'); ?></span>
                                            <?php else: ?>
                                                <span class="status-inactive">✗ <?php _e('Missing', 'vectorrank'); ?></span>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="user-actions">
                                    <button class="button button-primary test-connection-btn">
                                        <?php _e('Test Connection', 'vectorrank'); ?>
                                    </button>
                                    <button class="button button-secondary refresh-token-btn">
                                        <?php _e('Refresh Token', 'vectorrank'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Features Page -->
            <div id="page-features" class="vectorrank-page <?php echo $is_logged_in ? 'active' : ''; ?>">
                <div class="features-header">
                    <h2><?php _e('AI-Powered Features', 'vectorrank'); ?></h2>
                    <p><?php _e('Unlock the power of artificial intelligence for your WordPress site', 'vectorrank'); ?></p>
                    
                    <div class="features-actions">
                        <button class="button button-primary button-large save-features-btn">
                            <span class="btn-text"><?php _e('Save & Sync Features', 'vectorrank'); ?></span>
                            <span class="btn-loader"></span>
                        </button>
                    </div>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card" data-feature="ai_search">
                        <div class="feature-header">
                            <div class="feature-icon">🔍</div>
                            <div class="feature-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" class="feature-checkbox" data-feature="ai_search" <?php echo (!isset($features['ai_search']) || $features['ai_search']) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="feature-content">
                            <h3><?php _e('AI Powered Search', 'vectorrank'); ?></h3>
                            <p><?php _e('Intelligent search that understands context and user intent, delivering more accurate results than traditional keyword matching.', 'vectorrank'); ?></p>
                            <div class="feature-status">
                                <span class="status-badge <?php echo (!isset($features['ai_search']) || $features['ai_search']) ? 'active' : 'inactive'; ?>">
                                    <?php echo (!isset($features['ai_search']) || $features['ai_search']) ? __('Active', 'vectorrank') : __('Inactive', 'vectorrank'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card" data-feature="autocomplete">
                        <div class="feature-header">
                            <div class="feature-icon">✨</div>
                            <div class="feature-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" class="feature-checkbox" data-feature="autocomplete" <?php echo (!isset($features['autocomplete']) || $features['autocomplete']) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="feature-content">
                            <h3><?php _e('Search Bar Autocomplete & Spelling Fixer', 'vectorrank'); ?></h3>
                            <p><?php _e('Smart autocomplete suggestions and automatic spelling correction to help users find what they\'re looking for faster.', 'vectorrank'); ?></p>
                            <div class="feature-status">
                                <span class="status-badge <?php echo (!isset($features['autocomplete']) || $features['autocomplete']) ? 'active' : 'inactive'; ?>">
                                    <?php echo (!isset($features['autocomplete']) || $features['autocomplete']) ? __('Active', 'vectorrank') : __('Inactive', 'vectorrank'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card" data-feature="cross_sell">
                        <div class="feature-header">
                            <div class="feature-icon">🛒</div>
                            <div class="feature-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" class="feature-checkbox" data-feature="cross_sell" <?php echo (!isset($features['cross_sell']) || $features['cross_sell']) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="feature-content">
                            <h3><?php _e('AI Cross-Sell Recommendations', 'vectorrank'); ?></h3>
                            <p><?php _e('Intelligent product recommendations that increase average order value by suggesting complementary items based on user behavior.', 'vectorrank'); ?></p>
                            <div class="feature-status">
                                <span class="status-badge <?php echo (!isset($features['cross_sell']) || $features['cross_sell']) ? 'active' : 'inactive'; ?>">
                                    <?php echo (!isset($features['cross_sell']) || $features['cross_sell']) ? __('Active', 'vectorrank') : __('Inactive', 'vectorrank'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card" data-feature="personalized_recommendations">
                        <div class="feature-header">
                            <div class="feature-icon">👤</div>
                            <div class="feature-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" class="feature-checkbox" data-feature="personalized_recommendations" <?php echo (!isset($features['personalized_recommendations']) || $features['personalized_recommendations']) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="feature-content">
                            <h3><?php _e('Customer Specialized Recommendations', 'vectorrank'); ?></h3>
                            <p><?php _e('Personalized recommendations tailored to individual customer preferences, shopping history, and browsing behavior.', 'vectorrank'); ?></p>
                            <div class="feature-status">
                                <span class="status-badge <?php echo (!isset($features['personalized_recommendations']) || $features['personalized_recommendations']) ? 'active' : 'inactive'; ?>">
                                    <?php echo (!isset($features['personalized_recommendations']) || $features['personalized_recommendations']) ? __('Active', 'vectorrank') : __('Inactive', 'vectorrank'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card" data-feature="favorite_popup">
                        <div class="feature-header">
                            <div class="feature-icon">💝</div>
                            <div class="feature-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" class="feature-checkbox" data-feature="favorite_popup" <?php echo (!isset($features['favorite_popup']) || $features['favorite_popup']) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="feature-content">
                            <h3><?php _e('Favorite Product Popup Suggestions', 'vectorrank'); ?></h3>
                            <p><?php _e('Smart popup recommendations showing customers their favorite products and articles based on their interaction patterns.', 'vectorrank'); ?></p>
                            <div class="feature-status">
                                <span class="status-badge <?php echo (!isset($features['favorite_popup']) || $features['favorite_popup']) ? 'active' : 'inactive'; ?>">
                                    <?php echo (!isset($features['favorite_popup']) || $features['favorite_popup']) ? __('Active', 'vectorrank') : __('Inactive', 'vectorrank'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="feature-card" data-feature="email_recommendations">
                        <div class="feature-header">
                            <div class="feature-icon">📧</div>
                            <div class="feature-toggle">
                                <label class="toggle-switch">
                                    <input type="checkbox" class="feature-checkbox" data-feature="email_recommendations" <?php echo (!isset($features['email_recommendations']) || $features['email_recommendations']) ? 'checked' : ''; ?>>
                                    <span class="toggle-slider"></span>
                                </label>
                            </div>
                        </div>
                        <div class="feature-content">
                            <h3><?php _e('Customer Product Recommendation Mailing', 'vectorrank'); ?></h3>
                            <p><?php _e('Automated email campaigns with personalized product recommendations to re-engage customers and drive sales.', 'vectorrank'); ?></p>
                            <div class="feature-status">
                                <span class="status-badge <?php echo (!isset($features['email_recommendations']) || $features['email_recommendations']) ? 'active' : 'inactive'; ?>">
                                    <?php echo (!isset($features['email_recommendations']) || $features['email_recommendations']) ? __('Active', 'vectorrank') : __('Inactive', 'vectorrank'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Page -->
            <div id="page-content" class="vectorrank-page">
                <div class="content-header">
                    <h2><?php _e('WordPress Content', 'vectorrank'); ?></h2>
                    <p><?php _e('Manage and optimize your content with AI-powered insights', 'vectorrank'); ?></p>
                    
                    <div class="content-filters">
                        <button class="filter-btn active" data-type="posts"><?php _e('Posts', 'vectorrank'); ?></button>
                        <?php if (class_exists('WooCommerce')): ?>
                        <button class="filter-btn" data-type="products"><?php _e('Products', 'vectorrank'); ?></button>
                        <?php endif; ?>
                        <button class="refresh-content button button-secondary"><?php _e('Refresh', 'vectorrank'); ?></button>
                    </div>
                </div>
                
                <div class="content-list">
                    <div class="content-loading">
                        <div class="loading-spinner"></div>
                        <p><?php _e('Loading content...', 'vectorrank'); ?></p>
                    </div>
                    <div id="content-items" class="content-items"></div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div id="vectorrank-messages" class="vectorrank-messages"></div>
</div>