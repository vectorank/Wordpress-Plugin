<?php
/**
 * VectorRank Widgets Autoloader
 * 
 * This file automatically loads all widget classes from the widgets directory
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Autoload VectorRank widgets
 */
function vectorrank_load_widgets() {
    $widgets_dir = VECTORRANK_PLUGIN_PATH . 'widgets/';
    
    // Get all PHP files in the widgets directory
    $widget_files = glob($widgets_dir . 'class-*.php');
    
    if (!empty($widget_files)) {
        foreach ($widget_files as $widget_file) {
            require_once $widget_file;
        }
    }
}

// Load widgets
vectorrank_load_widgets();

/**
 * Register all VectorRank widgets
 */
function vectorrank_register_widgets() {
    // Register Recommendations Widget
    if (class_exists('VectorRank_Recommendations_Widget')) {
        register_widget('VectorRank_Recommendations_Widget');
    }
    
    // Future widgets can be registered here
    // if (class_exists('VectorRank_Another_Widget')) {
    //     register_widget('VectorRank_Another_Widget');
    // }
}
add_action('widgets_init', 'vectorrank_register_widgets');