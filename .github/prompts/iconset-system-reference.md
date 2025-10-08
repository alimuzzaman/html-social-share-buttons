# Iconset System - Quick Reference

## 🎨 Overview

This document explains the iconset system migration from current to new structure.

## 📁 Current Structure (Before Rewrite)

```
iconset/
  default/
    ssb.php                    # Class definition
    style.css                  # Hand-written CSS
    preview.png                # Preview image
    square/                    # Type variant
      facebook.png
      twitter.png
      linkedin.png
      ...
  flat/
    ssb.php
    style.css
    square/
      *.png
    circle/
      *.png
  long_shadow/
    ssb.php
    style.css
    square/
    circle/
  prajin/
    ssb.php
    style.css
    square/
    circle/
```

## 🎯 New Structure (After Phase 1)

```
assets/iconset/
  default_square/              # Iconset + type combined
    facebook.png
    twitter.png
    linkedin.png
    style.css                  # Optional: custom CSS
    metadata.json              # Optional: iconset info
  flat_square/
    *.png
  flat_circle/
    *.png
  long_shadow_square/
    *.png
  long_shadow_circle/
    *.png
  prajin_square/
    *.png
  prajin_circle/
    *.png

build/iconset/                 # Generated CSS output
  default_square.css
  flat_square.css
  flat_circle.css
  long_shadow_square.css
  long_shadow_circle.css
  prajin_square.css
  prajin_circle.css
```

## 🔄 Key Changes

### Before: Nested Structure
- One folder per iconset
- Types as subfolders
- PHP class defines everything

### After: Flat Structure
- One folder per iconset+type combination
- No nesting
- Metadata in JSON (optional)

## 🏗️ Build Process

### PHP Generation

```php
// IconsetBuilder scans assets/iconset/
foreach ($iconsets as $iconset) {
    $css = generateIconsetCss($iconset);
    file_put_contents("build/iconset/{$iconset}.css", $css);
}
```

### Generated CSS Example

**Input:** `assets/iconset/default_square/facebook.png`

**Output:** `build/iconset/default_square.css`
```css
.zmshbt.default.square .facebook {
  background-image: url('../assets/iconset/default_square/facebook.png');
  width: 32px;
  height: 32px;
  display: block;
  background-size: cover;
}
```

## 📝 CSS Rules

### Base Structure
```css
.zmshbt.{iconset}.{type} {
  /* Container styles */
}

.zmshbt.{iconset}.{type} a {
  /* Common button styles */
}

.zmshbt.{iconset}.{type} .{network} {
  /* Network-specific icon */
  background-image: url('path/to/icon.png');
}
```

### Positioning Rules
```css
.zmshbt.{iconset}.left {
  position: fixed;
  left: 0;
  top: 30%;
  z-index: 9999;
}

.zmshbt.{iconset}.right {
  position: fixed;
  right: 0;
  top: 30%;
  z-index: 9999;
}
```

### Hover Effects
```css
.zmshbt.{iconset} a:hover {
  transform: scale(1.5);
  transition: all 0.25s linear;
}
```

## 🎨 How Icons Render

### 1. Load Iconset CSS
```php
wp_enqueue_style(
    'hss-iconset-default-square',
    HSS_URL . 'build/iconset/default_square.css'
);
```

### 2. Generate HTML
```php
echo '<div class="zmshbt default square">';
foreach ($icons as $network) {
    echo '<a class="' . $network . '" href="' . $shareUrl . '"></a>';
}
echo '</div>';
```

### 3. CSS Applies Background
```css
.zmshbt.default.square .facebook {
  background-image: url('facebook.png');
}
```

### 4. Result
```html
<div class="zmshbt default square">
  <a class="facebook" href="..." style="background-image: url(facebook.png)"></a>
</div>
```

## 🔧 IconRegistry Class

### Purpose
Manages all iconsets and provides icon data to renderers.

### Key Methods

```php
class IconRegistry {
    // Get all available iconsets
    public function getIconsets(): array
    
    // Get specific iconset data
    public function getIconset(string $name): ?array
    
    // Get icon data for rendering
    public function getIcon(string $iconset, string $network): ?array
    
    // Check if iconset exists
    public function hasIconset(string $name): bool
    
    // Get networks available in iconset
    public function getNetworks(string $iconset): array
}
```

### Usage Example

```php
$registry = new IconRegistry();

// Get iconset
$iconset = $registry->getIconset('default_square');

// Get icon for rendering
$icon = $registry->getIcon('default_square', 'facebook');
// Returns:
// [
//   'name' => 'Facebook',
//   'class' => 'facebook',
//   'image' => 'facebook.png',
//   'url_template' => 'https://facebook.com/sharer/...',
// ]
```

## 🛠️ IconsetBuilder Class

### Purpose
Generates CSS files from iconset directories.

### Key Methods

```php
class IconsetBuilder {
    // Build all iconsets
    public function buildAll(): array
    
    // Build specific iconset
    public function build(string $iconset): bool
    
    // Generate CSS for iconset
    protected function generateCss(string $iconset): string
    
    // Scan for icons in directory
    protected function scanIcons(string $path): array
}
```

### Usage Example

```php
$builder = new IconsetBuilder();

// Build all iconsets
$results = $builder->buildAll();
// Returns: ['default_square' => true, 'flat_circle' => true, ...]

// Build single iconset
$success = $builder->build('default_square');
```

## 📦 Metadata Format (Optional)

### metadata.json

```json
{
  "name": "Default Square",
  "id": "default_square",
  "type": "square",
  "description": "Classic square social icons",
  "author": "Plugin Author",
  "version": "1.0.0",
  "networks": [
    "facebook",
    "twitter",
    "linkedin",
    "pinterest",
    "email"
  ],
  "preview": "preview.png",
  "css": {
    "width": "32px",
    "height": "32px",
    "hover_scale": 1.5
  }
}
```

## 🚀 Migration Checklist

### For Each Iconset:

- [ ] Create `assets/iconset/{name}_{type}/` directory
- [ ] Copy PNG images from old structure
- [ ] Create metadata.json (optional)
- [ ] Run IconsetBuilder to generate CSS
- [ ] Verify CSS in `build/iconset/`
- [ ] Test rendering in browser
- [ ] Run visual regression tests
- [ ] Verify pixel-perfect match

## 🧪 Testing

### Visual Tests

```javascript
// Playwright test
test('default_square iconset renders correctly', async ({ page }) => {
  await page.goto('/test-page');
  await expect(page.locator('.zmshbt.default.square')).toBeVisible();
  await expect(page).toHaveScreenshot('default-square.png');
});
```

### Unit Tests

```php
class IconRegistryTest extends TestCase {
    public function testGetIconset() {
        $registry = new IconRegistry();
        $iconset = $registry->getIconset('default_square');
        
        $this->assertNotNull($iconset);
        $this->assertEquals('default_square', $iconset['id']);
        $this->assertArrayHasKey('networks', $iconset);
    }
}
```

## 💡 Tips & Best Practices

### Image Requirements
- **Format**: PNG (transparent background preferred)
- **Size**: 32x32px or 64x64px recommended
- **Naming**: lowercase, match network ID (facebook.png, twitter.png)

### CSS Best Practices
- Use consistent dimensions across iconset
- Include hover effects for better UX
- Use CSS transitions for smooth animations
- Maintain high z-index for floating buttons

### Directory Naming
- Format: `{iconset}_{type}`
- Examples: `default_square`, `flat_circle`, `modern_rounded`
- No spaces, use underscore

### Build Triggers
- Manual: `wp hss build-iconsets`
- On activation (if needed)
- On iconset upload (future)

## 🔗 Related Files

- `src/IconRegistry.php` - Icon management
- `src/Build/IconsetBuilder.php` - CSS generation
- `tests/Unit/IconRegistryTest.php` - Unit tests
- `tests/visual/iconsets.spec.js` - Visual tests

## 📚 References

- Phase 1 Task: PHASE1-020 to PHASE1-026
- Current code: `iconsets.php`, `interfaces.php`
- Example: `iconset/default/ssb.php`

---

**Next Steps**: Read the main plan in `phase1-rewrite-foundation.instructions.md` for complete implementation details.
