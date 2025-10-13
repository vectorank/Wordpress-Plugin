# VectorRank Widgets

This directory contains all WordPress widgets for the VectorRank plugin.

## Structure

```
widgets/
├── widgets-loader.php                           # Autoloader and widget registration
├── class-vectorrank-recommendations-widget.php  # AI-powered recommendations widget
└── README.md                                   # This file
```

## Adding New Widgets

To add a new widget to VectorRank:

1. **Create Widget Class File**
   - Name: `class-vectorrank-[widget-name]-widget.php`
   - Location: `widgets/`
   - Extend: `WP_Widget`

2. **Widget Registration**
   - The widget will be automatically loaded by `widgets-loader.php`
   - Add registration in `vectorrank_register_widgets()` function in `widgets-loader.php`

## Example Widget Structure

```php
<?php
/**
 * VectorRank Example Widget
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VectorRank_Example_Widget extends WP_Widget {

    public function __construct() {
        $widget_ops = array(
            'classname' => 'vectorrank_example_widget',
            'description' => __('Description of your widget', 'vectorrank'),
        );
        parent::__construct('vectorrank_example', __('VectorRank Example', 'vectorrank'), $widget_ops);
    }

    public function widget($args, $instance) {
        // Widget output logic
    }

    public function form($instance) {
        // Admin form logic
    }

    public function update($new_instance, $old_instance) {
        // Save settings logic
    }
}
```

## Current Widgets

### VectorRank Recommendations Widget

**File**: `class-vectorrank-recommendations-widget.php`
**Class**: `VectorRank_Recommendations_Widget`
**ID**: `vectorrank_recommendations`

**Features**:
- AI-powered post recommendations
- Fallback to category-based recommendations
- Customizable display options (thumbnails, excerpts, dates)
- Responsive design
- Only displays on single post pages

**Usage**:
- Widget: Drag from Appearance > Widgets
- Shortcode: `[vectorrank_recommendations]`
- Template: `vectorrank_display_recommendations()`

## Widget Development Guidelines

1. **Naming Convention**
   - File: `class-vectorrank-[name]-widget.php`
   - Class: `VectorRank_[Name]_Widget`
   - Widget ID: `vectorrank_[name]`

2. **Security**
   - Always include ABSPATH check
   - Sanitize all inputs
   - Escape all outputs

3. **Internationalization**
   - Use `__()` for translatable strings
   - Text domain: `'vectorrank'`

4. **Performance**
   - Cache API calls when possible
   - Use appropriate timeouts
   - Implement fallback mechanisms

5. **Integration**
   - Check VectorRank settings/features
   - Use VectorRank API functions
   - Follow WordPress coding standards

## Styling

Widget styles should be placed in:
- `assets/css/[widget-name]-widget.css`
- Enqueue in `vectorrank_enqueue_frontend_styles()` function
- Use responsive design principles
- Follow VectorRank design system

## Testing

Before adding a new widget:
1. Test with different themes
2. Verify mobile responsiveness
3. Test with/without VectorRank API
4. Check accessibility standards
5. Validate HTML output