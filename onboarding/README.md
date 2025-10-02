# Onboarding: AI Product Recommendation & Search Plugin

## 1. Installation
- Upload to `/wp-content/plugins/` and activate in WordPress admin.

## 2. SaaS Integration
- On login, users are redirected to your SaaS app with a key.
- After authentication, your app returns to WordPress with a token.
- The plugin uses this token for secure API communication.

## 3. Multilingual Setup
- Add translations in `includes/i18n/languages/`.
- Use WordPress translation functions in templates and PHP files.

## 4. Customization
- UI components: `templates/`
- Styles/scripts: `assets/`
- API/business logic: `includes/`

## 5. Development
- Follow WordPress coding standards.
- Use modular classes in `includes/`.
- Keep UI/UX clean and accessible.

## 6. Support
- For help, open an issue or contact the maintainer.
