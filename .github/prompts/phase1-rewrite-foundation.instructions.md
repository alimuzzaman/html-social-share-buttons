---
applyTo: '**'
---
# Phase 1: HTML Social Share Buttons - Complete Ground-Up Rewrite Plan

## 🎯 Project Vision & Objectives

### Core Principles
1. **Keep Frontend Output Identical**: HTML/CSS output must match current rendering exactly
2. **No JavaScript**: Pure HTML/CSS solution (maintain current philosophy)
3. **Test-First Approach**: Write tests capturing current behavior before changing anything
4. **Unified Iconset System**: All iconsets (current and future) use same rendering method
5. **Flat Options Structure**: Simple, organized options (no tab-based complexity from archive2)
6. **Modern PHP**: PSR-4 autoloading, OOP, testable architecture
7. **Tailwind CSS**: Admin UI built with Tailwind (not frontend buttons)

## 📊 Current State Analysis

### Current Architecture Overview

**How It Works Now:**
```
User Request → WordPress → zm_sh_btn() function
                              ↓
                         Get Options (flat array)
                              ↓
                         Get Iconset Object
                              ↓
                         Loop Through Icons
                              ↓
                         Generate HTML <a> tags
                              ↓
                         Inject CSS in footer
                              ↓
                         Output: <div class="zmshbt {iconset} {type}">
                                   <a class="{network}"></a>
                                 </div>
```

**Current Options Structure** (from docs/options.md):
```php
array(
  'title' => 'Share this with your friends',
  'excludes' => '55,66',              // Post IDs to exclude
  'g_analytics' => true,               // Google Analytics tracking
  'auto_hide_btn' => true,            // Auto-hide left/right buttons
  'use_port' => true,                 // Include port in URL
  'nofollow' => true,                 // Add nofollow to links
  'iconset' => 'long-shadows',        // Which iconset to use
  'show_in' => array(                 // Placement options
    'show_left' => 'square',          // Type: square/circle
    'show_right' => 'square',
    'show_before_post' => 'square',
    'show_after_post' => 'square',
  ),
  'icons' => array(                   // Enabled networks
    'facebook' => '1',
    'twitter' => '1',
    'linkedin' => '1',
    // ...
  ),
);
```

### Current Iconset System

**Directory Structure:**
```
iconset/
  default/
    ssb.php              # Class definition
    style.css            # CSS for this iconset
    preview.png          # Preview image
    square/              # OR types as folders
      facebook.png
      twitter.png
  flat/
    ssb.php
    style.css
    square/
      *.png
    circle/
      *.png
```

**Current Iconset Class:**
```php
class zm_sh_iconset_default extends __iconset_parent_class {
    public $id = 'default';
    public $name = 'Default';
    public $__FILE__ = __FILE__;
    public $stylesheet = "style.css";
    public $preview_img = "preview.png";
    public $types = array("square", "circle");
    public $icons = array(
        'facebook' => array(
            'id' => 'facebook',
            'name' => "Facebook",
            'class' => 'facebook',
            'image' => 'facebook.png',
            'url' => "http://www.facebook.com/sharer.php?u=%%permalink%%&t=%%title%%",
        ),
        // ...
    );
}
```

**Current CSS Output Method:**
```php
// Icons are rendered with CSS background-image
.zmshbt.{iconset}.{type} .{class} {
    background-image: url('{iconset_url}{type}/{image}');
}
```

**Current HTML Output:**
```html
<div class="zmshbt left default square">
  <a class="facebook" target="_blank" href="..."></a>
  <a class="twitter" target="_blank" href="..."></a>
</div>
```

### Future Iconset System (archive2/assets/iconset)

**New Structure:**
```
assets/iconset/
  default_square/
    facebook.png
    twitter.png
    style.css        # Hand-crafted or auto-generated
  flat_circle/
    facebook.png
    twitter.png
    style.css
```

**Build Process:**
```
1. PNG images in assets/iconset/{iconset}/
2. Generate CSS from images + PHP code
3. Output to build/iconset/{iconset}.css
4. Frontend loads: build/iconset/{iconset}.css
```

## 🚫 What We Don't Like About archive2/

### Issues with archive2 Approach:

1. **Over-Complex Options Structure**
   - Organized by tabs (general, networks, display, etc.)
   - Makes migration harder
   - Harder to understand flat structure

2. **Legacy vs Modern Split**
   - Unnecessary distinction between "legacy" and "new" iconsets
   - All iconsets should work the same way

3. **Too Many Abstractions**
   - Multiple renderer classes
   - Profile system (we don't need profiles yet)
   - Icon registry with SVG focus (we use PNG)

4. **Settings Architecture**
   - Tab-based structure adds complexity
   - We need flat, simple, organized

## ✅ What We Can Use from archive2/

### Good Parts to Adopt:

1. **PSR-4 Autoloading**
   - Namespace: `HtmlSocialShare\`
   - Clean class structure

2. **Renderer Pattern**
   - Separate rendering logic
   - Testable components

3. **URL Builder**
   - Clean URL generation
   - Placeholder replacement

4. **Test Infrastructure**
   - PHPUnit setup
   - Playwright for E2E

5. **Build Process**
   - Asset compilation
   - CSS generation

## 🎯 Success Criteria

### Phase 1 Must Achieve:

1. ✅ **Identical Frontend Output**
   - Same HTML structure
   - Same CSS classes
   - Same visual appearance
   - All current iconsets work

2. ✅ **All Tests Pass**
   - Capture current behavior first
   - New code passes same tests

3. ✅ **Options Compatibility**
   - Read current options format
   - No data loss on upgrade

4. ✅ **Same Features**
   - All placements work (left, right, before, after)
   - All iconsets render correctly
   - All networks work
   - Shortcode compatibility

5. ✅ **Performance**
   - Equal or better page load
   - No JavaScript added

## 📋 Phase 1 Detailed Task Breakdown

### Phase 1A: Test Infrastructure & Current Behavior Capture (40-50 hours)

#### PHASE1-001: Set Up Test Environment (4 hours) - ❌ NOT STARTED
- **Task**: Create comprehensive testing infrastructure
- **Success Criteria**: 
  - PHPUnit installed and configured
  - Test database set up
  - wp-env or local WordPress running
  - Playwright configured for visual tests
- **Files**: 
  - `phpunit.xml`
  - `tests/bootstrap.php`
  - `.wp-env.json`
  - `playwright.config.ts`
- **Dependencies**: None
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Install PHPUnit via Composer
  - ✅ Create phpunit.xml with WordPress test suite
  - ✅ Set up wp-env configuration
  - ✅ Install Playwright for visual regression
  - ✅ Create test database and fixtures
  - ✅ Configure autoloading for tests
  - ✅ Create bootstrap.php for WordPress test environment
  - ✅ Document test running commands

#### PHASE1-002: Document Current HTML Output Patterns (6 hours) - ❌ NOT STARTED
- **Task**: Create comprehensive documentation of all current HTML/CSS output patterns
- **Success Criteria**: 
  - All placement variations documented
  - All iconset+type combinations captured
  - CSS class patterns recorded
  - Sample HTML for each scenario
- **Files**: 
  - `tests/fixtures/expected-output.php`
  - `docs/current-html-patterns.md`
- **Dependencies**: PHASE1-001
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Document left placement HTML structure
  - ✅ Document right placement HTML structure
  - ✅ Document before_post placement HTML structure
  - ✅ Document after_post placement HTML structure
  - ✅ Document shortcode output patterns
  - ✅ Document widget output patterns
  - ✅ Capture CSS class naming conventions
  - ✅ Document all iconset+type combinations
  - ✅ Record z-index and positioning CSS
  - ✅ Document hover effects and transitions
  - ✅ Create fixture files with expected output

#### PHASE1-003: Create Visual Regression Test Suite (8 hours) - ❌ NOT STARTED
- **Task**: Implement visual regression tests using Playwright
- **Success Criteria**: 
  - Screenshots of all placement variations
  - Tests for all iconsets
  - Mobile and desktop views
  - Baseline images stored
- **Files**: 
  - `tests/visual/placements.spec.js`
  - `tests/visual/iconsets.spec.js`
  - `tests/visual/baseline/`
- **Dependencies**: PHASE1-001, PHASE1-002
- **Estimated Time**: 8 hours
- **Implementation**:
  - ✅ Create Playwright test for left placement
  - ✅ Create Playwright test for right placement
  - ✅ Create Playwright test for before_post placement
  - ✅ Create Playwright test for after_post placement
  - ✅ Create Playwright test for shortcode output
  - ✅ Test default iconset rendering
  - ✅ Test flat iconset rendering
  - ✅ Test long_shadow iconset rendering
  - ✅ Test prajin iconset rendering
  - ✅ Capture mobile viewport screenshots
  - ✅ Capture desktop viewport screenshots
  - ✅ Store baseline images for comparison
  - ✅ Create diff reporting for failures

#### PHASE1-004: Write HTML Structure Unit Tests (6 hours) - ❌ NOT STARTED
- **Task**: Create PHPUnit tests for HTML output structure
- **Success Criteria**: 
  - Test zm_sh_btn() output for all scenarios
  - Assert correct HTML elements
  - Assert correct CSS classes
  - Assert correct attributes
- **Files**: 
  - `tests/Unit/HtmlOutputTest.php`
- **Dependencies**: PHASE1-001, PHASE1-002
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Test basic button rendering
  - ✅ Test left placement output
  - ✅ Test right placement output
  - ✅ Test before_post placement output
  - ✅ Test after_post placement output
  - ✅ Test shortcode output
  - ✅ Assert <div> wrapper classes
  - ✅ Assert <a> element generation
  - ✅ Assert href attributes
  - ✅ Assert target="_blank"
  - ✅ Assert nofollow when enabled
  - ✅ Test icon ordering matches input

#### PHASE1-005: Write CSS Output Unit Tests (5 hours) - ❌ NOT STARTED
- **Task**: Test CSS generation and injection
- **Success Criteria**: 
  - Test icon_styles() output
  - Assert correct CSS selectors
  - Assert correct background-image URLs
  - Test auto_hide_btn CSS logic
- **Files**: 
  - `tests/Unit/CssOutputTest.php`
- **Dependencies**: PHASE1-001, PHASE1-002
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Test CSS selector generation
  - ✅ Test background-image URL generation
  - ✅ Test iconset_url variable insertion
  - ✅ Test auto_hide_btn=false CSS
  - ✅ Test auto_hide_btn=true CSS
  - ✅ Test left positioning CSS
  - ✅ Test right positioning CSS
  - ✅ Assert no CSS leakage between iconsets

#### PHASE1-006: Write Options & Settings Unit Tests (4 hours) - ❌ NOT STARTED
- **Task**: Test options handling and sanitization
- **Success Criteria**: 
  - Test option retrieval
  - Test option sanitization
  - Test default values
  - Test invalid input handling
- **Files**: 
  - `tests/Unit/OptionsTest.php`
- **Dependencies**: PHASE1-001
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Test get_option() retrieval
  - ✅ Test default options fallback
  - ✅ Test sanitize_key() on iconset
  - ✅ Test sanitize_html_class() on class
  - ✅ Test URL sanitization
  - ✅ Test boolean option handling
  - ✅ Test array option handling
  - ✅ Test excludes parsing

#### PHASE1-007: Run All Tests on Current Codebase (3 hours) - ❌ NOT STARTED
- **Task**: Execute all tests against current code and save baseline
- **Success Criteria**: 
  - All tests pass on current code
  - Baseline results saved
  - Test coverage report generated
  - Known issues documented
- **Files**: 
  - `tests/results/baseline-report.html`
  - `tests/results/coverage.html`
- **Dependencies**: PHASE1-003, PHASE1-004, PHASE1-005, PHASE1-006
- **Estimated Time**: 3 hours
- **Implementation**:
  - ✅ Run PHPUnit test suite
  - ✅ Run Playwright visual tests
  - ✅ Generate coverage report
  - ✅ Save baseline screenshots
  - ✅ Document any failing tests
  - ✅ Document known issues
  - ✅ Create test execution script
  - ✅ Commit baseline results

#### PHASE1-008: Document All Iconset Structures (4 hours) - ❌ NOT STARTED
- **Task**: Analyze and document all current iconset implementations
- **Success Criteria**: 
  - Each iconset class analyzed
  - Icon arrays documented
  - URL templates documented
  - Type variations documented
- **Files**: 
  - `docs/iconset-analysis.md`
- **Dependencies**: PHASE1-001
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Document default iconset structure
  - ✅ Document flat iconset structure
  - ✅ Document long_shadow iconset structure
  - ✅ Document prajin iconset structure
  - ✅ List all supported networks per iconset
  - ✅ Document URL template patterns
  - ✅ Document image file naming conventions
  - ✅ Document CSS class naming patterns
  - ✅ Document type variations (square/circle)

### Phase 1B: New Architecture Design & Core Classes (50-60 hours)

#### PHASE1-009: Design New Class Architecture (6 hours) - ❌ NOT STARTED
- **Task**: Design PSR-4 class structure for new implementation
- **Success Criteria**: 
  - Namespace defined
  - Class responsibilities defined
  - Dependency injection plan
  - Interface contracts defined
- **Files**: 
  - `docs/architecture-design.md`
  - `src/Interfaces/`
- **Dependencies**: PHASE1-008
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Define root namespace: HtmlSocialShare
  - ✅ Design Renderer interface
  - ✅ Design IconRegistry interface
  - ✅ Design UrlBuilder interface
  - ✅ Design OptionsManager interface
  - ✅ Plan dependency injection container
  - ✅ Document class relationships
  - ✅ Create interface files
  - ✅ Define public API surface

#### PHASE1-010: Create IconRegistry Class (8 hours) - ❌ NOT STARTED
- **Task**: Build unified iconset registry
- **Success Criteria**: 
  - Loads all iconsets from assets/iconset/
  - Supports current iconset structure
  - Returns icon data for rendering
  - Compatible with current format
- **Files**: 
  - `src/IconRegistry.php`
  - `tests/Unit/IconRegistryTest.php`
- **Dependencies**: PHASE1-009
- **Estimated Time**: 8 hours
- **Implementation**:
  - ✅ Create IconRegistry class
  - ✅ Implement directory scanning
  - ✅ Load iconset metadata
  - ✅ Parse icon definitions
  - ✅ Support both old and new directory structures
  - ✅ Cache icon data
  - ✅ Implement getIcon() method
  - ✅ Implement getIconset() method
  - ✅ Handle missing icons gracefully
  - ✅ Write unit tests

#### PHASE1-011: Create UrlBuilder Class (5 hours) - ❌ NOT STARTED
- **Task**: Extract URL generation logic
- **Success Criteria**: 
  - Builds share URLs for all networks
  - Handles %%permalink%% placeholder
  - Handles %%title%% placeholder
  - Matches current URL output exactly
- **Files**: 
  - `src/UrlBuilder.php`
  - `tests/Unit/UrlBuilderTest.php`
- **Dependencies**: PHASE1-009
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Create UrlBuilder class
  - ✅ Implement buildUrl() method
  - ✅ Parse URL templates
  - ✅ Replace %%permalink%% placeholder
  - ✅ Replace %%title%% placeholder
  - ✅ Handle URL encoding
  - ✅ Apply zm_sh_placeholder filter
  - ✅ Write unit tests for all networks

#### PHASE1-012: Create CssGenerator Class (6 hours) - ❌ NOT STARTED
- **Task**: Extract CSS generation logic
- **Success Criteria**: 
  - Generates icon CSS rules
  - Generates positioning CSS
  - Matches current output exactly
  - Supports auto_hide_btn logic
- **Files**: 
  - `src/CssGenerator.php`
  - `tests/Unit/CssGeneratorTest.php`
- **Dependencies**: PHASE1-009, PHASE1-010
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Create CssGenerator class
  - ✅ Implement generateIconCss() method
  - ✅ Implement generatePositioningCss() method
  - ✅ Build background-image rules
  - ✅ Build left/right positioning rules
  - ✅ Handle auto_hide_btn option
  - ✅ Output <style> tags
  - ✅ Write unit tests

#### PHASE1-013: Create ButtonRenderer Class (8 hours) - ❌ NOT STARTED
- **Task**: Build main rendering engine
- **Success Criteria**: 
  - Renders HTML matching current output
  - Uses IconRegistry for data
  - Uses UrlBuilder for URLs
  - Uses CssGenerator for styles
  - Passes all HTML structure tests
- **Files**: 
  - `src/ButtonRenderer.php`
  - `tests/Unit/ButtonRendererTest.php`
- **Dependencies**: PHASE1-010, PHASE1-011, PHASE1-012
- **Estimated Time**: 8 hours
- **Implementation**:
  - ✅ Create ButtonRenderer class
  - ✅ Implement render() method
  - ✅ Build <div> wrapper
  - ✅ Loop through selected icons
  - ✅ Generate <a> elements
  - ✅ Add CSS classes
  - ✅ Add attributes (target, nofollow)
  - ✅ Handle icon ordering
  - ✅ Write comprehensive unit tests

#### PHASE1-014: Create OptionsManager Class (6 hours) - ❌ NOT STARTED
- **Task**: Centralize options handling
- **Success Criteria**: 
  - Loads options from WordPress
  - Provides defaults
  - Sanitizes all values
  - Compatible with current structure
- **Files**: 
  - `src/OptionsManager.php`
  - `tests/Unit/OptionsManagerTest.php`
- **Dependencies**: PHASE1-009
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Create OptionsManager class
  - ✅ Implement get() method
  - ✅ Implement getAll() method
  - ✅ Implement sanitize() method
  - ✅ Define default options
  - ✅ Handle legacy option format
  - ✅ Cache options in memory
  - ✅ Write unit tests

#### PHASE1-015: Create PlacementManager Class (6 hours) - ❌ NOT STARTED
- **Task**: Handle button placement logic
- **Success Criteria**: 
  - Renders left/right placements
  - Renders before/after content
  - Applies correct CSS classes
  - Handles show_in configuration
- **Files**: 
  - `src/PlacementManager.php`
  - `tests/Unit/PlacementManagerTest.php`
- **Dependencies**: PHASE1-013
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Create PlacementManager class
  - ✅ Implement renderLeft() method
  - ✅ Implement renderRight() method
  - ✅ Implement renderBeforePost() method
  - ✅ Implement renderAfterPost() method
  - ✅ Set correct class for each placement
  - ✅ Handle show_on option
  - ✅ Write unit tests

#### PHASE1-016: Create Plugin Bootstrap Class (5 hours) - ❌ NOT STARTED
- **Task**: Main plugin initialization
- **Success Criteria**: 
  - PSR-4 autoloader registered
  - All hooks registered
  - Dependency injection container
  - Backward compatibility maintained
- **Files**: 
  - `src/Plugin.php`
  - `html-social-share.php` (updated)
- **Dependencies**: PHASE1-009, PHASE1-013, PHASE1-014, PHASE1-015
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Create Plugin class
  - ✅ Register PSR-4 autoloader
  - ✅ Create service container
  - ✅ Register WordPress hooks
  - ✅ Initialize ButtonRenderer
  - ✅ Initialize PlacementManager
  - ✅ Register footer action
  - ✅ Register content filter
  - ✅ Maintain zm_sh_btn() global function

#### PHASE1-017: Implement Legacy Function Wrapper (4 hours) - ❌ NOT STARTED
- **Task**: Maintain backward compatibility with zm_sh_btn()
- **Success Criteria**: 
  - Global function works identically
  - All parameters supported
  - All tests pass
- **Files**: 
  - `src/Compatibility/LegacyFunctions.php`
- **Dependencies**: PHASE1-016
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Create LegacyFunctions class
  - ✅ Implement zm_sh_btn() wrapper
  - ✅ Map old parameters to new API
  - ✅ Handle array flip for icons
  - ✅ Call new ButtonRenderer
  - ✅ Maintain function signature
  - ✅ Write tests for legacy function

#### PHASE1-018: Implement Shortcode Handler (4 hours) - ❌ NOT STARTED
- **Task**: Update shortcode to use new classes
- **Success Criteria**: 
  - [zm_sh_btn] shortcode works
  - All attributes supported
  - Output matches current
- **Files**: 
  - `src/Shortcode.php`
- **Dependencies**: PHASE1-016
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Create Shortcode class
  - ✅ Register shortcode handler
  - ✅ Parse shortcode attributes
  - ✅ Sanitize all inputs
  - ✅ Call ButtonRenderer
  - ✅ Handle class attribute
  - ✅ Write shortcode tests

#### PHASE1-019: Implement Widget Handler (4 hours) - ❌ NOT STARTED
- **Task**: Update widget to use new classes
- **Success Criteria**: 
  - Widget works in sidebars
  - Form fields work
  - Output matches current
- **Files**: 
  - `src/Widget.php`
- **Dependencies**: PHASE1-016
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Create Widget class
  - ✅ Extend WP_Widget
  - ✅ Implement widget() method
  - ✅ Implement form() method
  - ✅ Implement update() method
  - ✅ Call ButtonRenderer
  - ✅ Register widget

### Phase 1C: Iconset Build System (30-40 hours)

#### PHASE1-020: Design Iconset Build Process (5 hours) - ❌ NOT STARTED
- **Task**: Design CSS generation from PNG images
- **Success Criteria**: 
  - Process defined for assets/iconset/ → build/
  - CSS template created
  - Build script architecture designed
- **Files**: 
  - `docs/iconset-build-process.md`
- **Dependencies**: PHASE1-008
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Document input structure (assets/iconset/)
  - ✅ Document output structure (build/iconset/)
  - ✅ Design CSS template format
  - ✅ Plan PHP-based CSS generation
  - ✅ Plan SCSS fallback option
  - ✅ Define build trigger (on-demand vs automatic)
  - ✅ Document iconset metadata format

#### PHASE1-021: Create Iconset Builder Class (8 hours) - ❌ NOT STARTED
- **Task**: Build PHP class to generate iconset CSS
- **Success Criteria**: 
  - Scans assets/iconset/ directories
  - Generates CSS for each iconset
  - Outputs to build/iconset/
  - Matches current CSS output
- **Files**: 
  - `src/Build/IconsetBuilder.php`
  - `tests/Unit/IconsetBuilderTest.php`
- **Dependencies**: PHASE1-020
- **Estimated Time**: 8 hours
- **Implementation**:
  - ✅ Create IconsetBuilder class
  - ✅ Scan assets/iconset/ for iconsets
  - ✅ Detect iconset + type combinations
  - ✅ Generate CSS selectors
  - ✅ Generate background-image rules
  - ✅ Generate positioning rules
  - ✅ Generate hover effects
  - ✅ Write to build/iconset/{name}.css
  - ✅ Write unit tests

#### PHASE1-022: Migrate default Iconset (6 hours) - ❌ NOT STARTED
- **Task**: Move default iconset to new structure
- **Success Criteria**: 
  - Images in assets/iconset/default_square/
  - CSS generated in build/
  - Visual output identical
  - Tests pass
- **Files**: 
  - `assets/iconset/default_square/`
  - `build/iconset/default_square.css`
- **Dependencies**: PHASE1-021
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Create assets/iconset/default_square/
  - ✅ Copy PNG images from iconset/default/square/
  - ✅ Run IconsetBuilder
  - ✅ Verify CSS output
  - ✅ Compare with current CSS
  - ✅ Update IconRegistry to load new structure
  - ✅ Run visual tests
  - ✅ Verify identical rendering

#### PHASE1-023: Migrate flat Iconset (5 hours) - ❌ NOT STARTED
- **Task**: Move flat iconset to new structure
- **Success Criteria**: 
  - Both circle and square variants migrated
  - CSS generated correctly
  - Tests pass
- **Files**: 
  - `assets/iconset/flat_square/`
  - `assets/iconset/flat_circle/`
- **Dependencies**: PHASE1-021, PHASE1-022
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Create assets/iconset/flat_square/
  - ✅ Create assets/iconset/flat_circle/
  - ✅ Copy images for both variants
  - ✅ Run IconsetBuilder for both
  - ✅ Verify CSS output
  - ✅ Run visual tests
  - ✅ Verify identical rendering

#### PHASE1-024: Migrate long_shadow Iconset (5 hours) - ❌ NOT STARTED
- **Task**: Move long_shadow iconset to new structure
- **Success Criteria**: 
  - Both variants migrated
  - CSS generated correctly
  - Tests pass
- **Files**: 
  - `assets/iconset/long_shadow_square/`
  - `assets/iconset/long_shadow_circle/`
- **Dependencies**: PHASE1-021, PHASE1-022
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Create directories for both variants
  - ✅ Copy images
  - ✅ Run IconsetBuilder
  - ✅ Verify CSS output
  - ✅ Run visual tests

#### PHASE1-025: Migrate prajin Iconset (5 hours) - ❌ NOT STARTED
- **Task**: Move prajin iconset to new structure
- **Success Criteria**: 
  - Both variants migrated
  - CSS generated correctly
  - Tests pass
- **Files**: 
  - `assets/iconset/prajin_square/`
  - `assets/iconset/prajin_circle/`
- **Dependencies**: PHASE1-021, PHASE1-022
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Create directories for both variants
  - ✅ Copy images
  - ✅ Run IconsetBuilder
  - ✅ Verify CSS output
  - ✅ Run visual tests

#### PHASE1-026: Create WP-CLI Build Command (4 hours) - ❌ NOT STARTED
- **Task**: Add WP-CLI command to rebuild iconset CSS
- **Success Criteria**: 
  - `wp hss build-iconsets` command works
  - Rebuilds all iconset CSS
  - Shows progress and results
- **Files**: 
  - `src/CLI/BuildCommand.php`
- **Dependencies**: PHASE1-021
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Create BuildCommand class
  - ✅ Register with WP-CLI
  - ✅ Implement invoke() method
  - ✅ Call IconsetBuilder
  - ✅ Show progress output
  - ✅ Handle errors gracefully
  - ✅ Document command usage

### Phase 1D: Integration & Testing (30-40 hours)

#### PHASE1-027: Run Full Test Suite on New Code (5 hours) - ❌ NOT STARTED
- **Task**: Execute all tests against new implementation
- **Success Criteria**: 
  - All unit tests pass
  - All visual tests pass
  - No regressions detected
- **Files**: 
  - `tests/results/new-implementation-report.html`
- **Dependencies**: PHASE1-019, PHASE1-026
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Run PHPUnit test suite
  - ✅ Run Playwright visual tests
  - ✅ Compare with baseline
  - ✅ Document any differences
  - ✅ Fix failing tests
  - ✅ Re-run until all pass

#### PHASE1-028: Visual Comparison Verification (6 hours) - ❌ NOT STARTED
- **Task**: Manually verify visual output matches
- **Success Criteria**: 
  - Side-by-side screenshots
  - Pixel-perfect comparison
  - Document any intentional differences
- **Files**: 
  - `docs/visual-comparison-report.md`
- **Dependencies**: PHASE1-027
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Generate screenshots of old code
  - ✅ Generate screenshots of new code
  - ✅ Compare all placements
  - ✅ Compare all iconsets
  - ✅ Document differences
  - ✅ Verify differences are acceptable
  - ✅ Create comparison report

#### PHASE1-029: Performance Benchmarking (4 hours) - ❌ NOT STARTED
- **Task**: Compare performance of old vs new
- **Success Criteria**: 
  - Page load time measured
  - Database queries counted
  - Memory usage compared
  - New code is equal or better
- **Files**: 
  - `docs/performance-comparison.md`
- **Dependencies**: PHASE1-027
- **Estimated Time**: 4 hours
- **Implementation**:
  - ✅ Install Query Monitor
  - ✅ Measure old code performance
  - ✅ Measure new code performance
  - ✅ Compare page load times
  - ✅ Compare query counts
  - ✅ Compare memory usage
  - ✅ Document results

#### PHASE1-030: Migration Script for Options (6 hours) - ❌ NOT STARTED
- **Task**: Create migration script for users upgrading
- **Success Criteria**: 
  - Reads old options format
  - Writes new options format
  - No data loss
  - Idempotent (safe to run multiple times)
- **Files**: 
  - `src/Migration/OptionsMigration.php`
  - `tests/Unit/OptionsMigrationTest.php`
- **Dependencies**: PHASE1-014
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Create OptionsMigration class
  - ✅ Read old option: zm_shbt_fld
  - ✅ Map all fields to new structure
  - ✅ Handle edge cases
  - ✅ Create backup of old options
  - ✅ Write new options
  - ✅ Mark migration complete
  - ✅ Write unit tests

#### PHASE1-031: Update Settings Page (8 hours) - ❌ NOT STARTED
- **Task**: Create new settings UI (keep structure similar)
- **Success Criteria**: 
  - All current options available
  - Save functionality works
  - Admin preview works
  - Uses WordPress Settings API
- **Files**: 
  - `src/Admin/SettingsPage.php`
  - `assets/admin-ui/settings.js` (minimal)
- **Dependencies**: PHASE1-014
- **Estimated Time**: 8 hours
- **Implementation**:
  - ✅ Create SettingsPage class
  - ✅ Register admin menu
  - ✅ Use Settings API
  - ✅ Title field
  - ✅ Excludes field
  - ✅ Google Analytics toggle
  - ✅ Auto-hide button toggle
  - ✅ Use port toggle
  - ✅ Nofollow toggle
  - ✅ Iconset selector
  - ✅ Placement options (show_in)
  - ✅ Network selection checkboxes
  - ✅ Preview functionality
  - ✅ Save handler

#### PHASE1-032: Update Admin Assets (5 hours) - ❌ NOT STARTED
- **Task**: Update admin CSS/JS for new settings
- **Success Criteria**: 
  - Modern admin styling
  - Icon preview works
  - No JavaScript for frontend
  - Minimal JS for admin only
- **Files**: 
  - `assets/admin-ui/admin.css`
  - `assets/admin-ui/admin.js`
- **Dependencies**: PHASE1-031
- **Estimated Time**: 5 hours
- **Implementation**:
  - ✅ Update admin CSS
  - ✅ Update icon preview JS
  - ✅ Update iconset selector
  - ✅ Handle form interactions
  - ✅ Maintain backward compatibility

#### PHASE1-033: Documentation Update (6 hours) - ❌ NOT STARTED
- **Task**: Update all documentation for new architecture
- **Success Criteria**: 
  - README updated
  - Developer docs updated
  - API reference created
  - Migration guide written
- **Files**: 
  - `README.md`
  - `docs/developer-guide.md`
  - `docs/api-reference.md`
  - `docs/migration-guide.md`
- **Dependencies**: All previous tasks
- **Estimated Time**: 6 hours
- **Implementation**:
  - ✅ Update README with new info
  - ✅ Document new class structure
  - ✅ Document extensibility hooks
  - ✅ Write migration guide
  - ✅ Document iconset creation
  - ✅ Update code examples

## ⏱️ Estimated Timeline

- **Total Estimated Time**: 150-190 hours (4-5 weeks full-time)
- **Critical Path**: 100 hours (2.5 weeks)
- **Can be parallelized**: 
  - Visual tests (PHASE1-003) can run parallel to unit tests (PHASE1-004-006)
  - Iconset migrations (PHASE1-022-025) can run in parallel
- **Dependencies**: Must complete Phase 1A before starting 1B

## 🎯 Phase 1 Success Criteria Summary

1. ✅ **All Tests Pass**: New code passes all baseline tests
2. ✅ **Visual Parity**: Output is pixel-perfect identical
3. ✅ **Performance**: Equal or better than current
4. ✅ **No Breaking Changes**: All current features work
5. ✅ **Clean Architecture**: PSR-4, testable, maintainable
6. ✅ **Documentation**: Complete developer docs
7. ✅ **Migration Path**: Safe upgrade from current version

## 📝 Testing Strategy

### Test Types

1. **Unit Tests (PHPUnit)**
   - Test individual classes
   - Mock dependencies
   - Fast feedback loop

2. **Integration Tests (PHPUnit + WordPress)**
   - Test WordPress integration
   - Test hooks and filters
   - Test database operations

3. **Visual Regression Tests (Playwright)**
   - Screenshot comparison
   - Pixel-perfect verification
   - Multiple viewports

4. **Performance Tests**
   - Page load time
   - Query count
   - Memory usage

### Test Coverage Goals

- **Code Coverage**: 80%+ for core classes
- **Visual Coverage**: 100% of placements and iconsets
- **Integration Coverage**: All WordPress hooks tested

## 🔄 Migration Strategy

### For End Users

1. **Automatic Migration**
   - Runs on plugin update
   - Backs up old options
   - Migrates to new structure
   - Shows admin notice on completion

2. **Rollback Option**
   - Keep old options for 30 days
   - Provide rollback button in admin
   - Log all migration actions

3. **Validation**
   - Verify all options migrated
   - Check button rendering
   - Test all placements

### For Developers

1. **Backward Compatibility**
   - zm_sh_btn() function still works
   - All filters/actions preserved
   - Deprecation notices for old APIs

2. **New APIs**
   - PSR-4 classes available
   - New filters for extensibility
   - Better documentation

## 🚀 Future Phases (Not in Phase 1)

### Phase 2: Enhanced Admin UI with Tailwind
- Modern React-based admin
- Drag-and-drop icon ordering
- Live preview
- Better iconset management

### Phase 3: New Networks & Features
- Add modern networks (Threads, Mastodon, etc.)
- Share counts (optional)
- Analytics integration (optional)
- Block editor integration

### Phase 4: Advanced Features
- Multiple button sets per page
- Custom styling options
- A/B testing
- Performance optimizations

## 📊 Key Metrics to Track

1. **Functionality Parity**
   - All features work: ✅/❌
   - All placements work: ✅/❌
   - All iconsets work: ✅/❌

2. **Performance**
   - Page load time: [current] vs [new]
   - Query count: [current] vs [new]
   - Memory usage: [current] vs [new]

3. **Code Quality**
   - Test coverage: [X]%
   - PSR-4 compliance: ✅/❌
   - Documentation coverage: [X]%

## 🎬 Getting Started

### Prerequisites
- WordPress 5.8+ test environment
- PHP 8.0+
- Composer
- Node.js (for Playwright)
- Git

### Initial Setup
```bash
# Install dependencies
composer install
npm install

# Set up test environment
wp-env start

# Run baseline tests
composer test
npm test
```

### Development Workflow
1. Create feature branch
2. Write tests first
3. Implement feature
4. Run tests
5. Commit with conventional commits
6. Create PR

## 📚 Reference Materials

### Current Implementation
- `docs/options.md` - Current options structure
- `html-social-share.php` - Main plugin file
- `iconsets.php` - Iconset system
- `form.php` - Admin form handling

### archive2 Reference (what to use/avoid)
- ✅ Use: PSR-4 structure, renderer pattern, tests
- ❌ Avoid: Tab-based settings, profile system, SVG focus

### Documentation
- WordPress Plugin Handbook
- PSR-4 Autoloading Standard
- WordPress Coding Standards
- PHPUnit Documentation
- Playwright Documentation

---

## 🎯 Critical Success Factors

1. **Test First**: Always write tests before changing code
2. **No Regressions**: Every change must pass baseline tests
3. **Visual Parity**: Frontend output must be identical
4. **Clean Code**: Follow PSR-4 and WordPress standards
5. **Documentation**: Every class and method documented
6. **Backward Compatibility**: Don't break existing sites

## 🤝 Collaboration Guidelines

### Commit Messages
```
[PHASE1-XXX] Brief description

Detailed description of changes
- What changed
- Why it changed
- Any breaking changes

Related to: PHASE1-XXX
```

### Pull Request Template
```
## Phase 1 Task: PHASE1-XXX

### Description
[What this PR does]

### Testing
- [ ] Unit tests pass
- [ ] Visual tests pass
- [ ] Manual testing completed

### Checklist
- [ ] Tests written first
- [ ] All tests pass
- [ ] Documentation updated
- [ ] No breaking changes
```

---

**Remember**: The goal of Phase 1 is to rewrite from the ground up while maintaining 100% compatibility with current behavior. Test everything, break nothing.
