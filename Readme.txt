=== Html Social share buttons ===
Contributors: alimuzzamanalim
Author: Md. Alimuzzaman Alim
Tags: social share buttons, social sharing, gutenberg block, social media, share icons
Requires at least: 5.3
Tested up to: 7.0
Requires PHP: 7.0
Version: 2.2.6
License: GPLv2
Stable tag: 2.2.6


Fast, privacy-friendly social share buttons with Gutenberg, Elementor, widgets, profile links, SVG icons, and no tracking by default.

== Description ==

HTML Social Share Buttons adds fast social sharing controls to WordPress posts,
pages, sidebars, and page-builder layouts. The frontend uses lightweight HTML
and CSS, keeps tracking off by default, and does not depend on a remote icon CDN.

Use the native Gutenberg block, automatic content placement, a widget,
Elementor, WPBakery, a shortcode, or the generated PHP snippet. Share actions
and optional social profile/contact links are configured separately, so a link
to your profile is never treated as a share event.

<strong>Features:</strong><br />
<ul>
	<li>Share URLs can be configured in a shortcode or generated PHP snippet.</li>
	<li>Exclude pages and posts by ID, slug, or searchable content.</li>
	<li>Optional Google Social Analytics tracking.</li>
	<li>Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email sharing.</li>
	<li>Optional profile and contact links displayed beside sharing actions.</li>
	<li>Default, flat, long-shadow, Prajin, Bootstrap Solid, and Tabler Outline icon sets with selectable button shapes.</li>
	<li>Complete square and circle SVG coverage for every network in both new icon sets.</li>
	<li>Native block editor, Elementor, WPBakery, and widget integrations.</li>
	<li>Horizontal or floating placements before, after, left, or right of post content.</li>
	<li>Translation-ready strings and a lightweight HTML/CSS frontend.</li>
</ul>

<strong>Shortcode example:</strong>

`[zm_sh_btn iconset='long_shadow' iconset_type='square' icons='facebook,x,linkedin,pinterest,mail' class='in_widget']`

The `in_widget` class displays the buttons horizontally.

== Installation ==
1. Upload the plugin, or install it from the WordPress plugin directory.
2. Activate **HTML Social Share Buttons**.
3. Open **Settings > Html Social Share**.
4. Choose networks, icon style, button shape, and automatic placements.
5. Optionally add profile/contact links, customize share URL templates, or add
   the Gutenberg block, widget, Elementor element, WPBakery element, shortcode,
   or generated PHP snippet where needed.

== Frequently Asked Questions ==

= Does the plugin track visitors? =

No tracking is enabled by default. Optional Google Social Analytics support can
be enabled in Advanced settings.

= What is the difference between a share button and a profile link? =

A share button opens a network composer for the current page. A profile link
opens your configured social profile or email destination. Profile clicks are
excluded from the optional share analytics handler.

= Does it work with the block editor? =

Yes. Add the native HTML Social Share block for share actions, or the Social
Links block for profile/contact destinations. Both blocks are rendered
dynamically on the server. The share block uses the current post URL and both
blocks stay compatible with frontend filters and global profile-link settings.

= Can I use it without the block editor? =

Yes. Automatic placement, widgets, Elementor, WPBakery, shortcodes, and a PHP
code generator are also available. Both `[html-social-share-buttons]` and the
historical `[zm_sh_btn]` shortcode name are supported.

= Are icons loaded from a third-party CDN? =

No. Bundled icon assets are served from your WordPress site.

== Credits ==

Historical icon-pack provenance is under review; the repository does not yet
contain verified redistribution records for all retained PNG assets. Generated
Bootstrap Icons and Tabler Icons sources and their license notices are included
with the plugin. See `resources/iconsets/ASSET-SOURCES.md` in the source
repository for the current provenance record.

== Changelog ==

= Unreleased =
* **FEATURE**: Added optional global social profile and email links beside share buttons.
* **FEATURE**: Added complete Bootstrap Solid and Tabler Outline SVG icon sets for all supported networks.
* **FIX**: Saved widget network selections now render correctly while preserving existing widget data.
* **IMPROVEMENT**: Added reproducible icon generation, pinned source checksums, and bundled MIT license notices.
* **QUALITY**: Added scoped PHPStan and WordPress coding-standard checks for the rewritten namespace.

= 2.2.6 =
* **SECURITY**: Hardened rendering, icon-set, widget, shortcode, and integration paths against malformed input and missing runtime objects.
* **FIX**: Debounced and cancelled exclusion searches so stale requests do not overwrite newer results.
* **FIX**: Prevented exclusion searches from loading excessive post results.
* **IMPROVEMENT**: Social network icon previews now follow the selected button style.
* **IMPROVEMENT**: Replaced the shell-based frontend drift check with a PHP test helper for plugin compatibility.
* **UPDATE**: Restored PHP 7.0 compatibility and verified syntax across supported PHP versions.

= 2.2.5 =
* **FEATURE**: Added native block editor and Elementor share-button controls, including icon-set inheritance.
* **FEATURE**: Added Telegram and Bluesky sharing templates and matching icons for every bundled icon set.
* **IMPROVEMENT**: Redesigned the settings page, including searchable exclusions and per-platform template controls.
* **IMPROVEMENT**: Distribution archives now build required editor assets before `wp dist-archive` packages the plugin.

= 2.2.4 =
* **UPDATE**: Compatibility release tested through WordPress 7.0 and PHP 8.5
* **UPDATE**: Requires at least WordPress 5.3 for the native block editor integration
* **FIX**: Fixed a Google Social analytics console typo that caused a JavaScript error
* **IMPROVEMENT**: Added `noopener noreferrer` to share links opened in a new tab while preserving optional `nofollow`
* **IMPROVEMENT**: Scoped settings-page button styles so they do not affect other WordPress admin buttons

= 2.2.3 =
* **UPDATE**: Tested up to WordPress 7.0
* **FEATURE**: Added direct settings link on plugins admin page for quick access

= 2.2.2 =
* **BREAKING**: Removed Google Plus sharing (service discontinued April 2019)
* **BREAKING**: Removed Google Bookmarks (service discontinued September 2021)
* **UPDATE**: Renamed Twitter to X (Twitter) following platform rebrand
* **UPDATE**: Updated share URL from twitter.com to x.com
* **UPDATE**: Requires at least WordPress 5.0
* **UPDATE**: Tested up to WordPress 6.9
* **UPDATE**: Requires PHP 7.0 minimum
* **SECURITY**: Sanitized AJAX input in iconset handlers
* **IMPROVEMENT**: Replaced extract() calls for better PHP 8.x compatibility

= 2.2.1 =
* Tested up to WordPress 6.8
* Tags updated to emphasize lightweight, no-JS focus (limited to 5)
* Fixed PHP 8.2 deprecation: dynamic property creation in iconsets and core classes
* Minor code quality improvements

= 2.2.0 =
* **SECURITY FIX**: Fixed Stored Cross-Site Scripting (XSS) vulnerability (CVE-2025-9849) in zm_sh_btn shortcode
* **SECURITY**: Added proper input sanitization for all shortcode attributes using sanitize_text_field(), sanitize_key(), and sanitize_html_class()
* **SECURITY**: Added output escaping throughout the plugin using esc_attr(), esc_html(), esc_url(), and esc_textarea()
* **SECURITY**: Fixed unescaped HTML output in form functions and stylesheet generation
* **SECURITY**: Improved overall security posture with defense-in-depth approach
* **CREDIT**: Security vulnerability responsibly disclosed by Peter Thaleikis
* **IMPROVEMENT**: Enhanced code quality and WordPress coding standards compliance

= 2.1.16 =
* Previous version features and fixes
