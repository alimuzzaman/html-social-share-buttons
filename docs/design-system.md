# Design System Documentation

## Overview

This document provides a comprehensive design system for the HTML Social Share Buttons WordPress plugin admin interface. It defines the visual language, component patterns, and interaction guidelines used throughout the plugin's administration area.

## Color Palette

### Primary Colors

**Blue (Primary Actions)**
- `#3C90BE` - Primary button background
- `#347DA4` - Primary button hover/active state
- `#0096d7` - Toggle active state
- `#378bd7` - Heading text
- `#698CDA` - Selected input border

**Usage**: Primary actions, active states, headings, and focus indicators.

### Secondary Colors

**Green/Lime (Accents)**
- `#d4e477` - Hover/selected border accent

**Usage**: Highlighting selected items, hover states for special interactions.

### Neutral Colors

**Grays (Text & Borders)**
- `#222` - Primary heading text (dark)
- `#888888` - Inactive toggle background
- `rgb(125, 125, 125)` - Secondary text
- `rgba(0, 0, 0, 0.702)` - Button text with transparency
- `rgb(186, 186, 186)` - Light borders
- `rgba(176, 168, 168, 0.71)` - Widget label borders
- `rgba(205, 205, 205, 0.71)` - Input borders
- `rgba(194, 194, 194, 0.9)` - Box shadow neutral

**White & Light Backgrounds**
- `#fff` - Pure white (backgrounds, button text)
- `#EDEDED` - Light gray background (modal)
- `#F0F0F0` - Hover background for secondary buttons
- `#d6d6d6` - Light borders
- `#d0d0d0` - Hover borders

**Overlays & Shadows**
- `rgba(168, 168, 168, 0.92)` - Modal backdrop
- `rgba(219, 219, 219, 0.91)` - Close button hover
- `rgba(55, 139, 215, 0.9)` - Blue box shadow for focus/selection

**Usage**: Text hierarchy, borders, backgrounds, overlays, and separators.

## Typography

### Font Families

**Primary**: 'Open Sans', sans-serif
- Used for: Main headings, body text, buttons

**Secondary**: 'Bree Serif'
- Used for: Section headings (h2)

**Monospace/Special**: 'Opensans', 'Roboto Slab'
- Used for: Toggle switches, special UI elements

### Font Sizes

**Headings**
- `26px` - Main page heading (`.zm_options_page_heading`)
- `23px` - Section headings (h2)
- `20px` - Large text elements

**Body & UI Elements**
- `16.69px` - Button text (uppercase)
- `16px` - Form labels, standard text
- `15px` - Toggle switch text
- `14px` - WordPress core button overrides
- `13px` - Small text (designer credits, captions)

**Special Elements**
- `18px` - Widget toggle text

### Font Weights

- `400` - Normal/regular weight
- `bold` - Bold weight for labels and emphasis

### Line Heights

- `38px` - Main page heading
- `29px` - Section headings
- `35px` - Toggle switches
- `32px` - Toggle switch pseudo-elements
- `normal` - Buttons (explicit)

## Spacing System

### Margins

**Large Spacing**
- `27px 55px 65px` - Button style image spacing
- `30px auto` - Modal centering
- `30px` - Large vertical spacing
- `20px` - Standard vertical spacing between rows

**Medium Spacing**
- `10px` - Standard element spacing
- `5px` - Small element spacing
- `2%` - Percentage-based margin for flexible layouts

**Small Spacing**
- `0px` - Remove spacing where needed

### Padding

**Large Padding**
- `9px 26px` - Primary button padding
- `9px 15px 4px 0` - Heading padding

**Medium Padding**
- `5px` - Small padding (close button)
- `2px 20px 20px` - Modal container padding
- `2px` - Toggle switch padding

**Form Padding**
- `10px` - Form element internal spacing (when checked)
- `10px` - Background positioning padding

## Component Patterns

### Buttons

#### Primary Button
```css
.button {
  background: #3C90BE;
  border-bottom: 3px solid #347DA4;
  vertical-align: baseline;
  padding: 9px 26px;
  font-size: 16.69px;
  text-transform: uppercase;
  line-height: normal;
  min-width: 150px;
  min-height: 40px;
  border: none;
}

.button:hover {
  background-color: #347DA4;
  border-bottom: 3px solid #347DA4;
}
```

**Usage**: Primary actions like "Save Settings", "Submit"

#### Secondary Button (Code Display)
```css
.get_phpcode, .get_shortcode {
  background: #fff;
  border-bottom: 3px solid #d6d6d6;
  border-top: 1px solid #d6d6d6;
  border-right: 1px solid #d6d6d6;
  border-left: 1px solid #d6d6d6;
  font-family: "Open Sans";
  color: rgba(0, 0, 0, 0.702);
}

.get_phpcode:hover, .get_shortcode:hover {
  background: #F0F0F0;
  border-bottom: 3px solid #d0d0d0;
}
```

**Usage**: Secondary actions, code display buttons

#### WordPress Core Button Overrides
```css
.wp-core-ui .button, 
.wp-core-ui .button-primary, 
.wp-core-ui .button-secondary {
  font-size: 14px;
  border-radius: 5px;
}
```

**Usage**: Maintains consistency with WordPress admin styles

### Form Elements

#### Form Row
```css
.row {
  min-height: 40px;
  margin-top: 20px;
}

.row:before, .row:after {
  content: " ";
  display: table;
  clear: both;
}

.row label, .row input {
  float: left;
}
```

**Usage**: Standard form field container with clearfix

#### Form Labels
```css
.row label {
  float: left;
  width: 25%; /* 60% for widget variant */
  font-size: 16px;
  font-weight: bold;
}
```

**Usage**: Form field labels with consistent sizing

#### Toggle Switch
```css
input[type=checkbox] {
  display: none;
}

input + .for_label .toggle-check {
  border: none;
  width: 75px; /* 90px for widget variant */
  padding: 2px;
  height: 35px;
  outline: none;
  display: block;
  cursor: pointer;
  position: relative;
  user-select: none;
}

input + .for_label .toggle-check:after {
  display: block;
  position: absolute;
  top: -4px;
  left: 0;
  bottom: 10px;
  right: 0;
  color: #fff;
  font-family: "Opensans";
  font-size: 15px;
  line-height: 32px;
  border-radius: 30px;
  padding-left: 10px;
  background-image: url(setting.png);
  background-repeat: no-repeat;
  transition: all 0.5s;
}

/* Inactive State */
input + .for_label .toggle-check:after {
  background-color: #888888;
  content: attr(data-off);
  background-position: 0%;
  text-align: right;
}

/* Active State */
input:checked + .for_label .toggle-check:after {
  background-color: #0096d7;
  content: attr(data-on);
  background-position: 100%;
  text-align: left;
}
```

**Usage**: Custom toggle switches with smooth transitions

#### Radio Buttons (Visual Selection)
```css
.show_on input {
  display: none;
}

.show_on label {
  width: auto;
  margin-right: 30px;
  border: 4px solid rgba(205, 205, 205, 0.71);
}

.show_on input[type="radio"]:checked + label,
.show_on input[type="radio"] + label:hover {
  border-color: #698CDA;
  box-shadow: 0px 0px 7px 0px rgba(55, 139, 215, 0.9);
}
```

**Usage**: Visual selection tiles for icon sets and themes

### Layout Patterns

#### Modal/Popup
```css
.zm-sh-thick-box {
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow: overlay;
  background-color: rgba(168, 168, 168, 0.92);
  z-index: 99999;
}

.zm-tabs {
  max-width: 555px;
  margin: 30px auto;
  background-color: #EDEDED;
  padding: 2px 20px 20px;
  position: relative;
}
```

**Usage**: Overlay modals for code display and advanced options

#### Close Button
```css
.zm-tabs .close {
  position: absolute;
  right: 10px;
  font-size: 20px;
  font-weight: bold;
  padding: 5px;
  top: 8px;
  cursor: pointer;
}

.zm-tabs .close:hover {
  background-color: rgba(219, 219, 219, 0.91);
}
```

**Usage**: Dismissing modals and overlays

#### Backdrop
```css
.backdrop {
  position: absolute;
  width: 100%;
  height: 100%;
}
```

**Usage**: Click-to-close overlay background

### Collapsible Sections

#### Toggle Container
```css
input + .for_label .show_on {
  transition: 1s all;
  overflow: hidden;
  height: 0px;
  margin: 0px;
  min-height: 0px;
}

input:checked + .for_label .show_on {
  height: 279px;
  padding-top: 10px;
  padding-left: 5px;
}
```

**Usage**: Expandable sections that reveal additional options

## Interactive States

### Hover States

**Buttons**
- Primary: Background changes from `#3C90BE` to `#347DA4`
- Secondary: Background changes from `#fff` to `#F0F0F0`
- Close button: Background changes to `rgba(219, 219, 219, 0.91)`

**Form Elements**
- Radio button labels: Border color changes to `#698CDA` with blue shadow
- Toggle switches: Color change handled through checked state

### Focus States

**Inputs**
- Box shadow: `0px 0px 7px 0px rgba(55, 139, 215, 0.9)`
- Border color: `#698CDA`
- Outline: None (custom focus styles used)

### Active/Checked States

**Toggle Switches**
- Inactive: `#888888` background, right-aligned text
- Active: `#0096d7` background, left-aligned text
- Smooth transition: `all 0.5s`

**Radio Buttons**
- Selected: `#698CDA` border with blue shadow
- Unselected: Light gray border `rgba(205, 205, 205, 0.71)`

### Disabled States

While not explicitly defined in the current CSS, disabled states should follow these conventions:
- Reduced opacity: `0.5-0.6`
- Cursor: `not-allowed`
- Hover effects: Disabled

## Transitions & Animations

### Standard Transitions
- Toggle switches: `all 0.5s` - Smooth color and position changes
- Collapsible sections: `1s all` - Slow height transitions for visibility

### User Interaction Feedback
- All interactive elements should provide visual feedback
- Smooth transitions enhance perceived performance
- Timing should be fast enough to feel responsive but slow enough to be noticeable

## Accessibility Guidelines

### Focus Management

1. **Visible Focus Indicators**: All interactive elements have custom focus styles with blue shadow
2. **Focus Outline**: Custom outlines replace default browser outlines for consistency
3. **Keyboard Navigation**: Ensure all controls are keyboard accessible

### Color Contrast

**Text on Backgrounds**
- Dark text (`#222`) on white: High contrast ✓
- White text on blue (`#3C90BE`): Adequate contrast ✓
- Light text on gray: May need review for WCAG AA compliance

**Interactive Elements**
- Button text has sufficient contrast
- Toggle switches use high-contrast colors
- Selected states are clearly distinguishable

### Screen Reader Considerations

1. Hidden checkboxes and radio buttons rely on labels
2. Toggle switches use `data-on` and `data-off` attributes for state
3. Modal overlays should have proper ARIA labels
4. Form labels should be properly associated with inputs

### Best Practices

- Minimum touch target size: `40px` (met by buttons and toggles)
- Consistent spacing for easy navigation
- Clear visual hierarchy through typography and color
- Predictable interaction patterns

## Responsive Design

The current implementation focuses on desktop admin interface. For responsive considerations:

### Breakpoints (Recommended)

- Desktop: `> 782px` (WordPress admin breakpoint)
- Tablet: `601px - 782px`
- Mobile: `< 600px`

### Layout Adaptations

**Current fixed widths that may need adjustment:**
- Form labels: `25%` (should stack on mobile)
- Modal: `555px max-width` (should use full width with padding on mobile)
- Toggle switches: Fixed pixel widths (should scale)

### Mobile Considerations

1. Stack form labels above inputs
2. Increase touch target sizes
3. Full-width buttons
4. Adjusted spacing for smaller screens
5. Simplified toggle switch designs

## Widget-Specific Patterns

The widget admin interface (`.HSSWidget`) has some variations:

### Differences from Main Admin

1. **Label Width**: `60%` instead of `25%` for better space utilization
2. **Toggle Width**: `90px` instead of `75px`
3. **Font Family**: Uses 'Roboto Slab' for toggle text
4. **Collapsible Height**: Fixed `279px` when expanded
5. **Namespace**: All styles prefixed with `.HSSWidget`

## Implementation Guidelines

### CSS Organization

1. **Base Styles**: Typography, colors, resets
2. **Layout**: Grid systems, containers, spacing
3. **Components**: Buttons, forms, cards
4. **Utilities**: Helper classes for common patterns
5. **State**: Hover, focus, active, disabled variations

### Naming Conventions

**Current Convention**: 
- Semantic class names (`.button`, `.row`, `.toggle-check`)
- Vendor prefixes for WordPress (`.wp-core-ui`)
- Component namespacing (`.HSSWidget`, `.zm-tabs`)

**Best Practices**:
- Use lowercase with hyphens
- Be descriptive and semantic
- Namespace plugin-specific styles to avoid conflicts

### Browser Support

- Vendor prefixes used for older browsers (`-webkit-`, `-moz-`, `-ms-`)
- Fallbacks provided for CSS features
- Graceful degradation for advanced features

### Performance Considerations

1. **CSS Specificity**: Keep specificity low for easier overrides
2. **Vendor Prefixes**: Only where necessary for broader support
3. **Transitions**: Use `transform` and `opacity` for smooth performance
4. **Image Assets**: Optimize background images
5. **Import Statements**: External font imports add HTTP requests

## File Structure

```
assets/
├── admin.css           # Main admin interface styles
├── admin-widget.css    # Widget-specific styles
└── image/              # UI images and icons
```

## Version History

- **Current**: Design system documented based on existing CSS patterns
- **Next Steps**: Potential modernization with CSS custom properties, responsive improvements

## References

- WordPress Admin UI Guidelines
- WCAG 2.1 Level AA Accessibility Standards
- CSS Transition Best Practices

---

**Last Updated**: 2025
**Maintainer**: Plugin Development Team
