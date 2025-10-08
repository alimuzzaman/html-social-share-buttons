# Current HTML Output Patterns Documentation

## Purpose
This document captures all current HTML/CSS output patterns from the existing implementation
to ensure the new Phase 1 implementation produces identical output.

## Placement Variations

### 1. Left Placement
**Class:** `.zmshbt.left.{iconset}.{type}`

**HTML Structure:**
```html
<div class="zmshbt left default square">
    <a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=..."></a>
    <a class="twitter" target="_blank" href="http://twitter.com/share?url=..."></a>
    <a class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?url=..."></a>
</div>
```

**CSS Properties:**
- `position: fixed`
- `left: -25px` (hides partially)
- `left: 0` (on hover)
- `top: 30%`
- `z-index: 9999`
- Transition effect on hover

### 2. Right Placement
**Class:** `.zmshbt.right.{iconset}.{type}`

**HTML Structure:**
```html
<div class="zmshbt right default square">
    <a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=..."></a>
    <a class="twitter" target="_blank" href="http://twitter.com/share?url=..."></a>
</div>
```

**CSS Properties:**
- `position: fixed`
- `right: -25px` (hides partially)
- `right: 0` (on hover)
- `top: 30%`
- `z-index: 9999`
- Transition effect on hover

### 3. Before Post Placement
**Class:** `.zmshbt.in_shortcode.{iconset}.{type}`

**HTML Structure:**
```html
<div class="zmshbt in_shortcode default square">
    <a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=..."></a>
    <a class="twitter" target="_blank" href="http://twitter.com/share?url=..."></a>
</div>
```

**CSS Properties:**
- Inline display for icons
- `display: inline-block` for each link
- `margin: 5px` between icons

### 4. After Post Placement
**Class:** `.zmshbt.in_shortcode.{iconset}.{type}`
(Same as before_post)

### 5. Widget Placement
**Class:** `.zmshbt.in_widget.{iconset}.{type}`

**HTML Structure:**
```html
<div class="zmshbt in_widget default square">
    <a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=..."></a>
    <a class="twitter" target="_blank" href="http://twitter.com/share?url=..."></a>
</div>
```

**CSS Properties:**
- Similar to shortcode placement
- Inline display

### 6. Shortcode Usage
```
[zm_sh_btn iconset='default' iconset_type='square' icons='facebook,twitter,linkedin' class='in_shortcode']
```

## Icon Link Structures

### Facebook
```html
<a class="facebook" target="_blank" href="http://www.facebook.com/sharer.php?u=ENCODED_URL&t=ENCODED_TITLE"></a>
```

### Twitter
```html
<a class="twitter" target="_blank" href="http://twitter.com/share?url=ENCODED_URL&text=ENCODED_TITLE"></a>
```

### LinkedIn
```html
<a class="linkedin" target="_blank" href="http://www.linkedin.com/shareArticle?url=ENCODED_URL&title=ENCODED_TITLE"></a>
```

### Pinterest
```html
<a class="pinterest" target="_blank" href="http://pinterest.com/pin/create/button/?url=ENCODED_URL&description=ENCODED_TITLE&media=IMAGE_URL"></a>
```

### Google Plus (deprecated but still in code)
```html
<a class="googlepluse" target="_blank" href="https://plus.google.com/share?url=ENCODED_URL"></a>
```

### Email
```html
<a class="mail" target="_blank" href="mailto:?subject=ENCODED_TITLE&body=ENCODED_URL"></a>
```

## CSS Class Naming Conventions

### Base Classes
- `.zmshbt` - Base wrapper class (always present)
- `.{placement}` - Placement class: left, right, in_widget, in_shortcode
- `.{iconset}` - Iconset name: default, flat, long_shadow, prajin
- `.{type}` - Type: square, circle

### Icon Classes
- `.facebook` - Facebook icon
- `.twitter` - Twitter icon
- `.linkedin` - LinkedIn icon
- `.pinterest` - Pinterest icon
- `.googlepluse` - Google Plus icon
- `.mail` - Email icon

## CSS Background Image Pattern

Each iconset generates CSS like:
```css
.zmshbt.{iconset}.{type} a.{network} {
    background-image: url('path/to/iconset/{type}/{network}.png');
    background-size: cover;
    width: 32px;
    height: 32px;
    display: block;
}
```

## Z-Index and Positioning

### Fixed Placements (Left/Right)
- `z-index: 9999` - Ensures buttons stay on top
- `position: fixed` - Fixed to viewport
- Auto-hide feature: partial hide when not hovering

### Inline Placements (Shortcode/Widget)
- No fixed positioning
- Inline-block display
- Flow with content

## Hover Effects

All iconsets include:
```css
.zmshbt a:hover, .zmshbt a:active {
    transform: scale(1.5);
    transition: all .25s linear;
}
```

## Options Affecting Output

### nofollow
Adds `rel="nofollow"` attribute to links

### auto_hide_btn
When disabled, removes the partial hide effect on left/right placements

### g_analytics
Adds Google Analytics tracking parameters

## Iconset + Type Combinations

### Default Iconset
- default + square
- default + circle

### Flat Iconset
- flat + square
- flat + circle

### Long Shadow Iconset
- long_shadow + square
- long_shadow + circle

### Prajin Iconset
- prajin + square
- prajin + circle

## Current CSS Injection Method

CSS is injected in the footer via `wp_footer` hook:
```php
add_action('wp_footer', 'zm_sh_icon_styles');
```

The function generates:
1. Icon CSS (background-image rules)
2. Positioning CSS (left/right placement styles)
3. Auto-hide CSS (if enabled)

## Placeholder Replacements

### %%permalink%%
Replaced with `get_permalink()` or current URL

### %%title%%
Replaced with `get_the_title()` or page title

## Known Edge Cases

1. Multiple button sets on same page (currently supported)
2. Different iconsets on same page (currently supported)
3. Same network appearing multiple times (currently supported)
4. Empty icons array (should not render)
5. Invalid iconset name (falls back to default)

## Testing Checklist

- [ ] Left placement renders correctly
- [ ] Right placement renders correctly
- [ ] Before post placement renders correctly
- [ ] After post placement renders correctly
- [ ] Widget placement renders correctly
- [ ] Shortcode renders correctly
- [ ] All iconsets render identically
- [ ] All types (square/circle) render correctly
- [ ] Hover effects work
- [ ] Auto-hide works (left/right)
- [ ] nofollow adds attribute
- [ ] URLs encode correctly
- [ ] Placeholders replace correctly

## Version Information

This documentation captures behavior from:
- Version: 2.2.1
- Date: December 2024
- Archive Location: `/archive/`
