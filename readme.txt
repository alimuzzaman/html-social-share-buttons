=== HTML Social Share Buttons ===
Contributors: alimuzzamanalim
Author: Md. Alimuzzaman Alim
Tags: social share, share buttons, gutenberg, elementor, social media
Requires at least: 5.3
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 3.5.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Lightweight HTML & CSS share buttons and profile links for blocks, Elementor & WPBakery. Local icons, copy link, native share, zero JS by default.

== Description ==

HTML Social Share Buttons adds lightweight, server-rendered HTML and CSS share buttons and social profile links to WordPress posts, pages, sidebars, and builder layouts. Because the default setup ships zero frontend JavaScript, zero third-party SDKs, and zero external requests, your pages stay fast for Core Web Vitals and privacy-friendly without tracking cookies or external beacons.

Use the **HTML Social Share** or **HTML Social Links** block, automatic placement, the classic widget, Elementor, WPBakery, a shortcode, or PHP. Choose from six bundled icon sets and square or circle buttons.

<strong>Why this plugin:</strong>

* <strong>HTML + CSS performance:</strong> Server-rendered share and profile links with zero frontend JavaScript by default for strong Core Web Vitals.
* <strong>Private by design:</strong> Local SVG and PNG icons load from your own site with no tracking scripts, external CDNs, or cookies.
* <strong>Place anywhere:</strong> Automatic before/after and floating placement, two dynamic Gutenberg blocks, a widget, Elementor, WPBakery, shortcodes, and PHP.
* <strong>Share and follow separately:</strong> Share actions open a composer for the current page; profile links open your configured profile or `mailto:` destination.
* <strong>Current destinations:</strong> Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, WhatsApp, Reddit, email, plus optional Copy link and Native share actions.
* <strong>Choose the presentation:</strong> Legacy keeps pack styling; Minimal, Framed, and Soft shadow add modern CSS-only styles.

<strong>Other features:</strong>

* Global social profile links with per-placement inherit or suppress controls.
* Independent audience controls for the post author, logged-in users, and visitors.
* Bootstrap Solid default for new sites, plus Flat, Long Shadow, Prajin, Tabler Outline, and retained Legacy Default.
* Responsive floating rails that collapse into centered wrapping rows at `600px` and below.
* Per-network URL templates with advisory preview diagnostics, plus exclusions by ID, slug, or title.
* Translation-ready admin UI and support via the [WordPress.org support forum](https://wordpress.org/support/plugin/html-social-share-buttons/).

<strong>Shortcode examples:</strong>

`[zm_sh_btn iconset="long-shadows" iconset_type="square" icons="facebook,x,linkedin,pinterest,mail"]`

`[html-social-share-buttons iconset="tabler-outline" iconset_type="circle" profile_links_mode="inherit"]`

Both `[html-social-share-buttons]` and `[zm_sh_btn]` render horizontally by default across all six icon sets.

== Installation ==

1. Install **HTML Social Share Buttons** from the WordPress plugin directory, or upload the release ZIP.
2. Activate the plugin and open **Settings > HTML Social Share**.
3. Choose networks, icon set, button appearance, button shape, automatic placements, and viewer audiences.
4. Optionally configure profile links, customize share URL templates, or set content exclusions.
5. Insert a block, widget, Elementor/WPBakery element, shortcode, or PHP snippet anywhere needed.

== Screenshots ==

1. Inline share buttons and a responsive floating rail on a post.
2. Appearance, placement, and audience controls in Settings.
3. Social Share and Social Links blocks in the block editor.
4. Separate profile-link destinations and viewer-audience controls.

== Frequently Asked Questions ==

= Does the plugin track visitors? =

No. Bundled icons are served from your site with no tracking scripts, external requests, or third-party SDKs. Legacy Google Social Analytics has been retired.

= What is the difference between a share button and a profile link? =

A share button opens a sharing composer for the current page. A profile link opens your configured social profile or email destination.

= What does Native share do? =

Native share is off by default. When enabled, a small local script opens the browser/device share sheet in supported secure contexts, offers Copy link in unsupported browsers, and falls back to a plain link without JavaScript. Cancelling the share menu does not copy anything. Enabling both Native share and Copy link renders only one Copy link fallback.

= Does a template preview save settings or contact a share service? =

No. Preview sample uses fixed sample data and outputs the resolved URL as text without saving settings or contacting external services.

= Does it work with the block editor and page builders? =

Yes. It provides dynamic **HTML Social Share** and **HTML Social Links** blocks (API v3 on WP 6.3+, v1 fallback on WP 5.3-6.2), Elementor widget, WPBakery element, classic widget, `[html-social-share-buttons]` / `[zm_sh_btn]` shortcodes, and PHP integration.

= Can I control who sees the buttons? =

Yes. Audience controls independently toggle buttons for the post author, other logged-in users, and logged-out visitors across every placement and integration.

= Which icon sets, shapes, and appearances are available? =

Bootstrap Solid is the default for new installs alongside Tabler Outline, Flat, Long Shadow, Prajin (square and circle), and retained Legacy Default (square). Legacy preserves original spacing, while Minimal, Framed, and Soft shadow provide CSS-only styling.

= Need help or want to report a bug? =

Visit the [WordPress.org support forum](https://wordpress.org/support/plugin/html-social-share-buttons/) with your WordPress version, PHP version, and active icon set or placement details.

= Is the WPBakery integration tested in the paid editor? =

The integration follows WPBakery's `vc_map()` and shortcode contract and is covered by mapping, persistence, bundle, and render tests without claiming a live run in the paid editor.

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

= 3.5.1 - 2026-11-25 =
* **FIX**: Render horizontal `inline-block` layouts consistently across all legacy icon sets inside Block, Elementor, Shortcode (`long-shadows`), and PHP function wrappers, and enforce `[hidden]` CSS specificity for paired Native + Copy buttons.
* **FIX**: Guard `the_content` automatic placement strictly to main loop singular/front content (skipping RSS feeds, embeds, and secondary queries) and avoid writing empty metabox post meta when unchecked or on post revisions.
* **FIX**: Resolve accurate document titles without stray post featured images for page-level floating bars, and omit `target="_blank"` from `mailto:` share buttons.
* **SECURITY**: Harden Settings API `$_POST` parsing, AJAX `is_string()` payload checks, and metabox POST type checks; remove dead external `tonicons.com` link from admin footer.
* **IMPROVEMENT**: Harden frontend browser-URL, native-share, and clipboard scripts (`MutationObserver` element scoping, `<button>` skip, timer cleanup, shared DOM status nodes), remove unused legacy images (~480 KB), and update widget/listing metadata.

= 3.5.0 - 2026-11-12 =
* **FEATURE**: Added optional Native share, opening the device sharing menu with a Copy link fallback. It is off by default.
* **IMPROVEMENT**: Native share loads local JavaScript and styles only on pages that render it, preserves custom and post-specific URLs, and handles cancellation without copying.
* **COMPATIBILITY**: Expanded WordPress runtime checks to PHP 8.4 and 8.5 while retaining PHP 7.0 and WordPress 5.3 minimums.
* **TEST**: Added native sharing, fallback, URL provenance, Pinterest image-query and runtime diagnostic coverage.

= 3.4.0 - 2026-10-09 =
* **FEATURE**: Added WhatsApp and Reddit share buttons. Both are off by default.
* **FEATURE**: Added an optional Copy link button. It copies the shared URL to the clipboard and announces the result to screen readers. Without JavaScript it stays a plain link to the page.
* **IMPROVEMENT**: Frontend scripts now load per feature. Copy link and browser-URL sharing each load only on pages that render them.
* **IMPROVEMENT**: Added WhatsApp, Reddit, and Copy link icons to all six bundled icon sets.
* **TEST**: Added Copy link smoke coverage and contracts for the new share templates and icons.

See `changelog.txt` for 3.3.0 and earlier release history.

== Upgrade Notice ==

= 3.5.1 =
Fixes horizontal block/Elementor layouts for legacy icon sets, skips RSS/embed automatic output, prevents empty metabox post meta rows, hardens admin input & frontend JS guards, and trims ~480 KB of unused legacy images.
