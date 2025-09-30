# Admin UI Components

Modern React/TypeScript components with pure Tailwind CSS for the HTML Social Share Buttons WordPress plugin.

## Components

### Notice Component

A modern, accessible notice component with multiple types and smooth animations.

#### Features

- **4 Notice Types**: Success, Warning, Error, Info
- **Pure Tailwind CSS**: No custom CSS classes
- **Icons**: Built-in SVG icons for each type
- **Dismissible**: Optional dismiss functionality with smooth fade-out
- **Accessibility**: 
  - ARIA live regions for screen reader announcements
  - Assertive announcements for errors
  - Polite announcements for other types
  - Screen reader text for dismiss buttons
- **Smooth Transitions**: 300ms ease-in-out animations
- **Modern Design**: Border-left accent with background colors matching notice type

#### Props

```typescript
interface NoticeProps {
  type: 'success' | 'warning' | 'error' | 'info';
  message: string;
  dismissible?: boolean;
  onDismiss?: () => void;
}
```

#### Usage Examples

```tsx
// Success notice with dismiss
<Notice
  type="success"
  message="Settings saved successfully!"
  dismissible
  onDismiss={() => console.log('Dismissed')}
/>

// Non-dismissible error notice
<Notice
  type="error"
  message="Unable to connect to server."
/>

// Warning notice
<Notice
  type="warning"
  message="This action cannot be undone."
  dismissible
  onDismiss={handleWarningDismiss}
/>

// Info notice
<Notice
  type="info"
  message="Remember to save your changes."
/>
```

#### Notice Type Colors

| Type    | Border Color      | Background Color |
|---------|-------------------|------------------|
| Success | `border-green-400`| `bg-green-50`    |
| Warning | `border-yellow-400`| `bg-yellow-50`  |
| Error   | `border-red-400`  | `bg-red-50`      |
| Info    | `border-blue-400` | `bg-blue-50`     |

### LoadingSpinner Component

A flexible loading spinner with multiple sizes and optional message display.

#### Features

- **4 Sizes**: Small (sm), Medium (md), Large (lg), Extra Large (xl)
- **Standard Colors**: Uses Tailwind's `text-blue-600` and `text-gray-600`
- **Optional Message**: Display loading message below spinner
- **Accessibility**: Hidden "Loading..." text for screen readers
- **Smooth Animation**: CSS-based spin animation
- **Customizable**: Additional className support

#### Props

```typescript
interface LoadingSpinnerProps {
  size?: 'sm' | 'md' | 'lg' | 'xl';
  message?: string;
  className?: string;
}
```

#### Usage Examples

```tsx
// Default medium spinner
<LoadingSpinner />

// Large spinner with message
<LoadingSpinner 
  size="lg" 
  message="Loading your data..."
/>

// Small inline spinner
<button disabled>
  <LoadingSpinner size="sm" />
  <span className="ml-2">Processing...</span>
</button>

// Extra large spinner for full page loading
<div className="min-h-screen flex items-center justify-center">
  <LoadingSpinner 
    size="xl" 
    message="Initializing application..."
  />
</div>
```

#### Spinner Sizes

| Size | CSS Class | Dimensions |
|------|-----------|------------|
| sm   | `h-4 w-4` | 16x16px    |
| md   | `h-8 w-8` | 32x32px    |
| lg   | `h-12 w-12`| 48x48px   |
| xl   | `h-16 w-16`| 64x64px   |

## Color Migration

### Before (Custom WordPress Colors)

```tsx
// Old LoadingSpinner colors
text-wp-blue-600  // Custom color
text-wp-gray-600  // Custom color
```

### After (Standard Tailwind Colors)

```tsx
// New standard Tailwind colors
text-blue-600  // Standard Tailwind blue
text-gray-600  // Standard Tailwind gray
```

## Installation

```bash
# Install dependencies
npm install

# Start development server
npm run dev

# Build for production
npm run build

# Type check
npm run type-check
```

## Component Demo

See `ComponentDemo.tsx` for a comprehensive demonstration of all component features and variations.

## Accessibility Compliance

All components follow WCAG 2.1 AA standards:

- ✅ Proper ARIA attributes
- ✅ Screen reader support
- ✅ Keyboard navigation
- ✅ Focus indicators
- ✅ Color contrast requirements
- ✅ Semantic HTML

## Browser Support

- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Modern mobile browsers

## Related Issues

- Parent Issue: #4 - Complete Tailwind CSS Implementation for Admin Dashboard
