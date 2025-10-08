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
            
            // Logout
            $(document).on('click', '#vectorrank-logout', this.handleLogout);
            
            // Content filters
            $(document).on('click', '.filter-btn', this.handleContentFilter);
            $(document).on('click', '.refresh-content', this.refreshContent);
            
            // Feature toggles
            $(document).on('change', '.feature-checkbox', this.handleFeatureToggle);
            
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
            
            const username = $form.find('#username').val();
            const password = $form.find('#password').val();
            
            if (!username || !password) {
                VectorRank.showMessage('Please enter both username and password.', 'error');
                return;
            }
            
            // Show loading state
            $button.addClass('loading').prop('disabled', true);
            
            $.ajax({
                url: vectorrank_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'vectorrank_login',
                    username: username,
                    password: password,
                    nonce: vectorrank_ajax.nonce
                },
                success: function(response) {
                    if (response.success) {
                        VectorRank.showMessage(response.data.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        VectorRank.showMessage(response.data.message, 'error');
                    }
                },
                error: function() {
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
                // In a real implementation, you would call your SaaS API to logout
                // For now, we'll just reload the page
                location.reload();
            }
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
            const messageClass = type === 'success' ? 'success' : 'error';
            
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