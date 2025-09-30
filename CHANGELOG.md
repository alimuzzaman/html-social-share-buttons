# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

### Added
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
