# VectorRank Widget Design Customization Guide

The VectorRank Recommendations Widget offers extensive design customization options to match your site's look and feel perfectly.

## 🎨 Design Options

### 1. Theme Integration (Recommended)

**Enable "Use theme's default post widget style"** to automatically inherit your theme's styling for recent posts widgets.

**Benefits:**
- ✅ Seamless integration with your theme
- ✅ Automatically matches your site's typography and colors
- ✅ Responsive design inherited from theme
- ✅ No additional customization needed

**How it works:**
- Detects your active theme
- Applies the same CSS classes used by WordPress Recent Posts widget
- Loads theme-specific compatibility styles when available

**Supported Themes:**
- Twenty Twenty-One, Twenty Twenty-Two, Twenty Twenty-Three
- Astra
- GeneratePress  
- OceanWP
- Kadence
- Neve
- Most other well-coded themes

### 2. Custom Colors

**Disable theme style** to access custom color options:

#### Available Color Settings:
- **Background Color**: Widget background color
- **Border Color**: Border and divider lines color
- **Text Color**: Main text color for excerpts and dates
- **Link Color**: Title links and hover states color

#### Color Picker Features:
- HTML5 color picker for easy selection
- Hex color code support
- Real-time preview in widget areas
- Saved settings persist across theme changes

## 🛠️ Implementation Details

### CSS Class Structure

**Theme Style Mode:**
```html
<div class="vectorrank-recommendations theme-style widget_recent_entries">
    <ul class="vectorrank-recommendations-list">
        <li class="recommendation-item theme-item">
            <!-- Content -->
        </li>
    </ul>
</div>
```

**Custom Style Mode:**
```html
<div class="vectorrank-recommendations custom-style" style="...custom colors...">
    <div class="vectorrank-recommendations-list">
        <div class="recommendation-item">
            <!-- Content -->
        </div>
    </div>
</div>
```

### Dynamic CSS Generation

When using custom colors, the widget generates inline CSS:

```css
#vectorrank-widget-123 .vectorrank-recommendations {
    background-color: #ffffff;
    border: 1px solid #e1e5e9;
    color: #333333;
}
#vectorrank-widget-123 .recommendation-title a {
    color: #6366f1;
}
```

## 🎯 Advanced Customization

### Custom CSS Targeting

Use these CSS selectors for additional customization:

```css
/* Target all VectorRank widgets */
.vectorrank-recommendations { }

/* Target only custom-styled widgets */
.vectorrank-recommendations.custom-style { }

/* Target only theme-styled widgets */
.vectorrank-recommendations.theme-style { }

/* Target specific elements */
.vectorrank-recommendations .recommendation-item { }
.vectorrank-recommendations .recommendation-title { }
.vectorrank-recommendations .recommendation-thumbnail { }
.vectorrank-recommendations .recommendation-excerpt { }
.vectorrank-recommendations .recommendation-date { }
```

### Theme Compatibility Files

For theme developers or advanced users, create theme-specific CSS:

**File Location:** `/assets/css/theme-compatibility/[theme-name].css`

**Example for a custom theme:**
```css
/* mytheme.css */
.vectorrank-recommendations.theme-style {
    font-family: var(--theme-font-family);
    background: var(--theme-widget-background);
}

.vectorrank-recommendations.theme-style .recommendation-title a {
    color: var(--theme-primary-color);
    font-weight: var(--theme-heading-weight);
}
```

## 📱 Responsive Design

### Built-in Responsive Features:
- Mobile-optimized layouts
- Touch-friendly link areas
- Responsive image sizing
- Flexible typography scaling

### Custom Responsive CSS:
```css
@media (max-width: 768px) {
    .vectorrank-recommendations .recommendation-item {
        flex-direction: column;
        text-align: center;
    }
    
    .vectorrank-recommendations .recommendation-thumbnail img {
        width: 80px;
        height: 80px;
        margin: 0 auto;
    }
}
```

## 🔧 Developer API

### Programmatic Color Setting

```php
// Set widget colors programmatically
$widget_options = array(
    'title' => 'Related Articles',
    'count' => 5,
    'use_theme_style' => false,
    'custom_bg_color' => '#f8f9fa',
    'custom_border_color' => '#dee2e6',
    'custom_text_color' => '#495057',
    'custom_link_color' => '#007bff'
);

// Apply to shortcode
echo do_shortcode('[vectorrank_recommendations ' . http_build_query($widget_options, '', ' ') . ']');
```

### Theme Integration Hooks

```php
// Customize theme detection
add_filter('vectorrank_should_use_theme_style', function($should_use) {
    return true; // Always use theme style
});

// Add custom theme classes
add_filter('vectorrank_theme_widget_classes', function($classes) {
    $classes[] = 'my-custom-widget-class';
    return $classes;
});
```

## 🎨 Design Best Practices

### Color Selection Tips:
1. **Contrast**: Ensure sufficient contrast for accessibility
2. **Brand Alignment**: Use colors from your brand palette
3. **Theme Harmony**: Choose colors that complement your theme
4. **Readability**: Prioritize text readability over visual flair

### When to Use Each Mode:
- **Theme Style**: For seamless integration and minimal maintenance
- **Custom Colors**: For branded experiences and specific design requirements

### Testing Checklist:
- ✅ Test on mobile devices
- ✅ Check in different widget areas (sidebar, footer, etc.)
- ✅ Verify color contrast accessibility
- ✅ Test with different post thumbnail sizes
- ✅ Validate HTML markup

## 🔍 Troubleshooting

### Common Issues:

**Colors not applying:**
- Check if "Use theme style" is disabled
- Verify color codes are valid hex values
- Clear any caching plugins

**Theme style not working:**
- Ensure your theme supports widgets properly
- Check if theme has recent posts widget styling
- Try switching to custom colors temporarily

**Layout issues:**
- Check for theme CSS conflicts
- Add custom CSS to override theme styles
- Consider using theme style mode instead

### Debug Tools:

1. **Browser Developer Tools**: Inspect CSS classes and styles
2. **Widget Preview**: Test in Customizer before publishing
3. **Shortcode Testing**: Use shortcode to test different settings
4. **Theme Switcher**: Test with different themes to isolate issues

## 📚 Examples

### Corporate Website:
```php
// Professional blue theme
'custom_bg_color' => '#ffffff',
'custom_border_color' => '#e3f2fd',
'custom_text_color' => '#37474f',
'custom_link_color' => '#1976d2'
```

### Creative Portfolio:
```php
// Vibrant creative theme
'custom_bg_color' => '#fafafa',
'custom_border_color' => '#ff5722',
'custom_text_color' => '#212121',
'custom_link_color' => '#ff5722'
```

### Minimal Blog:
```php
// Clean minimal theme
'custom_bg_color' => '#ffffff',
'custom_border_color' => '#eeeeee',
'custom_text_color' => '#666666',
'custom_link_color' => '#333333'
```

This comprehensive design system ensures your VectorRank recommendations widget looks perfect on any website!