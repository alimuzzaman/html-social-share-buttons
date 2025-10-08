# Pure Function Utilities Documentation

## Overview

This documentation covers the pure function utility classes implemented to improve code testability, maintainability, and performance. These classes contain only pure functions with no side effects, no dependencies on WordPress globals, and predictable, deterministic behavior.

## Architecture Benefits

### Why Pure Functions?

1. **Testability**: Easy to test without mocking WordPress functions
2. **Performance**: No side effects or external dependencies
3. **Reusability**: Can be used across different contexts
4. **Reliability**: Predictable output for given input
5. **Maintainability**: Easier to debug and refactor

### SOLID Principles Compliance

- **Single Responsibility**: Each class has one focused purpose
- **Open/Closed**: Functions can be extended without modification
- **Dependency Inversion**: No dependencies on concrete implementations

## Utility Classes

### 1. RenderUtils (`src/Renderers/RenderUtils.php`)

Pure functions for rendering operations, HTML generation, and content formatting.

#### Key Functions

```php
// Format share counts for display
RenderUtils::formatCount(1500); // Returns "1.5K"
RenderUtils::formatCount(2500000); // Returns "2.5M"

// Generate share URLs from templates
$url = RenderUtils::generateShareUrl(
    'https://facebook.com/sharer.php?u={encoded_url}',
    'https://example.com/post',
    'My Post Title'
);

// Sanitize content and attributes
$safe = RenderUtils::sanitizeAttribute('<script>alert("xss")</script>');
$content = RenderUtils::sanitizeContent('Safe content with "quotes"');

// Build HTML attributes
$attrs = RenderUtils::buildAttributes([
    'class' => 'btn btn-primary',
    'id' => 'share-button',
    'disabled' => true,
    'hidden' => false // Will be omitted
]);

// Generate accessibility attributes
$a11y = RenderUtils::generateA11yAttributes('facebook', '@username');
// Returns: ['aria-label' => 'Share on Facebook with @username', ...]

// Generate CSS classes
$classes = RenderUtils::generateButtonClasses(
    'twitter', 
    ['large', 'primary'], 
    true // selected
);
// Returns: "hssb-share hssb-twitter hssb-large hssb-primary hssb-selected"
```

#### Validation Functions

```php
// Validate URL templates
RenderUtils::isValidUrlTemplate('https://example.com/share?url={url}'); // true
RenderUtils::isValidUrlTemplate('invalid-template'); // false

// Extract domain from URL
RenderUtils::extractDomain('https://facebook.com/page'); // "facebook.com"

// Generate unique IDs
RenderUtils::generateUniqueId('button', 'facebook'); // "button-60f7b2c4-facebook"

// Parse network strings
RenderUtils::parseNetworkString('facebook|square');
// Returns: ['network' => 'facebook', 'iconset' => 'square']
```

### 2. DataUtils (`src/Utils/DataUtils.php`)

Pure functions for data validation, sanitization, and configuration processing.

#### Validation Functions

```php
// Validate network configuration
$result = DataUtils::validateNetworkConfig([
    'network' => 'facebook',
    'label' => 'Facebook',
    'url_template' => 'https://facebook.com/sharer.php?u={url}',
    'color' => '#1877f2'
]);
// Returns: ['valid' => true, 'errors' => []]

// Validate profile configuration
$result = DataUtils::validateProfileConfig([
    'network' => 'twitter',
    'handle' => '@username',
    'url' => 'https://twitter.com/username'
]);

// Individual validation functions
DataUtils::isValidNetworkName('facebook_2'); // true
DataUtils::isValidNetworkName('Facebook 2'); // false

DataUtils::isValidHexColor('#ff0000'); // true
DataUtils::isValidHexColor('red'); // false

DataUtils::isValidHandle('twitter', '@username'); // true
DataUtils::isValidHandle('twitter', 'toolongusernameexceeding15chars'); // false
```

#### Sanitization Functions

```php
// Sanitize network configuration
$clean = DataUtils::sanitizeNetworkConfig([
    'network' => 'Face Book',
    'label' => '<script>Facebook</script>',
    'color' => 'ff0000',
    'enabled' => 'yes',
    'order' => '5.7'
]);
// Returns clean, properly typed data

// Network-specific handle sanitization
DataUtils::sanitizeHandle('twitter', '@user_name_123'); // "user_name_123"
DataUtils::sanitizeHandle('instagram', '@user.name'); // "user.name"
DataUtils::sanitizeHandle('linkedin', 'user-profile'); // "user-profile"

// Safe data type conversion
DataUtils::sanitizeBoolean('yes'); // true
DataUtils::sanitizeBoolean('0'); // false
DataUtils::sanitizeInteger('42.7'); // 42
```

#### Configuration Management

```php
// Merge configurations with priority
$merged = DataUtils::mergeNetworkConfigs($baseConfig, $overrideConfig);

// Filter by allowed keys
$filtered = DataUtils::filterByAllowedKeys($data, ['network', 'label', 'enabled']);

// Convert between formats
DataUtils::toArray('facebook,twitter,linkedin'); // ['facebook', 'twitter', 'linkedin']
DataUtils::toString(['facebook', 'twitter']); // "facebook, twitter"
```

### 3. ArrayUtils (`src/Utils/ArrayUtils.php`)

Pure functions for advanced array manipulation and configuration processing.

#### Deep Array Operations

```php
// Deep merge arrays
$merged = ArrayUtils::deepMerge(
    ['config' => ['enabled' => true]],
    ['config' => ['networks' => ['facebook']]]
);
// Result: ['config' => ['enabled' => true, 'networks' => ['facebook']]]

// Dot notation access
$value = ArrayUtils::get($data, 'settings.networks.facebook.enabled', false);
$newData = ArrayUtils::set($data, 'settings.networks.twitter.enabled', true);
$exists = ArrayUtils::has($data, 'settings.cache.ttl');
$removed = ArrayUtils::unset($data, 'settings.debug');
```

#### Functional Array Operations

```php
// Filter with predicate
$active = ArrayUtils::filter($networks, function($network) {
    return $network['enabled'] === true;
});

// Map transformation
$labels = ArrayUtils::map($networks, function($network) {
    return $network['label'];
});

// Reduce to single value
$total = ArrayUtils::reduce($numbers, function($sum, $num) {
    return $sum + $num;
}, 0);

// Group by field
$grouped = ArrayUtils::groupBy($networks, 'category');
// or with callback
$grouped = ArrayUtils::groupBy($networks, function($network) {
    return $network['enabled'] ? 'active' : 'inactive';
});
```

#### Sorting and Organization

```php
// Sort by field or callback
$sorted = ArrayUtils::sortBy($networks, 'order'); // ascending
$sorted = ArrayUtils::sortBy($networks, 'label', 'desc'); // descending
$sorted = ArrayUtils::sortBy($networks, function($network) {
    return $network['priority'];
});

// Pluck values from nested arrays
$ids = ArrayUtils::pluck($networks, 'id');
$byKey = ArrayUtils::pluck($networks, 'label', 'id'); // Use 'id' as key
```

#### Array Utilities

```php
// Flatten multi-dimensional arrays
$flat = ArrayUtils::flatten($nested); // Unlimited depth
$flat = ArrayUtils::flatten($nested, 2); // Max 2 levels

// Remove empty values
$clean = ArrayUtils::removeEmpty($data); // Non-recursive
$clean = ArrayUtils::removeEmpty($data, true); // Recursive

// Array information
ArrayUtils::first($array, 'default'); // First value or default
ArrayUtils::last($array, 'default'); // Last value or default
ArrayUtils::random($array, 'default'); // Random value or default
ArrayUtils::isAssociative($array); // Check if associative

// Key operations
$renamed = ArrayUtils::renameKey($array, 'oldKey', 'newKey');
$intersection = ArrayUtils::intersectByKey($array1, $array2);
$difference = ArrayUtils::diffByKey($array1, $array2);

// Value wrapping and conversion
$wrapped = ArrayUtils::wrap($value); // Ensures array
$query = ArrayUtils::toQueryString($array); // Convert to query string
```

## Usage Examples

### Example 1: Processing Network Configuration

```php
use HtmlSocialShare\Utils\DataUtils;
use HtmlSocialShare\Utils\ArrayUtils;

// Raw configuration from user input
$rawConfig = [
    'network' => 'Face Book',
    'label' => '<strong>Facebook</strong>',
    'enabled' => 'yes',
    'color' => 'blue',
    'handles' => 'page1,page2,page3'
];

// Validate first
$validation = DataUtils::validateNetworkConfig($rawConfig);
if (!$validation['valid']) {
    // Handle validation errors
    foreach ($validation['errors'] as $error) {
        error_log("Validation error: $error");
    }
}

// Sanitize the data
$cleanConfig = DataUtils::sanitizeNetworkConfig($rawConfig);

// Process handles array
$cleanConfig['handles'] = DataUtils::toArray($cleanConfig['handles']);

// Filter to allowed keys only
$allowedKeys = ['network', 'label', 'enabled', 'color', 'handles'];
$finalConfig = ArrayUtils::filterByAllowedKeys($cleanConfig, $allowedKeys);
```

### Example 2: Building Share Button HTML

```php
use HtmlSocialShare\Renderers\RenderUtils;

// Configuration
$network = 'facebook';
$url = 'https://example.com/my-post';
$title = 'My Awesome Post';
$handle = '@mypage';
$shareCount = 1250;

// Generate share URL
$template = 'https://facebook.com/sharer.php?u={encoded_url}&t={encoded_title}';
$shareUrl = RenderUtils::generateShareUrl($template, $url, $title);

// Generate CSS classes
$classes = RenderUtils::generateButtonClasses($network, ['large', 'rounded']);

// Generate accessibility attributes
$a11yAttrs = RenderUtils::generateA11yAttributes($network, $handle);

// Build all attributes
$allAttrs = array_merge([
    'class' => $classes,
    'href' => $shareUrl
], $a11yAttrs);

$attrString = RenderUtils::buildAttributes($allAttrs);

// Format share count
$formattedCount = RenderUtils::formatCount($shareCount); // "1.3K"

// Generate screen reader text
$srText = RenderUtils::generateShareCountScreenReaderText($shareCount, $network);

echo "<a $attrString>Share on Facebook ($formattedCount) <span class='sr-only'>$srText</span></a>";
```

### Example 3: Complex Configuration Processing

```php
use HtmlSocialShare\Utils\ArrayUtils;

// Complex nested configuration
$config = [
    'networks' => [
        'facebook' => ['enabled' => true, 'order' => 1],
        'twitter' => ['enabled' => false, 'order' => 2],
        'linkedin' => ['enabled' => true, 'order' => 3]
    ],
    'settings' => [
        'cache' => ['enabled' => true, 'ttl' => 3600],
        'style' => 'modern'
    ]
];

// Get specific values with dot notation
$cacheEnabled = ArrayUtils::get($config, 'settings.cache.enabled', false);
$twitterOrder = ArrayUtils::get($config, 'networks.twitter.order', 99);

// Filter enabled networks only
$enabledNetworks = ArrayUtils::filter(
    $config['networks'], 
    function($network) {
        return $network['enabled'] === true;
    }
);

// Sort networks by order
$sortedNetworks = ArrayUtils::sortBy($enabledNetworks, 'order');

// Get just the network names
$networkNames = array_keys($sortedNetworks);

// Group networks by enabled status
$groupedNetworks = ArrayUtils::groupBy($config['networks'], function($network) {
    return $network['enabled'] ? 'enabled' : 'disabled';
});
```

## Testing Pure Functions

Pure functions are extremely easy to test because they have no dependencies and no side effects:

```php
class RenderUtilsTest extends TestCase
{
    public function testFormatCount()
    {
        $this->assertEquals('42', RenderUtils::formatCount(42));
        $this->assertEquals('1K', RenderUtils::formatCount(1000));
        $this->assertEquals('1.5K', RenderUtils::formatCount(1500));
        $this->assertEquals('2.3M', RenderUtils::formatCount(2300000));
    }

    public function testGenerateShareUrl()
    {
        $template = 'https://example.com/share?url={encoded_url}&title={encoded_title}';
        $result = RenderUtils::generateShareUrl($template, 'https://test.com', 'Test Title');
        
        $this->assertStringContains('url=' . urlencode('https://test.com'), $result);
        $this->assertStringContains('title=' . urlencode('Test Title'), $result);
    }

    public function testSanitizeAttribute()
    {
        $this->assertEquals('&lt;script&gt;', RenderUtils::sanitizeAttribute('<script>'));
        $this->assertEquals('&quot;test&quot;', RenderUtils::sanitizeAttribute('"test"'));
    }
}
```

## Performance Characteristics

### Benchmarks

Pure functions generally perform better than their WordPress-dependent counterparts:

- **No function_exists() checks**: Direct execution
- **No global variable access**: Local scope only  
- **No database queries**: All data passed as parameters
- **No file system access**: In-memory operations only
- **Predictable execution time**: No external dependencies

### Memory Usage

Pure functions typically use less memory because:

- No global state retention
- No persistent connections
- Local variable scope only
- Garbage collection friendly

## Migration from WordPress Functions

### Before (WordPress-dependent)
```php
function generate_share_url($network, $url, $title) {
    $template = get_option("hss_{$network}_template");
    if (empty($template)) {
        $template = get_default_template($network);
    }
    
    $share_url = str_replace(
        ['{url}', '{title}'],
        [urlencode($url), urlencode($title)],
        $template
    );
    
    return apply_filters('hss_share_url', $share_url, $network);
}
```

### After (Pure function)
```php
// Pure function - no WordPress dependencies
$template = $this->getTemplate($network); // Injected dependency
$shareUrl = RenderUtils::generateShareUrl($template, $url, $title);

// Apply filters separately if needed
if (function_exists('apply_filters')) {
    $shareUrl = apply_filters('hss_share_url', $shareUrl, $network);
}
```

## Best Practices

### 1. Function Design
- Keep functions small and focused
- Use descriptive parameter and return type names
- Include comprehensive parameter validation
- Document expected input/output formats

### 2. Error Handling
- Return error objects instead of throwing exceptions
- Provide meaningful error messages
- Use default values for optional parameters
- Validate all inputs thoroughly

### 3. Testing
- Test with various input combinations
- Include edge cases (empty, null, invalid data)
- Test performance with large datasets
- Verify pure function properties (same input = same output)

### 4. Documentation
- Document all parameters and return values
- Include usage examples
- Explain any complex logic or algorithms
- Note any performance considerations

## Conclusion

The pure function utilities provide a solid foundation for building maintainable, testable, and performant WordPress plugins. By separating pure logic from WordPress-specific functionality, we achieve:

- **Better code quality** through easier testing
- **Improved performance** through reduced dependencies
- **Enhanced maintainability** through predictable behavior
- **Greater reusability** across different contexts

These utilities can be used independently or integrated with WordPress-specific code as needed, providing maximum flexibility for developers.

---

**Last Updated:** December 2024  
**Utility Version:** 3.0.0  
**Pure Functions Count:** 60+