# WordPress.org brand asset and listing brief

Research snapshot: 2026-08-24

This brief defines the message and visual system for a new WordPress.org banner and icon for HTML Social Share Buttons. It also records the listing changes that should follow the asset work. The recommended direction is based on the live WordPress.org listing, the current plugin source, official directory documentation, and first-party peer listings.

## Decision

Position the plugin around its original and still-current implementation advantage:

> HTML and CSS sharing with no frontend JavaScript by default, plus current WordPress placement options.

The deeper tagline research in [`WORDPRESS-ORG-TAGLINE-RESEARCH.md`](WORDPRESS-ORG-TAGLINE-RESEARCH.md) supersedes the earlier banner wording in this brief. The banner should lead with the product name and this support line:

> **HTML Social Share Buttons**  
> **HTML + CSS sharing. No frontend JS by default.**

Do not compete on network count. Leading peers already claim 100 or more services. HSSB has a stronger and more credible story in its HTML/CSS public output with no frontend JavaScript by default. Local icon delivery, analytics disabled by default, separate share and profile-link behavior, and support for blocks, widgets, builders, shortcodes, PHP, and automatic placement support that primary position in the listing.

## What is current today

- WordPress.org is serving version 3.0.0, the current description, FAQ, two block descriptions, tags, and changelog from the updated `readme.txt`. The [live plugin page](https://wordpress.org/plugins/html-social-share-buttons/) and [official plugin information API](https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request%5Bslug%5D=html-social-share-buttons) are the public sources.
- The local [`readme.txt`](../readme.txt) and the [live SVN trunk readme](https://plugins.svn.wordpress.org/html-social-share-buttons/trunk/readme.txt) were byte-identical during this research pass. The local main plugin file and [live SVN trunk plugin file](https://plugins.svn.wordpress.org/html-social-share-buttons/trunk/html-social-share.php) were also byte-identical.
- The short description already appears on the [social-share-buttons tag listing](https://wordpress.org/plugins/tags/social-share-buttons/). It currently ends with the absolute claim "no tracking."
- The current [772 by 250 banner](https://ps.w.org/html-social-share-buttons/assets/banner-772x250.png?rev=3439566) and [1544 by 500 banner](https://ps.w.org/html-social-share-buttons/assets/banner-1544x500.png?rev=3439566) use Google+, the former Twitter bird, textured paper, old display type, and the sentence "You are in right place." They no longer represent the product or its current network set.
- The [live SVN asset directory](https://plugins.svn.wordpress.org/html-social-share-buttons/assets/) contains only the two banner files. WordPress.org therefore shows an [auto-generated geometric icon](https://s.w.org/plugins/geopattern-icon/html-social-share-buttons_d1c0c2.svg), not a product mark.
- The listing has no screenshots section. The plugin information API has no screenshots field for this plugin, the SVN asset directory has no screenshot files, and `readme.txt` has no `== Screenshots ==` section.

## Product truth that the design may claim

The current source supports these messages:

- Frontend output is server-rendered HTML and CSS, and icons are served locally. See [`readme.txt`](../readme.txt#L16-L19).
- Tracking is off by default, but optional Google Social Analytics exists. See [`readme.txt`](../readme.txt#L41) and the false analytics default in [`SettingsDefaults.php`](../src/Domain/Settings/SettingsDefaults.php#L39-L45).
- Share actions and profile links are separate. See [`readme.txt`](../readme.txt#L21-L23) and the separate [Social Share](../block.json) and [Social Links](../blocks/social-links/block.json) block definitions.
- Automatic before and after placement, floating left and right placement, blocks, a classic widget, Elementor, WPBakery, shortcodes, and PHP are supported. See [`readme.txt`](../readme.txt#L25-L26) and [`readme.txt`](../readme.txt#L33-L40).
- The maintained network set is Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email. See [`BuiltInNetworkProvider.php`](../src/Infrastructure/Definition/BuiltInNetworkProvider.php#L28-L86).
- Six local icon sets exist, with Bootstrap Solid as the new-install default and historical compatibility retained. See [`readme.txt`](../readme.txt#L35-L37).

Avoid these claims:

- "No tracking." Optional analytics makes this too broad. Use "tracking off by default."
- "No JavaScript." The frontend has optional analytics JavaScript, and the block and settings experiences use JavaScript.
- "GDPR compliant," "the fastest," "the best," or similar legal or unprovable claims.
- A network-count promise as the headline. Seven maintained share destinations cannot credibly own the breadth position against peers claiming 100 or more.

## Peer pattern research

The peer set shows two dominant positions.

| Position | First-party evidence | What to learn |
|---|---|---|
| Breadth and universal sharing | [AddToAny](https://wordpress.org/plugins/add-to-any/) leads with universal sharing, many services, and follow buttons. Its banner is a sparse, text-free row of colored button forms. | Simple brand assets scale well, but HSSB should not imitate its plus mark or compete on network count. |
| Breadth and customization | [Sassy Social Share](https://wordpress.org/plugins/sassy-social-share/) leads with around 100 services and customization. Its banner is a dense feature list. | The category already has copy-heavy banners. HSSB can stand out with one clear promise and more space. |
| Easy placement and customization | [Hubbub Lite](https://wordpress.org/plugins/social-pug/) leads with simple share and follow buttons and a customizable, no-code setup. | A product mark plus one sentence is enough. Avoid Hubbub's broad "audience growth" framing because HSSB does not need to promise growth. |
| Privacy and script reduction | [Shariff Wrapper](https://wordpress.org/plugins/shariff/) centers privacy, and [Scriptless Social Sharing](https://wordpress.org/plugins/scriptless-social-sharing/) centers simple links, no JavaScript, and accessibility. | Privacy and script reduction are proven category needs. HSSB should state its own qualified mechanism: HTML/CSS sharing with no frontend JavaScript by default. |

The [WordPress.org social-share-buttons category](https://wordpress.org/plugins/tags/social-share-buttons/) repeats the same split. Plugins either promise more networks and customization or promise lightweight, private output. HSSB can credibly connect those positions by pairing privacy with placement flexibility.

## Visual direction

### Concept: code brackets and connected nodes

Create an original symbol made from two angle brackets around a three-node share path. The brackets refer to HTML. The connected nodes refer to sharing and links. The mark should work without letters, third-party logos, or a platform-specific visual reference.

Use the same mark in the icon and banner. Around it, use restrained rounded button tiles and connection paths as decoration. Those tiles must be generic shapes with no Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, email, or WordPress glyphs.

The visual style should be clean geometric vector art. It should feel native beside current WordPress admin UI without copying the WordPress logo or treating WordPress blue as the only brand cue. Avoid 3D chrome, glass effects, stock photography, paper texture, gradients with banding, and detailed AI illustration.

### Palette

| Role | Color | Use |
|---|---|---|
| Ink | `#0B1220` | Dark background and primary contrast field |
| Deep blue | `#172554` | Background gradient endpoint |
| Product blue | `#2563EB` | Main mark and button accents |
| Cyan | `#38BDF8` | Connection paths and small highlights |
| Teal | `#2DD4BF` | Secondary decorative nodes only |
| Paper | `#F8FAFC` | Headline, icon glyph, and light surfaces |
| Slate | `#CBD5E1` | Supporting copy on the dark field |

Keep all text on the ink or deep-blue part of the background. Do not place white copy on cyan or teal. Check the final raster at both sizes for at least WCAG AA contrast even though the banner itself is not interactive.

### Typography

Use a neutral humanist sans serif with a clear open-source license. Inter is the preferred direction. Use ExtraBold or Bold for the product name and Medium for the support line. Use no more than those two weights and do not add a second family.

At 772 by 250, target a 38 to 42 pixel product name and an 18 to 21 pixel support line. Double those values in the 1544 by 500 master. The designer must typeset the final words as vector or normal text before raster export. Do not ask an image model to render the final text.

## Banner specification

### Exact copy

Product name:

> HTML Social Share Buttons

Support line:

> HTML + CSS sharing. No frontend JS by default.

Do not add a CTA, version number, list of networks, feature badges, ratings, install count, or URL.

### Layout at 772 by 250

- Keep critical content inside `x = 72..700` and `y = 28..222`.
- Center the code-and-share mark at approximately `x = 386`, `y = 61`, with a 64 to 72 pixel bounding box.
- Center the product name beneath the mark, with its visual baseline around `y = 150`.
- Center the support line beneath the name, with its visual baseline around `y = 191`.
- Place generic rounded tiles and connecting paths only in the outer left and right decoration zones. Let them crop at the edges so the center remains calm.
- Keep the composition horizontally balanced. This allows one English asset to sit naturally in left-to-right and right-to-left directory layouts. If localized text is added later, export explicit `-rtl` variants rather than mirroring text.

The 1544 by 500 version should be the two-times master. Every coordinate, radius, type size, and stroke weight should scale exactly by two. Export the 772 by 250 file from the master with a high-quality downsample. Do not compose the two banners independently.

### Image-generation handoff

If image generation is used, generate only the abstract background and decorative vector-like shapes. Prompt for no text, no letters, no words, no logos, no platform symbols, and no watermark. Rebuild the central mark and all type as exact vector layers afterward. This avoids misspelled product copy and keeps the 1x and 2x outputs identical.

## Icon specification

- Use a rounded square with an 18 percent corner radius and the ink-to-deep-blue background.
- Center the original angle-brackets-and-share-nodes mark in paper white, with product blue and cyan used only for one node or path accent.
- Keep at least 12 percent clear space on all sides. At 128 by 128, keep primary strokes at 8 pixels or thicker and small nodes at 12 pixels or larger.
- Use no letters. "HSSB" is not legible enough at small size and is not needed when the symbol already joins HTML and sharing.
- Use no platform marks, WordPress mark, browser chrome, text, shadows, or fine texture.
- Verify at 32 by 32 as a stress test even though WordPress.org requires 128 and 256 pixel files.

The source SVG, if supplied, must contain only static paths and gradients, with no scripts, external references, embedded raster data, or font dependency. The PNG fallbacks must match it.

## Trademark, copyright, and aging constraints

WordPress.org requires directory-hosted images to use rights-compatible material, and plugin developers are responsible for validating those rights before upload. It also requires plugins to respect trademarks, copyrights, and project names. See the [Detailed Plugin Guidelines](https://developer.wordpress.org/plugins/wordpress-org/detailed-plugin-guidelines/).

Use a newly drawn generic share/code mark. Do not reuse the historical bundled icon packs as the new plugin identity. Their provenance record includes compatibility exceptions and incomplete archival evidence. See [`ASSET-SOURCES.md`](../resources/iconsets/ASSET-SOURCES.md). An original mark also avoids another Google+ or Twitter-style aging problem when a service closes or rebrands.

Platform names may remain in factual listing copy. Their logos and proprietary brand treatments are unnecessary in the banner and icon.

## Required deliverables

Prepare these exact files for the top-level `/assets` directory of the WordPress.org SVN repository:

```text
banner-772x250.png
banner-1544x500.png
icon-128x128.png
icon-256x256.png
icon.svg                 optional
```

The [official plugin asset documentation](https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/) requires exact dimensions. The normal banner is required for the retina banner to work. Banner files must stay below 4 MB. Icon files must stay below 1 MB. An SVG icon needs a PNG fallback. The WordPress.org CDN caches these files heavily, so a visible update can take minutes and sometimes several hours.

Keep editable source and review exports in Git under a project-owned design folder, but do not confuse the plugin runtime [`assets`](../assets) directory with WordPress.org SVN `/assets`. Only the latter controls the directory banner, icon, and screenshots.

### Export checks

- Verify file dimensions with `file`, `sips`, or another deterministic image inspector.
- Verify PNG color mode is RGB or RGBA in sRGB.
- Inspect both PNG sizes at 100 percent. The 772 and 128 pixel files are the real legibility tests.
- Compare the PNGs with the SVG source for geometry and color parity.
- Optimize without changing dimensions or introducing indexed-color banding.
- Set the SVN MIME type to `image/png` if needed, as described in the official asset documentation.
- Preview the icon on both light and dark surrounding UI.
- Preview the banner with a right-to-left page shell. The image itself should remain balanced without mirroring.

## Listing copy and page follow-up

The v3.0.0 description has been updated and is already live. It needs a focused maintenance pass, not a rewrite from scratch.

### 1. Correct the short description

Current:

> Fast, privacy-friendly WordPress share buttons and profile links with blocks, widgets, builder integrations, local icons, and no tracking.

Recommended, 117 characters:

> HTML + CSS share buttons and profile links with blocks, builders, local icons, and no frontend JavaScript by default.

This keeps the strongest product terms, restores the original HTML/CSS position, and matches the optional-analytics behavior.

### 2. Align the installed-plugin description

The main plugin header still says:

> Lightweight HTML and CSS social share buttons. Settings and block editing use WordPress JavaScript.

Recommended:

> HTML + CSS share buttons and profile links with blocks, builders, local icons, and no frontend JavaScript by default.

Also change the Plugin URI from `http://wordpress.org/...` to HTTPS when this file next receives an authorized metadata update.

### 3. Tighten the opening description

Recommended opening:

> HTML Social Share Buttons adds server-rendered HTML and CSS share buttons and profile links to posts, pages, sidebars, and builder layouts. By default, the plugin adds no frontend JavaScript. Bundled icons load from your own site, and optional Google Social Analytics stays off unless you enable it.
>
> Use the Social Share or Social Links block, automatic placement, the classic widget, Elementor, WPBakery, a shortcode, or PHP. Choose from six bundled icon sets and square or circle buttons.

Follow that with five scannable benefit groups: HTML + CSS by default, private by default, place anywhere, share and follow separately, and current network support. Keep the detailed shortcode examples and FAQ after the user-facing summary.

### 4. Add screenshots

The [official asset documentation](https://developer.wordpress.org/plugins/wordpress-org/plugin-assets/) says screenshot captions come from numbered lines in a `== Screenshots ==` section, and the image files must live in SVN `/assets`.

Recommended set:

1. Frontend inline row and responsive floating rail.
2. Settings page with placement and icon style controls.
3. Social Share and Social Links blocks in the editor.
4. Profile-link and audience controls.
5. Elementor control and public output, only if the screenshot is current and verified.

Use current UI, a neutral demo post, and no personal data. Captions should state what the screenshot proves, not call it beautiful or easy.

### 5. Reduce `readme.txt` below the warning threshold

The file was 10,440 bytes during this research pass. The [official readme documentation](https://developer.wordpress.org/plugins/wordpress-org/how-your-readme-txt-works/) warns that readmes larger than 10 KB may error and recommends keeping the current release in the readme while moving older history to a separate `changelog.txt`.

Keep the 3.0.0 entry in `readme.txt` and move older entries to `changelog.txt`. The stable-tag rule matters: WordPress.org reads the stable tag from trunk and then uses the readme in that tag. Update the authoritative tagged readme as part of the normal SVN metadata workflow rather than assuming a trunk-only edit will change the public page.

## Recommended sequence

1. Have the XHigh design pass refine the symbol proportions and banner composition without changing the positioning or factual copy.
2. Generate the abstract visual layer, then typeset and draw the final mark as exact vector content.
3. Export and inspect the four required PNGs. Add `icon.svg` only if it remains a clean static vector and the PNG fallbacks match.
4. Review the assets for rights, trademark, contrast, small-size legibility, RTL balance, exact dimensions, and file-size limits.
5. In a separate content change, correct the short description and plugin header, trim the readme, and add screenshot captions and assets.
6. Upload only after explicit publication authority. Verify the live CDN assets and public listing after cache propagation.

## Acceptance checklist

- [ ] Banner copy is exactly "HTML Social Share Buttons" and "HTML + CSS sharing. No frontend JS by default."
- [ ] Icon is recognizable at 32 pixels and contains no text or third-party mark.
- [ ] 1x and 2x banners have identical composition and exact dimensions.
- [ ] All text stays within the safe zone and remains readable at 772 by 250.
- [ ] The banner remains visually balanced in an RTL page shell.
- [ ] No generated text, watermark, visual artifact, or platform logo remains.
- [ ] PNG and optional SVG outputs match.
- [ ] Banner files are below 4 MB and icon files are below 1 MB.
- [ ] Rights and source records exist for every visual element.
- [ ] Listing claims match the current plugin behavior.
- [ ] Publication and SVN upload remain a separate, explicitly authorized step.
