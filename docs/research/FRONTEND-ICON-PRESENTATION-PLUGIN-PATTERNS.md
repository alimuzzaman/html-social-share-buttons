# Frontend icon presentation patterns in WordPress sharing plugins

**Research date:** 2026-08-24
**Scope:** frontend presentation only (target size, shape, spacing, surfaces, color, hover/focus, labels, counters, placement, responsive behavior, and customization). The comparison covers six established WordPress sharing plugins requested for this task: AddToAny, Jetpack Sharing, Sassy Social Share, Hubbub Lite (formerly Social Pug), Shariff Wrapper, and Scriptless Social Sharing.

## Method and evidence boundary

The evidence below comes from the plugins' WordPress.org pages, official documentation, official WordPress.org SVN packages, or the projects' public source repositories. “Observed source” means a value or selector was read from the linked CSS/PHP/SCSS; “documented” means the plugin's own page or documentation says the feature exists. Marketing copy is not treated as proof of a rendered CSS value. Packages and `trunk`/default branches were checked on 2026-08-24, so a site can differ because of an older release, a theme override, or saved plugin settings.

The plugins do not all implement the same product: AddToAny, Jetpack, Sassy, Hubbub, and Shariff include optional JavaScript-driven counts, popups, or floating behavior, while Scriptless deliberately renders ordinary links without frontend JavaScript. That implementation difference matters when selecting a style for HTML-only HSSB.

## At-a-glance comparison

| Plugin | Placement and responsive behavior | Observed default/tokenized presentation | Labels and counters | Customization model |
| --- | --- | --- | --- | --- |
| [AddToAny](https://wordpress.org/plugins/add-to-any/) | Standard inline list plus horizontal/vertical fixed or content-attached floating bars. The admin source exposes responsive hide/show breakpoints (vertical default max-width 980px; horizontal default min-width 981px). | Common 32px preset; inline links have `padding: 0 4px`, artwork spans are `32×32`, `border-radius: 4px`; no shadow or frame. Hover lowers image/span opacity to `.7`. | Universal button can be icon, text, or icon+text; service counts are a separate `a2a_count` element. | Custom SVG/image URL, size, background, and floating position are exposed by settings; many values can be overridden with CSS. |
| [Jetpack Sharing](https://jetpack.com/support/sharing/) | Inline list; touch devices receive extra left padding. The same module offers icon-only, text-only, icon+text, and third-party “official” modes. | 13px Open Sans text, 18px glyph, 4px radius, white surface, and a 1px ring (second `box-shadow`) plus `0 1px 2px` shadow. Items are `8px` right and `12px` bottom apart. Hover/active darken the ring/shadow. | Optional heading/label; icon/text modes are separate. Share counts are an optional service setting. | Admin drag/drop service ordering and four display modes; official buttons are delegated to third-party services. |
| [Sassy Social Share](https://wordpress.org/plugins/sassy-social-share/) | Standard top/bottom inline output, fixed vertical bar, and mobile bottom bar/popup. Responsive popup columns change at 783/752/590/413px. | Defaults in plugin options: horizontal `35px` round; vertical `40px` square. Horizontal items have `2px` margins. Shape can be square, round (`border-radius: 999px`), or rectangle with independent width/height. | Small share-count badge is a separate `10px` pill with white border and shadow; total shares can be a count plus “Share(s)” text. | Per-location shape, size, width/height, fill/hover colors, icon colors, border width/color, and radius. |
| [Hubbub Lite / Social Pug](https://wordpress.org/plugins/social-pug/) | Inline above/below content, floating sidebar, and (in the product family) sticky/mobile tools. Labels can be hidden on mobile; button columns/wrapping are configurable. | Base click box is `min-width: 40px`, `height: 40px`, 2px border, no shadow, and `0.15s` transition. The CSS ships eight numbered `button_style` modifiers spanning filled, two-tone/icon-background, transparent, and icon-hover treatments. | Label, count, total-count, spacing, column, and mobile-label settings are independent. Sidebar counts sit in a 20px-high bottom band. | `button_style` (1–8), shape, size, columns, labels, spacing, service colors, and per-tool settings are all exposed; the output PHP adds a shared `dpsp-button-style-N` wrapper class. |
| [Shariff Wrapper](https://wordpress.org/plugins/shariff/) | Flex row-wrap inline list or vertical orientation. At viewport widths ≤360px, text and counters are hidden and icons remain. | Medium is `35px` high with `5px` margins. Named themes include default/color/grey/white/round/WCAG; round is a 35px circle. Base links have no border or shadow. | Text is 12px; count is an absolutely positioned translucent panel. Round theme makes the count transparent until hover, so it is not persistently discoverable. | `theme`, orientation, `buttonsize` (small 25 / medium 35 / large 45), alignment/stretch, and optional radius are parameters. |
| [Scriptless Social Sharing](https://github.com/robincornett/scriptless-social-sharing) | Before/after/manual locations, shortcode/block, flex or legacy table layout. Default icon+text labels become screen-reader-only at ≤767px. | Inline SVG icon is `18px` (`1em`), table layout uses `3px` border-spacing, flex buttons use a 1px border, no radius, no shadow, and `12px` default padding. Brand backgrounds are `.8` alpha at rest and solid on hover. | Four documented button modes (icon only, icon+text responsive, icon+text always, text only); accessible names remain in markup when visual labels are hidden. | No-JS HTML links; SVG or Font Awesome icons, CSS flex/table switch, color filters, and a numeric padding setting (`12` default) are available. |

## Plugin-by-plugin observations

### AddToAny: lightweight icon-first baseline

The [official frontend stylesheet](https://plugins.svn.wordpress.org/add-to-any/trunk/addtoany.min.css) is intentionally sparse. Its normal links are inline-blocks with 4px horizontal padding and no border or shadow. The named 32px kit sets the icon span to `32×32`; non-special spans have a 4px radius. A combined hover/focus selector removes background, border, and shadow; a separate hover rule lowers icon/span opacity to `.7`. This creates a quiet, icon-first treatment rather than a raised button. The opacity rule is not a substitute for a focus indicator, so a theme may need to add one.

The [official admin source](https://plugins.svn.wordpress.org/add-to-any/trunk/addtoany.admin.php) exposes a universal button choice (SVG icon, custom image, text-only, or none), a 32px default icon size, horizontal/vertical floating placement, transparent/custom backgrounds, and responsive width thresholds. The [feature/FAQ documentation](https://www.addtoany.com/buttons/faq/) documents vector icons, share counts, and responsive floating bars; those claims are consistent with the source's option names but do not describe every rendered value.

**Useful pattern:** a small, predictable icon token with a tiny gap and no decorative surface.

**Caution:** floating bars and opacity-only hover/focus are separate concerns; do not make either a required appearance preset.

### Jetpack Sharing: compact raised controls with explicit modes

The [official Sharing documentation](https://jetpack.com/support/sharing/) exposes `Icons + Text`, `Icons Only`, `Text Only`, and third-party “official sharing button” styles, plus a heading/label and drag-and-drop service order. In the [public `sharing.css`](https://github.com/Automattic/jetpack/blob/trunk/projects/plugins/jetpack/modules/sharedaddy/sharing.css), the common button rule is 13px Open Sans, 4px radius, white background, a 1px ring (the second `box-shadow` layer) plus a `0 1px 2px` shadow, and `4px 11px 3px 9px` padding. The social-logo glyph is 18px; icon+text adds a 6px label gap. List items are inline-block with 8px right and 12px bottom margins.

Hover/active darken both the outline and shadow; active adds an inset top shadow. Touch mode adds 10px left padding to each list item. These are concrete examples of a “soft framed” button, but the shared selector also applies to official third-party markup, so the visual result can vary by service.

**Useful pattern:** separate icon/text modes while keeping one shared geometry and hover/active treatment.

**Caution:** third-party official buttons and count behavior are not compatible with an HTML-only/no-JavaScript guarantee; keep them out of HSSB's core style contract.

### Sassy Social Share: broad shape and size controls

The [WordPress.org feature page](https://wordpress.org/plugins/sassy-social-share/) documents square, round, and rectangular shapes, vector icons, size/color controls, counters, standard placement, and floating/mobile bars. The current option defaults in the [official package PHP](https://plugins.svn.wordpress.org/sassy-social-share/trunk/sassy-social-share.php) are horizontal round `35px` and vertical square `40px`; rectangular output uses independent width/height.

The [frontend CSS](https://plugins.svn.wordpress.org/sassy-social-share/trunk/public/css/sassy-social-share-public.css) places horizontal links with 2px margins. The count badge is a distinct 10px pill (`border-radius: 15px`) with a 2px white border and `0 2px 2px rgba(0,0,0,.4)` shadow. The vertical fixed tray has 10px padding, a 4px radius, and a subtle `0 1px 4px 1px rgba(0,0,0,.1)` shadow. The [frontend renderer](https://plugins.svn.wordpress.org/sassy-social-share/trunk/public/class-sassy-social-share-public.php) writes size, radius, background, icon, and border styles into each output element.

The stylesheet has `div.heateor_sss_sharing_ul a:focus { text-decoration: none; background: transparent !important; }` near its reset rules, but does not add a visible replacement focus ring. This is a compatibility warning, not a recommendation to copy the rule.

**Useful pattern:** counts are a separate badge, and the fixed tray is a separate surface from the icons.

**Caution:** independent width/height, a broad size range, per-location colors, and border controls create a large combination space; they are poor defaults for a small style dropdown.

### Hubbub Lite (Social Pug): style presets as a product feature

The [official plugin page](https://wordpress.org/plugins/social-pug/) documents inline and floating tools, SVG icons, counts, and separately configurable labels. Its changelog records the addition of show/hide button labels on desktop/mobile and shortcode output. The [official frontend output source](https://plugins.svn.wordpress.org/social-pug/trunk/inc/functions-frontend.php) accepts `button_style` 1–8, shape, size, columns, labels, spacing, and count flags, then emits a shared `dpsp-button-style-N` wrapper class.

The [official compiled stylesheet](https://plugins.svn.wordpress.org/social-pug/trunk/assets/dist/style-frontend-pro.css) gives every network a 40px-high, 2px-bordered base button with no shadow and a 0.15s transition. The base hover/focus rule adds a 3px outer ring (`box-shadow: 0 0 0 3px var(--networkHover)`) while also setting `outline: none`. Labels disappear at ≤480px when the mobile-label class is present. Counts are inline in normal buttons and become a 20px-high bottom band in the sidebar.

The eight numbered modifiers cover filled brand surfaces, icon backgrounds, transparent/outline treatments, and hover/icon-hover variants. They are intentionally visual presets, but the source does not give each number a durable semantic name; the exact appearance can also vary by shape, location, and network color. This is strong evidence that a style dropdown is useful, and equally strong evidence that eight-plus variants plus per-location overrides are expensive to explain and test.

**Useful pattern:** one wrapper modifier can style every icon set, and a real 40px click box plus a 3px focus ring survives compact icon-only layouts.

**Caution:** avoid copying the eight-style/per-tool matrix into HSSB v1; use a few named, stable presets instead.

### Shariff Wrapper: named themes and explicit mobile reduction

The [WordPress.org page](https://wordpress.org/plugins/shariff/) documents privacy-preserving share links, themes, orientation, button size, and counts. The public [Shariff Wrapper stylesheet](https://github.com/3UU/wordpress-shariff-wrapper/blob/master/trunk/css/shariff.css) uses a flex row-wrap list with 5px margins and 35px medium buttons. Base links have no border or shadow. The icon SVG is 32×20 inside the 35px button; text/count are 12px. Themes include default/color/grey (service-color surfaces), white (white surface with a 1px #ddd border), round (35px circle), and WCAG color variants. Small and large presets are 25px and 45px respectively.

At widths ≤360px the plugin hides `.shariff-text` and `.shariff-count`, preserving the icon-only button. Counts are otherwise absolutely positioned in a translucent right-side panel. In the round theme the count is transparent and only becomes visible on hover, which is a discoverability problem for touch users. The stylesheet has a hover rule but no plugin-specific `:focus` rule; a consuming theme must supply a visible keyboard indicator.

**Useful pattern:** named theme/size/orientation tokens and an explicit mobile label reduction.

**Caution:** do not make counters hover-only or let a “small” preset undercut HSSB's minimum touch target.

### Scriptless Social Sharing: closest to the HTML-only story

The [official README](https://github.com/robincornett/scriptless-social-sharing) describes ordinary share links, no frontend JavaScript, accessible labels for icon-only buttons, SVG icons, shortcode/block/manual locations, and a flex/table CSS choice. Its [button-style settings](https://github.com/robincornett/scriptless-social-sharing/blob/master/includes/settings/fields.php) list four modes: icon only, icon + text with responsive hiding, icon + text always, and text only. The [default settings](https://github.com/robincornett/scriptless-social-sharing/blob/master/includes/settings/defaults.php) select SVG icons, the responsive icon+text mode, flex layout, full-width buttons, and 12px button padding.

The [SCSS source](https://github.com/robincornett/scriptless-social-sharing/blob/master/sass/scriptlesssocialsharing-style.scss) is a useful concrete baseline: 18px current-color SVG icons, 8px icon-to-label gap, 3px table `border-spacing`, 1px border on flex buttons, zero radius, zero shadow, and service colors at `.8` alpha until hover. The [inline-style builder](https://github.com/robincornett/scriptless-social-sharing/blob/master/includes/class-scriptlesssocialsharing-enqueue.php) hides visual labels at ≤767px while retaining screen-reader text; it also permits a numeric padding value. The source has no explicit focus-visible ring or motion-reduction rule.

**Useful pattern:** keep the semantic link/accessible name in HTML and use CSS only to switch between icon+label and icon-only presentation.

**Caution:** a 0–400px padding control and no built-in focus style show why HSSB should clamp geometry and own its focus contract.

## Patterns worth adopting for HSSB

The evidence supports a small named style system independent of icon set, service color, and placement. A proposed mapping for HSSB is:

| HSSB style | Geometry/surface contract | Evidence it builds on |
| --- | --- | --- |
| **Legacy** | Preserve the current markup, spacing, colors, and hover behavior byte-for-byte as much as possible. This is the migration-safe default. | AddToAny's sparse icon-first CSS and Scriptless's no-JS link model show that an icon does not need a new surface. |
| **Minimal** | Transparent button; no border or shadow; a 40–44px clickable box; artwork centered at roughly 18–24px (or the icon-set's intrinsic ratio); 4–8px gap; labels optional but semantically present. | AddToAny, Scriptless, and Shariff's icon-only mobile reduction. |
| **Framed** | Same box and gap tokens, plus a 1px neutral/brand frame and a stable radius token (for example 4px). Use `:focus-visible` ring outside the frame; never remove the ring with `outline: none` unless the replacement is guaranteed. | Jetpack's 1px outline/ring, Shariff white theme, and Hubbub's separate 3px focus ring. |
| **Soft-shadow** | White/neutral surface, 1px low-contrast ring, and a restrained 0–2px shadow; no layout shift on hover. Focus ring remains separate and higher contrast. | Jetpack's layered outline/shadow and Sassy's separate tray/badge surfaces. |

Cross-cutting rules:

1. Keep `style` (Legacy/Minimal/Framed/Soft-shadow) separate from icon set, service brand color, labels, counters, and placement. One shared wrapper modifier should style all icon sets.
2. Clamp the visual/click target to a small supported range (40–44px is a practical baseline); do not expose Sassy/Scriptless-style broad size or padding controls in the first dropdown.
3. Keep labels as a separate `icon`, `icon + text`, and `text` mode. If labels are visually hidden at a breakpoint, retain the accessible name in the link and make the breakpoint explicit.
4. Treat counters as an optional, separate badge/slot. Reserve space or use a stable layout so a fetched count cannot move neighboring buttons; never make the only count visible on hover.
5. Use `:hover` for a modest color/surface change and `:focus-visible` for a clearly visible keyboard indicator. Do not copy the focus resets observed in Sassy or the `outline: none` shortcut in Hubbub without a replacement.
6. Use wrapping rather than shrinking below the target size. For floating/fixed layouts, keep placement as a separate setting and define a mobile/safe-area rule rather than treating “floating” as a visual style.
7. Preserve icon aspect ratio (`contain`/intrinsic SVG dimensions); do not force every icon into a square crop. Check brand and neutral variants for contrast, including forced-colors mode.
8. Avoid transitions that are required to understand state. If motion is added later, gate it behind `prefers-reduced-motion: reduce`.

## Anti-patterns to avoid

- **Eight-plus unnamed styles:** Hubbub demonstrates the visual range, but numeric presets plus location/shape/size switches become a testing and support matrix. Four named HSSB styles are enough for the first release.
- **Broad geometry controls:** Sassy's independent width/height and Scriptless's 0–400px padding setting can produce tiny targets, oversized bars, or inconsistent service rows. Clamp values and use one shared box token.
- **Style changes that also change placement:** inline, fixed, sticky, popup, and mobile footer are layout features. Keep them out of the icon style dropdown.
- **Counters as decoration:** Shariff's overlay and hover-only round count, or a badge that changes the button width after load, can hide information and shift the row. Use an optional stable badge.
- **Brand fill as the only mode:** white-on-brand works for many services but can fail contrast for light brands and can look noisy across a row. Provide neutral/monochrome or framed choices.
- **Hover-only affordances:** touch users have no hover. Ensure the icon/link remains identifiable and actionable without hover or motion.
- **Focus removal:** Sassy resets focus background and Hubbub sets `outline: none`; neither is safe as a standalone accessibility pattern. HSSB should own a visible `:focus-visible` ring.
- **Third-party official buttons in the core contract:** Jetpack's official mode and JS-backed counters can alter dimensions and violate the product's “HTML/no frontend JS” positioning. Keep those integrations optional and outside the style guarantee.

## Source register

The links below are the primary sources used for the observations above.

| Plugin | Documentation / product page | Source inspected |
| --- | --- | --- |
| AddToAny | [WordPress.org](https://wordpress.org/plugins/add-to-any/), [FAQ](https://www.addtoany.com/buttons/faq/) | [frontend CSS](https://plugins.svn.wordpress.org/add-to-any/trunk/addtoany.min.css), [admin options](https://plugins.svn.wordpress.org/add-to-any/trunk/addtoany.admin.php) |
| Jetpack Sharing | [Sharing support](https://jetpack.com/support/sharing/) | [sharing.css](https://github.com/Automattic/jetpack/blob/trunk/projects/plugins/jetpack/modules/sharedaddy/sharing.css), [sharing-service.php](https://github.com/Automattic/jetpack/blob/trunk/projects/plugins/jetpack/modules/sharedaddy/sharing-service.php) |
| Sassy Social Share | [WordPress.org](https://wordpress.org/plugins/sassy-social-share/) | [defaults](https://plugins.svn.wordpress.org/sassy-social-share/trunk/sassy-social-share.php), [frontend CSS](https://plugins.svn.wordpress.org/sassy-social-share/trunk/public/css/sassy-social-share-public.css), [renderer](https://plugins.svn.wordpress.org/sassy-social-share/trunk/public/class-sassy-social-share-public.php) |
| Hubbub Lite / Social Pug | [WordPress.org](https://wordpress.org/plugins/social-pug/) | [frontend CSS](https://plugins.svn.wordpress.org/social-pug/trunk/assets/dist/style-frontend-pro.css), [frontend output](https://plugins.svn.wordpress.org/social-pug/trunk/inc/functions-frontend.php) |
| Shariff Wrapper | [WordPress.org](https://wordpress.org/plugins/shariff/) | [Shariff Wrapper CSS](https://github.com/3UU/wordpress-shariff-wrapper/blob/master/trunk/css/shariff.css), [source repository](https://github.com/3UU/wordpress-shariff-wrapper) |
| Scriptless Social Sharing | [README/source repository](https://github.com/robincornett/scriptless-social-sharing) | [SCSS](https://github.com/robincornett/scriptless-social-sharing/blob/master/sass/scriptlesssocialsharing-style.scss), [button-style fields](https://github.com/robincornett/scriptless-social-sharing/blob/master/includes/settings/fields.php), [defaults](https://github.com/robincornett/scriptless-social-sharing/blob/master/includes/settings/defaults.php), [style builder](https://github.com/robincornett/scriptless-social-sharing/blob/master/includes/class-scriptlesssocialsharing-enqueue.php) |

## Limitations

- This is source/documentation research, not a browser rendering comparison. No screenshots, theme overrides, or live-device tap tests were used.
- Plugin settings, active version, theme CSS, and service-specific markup can change the final pixels. The values above describe the linked package/source at research time, not every possible configuration.
- “Eight styles” in Hubbub is a source-level numbered API; its visual names are inferred from the CSS rules and preview classes because the public source does not provide a stable semantic name for every number.
- The absence of a focus or reduced-motion rule is reported only where the inspected stylesheet had no matching rule; it is not proof that a theme or another asset cannot add one.
- This report does not select an icon artwork library or license. Open-license iconset candidates should be evaluated separately for license text, trademark terms, SVG viewBox consistency, and whether the project permits local packaging.
