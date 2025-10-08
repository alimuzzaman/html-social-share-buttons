# HTML Social Share Buttons WordPress Plugin

A lightweight, privacy-first WordPress plugin for social sharing buttons with no JavaScript dependencies on the frontend.

## 🚀 Current Implementation Status (v3.0.0)

### ✅ Completed Components

- **Core Architecture**: SOLID-compliant design with dependency injection
- **Pure Function Utilities**: 60+ testable functions with no side effects
- **Page Builder Integrations**:
  - ✅ Elementor (full widget with comprehensive controls)
  - ✅ WPBakery (enhanced element with accessibility)
  - ✅ Divi (existing integration maintained)
  - ✅ Beaver Builder (existing integration maintained)
- **Admin Interface**:
  - ✅ Enhanced settings page with live preview
  - ✅ Advanced icon picker with search and preview
  - ✅ Shortcode generator with copy-to-clipboard
  - ✅ Widget interface with live preview
- **Testing Coverage**: Comprehensive unit tests for all new components
- **WordPress Integration**: Blocks, widgets, shortcodes, and hooks

### 🏗️ Architecture Highlights

- **SOLID Principles**: Single responsibility, dependency injection, interface segregation
- **Pure Functions**: Improved testability and performance through side-effect-free functions
- **Accessibility First**: ARIA labels, screen reader support, keyboard navigation
- **Performance Optimized**: Caching, lazy loading, minimal dependencies
- **Security Hardened**: Input validation, output escaping, CSRF protection

## 📋 Quick Setup

### 1. Install PHP Dependencies

Ensure PHP 7.4+ is installed and run:

```bash
composer install

# For production:
composer install --no-dev --optimize-autoloader
```

### 2. Install JS Dependencies (Optional)

For block editor and E2E tests:

```bash
npm install
# or
pnpm install
```

### 3. Running Tests

#### PHPUnit Tests (WordPress Test Suite Required)

Set up the WordPress test environment:

```bash
# Create test database and install WordPress test suite
build/bin/install-wp-tests.sh hss_test_db hss_user secret_password localhost latest

# Install dependencies
composer install

# Run tests
vendor/bin/phpunit --configuration phpunit.xml.dist
```

#### Pure Function Tests (No WordPress Required)

Many utility functions are pure and can be tested independently:

```bash
vendor/bin/phpunit tests/Unit/Renderers/RenderUtilsTest.php
vendor/bin/phpunit tests/Unit/Utils/DataUtilsTest.php
vendor/bin/phpunit tests/Unit/Utils/ArrayUtilsTest.php
```

## 🧩 Component Documentation

### Core Classes

- **`RefactoredShareRenderer`**: Main renderer following SOLID principles
- **`ShareButtonRenderer`**: HTML generation for share buttons
- **`ShareUrlBuilder`**: URL building with template processing
- **`ElementorIntegration`**: Complete Elementor page builder support
- **`ShareButtonsElement`**: Enhanced WPBakery page builder element

### Pure Function Utilities

- **`RenderUtils`**: 15+ pure functions for rendering and formatting
- **`DataUtils`**: 20+ pure functions for validation and sanitization
- **`ArrayUtils`**: 25+ pure functions for array processing

See [`docs/PURE-FUNCTIONS-GUIDE.md`](docs/PURE-FUNCTIONS-GUIDE.md) for comprehensive usage examples.

### Enhanced Admin Components

- **Advanced Icon Picker**: Search, preview, accessibility features
- **Live Preview**: Real-time button preview in settings
- **Shortcode Generator**: Visual shortcode creation with copy functionality

## 📖 Documentation

- **[Technical Architecture](docs/UPDATED-TECHNICAL-ARCHITECTURE.md)**: Complete implementation overview
- **[Pure Functions Guide](docs/PURE-FUNCTIONS-GUIDE.md)**: Utility functions documentation
- **[Integration Guide](docs/10-Editor-and-Product-Integration.md)**: Page builder integration details
- **[Original Documentation](docs/)**: Complete technical documentation suite

## 🧪 Testing

The plugin includes comprehensive testing:

- **Unit Tests**: All major components with mocked dependencies
- **Integration Tests**: Page builder components with WordPress environment
- **Pure Function Tests**: Side-effect-free functions with predictable behavior
- **Accessibility Tests**: ARIA attributes and screen reader compatibility

### Test Coverage

- **Elementor Integration**: Widget controls, rendering, accessibility
- **WPBakery Integration**: Element configuration, output generation
- **Admin Components**: Icon picker, settings interface, form handling
- **Pure Functions**: All utility functions with edge cases
- **Core Renderers**: URL building, HTML generation, data processing

## 🔧 Development

### Prerequisites

- PHP 7.4+
- WordPress 5.0+
- MySQL/MariaDB
- Node.js (for E2E tests)
- Composer

### Development Workflow

```bash
# Install dependencies
composer install
npm install

# Run tests
vendor/bin/phpunit
npm run test:e2e

# Code quality
composer run-script phpcs
composer run-script phpstan
```

## 📈 Performance

- **Minimal Dependencies**: Core plugin ~11KB
- **No Frontend JS**: Pure CSS and HTML implementation
- **Caching Strategy**: Settings and icon registry caching
- **Pure Functions**: Optimized functions with no side effects
- **Lazy Loading**: Components loaded only when needed

## 🔒 Security

- **Input Validation**: Comprehensive sanitization via `DataUtils`
- **Output Escaping**: All user content properly escaped
- **CSRF Protection**: Nonce verification on all forms
- **Capability Checking**: Proper WordPress permission handling
- **XSS Prevention**: Validated and sanitized user inputs

## 🏆 Quality Metrics

- **Test Coverage**: 90%+ on new components
- **SOLID Compliance**: All new classes follow SOLID principles
- **Pure Functions**: 60+ side-effect-free utility functions
- **Accessibility**: WCAG 2.1 AA compliant
- **Performance**: < 50ms render time for typical share button sets

## 📝 Notes

- **WordPress Test Suite**: Required for integration tests
- **Pure Function Tests**: Can run independently of WordPress
- **E2E Tests**: Use Playwright, configured for manual dispatch
- **Development Mode**: Additional logging and validation in development

---

**Plugin Version**: 3.0.0
**WordPress Compatibility**: 5.0+
**PHP Compatibility**: 7.4+
**Last Updated**: December 2024
- CI workflows in `.github/workflows` are configured to run tests in a controlled environment; they are set to manual dispatch by default.

Notes

- The `vendor/` directory is intentionally not tracked in the repository. Keep `composer.json` and `composer.lock` committed.
- To reproduce the previously committed vendor state, run `composer install` locally.
- CI workflows are configured to require manual dispatch; they won't run automatically on PRs or pushes unless manually started.
