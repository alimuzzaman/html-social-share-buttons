---
mode: agent
---
# Phase M: Legacy-First Dashboard & Rendering Migration

## 📚 **READ FIRST**
Before starting any work, read and understand:
- `docs/legacy.md` - Complete analysis of legacy plugin architecture and features
- `archive/settings_page.php` - Original admin interface structure
- `archive/form.php` - Legacy form system and toggle switches
- `src/Compatibility.php` - Existing compatibility layer

## 🎯 **Critical Priority: Legacy-First Approach**

### 🔥 **MUST DO FIRST: Bring Back All Original Settings/Options**
The dashboard should **show all legacy options** using new React components and organization methods. Don't bring back old PHP code - use new architecture with legacy settings.

**Legacy Settings to Restore:**
- All original placement options (show_left, show_right, show_before_post, show_after_post)
- All original network checkboxes and ordering
- All original design options (title, excludes, iconset, etc.)
- All original advanced options (nofollow, use_port, etc.)
- All original shortcode options

**New Architecture Benefits:**
- Use new React components for better UX
- Use new data management methods
- Use new rendering engine for frontend
- Keep wrapper functions for backward compatibility

### ⚡ **Frontend Rendering Migration**

**Use New Render Engine, Render Like Legacy:**
- Don't use LegacyButtonRenderer()
- Use new render methods to render HTML in new format for frontend
- No need to match legacy HTML structure exactly
- Create wrapper functions for old global functions to use new methods

**Iconset System:**
- New icons will also be put into `assets/iconset/*`
- Generate separate CSS files for each iconset
- Use new rendering system with legacy iconset compatibility

### 🌟 **React App Enhancements**

**Modern UI with Legacy Options:**
- Keep all legacy settings but organize them better
- Use new components for improved user experience
- Maintain backward compatibility through wrapper functions

## 🎯 **Success Criteria**

1. ✅ Dashboard shows all legacy options using new React components
2. ✅ Frontend uses new render engine with modern HTML structure
3. ✅ All legacy settings work identically to v2.x
4. ✅ Wrapper functions maintain backward compatibility
5. ✅ Iconsets work with new system and separate CSS files
6. ✅ No old code brought back - only new architecture with legacy settings

## 📋 **Ultra-Granular Task Breakdown**

### Phase MA: Restore Legacy Settings Structure (6-8 hours)

#### MIGRATE-001: Analyze Legacy Settings from Archive (2 hours) - ❌ NOT STARTED
- **Task**: Document all legacy settings from archive/settings_page.php
- **Files**: `archive/settings_page.php`, `archive/form.php`
- **Document**: All form fields, checkboxes, dropdowns, textareas
- **Status**: ❌ NOT STARTED

#### MIGRATE-002: Create Legacy Options Schema (2 hours) - ❌ NOT STARTED
- **Task**: Define schema for all legacy options in new system
- **Files**: `src/Options/LegacyOptionsSchema.php`
- **Include**: All original settings with new validation
- **Status**: ❌ NOT STARTED

#### MIGRATE-003: Migrate Existing User Settings (2 hours) - ❌ NOT STARTED
- **Task**: Migrate current user settings to include all legacy options
- **Files**: `src/Options/Migration.php`
- **Ensure**: No data loss, backward compatibility
- **Status**: ❌ NOT STARTED

### Phase MB: Update Dashboard Tabs (12-16 hours)

#### MIGRATE-051: Update Display Tab (4 hours) - ❌ NOT STARTED
- **Task**: Keep Automatic Placement, make Placement Position multi-select
- **Files**: `src/admin-ui/components/tabs/DisplayTab.tsx`
- **Changes**:
  - Keep Automatic Placement section
  - Convert placement checkboxes to multi-select dropdown
  - Migrate existing users from 4 checkboxes to multi-select
  - Hide/archive Display Locations section
- **Status**: ❌ NOT STARTED

#### MIGRATE-052: Update Networks Tab (4 hours) - ❌ NOT STARTED
- **Task**: Merge Available Networks and Network Order with vertical DnD
- **Files**: `src/admin-ui/components/tabs/NetworksTab.tsx`
- **Changes**:
  - Merge Available Networks and Network Order into single tab
  - Make network list vertical with drag-and-drop reordering
  - Input takes most of the space
  - Input doesn't toggle the input state (add margin to prevent accidental toggle)
- **Status**: ❌ NOT STARTED

#### MIGRATE-053: Hide Profiles Tab (1 hour) - ❌ NOT STARTED
- **Task**: Hide/archive the Profiles tab from dashboard
- **Files**: `src/admin-ui/components/ReactAdminInterface.tsx`
- **Action**: Remove profiles tab from navigation and configuration
- **Status**: ❌ NOT STARTED

#### MIGRATE-054: Update Design Tab (3 hours) - ❌ NOT STARTED
- **Task**: Keep only original options, hide/archive new options
- **Files**: `src/admin-ui/components/tabs/DesignTab.tsx`
- **Keep**: Title, Excludes, Iconset dropdown, Google Analytics, Auto hide, Use port, Nofollow
- **Hide**: All new design options added in recent versions
- **Status**: ❌ NOT STARTED

#### MIGRATE-055: Hide Integrations Tab (1 hour) - ❌ NOT STARTED
- **Task**: Hide/archive the Integrations tab from dashboard
- **Files**: `src/admin-ui/components/ReactAdminInterface.tsx`
- **Action**: Remove integrations tab from navigation and configuration
- **Status**: ❌ NOT STARTED

#### MIGRATE-056: Update Advanced Tab (3 hours) - ❌ NOT STARTED
- **Task**: Keep original options, hide new options, remove cache options
- **Files**: `src/admin-ui/components/tabs/AdvancedTab.tsx`
- **Keep**: Original advanced options from legacy
- **Hide**: All new advanced options
- **Remove**: All cache-related options
- **Status**: ❌ NOT STARTED

#### MIGRATE-057: Update Shortcode Tab (2 hours) - ❌ NOT STARTED
- **Task**: Keep shortcode tab with only original options
- **Files**: `src/admin-ui/components/tabs/ShortcodeTab.tsx`
- **Keep**: Only original shortcode options from legacy
- **Remove**: Any new shortcode features
- **Status**: ❌ NOT STARTED

### Phase MC: Frontend Rendering Migration (8-12 hours)

#### MIGRATE-101: Update Render Engine (4 hours) - ❌ NOT STARTED
- **Task**: Use new render methods instead of LegacyButtonRenderer
- **Files**: `src/Frontend/ButtonRenderer.php`
- **Changes**:
  - Don't use LegacyButtonRenderer()
  - Use new render methods for modern HTML structure
  - No need to match legacy HTML exactly
- **Status**: ❌ NOT STARTED

#### MIGRATE-102: Create Wrapper Functions (4 hours) - ❌ NOT STARTED
- **Task**: Create wrapper functions for backward compatibility
- **Files**: `src/Compatibility/LegacyWrappers.php`
- **Wrap**: Old global functions to use new methods
- **Ensure**: Other plugins can keep using old function names
- **Status**: ❌ NOT STARTED

#### MIGRATE-103: Update Iconset System (4 hours) - ❌ NOT STARTED
- **Task**: Generate separate CSS files for each iconset
- **Files**: `src/Iconset/IconsetGenerator.php`, `assets/iconset/`
- **Changes**:
  - New icons go to `assets/iconset/*`
  - Generate separate CSS files for each iconset
  - Use new rendering system
- **Status**: ❌ NOT STARTED

### Phase MD: Testing & Validation (4-6 hours)

#### MIGRATE-151: Test Legacy Compatibility (2 hours) - ❌ NOT STARTED
- **Task**: Verify all legacy settings work with new architecture
- **Test**: All restored options function correctly
- **Validate**: No breaking changes for existing users
- **Status**: ❌ NOT STARTED

#### MIGRATE-152: Test Wrapper Functions (2 hours) - ❌ NOT STARTED
- **Task**: Verify wrapper functions maintain backward compatibility
- **Test**: Old function calls work with new methods
- **Validate**: Third-party plugins continue working
- **Status**: ❌ NOT STARTED

#### MIGRATE-153: Update Documentation (2 hours) - ❌ NOT STARTED
- **Task**: Update docs to reflect legacy-first approach
- **Files**: `docs/legacy-migration.md`
- **Document**: New architecture with legacy settings
- **Status**: ❌ NOT STARTED

## 📊 **Phase Summary**

- **Total Tasks**: 15 granular tasks across 4 sub-phases
- **Estimated Hours**: 30-42 hours
- **Key Deliverables**: Legacy settings in new React UI, modern rendering, wrapper compatibility, separate iconset CSS
- **Success Metrics**: All legacy options available, new architecture used, backward compatibility maintained

## 📝 **Implementation Notes**

- **Atomic Commits**: Each task = one commit with CHANGELOG update
- **Testing**: Test each legacy setting against original behavior
- **Documentation**: Update docs with migration progress
- **Wrapper Functions**: Ensure old global functions work with new methods
- **Iconsets**: Generate separate CSS files for each iconset in assets/iconset/
- **Settings Migration**: Migrate existing users without data loss</content>
<parameter name="filePath">/Users/alim/Sites/git/html-social-share-buttons/.github/prompts/legacy-first-migration.prompt.md