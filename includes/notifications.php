<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include shared header
include_once VECTORRANK_PLUGIN_PATH . 'includes/header.php';
?>

    <div class="vectorrank-main notifications-page">
        <!-- Notifications Header -->
        <div class="notifications-header">
            <h2><?php _e('Notifications Center', 'vectorrank'); ?></h2>
            <p><?php _e('Stay updated with real-time alerts and system notifications', 'vectorrank'); ?></p>
            
            <div class="notifications-actions">
                <button class="button button-secondary mark-all-read"><?php _e('Mark All as Read', 'vectorrank'); ?></button>
                <button class="button button-primary refresh-notifications"><?php _e('Refresh', 'vectorrank'); ?></button>
            </div>
        </div>

        <!-- Notification Filters -->
        <div class="notification-filters">
            <div class="filter-tabs">
                <button class="filter-tab active" data-filter="all"><?php _e('All', 'vectorrank'); ?> <span class="tab-count">12</span></button>
                <button class="filter-tab" data-filter="alerts"><?php _e('Alerts', 'vectorrank'); ?> <span class="tab-count">3</span></button>
                <button class="filter-tab" data-filter="updates"><?php _e('Updates', 'vectorrank'); ?> <span class="tab-count">5</span></button>
                <button class="filter-tab" data-filter="performance"><?php _e('Performance', 'vectorrank'); ?> <span class="tab-count">4</span></button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="notifications-list">
            <!-- Alert Notifications -->
            <div class="notification-item unread alert" data-type="alerts">
                <div class="notification-icon alert">🚨</div>
                <div class="notification-content">
                    <h4><?php _e('High Search Volume Detected', 'vectorrank'); ?></h4>
                    <p><?php _e('Search traffic has increased by 340% in the last hour. AI processing is automatically scaling to handle the load.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">5 minutes ago</span>
                        <span class="notification-category">System Alert</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="notification-btn view-details"><?php _e('View Details', 'vectorrank'); ?></button>
                </div>
            </div>
            
            <div class="notification-item unread alert" data-type="alerts">
                <div class="notification-icon alert">⚠️</div>
                <div class="notification-content">
                    <h4><?php _e('API Rate Limit Approaching', 'vectorrank'); ?></h4>
                    <p><?php _e('Your current plan is approaching the monthly API limit. Consider upgrading to avoid service interruption.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">2 hours ago</span>
                        <span class="notification-category">Billing Alert</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="notification-btn upgrade-plan"><?php _e('Upgrade Plan', 'vectorrank'); ?></button>
                </div>
            </div>

            <!-- Update Notifications -->
            <div class="notification-item unread update" data-type="updates">
                <div class="notification-icon update">🔄</div>
                <div class="notification-content">
                    <h4><?php _e('AI Model Updated', 'vectorrank'); ?></h4>
                    <p><?php _e('New machine learning model v2.3.1 has been deployed. Expect improved accuracy for product recommendations.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">1 hour ago</span>
                        <span class="notification-category">System Update</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="notification-btn view-changelog"><?php _e('View Changelog', 'vectorrank'); ?></button>
                </div>
            </div>
            
            <div class="notification-item read update" data-type="updates">
                <div class="notification-icon update">✨</div>
                <div class="notification-content">
                    <h4><?php _e('Feature Enhancement: Autocomplete', 'vectorrank'); ?></h4>
                    <p><?php _e('Autocomplete feature now supports multilingual queries and improved spelling correction algorithms.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">3 hours ago</span>
                        <span class="notification-category">Feature Update</span>
                    </div>
                </div>
            </div>

            <!-- Performance Notifications -->
            <div class="notification-item unread performance" data-type="performance">
                <div class="notification-icon performance">📈</div>
                <div class="notification-content">
                    <h4><?php _e('Performance Milestone Reached', 'vectorrank'); ?></h4>
                    <p><?php _e('Congratulations! Your AI search has achieved 97.8% accuracy rate, surpassing industry standards.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">6 hours ago</span>
                        <span class="notification-category">Achievement</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <button class="notification-btn share-achievement"><?php _e('Share', 'vectorrank'); ?></button>
                </div>
            </div>
            
            <div class="notification-item read performance" data-type="performance">
                <div class="notification-icon performance">💰</div>
                <div class="notification-content">
                    <h4><?php _e('Revenue Impact Report', 'vectorrank'); ?></h4>
                    <p><?php _e('AI recommendations generated $5,247 in additional revenue this week. View detailed analytics report.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">1 day ago</span>
                        <span class="notification-category">Revenue Report</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <a href="<?php echo admin_url('admin.php?page=vectorrank-analytics'); ?>" class="notification-btn view-analytics"><?php _e('View Analytics', 'vectorrank'); ?></a>
                </div>
            </div>
            
            <div class="notification-item read update" data-type="updates">
                <div class="notification-icon update">🔧</div>
                <div class="notification-content">
                    <h4><?php _e('Maintenance Completed', 'vectorrank'); ?></h4>
                    <p><?php _e('Scheduled maintenance has been completed successfully. All systems are running optimally.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">2 days ago</span>
                        <span class="notification-category">Maintenance</span>
                    </div>
                </div>
            </div>
            
            <div class="notification-item read performance" data-type="performance">
                <div class="notification-icon performance">🎯</div>
                <div class="notification-content">
                    <h4><?php _e('Search Accuracy Improved', 'vectorrank'); ?></h4>
                    <p><?php _e('Machine learning algorithms have been optimized, resulting in 12% improvement in search result accuracy.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">3 days ago</span>
                        <span class="notification-category">Performance</span>
                    </div>
                </div>
            </div>
            
            <div class="notification-item read update" data-type="updates">
                <div class="notification-icon update">🚀</div>
                <div class="notification-content">
                    <h4><?php _e('New Feature: Email Recommendations', 'vectorrank'); ?></h4>
                    <p><?php _e('Email recommendation feature is now available! Automatically send personalized product suggestions to your customers.', 'vectorrank'); ?></p>
                    <div class="notification-meta">
                        <span class="notification-time">1 week ago</span>
                        <span class="notification-category">New Feature</span>
                    </div>
                </div>
                <div class="notification-actions">
                    <a href="<?php echo admin_url('admin.php?page=vectorrank-settings'); ?>" class="notification-btn configure-feature"><?php _e('Configure', 'vectorrank'); ?></a>
                </div>
            </div>
        </div>

        <!-- Load More -->
        <div class="notifications-footer">
            <button class="button button-secondary load-more-notifications"><?php _e('Load More Notifications', 'vectorrank'); ?></button>
            <div class="notifications-summary">
                <p><?php _e('Showing 9 of 47 notifications', 'vectorrank'); ?></p>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div id="vectorrank-messages" class="vectorrank-messages"></div>
</div>