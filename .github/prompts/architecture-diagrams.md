# Architecture Diagrams

## 🏗️ Current Architecture (Before Rewrite)

```
┌─────────────────────────────────────────────────────────────┐
│                     WordPress Request                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              Global Function: zm_sh_btn()                    │
│                                                              │
│  • Takes options array                                       │
│  • Gets iconset object                                       │
│  • Loops through icons                                       │
│  • Generates HTML                                            │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                  Iconset Class Hierarchy                     │
│                                                              │
│  __iconset_parent_class                                     │
│         │                                                    │
│         ├─→ zm_sh_iconset_default                          │
│         ├─→ zm_sh_iconset_flat                             │
│         ├─→ zm_sh_iconset_long_shadow                      │
│         └─→ zm_sh_iconset_prajin                           │
│                                                              │
│  Each contains:                                              │
│  • $icons array (network definitions)                       │
│  • $types array (square/circle)                            │
│  • $stylesheet path                                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                     HTML Generation                          │
│                                                              │
│  <div class="zmshbt {iconset} {type}">                     │
│    <a class="{network}" href="{url}"></a>                  │
│  </div>                                                      │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                   CSS Injection (Footer)                     │
│                                                              │
│  <style>                                                     │
│    .zmshbt.{iconset}.{type} .{network} {                   │
│      background-image: url('{path}');                       │
│    }                                                         │
│  </style>                                                    │
└─────────────────────────────────────────────────────────────┘
```

## 🚀 New Architecture (Phase 1)

```
┌─────────────────────────────────────────────────────────────┐
│                     WordPress Request                        │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                    Plugin Bootstrap                          │
│                  HtmlSocialShare\Plugin                      │
│                                                              │
│  • PSR-4 Autoloader                                         │
│  • Service Container                                         │
│  • Hook Registration                                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
            ┌──────────┴──────────┐
            │                     │
            ▼                     ▼
┌─────────────────────┐  ┌─────────────────────┐
│  OptionsManager     │  │   IconRegistry      │
│                     │  │                     │
│  • Load options     │  │  • Scan iconsets    │
│  • Provide defaults │  │  • Load metadata    │
│  • Sanitize values  │  │  • Cache data       │
└──────────┬──────────┘  └──────────┬──────────┘
           │                        │
           └────────┬───────────────┘
                    │
                    ▼
         ┌─────────────────────┐
         │   PlacementManager  │
         │                     │
         │  • Left/Right       │
         │  • Before/After     │
         │  • Shortcode        │
         └──────────┬──────────┘
                    │
                    ▼
         ┌─────────────────────┐
         │   ButtonRenderer    │
         │                     │
         │  Uses:              │
         │  • IconRegistry     │
         │  • UrlBuilder       │
         │  • CssGenerator     │
         └──────────┬──────────┘
                    │
         ┌──────────┴──────────┐
         │                     │
         ▼                     ▼
┌─────────────────┐   ┌─────────────────┐
│   UrlBuilder    │   │  CssGenerator   │
│                 │   │                 │
│  • Build URLs   │   │  • Icon CSS     │
│  • Placeholders │   │  • Positioning  │
│  • Encoding     │   │  • Hover        │
└─────────────────┘   └─────────────────┘
         │                     │
         └──────────┬──────────┘
                    │
                    ▼
         ┌─────────────────────┐
         │    Final Output     │
         │                     │
         │  HTML + CSS         │
         │  (Identical to      │
         │   current!)         │
         └─────────────────────┘
```

## 🎨 Iconset System Flow

### Current System
```
iconset/
  default/
    ssb.php ──────┐
    style.css     │
    square/       │   Manual
      *.png       │   Creation
    circle/       │
      *.png ──────┘
         │
         ▼
    Used directly
         │
         ▼
    CSS loaded
         │
         ▼
    Icons display
```

### New System (Phase 1)
```
assets/iconset/
  default_square/
    *.png
  flat_circle/
    *.png
         │
         ▼
  IconsetBuilder ←──── WP-CLI Command
         │            (Manual Trigger)
         ▼
  Scan directories
  Generate CSS
         │
         ▼
  build/iconset/
    default_square.css
    flat_circle.css
         │
         ▼
    CSS loaded
    (auto-generated)
         │
         ▼
    Icons display
```

## 🔄 Request Flow Comparison

### Current Flow
```
1. Request
   ↓
2. WordPress loads plugin
   ↓
3. Register hooks
   ↓
4. Content filter or footer action fires
   ↓
5. zm_sh_btn() called
   ↓
6. Get options from DB
   ↓
7. Get iconset object
   ↓
8. Loop through icons array
   ↓
9. Build URLs manually
   ↓
10. Echo HTML
    ↓
11. Footer hook
    ↓
12. Inject CSS <style> tags
    ↓
13. Done
```

### Phase 1 Flow
```
1. Request
   ↓
2. WordPress loads plugin
   ↓
3. Bootstrap → Service Container
   ↓
4. Content filter or footer action fires
   ↓
5. PlacementManager determines where
   ↓
6. OptionsManager provides config
   ↓
7. IconRegistry provides icon data
   ↓
8. UrlBuilder generates URLs
   ↓
9. ButtonRenderer generates HTML
   ↓
10. CssGenerator generates CSS
    ↓
11. Output HTML + CSS
    ↓
12. Done
```

**Result**: Same output, better architecture!

## 📦 Class Dependency Graph

```
                    Plugin (Bootstrap)
                         │
           ┌─────────────┼─────────────┐
           │             │             │
           ▼             ▼             ▼
    OptionsManager  IconRegistry  PlacementManager
                         │             │
                         │             ▼
                         │      ButtonRenderer
                         │             │
                         │    ┌────────┼────────┐
                         │    │        │        │
                         ▼    ▼        ▼        ▼
                    IconRegistry  UrlBuilder  CssGenerator
                         │
                         ▼
                   IconsetBuilder
                  (Build-time only)
```

## 🧪 Test Strategy Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                        Phase 1A                              │
│                  Capture Current Behavior                    │
│                                                              │
│  ┌──────────────────────────────────────────────────┐      │
│  │              Visual Regression Tests              │      │
│  │  (Playwright - Screenshot Comparison)            │      │
│  └──────────────────────────────────────────────────┘      │
│                         │                                    │
│  ┌──────────────────────────────────────────────────┐      │
│  │                Unit Tests                         │      │
│  │  (PHPUnit - HTML/CSS/Options)                    │      │
│  └──────────────────────────────────────────────────┘      │
│                         │                                    │
│                         ▼                                    │
│                 Baseline Saved                               │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                     Phase 1B & 1C                            │
│                  Implement New Code                          │
│                                                              │
│  Write classes → Write tests → Implement → Run tests        │
└──────────────────────┬───────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                       Phase 1D                               │
│              Verify Against Baseline                         │
│                                                              │
│  New Output == Baseline? ──Yes──→ Success! ✅               │
│       │                                                      │
│       No                                                     │
│       │                                                      │
│       ▼                                                      │
│    Fix Issues                                                │
│       │                                                      │
│       └──────→ Re-test                                       │
└─────────────────────────────────────────────────────────────┘
```

## 🎯 Migration Path

```
Current State                    Phase 1                      Future
─────────────                   ─────────                    ────────

Old Options         →→→    Migration Script    →→→    New Options
(zm_shbt_fld)                    │                    (hss_options)
                                 │
                                 ▼
                          Backup Created
                                 │
                                 ▼
                          Options Mapped
                                 │
                                 ▼
                         Validation Check
                                 │
                    ┌────────────┴────────────┐
                    │                         │
                Success                     Failed
                    │                         │
                    ▼                         ▼
            New System Active           Rollback Available
                    │                         │
                    │                    Admin Notice
                    │                    "Migration failed"
                    │                         │
                    ▼                         │
            Admin Notice                      │
            "Migration Success"               │
                    │                         │
                    └─────────────────────────┘
```

## 🔧 Build System Flow

```
                    Developer/Admin
                         │
                    ┌────┴────┐
                    │         │
                Manual    WP-CLI Command
                Upload    (build-iconsets)
                    │         │
                    └────┬────┘
                         │
                         ▼
              ┌──────────────────────┐
              │   IconsetBuilder     │
              │                      │
              │  1. Scan directories │
              │  2. Find PNG images  │
              │  3. Generate CSS     │
              │  4. Write to build/  │
              └──────────┬───────────┘
                         │
                         ▼
              build/iconset/{name}.css
                         │
                         ▼
              WordPress enqueues CSS
                         │
                         ▼
              Icons display with
              background-image
```

## 📊 Options Structure Comparison

### Current (Flat)
```
zm_shbt_fld
  │
  ├── title
  ├── excludes
  ├── g_analytics
  ├── iconset
  ├── show_in
  │     ├── show_left
  │     ├── show_right
  │     ├── show_before_post
  │     └── show_after_post
  └── icons
        ├── facebook
        ├── twitter
        └── linkedin
```

### archive2 (Complex - Avoid)
```
hss_options
  │
  ├── general
  │     ├── title
  │     └── enabled
  ├── networks
  │     ├── facebook
  │     │     ├── enabled
  │     │     ├── label
  │     │     └── handle
  │     └── twitter
  ├── display
  │     ├── positions
  │     └── styling
  └── advanced
        └── analytics
```

### Phase 1 (Same as Current)
```
hss_options
  │
  ├── title
  ├── excludes
  ├── g_analytics
  ├── iconset
  ├── show_in
  │     ├── show_left
  │     ├── show_right
  │     ├── show_before_post
  │     └── show_after_post
  └── icons
        ├── facebook
        ├── twitter
        └── linkedin
```

**Why?** Keep migration simple!

---

## 🎬 Summary

These diagrams show:

1. **Current system** is monolithic but works
2. **Phase 1 system** is modular and maintainable
3. **Output remains identical** - same HTML/CSS
4. **Migration is straightforward** - no data loss
5. **Build system** automates CSS generation
6. **Test strategy** ensures no regressions

**Key Takeaway**: Better architecture, same behavior, zero breaking changes! 🚀
