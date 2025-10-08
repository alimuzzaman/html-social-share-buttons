# Iconset Build Process Documentation

## Overview

This document defines the complete process for building iconset CSS from PNG images. The goal is to generate CSS files automatically from PNG icons stored in the `assets/iconset/` directory.

## Directory Structure

### Input Structure (Author-Created)
```
assets/iconset/{iconset}/
├── facebook.png
├── twitter.png
├── linkedin.png
├── pinterest.png
├── googlepluse.png
└── mail.png
```

**Example:**
```
assets/iconset/default_square/
assets/iconset/default_circle/
assets/iconset/flat_square/
assets/iconset/flat_circle/
assets/iconset/long_shadow_square/
assets/iconset/long_shadow_circle/
assets/iconset/prajin_square/
assets/iconset/prajin_circle/
```

### Output Structure (Generated)
```
build/iconsets/{iconset}/
└── style.css
```

**Example:**
```
build/iconsets/default_square/style.css
build/iconsets/default_circle/style.css
build/iconsets/flat_square/style.css
build/iconsets/flat_circle/style.css
```

## CSS Template Format

Each generated CSS file contains:

### 1. Base Wrapper Styles
```css
.zmshbt.{iconset}.{type} {
    /* Container positioning */
}

.zmshbt.{iconset}.{type}.left,
.zmshbt.{iconset}.{type}.right {
    /* Fixed positioning for left/right placements */
    position: fixed;
    top: 30%;
    z-index: 9999;
}

.zmshbt.{iconset}.{type}.left {
    left: -25px;
}

.zmshbt.{iconset}.{type}.left:hover {
    left: 0;
}

.zmshbt.{iconset}.{type}.right {
    right: -25px;
}

.zmshbt.{iconset}.{type}.right:hover {
    right: 0;
}
```

### 2. Icon Link Styles
```css
.zmshbt.{iconset}.{type} a {
    width: 32px;
    height: 32px;
    display: block;
    background-size: cover;
    margin: 10px;
    transition: all .25s linear;
}

.zmshbt.{iconset}.{type}.in_widget a,
.zmshbt.{iconset}.{type}.in_shortcode a {
    display: inline-block;
    margin: 5px;
}
```

### 3. Network-Specific Background Images
```css
.zmshbt.{iconset}.{type} a.facebook {
    background-image: url('../../../assets/iconset/{iconset}/facebook.png');
}

.zmshbt.{iconset}.{type} a.twitter {
    background-image: url('../../../assets/iconset/{iconset}/twitter.png');
}

.zmshbt.{iconset}.{type} a.linkedin {
    background-image: url('../../../assets/iconset/{iconset}/linkedin.png');
}

.zmshbt.{iconset}.{type} a.pinterest {
    background-image: url('../../../assets/iconset/{iconset}/pinterest.png');
}

.zmshbt.{iconset}.{type} a.googlepluse {
    background-image: url('../../../assets/iconset/{iconset}/googlepluse.png');
}

.zmshbt.{iconset}.{type} a.mail {
    background-image: url('../../../assets/iconset/{iconset}/mail.png');
}
```

### 4. Hover Effects
```css
.zmshbt.{iconset}.{type} a:hover,
.zmshbt.{iconset}.{type} a:active {
    transform: scale(1.2);
}
```

### 5. Auto-Hide Button CSS (Optional)
```css
.zmshbt.{iconset}.{type}.auto-hide.left,
.zmshbt.{iconset}.{type}.auto-hide.right {
    transition: all .25s linear .5s;
}
```

## Build Trigger Options

### Option 1: On-Demand Build (Recommended)
- **When:** When author uploads new PNG files or changes iconset
- **How:** Via WP-CLI command: `wp html-social-share build-iconsets`
- **Advantages:** Full control, predictable timing
- **Implementation:** WP-CLI command class

### Option 2: Automatic Build
- **When:** On plugin activation or settings save
- **How:** Hook into activation and settings update
- **Advantages:** Convenient for users
- **Disadvantages:** Could slow down admin actions

### Chosen Approach: On-Demand + Activation
- Build on plugin activation (ensures fresh start)
- Build via WP-CLI for manual updates
- Build when new iconset is added

## Iconset Metadata Format

### Directory Naming Convention
```
{iconset}_{type}
```

**Examples:**
- `default_square`
- `default_circle`
- `flat_square`
- `flat_circle`

### Required Files per Iconset
```
assets/iconset/{iconset}_{type}/
├── facebook.png      (required)
├── twitter.png       (required)
├── linkedin.png      (required)
├── pinterest.png     (required)
├── googlepluse.png   (required)
└── mail.png          (required)
```

### Optional Metadata File (Future Enhancement)
```json
{
  "name": "Default Square",
  "author": "Plugin Author",
  "version": "1.0.0",
  "icon_size": 32,
  "hover_effect": "scale",
  "networks": ["facebook", "twitter", "linkedin", "pinterest", "googlepluse", "mail"]
}
```

## CSS Generation Process

### Step 1: Scan Input Directory
```php
$iconsets = scandir('assets/iconset/');
// Returns: ['default_square', 'default_circle', 'flat_square', ...]
```

### Step 2: For Each Iconset
1. Parse directory name → extract iconset + type
2. Scan for PNG files → get available networks
3. Load CSS template
4. Replace placeholders with iconset/type values
5. Generate network-specific CSS rules
6. Write to `build/iconsets/{iconset}/style.css`

### Step 3: Verify Output
1. Check CSS file exists
2. Validate CSS syntax
3. Verify all network rules present
4. Log success/errors

## PHP Implementation Strategy

### IconsetBuilder Class
```php
namespace HtmlSocialShare\Build;

class IconsetBuilder {
    private string $assetsDir;
    private string $buildDir;
    private array $supportedNetworks;
    
    public function __construct() {
        $this->assetsDir = HTML_SOCIAL_SHARE_DIR . '/assets/iconset';
        $this->buildDir = HTML_SOCIAL_SHARE_DIR . '/build/iconsets';
        $this->supportedNetworks = ['facebook', 'twitter', 'linkedin', 'pinterest', 'googlepluse', 'mail'];
    }
    
    public function buildAll(): array {
        // Scan assets/iconset/
        // Build CSS for each iconset
        // Return results
    }
    
    public function buildIconset(string $iconset): bool {
        // Build CSS for specific iconset
    }
    
    private function generateCss(string $iconset, string $type, array $networks): string {
        // Generate complete CSS content
    }
    
    private function writeFile(string $path, string $content): bool {
        // Write CSS file to build directory
    }
}
```

### WP-CLI Command
```php
namespace HtmlSocialShare\CLI;

use WP_CLI;

class BuildCommand {
    public function __invoke($args, $assoc_args) {
        $builder = new IconsetBuilder();
        $results = $builder->buildAll();
        
        foreach ($results as $iconset => $status) {
            if ($status['success']) {
                WP_CLI::success("Built: {$iconset}");
            } else {
                WP_CLI::error("Failed: {$iconset} - {$status['error']}");
            }
        }
    }
}
```

## Error Handling

### Common Issues
1. **Missing PNG files** → Log warning, generate CSS with available networks
2. **Invalid directory name** → Skip, log error
3. **Write permissions** → Fail gracefully, show error message
4. **Invalid CSS** → Validate before writing, log errors

### Logging
- Success: "Built {iconset} CSS: {num_networks} networks"
- Warning: "{iconset}: Missing {network}.png - skipped"
- Error: "Failed to write {iconset} CSS: {error_message}"

## Testing Strategy

### Unit Tests
```php
class IconsetBuilderTest extends TestCase {
    public function test_scans_iconset_directories()
    public function test_parses_directory_names()
    public function test_generates_valid_css()
    public function test_writes_css_files()
    public function test_handles_missing_images()
    public function test_handles_write_errors()
}
```

### Integration Tests
1. Create test iconset with PNG files
2. Run builder
3. Verify CSS file created
4. Verify CSS contains all expected rules
5. Load CSS in browser
6. Verify icons display correctly

### Visual Comparison
1. Build CSS for existing iconsets
2. Compare output with current iconset/*/style.css
3. Verify visual rendering identical
4. Document any differences

## Migration Strategy

### Phase 1: Build Alongside Current
- Keep existing iconset/ directory
- Build to new build/iconsets/ directory
- Load from build/ in new code
- Compare outputs

### Phase 2: Switch to New System
- Update IconRegistry to load from build/
- Run builder on plugin activation
- Test with all iconsets

### Phase 3: Remove Old System
- Archive iconset/ directory
- Keep only build/ and assets/iconset/
- Update documentation

## Performance Considerations

### Build Time
- Each iconset: ~100ms
- Total for 8 iconsets: ~800ms
- Acceptable for on-demand build
- Consider caching build status

### File Size
- Each CSS file: ~2-5KB
- Total for 8 iconsets: ~16-40KB
- Minification not needed (already small)
- Gzip compression via server

### Caching
- Cache build status in transient
- Rebuild only when files change
- Check file modification times
- Clear cache on explicit build command

## Future Enhancements

### 1. Custom Iconset Creator UI
- Admin interface for uploading PNGs
- Automatic iconset creation
- Preview before save
- Validation of image dimensions

### 2. SCSS Support
- Convert templates to SCSS
- Compile via scssphp
- Allow iconset-specific variables
- More maintainable templates

### 3. SVG Support
- Allow SVG icons alongside PNG
- Generate inline SVG CSS
- Fallback to PNG for older browsers
- Size optimization

### 4. CDN Integration
- Upload built CSS to CDN
- Serve from CDN in production
- Fallback to local files
- Performance boost

## Maintenance Guidelines

### When to Rebuild
1. After plugin update (if templates change)
2. After uploading new PNG files
3. After changing iconset directory structure
4. When CSS appears broken/missing

### How to Rebuild
```bash
# Via WP-CLI (recommended)
wp html-social-share build-iconsets

# Via admin (future)
# Settings > HTML Social Share > Build Iconsets button
```

### Troubleshooting

**Issue:** CSS file not generated
**Solution:** Check write permissions on build/ directory

**Issue:** Icons not displaying
**Solution:** Verify PNG files exist in assets/iconset/

**Issue:** Wrong icon size
**Solution:** Check CSS width/height values

**Issue:** Hover effect not working
**Solution:** Verify transition CSS generated

## Summary

This build process provides:
- ✅ Automated CSS generation from PNG files
- ✅ Clear separation of concerns (assets vs build)
- ✅ Flexible build triggers (on-demand + activation)
- ✅ Comprehensive error handling
- ✅ Easy maintenance and updates
- ✅ Future extensibility (SVG, SCSS, CDN)

**Status:** Design complete, ready for implementation
**Next Step:** PHASE1-021 - Implement IconsetBuilder class
