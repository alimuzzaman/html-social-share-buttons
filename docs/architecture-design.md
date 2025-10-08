# Phase 1 Architecture Design

## Overview

This document defines the PSR-4 class architecture for the HTML Social Share Buttons plugin rewrite.

## Namespace

Root namespace: `HtmlSocialShare\`

## Directory Structure

```
src/
├── Core/
│   └── Plugin.php                 # Main plugin bootstrap
├── IconSystem/
│   ├── IconRegistry.php           # Unified iconset registry
│   └── IconMetadata.php           # Icon data structure
├── Renderers/
│   ├── ButtonRenderer.php         # Main HTML rendering
│   └── CssGenerator.php           # CSS generation
├── Options/
│   └── OptionsManager.php         # Options handling
├── Services/
│   ├── UrlBuilder.php             # Share URL generation
│   └── PlacementManager.php       # Placement logic
├── Build/
│   └── IconsetBuilder.php         # CSS build system
├── Compatibility/
│   └── LegacyFunctions.php        # Backward compatibility
├── Admin/
│   ├── SettingsPage.php           # Settings UI
│   └── Widget.php                 # Widget class
└── Frontend/
    └── Shortcode.php              # Shortcode handler
```

## Core Classes

### 1. Plugin (Bootstrap)

**File:** `src/Core/Plugin.php`
**Namespace:** `HtmlSocialShare\Core`

**Responsibilities:**
- Initialize all services
- Register WordPress hooks
- Set up dependency injection
- Load autoloader
- Activate/deactivate hooks

**Dependencies:** All other classes

**Public API:**
```php
class Plugin {
    public static function getInstance(): Plugin
    public function init(): void
    public function activate(): void
    public function deactivate(): void
}
```

### 2. IconRegistry

**File:** `src/IconSystem/IconRegistry.php`
**Namespace:** `HtmlSocialShare\IconSystem`

**Responsibilities:**
- Load iconset metadata
- Provide icon data for rendering
- Validate iconset exists
- Cache icon data

**Dependencies:** None (pure data)

**Public API:**
```php
class IconRegistry {
    public function getIconset(string $iconsetId, string $type): ?Iconset
    public function getIcon(string $iconsetId, string $type, string $network): ?Icon
    public function getAvailableIconsets(): array
    public function iconsetExists(string $iconsetId): bool
}
```

### 3. ButtonRenderer

**File:** `src/Renderers/ButtonRenderer.php`
**Namespace:** `HtmlSocialShare\Renderers`

**Responsibilities:**
- Generate HTML for buttons
- Build wrapper div with correct classes
- Generate anchor tags
- Apply attributes (target, nofollow, etc.)
- Coordinate with UrlBuilder and IconRegistry

**Dependencies:**
- IconRegistry
- UrlBuilder
- OptionsManager

**Public API:**
```php
class ButtonRenderer {
    public function __construct(
        IconRegistry $iconRegistry,
        UrlBuilder $urlBuilder,
        OptionsManager $optionsManager
    )
    
    public function render(array $options): string
    public function renderIcon(string $network, array $options): string
}
```

### 4. CssGenerator

**File:** `src/Renderers/CssGenerator.php`
**Namespace:** `HtmlSocialShare\Renderers`

**Responsibilities:**
- Generate CSS for icons
- Generate positioning CSS
- Handle auto-hide logic
- Output <style> tags

**Dependencies:**
- IconRegistry
- OptionsManager

**Public API:**
```php
class CssGenerator {
    public function generateIconCss(string $iconsetId, string $type): string
    public function generatePositioningCss(array $options): string
    public function output(): void
}
```

### 5. UrlBuilder

**File:** `src/Services/UrlBuilder.php`
**Namespace:** `HtmlSocialShare\Services`

**Responsibilities:**
- Build share URLs for networks
- Replace placeholders (%%permalink%%, %%title%%)
- URL encoding
- Apply filters

**Dependencies:**
- IconRegistry (for URL templates)

**Public API:**
```php
class UrlBuilder {
    public function buildUrl(string $network, array $context): string
    public function replacePlaceholders(string $template, array $context): string
}
```

### 6. OptionsManager

**File:** `src/Options/OptionsManager.php`
**Namespace:** `HtmlSocialShare\Options`

**Responsibilities:**
- Load options from WordPress
- Provide defaults
- Sanitize values
- Cache in memory

**Dependencies:** None

**Public API:**
```php
class OptionsManager {
    public function get(string $key, $default = null)
    public function getAll(): array
    public function update(array $options): bool
    public function sanitize(array $options): array
}
```

### 7. PlacementManager

**File:** `src/Services/PlacementManager.php`
**Namespace:** `HtmlSocialShare\Services`

**Responsibilities:**
- Determine placement for buttons
- Render in correct location
- Apply correct class based on placement
- Handle show_in configuration

**Dependencies:**
- ButtonRenderer
- OptionsManager

**Public API:**
```php
class PlacementManager {
    public function renderLeft(): string
    public function renderRight(): string
    public function renderBeforePost(): string
    public function renderAfterPost(): string
    public function shouldRenderPlacement(string $placement): bool
}
```

### 8. IconsetBuilder

**File:** `src/Build/IconsetBuilder.php`
**Namespace:** `HtmlSocialShare\Build`

**Responsibilities:**
- Scan assets/iconset/ directories
- Generate CSS for each iconset
- Write to build/iconsets/
- Create metadata files

**Dependencies:** None (build-time only)

**Public API:**
```php
class IconsetBuilder {
    public function buildAll(): array
    public function buildIconset(string $iconsetId, string $type): bool
    public function generateCss(string $iconsetId, string $type): string
}
```

### 9. LegacyFunctions

**File:** `src/Compatibility/LegacyFunctions.php`
**Namespace:** `HtmlSocialShare\Compatibility`

**Responsibilities:**
- Provide backward compatibility
- Wrap new API for old function calls
- Maintain zm_sh_btn() global function

**Dependencies:**
- Plugin (for service access)

**Public API:**
```php
class LegacyFunctions {
    public static function zm_sh_btn(array $atts): string
}
```

### 10. Shortcode

**File:** `src/Frontend/Shortcode.php`
**Namespace:** `HtmlSocialShare\Frontend`

**Responsibilities:**
- Register [zm_sh_btn] shortcode
- Parse attributes
- Sanitize inputs
- Call ButtonRenderer

**Dependencies:**
- ButtonRenderer
- OptionsManager

**Public API:**
```php
class Shortcode {
    public function register(): void
    public function handle(array $atts): string
}
```

### 11. Widget

**File:** `src/Admin/Widget.php`
**Namespace:** `HtmlSocialShare\Admin`

**Responsibilities:**
- Extend WP_Widget
- Render widget form
- Handle widget update
- Output buttons in widget

**Dependencies:**
- ButtonRenderer

**Public API:**
```php
class Widget extends \WP_Widget {
    public function widget($args, $instance): void
    public function form($instance): void
    public function update($new_instance, $old_instance): array
}
```

### 12. SettingsPage

**File:** `src/Admin/SettingsPage.php`
**Namespace:** `HtmlSocialShare\Admin`

**Responsibilities:**
- Register settings page
- Render form fields
- Handle save
- Use WordPress Settings API

**Dependencies:**
- OptionsManager
- IconRegistry (for iconset list)

**Public API:**
```php
class SettingsPage {
    public function register(): void
    public function render(): void
    public function save(): void
}
```

## Data Structures

### Icon

```php
class Icon {
    public string $id;
    public string $name;
    public string $class;
    public string $image;
    public string $urlTemplate;
}
```

### Iconset

```php
class Iconset {
    public string $id;
    public string $name;
    public string $type; // 'square' or 'circle'
    public array $icons; // Icon[]
    public string $cssPath;
    public string $previewImage;
}
```

## Dependency Injection

### Simple Container

```php
class Container {
    private array $services = [];
    
    public function register(string $key, callable $factory): void
    public function get(string $key): object
}
```

### Service Registration

```php
$container->register('iconRegistry', fn() => new IconRegistry());
$container->register('urlBuilder', fn() => new UrlBuilder($container->get('iconRegistry')));
$container->register('optionsManager', fn() => new OptionsManager());
$container->register('buttonRenderer', fn() => new ButtonRenderer(
    $container->get('iconRegistry'),
    $container->get('urlBuilder'),
    $container->get('optionsManager')
));
```

## WordPress Integration

### Hook Registration

**In Plugin::init():**

```php
// Frontend hooks
add_action('wp_footer', [$cssGenerator, 'output']);
add_filter('the_content', [$placementManager, 'filterContent']);

// Admin hooks
add_action('admin_menu', [$settingsPage, 'register']);
add_action('widgets_init', [$widget, 'register']);

// Shortcode
add_shortcode('zm_sh_btn', [$shortcode, 'handle']);
```

### Filter Points

```php
// Allow filtering of share URLs
apply_filters('html_social_share_url', $url, $network, $context);

// Allow filtering of HTML output
apply_filters('html_social_share_output', $html, $options);

// Allow filtering of options
apply_filters('html_social_share_options', $options);

// Allow custom icon definitions
apply_filters('html_social_share_icons', $icons, $iconsetId);
```

## Class Relationships

```
Plugin
  ├── IconRegistry
  ├── OptionsManager
  ├── UrlBuilder (uses IconRegistry)
  ├── ButtonRenderer (uses IconRegistry, UrlBuilder, OptionsManager)
  ├── CssGenerator (uses IconRegistry, OptionsManager)
  ├── PlacementManager (uses ButtonRenderer, OptionsManager)
  ├── Shortcode (uses ButtonRenderer, OptionsManager)
  ├── Widget (uses ButtonRenderer)
  └── SettingsPage (uses OptionsManager, IconRegistry)
```

## Error Handling

### Principles
1. Fail gracefully - don't break page if buttons can't render
2. Log errors to debug.log when WP_DEBUG is true
3. Return empty string on render failures
4. Validate all inputs

### Example
```php
public function render(array $options): string {
    try {
        // Rendering logic
        return $html;
    } catch (\Exception $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('HTML Social Share: ' . $e->getMessage());
        }
        return '';
    }
}
```

## Testing Strategy

### Unit Tests
- Test each class in isolation
- Mock dependencies
- Test all public methods
- Test edge cases

### Integration Tests
- Test WordPress integration
- Test hook registration
- Test database operations

### Visual Tests
- Playwright screenshot comparison
- Test all placements
- Test all iconsets

## Performance Considerations

### Caching
- Cache IconRegistry data in memory
- Cache OptionsManager data
- Transient cache for expensive operations

### Lazy Loading
- Only load CSS for active iconset
- Only render buttons when needed
- Defer non-critical code

### Database Queries
- Single options query per page load
- No queries per button render
- Use WordPress object cache

## Security

### Input Sanitization
- sanitize_key() for iconset/type
- sanitize_html_class() for classes
- esc_url_raw() for URLs
- wp_kses_post() for HTML content

### Output Escaping
- esc_attr() for attributes
- esc_html() for text
- esc_url() for URLs in output

### Nonce Verification
- verify_nonce() for settings save
- check_admin_referer() for actions

## Backward Compatibility

### Global Function Wrapper
```php
// In global scope
function zm_sh_btn($atts = []) {
    return \HtmlSocialShare\Compatibility\LegacyFunctions::zm_sh_btn($atts);
}
```

### Filter Compatibility
```php
// Old filter names still work
add_filter('zm_sh_placeholder', ...); // Deprecated but supported
add_filter('html_social_share_url', ...); // New filter name
```

## Success Criteria

1. ✅ All classes follow PSR-4
2. ✅ Dependency injection used
3. ✅ Single responsibility principle
4. ✅ Testable (no WordPress globals in core logic)
5. ✅ Backward compatible
6. ✅ Well documented
7. ✅ Type-safe (PHP 8+ features where possible)

## Next Steps

1. Create interface files
2. Implement IconRegistry
3. Implement UrlBuilder
4. Implement OptionsManager
5. Implement ButtonRenderer
6. Implement remaining classes
7. Wire up in Plugin class
8. Test everything
