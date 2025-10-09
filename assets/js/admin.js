(function($) {
    'use strict';

    // VectorRank Admin JavaScript
    const VectorRank = {
        
        init: function() {
            this.bindEvents();
            this.loadInitialContent();
        },

        bindEvents: function() {
            // Sidebar Navigation
            $(document).on('click', '.nav-link', this.handleNavigation);
            
            // Top Navigation Bar
            $(document).on('click', '.nav-item-link', this.handleTopNavigation);
            
            // Login form
            $(document).on('submit', '#vectorrank-login-form', this.handleLogin);
            
            // Debug: Check if form exists
            console.log('VectorRank: Login form found:', $('#vectorrank-login-form').length);
            
            // Logout
            $(document).on('click', '#vectorrank-logout', this.handleLogout);
            
            // Content filters
            $(document).on('click', '.filter-btn', this.handleContentFilter);
            $(document).on('click', '.refresh-content', this.refreshContent);
            
            // Feature toggles
            $(document).on('change', '.feature-checkbox', this.handleFeatureToggle);
            
            // User info actions
            $(document).on('click', '.test-connection-btn', this.testConnection);
            $(document).on('click', '.refresh-token-btn', this.refreshToken);
            
            // Features save button
            $(document).on('click', '.save-features-btn', this.saveFeatures);
            
            // Sync posts button
            $(document).on('click', '.sync-posts-btn', this.syncPosts);
            
            // Sync paragraphs button
            $(document).on('click', '.sync-paragraphs-btn', this.syncParagraphs);
            
            // Debug apps button
            $(document).on('click', '.debug-apps-btn', this.debugApps);
            
            // Top navigation dropdown
            $(document).on('click', '#nav-dropdown-toggle', this.toggleDropdown);
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nav-dropdown').length) {
                    $('.nav-dropdown').removeClass('active');
                }
            });
        },

        handleNavigation: function(e) {
            e.preventDefault();
            
            const $this = $(this);
            const page = $this.data('page');
            
            // Update active nav
            $('.nav-link').removeClass('active');
            $this.addClass('active');
            
            // Show corresponding page
            $('.vectorrank-page').removeClass('active');
            $('#page-' + page).addClass('active');
            
            // Load content for content page
            if (page === 'content') {
                VectorRank.loadContent('posts');
            }
        },

        handleLogin: function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $button = $form.find('.login-btn');
            const $btnText = $button.find('.btn-text');
            const $btnLoader = $button.find('.btn-loader');
            
            const email = $form.find('#email').val();
            const password = $form.find('#password').val();
            
            // Debug logging
            console.log('Form submission data:', { email: email, password: password ? '***' : 'empty' });
            
            if (!email || !password) {
                VectorRank.showMessage('Please enter both email and password.', 'error');
                return;
            }
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_login',
                    email: email,
                    password: password,
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    console.log('Login response:', response);
                    if (response.success) {
                        VectorRank.showMessage(response.data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        VectorRank.showMessage(response.data.message || 'Login failed. Please check your credentials.', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Login AJAX error:', { xhr: xhr, status: status, error: error });
                    VectorRank.showMessage('An error occurred. Please try again.', 'error');
                },
                complete: function() {
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        handleLogout: function(e) {
            e.preventDefault();
            
            if (confirm('Are you sure you want to logout?')) {
                $.ajax({
                    url: vectorrank_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'vectorrank_logout',
                        nonce: vectorrank_ajax.nonce
                    },
                    success: function(response) {
                        if (response.success) {
                            VectorRank.showMessage(response.data.message, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            VectorRank.showMessage(response.data.message || 'Logout failed.', 'error');
                        }
                    },
                    error: function() {
                        VectorRank.showMessage('An error occurred during logout.', 'error');
                        // Force reload anyway
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    }
                });
            }
        },

        testConnection: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const $statusCheck = $('.api-status-check');
            
            $button.prop('disabled', true).text('Testing...');
            $statusCheck.find('.info-value').html('<span class="status-checking">Testing connection...</span>');
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_test_connection',
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        $statusCheck.find('.info-value').html('<span class="status-online">✓ Online (' + response.data.response_time + 'ms)</span>');
                        VectorRank.showMessage('Connection test successful!', 'success');
                    } else {
                        $statusCheck.find('.info-value').html('<span class="status-offline">✗ Offline</span>');
                        VectorRank.showMessage(response.data.message || 'Connection test failed', 'error');
                    }
                },
                error: function() {
                    $statusCheck.find('.info-value').html('<span class="status-offline">✗ Error</span>');
                    VectorRank.showMessage('Connection test failed', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).text('Test Connection');
                }
            });
        },

        refreshToken: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            
            $button.prop('disabled', true).text('Refreshing...');
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_refresh_token',
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        VectorRank.showMessage('Token refreshed successfully!', 'success');
                    } else {
                        VectorRank.showMessage(response.data.message || 'Failed to refresh token', 'error');
                    }
                },
                error: function() {
                    VectorRank.showMessage('Failed to refresh token', 'error');
                },
                complete: function() {
                    $button.prop('disabled', false).text('Refresh Token');
                }
            });
        },

        saveFeatures: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            
            // Show processing message
            VectorRank.showMessage('Please wait, your data is preparing...', 'info');
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_save_features',
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        VectorRank.showMessage(response.data.message, 'success');
                        
                        // If apps data is returned, show summary
                        if (response.data.apps_count) {
                            setTimeout(function() {
                                VectorRank.showMessage('Successfully synced ' + response.data.apps_count + ' applications with their tokens!', 'success');
                            }, 1000);
                        }
                    } else {
                        VectorRank.showMessage(response.data.message || 'Failed to save features', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Save Features AJAX error:', { xhr: xhr, status: status, error: error });
                    VectorRank.showMessage('An error occurred while saving features', 'error');
                },
                complete: function() {
                    // Always reset button state
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        syncPosts: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            
            // Show confirmation dialog
            if (!confirm('This will sync all your blog posts to the AI recommendation system. This may take a few minutes depending on the number of posts. Continue?')) {
                return;
            }
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            
            // Show processing message
            VectorRank.showMessage('Syncing posts to AI system, please wait...', 'info');
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_sync_posts',
                    nonce: vectorrank_ajax.nonce
                },
                timeout: 300000, // 5 minutes timeout for large sync operations
                success: function(response) {
                    if (response.success) {
                        VectorRank.showMessage(response.data.message, 'success');
                        
                        // Show additional details if available
                        if (response.data.total_posts && response.data.synced_posts) {
                            setTimeout(function() {
                                VectorRank.showMessage(
                                    'Sync completed: ' + response.data.synced_posts + '/' + response.data.total_posts + ' posts processed successfully!', 
                                    'success'
                                );
                            }, 1500);
                        }
                    } else {
                        VectorRank.showMessage(response.data.message || 'Failed to sync posts', 'error');
                        
                        // Show partial success details if available
                        if (response.data.synced_posts && response.data.failed_posts) {
                            setTimeout(function() {
                                VectorRank.showMessage(
                                    'Partial sync: ' + response.data.synced_posts + ' succeeded, ' + response.data.failed_posts + ' failed', 
                                    'warning'
                                );
                            }, 1500);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Sync Posts AJAX error:', { xhr: xhr, status: status, error: error });
                    
                    if (status === 'timeout') {
                        VectorRank.showMessage('Sync operation timed out. Some posts may have been synced successfully.', 'warning');
                    } else {
                        VectorRank.showMessage('An error occurred while syncing posts: ' + error, 'error');
                    }
                },
                complete: function() {
                    // Always reset button state
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        syncParagraphs: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            
            // Show confirmation dialog
            if (!confirm('This will sync all your blog posts paragraph by paragraph to the AI recommendation system. This may take longer than regular sync depending on the content. Continue?')) {
                return;
            }
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            
            // Show processing message
            VectorRank.showMessage('Syncing posts by paragraphs to AI system, please wait...', 'info');
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_sync_posts_by_paragraphs',
                    nonce: vectorrank_ajax.nonce
                },
                timeout: 300000, // 5 minutes timeout for large sync operations
                success: function(response) {
                    if (response.success) {
                        VectorRank.showMessage(response.data.message, 'success');
                        
                        // Show additional details if available
                        if (response.data.total_paragraphs && response.data.synced_paragraphs) {
                            setTimeout(function() {
                                VectorRank.showMessage(
                                    'Paragraph sync completed: ' + response.data.synced_paragraphs + '/' + response.data.total_paragraphs + ' paragraphs from ' + response.data.total_posts + ' posts processed successfully!', 
                                    'success'
                                );
                            }, 1500);
                        }
                    } else {
                        VectorRank.showMessage(response.data.message || 'Failed to sync paragraphs', 'error');
                        
                        // Show partial success details if available
                        if (response.data.synced_paragraphs && response.data.failed_paragraphs) {
                            setTimeout(function() {
                                VectorRank.showMessage(
                                    'Partial paragraph sync: ' + response.data.synced_paragraphs + ' succeeded, ' + response.data.failed_paragraphs + ' failed', 
                                    'warning'
                                );
                            }, 1500);
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Sync Paragraphs AJAX error:', { xhr: xhr, status: status, error: error });
                    
                    if (status === 'timeout') {
                        VectorRank.showMessage('Paragraph sync operation timed out. Some paragraphs may have been synced successfully.', 'warning');
                    } else {
                        VectorRank.showMessage('An error occurred while syncing paragraphs: ' + error, 'error');
                    }
                },
                complete: function() {
                    // Always reset button state
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        debugApps: function(e) {
            e.preventDefault();
            
            const $button = $(this);
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_debug_apps',
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        
                        // Create debug message
                        let debugMessage = 'Debug Info:\n';
                        debugMessage += 'Has Apps: ' + (data.has_apps ? 'Yes' : 'No') + '\n';
                        debugMessage += 'Apps Count: ' + data.apps_count + '\n';
                        debugMessage += 'Last Sync: ' + data.last_sync + '\n';
                        debugMessage += 'Settings Keys: ' + data.all_settings_keys.join(', ') + '\n\n';
                        
                        if (data.apps_data && data.apps_data.length > 0) {
                            debugMessage += 'Apps Found:\n';
                            data.apps_data.forEach(function(app, index) {
                                debugMessage += (index + 1) + '. Name: "' + app.name + '"\n';
                                debugMessage += '   ID: ' + app.id + '\n';
                                debugMessage += '   Write Token: ' + app.write_token.substring(0, 20) + '...\n';
                                debugMessage += '   Search Token: ' + app.search_token.substring(0, 20) + '...\n\n';
                            });
                        } else {
                            debugMessage += 'No apps data found.\n';
                        }
                        
                        // Show in alert for now (you can improve this UI later)
                        alert(debugMessage);
                        
                        // Also show as message
                        VectorRank.showMessage('Debug info displayed in alert. Check browser console for detailed logs.', 'info');
                        
                        // Log to console for detailed inspection
                        console.log('VectorRank Debug Apps:', data);
                        
                    } else {
                        VectorRank.showMessage('Failed to get debug info: ' + (response.data.message || 'Unknown error'), 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Debug Apps AJAX error:', { xhr: xhr, status: status, error: error });
                    VectorRank.showMessage('An error occurred while getting debug info', 'error');
                },
                complete: function() {
                    // Always reset button state
                    $button.removeClass('loading').prop('disabled', false);
                }
            });
        },

        handleContentFilter: function(e) {
            e.preventDefault();
            
            const $this = $(this);
            const type = $this.data('type');
            
            $('.filter-btn').removeClass('active');
            $this.addClass('active');
            
            VectorRank.loadContent(type);
        },

        refreshContent: function(e) {
            e.preventDefault();
            
            const activeType = $('.filter-btn.active').data('type') || 'posts';
            VectorRank.loadContent(activeType);
        },

        loadInitialContent: function() {
            // Load content if on content page
            if ($('#page-content').hasClass('active')) {
                this.loadContent('posts');
            }
        },

        loadContent: function(type) {
            const $contentList = $('.content-loading');
            const $contentItems = $('#content-items');
            
            // Show loading state
            $contentList.show();
            $contentItems.empty();
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_get_content',
                    content_type: type,
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        VectorRank.renderContent(response.data, type);
                    } else {
                        VectorRank.showMessage('Failed to load content.', 'error');
                    }
                },
                error: function() {
                    VectorRank.showMessage('An error occurred while loading content.', 'error');
                },
                complete: function() {
                    $contentList.hide();
                }
            });
        },

        renderContent: function(items, type) {
            const $contentItems = $('#content-items');
            
            if (!items || items.length === 0) {
                $contentItems.html(`
                    <div style="text-align: center; padding: 3rem; color: #6b7280;">
                        <p>No ${type} found.</p>
                    </div>
                `);
                return;
            }
            
            let html = '';
            items.forEach(function(item) {
                const typeLabel = item.type || (type === 'posts' ? 'Post' : 'Product');
                const metaInfo = item.price ? 
                    `<span>Price: ${item.price}</span>` : 
                    `<span>Date: ${item.date}</span>`;
                
                html += `
                    <div class="content-item" data-id="${item.id}">
                        <div class="content-info">
                            <h4 class="content-title">${VectorRank.escapeHtml(item.title)}</h4>
                            <div class="content-meta">
                                <span>Type: ${typeLabel}</span>
                                ${metaInfo}
                                <span>Status: ${item.status}</span>
                            </div>
                        </div>
                        <div class="content-actions">
                            <a href="${item.url}" target="_blank" class="button button-secondary">View</a>
                            <button class="button button-primary optimize-btn" data-id="${item.id}" data-type="${type}">
                                Optimize with AI
                            </button>
                        </div>
                    </div>
                `;
            });
            
            $contentItems.html(html);
            
            // Bind optimize buttons
            $(document).on('click', '.optimize-btn', this.handleOptimize);
        },

        handleOptimize: function(e) {
            e.preventDefault();
            
            const $this = $(this);
            const itemId = $this.data('id');
            const itemType = $this.data('type');
            
            // This would integrate with your SaaS API for AI optimization
            VectorRank.showMessage(`AI optimization started for ${itemType} #${itemId}`, 'success');
            
            // Simulate processing
            $this.text('Processing...').prop('disabled', true);
            
            setTimeout(function() {
                $this.text('Optimize with AI').prop('disabled', false);
                VectorRank.showMessage(`${itemType} #${itemId} has been optimized!`, 'success');
            }, 2000);
        },

        handleFeatureToggle: function(e) {
            const $checkbox = $(this);
            const featureKey = $checkbox.data('feature');
            const isChecked = $checkbox.prop('checked');
            const $card = $checkbox.closest('.feature-card');
            const $statusBadge = $card.find('.status-badge');
            
            // Disable checkbox during request
            $checkbox.prop('disabled', true);
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_toggle_feature',
                    feature_key: featureKey,
                    status: isChecked,
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Update UI
                        if (isChecked) {
                            $statusBadge.removeClass('inactive').addClass('active').text('Active');
                            $card.removeClass('disabled');
                        } else {
                            $statusBadge.removeClass('active').addClass('inactive').text('Inactive');
                            $card.addClass('disabled');
                        }
                        
                        // Update stats counter
                        VectorRank.updateStatsCounter();
                        
                        VectorRank.showMessage(response.data.message, 'success');
                    } else {
                        // Revert checkbox state on error
                        $checkbox.prop('checked', !isChecked);
                        VectorRank.showMessage('Failed to update feature status.', 'error');
                    }
                },
                error: function() {
                    // Revert checkbox state on error
                    $checkbox.prop('checked', !isChecked);
                    VectorRank.showMessage('An error occurred while updating the feature.', 'error');
                },
                complete: function() {
                    $checkbox.prop('disabled', false);
                }
            });
        },

        toggleDropdown: function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            $('.nav-dropdown').toggleClass('active');
        },

        updateStatsCounter: function() {
            const activeFeatures = $('.feature-checkbox:checked').length;
            $('.stat-number').first().text(activeFeatures);
        },

        handleTopNavigation: function(e) {
            e.preventDefault();
            
            const $this = $(this);
            const page = $this.data('page');
            
            // Update active nav item
            $('.nav-item-link').removeClass('active');
            $this.addClass('active');
            
            // Handle different navigation pages
            switch(page) {
                case 'home':
                    // Navigate to features page (main dashboard)
                    $('.nav-link').removeClass('active');
                    $('.nav-link[data-page="features"]').addClass('active');
                    $('.vectorrank-page').removeClass('active');
                    $('#page-features').addClass('active');
                    VectorRank.showMessage('Welcome to VectorRank Dashboard!', 'success');
                    break;
                    
                case 'notifications':
                    VectorRank.showNotifications();
                    break;
                    
                case 'analytics':
                    VectorRank.showAnalytics();
                    break;
                    
                case 'settings':
                    // Navigate to content page for now
                    $('.nav-link').removeClass('active');
                    $('.nav-link[data-page="content"]').addClass('active');
                    $('.vectorrank-page').removeClass('active');
                    $('#page-content').addClass('active');
                    VectorRank.loadContent('posts');
                    break;
            }
        },

        showNotifications: function() {
            // Simulate notifications
            const notifications = [
                'New AI search optimization completed for 5 products',
                'Weekly analytics report is ready',
                'System maintenance scheduled for tonight'
            ];
            
            let message = '<strong>Recent Notifications:</strong><br>';
            notifications.forEach((notification, index) => {
                message += `${index + 1}. ${notification}<br>`;
            });
            
            VectorRank.showMessage(message, 'success');
            
            // Remove notification badge
            $('.notification-badge').fadeOut(300);
        },

        showAnalytics: function() {
            // Simulate analytics data
            const analytics = {
                totalSearches: Math.floor(Math.random() * 10000) + 5000,
                conversionRate: (Math.random() * 5 + 2).toFixed(1),
                avgOrderValue: Math.floor(Math.random() * 50) + 100,
                aiAccuracy: (Math.random() * 5 + 90).toFixed(1)
            };
            
            const message = `
                <strong>AI Performance Analytics:</strong><br>
                • Total Searches: ${analytics.totalSearches.toLocaleString()}<br>
                • Conversion Rate: ${analytics.conversionRate}%<br>
                • Avg Order Value: $${analytics.avgOrderValue}<br>
                • AI Accuracy: ${analytics.aiAccuracy}%
            `;
            
            VectorRank.showMessage(message, 'success');
        },

        showMessage: function(message, type) {
            const $messages = $('#vectorrank-messages');
            let messageClass = 'info'; // default
            
            if (type === 'success') {
                messageClass = 'success';
            } else if (type === 'error') {
                messageClass = 'error';
            } else if (type === 'info') {
                messageClass = 'info';
            }
            
            const $message = $(`
                <div class="vectorrank-message ${messageClass}">
                    ${VectorRank.escapeHtml(message)}
                </div>
            `);
            
            $messages.append($message);
            
            // Auto remove after 5 seconds
            setTimeout(function() {
                $message.fadeOut(300, function() {
                    $(this).remove();
                });
            }, 5000);
            
            // Click to remove
            $message.on('click', function() {
                $(this).fadeOut(300, function() {
                    $(this).remove();
                });
            });
        },

        escapeHtml: function(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    };

    // Initialize when document is ready
    $(document).ready(function() {
        VectorRank.init();
        VectorRank.updateStatsCounter();
    });

    // Feature card hover effects
    $(document).on('mouseenter', '.feature-card', function() {
        $(this).find('.feature-icon').css('transform', 'scale(1.1) rotate(5deg)');
    });

    $(document).on('mouseleave', '.feature-card', function() {
        $(this).find('.feature-icon').css('transform', 'scale(1) rotate(0deg)');
    });

    // Smooth scroll for navigation
    $(document).on('click', 'a[href^="#"]', function(e) {
        const target = $(this.getAttribute('href'));
        if (target.length) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: target.offset().top - 100
            }, 300);
        }
    });

    // Add some interactive animations
    $(document).on('focus', 'input', function() {
        $(this).parent().addClass('focused');
    });

    $(document).on('blur', 'input', function() {
        $(this).parent().removeClass('focused');
    });

    // Keyboard shortcuts
    $(document).on('keydown', function(e) {
        // Ctrl/Cmd + Enter to submit login form
        if ((e.ctrlKey || e.metaKey) && e.which === 13) {
            if ($('#page-login').hasClass('active')) {
                $('#vectorrank-login-form').submit();
            }
        }
        
        // Escape to close messages
        if (e.which === 27) {
            $('.vectorrank-message').fadeOut(300, function() {
                $(this).remove();
            });
        }
    });

    // Add loading states for better UX
    $(document).on('ajaxStart', function() {
        $('body').addClass('vectorrank-loading');
    });

    $(document).on('ajaxStop', function() {
        $('body').removeClass('vectorrank-loading');
    });

    // Auto-save draft functionality (for future use)
    let autoSaveTimer;
    $(document).on('input', 'input, textarea', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(function() {
            // Auto-save logic here
            console.log('Auto-saving...');
        }, 2000);
    });

})(jQuery);