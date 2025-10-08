# Changelog

All notable changes to this project are documented in this file.

## [Unreleased]

### Fixed
- **Settings Save Debug Logging**: Added comprehensive debug logging to SettingsController to track setting save operations
  - Logs each individual setting being saved with key and value
  - Logs total count of updated settings keys
  - Performs database check after save to verify iconset, title, and enabled_networks are persisted
  - Added `iconset` field to REST API schema in appearance properties
  - Fixed `profiles` schema type from object to array with proper item structure
  - Added `default_profile` to top-level schema

### Changed
- **Component Decomposition Rule Added**: Updated `.github/copilot-instructions.md` with new architectural rule requiring components over 200-300 lines to be decomposed into smaller, focused components with single responsibilities. This improves maintainability, testability, and reduces risk of file corruption during edits.
- **Networks Tab Drag-and-Drop UX Improvements**: Enhanced drag-and-drop experience in Networks tab
  - Implemented DragOverlay for proper z-index handling and visual separation of dragged items
  - Added activation constraint (8px distance) to prevent accidental drag triggers
  - Improved drag feedback: dragging item shows 40% opacity, overlay shows full opacity with shadow-2xl
  - Added smooth scale animations (1.01x hover, 1.05x during drag)
  - Enhanced cursor feedback: cursor-grab (rest) → cursor-grabbing (active drag)
  - Improved transitions with custom 200ms ease for smoother animations
  - Overlay displays with border-2 border-blue-500 and shadow-2xl for clear visual hierarchy
  - Fixed icon rendering in drag overlay with proper error handling

### Improved
- **Settings API Response**: REST API now returns fresh settings from database after save
  - `POST /html-social-share/v1/settings` returns complete settings object in response
  - Frontend verifies saved settings by updating local state with DB data
  - Ensures UI always reflects actual saved values, preventing desync issues
- **Iconsets Data Loading**: Moved iconsets from REST API to localized script data
  - Iconsets now passed via `hssAdminConfig.iconsets` instead of separate API call
  - Eliminates unnecessary HTTP request since iconsets are static after page load
  - `useIconsets` hook now reads from window config instead of calling API
  - ReactAdminInterface receives IconRegistry dependency to provide iconsets
  - Updated ServiceRegistrar to pass iconRegistry to Admin constructor

### Fixed
- **Dependency Injection Fix**: Fixed fatal error in ReactAdminInterface constructor
  - ServiceRegistrar now passes both SettingsInterface and IconRegistryInterface to ReactAdminInterface constructor
  - Resolved "Too few arguments to function HtmlSocialShare\Admin\ReactAdminInterface::__construct()" error
  - Build now completes successfully without runtime errors
- **[MIGRATE-057] Settings Save Issue:** Fixed placement settings not persisting on save
  - Added floating_left, floating_right, before_content, after_content, exclude_pages to placement section in useSettings.ts saveSettings()
  - Added google_analytics, auto_hide_buttons, use_port_in_url, nofollow_links to advanced section in saveSettings()
  - Settings now properly save to WordPress options and persist across page reloads
- **[MIGRATE-056] Advanced Tab Restoration:** Restored Advanced tab from archived folder
  - Created simplified AdvancedTab.tsx with only legacy v2.x options
  - Removed cache_enabled, cache_duration, debug_mode fields
  - Kept only google_analytics, auto_hide_buttons, use_port_in_url, nofollow_links
  - Added Advanced tab to ReactAdminInterface with Settings icon
  - Updated tabs/index.ts to export AdvancedTab
- **[MIGRATE-052] Networks Tab Redesign:** Decomposed NetworksTab from monolithic 800-line component into smaller, focused components
  - Created subdirectory `tabs/networks/` with:
    - **SortableNetworkItem.tsx** (79 lines): Individual draggable network item with inline checkbox, icon, and enabled status indicator
    - **NetworksList.tsx** (68 lines): DnD context wrapper with SortableContext managing vertical list drag-and-drop
    - **defaultNetworks.ts** (57 lines): Network configuration data extracted from component
    - **NetworksTab.tsx** (133 lines): Main orchestration component managing state and save functionality
  - Merged "Available Networks" (grid with checkboxes) and "Network Order" (vertical DnD list) into single unified vertical DnD list with inline checkboxes
  - Users can now drag to reorder and checkbox to enable/disable in one interface, eliminating the confusing two-section UI
  - Component decomposition follows new architectural guidelines and improves maintainability

### Changed
- **[MIGRATE-054] Design Tab Simplification:** Simplified Design/Appearance tab to show only legacy v2.x options
  - Removed complex design options (default_style, default_size, icon_style, button_size, button_spacing)
  - Removed Custom CSS textarea and preview section
  - Now shows only 7 legacy settings:
    - Title (text input) - "Share this with your friends"
    - Icon Set (dropdown) - Choose button style
    - Exclude Pages (textarea) - Comma-separated page IDs/slugs/titles
    - Google Analytics (checkbox) - Track social shares
    - Auto Hide Buttons (checkbox) - Auto-hide floating buttons
    - Use Port in URL (checkbox) - Include :443 in URLs
    - Nofollow Links (checkbox) - Add rel="nofollow" to links
  - Updated component to use LegacyDesignSettings type
  - Reduced file from 293 lines to 233 lines
  - Maintained modern UI components and organized into Basic Settings and Advanced Options sections
- **[MIGRATE-051] Display Tab Simplification:** Simplified DisplayTab to legacy 4-checkbox layout matching v2.x behavior
  - Removed complex display locations (show_on_front_page, show_on_posts, show_on_pages, show_on_archives)
  - Removed auto_placement toggle and placement_position dropdown
  - Removed post type selection and exclude pages field
  - Now shows only 4 legacy checkboxes: floating_left, floating_right, before_content, after_content
  - Updated component to use LegacyDisplaySettings type
  - Reduced file from 322 lines to 142 lines
  - Maintains modern UI components (FormField, Checkbox, Button, LoadingOverlay)

## [1.1.0] - 2025-01-04

### Added
- Modern React-based admin interface with TypeScript
- Tabbed navigation for better organization (Display, Networks, Design, Advanced)
- Real-time settings validation and error handling
- Comprehensive notification system for user feedback
- REST API endpoints for settings management
- Drag-and-drop network ordering
- Icon set preview and selection
- Advanced settings panel for power users

### Changed
- Migrated from PHP-rendered admin forms to React SPA
- Updated build system to use wp-scripts
- Improved settings schema and validation
- Enhanced user experience with loading states and transitions

### Fixed
- Settings persistence issues
- Network ordering bugs
- Icon set loading problems

## [1.0.0] - 2024-12-15

### Added
- Initial release
- Basic social share button functionality
- Multiple icon sets
- Floating and inline placement options
- Shortcode support
- Widget support

### Security
- Input sanitization
- Output escaping
- Nonce verification for all forms
