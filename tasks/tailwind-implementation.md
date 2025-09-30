# Tailwind CSS Implementation Tasks

## Overview
This document outlines the tasks needed to fully implement Tailwind CSS in the HTML Social Share Buttons WordPress plugin admin dashboard.

## Current State Analysis

### ✅ Completed
- [x] Tailwind CSS v4.1.13 installed and configured
- [x] PostCSS configuration setup with @tailwindcss/postcss
- [x] Build process generating Tailwind utilities (27KB optimized CSS)
- [x] Basic admin CSS converted to use @import syntax
- [x] WordPress-compatible theme colors configured
- [x] Replace WordPress UI classes with Tailwind utilities across admin React app
- [x] Replace lucide-react icons with local inline SVG components for core admin UI
- [x] Replace Font Awesome references in admin UI with packaged icon assets or fallbacks
- [x] Replaced uses of `@wordpress/components` in admin UI with Tailwind-first components (Files: `AdminInterface.tsx`, `ShareCountsTable.tsx`, `RefreshControls.tsx`)
- [x] Standardized color tokens in `tailwind.config.js` to semantic names (`primary`, `muted`, `success`, `warning`, `danger`)

### 🔄 In Progress / Needs Review
- [ ] Some components use inline styles that could be Tailwind utilities
- [ ] CSS @apply directives could be replaced with direct className usage

## Implementation Tasks

### Phase 1: React Component Refactoring (High Priority)

#### Task 1.1: Update FormFields Components
**File**: `src/admin-ui/components/ui/FormFields.tsx`
**Current Issues**:
- Uses custom `wp-form-field`, `wp-text-input`, `wp-select` classes
- Mixes Tailwind classes with WordPress-specific classes
- Some color references use non-standard `text-wp-gray-600`

**Required Changes**:
- Replace `wp-form-field` with pure Tailwind layout classes
- Convert `wp-text-input` to standard Tailwind input styling
- Replace `wp-select` with Tailwind select styling
- Standardize color usage to Tailwind color palette
- Remove dependency on CSS @apply directives

#### Task 1.2: Update Notice Components
**File**: `src/admin-ui/components/ui/Notice.tsx`
**Current Issues**:
- Uses WordPress admin notice classes
- Limited to WordPress styling patterns

**Required Changes**:
- Create modern Tailwind-based notice components
- Implement consistent spacing and typography
- Add proper dismiss functionality with Tailwind transitions

#### Task 1.3: Update Tabs Component
**File**: `src/admin-ui/components/ui/Tabs.tsx`
**Current Issues**:
- Uses WordPress nav-tab classes
- Limited styling flexibility

**Required Changes**:
- Replace WordPress tab styling with modern Tailwind design
- Add proper focus states and accessibility
- Implement smooth transitions

**Network Icons**
- Prefer `lucide-react` for small, tree-shakable social network icons in the admin UI where possible. Fall back to:
  1. plugin iconset assets under `assets/iconset/<set>/<network>.png` (loaded via `hssAdminConfig.pluginUrl`), then
  2. an initial-letter placeholder when no icon asset is available.
- Implement this in `src/admin-ui/components/tabs/NetworksTab.tsx` and use the `AdminIcon` wrapper for other admin icons.

#### Task 1.4: Update Main Admin Interface
**File**: `src/admin-ui/components/ReactAdminInterface.tsx`
**Current Issues**:
- Uses WordPress-specific classes like `html-social-share-admin`
- Mixed styling approach

**Required Changes**:
- Convert to pure Tailwind layout system
- Implement responsive design patterns
- Standardize spacing and typography

### Phase 2: Remove CSS Dependencies (Medium Priority)

#### Task 2.1: Eliminate @apply Directives
**File**: `src/admin-ui/index.css`
**Current State**: Uses @apply to create WordPress-like components
**Target**: Move all styling to component-level className attributes

**Required Changes**:
- Remove all @apply directives from CSS
- Move component styling to React className props
- Ensure consistent design system implementation

#### Task 2.2: Create Design System Documentation
**New File**: `docs/design-system.md`
**Purpose**: Document standardized Tailwind patterns for the plugin

**Required Content**:
- Color palette mapping
- Typography scale
- Component patterns
- Spacing standards
- Interactive states (hover, focus, active)

### Phase 3: Enhanced User Experience (Low Priority)

#### Task 3.1: Add Loading States
**Files**: All form components
**Enhancement**: Implement proper loading states with Tailwind animations

#### Task 3.2: Improve Responsive Design
**Files**: All layout components
**Enhancement**: Ensure mobile-first responsive design

#### Task 3.3: Add Micro-interactions
**Files**: Interactive components
**Enhancement**: Subtle animations and transitions for better UX

## Technical Specifications

### Tailwind Configuration Requirements
Updated `tailwind.config.js` now contains semantic color tokens (primary, muted, success, warning, danger) and a system font family to reduce coupling to WordPress-specific tokens.

### Component Patterns

#### Standard Button Pattern
```tsx
<button className="px-4 py-2 bg-primary text-white rounded hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-colors">
  Button Text
</button>
```

#### Standard Input Pattern
```tsx
<input className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary" />
```

#### Standard Form Field Pattern
```tsx
<div className="space-y-2">
  <label className="block text-sm font-medium text-gray-700">
    Label Text
  </label>
  <input className="..." />
  <p className="text-sm text-gray-500">Description text</p>
</div>
```

## Testing Requirements

### Browser Testing
- [ ] Chrome/Chromium (latest 2 versions)
- [ ] Firefox (latest 2 versions)
- [ ] Safari (latest version)
- [ ] Edge (latest version)

### Responsive Testing
- [ ] Mobile (320px - 767px)
- [ ] Tablet (768px - 1023px)
- [ ] Desktop (1024px+)

### Accessibility Testing
- [ ] Keyboard navigation
- [ ] Screen reader compatibility
- [ ] Color contrast compliance (WCAG 2.1 AA)
- [ ] Focus management

## Performance Considerations

### CSS Bundle Size
- Current: ~27KB optimized
- Target: <30KB optimized
- Strategy: Use PurgeCSS effectively through Tailwind's content configuration

### Runtime Performance
- Minimize JavaScript bundle size
- Use React.memo() for expensive components
- Implement proper code splitting if needed

## Definition of Done

For each task to be considered complete:

1. ✅ **Code Quality**
   - No custom CSS classes except for WordPress compatibility
   - Consistent Tailwind utility usage
   - Proper TypeScript types maintained
   - ESLint and Prettier compliance

2. ✅ **Functionality**
   - All existing functionality preserved
   - No regressions in user experience
   - Proper error handling maintained

3. ✅ **Design**
   - Consistent with WordPress admin design language
   - Responsive design implemented
   - Accessibility standards met

4. ✅ **Testing**
   - Components render correctly
   - Form submissions work as expected
   - No console errors
   - Cross-browser compatibility verified

5. ✅ **Documentation**
   - Code comments updated
   - Component usage examples provided
   - Design system patterns documented

## Short Changelog (Tailwind conversion)
- Converted core admin components to Tailwind-first implementations: `AdminInterface.tsx`, `ShareCountsTable.tsx`, `RefreshControls.tsx`.
- Replaced `@wordpress/components` usage in admin UI with local Tailwind-based components and utilities.
- Standardized Tailwind color tokens in `tailwind.config.js`.

## Short PR Description (for reviewers)
This PR converts the admin React UI to a Tailwind-first implementation and removes direct dependencies on `@wordpress/components` for presentation. Files changed include:
- `src/admin-ui/components/AdminInterface.tsx` — replaces Panel/Card/Spinner/Notice with Tailwind-based containers and the local `Notice` / `LoadingSpinner` components.
- `src/admin-ui/components/ShareCountsTable.tsx` — removed `@wordpress/components` usage, replaced with local `Checkbox` and `Button` components and fixed icon imports.
- `src/admin-ui/components/RefreshControls.tsx` — replaced `CheckboxControl` with local `Checkbox`, fixed lucide icon imports and Button usage.
- `tailwind.config.js` — replaced WordPress-specific color tokens with semantic names.
- `tasks/tailwind-implementation.md` — updated to reflect progress and changes.

How to test locally:
1. Run `pnpm install` to ensure deps are present.
2. Start the dev server with `pnpm start` and open the plugin admin page in WordPress.
3. Go to Social Share plugin admin screens and verify the UI loads, tabs work, and share counts can be refreshed.
4. Run `pnpm run build` to validate there are no TypeScript or CSS build errors.

If you want me to continue by converting more files (e.g., replace remaining `@wordpress/components` usages across the repo or update design docs), I can continue in the next change set.