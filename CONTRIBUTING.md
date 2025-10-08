# Contributing to HTML Social Share Buttons

Thank you for your interest in contributing to HTML Social Share Buttons! This document provides guidelines and instructions for contributing to the project.

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Project Structure](#project-structure)
- [Coding Standards](#coding-standards)
- [Testing](#testing)
- [Submitting Changes](#submitting-changes)
- [Release Process](#release-process)

## Code of Conduct

Be respectful, inclusive, and professional in all interactions.

## Getting Started

### Prerequisites

- PHP 5.6 - 8.5+
- WordPress 3.0+
- Composer
- pnpm (Node package manager)
- Git

### Fork and Clone

1. Fork the repository on GitHub
2. Clone your fork:
   ```bash
   git clone https://github.com/YOUR-USERNAME/html-social-share-buttons.git
   cd html-social-share-buttons
   ```

## Development Setup

### Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install Node dependencies
pnpm install
```

### Start Development Environment

```bash
# Start WordPress development environment
pnpm wp-env start

# Access WordPress at http://localhost:8888
# Admin: http://localhost:8888/wp-admin
# Username: admin
# Password: password
```

### Stop Development Environment

```bash
pnpm wp-env stop
```

## Project Structure

```
html-social-share-buttons/
├── src/                      # PSR-4 source code
│   ├── Core/                 # Plugin bootstrap
│   ├── IconSystem/           # Icon management
│   ├── Services/             # Business logic services
│   ├── Renderers/            # HTML/CSS generation
│   ├── Options/              # Settings management
│   ├── Frontend/             # Shortcode handlers
│   ├── Admin/                # Admin UI and widgets
│   └── Compatibility/        # Backward compatibility
├── tests/                    # Test suites
│   ├── Unit/                 # PHPUnit unit tests
│   ├── visual/               # Playwright visual tests
│   └── fixtures/             # Test fixtures
├── docs/                     # Documentation
├── archive/                  # v2.2.1 reference code
├── build/                    # Compiled assets
├── assets/                   # Static assets
│   └── iconset/              # Icon images
├── vendor/                   # Composer dependencies
├── node_modules/             # npm dependencies
└── html-social-share.php     # Main plugin file
```

## Coding Standards

### PHP

- **PSR-4**: Namespace `HtmlSocialShare\` maps to `src/`
- **WordPress Coding Standards**: Follow WPCS
- **PHP Compatibility**: Code must work on PHP 5.6 - 8.5+
- **Security**: 
  - Sanitize all inputs
  - Escape all outputs
  - Use WordPress sanitization functions
- **Performance**:
  - Cache expensive operations
  - Minimize database queries
  - Use lazy loading

### Code Style

```bash
# Check code style
composer phpcs

# Fix code style automatically
composer phpcbf

# Run static analysis
composer phpstan
```

### Documentation

- All classes, methods, and functions must have PHPDoc blocks
- Include `@param`, `@return`, and `@throws` tags
- Describe what the code does, not how it does it
- Update documentation when changing functionality

Example:
```php
/**
 * Builds a share URL for a given network
 *
 * @param string $network The network identifier (facebook, twitter, etc.)
 * @param array $context Context data (url, title, image)
 * @return string The generated share URL
 */
public function buildUrl(string $network, array $context): string {
    // Implementation
}
```

## Testing

### Run All Tests

```bash
# PHP unit tests
composer test

# Playwright visual tests
pnpm test

# Specific test file
composer test -- --filter HtmlOutputTest
```

### Writing Tests

**Unit Tests (PHPUnit):**
```php
namespace HtmlSocialShare\Tests\Unit;

use PHPUnit\Framework\TestCase;

class MyClassTest extends TestCase {
    public function testSomething(): void {
        $this->assertTrue(true);
    }
}
```

**Visual Tests (Playwright):**
```typescript
import { test, expect } from '@playwright/test';

test('button renders correctly', async ({ page }) => {
    await page.goto('/');
    await expect(page.locator('.zmshbt')).toBeVisible();
});
```

### Test Coverage

- Aim for 80%+ code coverage
- All public methods should have tests
- Test edge cases and error conditions
- Write tests before fixing bugs

## Submitting Changes

### Branch Naming

- `feature/description` - New features
- `fix/description` - Bug fixes
- `docs/description` - Documentation updates
- `refactor/description` - Code refactoring
- `test/description` - Test improvements

### Commit Messages

Follow [Conventional Commits](https://www.conventionalcommits.org/):

```
type(scope): subject

body (optional)

footer (optional)
```

Types:
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Code style (formatting)
- `refactor`: Code refactoring
- `test`: Tests
- `chore`: Maintenance

Examples:
```
feat(shortcode): add html_social_share shortcode

Implements new modern shortcode with clean attribute names
while maintaining backward compatibility with zm_sh_btn.

Closes #123
```

### Pull Request Process

1. **Create Feature Branch**
   ```bash
   git checkout -b feature/my-feature
   ```

2. **Make Changes**
   - Write code
   - Add/update tests
   - Update documentation
   - Follow coding standards

3. **Test Changes**
   ```bash
   composer test
   pnpm test
   composer phpcs
   ```

4. **Commit Changes**
   ```bash
   git add .
   git commit -m "feat(scope): description"
   ```

5. **Push to Fork**
   ```bash
   git push origin feature/my-feature
   ```

6. **Create Pull Request**
   - Describe what changed and why
   - Reference related issues
   - Include screenshots for UI changes
   - Ensure CI passes

7. **Code Review**
   - Address reviewer feedback
   - Update PR as needed
   - Squash commits if requested

8. **Merge**
   - Maintainer will merge once approved
   - Delete feature branch after merge

## Release Process

Releases are handled by maintainers:

1. **Version Bump**
   - Update version in `html-social-share.php`
   - Update version in `package.json`
   - Update `CHANGELOG.md`
   - Update `Readme.txt`

2. **Tag Release**
   ```bash
   git tag -a v3.0.0 -m "Release v3.0.0"
   git push origin v3.0.0
   ```

3. **Deploy to WordPress.org**
   - Build assets
   - Create release package
   - Deploy to WordPress plugin repository

## Questions?

- **Documentation**: See [docs/](docs/) directory
- **Issues**: Open a GitHub issue
- **Discussions**: Use GitHub Discussions

## License

By contributing, you agree that your contributions will be licensed under the GPLv2 or later.

---

**Thank you for contributing!** 🎉
