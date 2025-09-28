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
