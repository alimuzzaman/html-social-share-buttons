=== HTML Social Share Buttons ===
Contributors: alimuzzamanalim
Author: Md. Alimuzzaman Alim
Tags: social share buttons, social sharing, gutenberg block, social media, share icons
Requires at least: 5.3
Tested up to: 7.0
Requires PHP: 7.0
Stable tag: 2.2.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Fast, privacy-friendly share buttons and profile links for WordPress, with blocks, widgets, builder integrations, local icons, and no tracking by default.

== Description ==

HTML Social Share Buttons adds lightweight sharing controls to posts, pages,
sidebars, and page-builder layouts. Frontend output is server-rendered HTML and
CSS. Icons are served locally; no remote icon CDN or visitor tracking is used by
default.

Share actions and profile/contact links are separate. A share action opens a
network composer for the current page. A profile link opens a configured social
profile or email destination and is excluded from optional share analytics.

Use automatic content placement, either dynamic block, the classic widget,
Elementor, WPBakery, a shortcode, or the generated PHP snippet.

<strong>Current features:</strong>

* Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email sharing.
* Optional global profile/contact links with per-placement inherit or suppress controls.
* Dynamic Social Share and Social Links blocks for the block editor.
* Classic widget, Elementor, WPBakery, shortcode, and PHP integrations.
* Default, Flat, Long Shadow, Prajin, Bootstrap Solid, and Tabler Outline icon sets.
* Square buttons in every set; circle buttons in Flat, Long Shadow, Prajin, Bootstrap Solid, and Tabler Outline.
* Automatic before/after placement and floating left/right placement.
* Responsive floating rails that become centered, wrapping rows at 600px and below.
* Per-network URL templates and exclusions by post/page ID, slug, or searchable content.
* Optional Google Social Analytics support, disabled by default.
* Translation-ready admin and editor strings.

<strong>Shortcode examples:</strong>

`[zm_sh_btn iconset="long-shadows" iconset_type="square" icons="facebook,x,linkedin,pinterest,mail" class="in_widget"]`

`[html-social-share-buttons iconset="tabler-outline" iconset_type="circle" profile_links_mode="inherit"]`

The historical `[zm_sh_btn]` tag remains supported. Use `class="in_widget"`
for a horizontal row.

== Installation ==

1. Install the plugin from the WordPress plugin directory, or upload the release ZIP.
2. Activate **HTML Social Share Buttons**.
3. Open **Settings > HTML Social Share**.
4. Choose networks, icon style, button shape, and automatic placements.
5. Optionally configure profile/contact links, share URL templates, exclusions, or analytics.
6. Add a block, widget, builder element, shortcode, or generated PHP snippet where needed.

== Frequently Asked Questions ==

= Does the plugin track visitors? =

No tracking is enabled by default. Optional Google Social Analytics support can
be enabled in Advanced settings.

= What is the difference between a share button and a profile link? =

A share button opens a network composer for the current page. A profile link
opens your configured social profile or email destination. Profile clicks are
not sent through the optional share-analytics handler.

= Does it work with the block editor? =

Yes. Use the dynamic **HTML Social Share** block for share actions and the
dynamic **HTML Social Links** block for profile/contact destinations. The share
block resolves the current post URL on the server.

= Can I use it without the block editor? =

Yes. Automatic placement, widgets, Elementor, WPBakery, shortcodes, and a PHP
code generator are supported. Both `[html-social-share-buttons]` and the
historical `[zm_sh_btn]` shortcode tags are available.

= Which button shapes are available? =

The Default set supports square buttons. Flat, Long Shadow, Prajin, Bootstrap
Solid, and Tabler Outline support square and circle buttons.

= Are icons loaded from a third-party CDN? =

No. Bundled icon assets are served from your WordPress site.

= Is the WPBakery integration tested in the paid editor? =

The integration follows WPBakery's documented `vc_map()`/shortcode model and is
covered by mapping, persistence, compiled-bundle, and public-render contracts.
The current release-candidate evidence does not claim a live run in the paid
WPBakery editor.

== Credits ==

Original historical-design credit: Hakan Ertan, tonicons.com. Prajin remains
credited for the historical Prajin pack. Maintainer attestation (2026-08-12):
the Flat, Long Shadow, and Prajin PNG packs are used with authorization from
their respective rights holders. The repository does not archive the written
authorizations or license instruments, so this is a maintainer statement, not
independent legal verification.

The Default PNG pack is retained under the release owner's accepted
compatibility exception. That decision is not an independent source, license,
redistribution, or clearance claim. Generated Bootstrap Icons and Tabler Icons
sources and their license notices are included with the plugin. See
`resources/iconsets/ASSET-SOURCES.md` in the source repository for the current
provenance record.

== Changelog ==

= Unreleased =
* **REWRITE**: Moved runtime ownership to a canonical namespaced service graph while retaining the documented 2.2.6 public and storage compatibility surfaces.
* **FEATURE**: Added optional global social profile and email links with per-placement inherit or suppress controls.
* **FEATURE**: Added a dynamic Social Links block and complete Bootstrap Solid and Tabler Outline SVG sets.
* **FIX**: Floating left/right rails now become centered, wrapping rows at viewport widths of 600px and below.
* **FIX**: Saved widget network selections render correctly while preserving existing widget data.
* **IMPROVEMENT**: Added reproducible icon generation, production-autoloader checks, deterministic archives, and broader integration/browser contracts.
* **COMPATIBILITY**: Retained Block API v1 for WordPress 5.3. The two corresponding Plugin Check findings are documented and accepted; no clean Plugin Check result is claimed.

= 2.2.6 =
* **SECURITY**: Hardened rendering, icon-set, widget, shortcode, and integration paths against malformed input and missing runtime objects.
* **FIX**: Debounced and cancelled exclusion searches so stale requests do not overwrite newer results.
* **FIX**: Prevented exclusion searches from loading excessive post results.
* **IMPROVEMENT**: Social network icon previews now follow the selected button style.
* **IMPROVEMENT**: Replaced the shell-based frontend drift check with a PHP compatibility helper.
* **UPDATE**: Restored PHP 7.0 compatibility and verified syntax across supported PHP versions.

= 2.2.5 =
* **FEATURE**: Added native block editor and Elementor share-button controls, including icon-set inheritance.
* **FEATURE**: Added Telegram and Bluesky sharing templates and matching icons for every bundled icon set.
* **IMPROVEMENT**: Redesigned the settings page, including searchable exclusions and per-platform template controls.
* **IMPROVEMENT**: Distribution archives build required editor assets before packaging.

= 2.2.4 =
* **UPDATE**: Declared testing through WordPress 7.0 and PHP 8.5.
* **UPDATE**: Raised the minimum WordPress version to 5.3 for the native block editor integration.
* **FIX**: Fixed a Google Social Analytics console typo.
* **IMPROVEMENT**: Added `noopener noreferrer` to new-tab share links while preserving optional `nofollow`.
* **IMPROVEMENT**: Scoped settings-page button styles to the plugin admin screen.

= 2.2.3 =
* **UPDATE**: Declared testing through WordPress 7.0.
* **FEATURE**: Added a direct settings link on the plugins screen.

= 2.2.2 =
* **BREAKING**: Removed Google Plus and Google Bookmarks after their services were discontinued.
* **UPDATE**: Renamed Twitter to X (Twitter) and updated its share URL.
* **UPDATE**: Raised the minimum PHP version to 7.0.
* **SECURITY**: Sanitized AJAX input in icon-set handlers.
* **IMPROVEMENT**: Removed `extract()` usage for PHP 8.x compatibility.

= 2.2.1 =
* Declared testing through WordPress 6.8.
* Fixed PHP 8.2 dynamic-property deprecations in icon-set and core classes.
* Updated WordPress.org tags and code quality.

= 2.2.0 =
* **SECURITY FIX**: Fixed stored XSS (CVE-2025-9849) in the `zm_sh_btn` shortcode path.
* **SECURITY**: Added input sanitization and output escaping across affected rendering and form paths.
* **CREDIT**: Vulnerability responsibly disclosed by Peter Thaleikis.

= 2.1.16 =
* Previous version features and fixes.
