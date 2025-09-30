# Admin UI Tabs Audit

_Date: 2025-10-01_

## 1. Current Tabs Overview

| Tab | Label in UI | Primary Sections | Notable Options |
| --- | --- | --- | --- |
| general | General | Display Options, Default Appearance | show_on_front_page, show_on_posts, show_on_pages, show_on_archives, default_style, default_size |
| networks | Networks | Available Networks grid, Network Order drag list, Custom Networks | enabled_networks, network_order, per-network labels, custom_networks CRUD |
| profiles | Profiles | Profile list, Editor, Default profile selector | profiles array, default_profile, per-profile networks + display_settings |
| integrations | Integrations | BetterLinks panel, Page Builder toggles, Status summary | betterlinks_enabled, betterlinks_api_key, elementor_enabled, divi_enabled, beaver_builder_enabled |
| appearance | Appearance | Button Style, Custom Styling, Preview | title, icon_style, button_size, button_spacing, custom_css |
| placement | Placement | Auto placement, Content Types, Manual placement info | auto_placement, placement_position, placement_post_types, exclude_pages |
| advanced | Advanced | Analytics & Tracking, Performance & Debug | google_analytics, auto_hide_buttons, use_port_in_url, nofollow_links, cache_enabled, cache_duration, debug_mode |
| shortcode | Shortcode | Generator wizard, Preview | shortcode builder (networks, style, size, etc.) |

## 2. Option Inventory

- **Display / Placement**
  - show_on_front_page, show_on_posts, show_on_pages, show_on_archives
  - auto_placement, placement_position, placement_post_types, exclude_pages
- **Design / Defaults**
  - default_style, default_size, title, icon_style, button_size, button_spacing, custom_css
- **Networks**
  - enabled_networks, network_order, custom_networks[] (id, name, label, share_url, color, icon_class, enabled)
  - Per-network label overrides
- **Profiles**
  - profiles[], default_profile
  - display_settings.{style,size,text_labels,icon_only}
- **Integrations**
  - betterlinks_enabled, betterlinks_api_key
  - elementor_enabled, divi_enabled, beaver_builder_enabled
- **Advanced**
  - google_analytics, auto_hide_buttons, use_port_in_url, nofollow_links
  - cache_enabled, cache_duration, debug_mode
- **Shortcode**
  - Generated shortcode parameters (networks, style, size, labels, counts, url, title, align, class)

## 3. Duplicates & Gaps

- **Duplicate responsibilities**
  - `default_style` and `default_size` live under General but conceptually match Appearance settings.
  - Button sizing appears as both general defaults and appearance overrides, causing confusion.
  - Separate "Network Order" section duplicates reorder capability already implied by enabled list order.

- **Missing persistence**
  - `title`, `google_analytics`, `auto_hide_buttons`, `use_port_in_url`, `nofollow_links`, `cache_enabled`, `cache_duration`, `debug_mode`, `exclude_pages` are not mapped in `useSettings` → saves silently fail.
  - BetterLinks advanced flags used in PHP (`betterlinks_shorten_urls`, `betterlinks_add_tracking`, `betterlinks_custom_tracking`) are absent from REST payload and UI.

- **Structural issues**
  - Available Networks grid becomes unstable when cards expand (inputs cause layout shifts, cards disappear).
  - Drag-and-drop only works inside the Network Order list; Available Networks list cannot be reordered.
  - Save buttons exist per tab with inconsistent feedback when REST payload omits fields.

## 4. Proposed Tab Reorganization

1. **Display**
   - Combine current "Display Options" from General with Placement controls.
   - Focus on where buttons appear (on/off toggles, placement position, post types, exclusions).

2. **Design**
   - Merge visual defaults: title, icon/button style, size, spacing, custom CSS, and `default_style`/`default_size`.
   - Provide a single Save covering presentation defaults.

3. **Networks**
   - Vertical list of available networks with full-card drag-and-drop for enabled order.
   - Inline label editing when enabled; remove standalone Network Order block.
   - Custom networks management remains but gains compact styling.

4. **Profiles**
   - Mostly unchanged; relies on reorganized defaults for clarity.

5. **Integrations**
   - Expand BetterLinks section: availability status, API key, toggles for URL shortening, UTM tracking, custom parameters.
   - Retain page builder toggles and status view.

6. **Advanced**
   - Persist analytics, performance, debug options (ensure REST coverage).
   - Potentially host future developer-focused settings.

7. **Shortcode**
   - Utility tab; no structural changes required.

## 5. Immediate Fixes & Follow-up Work

- Map all missing fields in REST (`useSettings`, controller defaults, sanitization) to resolve "Save settings" failures.
- Update `NetworksTab` layout and interactions per instructions; rely on `enabled_networks` array order.
- Add BetterLinks controls to match backend integration class expectations.
- Refresh documentation (`docs/13-Current-Options-Reference.md`) after implementation.
- Consider adding automated regression tests around REST payload (future work).
