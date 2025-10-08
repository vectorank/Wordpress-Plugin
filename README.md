# VectorRank WordPress Plugin

A modern WordPress plugin that integrates AI-powered search and recommendation features with your SaaS platform.

## Features

### 🔍 AI-Powered Search & Recommendations
- **AI Powered Search**: Intelligent search that understands context and user intent
- **Smart Autocomplete**: Search bar autocomplete with spelling correction
- **Cross-Sell Recommendations**: AI-driven product recommendations for increased sales
- **Personalized Recommendations**: Customer-specific suggestions based on behavior
- **Favorite Product Popups**: Smart popup recommendations for favorite items
- **Email Marketing**: Automated recommendation emails to customers

### 🎨 Modern Admin Interface
- **Three-Page Dashboard**: Login, Features, and Content management
- **Responsive Design**: Works perfectly on all devices
- **Real-time Updates**: AJAX-powered interface with smooth animations
- **User-Friendly**: Intuitive navigation and modern UI components

### 🔧 Technical Features
- **WordPress Integration**: Seamlessly integrates with WordPress and WooCommerce
- **AJAX Support**: Fast, responsive user interactions
- **Security**: Proper nonce verification and data sanitization
- **Extensible**: Easy to extend with additional features

## Installation

1. Upload the `vectorank-plugin` folder to your `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to **VectorRank** in your WordPress admin menu
4. Login with your SaaS credentials to start using AI features

## Usage

### Getting Started
1. **Login Page**: Enter your SaaS platform credentials to connect
2. **Features Page**: View and manage available AI-powered features
3. **Content Page**: See your WordPress posts and products with AI optimization options

### Managing Features
- All AI features are automatically activated upon successful login
- Monitor feature status from the Features dashboard
- Each feature includes detailed descriptions and current status

### Content Management
- View all your WordPress posts and WooCommerce products
- Use AI optimization features for individual items
- Filter content by type (Posts, Products)
- Direct links to view and edit content

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- WooCommerce (optional, for product features)

## Developer Information

### File Structure
```
vectorank-plugin/
├── vectorank-plugin.php     # Main plugin file
├── includes/
│   └── admin-page.php       # Admin interface template
├── assets/
│   ├── css/
│   │   └── admin.css        # Admin styles
│   └── js/
│       └── admin.js         # Admin JavaScript
└── README.md               # This file
```

### Hooks and Filters
- `vectorrank_login` - AJAX action for user authentication
- `vectorrank_get_content` - AJAX action for loading content
- Custom hooks available for extending functionality

### Customization
The plugin is built with extensibility in mind. You can:
- Add custom CSS by enqueueing additional stylesheets
- Extend JavaScript functionality through the VectorRank object
- Add custom AJAX actions for new features
- Modify the admin interface template

## Support

For support and feature requests, please contact the VectorRank team through your SaaS platform dashboard.

## Changelog

### Version 1.0.0
- Initial release
- Three-page admin interface (Login, Features, Content)
- AI-powered features integration
- WordPress and WooCommerce compatibility
- Modern, responsive design
- AJAX-powered interactions

## License

This plugin is proprietary software. All rights reserved.