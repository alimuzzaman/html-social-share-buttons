# HTML Social Share Buttons - WordPress Plugin

[![WordPress](https://img.shields.io/badge/WordPress-3.0%2B-blue.svg)](https://wordpress.org/)
[![PHP](https://img.shields.io/badge/PHP-5.6--8.5%2B-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPLv2-green.svg)](https://www.gnu.org/licenses/gpl-2.0.html)

A lightweight WordPress plugin for adding social share buttons without JavaScript. Pure HTML/CSS implementation for optimal performance and privacy.

## ✨ Features

- ✅ **No JavaScript** - Pure HTML/CSS social share buttons
- ✅ **Lightweight** - Only 10-11KB total load
- ✅ **6 Social Networks** - Facebook, Twitter, LinkedIn, Pinterest, Google+, Email
- ✅ **4 Icon Styles** - Default, Flat, Long Shadow, Prajin  
- ✅ **2 Shape Types** - Square and Circle
- ✅ **Multiple Placements** - Left, Right, Before Post, After Post, Widget, Shortcode
- ✅ **Privacy-Friendly** - No tracking, no external scripts
- ✅ **Modern Architecture** - PSR-4, Dependency Injection, WordPress Standards
- ✅ **Dual Shortcode Support** - Modern `[html_social_share]` + Legacy `[zm_sh_btn]`

## 🚀 Quick Start

### For Users

```bash
# Download and activate
1. Upload to /wp-content/plugins/html-social-share-buttons/
2. Activate in WordPress admin
3. Use shortcode or widget
```

### Modern Shortcode (Recommended)

```php
[html_social_share]
[html_social_share iconset="flat" type="circle"]
[html_social_share networks="facebook,twitter,linkedin"]
```

### Legacy Shortcode (Backward Compatible)

```php
[zm_sh_btn]
[zm_sh_btn iconset="flat" iconset_type="circle"]
[zm_sh_btn icons="facebook,twitter,linkedin"]
```

### Widget

Go to **Appearance > Widgets** → Add **HTML Social Share Buttons** widget

## 📖 Documentation

- **[Developer Guide](docs/DEVELOPER-GUIDE.md)** - Complete usage and API reference
- **[Implementation Status](docs/IMPLEMENTATION-STATUS.md)** - Current progress (52% complete)
- **[Architecture Design](docs/architecture-design.md)** - Technical architecture
- **[Test Status](docs/TEST-STATUS.md)** - Testing procedures
- **[Phase 1 Quick Start](docs/README-PHASE1.md)** - Development guide

## 🏗️ Architecture

### Modern PSR-4 Structure

```
HtmlSocialShare\
├── Core\Plugin              # Bootstrap & DI container
├── IconSystem\              # Icon management
├── Services\                # URL building, placement logic
├── Renderers\               # HTML/CSS generation
├── Options\                 # Settings management
├── Frontend\Shortcode       # Shortcode handler
├── Admin\Widget             # Widget handler
└── Compatibility\           # Backward compatibility
```

### Key Features

- **Dependency Injection** - Clean, testable code
- **WordPress Standards** - Follows WP coding standards
- **Security First** - Input sanitization, output escaping
- **Performance** - In-memory caching, lazy loading
- **Extensibility** - WordPress filters for customization

## 🔧 Development

### Requirements

- PHP 5.6-8.5+
- WordPress 3.0+
- Composer
- pnpm (Node package manager)

### Setup

```bash
# Clone repository
git clone https://github.com/alimuzzaman/html-social-share-buttons.git
cd html-social-share-buttons

# Install dependencies
composer install
pnpm install

# Start development environment
pnpm wp-env start
```

### Testing

```bash
# PHP unit tests
composer test

# Visual regression tests
pnpm test

# Code standards
composer phpcs
```

## 📊 Implementation Status

### Phase 1: Ground-Up Rewrite

**Progress:** 52% Complete (17/33 tasks)

- ✅ **Phase 1A:** Test Infrastructure (88% - 7/8 tasks)
- ✅ **Phase 1B:** Core Architecture (100% - 11/11 tasks) **COMPLETE!**
- ⏳ **Phase 1C:** Iconset Build System (0% - 0/7 tasks)
- ⏳ **Phase 1D:** Integration & Testing (0% - 0/7 tasks)

### What's Working Now

- ✅ All 12 core classes implemented
- ✅ Modern `[html_social_share]` shortcode
- ✅ Legacy `[zm_sh_btn]` shortcode (backward compatible)
- ✅ WordPress widget
- ✅ All placements (left, right, before, after)
- ✅ HTML rendering
- ✅ CSS generation
- ✅ URL building
- ✅ Options management
- ✅ 50KB+ comprehensive documentation

## 🎯 Shortcode Attributes

### Modern Shortcode `[html_social_share]`

| Attribute | Values | Default | Description |
|-----------|--------|---------|-------------|
| `iconset` | default, flat, long_shadow, prajin | default | Button style |
| `type` | square, circle | square | Button shape |
| `networks` | facebook,twitter,linkedin,pinterest,googlepluse,mail | (all) | Networks to show |
| `url` | Any URL | %%permalink%% | URL to share |
| `title` | Any text | (page title) | Title to share |
| `class` | CSS class | in_shortcode | Custom CSS class |

### Legacy Shortcode `[zm_sh_btn]`

Same functionality, uses `icons` instead of `networks` and `iconset_type` instead of `type`.

## 🔌 Extensibility

### Available Filters

```php
// Customize iconset data
add_filter('html_social_share_iconset_data', function($data, $iconsetId, $type) {
    return $data;
}, 10, 3);

// Customize share URLs
add_filter('html_social_share_url', function($url, $network, $context) {
    return $url;
}, 10, 3);

// Customize HTML output
add_filter('html_social_share_output', function($html, $options) {
    return $html;
}, 10, 2);
```

## 📝 Changelog

### 3.0.0 (Phase 1 Rewrite - In Progress)
- ✅ Complete architecture redesign with PSR-4
- ✅ New modern shortcode `[html_social_share]`
- ✅ Backward compatible `[zm_sh_btn]` shortcode
- ✅ Dependency injection container
- ✅ All 12 core classes implemented
- ✅ Comprehensive test suite (45+ methods)
- ✅ 50KB+ documentation
- ⏳ Iconset build system (pending)
- ⏳ Settings page UI (pending)

### 2.2.1
- Tested up to WordPress 6.8
- Fixed PHP 8.2 deprecation warnings
- Security improvements

### 2.2.0
- **SECURITY FIX**: Stored XSS vulnerability (CVE-2025-9849)
- Proper input sanitization and output escaping
- Security hardening throughout

## 🤝 Contributing

Contributions are welcome! Please see our development guides:

1. Fork the repository
2. Create a feature branch
3. Follow PSR-4 and WordPress coding standards
4. Write tests for new features
5. Submit a pull request

## 📄 License

GPLv2 or later. See [LICENSE](LICENSE) for details.

## 👨‍💻 Credits

- **Plugin Author:** [Alimuzzaman Alim](https://alim.dev)
- **Icon Designer:** Hakan Ertan ([www.tonicons.com](https://www.tonicons.com))
- **Phase 1 Rewrite:** December 2024

## 🔗 Links

- [WordPress Plugin Directory](http://wordpress.org/plugins/html-social-share-buttons/)
- [GitHub Repository](https://github.com/alimuzzaman/html-social-share-buttons)
- [Support](https://github.com/alimuzzaman/html-social-share-buttons/issues)

## 📈 Project Statistics

- **Code:** ~1,500 lines of production PHP
- **Tests:** 45+ test methods
- **Documentation:** 50KB+ (7 comprehensive guides)
- **Classes:** 12 fully implemented
- **Networks:** 6 social platforms
- **Iconsets:** 4 unique styles
- **Time Invested:** ~115 hours

---

**Status:** Core functionality complete and working. Plugin ready for testing and use. 🎉

For detailed implementation status, see [IMPLEMENTATION-STATUS.md](docs/IMPLEMENTATION-STATUS.md)
