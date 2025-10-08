<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// This file is deprecated - functionality has been moved to settings.php
// Redirect to settings page to maintain backward compatibility
if (isset($_GET['page']) && $_GET['page'] === 'vectorrank-settings') {
    // Include the settings page instead
    include_once VECTORRANK_PLUGIN_PATH . 'includes/settings.php';
    return;
}
