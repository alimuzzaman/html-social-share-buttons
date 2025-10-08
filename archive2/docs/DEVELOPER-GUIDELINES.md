# Developer Contribution Guidelines

## Table of Contents

1. [Getting Started](#getting-started)
2. [Development Setup](#development-setup)
3. [Code Standards](#code-standards)
4. [Architecture Guidelines](#architecture-guidelines)
5. [Testing Requirements](#testing-requirements)
6. [Security Standards](#security-standards)
7. [Pull Request Process](#pull-request-process)
8. [Performance Guidelines](#performance-guidelines)

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- WordPress 5.0 or higher
- Composer for dependency management
- Node.js and npm for build tools
- Basic understanding of WordPress plugin development

### Quick Start

1. **Clone the repository:**

   ```bash
   git clone https://github.com/your-org/html-social-share-buttons.git
   cd html-social-share-buttons
   ```

2. **Install dependencies:**

   ```bash
   composer install
   npm install
   ```

3. **Run tests:**

   ```bash
   composer test
   npm run test:e2e
   ```

## Development Setup

### Environment Configuration

1. **WordPress Test Environment:**

   ```bash
   # Install WordPress test suite
   bash build/bin/install-wp-tests.sh wordpress_test root '' localhost latest
   ```

2. **Local Development:**

   ```bash
   # Start local WordPress with Docker
   docker compose up -d

   # Or use LocalWP, MAMP, XAMPP, etc.
   ```

3. **VSCode Setup (Recommended):**

   Install extensions:
   - PHP IntelliSense
   - WordPress Snippets
   - PHPUnit Test Explorer

### Project Structure

```text
src/
├── Utils/              # Pure utility functions (MANDATORY for new utilities)
├── Admin/              # WordPress admin functionality
├── Integrations/       # Third-party integrations
├── ShareCounts/        # Share count management
├── Svg/               # SVG processing
└── Rest/              # REST API endpoints

tests/
├── unit/              # Unit tests (PHPUnit)
├── integration/       # Integration tests
└── e2e/              # End-to-end tests (Playwright)
```

## Code Standards

### PHP Coding Standards

We follow **PSR-12** with WordPress-specific adaptations:

#### 1. Class Structure

```php
<?php
declare(strict_types=1);

namespace HtmlSocialShare\Utils;

/**
 * Utility class for string operations
 *
 * All methods are pure functions with no side effects.
 *
 * @since 3.0.0
 */
class StringUtils
{
    /**
     * Truncates text to specified length
     *
     * @param string $text Text to truncate
     * @param int $length Maximum length
     * @param string $ellipsis Ellipsis string
     * @param bool $breakWords Whether to break words
     * @return string Truncated text
     *
     * @since 3.0.0
     */
    public static function truncate(
        string $text,
        int $length,
        string $ellipsis = '...',
        bool $breakWords = false
    ): string {
        if (empty($text)) {
            return '';
        }

        if (strlen($text) <= $length) {
            return $text;
        }

        // Implementation...
        return $result;
    }
}
```

#### 2. Function Requirements

**All new functions MUST:**

- Use strict type hints for parameters and return values
- Include complete PHPDoc documentation
- Follow single responsibility principle
- Be pure functions when possible (no side effects)
- Handle edge cases gracefully
- Return predictable types

#### 3. Naming Conventions

```php
// Classes: PascalCase
class StringUtils {}

// Methods: camelCase
public function sanitizeTextField() {}

// Constants: SCREAMING_SNAKE_CASE
const MAX_LENGTH = 100;

// Variables: camelCase
$cleanText = '';
```

### Security-First Development

#### Input Validation (MANDATORY)

```php
// ✅ ALWAYS validate input
public static function processData(string $input): string
{
    // 1. Check for empty/null
    if (empty($input)) {
        throw new InvalidArgumentException('Input cannot be empty');
    }

    // 2. Security checks
    if (SecurityUtils::hasXssPatterns($input)) {
        throw new SecurityException('XSS patterns detected');
    }

    // 3. Sanitize
    $input = SecurityUtils::sanitizeTextField($input);

    // 4. Process safely
    return $result;
}
```

#### Output Escaping (MANDATORY)

```php
// ✅ Always escape output
echo esc_html($userContent);
echo esc_url($userUrl);
echo esc_attr($userAttribute);

// ✅ For complex HTML, use sanitization
$safeHtml = wp_kses($userHtml, $allowedTags);
```

## Architecture Guidelines

### Pure Function Architecture

**New utility functions MUST be pure functions:**

```php
// ✅ Good: Pure function
public static function formatText(string $text): string
{
    return trim(strtolower($text));
}

// ❌ Bad: Has side effects
public static function formatAndSaveText(string $text): string
{
    $formatted = trim(strtolower($text));
    update_option('last_formatted', $formatted); // Side effect!
    return $formatted;
}
```

### Dependency Injection

Use dependency injection for better testability:

```php
// ✅ Good: Dependency injection
class ShareCounter
{
    private CacheInterface $cache;

    public function __construct(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
}

// ❌ Bad: Hard dependency
class ShareCounter
{
    public function getCount()
    {
        $cache = new Cache(); // Hard dependency
    }
}
```

### Error Handling

Use proper exception handling:

```php
// ✅ Specific exceptions
throw new InvalidArgumentException('Invalid URL format');
throw new SecurityException('XSS patterns detected');

// ✅ Try-catch with logging
try {
    $result = $this->processData($input);
} catch (SecurityException $e) {
    error_log('Security issue: ' . $e->getMessage());
    return null;
} catch (Exception $e) {
    error_log('Unexpected error: ' . $e->getMessage());
    throw $e;
}
```

## Testing Requirements

### Unit Testing (MANDATORY for new code)

Every new public method MUST have unit tests:

```php
<?php
declare(strict_types=1);

namespace HtmlSocialShare\Tests\Unit\Utils;

use PHPUnit\Framework\TestCase;
use HtmlSocialShare\Utils\StringUtils;

class StringUtilsTest extends TestCase
{
    /**
     * @dataProvider truncateProvider
     */
    public function testTruncate(string $input, int $length, string $expected): void
    {
        $result = StringUtils::truncate($input, $length);
        $this->assertEquals($expected, $result);
    }

    public function truncateProvider(): array
    {
        return [
            'short text' => ['Hello', 10, 'Hello'],
            'long text' => ['Hello World!', 5, 'He...'],
            'empty string' => ['', 10, ''],
            'exact length' => ['Hello', 5, 'Hello'],
        ];
    }

    public function testTruncateInvalidLength(): void
    {
        $this->expectException(InvalidArgumentException::class);
        StringUtils::truncate('Hello', -1);
    }
}
```

### Test Coverage Requirements

- **Minimum 80% code coverage** for new code
- **100% coverage** for utility functions
- **All edge cases** must be tested
- **Security functions** must test known attack vectors

### Running Tests

```bash
# Unit tests
composer test

# Unit tests with coverage
composer test:coverage

# Integration tests
composer test:integration

# E2E tests
npm run test:e2e

# All tests
composer test:all
```

## Security Standards

### Input Validation Checklist

For every user input, check:

- [ ] Empty/null validation
- [ ] XSS pattern detection
- [ ] SQL injection pattern detection
- [ ] File path traversal
- [ ] URL protocol validation
- [ ] Length limits
- [ ] Character set validation

### Security Testing

```php
class SecurityTest extends TestCase
{
    public function testXssDetection(): void
    {
        $xssPayloads = [
            '<script>alert("xss")</script>',
            'javascript:alert("xss")',
            '<img src="x" onerror="alert(\'xss\')">'
        ];

        foreach ($xssPayloads as $payload) {
            $this->assertTrue(SecurityUtils::hasXssPatterns($payload));
        }
    }

    public function testSqlInjectionDetection(): void
    {
        $sqlPayloads = [
            "'; DROP TABLE users; --",
            "1' OR '1'='1",
            "admin'/**/UNION/**/SELECT/**/*/**/FROM/**/users--"
        ];

        foreach ($sqlPayloads as $payload) {
            $this->assertTrue(SecurityUtils::hasSqlInjectionPatterns($payload));
        }
    }
}
```

## Pull Request Process

### Before Submitting

1. **Run all tests:**

   ```bash
   composer test:all
   npm run lint
   ```

2. **Check code standards:**

   ```bash
   composer phpcs
   composer phpstan
   ```

3. **Update documentation** if adding new features

4. **Add tests** for new functionality

### PR Template

Use this template for all pull requests:

```markdown
## Description
Brief description of changes made.

## Type of Change
- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Testing
- [ ] Unit tests pass
- [ ] Integration tests pass
- [ ] E2E tests pass
- [ ] Manual testing completed

## Security
- [ ] Input validation added/updated
- [ ] Output escaping verified
- [ ] Security tests added
- [ ] No sensitive data exposed

## Checklist
- [ ] Code follows project style guidelines
- [ ] Self-review completed
- [ ] Documentation updated
- [ ] Tests added/updated
- [ ] No breaking changes (or breaking changes documented)
```

### Review Process

1. **Automated checks** must pass
2. **Code review** by maintainer
3. **Testing** in staging environment
4. **Security review** for security-related changes

## Performance Guidelines

### Pure Functions (Preferred)

Write pure functions for better performance and cacheability:

```php
// ✅ Pure function - easily cacheable
public static function formatPrice(float $price, string $currency): string
{
    return $currency . number_format($price, 2);
}

// Cache usage
$cacheKey = 'price_' . md5($price . $currency);
$cached = wp_cache_get($cacheKey);
if ($cached) {
    return $cached;
}

$result = formatPrice($price, $currency);
wp_cache_set($cacheKey, $result, '', 3600);
return $result;
```

### Memory Management

Avoid memory leaks:

```php
// ✅ Good: No memory leaks
public function processLargeDataset(array $data): array
{
    $results = [];
    foreach ($data as $item) {
        $result = $this->processItem($item);
        $results[] = $result;
        unset($result); // Free memory
    }
    return $results;
}

// ✅ Better: Use generators for large datasets
public function processLargeDatasetGenerator(array $data): Generator
{
    foreach ($data as $item) {
        yield $this->processItem($item);
    }
}
```

### Database Optimization

```php
// ✅ Use prepared statements
$results = $wpdb->get_results($wpdb->prepare(
    "SELECT * FROM table WHERE id = %d AND status = %s",
    $id,
    $status
));

// ✅ Batch operations
wp_cache_set_multiple($cache_data);
```

## Common Patterns

### Adding a New Utility Function

1. **Add to appropriate Utils class:**

   ```php
   // src/Utils/StringUtils.php
   public static function newFunction(string $input): string
   {
       // Validate input
       if (empty($input)) {
           throw new InvalidArgumentException('Input cannot be empty');
       }

       // Sanitize if needed
       $input = SecurityUtils::sanitizeTextField($input);

       // Process
       $result = /* your logic */;

       return $result;
   }
   ```

2. **Add comprehensive tests:**

   ```php
   // tests/unit/Utils/StringUtilsTest.php
   public function testNewFunction(): void
   {
       // Test cases...
   }
   ```

3. **Update documentation:**

   - Add to API documentation
   - Update CHANGELOG.md

### Adding a New Social Network

1. **Add to Networks.php:**

   ```php
   'newnetwork' => [
       'label' => 'New Network',
       'url_template' => 'https://newnetwork.com/share?url={{url}}&title={{title}}',
       'icon' => 'newnetwork'
   ]
   ```

2. **Add icon to IconRegistry**
3. **Add tests**
4. **Update documentation**

## Release Process

### Version Numbers

Follow [Semantic Versioning](https://semver.org/):

- **MAJOR** version for incompatible API changes
- **MINOR** version for backwards-compatible functionality
- **PATCH** version for backwards-compatible bug fixes

### Release Checklist

- [ ] All tests pass
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] Version numbers updated
- [ ] Security review completed
- [ ] Performance testing completed

## Getting Help

### Resources

- [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [PSR-12 Coding Standard](https://www.php-fig.org/psr/psr-12/)

### Support Channels

- GitHub Issues for bugs and feature requests
- GitHub Discussions for questions
- Code review requests via pull requests

## License

By contributing to this project, you agree that your contributions will be licensed under the GPL v2 or later.

---

**Last Updated:** September 29, 2025
**Version:** 3.0.0