# FormFields Component Documentation

## Overview

The FormFields components have been migrated from custom `wp-*` classes to pure Tailwind CSS utilities. This migration eliminates the dependency on custom CSS classes and provides a more maintainable, consistent design system.

## Components

### FormField

A wrapper component that provides consistent styling for form fields including labels, error messages, and descriptions.

**Props:**
- `label` (string, required): The label text for the form field
- `children` (ReactNode, required): The input element(s) to be wrapped
- `required` (boolean, optional): Whether the field is required (shows red asterisk)
- `error` (string, optional): Error message to display
- `description` (string, optional): Helper text to display below the input
- `className` (string, optional): Additional CSS classes to apply

**Example:**
```tsx
<FormField
  label="Website Title"
  required
  description="Enter the title of your website"
  error="This field is required"
>
  <TextInput type="text" placeholder="My Website" />
</FormField>
```

### TextInput

A styled text input component using pure Tailwind CSS.

**Props:**
Extends all standard HTML input attributes plus:
- `className` (string, optional): Additional CSS classes to apply

**Example:**
```tsx
<TextInput
  type="email"
  placeholder="user@example.com"
  required
/>
```

### Select

A styled select dropdown component using pure Tailwind CSS.

**Props:**
Extends all standard HTML select attributes plus:
- `className` (string, optional): Additional CSS classes to apply
- `children` (ReactNode, required): Option elements

**Example:**
```tsx
<Select defaultValue="flat">
  <option value="default">Default</option>
  <option value="flat">Flat</option>
  <option value="long-shadow">Long Shadow</option>
</Select>
```

## Migration Changes

### Before (Custom wp-* Classes)

```tsx
// Old FormField structure
<div className={`wp-form-field ${className} ${error ? 'has-error' : ''}`}>
  <div className="wp-form-field-label">
    <label className="form-label">
      {label}
      {required && <span className="required text-red-500 ml-1">*</span>}
    </label>
  </div>
  <div className="wp-form-field-input">
    {children}
  </div>
</div>

// Old TextInput
<input className={`wp-text-input ${className}`} />

// Old Select
<select className={`wp-select ${className}`}>
```

### After (Pure Tailwind CSS)

```tsx
// New FormField structure
<div className={`space-y-2 ${className} ${error ? 'has-error' : ''}`}>
  <label className="block text-sm font-medium text-gray-700">
    {label}
    {required && <span className="text-red-500 ml-1">*</span>}
  </label>
  <div className="w-full">
    {children}
  </div>
</div>

// New TextInput
<input className={`w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 ${className}`} />

// New Select
<select className={`w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 ${className}`}>
```

## Color Standardization

All custom color references have been replaced with standard Tailwind colors:

| Old Class | New Class |
|-----------|-----------|
| `text-wp-gray-600` | `text-gray-600` |
| `text-wp-gray-700` | `text-gray-700` |
| `border-wp-gray-200` | `border-gray-200` |

## Tailwind Classes Used

### Layout & Spacing
- `space-y-2`: Vertical spacing between elements (0.5rem)
- `w-full`: Full width
- `block`: Display as block element

### Typography
- `text-sm`: Small text size (0.875rem)
- `font-medium`: Medium font weight (500)
- `text-gray-700`: Dark gray text color
- `text-gray-500`: Medium gray text color
- `text-red-500`: Red text color for errors
- `text-red-600`: Darker red for error messages

### Input Styling
- `px-3`: Horizontal padding (0.75rem)
- `py-2`: Vertical padding (0.5rem)
- `border`: Border width (1px)
- `border-gray-300`: Gray border color
- `rounded-md`: Medium border radius (0.375rem)

### Focus States
- `focus:outline-none`: Remove default outline
- `focus:ring-1`: Add focus ring (1px)
- `focus:ring-blue-500`: Blue focus ring color
- `focus:border-blue-500`: Blue border color on focus

## TypeScript Types

All components include proper TypeScript type definitions:

```typescript
interface FormFieldProps {
  label: string;
  children: React.ReactNode;
  required?: boolean;
  error?: string;
  description?: string;
  className?: string;
}

interface TextInputProps extends React.InputHTMLAttributes<HTMLInputElement> {
  className?: string;
}

interface SelectProps extends React.SelectHTMLAttributes<HTMLSelectElement> {
  className?: string;
  children: React.ReactNode;
}
```

## Testing

All components maintain existing functionality:
- ✅ Form fields display correctly
- ✅ Focus states work properly
- ✅ Error states show correctly
- ✅ Required field indicators appear
- ✅ Description text displays properly
- ✅ Custom className props are applied correctly
- ✅ All HTML input/select attributes are supported

## Browser Compatibility

The Tailwind CSS classes used are compatible with all modern browsers:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Opera (latest)

## Related Issues

- Parent Issue: [#4 - Complete Tailwind CSS Implementation for Admin Dashboard](https://github.com/alimuzzaman/html-social-share-buttons/issues/4)
- Current Issue: Update FormFields Components to Pure Tailwind CSS

## Additional Resources

- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Tailwind CSS Form Styling](https://tailwindcss.com/docs/forms)
- [React TypeScript Cheatsheet](https://react-typescript-cheatsheet.netlify.app/)
