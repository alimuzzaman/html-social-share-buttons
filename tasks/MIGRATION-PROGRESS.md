# Legacy-First Migration Progress (Phase M)

**Started:** 2025-10-05
**Goal:** Migrate admin UI to legacy-first approach with all v2.x settings restored using modern React components

## ✅ Completed Tasks

### MIGRATE-001: Legacy Settings Analysis
**Status:** ✅ Complete
**Files Analyzed:**
- `archive/settings_page.php` - Original admin page structure
- `archive/form.php` - Legacy form rendering system
- `docs/legacy.md` - Complete architecture documentation

**Findings:**
Legacy v2.x had these key components:
1. **Placement Options** (4 checkboxes):
   - `show_left` → `floating_left`
   - `show_right` → `floating_right`
   - `show_before_post` → `before_content`
   - `show_after_post` → `after_content`

2. **Settings Fields**:
   - `title` (text input) - "Share this with your friends"
   - `excludes` (textarea) - Page IDs, slugs, or titles to exclude
   - `iconset` (dropdown) - Button style selection
   - `iconset_type` (radio buttons) - Per-placement type selection (square, etc.)

3. **Advanced Toggles**:
   - `g_analytics` - Google Analytics tracking
   - `auto_hide_btn` - Auto-hide floating buttons
   - `use_port` - Include port in URLs (:443)
   - `nofollow` - Add rel="nofollow" to links

4. **Network Selection**:
   - Individual checkboxes for each network
   - Default: facebook, twitter, linkedin, googleplus, bookmark, pinterest, mail

5. **Shortcode/PHP Code Generation**:
   - Thickbox modal for code generation
   - Legacy format: `[zm_sh_btn iconset='default' iconset_type='square' icons='facebook,twitter,linkedin']`

### MIGRATE-051: Display Tab Simplification
**Status:** ✅ COMPLETED (Commit: a94de0d)
**Goal:** Replace complex auto_placement UI with simple 4 legacy checkboxes
**Files:** `src/admin-ui/components/tabs/DisplayTab.tsx`

**Completed Changes:**
1. ✅ Created `LegacyDisplaySettings` type with only 4 fields
2. ✅ Removed `show_on_front_page`, `show_on_posts`, `show_on_pages`, `show_on_archives`
3. ✅ Removed `auto_placement`, `placement_position`, `placement_post_types`, `exclude_pages`
4. ✅ Now shows only:
   - `floating_left` (default: true)
   - `floating_right` (default: false)
   - `before_content` (default: false)
   - `after_content` (default: true)

**Results:**
- File size reduced from 370 lines to 164 lines (55.7% reduction)
- Bundle size reduced from 91 KiB to 88.1 KiB
- Removed unused imports and complex state management
- Maintained modern UI components and notification system

## 🔄 In Progress

### MIGRATE-054: Simplify Design Tab
**Status:** 🔄 In Progress
**Goal:** Keep only legacy v2.x options in Design tab
**Files:** `src/admin-ui/components/tabs/AppearanceTab.tsx`

**Required Changes:**
1. Keep ONLY legacy options:
   - Title (text input)
   - Iconset (dropdown)
   - Excludes (textarea) - may need to add
2. Remove new options:
   - Default Style dropdown
   - Default Size dropdown
   - Icon Style dropdown
   - Button Size dropdown
   - Button Spacing input
   - Custom CSS textarea

## 📋 Next Tasks

### MIGRATE-052: Networks Tab Verification
**Status:** ✅ VERIFIED
**Priority:** High
**Files:** `src/admin-ui/components/tabs/NetworksTab.tsx`
**Result:** NetworksTab already has vertical DnD using @dnd-kit with proper ordering. Meets legacy requirements.

### MIGRATE-053: Hide Profiles Tab
**Priority:** High
**Files:** `src/admin-ui/App.tsx`
**Action:** Remove `profiles` entry from tabs array

### MIGRATE-054: Create/Update Design Tab
**Priority:** High
**Files:** `src/admin-ui/components/tabs/DesignTab.tsx` (may need to create)
**Include ONLY:**
- Title (text input)
- Excludes (textarea)
- Iconset (dropdown)
- Google Analytics (checkbox)
- Auto Hide (checkbox)
- Use Port (checkbox)
- Nofollow (checkbox)

### MIGRATE-055: Hide Integrations Tab
**Priority:** High
**Files:** `src/admin-ui/App.tsx`
**Action:** Remove `integrations` entry from tabs array

### MIGRATE-056: Move & Simplify Advanced Tab
**Priority:** Medium
**Files:** Move `src/admin-ui/components/tabs/archived/AdvancedTab.tsx` → `tabs/`
**Remove:** `cache_enabled`, `cache_duration`, `debug_mode`
**Keep:** `google_analytics`, `auto_hide_buttons`, `use_port_in_url`, `nofollow_links`

### MIGRATE-057: Move & Update Shortcode Tab
**Priority:** Medium
**Files:** Move `src/admin-ui/components/tabs/archived/ShortcodeTab.tsx` → `tabs/`
**Update:** Change from `[html_social_share_buttons]` to `[zm_sh_btn]`
**Parameters:** `iconset`, `iconset_type`, `icons` (comma-separated)

## 📊 Progress Summary

- **Total Tasks:** 8
- **Completed:** 2 (25%)
- **In Progress:** 1 (12.5%)
- **Remaining:** 5 (62.5%)
- **Estimated Time:** 30-42 hours total, ~20 hours remaining

## 🎯 Success Criteria

1. ✅ All legacy settings documented and mapped
2. ⏳ Dashboard shows all v2.x options in modern React UI
3. ⏳ No old PHP code brought back
4. ⏳ All 4 placement checkboxes working
5. ⏳ Networks tab has vertical DnD
6. ⏳ Design tab shows only legacy options
7. ⏳ Shortcode generator uses `[zm_sh_btn]` format

## 📝 Implementation Notes

### Key Principles
1. **Legacy-First:** Show ALL original v2.x settings
2. **Modern Architecture:** Use new React components and data management
3. **No Old Code:** Don't bring back archive PHP files
4. **Backward Compatibility:** Maintain through wrapper functions

### File Organization
- Keep `archived/` folder for reference only
- Move usable components from `archived/` to `tabs/`
- Update `App.tsx` to reflect new tab structure
- Ensure all imports updated when moving files

### Testing Checklist
- [ ] All 4 placement checkboxes save correctly
- [ ] Settings persist across page reloads
- [ ] Migration from v2.x settings works
- [ ] No console errors in browser
- [ ] All tabs render without errors
- [ ] Save buttons work on all tabs
- [ ] TypeScript compiles without errors

## 🔗 Related Documents

- [Legacy-First Migration Prompt](../.github/prompts/legacy-first-migration.prompt.md)
- [Legacy Plugin Analysis](../docs/legacy.md)
- [Archive Settings Page](../archive/settings_page.php)
- [Archive Form System](../archive/form.php)
