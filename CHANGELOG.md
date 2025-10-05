# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

### Added
- **[LEGACY-204] Content Filter Hooks:** Implemented automatic before/after post placement
  - Created `LegacyContentFilter` class handling `the_content` filter
  - Registered legacy_content_filter service and initialized in Bootstrap
  - Supports both legacy (show_before_post, show_after_post) and new (before_content, after_content) settings
  - Added floating button rendering in wp_footer hook
  - Implemented Google Analytics tracking script injection
  - Checks for excluded posts and per-post disable meta
  - Only runs on singular pages (posts/pages)
- **[LEGACY-203] PHP Function API:** Global `zm_sh_btn()` function available for theme integration
  - Function already implemented in Compatibility.php
  - Accepts legacy options array format
  - Returns HTML output via LegacyButtonRenderer
  - Can be called from any theme file after init hook
- **[LEGACY-202] Legacy Widget Integration:** Implemented v2.x-compatible WordPress widget
  - Created `LegacyWidget` class extending `WP_Widget` with legacy interface
  - Registered widget via `zm_sh_register_widgets()` compatibility function
  - Added iconset and icon type selection in widget form
  - Implemented network checkbox selection matching v2.x behavior
  - Widget uses LegacyButtonRenderer for v2.x-compatible output
  - Added exclusion checking (excluded posts and per-post disable)
  - Proper sanitization of all widget settings
  - Backward compatible with `html_share_button_widget` widget ID
- **[LEGACY-201] Legacy Shortcode Support:** Enhanced `[zm_sh_btn]` shortcode with full v2.x parameter compatibility
  - Updated `zm_sh_shortcode_cb()` in Compatibility.php with proper attribute parsing
  - Added support for all legacy parameters: title, iconset, url, icons, iconset_type, class, nofollow
  - Implemented icon list parsing from comma-separated string format
  - Added exclusion checking (excluded posts and per-post disable meta)
  - Sanitization of all user inputs for security
  - Works seamlessly with LegacyButtonRenderer for v2.x-compatible output
- **[LEGACY-002] Legacy Button Renderer:** Implemented `LegacyButtonRenderer` class for pure HTML/CSS v2.x-compatible share button rendering
  - Created `/src/Frontend/LegacyButtonRenderer.php` with identical HTML structure to legacy `zm_sh_btn()` method
  - Registered `legacy_button_renderer` service in container for dependency injection
  - Updated `Compatibility.php` to route `zm_sh_btn()` calls through new renderer
  - Added legacy CSS file `/assets/css/frontend-legacy.css` with floating button styles
  - Implemented dynamic CSS generation for icon backgrounds via `wp_footer` hook
  - Added URL placeholder processing (%%permalink%%, %%title%%, %%description%%, %%imageurl%%)
  - Restored floating left/right positioning with hover animations
  - Added support for Google Analytics social tracking
  - Enhanced `Networks.php` with `getNetworks()` method returning legacy-compatible share URLs
- **Full Backward Compatibility:** Implemented complete migration system for legacy v2.x settings to v3.0+ schema
  - All 12 legacy option keys (`zm_shbt_fld`) automatically migrated to new `hss_core` structure
  - Migration mapping: title, excludes→exclude_pages, g_analytics→google_analytics, auto_hide_btn→auto_hide_buttons, use_port→use_port_in_url, nofollow→nofollow_links, iconset→icon_style, show_left→floating_left, show_right→floating_right, show_before_post→before_content, show_after_post→after_content, icons→enabled_networks
  - Legacy options preserved in database for rollback safety
- **React Admin UI Enhancements:**
  - Added "Legacy Placement Options" section in Placement Tab with individual toggles for backward compatibility
  - Extended TypeScript interfaces to include all legacy placement options (floating_left, floating_right, before_content, after_content)
  - Updated REST API schema to validate and sanitize legacy placement fields
- **Documentation:**
  - Created comprehensive `docs/advanced-settings-reference.md` with migration guide, setting locations, SEO implications, and developer reference
  - Updated `docs/first-design-release.md` with complete implementation roadmap and testing checklist
  - Updated `.github/copilot-instructions.md` with GitHub MCP usage guidelines
- **Testing:**
  - Created unit test suite (`tests/php/unit/MigrationTest.php`) verifying all 12 legacy keys migrate correctly
  - Tests cover migration success, missing key defaults, network name normalization, and duplicate migration prevention
- Added support for new social networks: Mastodon, Bluesky, Threads, VK, WeChat, Instagram Direct, and Messenger.
- Gutenberg block integration with server-side rendering and full editor controls.
- Server-side share count system with caching and DB storage (`ShareCountManager`), including adapters for Facebook, Pinterest, VK, X (placeholder), LinkedIn (placeholder), and a generic adapter.
- Progressive enhancement for WeChat sharing: server-rendered QR code with JS toggle and local QR generation via `endroid/qr-code` when available.
- Icon registry enhancements: support for multiple iconsets and automatic background-image CSS output for PNG iconsets.
- Admin settings: options to enable share counts, cache TTL, manual refresh and flush controls, iconset and style selection, and a live preview.
- CI workflows: GitHub Actions for PHPUnit and Playwright E2E tests.
- New unit tests: WeChat QR rendering test and VK adapter test; added share count tests.

### Changed
- [PHASE3-124] Convert admin React UI to Tailwind-first implementation and fix admin stylesheet enqueue (timestamp: 2025-09-30 15:30:00)
- [PHASE3-125] Prefer lucide-react icons for available social networks in the Networks admin tab with fallbacks to plugin iconset assets and initial-letter placeholders (timestamp: 2025-09-30 15:45:00)
- [PHASE3-126] Reorganize admin UI tabs: rename General to Display, Appearance to Design, Placement to Display (merged), update icons to Lucide React (timestamp: 2025-10-01 12:00:00)
- Reworked the block preview to use `ServerSideRender` in editor for accurate live previews.
- Sanitized SVG handling and improved accessibility (ARIA labels, roles, keyboard support) across share buttons.
- Implemented local QR generation with graceful fallback to Google Chart API when local image extensions are unavailable.
- Output iconset CSS as inline `<style>` to ensure background-image icons load when iconsets are selected.

### Fixed
- Fixed live preview JavaScript promise chain bug in Settings page which prevented preview updates.
- Fixed iconset CSS rendering to ensure icon images are included in frontend output.

### Security
- Continued to sanitize any SVGs used and added SVG sanitizer utilities.
- Ensured server-side rendering and no frontend tracking by default to preserve privacy.

### Tests
- Added tests for share counts, VK adapter, and WeChat QR rendering. CI will execute tests in properly provisioned environments.

---

For detailed changes, see the individual commits on the `new` branch.

## [1.1.0] - 2025-09-29
### Added
- Elementor integration and widget (`src/Integrations/Elementor/ShareButtonsWidget.php`, `src/Integrations/Elementor/ElementorIntegration.php`).
- Advanced Icon Picker for admin (`src/Admin/IconPicker.php`) with search and live preview.
- Pure function utilities for rendering and data processing (`src/Renderers/RenderUtils.php`, `src/Utils/DataUtils.php`, `src/Utils/ArrayUtils.php`).
- ShareUrlBuilder and ShareButtonRenderer to separate URL-building and HTML rendering responsibilities (`src/Renderers/ShareUrlBuilder.php`, `src/Renderers/ShareButtonRenderer.php`).
- RefactoredShareRenderer as a DI-friendly main renderer (`src/RefactoredShareRenderer.php`).
- Unit tests for RenderUtils, IconPicker, Elementor and WPBakery components added.

### Changed
- Reworked the block preview to use `ServerSideRender` in editor for accurate live previews.
- Sanitized SVG handling and improved accessibility (ARIA labels, roles, keyboard support) across share buttons.
- Implemented local QR generation with graceful fallback to Google Chart API when local image extensions are unavailable.

### Fixed
- Miscellaneous bug fixes related to iconset mapping and admin preview handling.

### Security
- Added additional sanitization and validation utilities to reduce risk of XSS and malformed input.

### Tests
- Added tests for share counts, VK adapter, and WeChat QR rendering. CI will execute tests in properly provisioned environments.

---
  - Added support for all legacy parameters: title, iconset, url, icons, iconset_type, class, nofollow
  - Implemented icon list parsing from comma-separated string format
  - Added exclusion checking (excluded posts and per-post disable meta)
  - Sanitization of all user inputs for security
  - Works seamlessly with LegacyButtonRenderer for v2.x-compatible output
- **[LEGACY-002] Legacy Button Renderer:** Implemented `LegacyButtonRenderer` class for pure HTML/CSS v2.x-compatible share button rendering
  - Created `/src/Frontend/LegacyButtonRenderer.php` with identical HTML structure to legacy `zm_sh_btn()` method
  - Registered `legacy_button_renderer` service in container for dependency injection
  - Updated `Compatibility.php` to route `zm_sh_btn()` calls through new renderer
  - Added legacy CSS file `/assets/css/frontend-legacy.css` with floating button styles
  - Implemented dynamic CSS generation for icon backgrounds via `wp_footer` hook
  - Added URL placeholder processing (%%permalink%%, %%title%%, %%description%%, %%imageurl%%)
  - Restored floating left/right positioning with hover animations
  - Added support for Google Analytics social tracking
  - Enhanced `Networks.php` with `getNetworks()` method returning legacy-compatible share URLs
- **Full Backward Compatibility:** Implemented complete migration system for legacy v2.x settings to v3.0+ schema
  - All 12 legacy option keys (`zm_shbt_fld`) automatically migrated to new `hss_core` structure
  - Migration mapping: title, excludes→exclude_pages, g_analytics→google_analytics, auto_hide_btn→auto_hide_buttons, use_port→use_port_in_url, nofollow→nofollow_links, iconset→icon_style, show_left→floating_left, show_right→floating_right, show_before_post→before_content, show_after_post→after_content, icons→enabled_networks
  - Legacy options preserved in database for rollback safety
- **React Admin UI Enhancements:**
  - Added "Legacy Placement Options" section in Placement Tab with individual toggles for backward compatibility
  - Extended TypeScript interfaces to include all legacy placement options (floating_left, floating_right, before_content, after_content)
  - Updated REST API schema to validate and sanitize legacy placement fields
- **Documentation:**
  - Created comprehensive `docs/advanced-settings-reference.md` with migration guide, setting locations, SEO implications, and developer reference
  - Updated `docs/first-design-release.md` with complete implementation roadmap and testing checklist
  - Updated `.github/copilot-instructions.md` with GitHub MCP usage guidelines
- **Testing:**
  - Created unit test suite (`tests/php/unit/MigrationTest.php`) verifying all 12 legacy keys migrate correctly
  - Tests cover migration success, missing key defaults, network name normalization, and duplicate migration prevention
- Added support for new social networks: Mastodon, Bluesky, Threads, VK, WeChat, Instagram Direct, and Messenger.
- Gutenberg block integration with server-side rendering and full editor controls.
- Server-side share count system with caching and DB storage (`ShareCountManager`), including adapters for Facebook, Pinterest, VK, X (placeholder), LinkedIn (placeholder), and a generic adapter.
- Progressive enhancement for WeChat sharing: server-rendered QR code with JS toggle and local QR generation via `endroid/qr-code` when available.
- Icon registry enhancements: support for multiple iconsets and automatic background-image CSS output for PNG iconsets.
- Admin settings: options to enable share counts, cache TTL, manual refresh and flush controls, iconset and style selection, and a live preview.
- CI workflows: GitHub Actions for PHPUnit and Playwright E2E tests.
- New unit tests: WeChat QR rendering test and VK adapter test; added share count tests.

### Changed
- [PHASE3-124] Convert admin React UI to Tailwind-first implementation and fix admin stylesheet enqueue (timestamp: 2025-09-30 15:30:00)
- [PHASE3-125] Prefer lucide-react icons for available social networks in the Networks admin tab with fallbacks to plugin iconset assets and initial-letter placeholders (timestamp: 2025-09-30 15:45:00)
- [PHASE3-126] Reorganize admin UI tabs: rename General to Display, Appearance to Design, Placement to Display (merged), update icons to Lucide React (timestamp: 2025-10-01 12:00:00)
- Reworked the block preview to use `ServerSideRender` in editor for accurate live previews.
- Sanitized SVG handling and improved accessibility (ARIA labels, roles, keyboard support) across share buttons.
- Implemented local QR generation with graceful fallback to Google Chart API when local image extensions are unavailable.
- Output iconset CSS as inline `<style>` to ensure background-image icons load when iconsets are selected.

### Fixed
- Fixed live preview JavaScript promise chain bug in Settings page which prevented preview updates.
- Fixed iconset CSS rendering to ensure icon images are included in frontend output.

### Security
- Continued to sanitize any SVGs used and added SVG sanitizer utilities.
- Ensured server-side rendering and no frontend tracking by default to preserve privacy.

### Tests
- Added tests for share counts, VK adapter, and WeChat QR rendering. CI will execute tests in properly provisioned environments.

---

For detailed changes, see the individual commits on the `new` branch.

## [1.1.0] - 2025-09-29
### Added
- Elementor integration and widget (`src/Integrations/Elementor/ShareButtonsWidget.php`, `src/Integrations/Elementor/ElementorIntegration.php`).
- Advanced Icon Picker for admin (`src/Admin/IconPicker.php`) with search and live preview.
- Pure function utilities for rendering and data processing (`src/Renderers/RenderUtils.php`, `src/Utils/DataUtils.php`, `src/Utils/ArrayUtils.php`).
- ShareUrlBuilder and ShareButtonRenderer to separate URL-building and HTML rendering responsibilities (`src/Renderers/ShareUrlBuilder.php`, `src/Renderers/ShareButtonRenderer.php`).
- RefactoredShareRenderer as a DI-friendly main renderer (`src/RefactoredShareRenderer.php`).
- Unit tests for RenderUtils, IconPicker, Elementor and WPBakery components added.

### Changed
- Reworked the block preview to use `ServerSideRender` in editor for accurate live previews.
- Sanitized SVG handling and improved accessibility (ARIA labels, roles, keyboard support) across share buttons.
- Implemented local QR generation with graceful fallback to Google Chart API when local image extensions are unavailable.

### Fixed
- Miscellaneous bug fixes related to iconset mapping and admin preview handling.

### Security
- Added additional sanitization and validation utilities to reduce risk of XSS and malformed input.

### Tests
- Added tests for share counts, VK adapter, and WeChat QR rendering. CI will execute tests in properly provisioned environments.

---
>>>>>>> 5537645 (feat(legacy): LEGACY-202 - Restore Widget Integration)
=======
# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

### Added
- **[LEGACY-204] Content Filter Hooks:** Implemented automatic before/after post placement
  - Created `LegacyContentFilter` class handling `the_content` filter
  - Registered legacy_content_filter service and initialized in Bootstrap
  - Supports both legacy (show_before_post, show_after_post) and new (before_content, after_content) settings
  - Added floating button rendering in wp_footer hook
  - Implemented Google Analytics tracking script injection
  - Checks for excluded posts and per-post disable meta
  - Only runs on singular pages (posts/pages)
- **[LEGACY-203] PHP Function API:** Global `zm_sh_btn()` function available for theme integration
  - Function already implemented in Compatibility.php
  - Accepts legacy options array format
  - Returns HTML output via LegacyButtonRenderer
  - Can be called from any theme file after init hook
- **[LEGACY-202] Legacy Widget Integration:** Implemented v2.x-compatible WordPress widget
  - Created `LegacyWidget` class extending `WP_Widget` with legacy interface
  - Registered widget via `zm_sh_register_widgets()` compatibility function
  - Added iconset and icon type selection in widget form
  - Implemented network checkbox selection matching v2.x behavior
  - Widget uses LegacyButtonRenderer for v2.x-compatible output
  - Added exclusion checking (excluded posts and per-post disable)
  - Proper sanitization of all widget settings
  - Backward compatible with `html_share_button_widget` widget ID
- **[LEGACY-201] Legacy Shortcode Support:** Enhanced `[zm_sh_btn]` shortcode with full v2.x parameter compatibility
  - Updated `zm_sh_shortcode_cb()` in Compatibility.php with proper attribute parsing
  - Added support for all legacy parameters: title, iconset, url, icons, iconset_type, class, nofollow
  - Implemented icon list parsing from comma-separated string format
  - Added exclusion checking (excluded posts and per-post disable meta)
  - Sanitization of all user inputs for security
  - Works seamlessly with LegacyButtonRenderer for v2.x-compatible output
- **[LEGACY-002] Legacy Button Renderer:** Implemented `LegacyButtonRenderer` class for pure HTML/CSS v2.x-compatible share button rendering
  - Created `/src/Frontend/LegacyButtonRenderer.php` with identical HTML structure to legacy `zm_sh_btn()` method
  - Registered `legacy_button_renderer` service in container for dependency injection
  - Updated `Compatibility.php` to route `zm_sh_btn()` calls through new renderer
  - Added legacy CSS file `/assets/css/frontend-legacy.css` with floating button styles
  - Implemented dynamic CSS generation for icon backgrounds via `wp_footer` hook
  - Added URL placeholder processing (%%permalink%%, %%title%%, %%description%%, %%imageurl%%)
  - Restored floating left/right positioning with hover animations
  - Added support for Google Analytics social tracking
  - Enhanced `Networks.php` with `getNetworks()` method returning legacy-compatible share URLs
- **Full Backward Compatibility:** Implemented complete migration system for legacy v2.x settings to v3.0+ schema
  - All 12 legacy option keys (`zm_shbt_fld`) automatically migrated to new `hss_core` structure
  - Migration mapping: title, excludes→exclude_pages, g_analytics→google_analytics, auto_hide_btn→auto_hide_buttons, use_port→use_port_in_url, nofollow→nofollow_links, iconset→icon_style, show_left→floating_left, show_right→floating_right, show_before_post→before_content, show_after_post→after_content, icons→enabled_networks
  - Legacy options preserved in database for rollback safety
- **React Admin UI Enhancements:**
  - Added "Legacy Placement Options" section in Placement Tab with individual toggles for backward compatibility
  - Extended TypeScript interfaces to include all legacy placement options (floating_left, floating_right, before_content, after_content)
  - Updated REST API schema to validate and sanitize legacy placement fields
- **Documentation:**
  - Created comprehensive `docs/advanced-settings-reference.md` with migration guide, setting locations, SEO implications, and developer reference
  - Updated `docs/first-design-release.md` with complete implementation roadmap and testing checklist
- **Testing:**
  - Created unit test suite (`tests/php/unit/MigrationTest.php`) verifying all 12 legacy keys migrate correctly
  - Tests cover migration success, missing key defaults, network name normalization, and duplicate migration prevention
- Added support for new social networks: Mastodon, Bluesky, Threads, VK, WeChat, Instagram Direct, and Messenger.
- Gutenberg block integration with server-side rendering and full editor controls.
- Server-side share count system with caching and DB storage (`ShareCountManager`), including adapters for Facebook, Pinterest, VK, X (placeholder), LinkedIn (placeholder), and a generic adapter.
- Progressive enhancement for WeChat sharing: server-rendered QR code with JS toggle and local QR generation via `endroid/qr-code` when available.
- Icon registry enhancements: support for multiple iconsets and automatic background-image CSS output for PNG iconsets.
- Admin settings: options to enable share counts, cache TTL, manual refresh and flush controls, iconset and style selection, and a live preview.
- CI workflows: GitHub Actions for PHPUnit and Playwright E2E tests.
- New unit tests: WeChat QR rendering test and VK adapter test; added share count tests.

### Changed
- [PHASE3-124] Convert admin React UI to Tailwind-first implementation and fix admin stylesheet enqueue (timestamp: 2025-09-30 15:30:00)
- [PHASE3-125] Prefer lucide-react icons for available social networks in the Networks admin tab with fallbacks to plugin iconset assets and initial-letter placeholders (timestamp: 2025-09-30 15:45:00)
- [PHASE3-126] Reorganize admin UI tabs: rename General to Display, Appearance to Design, Placement to Display (merged), update icons to Lucide React (timestamp: 2025-10-01 12:00:00)
- Reworked the block preview to use `ServerSideRender` in editor for accurate live previews.
- Sanitized SVG handling and improved accessibility (ARIA labels, roles, keyboard support) across share buttons.
- Implemented local QR generation with graceful fallback to Google Chart API when local image extensions are unavailable.
- Output iconset CSS as inline `<style>` to ensure background-image icons load when iconsets are selected.

### Fixed
- Fixed live preview JavaScript promise chain bug in Settings page which prevented preview updates.
- Fixed iconset CSS rendering to ensure icon images are included in frontend output.

### Security
- Continued to sanitize any SVGs used and added SVG sanitizer utilities.
- Ensured server-side rendering and no frontend tracking by default to preserve privacy.

### Tests
- Added tests for share counts, VK adapter, and WeChat QR rendering. CI will execute tests in properly provisioned environments.

---

For detailed changes, see the individual commits on the `new` branch.

## [1.1.0] - 2025-09-29
### Added
- Elementor integration and widget (`src/Integrations/Elementor/ShareButtonsWidget.php`, `src/Integrations/Elementor/ElementorIntegration.php`).
- Advanced Icon Picker for admin (`src/Admin/IconPicker.php`) with search and live preview.
- Pure function utilities for rendering and data processing (`src/Renderers/RenderUtils.php`, `src/Utils/DataUtils.php`, `src/Utils/ArrayUtils.php`).
- ShareUrlBuilder and ShareButtonRenderer to separate URL-building and HTML rendering responsibilities (`src/Renderers/ShareUrlBuilder.php`, `src/Renderers/ShareButtonRenderer.php`).
- RefactoredShareRenderer as a DI-friendly main renderer (`src/RefactoredShareRenderer.php`).
- Unit tests for RenderUtils, IconPicker, Elementor and WPBakery components added.

### Changed
- Reworked the block preview to use `ServerSideRender` in editor for accurate live previews.
- Sanitized SVG handling and improved accessibility (ARIA labels, roles, keyboard support) across share buttons.
- Implemented local QR generation with graceful fallback to Google Chart API when local image extensions are unavailable.

### Fixed
- Miscellaneous bug fixes related to iconset mapping and admin preview handling.

### Security
- Added additional sanitization and validation utilities to reduce risk of XSS and malformed input.

### Tests
- Added tests for share counts, VK adapter, and WeChat QR rendering. CI will execute tests in properly provisioned environments.

---
>>>>>>> 95174f1 (feat(legacy): Implement legacy content filter and enhance button rendering)
