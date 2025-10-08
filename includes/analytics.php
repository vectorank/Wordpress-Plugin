<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Include shared header
include_once VECTORRANK_PLUGIN_PATH . 'includes/header.php';
?>

    <div class="vectorrank-main analytics-page">
        <!-- Analytics Header -->
        <div class="analytics-header">
            <h2><?php _e('AI Analytics Dashboard', 'vectorrank'); ?></h2>
            <p><?php _e('Comprehensive insights into your AI-powered features performance', 'vectorrank'); ?></p>
            
            <div class="analytics-filters">
                <select class="analytics-period">
                    <option value="7"><?php _e('Last 7 Days', 'vectorrank'); ?></option>
                    <option value="30" selected><?php _e('Last 30 Days', 'vectorrank'); ?></option>
                    <option value="90"><?php _e('Last 90 Days', 'vectorrank'); ?></option>
                </select>
                <button class="button button-primary refresh-analytics"><?php _e('Refresh Data', 'vectorrank'); ?></button>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="analytics-metrics">
            <div class="metric-card">
                <div class="metric-header">
                    <h3><?php _e('Total Searches', 'vectorrank'); ?></h3>
                    <span class="metric-icon">🔍</span>
                </div>
                <div class="metric-value">47,392</div>
                <div class="metric-change positive">
                    <span class="change-icon">↗</span>
                    <span class="change-text">+12.5% from last month</span>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-header">
                    <h3><?php _e('AI Accuracy', 'vectorrank'); ?></h3>
                    <span class="metric-icon">🎯</span>
                </div>
                <div class="metric-value">97.8%</div>
                <div class="metric-change positive">
                    <span class="change-icon">↗</span>
                    <span class="change-text">+2.1% improvement</span>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-header">
                    <h3><?php _e('Conversion Rate', 'vectorrank'); ?></h3>
                    <span class="metric-icon">💰</span>
                </div>
                <div class="metric-value">4.73%</div>
                <div class="metric-change positive">
                    <span class="change-icon">↗</span>
                    <span class="change-text">+0.8% increase</span>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-header">
                    <h3><?php _e('Revenue Impact', 'vectorrank'); ?></h3>
                    <span class="metric-icon">📈</span>
                </div>
                <div class="metric-value">$23,847</div>
                <div class="metric-change positive">
                    <span class="change-icon">↗</span>
                    <span class="change-text">+34.2% boost</span>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="analytics-charts">
            <div class="chart-container">
                <div class="chart-header">
                    <h3><?php _e('Search Performance Trends', 'vectorrank'); ?></h3>
                    <div class="chart-legend">
                        <span class="legend-item">
                            <span class="legend-color" style="background: var(--vectorrank-primary);"></span>
                            <?php _e('AI Searches', 'vectorrank'); ?>
                        </span>
                        <span class="legend-item">
                            <span class="legend-color" style="background: var(--vectorrank-secondary);"></span>
                            <?php _e('Successful Results', 'vectorrank'); ?>
                        </span>
                    </div>
                </div>
                <div class="chart-placeholder">
                    <div class="chart-bars">
                        <div class="bar-group">
                            <div class="bar primary" style="height: 60%"></div>
                            <div class="bar secondary" style="height: 55%"></div>
                        </div>
                        <div class="bar-group">
                            <div class="bar primary" style="height: 75%"></div>
                            <div class="bar secondary" style="height: 72%"></div>
                        </div>
                        <div class="bar-group">
                            <div class="bar primary" style="height: 45%"></div>
                            <div class="bar secondary" style="height: 43%"></div>
                        </div>
                        <div class="bar-group">
                            <div class="bar primary" style="height: 90%"></div>
                            <div class="bar secondary" style="height: 88%"></div>
                        </div>
                        <div class="bar-group">
                            <div class="bar primary" style="height: 85%"></div>
                            <div class="bar secondary" style="height: 83%"></div>
                        </div>
                        <div class="bar-group">
                            <div class="bar primary" style="height: 70%"></div>
                            <div class="bar secondary" style="height: 68%"></div>
                        </div>
                        <div class="bar-group">
                            <div class="bar primary" style="height: 95%"></div>
                            <div class="bar secondary" style="height: 93%"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="chart-container">
                <div class="chart-header">
                    <h3><?php _e('Feature Usage Distribution', 'vectorrank'); ?></h3>
                </div>
                <div class="feature-usage">
                    <div class="usage-item">
                        <span class="usage-label"><?php _e('AI Search', 'vectorrank'); ?></span>
                        <div class="usage-bar">
                            <div class="usage-fill" style="width: 85%"></div>
                        </div>
                        <span class="usage-percent">85%</span>
                    </div>
                    <div class="usage-item">
                        <span class="usage-label"><?php _e('Autocomplete', 'vectorrank'); ?></span>
                        <div class="usage-bar">
                            <div class="usage-fill" style="width: 92%"></div>
                        </div>
                        <span class="usage-percent">92%</span>
                    </div>
                    <div class="usage-item">
                        <span class="usage-label"><?php _e('Cross-sell', 'vectorrank'); ?></span>
                        <div class="usage-bar">
                            <div class="usage-fill" style="width: 67%"></div>
                        </div>
                        <span class="usage-percent">67%</span>
                    </div>
                    <div class="usage-item">
                        <span class="usage-label"><?php _e('Recommendations', 'vectorrank'); ?></span>
                        <div class="usage-bar">
                            <div class="usage-fill" style="width: 74%"></div>
                        </div>
                        <span class="usage-percent">74%</span>
                    </div>
                    <div class="usage-item">
                        <span class="usage-label"><?php _e('Popup Suggestions', 'vectorrank'); ?></span>
                        <div class="usage-bar">
                            <div class="usage-fill" style="width: 58%"></div>
                        </div>
                        <span class="usage-percent">58%</span>
                    </div>
                    <div class="usage-item">
                        <span class="usage-label"><?php _e('Email Marketing', 'vectorrank'); ?></span>
                        <div class="usage-bar">
                            <div class="usage-fill" style="width: 43%"></div>
                        </div>
                        <span class="usage-percent">43%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="analytics-reports">
            <div class="report-section">
                <h3><?php _e('Top Search Queries', 'vectorrank'); ?></h3>
                <div class="report-table">
                    <table>
                        <thead>
                            <tr>
                                <th><?php _e('Query', 'vectorrank'); ?></th>
                                <th><?php _e('Searches', 'vectorrank'); ?></th>
                                <th><?php _e('Success Rate', 'vectorrank'); ?></th>
                                <th><?php _e('Conversions', 'vectorrank'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>wireless headphones</td>
                                <td>2,847</td>
                                <td>96.3%</td>
                                <td>187</td>
                            </tr>
                            <tr>
                                <td>laptop accessories</td>
                                <td>1,923</td>
                                <td>94.7%</td>
                                <td>134</td>
                            </tr>
                            <tr>
                                <td>smartphone cases</td>
                                <td>1,567</td>
                                <td>98.1%</td>
                                <td>109</td>
                            </tr>
                            <tr>
                                <td>gaming mouse</td>
                                <td>1,234</td>
                                <td>95.8%</td>
                                <td>87</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="report-section">
                <h3><?php _e('AI Performance Insights', 'vectorrank'); ?></h3>
                <div class="insight-cards">
                    <div class="insight-card">
                        <div class="insight-icon">🎯</div>
                        <h4><?php _e('Accuracy Improved', 'vectorrank'); ?></h4>
                        <p><?php _e('AI search accuracy has increased by 15% over the past month through continuous learning.', 'vectorrank'); ?></p>
                    </div>
                    <div class="insight-card">
                        <div class="insight-icon">⚡</div>
                        <h4><?php _e('Response Time Optimized', 'vectorrank'); ?></h4>
                        <p><?php _e('Average response time reduced to 0.3 seconds, improving user experience significantly.', 'vectorrank'); ?></p>
                    </div>
                    <div class="insight-card">
                        <div class="insight-icon">📈</div>
                        <h4><?php _e('Revenue Growth', 'vectorrank'); ?></h4>
                        <p><?php _e('AI recommendations have contributed to a 34% increase in average order value.', 'vectorrank'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div id="vectorrank-messages" class="vectorrank-messages"></div>
</div>