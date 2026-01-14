# Deprecated Platform Migration Guide

**Last Updated:** January 14, 2026
**Plugin Version:** 2.2.1

---

## Overview

This document tracks social platforms that have been deprecated, discontinued, or rebranded and require updates in the Html Social Share Buttons plugin.

---

## Critical: Non-Functional Platforms

These platforms are **completely discontinued** and their share functionality is broken:

### 1. Google Plus ❌

| Detail | Information |
|--------|-------------|
| **Status** | Shut down April 2, 2019 |
| **Share URL** | `https://plus.google.com` (returns 404) |
| **Action Required** | Remove from plugin |

**Affected Files:**
| File | Line(s) | Content |
|------|---------|---------|
| `html-social-share.php` | 40 | `"googlepluse" => 1` in defaults |
| `iconset/default/ssb.php` | 40-46 | Google Plus icon definition |
| `iconset/flat/ssb.php` | 32-38 | Google Plus icon definition |
| `iconset/long_shadow/ssb.php` | 35-41 | Google Plus icon definition |
| `iconset/prajin/ssb.php` | 32-38 | Google Plus icon definition |
| `shortcode.php` | 18 | `"googlepluse" => "on"` |
| `Readme.txt` | 54 | Documentation reference |

> [!NOTE]
> The key is misspelled as "googlepluse" (missing 's'). This should be noted during migration.

---

### 2. Google Bookmarks ❌

| Detail | Information |
|--------|-------------|
| **Status** | Discontinued September 30, 2021 |
| **Share URL** | `google.com/bookmarks/mark` (discontinued) |
| **Action Required** | Remove from plugin |

**Affected Files:**
| File | Line(s) | Content |
|------|---------|---------|
| `html-social-share.php` | 41 | `"bookmark" => 1` in defaults |
| `iconset/default/ssb.php` | 47-53 | Bookmark icon definition |
| `iconset/flat/ssb.php` | 39-45 | Bookmark icon definition |
| `iconset/long_shadow/ssb.php` | 42-48 | Bookmark icon definition |
| `iconset/prajin/ssb.php` | 39-45 | Bookmark icon definition |
| `shortcode.php` | 19 | `"bookmark" => "on"` |
| `Readme.txt` | 55 | Documentation reference |

---

## High Priority: Rebranding Required

### 3. Twitter → X (Rebranded July 2023) ⚠️

| Detail | Information |
|--------|-------------|
| **Status** | Rebranded to "X" on July 23, 2023 |
| **Old Domain** | `twitter.com` |
| **New Domain** | `x.com` |
| **Share Functionality** | Still works on both domains |
| **Action Required** | Update branding and optionally URL |

**Current Implementation:**
```php
'twitter' => array(
    'id' => 'twitter',
    'name' => "Twitter",
    'class' => 'twitter',
    'image' => 'twitter.png',
    'url' => "http://twitter.com/share?url=%%permalink%%&amp;text=%%title%%",
)
```

**Recommended Update:**
```php
'x' => array(
    'id' => 'x',
    'name' => "X (Twitter)",
    'class' => 'x',
    'image' => 'x.png',  // Needs new icon
    'url' => "https://x.com/intent/tweet?url=%%permalink%%&text=%%title%%",
)
```

**Affected Files:**
| File | Changes Needed |
|------|----------------|
| `html-social-share.php` | Line 38, 206-207 |
| All iconset `ssb.php` files | Icon definitions |
| `shortcode.php` | Line 16 default |
| `Readme.txt` | Lines 20, 52 |
| Icon images | Need X logo (4 iconsets × 2 styles = 8 images) |

**Backward Compatibility Strategy:**
```php
// Keep 'twitter' as alias for existing users
'twitter' => array(
    'id' => 'twitter',
    'name' => "X (Twitter)",  // Updated name
    'class' => 'x',           // New class for styling
    'image' => 'x.png',       // New icon
    'url' => "https://x.com/intent/tweet?url=%%permalink%%&text=%%title%%",
),
// Future: Add 'x' as primary key
```

---

## Working Platforms ✅

These platforms are fully functional and require no changes:

| Platform | Status | Last Verified |
|----------|--------|---------------|
| Facebook | ✅ Working | Jan 2026 |
| LinkedIn | ✅ Working | Jan 2026 |
| Pinterest | ✅ Working | Jan 2026 |
| Email (mailto:) | ✅ Working | Jan 2026 |

---

## Suggested New Platforms

Consider adding these popular sharing platforms:

| Platform | Share URL Format | Priority |
|----------|------------------|----------|
| **WhatsApp** | `https://api.whatsapp.com/send?text={url}` | High |
| **Telegram** | `https://t.me/share/url?url={url}&text={title}` | High |
| **Reddit** | `https://reddit.com/submit?url={url}&title={title}` | Medium |
| **Threads** | No official share API yet | Low |
| **Bluesky** | `https://bsky.app/intent/compose?text={url}` | Low |

---

## Migration Checklist

### Phase 1: Remove Discontinued Platforms
- [ ] Remove Google Plus from all iconset files
- [ ] Remove Google Bookmarks from all iconset files
- [ ] Remove from `html-social-share.php` defaults
- [ ] Remove from `shortcode.php` defaults
- [ ] Update Readme.txt documentation
- [ ] Add deprecation notice for existing users

### Phase 2: Update Twitter to X
- [ ] Create new X icon images for all iconsets
- [ ] Update icon definitions to use X branding
- [ ] Update share URLs to use x.com
- [ ] Test backward compatibility with 'twitter' key
- [ ] Update documentation

### Phase 3: Add New Platforms (Optional)
- [ ] Add WhatsApp sharing
- [ ] Add Telegram sharing
- [ ] Add Reddit sharing
- [ ] Create icons for new platforms
- [ ] Update documentation

---

## User Communication

When releasing the update, notify users about:

1. **Breaking Changes:**
   - Google Plus sharing removed (non-functional since 2019)
   - Google Bookmarks removed (non-functional since 2021)

2. **Improvements:**
   - Twitter updated to X branding
   - Share functionality still works for existing 'twitter' shortcodes

3. **Recommended Actions:**
   - Review share button configurations
   - Update shortcodes if using 'googlepluse' or 'bookmark'

---

*Document generated as part of compatibility audit*
