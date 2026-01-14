# PHP Compatibility Reference

**Generated:** January 14, 2026
**Plugin Version:** 2.2.1
**Scope:** PHP 5.6 - 8.5

---

## Quick Reference

| PHP Version | Support Status | Plugin Compatible | Notes |
|-------------|----------------|-------------------|-------|
| 5.6 | ❌ EOL | ⚠️ Works | Dec 2018 EOL, not recommended |
| 7.0 | ❌ EOL | ✅ Yes | Dec 2018 EOL |
| 7.1 | ❌ EOL | ✅ Yes | Dec 2019 EOL |
| 7.2 | ❌ EOL | ✅ Yes | Nov 2020 EOL |
| 7.3 | ❌ EOL | ✅ Yes | Dec 2021 EOL |
| 7.0 | ❌ EOL | ✅ Yes | Dec 2018 EOL, **recommended minimum** |
| 8.0 | ❌ EOL | ✅ Yes | Nov 2023 EOL |
| 8.1 | Security | ✅ Yes | Security fixes only |
| 8.2 | Active | ✅ Yes | Current stable |
| 8.3 | Active | ✅ Yes | Current stable |
| 8.4 | Active | ⚠️ Minor | See issues below |
| 8.5 | Dev | ⚠️ Minor | See issues below |

---

## PHP 8.x Specific Issues

### 1. Dynamic Property Deprecation (PHP 8.2+)

**Status:** ✅ Fixed in v2.2.1

Dynamic property creation is deprecated in PHP 8.2. This was addressed in version 2.2.1 by adding explicit property declarations.

### 2. `extract()` Usage

**Status:** ⚠️ Recommend Refactoring

**Locations:**
| File | Line | Code |
|------|------|------|
| `html-social-share.php` | 252 | `extract($iconset);` |
| `html-social-share.php` | 311 | `extract($icon);` |
| `widget.php` | 25 | `extract( $args );` |

**Issue:** `extract()` can cause issues with dynamic property creation in PHP 8.2+.

**Recommended Fix for `widget.php:25`:**
```php
// Before
extract( $args );
echo $before_widget;

// After
$before_widget = $args['before_widget'] ?? '';
$after_widget = $args['after_widget'] ?? '';
$before_title = $args['before_title'] ?? '';
$after_title = $args['after_title'] ?? '';
echo $before_widget;
```

**Recommended Fix for `html-social-share.php:311`:**
```php
// Before
extract($icon);

// After
$class = $icon['class'] ?? '';
$image = $icon['image'] ?? '';
$url = $icon['url'] ?? '';
$name = $icon['name'] ?? '';
```

### 3. Implicit Nullable Types

**Status:** ⚠️ Future Concern (PHP 8.4+)

No issues found currently, but PHP 8.4 deprecates implicit nullable parameter types.

---

## Code Patterns by PHP Version

### PHP 5.4+ Required Features Used

| Feature | Location | PHP Requirement |
|---------|----------|-----------------|
| Short array syntax `[]` | Multiple files | PHP 5.4+ |
| Array dereferencing | `form.php` | PHP 5.4+ |

### PHP 5.3+ Required Features Used

| Feature | Location | PHP Requirement |
|---------|----------|-----------------|
| Anonymous functions | `html-social-share.php:81` | PHP 5.3+ |
| `__DIR__` constant | Not used | - |
| Namespaces | Not used | - |

### Modern PHP Features NOT Used

These could be adopted for better code quality:

| Feature | Benefit | PHP Requirement |
|---------|---------|-----------------|
| Type declarations | Better code safety | PHP 7.0+ |
| Null coalescing `??` | Cleaner null checks | PHP 7.0+ |
| Return type declarations | Self-documenting | PHP 7.0+ |
| Typed properties | Better encapsulation | PHP 7.4+ |
| Match expression | Cleaner switch cases | PHP 8.0+ |
| Constructor promotion | Less boilerplate | PHP 8.0+ |

---

## Recommended Minimum Version

**Recommend setting `Requires PHP: 7.0` in plugin headers.**

**Rationale:**
1. PHP 7.0 has wide hosting support among WordPress users
2. Many WordPress hosts still run PHP 7.x
3. Most security-conscious hosts have upgraded
4. WordPress 6.0+ requires PHP 5.6 but recommends 7.4+

---

## Static Analysis Results

### PHPStan / Psalm Recommendations

If running static analysis, expect these warnings:

1. **Unsafe `$_POST` access** in `iconsets.php`
2. **Unsafe `$_SERVER` access** in `html-social-share.php`
3. **Missing return types** on all functions
4. **Mixed types** in array operations
5. **Use of `extract()`** flagged as unsafe

---

## Testing Matrix

To ensure PHP compatibility, test on:

```yaml
# Suggested CI matrix
php:
  - '7.0'
  - '8.0'
  - '8.1'
  - '8.2'
  - '8.3'
wordpress:
  - '6.5'
  - '6.7'
  - '6.8'
  - '6.9'
```

---

*Reference document for PHP compatibility testing*
