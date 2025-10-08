# Design System Documentation

This document outlines the standardized Tailwind CSS patterns and design system used throughout the HTML Social Share Buttons WordPress plugin admin interface.

## Overview

The plugin uses a pure Tailwind CSS implementation without custom CSS classes or @apply directives. All styling is applied directly through component-level `className` attributes for maximum maintainability and consistency.

## Color Palette

### Primary Colors
- **Primary Blue**: `blue-600` (`#2563eb`) - Used for primary buttons, active states, and links
- **Primary Blue Hover**: `blue-700` (`#1d4ed8`) - Hover state for primary elements

### Semantic Colors
- **Success**: `green-500` (`#10b981`) - Success states, confirmations
- **Warning**: `yellow-500` (`#eab308`) - Warning messages, cautions
- **Error**: `red-500` (`#ef4444`) - Error states, destructive actions
- **Info**: `blue-500` (`#3b82f6`) - Informational messages

### Neutral Colors
- **Text Primary**: `gray-900` (`#111827`) - Primary text content
- **Text Secondary**: `gray-600` (`#4b5563`) - Secondary text, descriptions
- **Text Tertiary**: `gray-500` (`#6b7280`) - Placeholder text, muted content
- **Border**: `gray-300` (`#d1d5db`) - Default borders
- **Border Light**: `gray-200` (`#e5e7eb`) - Subtle borders
- **Background**: `white` (`#ffffff`) - Primary background
- **Background Secondary**: `gray-50` (`#f9fafb`) - Secondary backgrounds, subtle sections

## Typography

### Font Family
- **Base**: System font stack (`font-sans`) - Consistent with WordPress admin

### Text Sizes
- **Heading 1**: `text-3xl font-bold` - Main page titles
- **Heading 2**: `text-xl font-semibold` - Section headings
- **Heading 3**: `text-lg font-medium` - Subsection headings
- **Body Large**: `text-lg` - Large body text
- **Body**: `text-base` - Standard body text
- **Body Small**: `text-sm` - Small text, descriptions
- **Caption**: `text-xs` - Captions, metadata

### Font Weights
- **Bold**: `font-bold` - Strong emphasis
- **Semibold**: `font-semibold` - Section headings
- **Medium**: `font-medium` - Subsection headings, labels
- **Normal**: `font-normal` - Body text
- **Light**: `font-light` - Subtle text (rarely used)

## Spacing

### Margin/Padding Scale
- **xs**: `0.25rem` (`4px`) - Tight spacing
- **sm**: `0.5rem` (`8px`) - Small spacing
- **md**: `1rem` (`16px`) - Medium spacing (default)
- **lg**: `1.5rem` (`24px`) - Large spacing
- **xl**: `2rem` (`32px`) - Extra large spacing
- **2xl**: `3rem` (`48px`) - Double extra large

### Layout Spacing
- **Container**: `max-w-7xl mx-auto px-4 sm:px-6 lg:px-8` - Main container with responsive padding
- **Card Padding**: `p-6` - Standard card content padding
- **Form Spacing**: `space-y-4` - Vertical spacing between form elements
- **Grid Gaps**: `gap-4`, `gap-6` - Grid and flexbox spacing

## Component Patterns

### Buttons

#### Primary Button
```tsx
<button className="px-4 py-2 bg-blue-600 text-white rounded font-medium transition-colors hover:bg-blue-700 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-blue-400">
  Button Text
</button>
```

#### Secondary Button
```tsx
<button className="px-4 py-2 bg-white border border-gray-300 text-gray-900 rounded font-medium transition-colors hover:bg-gray-50 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:bg-gray-100 disabled:text-gray-400">
  Button Text
</button>
```

#### Tertiary Button
```tsx
<button className="px-4 py-2 bg-transparent text-blue-600 rounded font-medium transition-colors hover:bg-blue-50 focus:outline-none focus:ring-1 focus:ring-blue-500 disabled:text-blue-400">
  Button Text
</button>
```

### Form Fields

#### Form Field Container
```tsx
<div className="mb-4">
  <label className="block text-sm font-medium text-gray-700 mb-1">
    Label Text
  </label>
  <input className="..." />
  <p className="text-sm text-gray-500 mt-1">Description text</p>
</div>
```

#### Text Input
```tsx
<input className="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
```

#### Select
```tsx
<select className="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
  <option>Option 1</option>
</select>
```

#### Checkbox
```tsx
<label className="flex items-center cursor-pointer">
  <input type="checkbox" className="mr-2" />
  <span>Checkbox label</span>
</label>
```

### Cards

#### Basic Card
```tsx
<div className="bg-white border border-gray-200 rounded shadow-sm p-6">
  Card content
</div>
```

#### Card with Header
```tsx
<div className="bg-white border border-gray-200 rounded shadow-sm">
  <div className="px-6 py-4 border-b border-gray-200">
    <h3 className="text-lg font-medium">Card Title</h3>
  </div>
  <div className="p-6">
    Card content
  </div>
</div>
```

### Notices/Alerts

#### Success Notice
```tsx
<div className="p-4 border-l-4 border-green-500 bg-green-50 rounded-r shadow-sm">
  <p className="text-sm font-medium">Success message</p>
</div>
```

#### Error Notice
```tsx
<div className="p-4 border-l-4 border-red-500 bg-red-50 rounded-r shadow-sm">
  <p className="text-sm font-medium">Error message</p>
</div>
```

#### Warning Notice
```tsx
<div className="p-4 border-l-4 border-yellow-500 bg-yellow-50 rounded-r shadow-sm">
  <p className="text-sm font-medium">Warning message</p>
</div>
```

#### Info Notice
```tsx
<div className="p-4 border-l-4 border-blue-500 bg-blue-50 rounded-r shadow-sm">
  <p className="text-sm font-medium">Info message</p>
</div>
```

### Tabs

#### Tab Navigation
```tsx
<nav className="border-b border-gray-200 mb-6">
  <div className="flex space-x-1">
    <button className="px-4 py-2 text-sm font-medium rounded-t-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-blue-600 bg-blue-50 border-b-2 border-blue-600">
      Active Tab
    </button>
    <button className="px-4 py-2 text-sm font-medium rounded-t-md transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 border-b-2 border-transparent">
      Inactive Tab
    </button>
  </div>
</nav>
```

#### Tab Panel
```tsx
<div className="mt-6">
  Tab content
</div>
```

## Layout Patterns

### Main Container
```tsx
<div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
  Content
</div>
```

### Grid Layouts
```tsx
{/* Two column grid */}
<div className="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div>Column 1</div>
  <div>Column 2</div>
</div>

{/* Three column grid */}
<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <div>Column 1</div>
  <div>Column 2</div>
  <div>Column 3</div>
</div>
```

### Flexbox Layouts
```tsx
{/* Space between */}
<div className="flex justify-between items-center">
  <div>Left content</div>
  <div>Right content</div>
</div>

{/* Center content */}
<div className="flex items-center justify-center">
  <div>Centered content</div>
</div>
```

## Interactive States

### Focus States
- **Default**: `focus:outline-none focus:ring-1 focus:ring-blue-500 focus:ring-offset-2`
- **Button Focus**: `focus:outline-none focus:ring-1 focus:ring-blue-500`

### Hover States
- **Buttons**: `hover:bg-blue-700` (primary), `hover:bg-gray-50` (secondary)
- **Links**: `hover:text-blue-700`
- **Cards**: `hover:shadow-md` (optional, for interactive cards)

### Disabled States
- **Buttons**: `disabled:bg-blue-400 disabled:cursor-not-allowed` (primary)
- **Inputs**: `disabled:bg-gray-100 disabled:cursor-not-allowed`

## Responsive Design

### Breakpoints
- **sm**: `640px` and up
- **md**: `768px` and up
- **lg**: `1024px` and up
- **xl**: `1280px` and up

### Responsive Patterns
```tsx
{/* Responsive container */}
<div className="px-4 sm:px-6 lg:px-8">

{/* Responsive grid */}
<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3">

{/* Responsive text */}
<h1 className="text-2xl sm:text-3xl lg:text-4xl">
```

## Accessibility

### Focus Management
- All interactive elements have proper focus states
- Focus ring uses `focus:ring-blue-500` with appropriate contrast
- Tab order follows logical content flow

### Color Contrast
- Text on background meets WCAG 2.1 AA standards
- Focus indicators are clearly visible
- Error states use high-contrast colors

### Semantic HTML
- Proper heading hierarchy (h1, h2, h3)
- Semantic form elements with labels
- ARIA attributes where needed for screen readers

## Implementation Guidelines

### Class Name Ordering
1. Layout classes (`flex`, `grid`, `block`, etc.)
2. Spacing classes (`m-*`, `p-*`, `space-*`)
3. Sizing classes (`w-*`, `h-*`)
4. Typography classes (`text-*`, `font-*`)
5. Background classes (`bg-*`)
6. Border classes (`border-*`)
7. Interactive classes (`hover:*`, `focus:*`, `disabled:*`)
8. Animation classes (`transition-*`)

### Consistency Rules
- Always use the defined color palette
- Maintain consistent spacing using the spacing scale
- Use semantic color names over arbitrary values
- Prefer utility classes over custom CSS
- Keep component className attributes readable and maintainable

### Performance Considerations
- Leverage Tailwind's purging to minimize CSS bundle size
- Use consistent patterns to maximize CSS reuse
- Avoid arbitrary values when possible
- Keep component styles co-located with components</content>
<parameter name="filePath">/Users/alim/Sites/git/html-social-share-buttons/docs/design-system.md