# Advanced Settings Reference

This document provides comprehensive information about all advanced and legacy settings in HTML Social Share Buttons plugin.

## Overview

Starting with version 3.0, the plugin has migrated to a new React-based admin interface while maintaining full backward compatibility with legacy v2.x settings. All settings that were available in v2.x remain accessible in v3.0+.

## Settings Location Guide

### Display & Appearance Settings

**Location:** Settings → Social Share Settings → Appearance Tab

| Setting | Legacy Key (v2.x) | New Key (v3.0+) | Description |
|---------|------------------|-----------------|-------------|
| Share Button Title | `title` | `title` | Heading text displayed above or with share buttons |
| Icon Style/Set | `iconset` | `icon_style` | Visual theme for share button icons (e.g., flat, square, rounded) |
| Button Size | N/A | `button_size` | Size of buttons: small, medium, or large |
| Button Spacing | N/A | `button_spacing` | Space between buttons in pixels |

**How to use:**
1. Navigate to Settings → Social Share Settings
2. Click on the "Appearance" tab
3. Modify title, icon style, and button appearance
4. Click "Save Changes"

---

### Network Settings

**Location:** Settings → Social Share Settings → Networks Tab

| Setting | Legacy Key (v2.x) | New Key (v3.0+) | Description |
|---------|------------------|-----------------|-------------|
| Enabled Networks | `icons` (array) | `enabled_networks` | List of social networks to display (e.g., Facebook, Twitter, LinkedIn) |
| Network Order | N/A | `network_order` | Custom order for displaying networks |

**Legacy `icons` format:**
- v2.x used associative array: `['facebook' => 1, 'twitter' => 1]`
- v3.0+ uses simple array: `['facebook', 'twitter', 'linkedin']`

**Network name mappings:**
- `googlepluse` → `googleplus`
- `mail` → `email`
- All other names preserved

---

### Placement Settings

**Location:** Settings → Social Share Settings → Placement Tab

| Setting | Legacy Key (v2.x) | New Key (v3.0+) | Description | Default |
|---------|------------------|-----------------|-------------|---------|
| Exclude Pages | `excludes` | `exclude_pages` | Comma-separated list of page IDs, slugs, or titles to exclude | Empty |
| Show Before Post | `show_before_post` | `before_content` | Display buttons before post content | false |
| Show After Post | `show_after_post` | `after_content` | Display buttons after post content | true |
| Floating Left Side | `show_left` | `floating_left` | Show floating buttons on left side of screen | false |
| Floating Right Side | `show_right` | `floating_right` | Show floating buttons on right side of screen | false |

**Legacy Placement Options Section:**

In v3.0+, these legacy placement options are grouped under "Legacy Placement Options" in the Placement tab. This section provides individual toggles for backward compatibility with v2.x configurations.

**How to exclude specific pages:**
1. Go to Placement tab
2. Find "Exclude Pages" field (under "Automatic Placement" or "Legacy Placement Options")
3. Enter page IDs, slugs, or titles separated by commas
4. Examples:
   - By ID: `1, 5, 10`
   - By slug: `about-us, contact, privacy-policy`
   - Mixed: `1, about-us, 42, contact`

**Floating Buttons:**

Floating buttons appear as a sidebar on desktop screens. They scroll with the page and remain visible as users navigate content.

- **Left vs Right:** Choose which side of the screen displays floating buttons
- **Auto-hide:** Can be combined with "Auto Hide Buttons" advanced option (see below)
- **Mobile behavior:** Floating buttons automatically adapt on mobile devices

---

### Advanced Settings

**Location:** Settings → Social Share Settings → Advanced Tab

| Setting | Legacy Key (v2.x) | New Key (v3.0+) | Description | Default | Impact |
|---------|------------------|-----------------|-------------|---------|--------|
| Google Analytics | `g_analytics` | `google_analytics` | Enable Google Analytics social tracking | false | Adds GA event tracking to share clicks |
| Auto Hide Buttons | `auto_hide_btn` | `auto_hide_buttons` | Auto-hide floating buttons on page load | false | Buttons slide in when user scrolls |
| Use Port in URL | `use_port` | `use_port_in_url` | Include port number in shared URLs | false | Shares URLs like `example.com:443` |
| No-Follow Links | `nofollow` | `nofollow_links` | Add `rel="nofollow"` to share links | true | Affects SEO/link juice |
| Cache Enabled | N/A | `cache_enabled` | Enable caching for performance | true | Improves page load speed |
| Cache Duration | N/A | `cache_duration` | Cache lifetime in seconds | 3600 | How long to cache share counts |
| Debug Mode | N/A | `debug_mode` | Enable debug logging | false | For troubleshooting issues |

#### Google Analytics

**What it does:**
- Tracks social shares as events in Google Analytics
- Provides insights into which networks are most popular
- Tracks which content is shared most frequently

**Requirements:**
- Google Analytics must be installed on your site
- Works with GA3 (Universal Analytics) and GA4

**Event structure:**
- Event Category: "Social"
- Event Action: "Share"
- Event Label: Network name (e.g., "Facebook", "Twitter")

**How to verify it's working:**
1. Enable Google Analytics option
2. Share a page using one of the buttons
3. Check Google Analytics Real-Time Events report
4. Look for "Social" category events

#### Auto Hide Buttons

**What it does:**
- Floating buttons (left/right sidebar) are hidden initially
- Buttons slide into view when user scrolls down the page
- Reduces visual clutter on initial page load

**Best practices:**
- Use with floating left/right placement only
- Recommended for long-form content (blog posts, articles)
- Not recommended for homepage or landing pages

**Technical details:**
- Uses CSS transforms for smooth animation
- Triggers after 100-200px scroll
- Respects user's reduced motion preferences

#### Use Port in URL

**What it does:**
- Includes the port number when constructing shared URLs
- Example: `https://example.com:443/page` instead of `https://example.com/page`

**When to enable:**
- Your site uses non-standard ports (not 80/443)
- Testing on localhost with custom ports
- Behind reverse proxy or load balancer requiring specific ports

**When to keep disabled (default):**
- Standard HTTP (port 80) or HTTPS (port 443)
- Most production WordPress sites
- When using CDN or managed hosting

#### No-Follow Links

**What it does:**
- Adds `rel="nofollow"` attribute to share links
- Tells search engines not to follow these links for SEO purposes

**SEO implications:**
- **Enabled (default):** Share links don't pass "link juice" to social networks
- **Disabled:** Share links are treated as regular links by search engines

**Recommendation:**
- Keep enabled (default) for most sites
- Social share links are temporary navigation, not endorsements
- Prevents diluting your site's link authority

---

## Migration Notes (v2.x → v3.0+)

### Automatic Migration

When you upgrade from v2.x to v3.0+:

1. **Legacy options preserved:** Original `zm_shbt_fld` option is kept in database (not deleted)
2. **One-time migration:** Settings are copied to new `hss_core` option structure
3. **No data loss:** If migration fails, legacy options remain intact
4. **Rollback safe:** Downgrade to v2.x will use preserved legacy options

### Migration Mapping

| Legacy Option (v2.x) | New Option (v3.0+) | Type | Default |
|---------------------|-------------------|------|---------|
| `zm_shbt_fld['title']` | `hss_core['title']` | string | "Share this with your friends" |
| `zm_shbt_fld['excludes']` | `hss_core['exclude_pages']` | string | "" |
| `zm_shbt_fld['g_analytics']` | `hss_core['google_analytics']` | boolean | false |
| `zm_shbt_fld['auto_hide_btn']` | `hss_core['auto_hide_buttons']` | boolean | false |
| `zm_shbt_fld['use_port']` | `hss_core['use_port_in_url']` | boolean | false |
| `zm_shbt_fld['nofollow']` | `hss_core['nofollow_links']` | boolean | true |
| `zm_shbt_fld['iconset']` | `hss_core['icon_style']` | string | "default" |
| `zm_shbt_fld['show_left']` | `hss_core['floating_left']` | boolean | false |
| `zm_shbt_fld['show_right']` | `hss_core['floating_right']` | boolean | false |
| `zm_shbt_fld['show_before_post']` | `hss_core['before_content']` | boolean | false |
| `zm_shbt_fld['show_after_post']` | `hss_core['after_content']` | boolean | true |
| `zm_shbt_fld['icons']` | `hss_core['enabled_networks']` | array | ['facebook', 'twitter', 'linkedin'] |

### Verifying Migration

To confirm migration succeeded:

1. Check WordPress admin dashboard for success message after update
2. Navigate to Settings → Social Share Settings
3. Verify all your previous settings appear correctly
4. Test share buttons on a post/page

### Troubleshooting Migration Issues

**If settings appear blank after upgrade:**

1. Check for PHP errors in WordPress debug log
2. Ensure file permissions allow plugin to write to database
3. Deactivate and reactivate the plugin to retry migration
4. Contact support with error log if issues persist

**Manual rollback:**

If you need to rollback to v2.x:

1. Deactivate v3.0+ plugin
2. Delete v3.0+ plugin files
3. Install v2.x version from backup or repository
4. Activate v2.x plugin
5. Legacy `zm_shbt_fld` options will be used automatically

---

## Developer Reference

### Filter Hooks

Modify settings programmatically:

```php
// Modify title before display
add_filter('hss_share_title', function($title) {
    return 'Custom: ' . $title;
});

// Modify enabled networks
add_filter('hss_enabled_networks', function($networks) {
    // Force include Facebook
    if (!in_array('facebook', $networks)) {
        $networks[] = 'facebook';
    }
    return $networks;
});

// Conditionally exclude pages
add_filter('hss_exclude_pages', function($excluded) {
    if (is_singular('product')) {
        // Exclude all WooCommerce products
        return true;
    }
    return $excluded;
});
```

### Accessing Settings in Code

```php
// Get settings instance
$settings = \HtmlSocialShare\Plugin::getInstance()->getSettings();

// Read settings (dot notation supported)
$title = $settings->get('title');
$googleAnalytics = $settings->get('google_analytics');
$iconStyle = $settings->get('icon_style');
$excludePages = $settings->get('exclude_pages');

// Update settings
$settings->set('title', 'New Title');
$settings->set('google_analytics', true);
```

### REST API Endpoints

```
GET  /wp-json/html-social-share/v1/settings
POST /wp-json/html-social-share/v1/settings
POST /wp-json/html-social-share/v1/settings/reset
```

**Example request:**

```javascript
// Update settings via REST API
wp.apiFetch({
    path: '/html-social-share/v1/settings',
    method: 'POST',
    data: {
        advanced: {
            google_analytics: true,
            auto_hide_buttons: false,
            nofollow_links: true,
        },
        placement: {
            floating_left: true,
            before_content: false,
            after_content: true,
        }
    }
});
```

---

## Support

For additional help:

- **Documentation:** https://github.com/alimuzzaman/html-social-share-buttons
- **Issues:** https://github.com/alimuzzaman/html-social-share-buttons/issues
- **Support Forum:** WordPress.org plugin support forum

---

*Last updated: October 5, 2025*  
*Plugin version: 3.0.0+*
