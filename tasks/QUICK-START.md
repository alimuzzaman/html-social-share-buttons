# Quick Start: Continue Legacy-First Migration

## Current Status
✅ Analysis complete | 🔄 Display Tab in progress | ⏳ 6 tasks remaining

## What You Need to Know

The legacy v2.x plugin had a simple admin interface with:
- 4 placement checkboxes (left, right, before, after)
- Basic settings (title, excludes, iconset)
- Advanced toggles (analytics, auto-hide, use port, nofollow)
- Individual network checkboxes

**Goal:** Restore this simple interface using modern React components (no old PHP code).

## Next Step: Complete Display Tab (2-3 hours)

### The Problem
Current DisplayTab.tsx has complex UI with many options. We need just 4 checkboxes.

### The Solution
Follow **`tasks/MIGRATE-051-DISPLAY-TAB-GUIDE.md`** step-by-step.

### Quick Implementation

1. **Open file:** `src/admin-ui/components/tabs/DisplayTab.tsx`

2. **Replace type definition** (lines ~13-23):
```typescript
type LegacyDisplaySettings = Pick<
	PluginSettings,
	| 'floating_left'
	| 'floating_right'
	| 'before_content'
	| 'after_content'
>;
```

3. **Replace defaults** (lines ~25-33):
```typescript
const defaultLegacyDisplaySettings: LegacyDisplaySettings = {
	floating_left: true,
	floating_right: false,
	before_content: false,
	after_content: true,
};
```

4. **Update useState** (around line ~57):
```typescript
const [localSettings, setLocalSettings] = useState<LegacyDisplaySettings>(
	defaultLegacyDisplaySettings
);
```

5. **Replace useEffect** (around line ~64):
```typescript
useEffect(() => {
	if (apiSettings) {
		setLocalSettings({
			floating_left: apiSettings.floating_left ?? defaultLegacyDisplaySettings.floating_left,
			floating_right: apiSettings.floating_right ?? defaultLegacyDisplaySettings.floating_right,
			before_content: apiSettings.before_content ?? defaultLegacyDisplaySettings.before_content,
			after_content: apiSettings.after_content ?? defaultLegacyDisplaySettings.after_content,
		});
	}
}, [apiSettings]);
```

6. **Update updateLocal** (keep this, change type):
```typescript
const updateLocal = <K extends keyof LegacyDisplaySettings>(
	key: K,
	value: LegacyDisplaySettings[K]
) => {
	setLocalSettings((prev) => ({
		...prev,
		[key]: value,
	}));
};
```

7. **Remove:** `handlePostTypeToggle`, `selectedPostTypes`, any references to `show_on_front_page`, `auto_placement`, etc.

8. **Replace entire render** with 4 simple checkboxes (see guide for complete JSX)

### Test
```bash
cd /Users/alim/Sites/git/html-social-share-buttons
pnpm run build
# Check for TypeScript errors
# Open in browser and test checkboxes
```

### Commit
```bash
git add src/admin-ui/components/tabs/DisplayTab.tsx
git commit -m "[MIGRATE-051] Simplify Display Tab to legacy 4-checkbox layout"
```

## After Display Tab is Done

### Quick Wins (30 min each)

**1. Hide Tabs** (`src/admin-ui/App.tsx`)
```typescript
const tabs: TabConfig[] = [
	{ id: 'display', ... },
	{ id: 'networks', ... },
	// Remove: { id: 'profiles', ... },
	{ id: 'design', ... },
	// Remove: { id: 'integrations', ... },
	{ id: 'shortcode', ... },
	{ id: 'advanced', ... },
];
```

**2. Verify Networks Tab**
- Open `src/admin-ui/components/tabs/NetworksTab.tsx`
- Check if vertical DnD exists (it does!)
- Test in browser
- Mark task complete

### Medium Tasks (2-3 hours each)

**3. Create Design Tab**
- Check if `DesignTab.tsx` exists
- If not, create with legacy options only
- Use FormField, TextInput, Select, Checkbox components

**4. Move Advanced Tab**
- `mv src/admin-ui/components/tabs/archived/AdvancedTab.tsx src/admin-ui/components/tabs/`
- Remove cache options
- Update imports

**5. Move Shortcode Tab**
- `mv src/admin-ui/components/tabs/archived/ShortcodeTab.tsx src/admin-ui/components/tabs/`
- Change `[html_social_share_buttons]` → `[zm_sh_btn]`
- Update parameters

## All Documentation

- **This file:** Quick start guide
- **`tasks/MIGRATION-SUMMARY.md`:** Complete overview & status
- **`tasks/MIGRATION-PROGRESS.md`:** Detailed progress tracker
- **`tasks/MIGRATE-051-DISPLAY-TAB-GUIDE.md`:** Step-by-step DisplayTab guide
- **`.github/prompts/legacy-first-migration.prompt.md`:** Original requirements
- **`docs/legacy.md`:** Legacy plugin analysis
- **`archive/settings_page.php`:** Original admin code (reference only)

## Commands

```bash
# Navigate to project
cd /Users/alim/Sites/git/html-social-share-buttons

# Install dependencies (if needed)
pnpm install

# Build (watch mode for development)
pnpm start

# Build for production
pnpm run build

# Lint
pnpm run lint

# Fix lint issues
pnpm run lint:fix

# Git status
git status

# Commit changes
git add <files>
git commit -m "[TASK-ID] Description"

# Push to GitHub (uses MCP tools per instructions)
# Don't use: git push
# Instead, use mcp_github_github_push_files tool
```

## Important Principles

1. **Don't bring back old PHP code** - Use new architecture with legacy settings
2. **Keep modern React UI** - FormField, Checkbox, Button, LoadingOverlay
3. **4 checkboxes for placement** - Simple, matches v2.x exactly
4. **Legacy settings, modern implementation** - Best of both worlds

## Quick Reference: Legacy Settings Map

| Legacy v2.x | New v3.0+ |
|-------------|-----------|
| show_left | floating_left |
| show_right | floating_right |
| show_before_post | before_content |
| show_after_post | after_content |
| g_analytics | google_analytics |
| auto_hide_btn | auto_hide_buttons |
| use_port | use_port_in_url |
| nofollow | nofollow_links |
| iconset | icon_style |

## Timeline

- **✅ Analysis:** 4 hours (DONE)
- **🔄 Display Tab:** 2-3 hours (IN PROGRESS)
- **⏳ Hide Tabs:** 1 hour
- **⏳ Verify Networks:** 1 hour
- **⏳ Design Tab:** 2-3 hours
- **⏳ Move Advanced:** 2 hours
- **⏳ Move Shortcode:** 2 hours
- **Total Remaining:** ~10-12 hours

## Success = Simple Admin UI

Legacy v2.x had a simple, clear admin interface. That's what we're restoring. Modern React implementation, legacy simplicity.

**You've got this!** 🚀

Start with DisplayTab using the detailed guide, then knock out the quick wins. The momentum will carry you through.
