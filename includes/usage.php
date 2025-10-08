<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include shared header
include_once VECTORRANK_PLUGIN_PATH . 'includes/header.php';
?>

    <div class="vectorrank-main usage-page">
        <!-- Usage Header -->
        <div class="usage-header">
            <h2><?php _e('API Usage & Billing', 'vectorrank'); ?></h2>
            <p><?php _e('Monitor your VectorRank API usage, billing information, and plan details', 'vectorrank'); ?></p>
            
            <div class="usage-actions">
                <button class="button button-secondary export-usage"><?php _e('Export Usage Report', 'vectorrank'); ?></button>
                <button class="button button-primary upgrade-plan"><?php _e('Upgrade Plan', 'vectorrank'); ?></button>
            </div>
        </div>

        <!-- Current Plan Overview -->
        <div class="plan-overview">
            <div class="plan-card current-plan">
                <div class="plan-header">
                    <h3><?php _e('Current Plan', 'vectorrank'); ?></h3>
                    <span class="plan-badge premium"><?php _e('Premium', 'vectorrank'); ?></span>
                </div>
                <div class="plan-details">
                    <div class="plan-price">
                        <span class="currency">$</span>
                        <span class="amount">49</span>
                        <span class="period">/month</span>
                    </div>
                    <div class="plan-features">
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span><?php _e('100,000 API calls/month', 'vectorrank'); ?></span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span><?php _e('All AI features included', 'vectorrank'); ?></span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span><?php _e('Priority support', 'vectorrank'); ?></span>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">✅</span>
                            <span><?php _e('Advanced analytics', 'vectorrank'); ?></span>
                        </div>
                    </div>
                    <div class="plan-renewal">
                        <p><?php _e('Next billing date:', 'vectorrank'); ?> <strong>November 8, 2025</strong></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Usage Statistics -->
        <div class="usage-stats">
            <div class="usage-metric">
                <div class="metric-header">
                    <h3><?php _e('API Calls This Month', 'vectorrank'); ?></h3>
                    <span class="metric-icon">📞</span>
                </div>
                <div class="metric-content">
                    <div class="metric-value">67,432</div>
                    <div class="metric-total">of 100,000</div>
                    <div class="usage-bar">
                        <div class="usage-fill" style="width: 67.4%"></div>
                    </div>
                    <div class="usage-percentage">67.4% used</div>
                </div>
            </div>

            <div class="usage-metric">
                <div class="metric-header">
                    <h3><?php _e('Search Requests', 'vectorrank'); ?></h3>
                    <span class="metric-icon">🔍</span>
                </div>
                <div class="metric-content">
                    <div class="metric-value">42,847</div>
                    <div class="metric-change positive">+12.5% from last month</div>
                </div>
            </div>

            <div class="usage-metric">
                <div class="metric-header">
                    <h3><?php _e('Recommendations Generated', 'vectorrank'); ?></h3>
                    <span class="metric-icon">🎯</span>
                </div>
                <div class="metric-content">
                    <div class="metric-value">18,923</div>
                    <div class="metric-change positive">+8.7% from last month</div>
                </div>
            </div>

            <div class="usage-metric">
                <div class="metric-header">
                    <h3><?php _e('Email Campaigns Sent', 'vectorrank'); ?></h3>
                    <span class="metric-icon">📧</span>
                </div>
                <div class="metric-content">
                    <div class="metric-value">5,662</div>
                    <div class="metric-change positive">+23.1% from last month</div>
                </div>
            </div>
        </div>

        <!-- Usage Timeline -->
        <div class="usage-timeline">
            <div class="timeline-header">
                <h3><?php _e('Usage Timeline (Last 30 Days)', 'vectorrank'); ?></h3>
                <div class="timeline-controls">
                    <select class="timeline-period">
                        <option value="7"><?php _e('Last 7 Days', 'vectorrank'); ?></option>
                        <option value="30" selected><?php _e('Last 30 Days', 'vectorrank'); ?></option>
                        <option value="90"><?php _e('Last 90 Days', 'vectorrank'); ?></option>
                    </select>
                </div>
            </div>
            <div class="timeline-chart">
                <div class="chart-container">
                    <div class="chart-y-axis">
                        <span>5000</span>
                        <span>4000</span>
                        <span>3000</span>
                        <span>2000</span>
                        <span>1000</span>
                        <span>0</span>
                    </div>
                    <div class="chart-area">
                        <div class="chart-bars">
                            <?php for ($i = 1; $i <= 30; $i++): ?>
                                <div class="chart-day" style="height: <?php echo rand(20, 80); ?>%;" title="Day <?php echo $i; ?>: <?php echo rand(1500, 4500); ?> calls"></div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Feature Usage Breakdown -->
        <div class="feature-usage-breakdown">
            <h3><?php _e('Feature Usage Breakdown', 'vectorrank'); ?></h3>
            <div class="feature-usage-grid">
                <div class="feature-usage-item">
                    <div class="feature-header">
                        <span class="feature-icon">🔍</span>
                        <h4><?php _e('AI Search', 'vectorrank'); ?></h4>
                    </div>
                    <div class="feature-stats">
                        <div class="stat-item">
                            <span class="stat-value">42,847</span>
                            <span class="stat-label"><?php _e('Requests', 'vectorrank'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">63.6%</span>
                            <span class="stat-label"><?php _e('Of Total Usage', 'vectorrank'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="feature-usage-item">
                    <div class="feature-header">
                        <span class="feature-icon">✨</span>
                        <h4><?php _e('Autocomplete', 'vectorrank'); ?></h4>
                    </div>
                    <div class="feature-stats">
                        <div class="stat-item">
                            <span class="stat-value">12,435</span>
                            <span class="stat-label"><?php _e('Requests', 'vectorrank'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">18.4%</span>
                            <span class="stat-label"><?php _e('Of Total Usage', 'vectorrank'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="feature-usage-item">
                    <div class="feature-header">
                        <span class="feature-icon">🛒</span>
                        <h4><?php _e('Cross-Sell', 'vectorrank'); ?></h4>
                    </div>
                    <div class="feature-stats">
                        <div class="stat-item">
                            <span class="stat-value">7,892</span>
                            <span class="stat-label"><?php _e('Requests', 'vectorrank'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">11.7%</span>
                            <span class="stat-label"><?php _e('Of Total Usage', 'vectorrank'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="feature-usage-item">
                    <div class="feature-header">
                        <span class="feature-icon">👤</span>
                        <h4><?php _e('Recommendations', 'vectorrank'); ?></h4>
                    </div>
                    <div class="feature-stats">
                        <div class="stat-item">
                            <span class="stat-value">3,456</span>
                            <span class="stat-label"><?php _e('Requests', 'vectorrank'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">5.1%</span>
                            <span class="stat-label"><?php _e('Of Total Usage', 'vectorrank'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="feature-usage-item">
                    <div class="feature-header">
                        <span class="feature-icon">💝</span>
                        <h4><?php _e('Popup Suggestions', 'vectorrank'); ?></h4>
                    </div>
                    <div class="feature-stats">
                        <div class="stat-item">
                            <span class="stat-value">542</span>
                            <span class="stat-label"><?php _e('Requests', 'vectorrank'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">0.8%</span>
                            <span class="stat-label"><?php _e('Of Total Usage', 'vectorrank'); ?></span>
                        </div>
                    </div>
                </div>

                <div class="feature-usage-item">
                    <div class="feature-header">
                        <span class="feature-icon">📧</span>
                        <h4><?php _e('Email Marketing', 'vectorrank'); ?></h4>
                    </div>
                    <div class="feature-stats">
                        <div class="stat-item">
                            <span class="stat-value">260</span>
                            <span class="stat-label"><?php _e('Requests', 'vectorrank'); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-value">0.4%</span>
                            <span class="stat-label"><?php _e('Of Total Usage', 'vectorrank'); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing History -->
        <div class="billing-history">
            <div class="billing-header">
                <h3><?php _e('Billing History', 'vectorrank'); ?></h3>
                <button class="button button-secondary download-invoice"><?php _e('Download Invoice', 'vectorrank'); ?></button>
            </div>
            <div class="billing-table">
                <table>
                    <thead>
                        <tr>
                            <th><?php _e('Date', 'vectorrank'); ?></th>
                            <th><?php _e('Description', 'vectorrank'); ?></th>
                            <th><?php _e('Amount', 'vectorrank'); ?></th>
                            <th><?php _e('Status', 'vectorrank'); ?></th>
                            <th><?php _e('Action', 'vectorrank'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Oct 8, 2025</td>
                            <td>VectorRank Premium - Monthly</td>
                            <td>$49.00</td>
                            <td><span class="status-badge paid"><?php _e('Paid', 'vectorrank'); ?></span></td>
                            <td><a href="#" class="invoice-link"><?php _e('Download', 'vectorrank'); ?></a></td>
                        </tr>
                        <tr>
                            <td>Sep 8, 2025</td>
                            <td>VectorRank Premium - Monthly</td>
                            <td>$49.00</td>
                            <td><span class="status-badge paid"><?php _e('Paid', 'vectorrank'); ?></span></td>
                            <td><a href="#" class="invoice-link"><?php _e('Download', 'vectorrank'); ?></a></td>
                        </tr>
                        <tr>
                            <td>Aug 8, 2025</td>
                            <td>VectorRank Premium - Monthly</td>
                            <td>$49.00</td>
                            <td><span class="status-badge paid"><?php _e('Paid', 'vectorrank'); ?></span></td>
                            <td><a href="#" class="invoice-link"><?php _e('Download', 'vectorrank'); ?></a></td>
                        </tr>
                        <tr>
                            <td>Jul 8, 2025</td>
                            <td>VectorRank Starter - Monthly</td>
                            <td>$19.00</td>
                            <td><span class="status-badge paid"><?php _e('Paid', 'vectorrank'); ?></span></td>
                            <td><a href="#" class="invoice-link"><?php _e('Download', 'vectorrank'); ?></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Plan Comparison -->
        <div class="plan-comparison">
            <h3><?php _e('Available Plans', 'vectorrank'); ?></h3>
            <div class="plans-grid">
                <div class="plan-card">
                    <h4><?php _e('Starter', 'vectorrank'); ?></h4>
                    <div class="plan-price">
                        <span class="currency">$</span>
                        <span class="amount">19</span>
                        <span class="period">/month</span>
                    </div>
                    <ul class="plan-features">
                        <li>25,000 API calls/month</li>
                        <li>Basic AI features</li>
                        <li>Email support</li>
                        <li>Basic analytics</li>
                    </ul>
                    <button class="button button-secondary"><?php _e('Downgrade', 'vectorrank'); ?></button>
                </div>

                <div class="plan-card current">
                    <div class="plan-ribbon"><?php _e('Current', 'vectorrank'); ?></div>
                    <h4><?php _e('Premium', 'vectorrank'); ?></h4>
                    <div class="plan-price">
                        <span class="currency">$</span>
                        <span class="amount">49</span>
                        <span class="period">/month</span>
                    </div>
                    <ul class="plan-features">
                        <li>100,000 API calls/month</li>
                        <li>All AI features</li>
                        <li>Priority support</li>
                        <li>Advanced analytics</li>
                    </ul>
                    <button class="button button-primary disabled"><?php _e('Current Plan', 'vectorrank'); ?></button>
                </div>

                <div class="plan-card">
                    <h4><?php _e('Enterprise', 'vectorrank'); ?></h4>
                    <div class="plan-price">
                        <span class="currency">$</span>
                        <span class="amount">149</span>
                        <span class="period">/month</span>
                    </div>
                    <ul class="plan-features">
                        <li>500,000 API calls/month</li>
                        <li>All AI features + Custom</li>
                        <li>24/7 phone support</li>
                        <li>White-label options</li>
                    </ul>
                    <button class="button button-primary"><?php _e('Upgrade', 'vectorrank'); ?></button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div id="vectorrank-messages" class="vectorrank-messages"></div>
</div>