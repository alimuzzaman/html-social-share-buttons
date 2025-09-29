# HTML Social Share Buttons - Developer API Documentation

## Overview

This document provides comprehensive API documentation for the HTML Social Share Buttons plugin after the systematic PHP refactoring. The plugin now features a modern, secure, and highly maintainable architecture with comprehensive pure function utilities.

## Architecture Overview

### Core Principles

1. **Pure Function Architecture**: Separation of pure functions from side effects
2. **Dependency Injection**: Container-based architecture for loose coupling
3. **Security-First Design**: Comprehensive input validation and sanitization
4. **Performance Optimized**: Multi-level caching and memory efficiency
5. **Backward Compatibility**: All existing APIs preserved

### Directory Structure

```
src/
├── Utils/              # Pure utility functions
│   ├── SecurityUtils.php    # Security and sanitization
│   ├── StringUtils.php     # Text processing
│   ├── UrlUtils.php        # URL manipulation
│   ├── ArrayUtils.php      # Array operations
│   └── DataUtils.php       # Data validation
├── Admin/              # Admin interface components
├── Integrations/       # Third-party integrations
├── ShareCounts/        # Share count management
├── Svg/               # SVG processing and sanitization
└── Rest/              # REST API endpoints
```

## Utility Classes API Reference

### SecurityUtils

Pure functions for security operations, input validation, and sanitization.

#### Methods

##### `sanitizeTextField(string $value): string`

Sanitizes text field input by removing dangerous characters and normalizing whitespace.

```php
use HtmlSocialShare\Utils\SecurityUtils;

$clean = SecurityUtils::sanitizeTextField('Hello <script>alert("xss")</script>World');
// Returns: "Hello alert("xss")World"
```

**Parameters:**
- `$value` (string): Raw input value

**Returns:** Sanitized string with HTML tags removed and whitespace normalized

##### `sanitizeKey(string $key): string`

Sanitizes key/slug input to contain only safe characters.

```php
$safeKey = SecurityUtils::sanitizeKey('Hello-World@#$');
// Returns: "hello-world"
```

**Parameters:**
- `$key` (string): Raw key value

**Returns:** Lowercase alphanumeric string with underscores and dashes only

##### `hasXssPatterns(string $input): bool`

Detects potential XSS attack patterns in input.

```php
$isXss = SecurityUtils::hasXssPatterns('<script>alert("xss")</script>');
// Returns: true
```

**Parameters:**
- `$input` (string): Input to check

**Returns:** True if XSS patterns detected

##### `hasSqlInjectionPatterns(string $input): bool`

Detects potential SQL injection patterns in input.

```php
$isSqlInjection = SecurityUtils::hasSqlInjectionPatterns("'; DROP TABLE users; --");
// Returns: true
```

**Parameters:**
- `$input` (string): Input to check

**Returns:** True if SQL injection patterns detected

##### `isValidEmail(string $email): bool`

Validates email address format.

```php
$isValid = SecurityUtils::isValidEmail('user@example.com');
// Returns: true
```

##### `sanitizeUrl(string $url): string`

Sanitizes and validates URLs, blocking dangerous protocols.

```php
$cleanUrl = SecurityUtils::sanitizeUrl('javascript:alert("xss")');
// Returns: "" (blocked)

$cleanUrl = SecurityUtils::sanitizeUrl('https://example.com');
// Returns: "https://example.com"
```

##### `checkRateLimit(array $attempts, int $maxAttempts, int $timeWindow, int $currentTime): array`

Implements rate limiting logic.

```php
$attempts = [time() - 30, time() - 20];
$result = SecurityUtils::checkRateLimit($attempts, 3, 60, time());
// Returns: ['exceeded' => false, 'attempts' => [...], 'remaining' => 1]
```

**Parameters:**
- `$attempts` (array): Array of previous attempt timestamps
- `$maxAttempts` (int): Maximum allowed attempts
- `$timeWindow` (int): Time window in seconds
- `$currentTime` (int): Current timestamp

**Returns:** Array with rate limit status and updated attempts

### StringUtils

Pure functions for text processing and manipulation.

#### Methods

##### `truncate(string $text, int $length, string $ellipsis = '...', bool $breakWords = false): string`

Truncates text to specified length with ellipsis.

```php
use HtmlSocialShare\Utils\StringUtils;

$short = StringUtils::truncate('This is a long text', 10);
// Returns: "This is..."
```

**Parameters:**
- `$text` (string): Text to truncate
- `$length` (int): Maximum length
- `$ellipsis` (string): Ellipsis string (default: '...')
- `$breakWords` (bool): Whether to break words (default: false)

##### `toSlug(string $text, string $separator = '-'): string`

Converts text to URL-friendly slug format.

```php
$slug = StringUtils::toSlug('Hello World!');
// Returns: "hello-world"
```

##### `parseTemplate(string $template, array $variables, string $prefix = '{{', string $suffix = '}}'): string`

Parses template with variable substitution.

```php
$result = StringUtils::parseTemplate('Hello {{name}}!', ['name' => 'John']);
// Returns: "Hello John!"
```

##### `wordCount(string $text): int`

Counts words in text.

```php
$count = StringUtils::wordCount('Hello world');
// Returns: 2
```

##### `cleanText(string $text, bool $preserveLineBreaks = false): string`

Cleans text by removing control characters and normalizing whitespace.

```php
$clean = StringUtils::cleanText("  Hello\r\n\tWorld  ");
// Returns: "Hello World"
```

### UrlUtils

Pure functions for URL processing and manipulation.

#### Methods

##### `buildShareUrl(string $template, array $params): string`

Builds share URLs from templates with parameter substitution.

```php
use HtmlSocialShare\Utils\UrlUtils;

$url = UrlUtils::buildShareUrl(
    'https://example.com/share?url={{url}}&title={{title}}',
    ['url' => 'https://test.com', 'title' => 'Test']
);
// Returns: "https://example.com/share?url=https%3A%2F%2Ftest.com&title=Test"
```

**Parameters:**
- `$template` (string): URL template with {{variable}} placeholders
- `$params` (array): Parameters to substitute

**Returns:** Built URL with URL-encoded parameters

##### `extractDomain(string $url): string`

Extracts domain from URL.

```php
$domain = UrlUtils::extractDomain('https://example.com/path');
// Returns: "example.com"
```

##### `isValidUrl(string $url): bool`

Validates URL format (HTTP/HTTPS only).

```php
$isValid = UrlUtils::isValidUrl('https://example.com');
// Returns: true
```

##### `addQueryParams(string $url, array $params): string`

Adds query parameters to URL.

```php
$newUrl = UrlUtils::addQueryParams('https://example.com', ['param' => 'value']);
// Returns: "https://example.com?param=value"
```

##### `normalizeUrl(string $url): string`

Normalizes URL format (lowercase domain, remove default ports, etc.).

```php
$normalized = UrlUtils::normalizeUrl('https://EXAMPLE.COM:443/path//');
// Returns: "https://example.com/path"
```

## Core Classes API Reference

### Networks

Manages available social networks and their configurations.

#### Methods

##### `getAvailableNetworks(): array`

Returns all available social networks with their configurations.

```php
use HtmlSocialShare\Networks;

$networks = Networks::getAvailableNetworks();
// Returns array of network configurations
```

**Returns:** Array where keys are network IDs and values contain:
- `label` (string): Display name
- `url_template` (string): Share URL template
- `icon` (string): Icon identifier

### IconRegistry

Manages icons and iconsets with security features.

#### Methods

##### `hasIcon(string $key): bool`

Checks if an icon exists.

```php
use HtmlSocialShare\IconRegistry;

$registry = new IconRegistry($settings);
$exists = $registry->hasIcon('facebook');
// Returns: true
```

##### `getIcon(string $key): ?string`

Gets icon HTML/SVG content.

```php
$icon = $registry->getIcon('facebook');
// Returns: SVG or HTML content for the icon
```

##### `registerIcon(string $key, string $svg): void`

Registers a new icon with automatic sanitization.

```php
$registry->registerIcon('custom', '<svg>...</svg>');
```

### Settings

Manages plugin settings with validation and caching.

#### Methods

##### `get(string $key, mixed $default = null): mixed`

Gets a setting value with optional default.

```php
use HtmlSocialShare\Settings;

$settings = new Settings();
$value = $settings->get('enabled_networks', []);
```

##### `set(string $key, mixed $value): void`

Sets a setting value with validation.

```php
$settings->set('enabled_networks', ['facebook', 'twitter']);
```

## Security Guidelines

### Input Validation

Always validate and sanitize user input:

```php
// For text fields
$clean = SecurityUtils::sanitizeTextField($userInput);

// For URLs
$cleanUrl = SecurityUtils::sanitizeUrl($userUrl);
if (empty($cleanUrl)) {
    // URL was invalid or dangerous
}

// For keys/slugs
$safeKey = SecurityUtils::sanitizeKey($userKey);
```

### XSS Prevention

```php
// Check for XSS patterns
if (SecurityUtils::hasXssPatterns($input)) {
    // Block or sanitize the input
    return false;
}

// Escape output
$safeOutput = SecurityUtils::escapeHtml($content);
```

### SQL Injection Prevention

```php
// Check for SQL injection patterns
if (SecurityUtils::hasSqlInjectionPatterns($input)) {
    // Block the input
    return false;
}

// Use prepared statements for database operations
```

## Performance Best Practices

### Pure Functions

All utility functions are pure (no side effects):

```php
// Good: Pure function, same input = same output
$result = StringUtils::truncate($text, 100);

// Good: No global state modification
$slug = StringUtils::toSlug($title);
```

### Memory Management

Functions are designed for efficient memory usage:

```php
// Memory efficient - no leaks
for ($i = 0; $i < 1000; $i++) {
    $clean = StringUtils::cleanText($input);
    // $clean is automatically freed
}
```

### Caching

Use caching for expensive operations:

```php
// Cache expensive operations
$cacheKey = 'processed_' . md5($input);
if ($cached = $cache->get($cacheKey)) {
    return $cached;
}

$result = expensiveOperation($input);
$cache->set($cacheKey, $result, 3600);
return $result;
```

## Testing

### Unit Testing

Test pure functions with predictable inputs/outputs:

```php
class SecurityUtilsTest extends TestCase
{
    public function testSanitizeTextField()
    {
        $result = SecurityUtils::sanitizeTextField('Hello <script>alert("xss")</script>World');
        $this->assertEquals('Hello alert("xss")World', $result);
    }

    public function testHasXssPatterns()
    {
        $this->assertTrue(SecurityUtils::hasXssPatterns('<script>alert("xss")</script>'));
        $this->assertFalse(SecurityUtils::hasXssPatterns('Hello World'));
    }
}
```

### Security Testing

Validate security functions with known attack vectors:

```php
// Test XSS detection
$xssPayloads = [
    '<script>alert("xss")</script>',
    'javascript:alert("xss")',
    '<img src="x" onerror="alert(\'xss\')">'
];

foreach ($xssPayloads as $payload) {
    $this->assertTrue(SecurityUtils::hasXssPatterns($payload));
}
```

## Error Handling

### Exception Handling

All functions use proper error handling:

```php
try {
    $result = SomeClass::processData($input);
} catch (InvalidArgumentException $e) {
    // Handle invalid input
    error_log('Invalid input: ' . $e->getMessage());
    return false;
} catch (Exception $e) {
    // Handle unexpected errors
    error_log('Unexpected error: ' . $e->getMessage());
    return null;
}
```

### Validation Errors

Functions return safe defaults or empty values for invalid input:

```php
$cleanUrl = SecurityUtils::sanitizeUrl($invalidUrl);
// Returns empty string for invalid URLs

$domain = UrlUtils::extractDomain($invalidUrl);
// Returns empty string for invalid URLs
```

## Migration Guide

### From Legacy Code

The refactored code maintains backward compatibility:

```php
// Old way (still works)
$settings = get_option('html_social_share_settings');

// New way (recommended)
$settings = new Settings();
$value = $settings->get('some_key');
```

### New Features

Take advantage of new utility functions:

```php
// Enhanced security
$safe = SecurityUtils::sanitizeTextField($input);

// Better text processing
$slug = StringUtils::toSlug($title);

// URL utilities
$shareUrl = UrlUtils::buildShareUrl($template, $params);
```

## Contributing

### Code Standards

1. **Pure Functions**: Prefer pure functions with no side effects
2. **Type Hints**: Use strict typing for all parameters and returns
3. **Documentation**: Complete PHPDoc for all public methods
4. **Security**: Always validate and sanitize input
5. **Testing**: Write comprehensive unit tests

### Example Contribution

```php
<?php
namespace HtmlSocialShare\Utils;

/**
 * New utility function
 *
 * @param string $input Input to process
 * @return string Processed output
 * @throws InvalidArgumentException If input is invalid
 */
class NewUtils
{
    /**
     * Process input safely
     *
     * @param string $input Input to process
     * @return string Processed result
     */
    public static function processInput(string $input): string
    {
        // Validate input
        if (empty($input)) {
            throw new InvalidArgumentException('Input cannot be empty');
        }

        // Sanitize
        $input = SecurityUtils::sanitizeTextField($input);

        // Process
        $result = strtolower(trim($input));

        return $result;
    }
}
```

## Changelog

### Version 3.0.0 - Systematic Refactoring

#### Added
- Pure function architecture with 2000+ lines of utility functions
- Comprehensive security enhancements (XSS, SQL injection protection)
- New social networks: Mastodon, Bluesky, Threads, VK, WeChat
- Enhanced caching with TTL management
- Memory optimization and leak prevention
- Rate limiting functionality
- WeChat QR code generation

#### Security Enhancements
- All user input sanitized and validated
- Output properly escaped
- CSRF protection on all forms
- File upload security validation
- Dangerous URL protocol blocking

#### Performance Improvements
- Multi-level caching implementation
- Database query optimization
- Memory usage optimization
- Pure function architecture for better performance

#### Backward Compatibility
- All existing public APIs preserved
- Legacy function wrappers provided
- Automatic settings migration
- Smooth upgrade path

---

**Last Updated:** September 29, 2025
**Version:** 3.0.0
**Authors:** HTML Social Share Buttons Development Team