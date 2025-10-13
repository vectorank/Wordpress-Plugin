<?php
/**
 * VectorRank Post Recommendations Widget
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class VectorRank_Recommendations_Widget extends WP_Widget {

    /**
     * Constructor
     */
    public function __construct() {
        $widget_ops = array(
            'classname' => 'vectorrank_recommendations_widget',
            'description' => __('Display AI-powered post recommendations based on current post content', 'vectorrank'),
        );
        parent::__construct('vectorrank_recommendations', __('VectorRank Recommendations', 'vectorrank'), $widget_ops);
    }

    /**
     * Widget output
     */
    public function widget($args, $instance) {
        // Only show on single post pages
        if (!is_single()) {
            return;
        }

        // Check if recommendations are enabled
        $settings = get_option('vectorrank_settings', array());
        if (!isset($settings['features']['personalized_recommendations']) || !$settings['features']['personalized_recommendations']) {
            return;
        }

        global $post;
        $current_post_id = $post->ID;

        echo $args['before_widget'];

        $title = !empty($instance['title']) ? $instance['title'] : __('Related Posts', 'vectorrank');
        if (!empty($title)) {
            echo $args['before_title'] . apply_filters('widget_title', $title) . $args['after_title'];
        }

        // Get AI recommendations
        $recommendations = $this->get_ai_recommendations($current_post_id, $instance);

        if (!empty($recommendations)) {
            // Determine widget classes and styles
            $widget_class = 'vectorrank-recommendations';
            $custom_styles = '';
            
            if (isset($instance['use_theme_style']) && $instance['use_theme_style']) {
                $widget_class .= ' widget_recent_entries theme-style';
            } else {
                $widget_class .= ' custom-style';
                
                // Apply custom colors
                $widget_id = 'vectorrank-widget-' . $this->id;
                $custom_styles = '<style>
                    #' . $widget_id . ' .vectorrank-recommendations {
                        background-color: ' . esc_attr($instance['custom_bg_color'] ?? '#ffffff') . ';
                        border: 1px solid ' . esc_attr($instance['custom_border_color'] ?? '#e1e5e9') . ';
                        color: ' . esc_attr($instance['custom_text_color'] ?? '#333333') . ';
                    }
                    #' . $widget_id . ' .recommendation-title a {
                        color: ' . esc_attr($instance['custom_link_color'] ?? '#6366f1') . ';
                    }
                    #' . $widget_id . ' .recommendation-item {
                        border-color: ' . esc_attr($instance['custom_border_color'] ?? '#e1e5e9') . ';
                    }
                </style>';
            }
            
            echo $custom_styles;
            echo '<div id="vectorrank-widget-' . $this->id . '" class="' . $widget_class . '">';
            
            // Use appropriate container based on style choice
            if (isset($instance['use_theme_style']) && $instance['use_theme_style']) {
                echo '<ul class="vectorrank-recommendations-list">';
            } else {
                echo '<div class="vectorrank-recommendations-list">';
            }

            foreach ($recommendations as $recommendation) {
                $this->display_recommendation_item($recommendation, $instance);
            }

            if (isset($instance['use_theme_style']) && $instance['use_theme_style']) {
                echo '</ul>';
            } else {
                echo '</div>';
            }
            
            echo '<div class="vectorrank-powered-by">';
            // echo '<small>' . __('Powered by VectorRank AI', 'vectorrank') . '</small>';
            echo '</div>';
            echo '</div>';
        } else {
            echo '<p class="no-recommendations">' . __('No recommendations available at the moment.', 'vectorrank') . '</p>';
        }

        echo $args['after_widget'];
    }

    /**
     * Widget form in admin
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : __('Related Posts', 'vectorrank');
        $count = !empty($instance['count']) ? $instance['count'] : 5;
        $show_thumbnail = isset($instance['show_thumbnail']) ? $instance['show_thumbnail'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? $instance['show_excerpt'] : true;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;
        $use_theme_style = isset($instance['use_theme_style']) ? $instance['use_theme_style'] : vectorrank_should_use_theme_style();
        $custom_bg_color = !empty($instance['custom_bg_color']) ? $instance['custom_bg_color'] : '#ffffff';
        $custom_border_color = !empty($instance['custom_border_color']) ? $instance['custom_border_color'] : '#e1e5e9';
        $custom_text_color = !empty($instance['custom_text_color']) ? $instance['custom_text_color'] : '#333333';
        $custom_link_color = !empty($instance['custom_link_color']) ? $instance['custom_link_color'] : '#6366f1';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'vectorrank'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of recommendations:', 'vectorrank'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" min="1" max="20" value="<?php echo esc_attr($count); ?>">
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_thumbnail); ?> id="<?php echo $this->get_field_id('show_thumbnail'); ?>" name="<?php echo $this->get_field_name('show_thumbnail'); ?>" />
            <label for="<?php echo $this->get_field_id('show_thumbnail'); ?>"><?php _e('Show thumbnails', 'vectorrank'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_excerpt); ?> id="<?php echo $this->get_field_id('show_excerpt'); ?>" name="<?php echo $this->get_field_name('show_excerpt'); ?>" />
            <label for="<?php echo $this->get_field_id('show_excerpt'); ?>"><?php _e('Show excerpts', 'vectorrank'); ?></label>
        </p>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($show_date); ?> id="<?php echo $this->get_field_id('show_date'); ?>" name="<?php echo $this->get_field_name('show_date'); ?>" />
            <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show dates', 'vectorrank'); ?></label>
        </p>
        
        <hr style="margin: 15px 0;">
        <h4><?php _e('Design Options', 'vectorrank'); ?></h4>
        
        <p>
            <input class="checkbox" type="checkbox" <?php checked($use_theme_style); ?> id="<?php echo $this->get_field_id('use_theme_style'); ?>" name="<?php echo $this->get_field_name('use_theme_style'); ?>" onchange="toggleCustomColors(this)" />
            <label for="<?php echo $this->get_field_id('use_theme_style'); ?>"><?php _e('Use theme\'s default post widget style', 'vectorrank'); ?></label>
        </p>
        
        <div id="<?php echo $this->get_field_id('custom_colors'); ?>" style="<?php echo $use_theme_style ? 'display:none;' : ''; ?>">
            <p>
                <label for="<?php echo $this->get_field_id('custom_bg_color'); ?>"><?php _e('Background Color:', 'vectorrank'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('custom_bg_color'); ?>" name="<?php echo $this->get_field_name('custom_bg_color'); ?>" type="color" value="<?php echo esc_attr($custom_bg_color); ?>">
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('custom_border_color'); ?>"><?php _e('Border Color:', 'vectorrank'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('custom_border_color'); ?>" name="<?php echo $this->get_field_name('custom_border_color'); ?>" type="color" value="<?php echo esc_attr($custom_border_color); ?>">
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('custom_text_color'); ?>"><?php _e('Text Color:', 'vectorrank'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('custom_text_color'); ?>" name="<?php echo $this->get_field_name('custom_text_color'); ?>" type="color" value="<?php echo esc_attr($custom_text_color); ?>">
            </p>
            
            <p>
                <label for="<?php echo $this->get_field_id('custom_link_color'); ?>"><?php _e('Link Color:', 'vectorrank'); ?></label>
                <input class="widefat" id="<?php echo $this->get_field_id('custom_link_color'); ?>" name="<?php echo $this->get_field_name('custom_link_color'); ?>" type="color" value="<?php echo esc_attr($custom_link_color); ?>">
            </p>
        </div>
        
        <script>
        function toggleCustomColors(checkbox) {
            var customColorsDiv = document.getElementById('<?php echo $this->get_field_id('custom_colors'); ?>');
            if (checkbox.checked) {
                customColorsDiv.style.display = 'none';
            } else {
                customColorsDiv.style.display = 'block';
            }
        }
        </script>
        <?php
    }

    /**
     * Update widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? absint($new_instance['count']) : 5;
        $instance['show_thumbnail'] = isset($new_instance['show_thumbnail']) ? (bool) $new_instance['show_thumbnail'] : false;
        $instance['show_excerpt'] = isset($new_instance['show_excerpt']) ? (bool) $new_instance['show_excerpt'] : false;
        $instance['show_date'] = isset($new_instance['show_date']) ? (bool) $new_instance['show_date'] : false;
        $instance['use_theme_style'] = isset($new_instance['use_theme_style']) ? (bool) $new_instance['use_theme_style'] : false;
        $instance['custom_bg_color'] = (!empty($new_instance['custom_bg_color'])) ? sanitize_hex_color($new_instance['custom_bg_color']) : '#ffffff';
        $instance['custom_border_color'] = (!empty($new_instance['custom_border_color'])) ? sanitize_hex_color($new_instance['custom_border_color']) : '#e1e5e9';
        $instance['custom_text_color'] = (!empty($new_instance['custom_text_color'])) ? sanitize_hex_color($new_instance['custom_text_color']) : '#333333';
        $instance['custom_link_color'] = (!empty($new_instance['custom_link_color'])) ? sanitize_hex_color($new_instance['custom_link_color']) : '#6366f1';
        
        return $instance;
    }

    /**
     * Get AI-powered recommendations for the current post
     */
    private function get_ai_recommendations($post_id, $instance) {
        $settings = get_option('vectorrank_settings', array());
        
        // Check if user is logged in and has valid tokens
        if (!isset($settings['logged_in']) || !$settings['logged_in'] || empty($settings['apps'])) {
            return $this->get_fallback_recommendations($post_id, $instance);
        }

        // Find recommendation app
        $recommendation_app = null;
        foreach ($settings['apps'] as $app) {
            if ($app['name'] === 'recommendation-app') {
                $recommendation_app = $app;
                break;
            }
        }

        if (!$recommendation_app) {
            return $this->get_fallback_recommendations($post_id, $instance);
        }
        // Get current post content for AI analysis
        $current_post = get_post($post_id);
        if (!$current_post) {
            return array();
        }

        // Prepare recommendation request
        $query_text = $current_post->post_title . ' ' . strip_tags($current_post->post_content);
        $recommendation_data = array(
            'collection' => 'rec',
            'query' => $query_text,
            'properties' => null,
            'topN' => intval($instance['count']) ?: 5,
            'offset' => 0,
            'searchType' => 0,
            'excludeIds' => array((string)$post_id) // Exclude current post
        );

        // Get API URLs
        $api_urls = vectorrank_get_api_urls('search', '/v1/search/get');

        // Allow localhost connections for development
        add_filter('http_request_host_is_external', '__return_true');
        add_filter('block_local_requests', '__return_false');

        $response = false;

        foreach ($api_urls as $api_url) {
            error_log('[VectorRank] Trying recommendations API: ' . $api_url);
        
            $args = array(
                'method' => 'POST',
                'timeout' => 15,
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'User-Agent' => 'VectorRank-WordPress-Plugin/1.0.0',
                    'Authorization' => 'Bearer ' . $recommendation_app['search_token'],
                    'Referer' => get_permalink($post_id),
                ),
                'body' => wp_json_encode($recommendation_data),
                'sslverify' => false,
                'reject_unsafe_urls' => false,
                'blocking' => true,
            );

            $response = wp_remote_request($api_url, $args);
            if (!is_wp_error($response)) {
                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code === 200) {
                    error_log('[VectorRank] Recommendations API successful from: ' . $api_url);
                    break;
                }
            }
        }

        // Remove filters
        remove_filter('http_request_host_is_external', '__return_true');
        remove_filter('block_local_requests', '__return_false');

        if (is_wp_error($response)) {
            error_log('[VectorRank] Recommendations API error: ' . $response->get_error_message());
            return $this->get_fallback_recommendations($post_id, $instance);
        }

        // Parse response
        $response_body = wp_remote_retrieve_body($response);
        $recommendations_data = json_decode($response_body, true);
       
        if (json_last_error() !== JSON_ERROR_NONE || !is_array($recommendations_data)) {
            error_log('[VectorRank] Invalid JSON in recommendations response: ' . $response_body);
            return $this->get_fallback_recommendations($post_id, $instance);
        }

        // Convert AI results to WordPress posts
        $recommendations = array();
        foreach ($recommendations_data as $recommendation) {
            $id = isset($recommendation['Id']) ? $recommendation['Id'] : (isset($recommendation['id']) ? $recommendation['id'] : null);
            
            if (empty($id)) {
                continue;
            }

            // Extract post ID from result ID (handle paragraph format like "123_1")
            $post_id_from_result = $id;
            if (strpos($id, '_') !== false) {
                $id_parts = explode('_', $id);
                $post_id_from_result = intval($id_parts[0]);
            } else {
                $post_id_from_result = intval($id);
            }

            // Skip if it's the current post
            if ($post_id_from_result === $post_id) {
                continue;
            }

            // Get the WordPress post
            $recommended_post = get_post($post_id_from_result);
            if ($recommended_post && $recommended_post->post_status === 'publish') {
                $recommendations[] = $recommended_post;
            }
        }

        // Remove duplicates and limit results
        $unique_recommendations = array();
        $seen_ids = array();
        
        foreach ($recommendations as $rec_post) {
            if (!in_array($rec_post->ID, $seen_ids)) {
                $unique_recommendations[] = $rec_post;
                $seen_ids[] = $rec_post->ID;
                
                if (count($unique_recommendations) >= ($instance['count'] ?: 5)) {
                    break;
                }
            }
        }

        return $unique_recommendations;
    }

    /**
     * Fallback to WordPress-based recommendations if AI is not available
     */
    private function get_fallback_recommendations($post_id, $instance) {
        $count = intval($instance['count']) ?: 5;
        
        // Get posts from the same categories
        $categories = get_the_category($post_id);
        $category_ids = array();
        
        foreach ($categories as $category) {
            $category_ids[] = $category->term_id;
        }

        $args = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => $count * 2, // Get more to filter out current post
            'post__not_in' => array($post_id),
            'orderby' => 'rand',
        );

        if (!empty($category_ids)) {
            $args['category__in'] = $category_ids;
        }

        $recommendations = get_posts($args);
        
        // Limit to requested count
        return array_slice($recommendations, 0, $count);
    }

    /**
     * Display a single recommendation item
     */
    private function display_recommendation_item($post, $instance) {
        $show_thumbnail = isset($instance['show_thumbnail']) ? $instance['show_thumbnail'] : true;
        $show_excerpt = isset($instance['show_excerpt']) ? $instance['show_excerpt'] : true;
        $show_date = isset($instance['show_date']) ? $instance['show_date'] : false;
        $use_theme_style = isset($instance['use_theme_style']) && $instance['use_theme_style'];
        
        $item_class = $use_theme_style ? 'recommendation-item theme-item' : 'recommendation-item';
        echo '<div class="' . $item_class . '">';
        
        if ($use_theme_style) {
            // Use theme's recent posts widget structure
            echo '<li>';
        }
        
        if ($show_thumbnail && has_post_thumbnail($post->ID)) {
            echo '<div class="recommendation-thumbnail">';
            echo '<a href="' . get_permalink($post->ID) . '">';
            echo get_the_post_thumbnail($post->ID, 'thumbnail', array('alt' => get_the_title($post->ID)));
            echo '</a>';
            echo '</div>';
        }
        
        echo '<div class="recommendation-content">';
        
        echo '<h4 class="recommendation-title">';
        echo '<a href="' . get_permalink($post->ID) . '">' . get_the_title($post->ID) . '</a>';
        echo '</h4>';
        
        if ($show_date) {
            echo '<div class="recommendation-date">';
            echo get_the_date('', $post->ID);
            echo '</div>';
        }
        
        if ($show_excerpt) {
            $excerpt = get_the_excerpt($post->ID);
            if (empty($excerpt)) {
                $excerpt = wp_trim_words(strip_tags($post->post_content), 15);
            }
            echo '<div class="recommendation-excerpt">';
            echo esc_html($excerpt);
            echo '</div>';
        }
        
        echo '</div>'; // recommendation-content
        
        if ($use_theme_style) {
            echo '</li>';
        }
        echo '</div>'; // recommendation-item
    }
}