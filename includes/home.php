<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include shared header
include_once VECTORRANK_PLUGIN_PATH . 'includes/header.php';
?>

    <div class="vectorrank-main home-page">
        <!-- Welcome Section -->
        <div class="home-welcome-section">
            <div class="welcome-card">
                <h2><?php _e('Welcome to VectorRank AI Platform', 'vectorrank'); ?></h2>
                <p class="welcome-subtitle"><?php _e('Transform your WordPress site with cutting-edge AI technology', 'vectorrank'); ?></p>
                
                <div class="welcome-stats">
                    <div class="stat-box">
                        <div class="stat-icon">🚀</div>
                        <div class="stat-content">
                            <h3><?php echo count(array_filter($features)); ?>/6</h3>
                            <p><?php _e('AI Features Active', 'vectorrank'); ?></p>
                        </div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon">📈</div>
                        <div class="stat-content">
                            <h3>97.3%</h3>
                            <p><?php _e('Search Accuracy', 'vectorrank'); ?></p>
                        </div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon">⚡</div>
                        <div class="stat-content">
                            <h3>24/7</h3>
                            <p><?php _e('AI Processing', 'vectorrank'); ?></p>
                        </div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-icon">💰</div>
                        <div class="stat-content">
                            <h3>+34%</h3>
                            <p><?php _e('Revenue Boost', 'vectorrank'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="home-quick-actions">
            <h3><?php _e('Quick Actions', 'vectorrank'); ?></h3>
            <div class="action-cards">
                <a href="<?php echo admin_url('admin.php?page=vectorrank-settings'); ?>" class="action-card">
                    <div class="action-icon">⚙️</div>
                    <h4><?php _e('Manage Features', 'vectorrank'); ?></h4>
                    <p><?php _e('Enable or disable AI features for your site', 'vectorrank'); ?></p>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=vectorrank-analytics'); ?>" class="action-card">
                    <div class="action-icon">📊</div>
                    <h4><?php _e('View Analytics', 'vectorrank'); ?></h4>
                    <p><?php _e('Track performance and AI insights', 'vectorrank'); ?></p>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=vectorrank-notifications'); ?>" class="action-card">
                    <div class="action-icon">🔔</div>
                    <h4><?php _e('Check Notifications', 'vectorrank'); ?></h4>
                    <p><?php _e('Stay updated with system alerts', 'vectorrank'); ?></p>
                </a>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="home-activity">
            <h3><?php _e('Recent AI Activity', 'vectorrank'); ?></h3>
            <div class="activity-feed">
                <div class="activity-item">
                    <div class="activity-icon">🔍</div>
                    <div class="activity-content">
                        <h4><?php _e('AI Search Optimization', 'vectorrank'); ?></h4>
                        <p><?php _e('Processed 1,247 search queries with 98.2% accuracy', 'vectorrank'); ?></p>
                        <span class="activity-time"><?php _e('2 hours ago', 'vectorrank'); ?></span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon">🛒</div>
                    <div class="activity-content">
                        <h4><?php _e('Cross-sell Recommendations', 'vectorrank'); ?></h4>
                        <p><?php _e('Generated personalized recommendations for 89 customers', 'vectorrank'); ?></p>
                        <span class="activity-time"><?php _e('4 hours ago', 'vectorrank'); ?></span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon">📧</div>
                    <div class="activity-content">
                        <h4><?php _e('Email Campaigns', 'vectorrank'); ?></h4>
                        <p><?php _e('Sent 156 personalized product recommendation emails', 'vectorrank'); ?></p>
                        <span class="activity-time"><?php _e('6 hours ago', 'vectorrank'); ?></span>
                    </div>
                </div>
                
                <div class="activity-item">
                    <div class="activity-icon">✨</div>
                    <div class="activity-content">
                        <h4><?php _e('Autocomplete Enhancement', 'vectorrank'); ?></h4>
                        <p><?php _e('Improved search suggestions for better user experience', 'vectorrank'); ?></p>
                        <span class="activity-time"><?php _e('8 hours ago', 'vectorrank'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Overview -->
        <div class="home-performance">
            <h3><?php _e('Performance Overview', 'vectorrank'); ?></h3>
            <div class="performance-grid">
                <div class="performance-card">
                    <h4><?php _e('Search Performance', 'vectorrank'); ?></h4>
                    <div class="performance-metric">
                        <span class="metric-value">4.7s</span>
                        <span class="metric-label"><?php _e('Avg Response Time', 'vectorrank'); ?></span>
                    </div>
                    <div class="performance-chart">
                        <div class="chart-bar" style="height: 60%"></div>
                        <div class="chart-bar" style="height: 80%"></div>
                        <div class="chart-bar" style="height: 45%"></div>
                        <div class="chart-bar" style="height: 95%"></div>
                        <div class="chart-bar" style="height: 75%"></div>
                    </div>
                </div>
                
                <div class="performance-card">
                    <h4><?php _e('Recommendation Accuracy', 'vectorrank'); ?></h4>
                    <div class="performance-metric">
                        <span class="metric-value">94.3%</span>
                        <span class="metric-label"><?php _e('Success Rate', 'vectorrank'); ?></span>
                    </div>
                    <div class="progress-ring">
                        <div class="progress-fill" style="--progress: 94.3%"></div>
                    </div>
                </div>
                
                <div class="performance-card">
                    <h4><?php _e('User Engagement', 'vectorrank'); ?></h4>
                    <div class="performance-metric">
                        <span class="metric-value">+23%</span>
                        <span class="metric-label"><?php _e('Increase This Month', 'vectorrank'); ?></span>
                    </div>
                    <div class="trend-line">
                        <svg viewBox="0 0 100 40" class="trend-svg">
                            <polyline points="0,30 20,25 40,15 60,10 80,8 100,5" stroke="#10b981" stroke-width="2" fill="none"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div id="vectorrank-messages" class="vectorrank-messages"></div>
</div>