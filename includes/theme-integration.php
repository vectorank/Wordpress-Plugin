<?php
/**
 * VectorRank Theme Integration Helper
 * 
 * This file contains functions to integrate with theme styles
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Get theme's recent posts widget styling classes
 */
function vectorrank_get_theme_post_widget_classes() { 
    $theme = wp_get_theme();
    $theme_name = $theme->get('Name');
    
    // Common WordPress widget classes that most themes style
    $base_classes = array(
        'widget',
        'widget_recent_entries'
    );
    
    // Theme-specific classes (add more as needed)
    $theme_specific = array();
    
    switch (strtolower($theme_name)) {
        case 'twenty twenty-one':
        case 'twentytwentyone':
            $theme_specific[] = 'wp-block-latest-posts';
            break;
        case 'twenty twenty-two':
        case 'twentytwentytwo':
            $theme_specific[] = 'wp-block-latest-posts';
            $theme_specific[] = 'is-grid';
            break;
        case 'astra':
            $theme_specific[] = 'astra-widget';
            break;
        case 'generatepress':
            $theme_specific[] = 'inside-right-sidebar';
            break;
        case 'oceanwp':
            $theme_specific[] = 'sidebar-box';
            break;
        default:
            // Try to detect common theme patterns
            if (strpos(strtolower($theme_name), 'twenty') !== false) {
                $theme_specific[] = 'wp-block-latest-posts';
            }
            break;
    }
    
    return array_merge($base_classes, $theme_specific);
}

/**
 * Get theme's post item structure for recent posts
 */
function vectorrank_get_theme_post_item_structure() {
    $theme = wp_get_theme();
    $theme_name = $theme->get('Name');
    
    $structure = array(
        'container' => 'li',
        'title_tag' => 'a',
        'date_format' => get_option('date_format'),
        'excerpt_length' => 15
    );
    
    // Theme-specific adjustments
    switch (strtolower($theme_name)) {
        case 'twenty twenty-one':
        case 'twentytwentyone':
            $structure['container'] = 'article';
            $structure['title_tag'] = 'h3';
            break;
        case 'astra':
            $structure['excerpt_length'] = 20;
            break;
    }
    
    return $structure;
}

/**
 * Enqueue theme compatibility styles
 */
function vectorrank_enqueue_theme_compatibility_styles() {
    $theme = wp_get_theme();
    $theme_name = strtolower($theme->get('Name'));
    
    // Check if we have specific compatibility CSS for this theme
    $compatibility_file = VECTORRANK_PLUGIN_PATH . 'assets/css/theme-compatibility/' . sanitize_file_name($theme_name) . '.css';
    
    if (file_exists($compatibility_file)) {
        wp_enqueue_style(
            'vectorrank-theme-compatibility', 
            VECTORRANK_PLUGIN_URL . 'assets/css/theme-compatibility/' . sanitize_file_name($theme_name) . '.css',
            array('vectorrank-recommendations-widget'),
            VECTORRANK_PLUGIN_VERSION
        );
    }
}

/**
 * Auto-detect if user should use theme style
 */
function vectorrank_should_use_theme_style() {
    $theme = wp_get_theme();
    
    // Check if theme has recent posts widget styles
    $theme_supports_widgets = current_theme_supports('widgets');
    $theme_has_sidebar = is_active_sidebar('sidebar-1') || is_active_sidebar('primary');
    
    // Popular themes that have good widget styling
    $well_styled_themes = array(
        'twenty twenty-one',
        'twenty twenty-two',
        'twenty twenty-three',
        'astra',
        'generatepress',
        'oceanwp',
        'kadence',
        'neve'
    );
    
    $theme_name = strtolower($theme->get('Name'));
    
    return $theme_supports_widgets && in_array($theme_name, $well_styled_themes);
}