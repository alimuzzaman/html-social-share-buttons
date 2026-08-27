=== HTML Social Share Buttons ===
Contributors: alimuzzamanalim
Author: Md. Alimuzzaman Alim
Tags: social share buttons, social sharing, gutenberg block, social media, share icons
Requires at least: 5.3
Tested up to: 7.1
Requires PHP: 7.0
Stable tag: 3.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

HTML + CSS share buttons and profile links with blocks, builders, local icons, and no frontend JavaScript by default.

== Description ==

HTML Social Share Buttons adds server-rendered HTML and CSS share buttons and
profile links to posts, pages, sidebars, and builder layouts. By default, the
plugin adds no frontend JavaScript. Bundled icons load from your own site, and
optional Google Social Analytics stays off unless you enable it.

Use the Social Share or Social Links block, automatic placement, the classic
widget, Elementor, WPBakery, a shortcode, or PHP. Choose from six bundled icon
sets and square or circle buttons.

<strong>Why this plugin:</strong>

* <strong>HTML + CSS by default:</strong> Server-rendered share and profile links with no frontend plugin JavaScript unless optional analytics is enabled.
* <strong>Private by default:</strong> Bundled icons load from your site; optional analytics stays disabled until you enable it.
* <strong>Place anywhere:</strong> Automatic before/after and floating placement, two blocks, a widget, Elementor, WPBakery, shortcodes, and PHP.
* <strong>Share and follow separately:</strong> Share actions open a composer for the current page; profile links open the configured profile or email destination.
* <strong>Current destinations:</strong> Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email.
* <strong>Choose the presentation:</strong> Legacy preserves the existing pack behavior; Minimal, Framed, and Soft shadow add modern frontend styles.

<strong>Other features:</strong>

* Optional global profile/contact links with per-placement inherit or suppress controls.
* Audience controls for the content author, other logged-in users, and logged-out visitors.
* Bootstrap Solid as the default for new installations, plus Flat, Long Shadow, Prajin, and Tabler Outline.
* Existing content configured with the historical Default pack keeps rendering it without migration.
* Square buttons in every set; circle buttons in Flat, Long Shadow, Prajin, Bootstrap Solid, and Tabler Outline.
* Responsive floating rails that become centered, wrapping rows at 600px and below.
* Per-network URL templates and exclusions by post/page ID, slug, or searchable content.
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
4. Choose networks, icon set, button appearance, button shape, automatic placements, and viewer audiences.
5. Optionally configure profile/contact links, share URL templates, exclusions, or analytics.
6. Add a block, widget, builder element, shortcode, or generated PHP snippet where needed.

== Screenshots ==

1. Inline share buttons and a responsive floating rail on a post.
2. Appearance, placement, audience, and analytics controls in Settings.
3. Social Share and Social Links blocks in the block editor.
4. Separate profile-link destinations and viewer-audience controls.

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

= Can I control who sees the buttons? =

Yes. Audience settings independently control the content author viewing their
own post, other logged-in users, and logged-out visitors. All three are enabled
by default for existing and new installations, and the settings apply to
automatic placement, blocks, widgets, builders, shortcodes, and PHP output.

= Which button shapes are available? =

The Default set supports square buttons. Flat, Long Shadow, Prajin, Bootstrap
Solid, and Tabler Outline support square and circle buttons.

= Which button appearances are available? =

Legacy keeps the selected icon set's existing dimensions, spacing, and hover
behavior. Minimal uses consistent targets and spacing, Framed adds a subtle
shape-aware outline, and Soft shadow adds a quiet raised surface. Legacy is the
default so existing sites keep their current presentation.

= Which icon set does a new installation use? =

Bootstrap Solid. The historical Default pack is hidden from normal selectors
on a fresh installation. Existing settings and content that already select
Default keep that selection and continue using the retained local assets.

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

= 3.1.0 =
* **FEATURE**: Added a global Button appearance selector with Legacy, Minimal, Framed, and Soft shadow options.
* **FEATURE**: Added modern CSS-only presentation styles with consistent targets, spacing, shape-aware framing, and restrained motion.
* **IMPROVEMENT**: Kept Legacy as the default and fallback so existing sites retain their selected icon-set presentation.
* **IMPROVEMENT**: Added appearance previews and applied the selected presentation consistently across automatic placement, blocks, widgets, shortcodes, PHP, and builder integrations.
* **FIX**: Modern floating rails now reveal on hover and keyboard focus while remaining inside the viewport on mobile.
* **FIX**: Modern profile separators no longer inherit historical pack margins.

See `changelog.txt` for the 3.0.0 and earlier release history.
