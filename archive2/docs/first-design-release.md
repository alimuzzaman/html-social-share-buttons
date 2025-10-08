# Get ready for first new design release

## Backward compatibility

Source reviewed: `archive/settings_page.php` (admin settings UI for the legacy plugin).

Goal: For the first design release, preserve existing persisted options (database values) but simplify the admin UI by keeping only essential options visible. All other options must be hidden from the admin UI (not deleted), so existing installations keep their values and behavior unless we explicitly change code later.

Assumptions
- I'm only reviewing `archive/settings_page.php` as requested; there may be additional options defined elsewhere. If you want a full audit, I'll search the repo for `zm_shbt_fld` and `get_option` usages.
- "Hide" means remove the controls in the new admin React UI but continue to read/write the keys from the `zm_shbt_fld` option array so DB compatibility is preserved.

Contract (small):
- Inputs: existing WP option `zm_shbt_fld` (associative array of keys).
- Outputs: A UI decision list that identifies which keys stay visible and which are hidden for initial v1 of the redesign.
- Error modes: If a key is missing in DB, the plugin should fall back to sensible defaults; we do not delete keys in DB.
- Success criteria: The prompt file lists all option keys referenced in `archive/settings_page.php`, and marks each as "Keep (visible)" or "Hide (preserve)" for v1.

Detected option keys (from `archive/settings_page.php`)
- title
- excludes
- g_analytics
- auto_hide_btn
- use_port
- nofollow
- iconset
- show_left
- show_right
- show_before_post
- show_after_post
- icons (array/list of enabled icons)
- show_in (array handled specially in sanitize)

Other keys referenced in code and UI generation
- iconset_type (used when generating shortcode/PHP snippets; not a persistent field in sanitize's keep_as_is list but may be in runtime usage)
- class (used by the PHP snippet example, e.g., `in_php_function`; runtime only)

Notes about sanitize() in `archive/settings_page.php`
- The sanitize method treats the following as canonical persisted keys (kept "as is"):
  - title
  - iconset
  - excludes
  - icons
  - show_in
  - show_left
  - show_right
  - show_before_post
  - show_after_post
- Other options (e.g., checkboxes like `g_analytics`, `auto_hide_btn`, `use_port`, `nofollow`) are saved only if they are set in the submitted input, in which case they are coerced to `true` in DB. That means these keys can exist in the DB but are considered optional/feature flags.

Recommended visibility for v1 (updated)

As requested, the following options will be kept available in the first redesigned admin UI. The new admin design/implementation (React-based admin) should present these options according to the new design language and UX patterns.

Keep visible (these exact options):

- title
- excludes
- g_analytics
- auto_hide_btn
- use_port
- nofollow
- iconset
- show_left
- show_right
- show_before_post
- show_after_post
- icons (array)

All other existing persisted keys (for example: `show_in` and any other legacy flags) must be preserved in the database but hidden from the primary UI. They should either live behind an "Advanced settings" disclosure/collapse or be preserved implicitly by the save/merge logic (see Implementation notes below). The goal: keep data compatibility while letting the new design dictate visible controls.

Runtime-only / Not a stored admin field (no UI needed in first release):

- iconset_type — used by snippet generators (shortcode/PHP snippets)
- class — runtime-only helper used in examples

Rationale
- The kept items cover the main user needs: naming the block, choosing a style, selecting networks, and toggling common placement options.
- All the original options are kept visible for the first design release to maintain feature parity with the legacy admin while providing a modern UX.
- The new design follows modern WordPress patterns with React, Tailwind CSS, and REST API integration.

## Implementation Architecture

### Data Storage Strategy

**Two-tier backward compatibility approach:**

1. **Legacy `zm_shbt_fld` option** (archive system)
   - WP option key: `zm_shbt_fld`
   - Used by: archive/settings_page.php, archive/html-social-share.php
   - Schema: associative array with flat keys
   - Status: READ-ONLY for new implementation; preserved for backward compat during transition

2. **New `hss_*` options** (v3.0+ system)
   - WP option keys: `hss_core`, `hss_profiles`, `hss_icons`
   - Used by: src/Settings.php, src/Rest/SettingsController.php, src/admin-ui
   - Schema: nested/structured data with validation and caching
   - Status: PRIMARY storage for new implementation

### Migration from Legacy to New Schema

The plugin includes `src/Migration.php` which handles one-time migration from `zm_shbt_fld` to `hss_core`. After migration:
- Legacy option `zm_shbt_fld` is preserved (not deleted) for rollback safety
- New admin UI reads/writes only `hss_*` options
- Frontend rendering uses `hss_core` but falls back to `zm_shbt_fld` if needed

**Mapping: Legacy → New Schema**

Legacy `zm_shbt_fld` keys → New `hss_core` structure:

| Legacy Key | New Location in `hss_core` | Notes |
|------------|---------------------------|-------|
| `title` | `appearance.title` | Admin label/heading for share buttons |
| `excludes` | `placement.exclude_pages` | Comma-separated list of page/post IDs |
| `g_analytics` | `advanced.google_analytics` | Boolean: enable Google Analytics tracking |
| `auto_hide_btn` | `advanced.auto_hide_buttons` | Boolean: auto-hide floating buttons on load |
| `use_port` | `advanced.use_port_in_url` | Boolean: include port in shared URLs |
| `nofollow` | `advanced.nofollow_links` | Boolean: add rel="nofollow" to share links |
| `iconset` | `appearance.icon_style` | Icon style/theme name (e.g., "default", "flat") |
| `show_left` | `placement.floating_left` | Boolean: show floating buttons on left side |
| `show_right` | `placement.floating_right` | Boolean: show floating buttons on right side |
| `show_before_post` | `placement.before_content` | Boolean: show buttons before post content |
| `show_after_post` | `placement.after_content` | Boolean: show buttons after post content |
| `icons` | `networks.enabled_networks` | Array of network IDs (e.g., ["facebook", "twitter"]) |
| `show_in` | *(internal)* | Nested array; handled by `placement.*` booleans |
| `iconset_type` | `appearance.icon_size` or similar | Used in runtime/shortcode generation |

### REST API Endpoints

Base namespace: `html-social-share/v1`

**Settings endpoints:**
- `GET /settings` - Retrieve all settings (returns structured `hss_core` data)
- `POST /settings` - Update settings (validates and saves to `hss_core`)
- `POST /settings/reset` - Reset to defaults

**Profiles endpoints:**
- `GET /profiles` - List all profiles
- `POST /profiles` - Create profile
- `GET /profiles/{id}` - Get profile
- `PUT /profiles/{id}` - Update profile
- `DELETE /profiles/{id}` - Delete profile

**Networks endpoints:**
- `GET /networks` - List available networks
- `GET /networks/{id}` - Get network details

**Iconsets endpoints:**
- `GET /iconsets` - List available iconsets from `assets/iconset/`

All endpoints require `manage_options` capability (administrator).

### React Admin UI Structure

**Entry point:** `src/admin-ui/index.tsx`
**Main component:** `src/admin-ui/components/ReactAdminInterface.tsx`

**Tab structure (from `src/admin-ui/components/ui/Tabs.tsx`):**
1. Display Tab (`DisplayTab.tsx`) - icon style, button size, title, spacing, custom CSS
2. Placement Tab (`PlacementTab.tsx`) - auto-placement, position, post types, exclude pages
3. Advanced Tab (`AdvancedTab.tsx`) - Google Analytics, auto-hide, use_port, nofollow, caching, debug
4. Integrations Tab (`IntegrationsTab.tsx`) - BetterLinks, Elementor, Divi, Beaver Builder
5. Profiles Tab (`ProfilesTab.tsx`) - Custom profiles/presets
6. Shortcode Tab (`ShortcodeTab.tsx`) - Generate shortcodes and PHP snippets

**Where legacy options map to tabs:**
- `title`, `iconset` → Display Tab
- `excludes`, `show_left`, `show_right`, `show_before_post`, `show_after_post` → Placement Tab
- `icons` → Display Tab or network selector component
- `g_analytics`, `auto_hide_btn`, `use_port`, `nofollow` → Advanced Tab

### PHP Backend Components

**Admin interface:** `src/Admin/ReactAdminInterface.php`
- Registers submenu under Settings → "Social Share Settings"
- Page slug: `html-social-share-react`
- Enqueues React app bundle from `build/admin.js`
- Localizes `hssAdminConfig` with REST endpoints, nonces, plugin URL

**Settings management:** `src/Settings.php`
- Implements `SettingsInterface`
- Manages `hss_core`, `hss_profiles`, `hss_icons` options
- Provides dot-notation get/set (e.g., `get('appearance.title')`)
- Built-in validation and sanitization

**REST controller:** `src/Rest/SettingsController.php`
- Handles all REST API requests
- Validates/sanitizes input via schema (see `get_settings_schema()`)
- Delegates storage to `Settings` class

**Migration:** `src/Migration.php`
- Runs once on plugin activation or update
- Reads `zm_shbt_fld`, maps to `hss_core`, saves
- Sets migration flag to prevent re-run

### Implementation Tasks (Detailed)

#### Task 1: Verify Migration Coverage
**File:** `src/Migration.php`
**Action:** Ensure all legacy keys from the list above are properly migrated.
**Success criteria:** 
- All 12 legacy keys have explicit mapping logic
- Unit test confirms migration for each key
- Migrated data is validated against new schema

#### Task 2: Update REST Controller Schema
**File:** `src/Rest/SettingsController.php`
**Action:** Confirm `get_settings_schema()` includes fields for all legacy options.
**Success criteria:**
- `advanced.google_analytics`, `advanced.auto_hide_buttons`, `advanced.use_port_in_url`, `advanced.nofollow_links` exist in schema
- `appearance.icon_style` accepts iconset names
- `networks.enabled_networks` accepts array of network IDs

#### Task 3: Map UI Components to Legacy Options
**Files:** `src/admin-ui/components/tabs/*.tsx`
**Action:** Add/verify form controls for each legacy option in appropriate tabs.
**Success criteria:**
- Display Tab: title input, iconset selector, networks multi-select
- Placement Tab: exclude pages textarea, show_left/right/before/after checkboxes
- Advanced Tab: g_analytics, auto_hide_btn, use_port, nofollow checkboxes
- All inputs read from `hss_core` via REST and save back correctly

#### Task 4: Test Data Persistence
**Action:** Create E2E or integration test verifying legacy option values persist through save.
**Success criteria:**
- Set legacy option values via old admin (if available) or direct DB update
- Load new React admin
- Verify all values display correctly
- Change one value, save
- Verify only changed value updated, others unchanged

#### Task 5: Document Advanced Settings Location
**File:** Create `docs/advanced-settings-reference.md`
**Action:** Document what each advanced option does and where it lives in new UI.
**Success criteria:**
- List each legacy option with description
- Note which tab it's in
- Explain impact if disabled

### Edge Cases & Rollback

**Edge case: Iconset name mismatch**
- Legacy uses `iconset: "default"`, new system expects `icon_style: "default"`
- Migration must normalize names if iconsets were renamed
- Fallback: if `icon_style` not found in available iconsets, use first available

**Edge case: `show_in` nested array**
- Legacy schema has `show_in: { show_left: true, ... }`
- Migration flattens to `placement.floating_left`, etc.
- Reverse migration (if needed) reconstructs `show_in` from placement booleans

**Rollback strategy:**
- If user reverts plugin to pre-3.0, `zm_shbt_fld` still exists unchanged
- Archive code will use `zm_shbt_fld` as before
- No data loss even if `hss_*` options are cleared

### Testing Checklist

- [ ] Unit test: `Settings::get()` retrieves migrated legacy values
- [ ] Unit test: `Settings::set()` updates `hss_core` without affecting `zm_shbt_fld`
- [ ] Integration test: REST `/settings` endpoint returns all legacy fields in new structure
- [ ] Integration test: POST to `/settings` with legacy-equivalent data saves correctly
- [ ] E2E test: Load React admin, verify all 12 legacy options visible and editable
- [ ] E2E test: Change each option, save, reload admin, verify persistence
- [ ] E2E test: Generate shortcode with legacy options, verify output matches archive behavior

### Files Modified/Created Summary

**Modified:**
- `src/Migration.php` - Add/verify mappings for all 12 legacy keys
- `src/Rest/SettingsController.php` - Ensure schema covers legacy fields
- `src/admin-ui/components/tabs/DisplayTab.tsx` - Add title, iconset, icons controls
- `src/admin-ui/components/tabs/PlacementTab.tsx` - Add excludes, show_* controls
- `src/admin-ui/components/tabs/AdvancedTab.tsx` - Add g_analytics, auto_hide_btn, use_port, nofollow controls

**Created:**
- `docs/advanced-settings-reference.md` - User-facing documentation for advanced options
- `tests/php/unit/MigrationTest.php` - Unit tests for legacy → new migration
- `tests/php/integration/RestSettingsLegacyCompatTest.php` - Integration tests for REST API with legacy data
- `tests/e2e/legacy-options.spec.ts` - Playwright E2E tests for UI visibility and persistence

## Quick Start Guide for Developers

**To implement the first design release with full legacy compatibility:**

1. **Review the migration mapping table** (see "Mapping: Legacy → New Schema" above)
2. **Run the existing migration** (should be automatic on plugin update)
3. **Verify UI components exist** for all 12 legacy options in the React admin tabs
4. **Test data flow**: Legacy DB → REST API → React UI → Save → DB
5. **Run tests** (unit, integration, E2E) to confirm no data loss

**Priority order:**
1. Migration coverage (Task 1) - CRITICAL
2. UI component mapping (Task 3) - HIGH  
3. REST schema validation (Task 2) - HIGH
4. Testing & documentation (Tasks 4, 5) - MEDIUM

## Completion Status

✅ **Analysis complete:**
- All legacy option keys enumerated from `archive/settings_page.php` and `archive/html-social-share.php`
- New schema structure documented from `src/Settings.php` and `src/Rest/SettingsController.php`
- Migration strategy identified from `src/Migration.php`
- React admin UI structure mapped from `src/admin-ui/components/`

✅ **Backward compatibility strategy defined:**
- Legacy `zm_shbt_fld` option preserved (read-only for new system)
- New `hss_core` option as primary storage
- One-time migration from old to new schema
- Full mapping table provided for all 12 legacy keys

✅ **Implementation roadmap created:**
- 5 detailed tasks with files, actions, and success criteria
- Testing checklist with unit, integration, and E2E tests
- Files to modify/create listed explicitly
- Edge cases and rollback strategy documented

**This document is now ready for implementation.** Developers can follow the tasks sequentially to achieve full backward compatibility while launching the new design.

## Next Actions

**For developers:**
- Start with Task 1 (verify migration coverage in `src/Migration.php`)
- Cross-reference the mapping table against actual code
- Add missing controls to React admin UI tabs (Task 3)
- Write tests (Task 4) before making changes (TDD approach per coding agent instructions)

**For QA/testing:**
- Set up test WordPress site with legacy data (populate `zm_shbt_fld` with known values)
- Upgrade to new version and verify all settings visible in React admin
- Test save/load cycles for each option
- Verify frontend rendering matches legacy behavior

**For documentation:**
- Create `docs/advanced-settings-reference.md` with user-facing descriptions
- Update main README.md to reflect new admin UI
- Add migration notes to CHANGELOG.md
