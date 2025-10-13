# VectorRank Recommendations Widget

The VectorRank plugin now includes a powerful AI-powered recommendations widget that displays related posts based on the current post content.

## Features

- **AI-Powered Recommendations**: Uses VectorRank's AI engine to find truly related content
- **Fallback System**: Falls back to WordPress category-based recommendations if AI is unavailable
- **Customizable Display**: Control thumbnails, excerpts, dates, and number of recommendations
- **Multiple Integration Methods**: Widget, shortcode, and template function
- **Responsive Design**: Mobile-friendly layout
- **Performance Optimized**: Caches results and handles API timeouts gracefully

## Installation & Setup

1. The widget is automatically available after activating the VectorRank plugin
2. Make sure you're logged in to VectorRank and have synced your posts
3. The "Personalized Recommendations" feature must be enabled in VectorRank settings

## Usage Methods

### 1. WordPress Widget

1. Go to **Appearance > Widgets** in WordPress admin
2. Find "VectorRank Recommendations" widget
3. Drag it to your desired sidebar or widget area
4. Configure the settings:
   - **Title**: Widget title (default: "Related Posts")
   - **Number of recommendations**: 1-20 posts (default: 5)
   - **Show thumbnails**: Display post featured images
   - **Show excerpts**: Display post excerpts
   - **Show dates**: Display publication dates

### 2. Shortcode

Use the `[vectorrank_recommendations]` shortcode in posts, pages, or widgets:

```php
// Basic usage
[vectorrank_recommendations]

// With custom parameters
[vectorrank_recommendations count="3" show_thumbnail="true" show_excerpt="false" title="You Might Also Like"]

// All available parameters
[vectorrank_recommendations 
    count="5" 
    show_thumbnail="true" 
    show_excerpt="true" 
    show_date="false" 
    title="Related Articles" 
    class="my-custom-class"
    force="false"]
```

#### Shortcode Parameters

- `count` (number): Number of recommendations to show (1-20, default: 5)
- `show_thumbnail` (true/false): Show post thumbnails (default: true)
- `show_excerpt` (true/false): Show post excerpts (default: true)  
- `show_date` (true/false): Show publication dates (default: false)
- `title` (text): Section title (default: none)
- `class` (text): Additional CSS classes
- `force` (true/false): Show on non-post pages (default: false)

### 3. Template Function

For theme developers, use the template function in your theme files:

```php
<?php
// Basic usage
vectorrank_display_recommendations();

// With custom parameters
vectorrank_display_recommendations(array(
    'count' => 3,
    'show_thumbnail' => true,
    'show_excerpt' => false,
    'show_date' => true,
    'title' => 'You Might Also Like',
    'echo' => true,
    'before' => '<div class="my-recommendations">',
    'after' => '</div>'
));

// Return output instead of echoing
$recommendations_html = vectorrank_display_recommendations(array(
    'echo' => false
));
?>
```

#### Template Function Parameters

- `count` (number): Number of recommendations
- `show_thumbnail` (boolean): Show thumbnails
- `show_excerpt` (boolean): Show excerpts
- `show_date` (boolean): Show dates
- `title` (string): Section title
- `echo` (boolean): Echo output or return it (default: true)
- `before` (string): HTML before recommendations
- `after` (string): HTML after recommendations

## Styling & Customization

The widget comes with default styles that work with most themes. You can customize the appearance using CSS:

```css
/* Main container */
.vectorrank-recommendations {
    background: #fff;
    border-radius: 8px;
}

/* Individual recommendation item */
.recommendation-item {
    display: flex;
    gap: 12px;
    padding: 15px;
}

/* Recommendation thumbnail */
.recommendation-thumbnail img {
    width: 60px;
    height: 60px;
    border-radius: 6px;
}

/* Recommendation title */
.recommendation-title a {
    color: #333;
    font-weight: 600;
}

/* Compact layout */
.vectorrank-recommendations.compact .recommendation-item {
    padding: 10px;
}
```

## How It Works

1. **AI Analysis**: When displayed, the widget sends the current post content to VectorRank's AI engine
2. **Similarity Matching**: The AI finds the most similar content from your synced posts
3. **WordPress Integration**: Results are converted to proper WordPress post objects
4. **Fallback**: If AI is unavailable, it uses WordPress categories for recommendations
5. **Caching**: Results can be cached for performance (implement with transients if needed)

## Troubleshooting

### No Recommendations Showing

1. **Check VectorRank Login**: Ensure you're logged in to VectorRank
2. **Sync Posts**: Make sure your posts are synced (go to VectorRank Settings > Sync Posts)
3. **Enable Feature**: Check that "Personalized Recommendations" is enabled in settings
4. **Check Logs**: Look in WordPress debug logs for VectorRank error messages

### Widget Not Appearing

1. **Single Posts Only**: By default, the widget only shows on single post pages
2. **Use Force Parameter**: Use `force="true"` in shortcode to show elsewhere
3. **Check Theme**: Some themes may not support certain widget areas

### Styling Issues

1. **Theme Conflicts**: Your theme's CSS might override widget styles
2. **Add Custom CSS**: Use WordPress Customizer or theme CSS to adjust styling
3. **Use CSS Classes**: Add custom classes via shortcode parameters

## API Details

The recommendations use the VectorRank Search API endpoint:
- **Endpoint**: `/v1/search/get`
- **Method**: POST
- **Collection**: Uses same collection as synced posts
- **Parameters**: 
  - `query`: Current post content (stripped of HTML)
  - `topN`: Number of recommendations requested
  - `excludeIds`: Excludes current post ID
  - `searchType`: Set to 0

## Performance Notes

- API calls have a 15-second timeout for recommendations
- Duplicate results are automatically filtered
- Falls back gracefully if API is unavailable
- Consider implementing caching for high-traffic sites
- Widget loads CSS only when needed

## Support

For issues with the recommendations widget:
1. Check VectorRank plugin settings and logs
2. Ensure VectorRank service is running and accessible
3. Verify posts are properly synced
4. Contact VectorRank support for API-related issues