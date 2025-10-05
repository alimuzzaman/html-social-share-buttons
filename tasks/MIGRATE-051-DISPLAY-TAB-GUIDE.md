# Display Tab Implementation Guide (MIGRATE-051)

## Goal
Replace the current complex DisplayTab with a simplified version that matches legacy v2.x behavior - just 4 placement checkboxes.

## Step-by-Step Implementation

### Step 1: Update Type Definition

**File:** `src/admin-ui/components/tabs/DisplayTab.tsx`

Replace the `DisplaySettings` type with:

```typescript
type LegacyDisplaySettings = Pick<
	PluginSettings,
	| 'floating_left'
	| 'floating_right'
	| 'before_content'
	| 'after_content'
>;
```

### Step 2: Update Default Settings

Replace `defaultDisplaySettings` with:

```typescript
const defaultLegacyDisplaySettings: LegacyDisplaySettings = {
	floating_left: true,   // Legacy default: show_left was true
	floating_right: false,
	before_content: false,
	after_content: true,   // Legacy default: show_after_post was true
};
```

### Step 3: Simplify Component State

Replace the `useState` and `useEffect` hooks to use only the 4 legacy fields:

```typescript
const [localSettings, setLocalSettings] = useState<LegacyDisplaySettings>(
	defaultLegacyDisplaySettings
);

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

### Step 4: Update Helper Functions

Keep only the simple `updateLocal` function:

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

**Remove:** `handlePostTypeToggle`, `selectedPostTypes` useMemo, and any other complex state management.

### Step 5: Simplify Render

Replace the entire render section with:

```tsx
return (
	<LoadingOverlay isLoading={saving} message="Saving display settings...">
		<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
			<h2 className="text-xl font-semibold text-gray-900 mb-2">
				Display & Placement
			</h2>
			<p className="text-sm text-gray-600 mb-6">
				Choose where social share buttons should appear on your site.
			</p>

			<div className="space-y-6">
				<div>
					<h3 className="text-lg font-medium text-gray-900 mb-4">
						Automatic Placement
					</h3>
					<p className="text-sm text-gray-600 mb-4">
						Enable automatic placement to display share buttons without editing templates.
					</p>

					<div className="space-y-4">
						<FormField 
							label="Show on Left Side" 
							description="Display floating share buttons on the left side of the page"
						>
							<Checkbox
								checked={localSettings.floating_left}
								onChange={(checked) => updateLocal('floating_left', checked)}
								label="Enable left floating buttons"
							/>
						</FormField>

						<FormField 
							label="Show on Right Side" 
							description="Display floating share buttons on the right side of the page"
						>
							<Checkbox
								checked={localSettings.floating_right}
								onChange={(checked) => updateLocal('floating_right', checked)}
								label="Enable right floating buttons"
							/>
						</FormField>

						<FormField 
							label="Show Before Post" 
							description="Display share buttons before post content"
						>
							<Checkbox
								checked={localSettings.before_content}
								onChange={(checked) => updateLocal('before_content', checked)}
								label="Enable before content placement"
							/>
						</FormField>

						<FormField 
							label="Show After Post" 
							description="Display share buttons after post content"
						>
							<Checkbox
								checked={localSettings.after_content}
								onChange={(checked) => updateLocal('after_content', checked)}
								label="Enable after content placement"
							/>
						</FormField>
					</div>
				</div>

				<div className="flex justify-end pt-6 border-t border-gray-200">
					<Button
						onClick={handleSave}
						disabled={saving}
						className="px-6 py-2"
					>
						{saving ? 'Saving...' : 'Save Settings'}
					</Button>
				</div>
			</div>
		</div>
	</LoadingOverlay>
);
```

### Step 6: Update Imports

Make sure imports include only what's needed:

```typescript
import React, { useEffect, useState } from 'react';
import {
	Button,
	Checkbox,
	FormField,
	LoadingOverlay,
} from '../ui';
import { PluginSettings } from '../../types';
import { useSettingsContext, useNotifications } from '../../contexts';
```

**Remove unused imports:** `Select`, `TextInput`, `useMemo`

## Complete File Structure

The final file should have this structure:

```
1. Imports (React, UI components, types, contexts)
2. Type Definition (LegacyDisplaySettings)
3. Default Settings (defaultLegacyDisplaySettings)
4. Component Function (DisplayTab)
   - Hooks (useSettingsContext, useNotifications, useState, useEffect)
   - Helper Functions (updateLocal, handleSave)
   - Render (JSX)
5. Export (export const DisplayTab)
```

## Testing Checklist

After implementation, verify:

- [ ] File compiles without TypeScript errors
- [ ] All 4 checkboxes render correctly
- [ ] Checking/unchecking updates local state
- [ ] Save button triggers handleSave
- [ ] Settings persist to backend
- [ ] No console errors in browser
- [ ] LoadingOverlay shows during save
- [ ] Success notification appears after save

## Common Pitfalls to Avoid

1. **Don't** keep any references to `show_on_front_page`, `show_on_posts`, etc.
2. **Don't** keep `auto_placement` or `placement_position` fields
3. **Don't** keep `selectedPostTypes` or `availablePostTypes`
4. **Don't** keep complex conditional rendering
5. **Do** keep the modern UI components (FormField, Checkbox, Button)
6. **Do** keep the LoadingOverlay pattern
7. **Do** maintain the notification system

## Related Files

- Original: `archive/settings_page.php` (reference for legacy behavior)
- Current: `src/admin-ui/components/tabs/DisplayTab.tsx` (file to update)
- Types: `src/admin-ui/types.ts` (may need to verify PluginSettings type)
- Migration Doc: `tasks/MIGRATION-PROGRESS.md` (track progress)

## Next Steps After Completion

1. Update todo list to mark MIGRATE-051 as complete
2. Test the Display tab in browser
3. Commit changes with message: `[MIGRATE-051] Simplify Display Tab to legacy 4-checkbox layout`
4. Update MIGRATION-PROGRESS.md with completion status
5. Move to MIGRATE-052 (Networks Tab verification)
