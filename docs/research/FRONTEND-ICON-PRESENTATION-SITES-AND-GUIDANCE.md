# Frontend icon presentation: sites, plugins, and design-system guidance

Status: source-backed research, 2026-08-24  
Scope: presentation of social/profile icons and share-action controls on the frontend. This is research for the appearance variation requested for HTML Social Share Buttons; it does not change product code.

## Executive recommendation

Keep the existing rendering as **Legacy** (correct spelling; not `lagacy`) and add a separate appearance layer for modern presentation. The appearance layer should style the clickable target, surface, spacing, and interaction states; it should not select, recolor, stretch, or replace the icon set.

Recommended v1 behavior:

- **Legacy** (`legacy`) remains the stored default and fallback. It preserves the current markup, dimensions, margins, hover behavior, placement, and asset requests.
- **Minimal** (`minimal`) is the modern default to offer: a consistent 44 × 44 CSS-pixel target, 8 px group gap, centered `contain` artwork, a restrained hover/active state, and no resting surface.
- **Framed** (`framed`) adds a shape-aware, visible outline for themes where isolated marks need a clearer boundary.
- **Soft shadow** (`soft-shadow`) adds a quiet neutral surface and static low-elevation shadow; it should not animate the shadow.

Keep **Icon set**, **Button appearance**, **Button shape**, and **Placement** as separate concepts. WordPress Core makes the same separation: its Social Icons block exposes style, size, labels, orientation, wrapping, colors, borders, and spacing as different controls ([WordPress Social Icons documentation](https://wordpress.org/documentation/article/social-icons/)).

The most important semantic boundary is:

- **Profile/follow icons** are links to a service or profile. They should be rendered as links and can offer an optional visible service label.
- **Share actions** trigger an action such as opening a share endpoint, copying a link, opening email, or presenting a share sheet. They should be exposed as an action with a name such as “Share on Facebook,” “Copy link,” or “Share.” Apple’s HIG treats the Share button as the conventional entry to a share sheet and distinguishes it from app/profile destinations ([Activity views](https://developer.apple.com/design/human-interface-guidelines/activity-views), [Buttons](https://developer.apple.com/design/human-interface-guidelines/buttons)).

Do not make the modern style depend on frontend JavaScript. The shared layer can be CSS-only, preserve local assets, and keep the plugin’s HTML/CSS performance story intact.

## Evidence map

| Source/demo | Direct evidence | Implication for this plugin |
|---|---|---|
| [W3C WAI-ARIA Button Pattern](https://www.w3.org/WAI/ARIA/apg/patterns/button/) | A button is for an action/event; it must have an accessible name. WAI-ARIA distinguishes ordinary, toggle, and menu buttons. | Keep action semantics distinct from profile links. Preserve native elements and functional labels; do not turn an anchor into a script-driven `div`. |
| [W3C WCAG 2.2: Target Size (Minimum)](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum) and [Target Size (Enhanced)](https://www.w3.org/WAI/WCAG22/Understanding/target-size-enhanced) | AA requires a pointer target of at least 24 × 24 CSS px or sufficient spacing; the enhanced criterion uses 44 × 44 CSS px. | A 44 px modern target is a defensible, generous web default. If a legacy pack remains smaller, ensure target spacing is sufficient and do not claim the legacy dimensions are equivalent to the modern target. |
| [W3C Non-text Contrast](https://www.w3.org/WAI/WCAG22/Understanding/non-text-contrast) | Visual information needed to identify a control or state needs at least 3:1 contrast; hue alone is not enough. | Brand-colored logos can remain, but selected/focus/pressed states need an additional shape, border, or contrast cue. A monochrome or neutral surface must not erase the logo’s recognizability. |
| [W3C Technique C39: `prefers-reduced-motion`](https://www.w3.org/WAI/WCAG22/Techniques/css/C39) | CSS can suppress non-essential interaction motion when the user requests reduced motion. | Modern hover lift/scale must be disabled under `prefers-reduced-motion: reduce`; keep the resting and focus states understandable without motion. |
| [GitHub Primer IconButton guidelines](https://primer.style/product/components/icon-button/guidelines/) and [Tooltip accessibility](https://primer.style/product/components/tooltip/accessibility/) | Icon-only controls are appropriate only when the purpose is understandable; Primer recommends a tooltip text label on hover/focus and requires an accessible label. Tooltips are not a substitute for a name and are not appropriate on non-interactive elements. | Keep `aria-label`/equivalent accessible names on every icon-only anchor. A tooltip may be a desktop enhancement, but do not depend on hover for touch or screen-reader users. Visible labels are preferable when the icon is ambiguous. |
| [Material Web Icon Buttons](https://github.com/material-components/material-web/blob/main/docs/components/icon-button.md) | Material offers standard (lowest emphasis), filled (high), filled-tonal (middle), and outlined (medium) icon-button treatments. The typical icon is 24 px; filled and outlined examples use a 40 px container and expose shape/color tokens. | “Minimal,” “Framed,” and “Soft shadow” are meaningful appearance axes. Keep surface/emphasis independent of icon artwork. A 24–32 px artwork box inside a 44 px target is consistent with this pattern. |
| [Android Material `IconButton` reference](https://developer.android.com/reference/kotlin/androidx/compose/material3/IconButton.composable) | Material’s standard icon button has a 48 × 48 dp minimum touch target and a typical 24 × 24 dp internal icon. | Mobile/touch validation should retain a 44–48 px target even when the visible logo is smaller. The plugin can use 44 px on the web and avoid shrinking the hit area to the artwork. |
| [Apple HIG Buttons](https://developer.apple.com/design/human-interface-guidelines/buttons) | Buttons need a hit region of at least 44 × 44 pt, enough surrounding space, a press state, and a familiar icon or short text. | Add a visible press/active state, keep neighbors separated, and prefer “Share” text when the share glyph is not enough context. Platform points are guidance, not a claim that CSS px equal iOS points. |
| [Apple HIG Activity views](https://developer.apple.com/design/human-interface-guidelines/activity-views) | People expect the Share button to reveal a system share sheet; Apple recommends a succinct descriptive title for custom activities. | A single labeled Share affordance can be a useful future compact mode when many networks would otherwise create a crowded row. It is not the same as a profile/follow icon group. |
| [IBM Carbon Button usage](https://carbondesignsystem.com/components/button/usage/) and [Carbon Tooltip accessibility](https://carbondesignsystem.com/components/tooltip/accessibility/) | Carbon separates primary/secondary/tertiary/ghost emphasis. An icon-only button needs an accurate icon; tooltip text should be specified and appears on keyboard focus. | Use neutral, low-emphasis presentation for secondary social actions. Do not put every network in a high-emphasis filled treatment. Keep focus usable without hover. |
| [USWDS Button](https://designsystem.digital.gov/components/button/) | The button is a large selectable surface; guidance favors short, verb-led labels, standard button markup, and a visible focus state. The live preview includes default, hover, active, focus, disabled, outline, inverse, and large variants. | Treat the USWDS preview as a practical state checklist. If text is shown, lead with an action (“Share”, “Copy link”), not a service noun alone. |
| [GOV.UK Button](https://design-system.service.gov.uk/components/button/) | GOV.UK recommends clear action text, limiting high-emphasis buttons, grouping related actions, and not relying on color alone for warning meaning. | Keep a share group visually secondary and avoid making every social network look like the page’s primary call to action. |
| [WordPress Social Icons block](https://wordpress.org/documentation/article/social-icons/) and [Core Social Links reference](https://developer.wordpress.org/block-editor/reference-guides/core-blocks/core-blocks-widgets/core-block-social-links/) | WordPress provides `Default`, `Logos only`, and `Pill shape` styles, while separately controlling icon size, labels, orientation, wrapping, color, border, padding, and spacing. It explicitly describes these as links to profiles/sites. | This is the closest ecosystem precedent. Treat a pill as a label/layout mode, not merely a border-radius skin. Keep profile links separate from the share-action renderer. |
| [AddToAny customization](https://www.addtoany.com/buttons/customize/) and [no-script code generator](https://www.addtoany.com/buttons/for/website) | AddToAny exposes floating vs inline, custom color/image, centered, square, menu style, and follow buttons. Its generator documents a “No script (basic links)” option. | Users expect shape, color, alignment, and placement choices, but CSS-only/basic-link fallback is a credible compatibility pattern. Avoid adding a script requirement to modern appearance. |
| [ShareThis custom share buttons](https://sharethis.com/support/customization/how-to-set-custom-buttons/) and [inline/sticky setup](https://sharethis.com/support/installation/share-buttons-html-website/) | ShareThis demonstrates custom shape/color/text CSS, inline vs sticky placement, network ordering, size (`small|medium|large`), labels, counts, and alignment; it provides a worked CodeSandbox example. | These are useful demos of the configuration space, but the plugin should ship a compact, predictable set rather than a large visual preset matrix. Treat labels/counts and placement as separate settings. |

## Demos and implementation examples worth reviewing

These are direct first-party demos or documentation examples, not screenshots inferred from a blog post:

1. **Material Web** — the [Icon Button documentation](https://github.com/material-components/material-web/blob/main/docs/components/icon-button.md) includes runnable markup for standard, filled, tonal, and outlined buttons and token examples for icon size, shape, container dimensions, and color.
2. **Primer** — the [IconButton component page](https://www.primer.style/product/components/icon-button/) includes React examples and an “Open in StackBlitz” link; the [guidelines page](https://primer.style/product/components/icon-button/guidelines/) demonstrates icon-only use with tooltip labeling.
3. **USWDS** — the [Button component preview](https://designsystem.digital.gov/components/button/) shows default, hover, active, focus, disabled, outline, inverse, and large states in one page.
4. **WordPress Core** — the [Social Icons block guide](https://wordpress.org/documentation/article/social-icons/) demonstrates logos-only, pill-shape, fill/outline styles, optional labels, orientation, wrapping, equal spacing, and brand/neutral color controls.
5. **ShareThis** — the [custom button guide](https://sharethis.com/support/customization/how-to-set-custom-buttons/) contains HTML/CSS and a direct [CodeSandbox worked example](https://codesandbox.io/s/amazing-sun-0rrb2?file=/index.html).
6. **AddToAny** — the [button generator](https://www.addtoany.com/buttons/for/website) demonstrates service selection, menu-only mode, and a no-script basic-link fallback; the [customization index](https://www.addtoany.com/buttons/customize/) links to square, custom color, floating, follow, and menu examples.

The demos use different platforms and units. They are evidence of interaction and hierarchy patterns, not pixel-perfect CSS to copy.

## Findings by presentation concern

### 1. Icon-only controls versus labels

- An icon-only control is appropriate only when its purpose is recognizable in context. Primer explicitly asks designers to verify this and recommends a tooltip with a text label for `IconButton` ([Primer guidelines](https://primer.style/product/components/icon-button/guidelines/)).
- Every icon-only control still needs a programmatic accessible name. WAI-ARIA allows text content, `aria-labelledby`, or `aria-label`; W3C’s image-button rule confirms that an image control without a name fails the requirement ([WAI-ARIA Button Pattern](https://www.w3.org/WAI/ARIA/apg/patterns/button/), [W3C image-button rule](https://www.w3.org/WAI/standards-guidelines/act/rules/image-button-accessible-name-59796f/)).
- A tooltip is a visible aid for pointer/keyboard users, not the only label: Primer notes that tooltips are easily missed and unavailable to many touch users ([Primer Tooltip accessibility](https://primer.style/product/components/tooltip/accessibility/)). Keep the existing `aria-label` on the link/button and use a tooltip only as an enhancement.
- If a custom tooltip is added, follow WCAG 2.2’s hover/focus-content rules: it must be dismissible, remain available while the pointer moves over the tooltip, and persist until the trigger is left, dismissed, or invalidated ([Content on Hover or Focus](https://www.w3.org/WAI/WCAG22/Understanding/content-on-hover-or-focus)). A native `title` tooltip is not a complete touch or screen-reader labeling strategy.
- Profile groups benefit from an optional “Show labels” mode because the destination may be unfamiliar (for example, Mastodon, Bluesky, or a custom community). Share rows can use “Share” plus an overflow/menu affordance when the network list becomes long; each direct action still needs a specific accessible name.

**Recommendation:** do not make “labels” part of the appearance skin. If added, it should be a separate content/layout option that changes row width, wrapping, and localization requirements.

### 2. Target size and spacing

- WCAG 2.2 AA sets a 24 × 24 CSS-pixel pointer target minimum, with a spacing exception for smaller targets; the enhanced criterion uses 44 × 44 CSS px ([Target Size (Minimum)](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum), [Target Size (Enhanced)](https://www.w3.org/WAI/WCAG22/Understanding/target-size-enhanced)).
- Apple uses a 44 × 44 pt hit region, and Material uses 48 × 48 dp for the standard icon button ([Apple HIG Buttons](https://developer.apple.com/design/human-interface-guidelines/buttons), [Material `IconButton`](https://developer.android.com/reference/kotlin/androidx/compose/material3/IconButton.composable)). These are platform conventions, not web conformance requirements.
- Modern HSSB can use a 44 × 44 CSS-pixel anchor with an 8 px gap. This is deliberately more usable than the AA minimum and avoids relying on invisible margins to create the hit area. Keep legacy pack dimensions unchanged.
- The artwork should normally be smaller than the target (for example, 24–32 px or a maximum 36 px content box) and centered with `background-size: contain`. This preserves mixed aspect ratios and prevents Prajin-like rectangular assets from being stretched into a square.

### 3. Shape, surface, and hierarchy

- Material’s four icon-button treatments form a useful emphasis ladder: standard/no surface (lowest), outlined/tonal (medium), and filled (high) ([Material Web Icon Buttons](https://github.com/material-components/material-web/blob/main/docs/components/icon-button.md)).
- Carbon similarly recommends different visual emphasis for primary, secondary, tertiary, and ghost actions and warns against multiple high-emphasis actions in a group ([Carbon Button usage](https://carbondesignsystem.com/components/button/usage/)).
- WordPress Core’s `Pill shape` is a separate block style and only becomes natural when labels are visible; with icon-only artwork it risks looking like an arbitrary capsule ([WordPress Social Icons](https://wordpress.org/documentation/article/social-icons/)).
- Keep `Button shape` (square/circle artwork geometry) independent from `Button appearance` (surface and interaction). A circle icon should not be clipped or forced into a square frame; use shape-aware radius only for the optional frame/focus ring.

**Recommendation:** ship no-surface Minimal, Framed, and Soft shadow. Defer filled/brand-color surfaces and pill until visible-label semantics are designed. A compact style dropdown is more predictable than separate controls for border, radius, shadow, gradient, glow, and hover effect.

### 4. Brand color versus neutral presentation

- WordPress Core defaults to each service’s brand colors but allows a global foreground/background override ([Social Icons styles](https://wordpress.org/documentation/article/social-icons/)).
- ShareThis explicitly supports custom color and shape so publishers can align buttons to their brand ([custom share buttons](https://sharethis.com/support/customization/how-to-set-custom-buttons/)).
- Brand color is useful for discoverability, but color alone cannot communicate focus/selection/state. W3C requires sufficient non-text contrast and an additional cue when color is the only difference ([Non-text Contrast](https://www.w3.org/WAI/WCAG22/Understanding/non-text-contrast), [Use of Color](https://www.w3.org/WAI/WCAG22/Understanding/use-of-color.html)).
- Do not apply CSS filters to rasterized brand assets to manufacture a “monochrome” mode. Filters can alter contrast and create inconsistent results across packs. Use the selected asset’s existing artwork and neutralize only the surrounding surface.

**Recommendation:** preserve each icon set’s artwork/color in Legacy and Minimal. Framed/Soft can use a neutral `Canvas`/theme surface and a contrast-checked border. If a future “monochrome” option is desired, it needs a dedicated asset contract and contrast review rather than a CSS filter.

### 5. Hover, focus, active, and disabled states

- Apple says a custom button needs a press state so it does not feel unresponsive ([Apple HIG Buttons](https://developer.apple.com/design/human-interface-guidelines/buttons)).
- Primer and Carbon make icon-button tooltips appear on hover and keyboard focus and require the trigger to remain focusable ([Primer Tooltip accessibility](https://primer.style/product/components/tooltip/accessibility/), [Carbon Tooltip accessibility](https://carbondesignsystem.com/components/tooltip/accessibility/)).
- W3C’s non-text contrast guidance requires control/state indicators to remain perceptible; W3C’s focus guidance recommends a visible indicator with sufficient contrast ([Non-text Contrast](https://www.w3.org/WAI/WCAG22/Understanding/non-text-contrast), [Focus Appearance](https://www.w3.org/WAI/WCAG22/Understanding/focus-appearance)).
- A restrained transform (for example, 1–2 px lift or a small scale) is safer than a 1.5× hover enlargement that can overlap adjacent icons. Hover effects should be gated to fine pointers so touch browsers do not retain a sticky hover.
- Activate share URLs on the normal click/release path, not pointer-down, to preserve WCAG pointer cancellation and allow users to abort a touch or mouse gesture ([Pointer Cancellation](https://www.w3.org/WAI/WCAG22/Understanding/pointer-cancellation)).
- Disabled share/profile links are usually better omitted or explained than rendered as low-contrast disabled controls. If a disabled state exists, it must not be the only way to reveal the control’s purpose; Primer does not show a tooltip on a disabled element because it cannot receive focus.

### 6. Mobile, narrow containers, and placement

- WordPress’s Social Icons block has explicit orientation, equal-spacing, icon-size, label, and “allow to wrap multiple lines” controls for small screens ([WordPress Social Icons settings](https://wordpress.org/documentation/article/social-icons/)).
- AddToAny distinguishes inline buttons from floating buttons and offers a menu-only mode ([AddToAny customization](https://www.addtoany.com/buttons/customize/)). ShareThis similarly distinguishes inline and sticky layouts and documents narrow-container failures ([ShareThis setup](https://sharethis.com/support/installation/share-buttons-html-website/)).
- Use flex layout with wrapping for inline groups. For floating rails, preserve the current placement behavior in Legacy and use a separate responsive rule in modern modes. At a narrow viewport, the row should become a centered/static wrapped group rather than forcing horizontal overflow.
- For a very large network list, a single Share action that opens a menu/sheet is a valid future pattern; it should not be confused with a profile-link row. Carbon’s guidance also favors a menu for more than a few related actions ([Carbon Button usage](https://carbondesignsystem.com/components/button/usage/)).

### 7. Reduced motion and high-contrast/forced colors

- Use `@media (prefers-reduced-motion: reduce)` to remove non-essential transform and transition. W3C documents this as a technique for animation triggered by interaction ([Technique C39](https://www.w3.org/WAI/WCAG22/Techniques/css/C39)).
- Do not rely on box-shadow for focus in Windows High Contrast/forced-colors modes: user agents may suppress shadows and apply system colors. The [`forced-colors` media feature](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/%40media/forced-colors) documents this behavior; use a real border/outline fallback and do not set `forced-color-adjust: none` on the whole control.
- Focus should remain visible when the surface is transparent, framed, or shadowed. Keep an outline or border that can be mapped to system colors.
- A sticky rail, cookie banner, or other author-created overlay must not entirely hide the focused icon; check focus visibility in floating placements as well as inline rows ([Focus Not Obscured](https://www.w3.org/WAI/WCAG22/Understanding/focus-not-obscured-minimum)).

## Open-license iconsets and legal boundaries

An open-source asset license does not grant permission to imply endorsement or misuse a company’s trademark. The legal status of each brand mark must be checked separately from the library’s code/SVG license.

| Iconset | License evidence | Best fit | Cautions |
|---|---|---|---|
| [Simple Icons](https://github.com/simple-icons/simple-icons) | The project is released under CC0, but the maintainers explicitly warn that individual icons may have different licenses and that users must check per-icon metadata and brand guidelines ([disclaimer](https://github.com/simple-icons/simple-icons/blob/develop/DISCLAIMER.md)). | Broad social/profile brand coverage with consistent SVG geometry. | Do not treat the whole collection as CC0. Pin a version, retain each icon’s license/source metadata, and follow the brand’s own guidelines/trademark rules. |
| [Bootstrap Icons](https://github.com/twbs/icons) | Official Bootstrap SVG library, over 2,000 icons, MIT licensed; it supports embedded SVG, `<img>`, sprites, and CSS ([README](https://github.com/twbs/icons), [MIT license](https://github.com/twbs/icons/blob/main/LICENSE)). The official catalog includes social/brand entries such as [Instagram](https://icons.getbootstrap.com/icons/instagram/) and [Meta](https://icons.getbootstrap.com/icons/meta/). | Generic share/action symbols plus a practical selection of social logos; easy to self-host locally. | MIT covers the library, not a brand’s trademark permission. Coverage and visual style may differ from a dedicated brand set. |
| [Tabler Icons](https://github.com/tabler/tabler-icons) | The repository describes a large MIT-licensed SVG set; icons use a consistent 24 × 24 grid and stroke style. | Generic Share, Copy, Link, Mail, and action icons when a brand logo is not needed. | It is primarily a UI/action set, not a complete social-brand catalog. Do not substitute a generic icon for a service logo when the destination needs to be explicit. |
| [Font Awesome Free](https://fontawesome.com/license/free) | Free SVG/JS icons use CC BY 4.0; web/desktop fonts use SIL OFL 1.1; non-font/non-icon code uses MIT. The package contains attribution comments and its brand icons are trademarks ([license](https://fontawesome.com/license/free), [Brands style](https://docs-v6.fontawesome.com/web/dig-deeper/styles)). | Mature brand coverage and familiar social glyphs when the project can preserve the mixed license notices. | License obligations differ by file format. Keep attribution comments and verify the current release; brand marks must only represent the corresponding brand/service. |

### License recommendation for HSSB

1. Keep the existing built-in assets for compatibility and do not silently swap artwork during an appearance change.
2. For new generic actions (Share, Copy link, Email, More), prefer a local MIT-licensed set such as Tabler or Bootstrap Icons, or continue using the project’s existing assets.
3. For social/profile logos, Simple Icons is a good candidate only with per-icon license/source metadata and brand-guideline review. Font Awesome Free is viable if the distribution keeps its required license notices.
4. Pin versions and record source URL, asset license, and any trademark/brand-guideline URL next to generated assets. Recheck before each asset refresh; these terms and brand requirements can change.

## Product option proposal

| Option | Resting presentation | Artwork | Interaction | Use case |
|---|---|---|---|---|
| `legacy` | Existing pack-owned surface, dimensions, and margins | Existing asset rules | Existing hover/focus behavior | Compatibility default; no new class, stylesheet, or request |
| `minimal` | Transparent; no frame or shadow | Centered, `contain`, max ~36 px content box inside a 44 px target | Small pointer-only lift/scale; clear focus; pressed state; no motion under reduced-motion | Recommended modern baseline for mixed iconsets |
| `framed` | 1 px shape-aware neutral border | Same centered artwork, slightly smaller inner box | Same state rules; focus must remain distinct from resting border | Themes where a clear boundary matters |
| `soft-shadow` | Neutral theme/Canvas surface, 1 px border, static low shadow | Same as Framed | Small pointer-only lift; do not animate shadow; forced-colors border fallback | Contemporary raised-surface treatment |

The options should be controlled by one global **Button appearance** dropdown in v1. A missing, empty, invalid, array, or boolean value must resolve to `legacy`. The appearance modifier should live on the existing group wrapper, allowing one shared stylesheet to cover all iconsets and both share/profile link types.

## Acceptance criteria for implementation

- Legacy output is byte-for-byte/selector-compatible with the current release wherever existing regression contracts require it: no added class, stylesheet, image request, JavaScript, or changed placement.
- Modern output adds one allowlisted appearance modifier and one core-owned stylesheet. Do not duplicate the same modern CSS in each iconset stylesheet.
- Native links/buttons remain keyboard reachable and retain functional accessible names. Decorative image content remains hidden from the accessibility tree where appropriate.
- Modern targets are at least 44 × 44 CSS px, use an 8 px group rhythm, and wrap/reflow in narrow containers without horizontal scrolling. Artwork uses `contain` and preserves aspect ratio.
- Shape remains independent: square/circle artwork and any third-party shape slug must not be inferred from appearance; the frame/focus radius can follow the known shape class.
- Hover transforms are limited to fine pointers; active/pressed feedback is visible; focus is visible without hover; disabled controls are not made discoverable only through a tooltip.
- `prefers-reduced-motion: reduce` removes non-essential transform/transition. `forced-colors: active` retains a visible border/outline fallback and does not disable system colors globally.
- Test desktop keyboard focus, touch/mobile, 320 px width, 400% zoom/reflow, dark/light backgrounds, and a Windows forced-colors pass before changing the modern default or claiming accessibility conformance.
- Keep profile-link and share-action labels distinct in copy and markup. A “Share” menu/overflow mode is a future layout feature, not a replacement for profile links.

## Limitations and confidence

- This is design-system and first-party product guidance, not a usability study of HSSB users. The 44 px target, 8 px gap, and four-option taxonomy are product recommendations derived from the evidence, not normative WCAG numbers.
- Apple points, Android dp, Material tokens, Carbon sizes, and web CSS pixels are not interchangeable. They are used here as converging interaction guidance, not as a claim of cross-platform equivalence.
- AddToAny and ShareThis documentation demonstrates their own products and may reflect commercial tradeoffs (scripts, analytics, or tracking). It is useful for the category’s configuration vocabulary, not an accessibility or privacy endorsement.
- No production-site DOM scrape or browser screenshot was used for a popularity ranking. The examples are inspectable official docs, component previews, and first-party demos; dynamic sites and consent-gated pages can change.
- “Open license” applies to the library files, not automatically to a company’s logo/trademark. Verify the exact icon metadata, source brand rules, and release version before bundling or redistributing any new brand asset.
- Existing icon assets still need a separate contrast/provenance audit. A new CSS skin cannot retroactively make every legacy raster logo meet WCAG contrast requirements.
