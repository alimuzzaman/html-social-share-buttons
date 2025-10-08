# Iconset Analysis Documentation

## Purpose
Comprehensive analysis of all current iconset implementations to guide the new unified iconset system.

## Iconset Directory Structure

Current location: `/archive/iconset/`

### Available Iconsets
1. **default**
2. **flat**
3. **long_shadow** (long-shadows in options)
4. **prajin**

## Iconset Class Structure

### Base Parent Class
Location: `/archive/iconsets.php`

```php
class __iconset_parent_class {
    public $id;
    public $name;
    public $__FILE__;
    public $stylesheet;
    public $preview_img;
    public $types = [];
    public $icons = [];
}
```

### Default Iconset
File: `/archive/iconset/default/ssb.php`

```php
class zm_sh_iconset_default extends __iconset_parent_class {
    public $id = 'default';
    public $name = 'Default';
    public $stylesheet = "style.css";
    public $preview_img = "preview.png";
    public $types = ["square", "circle"];
    public $icons = [
        'facebook' => [...],
        'twitter' => [...],
        'linkedin' => [...],
        'googlepluse' => [...],
        'pinterest' => [...],
        'mail' => [...]
    ];
}
```

**Supported Networks:**
- Facebook
- Twitter
- LinkedIn
- Google Plus (deprecated)
- Pinterest
- Mail

**Types:**
- square
- circle

**Image Structure:**
```
default/
  square/
    facebook.png
    twitter.png
    linkedin.png
    googlepluse.png
    pinterest.png
    mail.png
  circle/
    facebook.png
    twitter.png
    linkedin.png
    googlepluse.png
    pinterest.png
    mail.png
```

### Flat Iconset
File: `/archive/iconset/flat/ssb.php`

```php
class zm_sh_iconset_flat extends __iconset_parent_class {
    public $id = 'flat';
    public $name = 'Flat';
    // Similar structure to default
}
```

**Same networks and types as default**

**Special CSS Features:**
- Hover scale effect: `transform: scale(1.5)`
- Smooth transitions

### Long Shadow Iconset
File: `/archive/iconset/long_shadow/ssb.php`

```php
class zm_sh_iconset_long_shadow extends __iconset_parent_class {
    public $id = 'long_shadow';
    public $name = 'Long Shadows';
    // Similar structure
}
```

**Same networks and types as default**

**Special Visual Features:**
- Long shadow effects
- Modern flat design

### Prajin Iconset
File: `/archive/iconset/prajin/ssb.php`

```php
class zm_sh_iconset_prajin extends __iconset_parent_class {
    public $id = 'prajin';
    public $name = 'Prajin';
    // Similar structure
}
```

**Same networks and types as default**

## Icon Array Structure

Each icon in an iconset has this structure:

```php
'facebook' => [
    'id' => 'facebook',
    'name' => 'Facebook',
    'class' => 'facebook',
    'image' => 'facebook.png',
    'url' => 'http://www.facebook.com/sharer.php?u=%%permalink%%&t=%%title%%'
]
```

### Common Icon Properties
- **id**: Unique identifier (matches key)
- **name**: Human-readable name
- **class**: CSS class name
- **image**: Image filename
- **url**: Share URL template with placeholders

## URL Templates by Network

### Facebook
```
http://www.facebook.com/sharer.php?u=%%permalink%%&t=%%title%%
```

### Twitter
```
http://twitter.com/share?url=%%permalink%%&text=%%title%%
```

### LinkedIn
```
http://www.linkedin.com/shareArticle?url=%%permalink%%&title=%%title%%
```

### Pinterest
```
http://pinterest.com/pin/create/button/?url=%%permalink%%&description=%%title%%&media=%%image%%
```

### Google Plus (Deprecated)
```
https://plus.google.com/share?url=%%permalink%%
```

### Email
```
mailto:?subject=%%title%%&body=%%permalink%%
```

## Placeholder System

### Available Placeholders
- `%%permalink%%` - Current page URL
- `%%title%%` - Page/post title
- `%%image%%` - Featured image URL (for Pinterest)

### Placeholder Replacement
Implemented in main plugin file via filters:
```php
apply_filters('zm_sh_placeholder', $url, $atts);
```

## CSS Class Naming Patterns

### Wrapper Classes
```css
.zmshbt.{placement}.{iconset}.{type}
```

Examples:
- `.zmshbt.left.default.square`
- `.zmshbt.right.flat.circle`
- `.zmshbt.in_shortcode.long_shadow.square`
- `.zmshbt.in_widget.prajin.circle`

### Icon Classes
```css
.zmshbt.{iconset}.{type} a.{network}
```

Examples:
- `.zmshbt.default.square a.facebook`
- `.zmshbt.flat.circle a.twitter`

## CSS File Structure

Each iconset has a `style.css` file with:

1. **Base Styles**
   ```css
   .zmshbt.{iconset} { }
   ```

2. **Positioning for Fixed Placements**
   ```css
   .zmshbt.{iconset}.left { position: fixed; left: -25px; top: 30%; }
   .zmshbt.{iconset}.right { position: fixed; right: -25px; top: 30%; }
   ```

3. **Inline Styles**
   ```css
   .zmshbt.{iconset}.in_widget a,
   .zmshbt.{iconset}.in_shortcode a {
       display: inline-block;
       margin: 5px;
   }
   ```

4. **Icon Styles**
   ```css
   .zmshbt.{iconset}.{type} a {
       width: 32px;
       height: 32px;
       display: block;
       background-size: cover;
   }
   
   .zmshbt.{iconset}.{type} a.{network} {
       background-image: url('path/to/image.png');
   }
   ```

5. **Hover Effects**
   ```css
   .zmshbt.{iconset} a:hover {
       transform: scale(1.5);
       transition: all .25s linear;
   }
   ```

## Image File Naming Conventions

### Standard Format
```
{network}.png
```

Examples:
- `facebook.png`
- `twitter.png`
- `linkedin.png`

### Directory Structure Options

**Option 1: Type-based subdirectories (current)**
```
iconset/
  default/
    square/
      facebook.png
    circle/
      facebook.png
```

**Option 2: Flat structure with type suffix**
```
iconset/
  default/
    facebook-square.png
    facebook-circle.png
```

## Type Variations

### Square
- Standard rectangular/square icons
- Typically 32x32 pixels
- Most common variant

### Circle
- Circular cropped icons
- Same icons as square, just different shape
- Same pixel dimensions

## Common Features Across All Iconsets

1. **Fixed positioning support** (left/right)
2. **Inline positioning support** (shortcode/widget)
3. **Hover scale effect** (1.5x)
4. **Smooth transitions** (0.25s)
5. **Auto-hide functionality** (left/right only)
6. **Same network support** (all have same networks)

## Differences Between Iconsets

1. **Visual design** (colors, shadows, style)
2. **CSS specifics** (some have unique effects)
3. **Image files** (different artwork)
4. **File sizes** (varies by design complexity)

## Migration Strategy for Phase 1

### New Structure
```
assets/iconset/
  default_square/
    facebook.png
    twitter.png
    ...
  default_circle/
    facebook.png
    ...
  flat_square/
    ...
  flat_circle/
    ...
```

### Build Process
1. Source CSS templates in `react-src/iconsets/{iconset}/`
2. Author uploads PNG images to `assets/iconset/{iconset}/`
3. Build command: `pnpm run build:iconsets`
4. Generated CSS output to `build/iconsets/{iconset}/style.css`
5. Frontend loads CSS from `build/`, images from `assets/`

### CSS Template Variables
```css
/* variables.css */
:root {
  --icon-size: 32px;
  --icon-margin: 5px;
  --hover-scale: 1.5;
  --transition-speed: 0.25s;
}
```

### CSS Template Rules
```css
/* template.css */
.zmshbt.{iconset}.{type} a.{network} {
  background-image: url('../../assets/iconset/{iconset}/{network}.png');
  width: var(--icon-size);
  height: var(--icon-size);
}
```

## Testing Checklist for Each Iconset

- [ ] All networks render correctly
- [ ] Both square and circle types work
- [ ] Left placement works
- [ ] Right placement works
- [ ] Shortcode placement works
- [ ] Widget placement works
- [ ] Hover effects work
- [ ] Auto-hide works (left/right)
- [ ] Icons are correct size (32x32 typically)
- [ ] CSS doesn't conflict with other iconsets
- [ ] Images load correctly
- [ ] URLs generate correctly

## Known Issues & Edge Cases

1. **Google Plus Deprecated**: Still in code but social network no longer exists
2. **Pinterest Image Placeholder**: Requires featured image, may not always be available
3. **Mixed Iconsets**: Currently not supported (one iconset per page load)
4. **Dynamic Icon Adding**: Not currently supported in UI
5. **Custom Icon URLs**: Not currently supported (fixed URL templates)

## Recommendations for Phase 1

1. **Maintain current icon class structure** - Don't change CSS classes
2. **Keep URL templates identical** - Exact same share URLs
3. **Separate iconset+type combinations** - Each combination is independent
4. **Use PNG + CSS approach** - Don't switch to SVG in Phase 1
5. **Build automation** - Generate CSS from templates
6. **Metadata in JSON** - Store iconset metadata separately from PHP classes
7. **Icon registry** - Central registry of all available icons

## Future Enhancements (Post Phase 1)

1. SVG support option
2. Custom icon uploads
3. Custom URL templates
4. Dynamic icon addition in UI
5. Icon pack marketplace
6. Animated icons
7. Retina support (@2x images)
8. WebP format support

## Version Information

Analysis based on:
- Version: 2.2.1
- Archive location: `/archive/iconset/`
- Date: December 2024
