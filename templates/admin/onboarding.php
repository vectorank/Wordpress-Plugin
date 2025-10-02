
<?php

$current_step = isset($_GET['step']) ? intval($_GET['step']) : 1;
$total_steps = 4;
function vectorank_onboarding_step_url($step) {
    return admin_url('admin.php?page=vectorank-onboarding&step=' . $step);
}
?>
<style>
.vectorank-onboarding {
    background: #111;
    color: #fff;
    border-radius: 18px;
    max-width: 600px;
    margin: 40px auto;
    box-shadow: 0 8px 32px 0 rgba(0,0,0,0.37);
    padding: 40px 40px 30px 40px;
    font-family: 'Segoe UI', 'Arial', sans-serif;
}
.vectorank-onboarding h1 {
    color: #fff;
    font-size: 2.2em;
    margin-bottom: 10px;
    text-align: center;
    letter-spacing: 1px;
}
.vectorank-onboarding-steps .steps {
    display: flex;
    justify-content: space-between;
    margin: 30px 0 40px 0;
    padding: 0;
    list-style: none;
}
.vectorank-onboarding-steps .steps li {
    flex: 1;
    text-align: center;
    padding: 10px 0;
    border-bottom: 4px solid #333;
    color: #bbb;
    font-weight: 500;
    font-size: 1.1em;
    background: transparent;
    transition: border-color 0.2s, color 0.2s;
}
.vectorank-onboarding-steps .steps li.active {
    border-bottom: 4px solid #fff;
    color: #111;
    background: #fff;
    border-radius: 8px 8px 0 0;
}
.vectorank-onboarding .step-content {
    background: #222;
    border-radius: 12px;
    padding: 32px 28px 24px 28px;
    margin-bottom: 24px;
    min-height: 180px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.12);
}
.vectorank-onboarding .step-content h2 {
    color: #fff;
    font-size: 1.4em;
    margin-bottom: 12px;
}
.vectorank-onboarding .step-content p {
    color: #eee;
    font-size: 1.08em;
    margin-bottom: 0;
}
.vectorank-onboarding .button,
.vectorank-onboarding .button-primary {
    display: inline-block;
    margin: 0 8px 0 0;
    padding: 10px 28px;
    border-radius: 6px;
    border: none;
    font-size: 1em;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.2s, color 0.2s;
}
.vectorank-onboarding .button {
    background: #fff;
    color: #111;
}
.vectorank-onboarding .button-primary {
    background: #111;
    color: #fff;
    border: 2px solid #fff;
}
.vectorank-onboarding .button-primary:hover {
    background: #fff;
    color: #111;
    border: 2px solid #111;
}
.vectorank-onboarding .button:hover {
    background: #eee;
    color: #111;
}
.vectorank-onboarding .step-navigation {
    text-align: right;
    margin-top: 10px;
}
@media (max-width: 700px) {
    .vectorank-onboarding {
        padding: 18px 6px 10px 6px;
    }
    .vectorank-onboarding .step-content {
        padding: 16px 8px 12px 8px;
    }
}
</style>
<div class="vectorank-onboarding">
    <h1><?php _e('Welcome to VectoRank!', 'vectorank'); ?></h1>
    <div class="vectorank-onboarding-steps">
        <ul class="steps">
            <?php for ($i = 1; $i <= $total_steps; $i++): ?>
                <li class="<?php echo $i == $current_step ? 'active' : ''; ?>">
                    <?php echo sprintf(__('Step %d', 'vectorank'), $i); ?>
                </li>
            <?php endfor; ?>
        </ul>
        <div class="step-content">
            <?php if ($current_step == 1): ?>
                <h2><?php _e('Connect Your SaaS Account', 'vectorank'); ?></h2>
                <p><?php _e('Start by connecting your VectoRank SaaS account. This enables secure, AI-powered recommendations and search for your store.', 'vectorank'); ?></p>
                <?php 
                $showed_result = false;
                if (isset($_GET['client-token']) && isset($_GET['status'])) {
                    echo 'asdadsadasd';
                    $status = sanitize_text_field($_GET['status']);
                    $error_message = isset($_GET['error-message']) ? sanitize_text_field($_GET['error-message']) : '';
                    if ($status === '0') {
                        echo '<div style="color:#ff4d4f; background:#222; border-radius:6px; padding:12px 16px; margin-bottom:16px;">' . esc_html($error_message ? $error_message : __('Login failed. Please try again.', 'vectorank')) . '</div>';
                        $showed_result = true;
                    } elseif ($status === '1' && isset($_GET['token']) && isset($_GET['client-token'])) {
                        update_option('vectorank_token', sanitize_text_field($_GET['token']));
                        update_option('vectorank_client_token', sanitize_text_field($_GET['client-token']));
                        // Optionally, save username/email too
                        if (isset($_GET['username'])) {
                            update_option('vectorank_username', sanitize_text_field($_GET['username']));
                        }
                        echo '<div style="color:#4caf50; background:#222; border-radius:6px; padding:12px 16px; margin-bottom:16px;">' . __('You are now logged in to VectoRank!', 'vectorank') . '</div>';
                        $showed_result = true;
                    }
                }
                // Generate a unique token for the redirect (nonce for security)
                if (!function_exists('vectorank_create_guid')) {
                    function vectorank_create_guid() {
                        if (function_exists('com_create_guid')) {
                            return trim(com_create_guid(), '{}');
                        } else {
                            // Generate GUID manually
                            return sprintf(
                                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                                mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                                mt_rand(0, 0xffff),
                                mt_rand(0, 0x0fff) | 0x4000,
                                mt_rand(0, 0x3fff) | 0x8000,
                                mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                            );
                        }
                    }
                }
                $vectorank_nonce = vectorank_create_guid();
                // Store the GUID for later validation
                update_option('vectorank_guid', $vectorank_nonce);
                $redirect_url = admin_url('admin.php?page=vectorank-onboarding&step=1&vectorank_callback=1');
                $saas_url = 'https://localhost:44381/api/v1/third-party-access';
                ?>
                <?php
                $vectorank_token = get_option('vectorank_token');
                $vectorank_email = get_option('vectorank_username');
                if ($vectorank_token && $vectorank_email) : ?>
                    <div style="background:#181f2a; color:#fff; border-radius:10px; padding:22px 18px; margin-bottom:18px; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <span style="font-size:1.1em; font-weight:600;">👤 <?php echo esc_html($vectorank_email); ?></span>
                        </div>
                        <div>
                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to sign out?');">
                                <input type="hidden" name="vectorank_logout" value="1" />
                                <button type="submit" class="button" style="background:#ff4d4f; color:#fff; border:none; margin-right:10px; padding:7px 18px; border-radius:5px; font-weight:600; cursor:pointer;">Sign Out</button>
                            </form>
                            <a href="<?php echo esc_url(vectorank_onboarding_step_url(2)); ?>" class="button button-primary" style="padding:7px 18px;">Continue to Step 2</a>
                        </div>
                    </div>
                <?php else: ?>
                    <form id="vectorank-saas-signup" style="display:inline; min-width:260px;">
                        <input type="hidden" name="token" value="<?php echo esc_attr($vectorank_nonce); ?>" />
                        <label for="vectorank-email" style="display:block; margin-bottom:4px; color:#fff;">Email</label>
                        <input type="email" id="vectorank-email" name="email" required style="width:100%;margin-bottom:10px;padding:8px;border-radius:4px;border:1px solid #333;background:#222;color:#fff;" />
                        <label for="vectorank-password" style="display:block; margin-bottom:4px; color:#fff;">Password</label>
                        <input type="password" id="vectorank-password" name="password" required style="width:100%;margin-bottom:14px;padding:8px;border-radius:4px;border:1px solid #333;background:#222;color:#fff;" />
                        <button type="submit" class="button button-primary"><?php _e('Login / Create Account', 'vectorank'); ?></button>
                    </form>
                    <div id="vectorank-login-message" style="margin-top:12px;"></div>
                    <script>
                    // Ensure ajaxurl is defined for WordPress AJAX
                    if (typeof ajaxurl === 'undefined') {
                        var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
                    }
                    document.getElementById('vectorank-saas-signup').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const form = e.target;
                        const data = new FormData(form);
                        document.getElementById('vectorank-login-message').textContent = 'Logging in...';
                        try {
                            const response = await fetch('<?php echo esc_url($saas_url); ?>', {
                                method: 'POST',
                                body: data
                            });
                            const result = await response.json();
                            if (result.status) {
                                // Send tokens to WordPress via AJAX
                                const wpResponse = await fetch(ajaxurl, {
                                    method: 'POST',
                                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                    body: new URLSearchParams({
                                        action: 'vectorank_save_tokens',
                                        token: result.token,
                                        client_token: result.clientToken,
                                        username: form.email.value
                                    })
                                });
                                const wpResult = await wpResponse.json();
                                // Debug output
                                console.log('wpResult:', wpResult);
                                if (wpResult.success === true || wpResult.success === 1 || (typeof wpResult.success !== 'undefined' && wpResult.success)) {
                                    document.getElementById('vectorank-login-message').textContent = '<?php echo esc_js(__('You are now logged in to VectoRank!', 'vectorank')); ?>';
                                    setTimeout(function() { window.location.reload(); }, 1200);
                                } else {
                                    document.getElementById('vectorank-login-message').textContent = (wpResult && wpResult.data) ? wpResult.data : 'Token save failed.';
                                }
                            } else {
                                document.getElementById('vectorank-login-message').textContent = result.errorMessage || 'Login failed.';
                            }
                        } catch (err) {
                            document.getElementById('vectorank-login-message').textContent = 'Network error. Please try again.';
                        }
                    });
                    </script>
                <?php endif; ?>
<?php
// PHP function to POST to SaaS (cURL)
function vectorank_post_to_saas($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local dev only; remove for production!
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true);
}

// AJAX handler to save tokens
add_action('wp_ajax_vectorank_save_tokens', function() {
    $token = isset($_POST['token']) ? sanitize_text_field($_POST['token']) : '';
    $client_token = isset($_POST['client_token']) ? sanitize_text_field($_POST['client_token']) : '';
    if ($token && $client_token) {
        update_option('vectorank_token', $token);
        update_option('vectorank_client_token', $client_token);
        wp_send_json_success();
    } else {
        wp_send_json_error(__('Missing token(s)', 'vectorank'));
    }
});

// AJAX handler to save appearance settings
add_action('wp_ajax_vectorank_save_appearance', function() {
    $button_color = isset($_POST['button_color']) ? sanitize_text_field($_POST['button_color']) : '#006400';
    $background_color = isset($_POST['background_color']) ? sanitize_text_field($_POST['background_color']) : '#FFFFFF';
    $text_color = isset($_POST['text_color']) ? sanitize_text_field($_POST['text_color']) : '#000000';
    
    update_option('vectorank_button_color', $button_color);
    update_option('vectorank_background_color', $background_color);
    update_option('vectorank_text_color', $text_color);
    
    wp_send_json_success();
});
// Handle sign out
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vectorank_logout'])) {
    delete_option('vectorank_token');
    delete_option('vectorank_client_token');
    delete_option('vectorank_username');
    echo '<script>window.location.href = "' . esc_url(vectorank_onboarding_step_url(1)) . '";</script>';
    exit;
}
?>
            <?php elseif ($current_step == 2): ?>
                <h2><?php _e('Configure Content Settings', 'vectorank'); ?></h2>
                <p><?php _e('Select the content types you want to include in AI recommendations and search results.', 'vectorank'); ?></p>
                <?php
                // Only show 'Products' if WooCommerce is active
                $has_woocommerce = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins', [])));
                // Get saved selection (array)
                $selected_types = get_option('vectorank_content_types', []);
                if (!is_array($selected_types)) $selected_types = [];
                ?>
                <form id="vectorank-content-types-form" method="post" style="margin-bottom:18px;">
                    <?php if ($has_woocommerce): ?>
                    <div style="margin-bottom:10px;">
                        <label>
                            <input type="checkbox" name="vectorank_content_types[]" value="products" <?php checked(in_array('products', $selected_types)); ?> />
                            <?php _e('Products', 'vectorank'); ?>
                        </label>
                    </div>
                    <?php endif; ?>
                    <div style="margin-bottom:10px;">
                        <label>
                            <input type="checkbox" name="vectorank_content_types[]" value="articles" <?php checked(in_array('articles', $selected_types)); ?> />
                            <?php _e('Articles', 'vectorank'); ?>
                        </label>
                    </div>
                </form>
                <script>
                // Save selection to localStorage on change (optional, for smoother UX)
                document.querySelectorAll('#vectorank-content-types-form input[type=checkbox]').forEach(function(cb) {
                    cb.addEventListener('change', function() {
                        let selected = Array.from(document.querySelectorAll('#vectorank-content-types-form input[type=checkbox]:checked')).map(x => x.value);
                        localStorage.setItem('vectorank_content_types', JSON.stringify(selected));
                    });
                });
                </script>
            <?php elseif ($current_step == 3): ?>
                <h2><?php _e('Customize Appearance', 'vectorank'); ?></h2>
                <p><?php _e('Personalize the look and feel of your recommendation widgets to match your brand.', 'vectorank'); ?></p>
                <?php
                // Get saved colors or defaults
                $button_color = get_option('vectorank_button_color', '#006400'); // Dark Green
                $background_color = get_option('vectorank_background_color', '#FFFFFF'); // White
                $text_color = get_option('vectorank_text_color', '#000000'); // Black
                ?>
                <form id="vectorank-appearance-form" method="post" style="margin-top:20px;">
                    <input type="hidden" name="vectorank_appearance" value="1" />
                    
                    <div style="margin-bottom:16px; display:flex; align-items:center;">
                        <label style="width:120px; font-weight:600;">
                            <?php _e('Button Color', 'vectorank'); ?>
                        </label>
                        <input type="color" name="button_color" value="<?php echo esc_attr($button_color); ?>" style="width:60px; height:30px; padding:0; margin-right:10px;" />
                    </div>

                    <div style="margin-bottom:16px; display:flex; align-items:center;">
                        <label style="width:120px; font-weight:600;">
                            <?php _e('Background', 'vectorank'); ?>
                        </label>
                        <input type="color" name="background_color" value="<?php echo esc_attr($background_color); ?>" style="width:60px; height:30px; padding:0; margin-right:10px;" />
                    </div>

                    <div style="margin-bottom:16px; display:flex; align-items:center;">
                        <label style="width:120px; font-weight:600;">
                            <?php _e('Text Color', 'vectorank'); ?>
                        </label>
                        <input type="color" name="text_color" value="<?php echo esc_attr($text_color); ?>" style="width:60px; height:30px; padding:0; margin-right:10px;" />
                    </div>
                </form>

                <!-- Preview section -->
                <div style="margin-top:30px; padding:20px; border-radius:8px; background:#222;">
                    <h3 style="margin-top:0; color:#fff;"><?php _e('Preview', 'vectorank'); ?></h3>
                    <div id="vectorank-preview" style="padding:20px; border-radius:6px; margin-top:10px;">
                        <?php _e('Sample recommendation widget', 'vectorank'); ?>
                    </div>
                </div>

                <script>
                // Live preview
                document.querySelectorAll('input[type="color"]').forEach(function(input) {
                    input.addEventListener('input', updatePreview);
                    input.addEventListener('change', updatePreview);
                });

                function updatePreview() {
                    var preview = document.getElementById('vectorank-preview');
                    var btnColor = document.querySelector('input[name="button_color"]').value;
                    var bgColor = document.querySelector('input[name="background_color"]').value;
                    var textColor = document.querySelector('input[name="text_color"]').value;
                    
                    preview.style.backgroundColor = bgColor;
                    preview.style.color = textColor;
                    preview.innerHTML = `
                        <div style="margin-bottom:10px;">Product Name</div>
                        <button style="background-color:${btnColor}; color:#fff; border:none; padding:8px 16px; border-radius:4px; cursor:pointer;">
                            Add To Cart
                        </button>
                    `;
                }

                // Initial preview
                updatePreview();
                </script>
            <?php elseif ($current_step == 4): ?>
                <h2><?php _e('You\'re Ready to Go!', 'vectorank'); ?></h2>
                <p><?php _e('VectoRank is now set up. Enjoy AI-powered search and product recommendations that boost your sales and customer satisfaction.', 'vectorank'); ?></p>
                <a href="<?php echo admin_url('admin.php?page=vectorank'); ?>" class="button button-primary"><?php _e('Go to Dashboard', 'vectorank'); ?></a>
            <?php endif; ?>
        </div>
        <div class="step-navigation">
            <?php if ($current_step > 1): ?>
                <a href="<?php echo esc_url(vectorank_onboarding_step_url($current_step - 1)); ?>" class="button"><?php _e('Back', 'vectorank'); ?></a>
            <?php endif; ?>
            <?php if ($current_step < $total_steps): ?>
                <?php if ($current_step == 2): ?>
                    <form id="vectorank-step2-continue" method="post" style="display:inline;">
                        <input type="hidden" name="vectorank_step2_continue" value="1" />
                        <button type="submit" class="button button-primary"><?php _e('Next', 'vectorank'); ?></button>
                    </form>
                    <script>
                    document.getElementById('vectorank-step2-continue').addEventListener('submit', function(e) {
                        // Before submitting, collect checked values and add as hidden fields
                        var form = document.getElementById('vectorank-content-types-form');
                        var checked = Array.from(form.querySelectorAll('input[type=checkbox]:checked')).map(x => x.value);
                        // Remove any existing hidden fields
                        Array.from(this.querySelectorAll('input[name^=vectorank_content_types]')).forEach(x => x.remove());
                        // Add checked as hidden fields
                        checked.forEach(function(val) {
                            var input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'vectorank_content_types[]';
                            input.value = val;
                            document.getElementById('vectorank-step2-continue').appendChild(input);
                        });
                        // Allow form to submit so PHP handler can process and redirect
                    });
                    </script>
                <?php else: ?>
                    <a href="<?php echo esc_url(vectorank_onboarding_step_url($current_step + 1)); ?>" class="button button-primary"><?php _e('Next', 'vectorank'); ?></a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
<?php
// Save content types if user continues to step 3
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vectorank_step2_continue'])) {
    $types = isset($_POST['vectorank_content_types']) ? (array)$_POST['vectorank_content_types'] : [];
    update_option('vectorank_content_types', $types);
    // Redirect to step 3
    echo '<script>window.location.href = "' . esc_url(vectorank_onboarding_step_url(3)) . '";</script>';
    exit;
}
?>
    </div>
</div>
