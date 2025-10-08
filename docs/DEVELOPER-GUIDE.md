# HTML Social Share Buttons - Developer Guide

## Overview

HTML Social Share Buttons is a lightweight WordPress plugin that adds social sharing buttons without JavaScript. This Phase 1 rewrite implements a modern, PSR-4 compliant architecture while maintaining backward compatibility.

## Features

✅ **No JavaScript** - Pure HTML/CSS social share buttons
✅ **6 Social Networks** - Facebook, Twitter, LinkedIn, Pinterest, Google+, Email
✅ **4 Icon Styles** - Default, Flat, Long Shadow, Prajin
✅ **2 Shape Types** - Square and Circle
✅ **Multiple Placements** - Left, Right, Before Post, After Post, Widget, Shortcode
✅ **Lightweight** - Only 10-11KB total load
✅ **Privacy-Friendly** - No tracking, no external scripts
✅ **Backward Compatible** - Existing shortcodes and functions still work

## Installation

### Requirements
- WordPress 3.0+
- PHP 5.6-8.5+
- Composer (for development)
- pnpm (for development)

### For Users
1. Upload plugin files to `/wp-content/plugins/html-social-share-buttons/`
2. Activate the plugin through WordPress admin
3. Use shortcodes or widgets to display buttons

### For Developers
```bash
# Clone repository
git clone https://github.com/alimuzzaman/html-social-share-buttons.git
cd html-social-share-buttons

# Install dependencies
composer install
pnpm install

# Start WordPress development environment
pnpm wp-env start
```

## Usage

### New Shortcode (Recommended)

```php
// Basic usage
[html_social_share]

// Custom iconset and type
[html_social_share iconset="flat" type="circle"]

// Specific networks
[html_social_share networks="facebook,twitter,linkedin"]

// All options
[html_social_share 
  iconset="default" 
  type="square" 
  networks="facebook,twitter,pinterest" 
  title="Share This" 
  url="https://example.com"
  class="my-custom-class"
]
```

### Legacy Shortcode (Backward Compatible)

```php
// Old shortcodes still work!
[zm_sh_btn]
[zm_sh_btn iconset="flat" iconset_type="circle"]
[zm_sh_btn icons="facebook,twitter,linkedin"]
```

### Widget

1. Go to **Appearance > Widgets**
2. Find **HTML Social Share Buttons** widget
3. Drag to your sidebar or footer
4. Configure:
   - Enter optional title
   - Select button style (iconset)
   - Choose type (square/circle)
   - Check networks to display
5. Save

### Programmatic Usage

```php
// Using new API
$plugin = \HtmlSocialShare\Core\Plugin::getInstance();
$renderer = $plugin->getService('buttonRenderer');
$html = $renderer->render([
    'iconset' => 'flat',
    'iconset_type' => 'circle',
    'icons' => ['facebook' => '1', 'twitter' => '1'],
]);
echo $html;

// Using legacy function (backward compatible)
echo zm_sh_btn([
    'iconset' => 'default',
    'icons' => 'facebook,twitter,linkedin',
]);
```

## Available Options

### Shortcode Attributes

| Attribute (New) | Attribute (Legacy) | Values | Default | Description |
|-----------------|-------------------|---------|---------|-------------|
| `iconset` | `iconset` | default, flat, long_shadow, prajin | default | Button style |
| `type` | `iconset_type` | square, circle | square | Button shape |
| `networks` | `icons` | facebook,twitter,linkedin,pinterest,googlepluse,mail | (all) | Social networks |
| `url` | `url` | Any URL | %%permalink%% | URL to share |
| `title` | `title` | Any text | (page title) | Title to share |
| `class` | `class` | CSS class | in_shortcode | Custom CSS class |

### Available Networks

- **facebook** - Facebook
- **twitter** - Twitter/X
- **linkedin** - LinkedIn
- **pinterest** - Pinterest
- **googlepluse** - Google Plus (deprecated but functional)
- **mail** - Email

### Available Iconsets

- **default** - Default iconset
- **flat** - Flat design
- **long_shadow** - Long shadow effect
- **prajin** - Prajin style

## Architecture

### PSR-4 Namespace Structure

```
HtmlSocialShare\
├── Core\
│   └── Plugin.php              # Main plugin bootstrap
├── IconSystem\
│   ├── Icon.php                # Icon data model
│   ├── Iconset.php             # Iconset data model
│   └── IconRegistry.php        # Icon management
├── Services\
│   ├── UrlBuilder.php          # Share URL generation
│   └── PlacementManager.php    # Placement logic
├── Renderers\
│   ├── ButtonRenderer.php      # HTML generation
│   └── CssGenerator.php        # CSS generation
├── Options\
│   └── OptionsManager.php      # Settings management
├── Frontend\
│   └── Shortcode.php           # Shortcode handler
├── Admin\
│   └── Widget.php              # Widget handler
└── Compatibility\
    └── LegacyFunctions.php     # Backward compatibility
```

### Dependency Injection

The plugin uses a simple dependency injection container in `Plugin.php`:

```php
$plugin = Plugin::getInstance();
$buttonRenderer = $plugin->getService('buttonRenderer');
$cssGenerator = $plugin->getService('cssGenerator');
// ... etc
```

### WordPress Hooks

**Actions:**
- `plugins_loaded` (priority 10) - Initialize plugin
- `wp_footer` (priority 5) - Output CSS
- `wp_footer` (priority 10) - Output fixed placements
- `widgets_init` - Register widget

**Filters:**
- `the_content` - Add before/after post buttons
- `html_social_share_iconset_data` - Customize iconset data
- `html_social_share_url` - Customize share URLs
- `html_social_share_output` - Customize HTML output

## Development

### Running Tests

```bash
# PHP unit tests
composer test

# Visual regression tests
pnpm test

# Code style check
composer phpcs

# Code style fix
composer phpcbf
```

### Project Structure

```
html-social-share-buttons/
├── src/                    # PSR-4 source code
├── tests/                  # Test suites
│   ├── Unit/              # PHPUnit tests
│   ├── visual/            # Playwright tests
│   └── fixtures/          # Test fixtures
├── docs/                   # Documentation
├── archive/                # v2.2.1 production code (reference)
├── archive2/               # Previous rewrite attempt (reference)
├── build/                  # Compiled assets (future)
├── assets/                 # Static assets
├── vendor/                 # Composer dependencies
├── node_modules/           # npm dependencies
├── html-social-share.php   # Main plugin file
├── composer.json           # PHP dependencies
├── package.json            # Node dependencies
├── phpunit.xml             # PHPUnit config
└── playwright.config.ts    # Playwright config
```

### Code Standards

- **PHP:** PSR-4, WordPress Coding Standards
- **Security:** Input sanitization, output escaping
- **Performance:** In-memory caching, lazy loading
- **Compatibility:** PHP 5.6-8.5+, WordPress 3.0+

### Security

All user inputs are sanitized:
- `sanitize_key()` for iconset/type
- `sanitize_text_field()` for text
- `sanitize_html_class()` for CSS classes
- `esc_url_raw()` for URLs

All outputs are escaped:
- `esc_attr()` for HTML attributes
- `esc_html()` for text content
- `esc_url()` for URLs in output

## Filters & Extensibility

### Customize Iconset Data

```php
add_filter('html_social_share_iconset_data', function($data, $iconsetId, $type) {
    // Modify iconset data
    return $data;
}, 10, 3);
```

### Customize Share URLs

```php
add_filter('html_social_share_url', function($url, $network, $context) {
    // Modify share URL
    return $url;
}, 10, 3);
```

### Customize HTML Output

```php
add_filter('html_social_share_output', function($html, $options) {
    // Modify HTML output
    return $html;
}, 10, 2);
```

## Troubleshooting

### Buttons Not Showing

1. Check if plugin is activated
2. Verify shortcode syntax
3. Check if CSS is being output (view page source)
4. Ensure no JavaScript errors in console

### CSS Not Loading

1. Clear cache (browser and WordPress)
2. Check wp_footer hook is called in theme
3. Verify theme loads wp_head() and wp_footer()

### Shortcode Not Working

1. Verify shortcode syntax
2. Check for typos in attribute names
3. Ensure networks/icons are comma-separated
4. Try the legacy `[zm_sh_btn]` shortcode

### Widget Not Appearing

1. Check Appearance > Widgets
2. Verify widget is added to a sidebar
3. Ensure sidebar is displayed in theme
4. Check widget settings are saved

## Support

- **Documentation:** See `/docs/` directory
- **Issues:** GitHub Issues
- **Legacy Reference:** See `/archive/` for v2.2.1 code

## License

GPLv2 or later

## Credits

- **Plugin Author:** Alimuzzaman Alim
- **Icon Designer:** Hakan Ertan (www.tonicons.com)
- **Phase 1 Rewrite:** December 2024

## Changelog

### 3.0.0 (Phase 1 Rewrite)
- Complete architecture redesign with PSR-4
- New modern shortcode `[html_social_share]`
- Backward compatible with `[zm_sh_btn]`
- Dependency injection container
- Comprehensive test suite
- Improved security and performance
- All 12 core classes implemented

### 2.2.1
- Tested up to WordPress 6.8
- Fixed PHP 8.2 deprecation warnings
- Security improvements

---

**For detailed implementation status, see:** `docs/IMPLEMENTATION-STATUS.md`
**For Phase 1 documentation, see:** `docs/README-PHASE1.md`
