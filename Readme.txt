=== Html Social share buttons ===
Contributors: alimuzzamanalim
Author: Md. Alimuzzaman Alim
Tags: lightweight, social share, block editor, fast, privacy-friendly
Requires at least: 5.3
Tested up to: 7.0
Requires PHP: 7.0
Version: 2.2.6
License: GPLv2
Stable tag: 2.2.6


Lightweight HTML and CSS share buttons with a no-JavaScript frontend. Settings and block editing use WordPress JavaScript.

== Description ==

Designed By Hakan Ertan <a target="_blank" href="https://www.tonicons.com/" rel="follow">www.tonicons.com</a>

To make Icons horizontal append  class='in_widget' in ShortCode.
Ex. [zm_sh_btn iconset='long_shadow' iconset_type='square' icons='facebook,x,linkedin,pinterest,mail' class='in_widget']

<strong>Features:</strong><br />
<ul>
	<li>Share URLs can be configured in a shortcode or generated PHP snippet.</li>
	<li>Exclude pages and posts by ID, slug, or searchable content.</li>
	<li>Optional Google Social Analytics tracking.</li>
	<li>Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email sharing.</li>
	<li>Default, flat, long-shadow, and Prajin icon sets with selectable button shapes.</li>
	<li>Native block editor, Elementor, WPBakery, and widget integrations.</li>
	<li>Horizontal or floating placements before, after, left, or right of post content.</li>
	<li>Translation-ready strings and a lightweight HTML/CSS frontend.</li>
</ul>

== Installation ==
1. At first activate the plugin.

2. There are two way to use this share button. You can use as widget or on the left site.

3. You get an option panel under the "Settings" menu called "Html Social Share".

4. By going to option panel:
	a) You will able to enable/disable widget.
	b) You will be able to display share button horizontally.

5. Then drag and drop this widget to your sidebar or header banner or footer.

6. That's all. Enjoy this widget.

== Changelog ==

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
