<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$settings = get_option('vectorrank_settings', array());
$is_logged_in = isset($settings['logged_in']) && $settings['logged_in'];
// $is_logged_in = true; // For testing purposes, remove this line in production
// Ensure all features default to true (active)
$default_features = array(
    'ai_search' => true,
    'autocomplete' => true,
    'cross_sell' => true,
    'personalized_recommendations' => true,
    'favorite_popup' => true,
    'email_recommendations' => true,
);

$features = isset($settings['features']) ? array_merge($default_features, $settings['features']) : $default_features;

// Get current page from URL parameter
$current_page = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 'vectorrank-home';
?>

<div class="wrap vectorrank-admin">
    <div class="vectorrank-header">
        <div class="vectorrank-logo">
            <h1><span class="vectorrank-icon">🚀</span> VectorRank</h1>
            <p>AI-Powered Search & Recommendations Platform</p>
        </div>
        
        <?php if ($is_logged_in): ?>
        <!-- Main Navigation Bar -->
        <nav class="vectorrank-navbar">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a href="<?php echo admin_url('admin.php?page=vectorrank-home'); ?>" class="nav-item-link <?php echo ($current_page === 'vectorrank-home') ? 'active' : ''; ?>">
                        <span class="nav-item-icon">🏠</span>
                        <span class="nav-item-text"><?php _e('Home', 'vectorrank'); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo admin_url('admin.php?page=vectorrank-notifications'); ?>" class="nav-item-link <?php echo ($current_page === 'vectorrank-notifications') ? 'active' : ''; ?>">
                        <span class="nav-item-icon">🔔</span>
                        <span class="nav-item-text"><?php _e('Notifications', 'vectorrank'); ?></span>
                        <span class="notification-badge">3</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo admin_url('admin.php?page=vectorrank-analytics'); ?>" class="nav-item-link <?php echo ($current_page === 'vectorrank-analytics') ? 'active' : ''; ?>">
                        <span class="nav-item-icon">📊</span>
                        <span class="nav-item-text"><?php _e('Analytics', 'vectorrank'); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo admin_url('admin.php?page=vectorrank-usage'); ?>" class="nav-item-link <?php echo ($current_page === 'vectorrank-usage') ? 'active' : ''; ?>">
                        <span class="nav-item-icon">📈</span>
                        <span class="nav-item-text"><?php _e('Usage', 'vectorrank'); ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo admin_url('admin.php?page=vectorrank-settings'); ?>" class="nav-item-link <?php echo ($current_page === 'vectorrank-settings') ? 'active' : ''; ?>">
                        <span class="nav-item-icon">⚙️</span>
                        <span class="nav-item-text"><?php _e('Settings', 'vectorrank'); ?></span>
                    </a>
                </li>
            </ul>
        </nav>
        
        <!-- Right Side Actions -->
        <div class="vectorrank-top-nav">
            <div class="nav-stats">
                <div class="stat-item">
                    <span class="stat-number"><?php echo count(array_filter($features)); ?></span>
                    <span class="stat-label"><?php _e('Active Features', 'vectorrank'); ?></span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label"><?php _e('AI Processing', 'vectorrank'); ?></span>
                </div>
            </div>
            
            <div class="nav-actions">
                <div class="connection-status">
                    <span class="status-indicator online"></span>
                    <span class="status-text"><?php _e('Connected', 'vectorrank'); ?></span>
                </div>
                
                <div class="nav-dropdown">
                    <button class="nav-dropdown-btn" id="nav-dropdown-toggle">
                        <div class="user-profile-avatar">
                            <?php echo strtoupper(substr($settings['username'] ?? 'VR', 0, 2)); ?>
                        </div>
                        <span class="user-name"><?php echo esc_html($settings['username'] ?? 'User'); ?></span>
                        <span class="dropdown-arrow">▼</span>
                    </button>
                    
                    <div class="nav-dropdown-menu" id="nav-dropdown-menu">
                        <div class="dropdown-header">
                            <div class="user-info">
                                <div class="user-avatar-large">
                                    <?php echo strtoupper(substr($settings['username'] ?? 'VR', 0, 2)); ?>
                                </div>
                                <div class="user-details">
                                    <h4><?php echo esc_html($settings['username'] ?? 'User'); ?></h4>
                                    <p><?php _e('VectorRank Account', 'vectorrank'); ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="dropdown-menu-items">
                            <a href="#" class="dropdown-item">
                                <span class="item-icon">👤</span>
                                <?php _e('Account Settings', 'vectorrank'); ?>
                            </a>
                            <a href="<?php echo admin_url('admin.php?page=vectorrank-analytics'); ?>" class="dropdown-item">
                                <span class="item-icon">📊</span>
                                <?php _e('Analytics Dashboard', 'vectorrank'); ?>
                            </a>
                            <a href="#" class="dropdown-item">
                                <span class="item-icon">⚙️</span>
                                <?php _e('API Configuration', 'vectorrank'); ?>
                            </a>
                            <a href="#" class="dropdown-item">
                                <span class="item-icon">📚</span>
                                <?php _e('Documentation', 'vectorrank'); ?>
                            </a>
                            <div class="dropdown-divider"></div>
                            <button class="dropdown-item logout-btn" id="vectorrank-logout">
                                <span class="item-icon">🚪</span>
                                <?php _e('Logout', 'vectorrank'); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>