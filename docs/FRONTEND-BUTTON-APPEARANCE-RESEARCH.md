# Frontend button appearance research

Status: research and implementation brief, 2026-08-24  
Repository baseline: `d4b78429d5cecc8fdc3edbbc8668805c4b625a24`  
Scope: presentation variants only; no product code was changed as part of this research.

## Recommendation

Add one global dropdown labeled **Button appearance**, stored as `button_appearance`, with these values:

1. **Legacy (current)** — `legacy`; the default and the fallback for missing or invalid data.
2. **Minimal (recommended)** — `minimal`; a 44 px target, centered artwork, balanced spacing, and a restrained hover lift.
3. **Framed** — `framed`; the Minimal geometry plus a shape-aware one-pixel frame.
4. **Soft shadow** — `soft-shadow`; a quiet neutral surface, shape-aware frame, and static low-elevation shadow.

Do not call this dropdown merely “Style.” The settings screen already uses “Icon Style” as a section title and “Button style” for the icon-set selector, while integrations use “Button shape” for `square`/`circle`. A fourth meaning of “style” would be hard to explain and easy to encode incorrectly. Rename the existing icon-set control to **Icon set**, put the new control beside it, and use **Appearance** as the section heading.

Version one should be global. It should affect automatic placement, both dynamic blocks, widgets, Elementor, WPBakery, shortcodes, the canonical PHP renderer, and adapted third-party icon sets through the same render policy. Do not add placement- or instance-level overrides yet. An absent stored value must resolve to `legacy`, so upgrading without changing the dropdown keeps the current HTML, CSS requests, dimensions, spacing, placement, and interaction behavior.

The modern appearances must live in one core-owned stylesheet and one wrapper modifier, not in six copies of icon-set CSS. This is the central architectural requirement.

## Research method and confidence

This review covered the current domain model, durable option codec, request sanitizer, admin React UI, canonical renderer, asset collector, all six built-in icon-set manifests and stylesheets, integration adapters, legacy extension adapter, fixtures, regression contracts, and relevant Git history. The repository evidence is pinned to the baseline commit where practical.

External evidence came from WordPress developer documentation and source, W3C/WAI guidance, browser documentation, and current first-party plugin listings. Competitor features were treated as category evidence, not as a quality or accessibility oracle.

No browser visual tests or screenshots were produced during this research. Visual values below are specifications to validate during implementation, not claims of observed cross-browser output.

## What the product concepts mean

| Concept | User question | Existing examples | Recommended owner | Scope |
|---|---|---|---|---|
| Icon set | “Which artwork should I use?” | Default, Flat, Long Shadows, Prajin, Bootstrap Solid, Tabler Outline | `IconSet` and asset resolver | Global today, with integration choices already supported |
| Shape | “Which artwork geometry should I use?” | Square, Circle | Icon-set manifest plus existing `iconset_type`/placement shape | Per automatic placement and per integration |
| Placement | “Where should the group appear?” | Left, right, before content, after content, block, widget, shortcode, builder, PHP | `Placement` and `RenderPlacement` | Per rendering surface |
| Button appearance | “How should the clickable targets be sized, framed, spaced, and react?” | Legacy, Minimal, Framed, Soft shadow | New `ButtonAppearance` domain value plus shared CSS | Global in version one |

Shape must not be folded into appearance. A circle asset rendered inside a square “style” would make the existing shape selection misleading. Placement must also remain independent: selecting Soft shadow must not move an inline group into a floating rail.

WordPress Core makes the same broad separation. Its Social Icons block has only three mutually exclusive block styles (`default`, `logos-only`, `pill-shape`) while size, spacing, orientation, color, and wrapping remain separate controls ([Core Social Icons reference](https://github.com/WordPress/gutenberg/blob/trunk/packages/block-library/src/social-links/README.md), [Core stylesheet](https://github.com/WordPress/gutenberg/blob/trunk/packages/block-library/src/social-links/style.scss)). Core's small set is a useful signal to avoid novelty presets.

## Repository findings

### Durable settings and compatibility

The current `Settings` object contains the global icon set and default shape, while `placementShapes` carries automatic-placement shape choices ([Settings.php](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/Domain/Settings/Settings.php), [SettingsDefaults.php](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/Domain/Settings/SettingsDefaults.php)). The durable option remains the historic `zm_shbt_fld` array. `OptionSettingsCodec` deliberately preserves unknown keys by mutating the original option array rather than replacing it wholesale ([OptionSettingsCodec.php](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/Infrastructure/WordPress/Settings/OptionSettingsCodec.php)).

That makes an additive scalar safe:

- Decode missing `button_appearance` as `legacy`.
- Sanitize against an explicit allowlist.
- Encode a valid value as `button_appearance`.
- Do not run a database migration just to add the key.
- A rollback to 3.0.0 ignores the unknown key and, under the current replacement/codec behavior, preserves it as opaque option data; old code renders its normal legacy presentation. Re-upgrading can recover the selection.

The “missing means legacy” rule must exist in every boundary, not only the React default. Invalid arrays, objects, booleans, unrecognized strings, or empty values must also become `legacy`.

### Rendering is already centralized enough to support this

`RenderFacade` is the canonical path for automatic placement, shortcodes, blocks, widgets, Elementor, WPBakery, and PHP adapters. It maps options to a `RenderRequest`, builds the buttons, renders the historical wrapper, and returns collected stylesheet/icon metadata ([RenderFacade.php](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/Presentation/Rendering/RenderFacade.php)). `HtmlRenderer` intentionally retains old-looking class names, quote style, and newlines as public compatibility ([HtmlRenderer.php](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/Presentation/Frontend/HtmlRenderer.php)). The rewrite history likewise calls out public markup compatibility ([canonical rewrite commit](https://github.com/alimuzzaman/html-social-share-buttons/commit/50152652f0456e55c8983a136f960931d1694eff)).

Therefore:

- `legacy` must add no class and collect no new stylesheet. The current golden-master HTML must remain byte-identical.
- A modern selection adds exactly one modifier after the established classes, for example `hssb-appearance--minimal`.
- All appearance IDs must be mapped from a domain allowlist; never append a raw stored/request value to HTML.
- The modifier belongs on `.zmshbt`, not on each network anchor, so a single selector applies to share and profile links.

The renderer currently emits empty anchors with functional `aria-label` values such as “Share on Facebook” and distinguishable profile-link labels. Keep those native links and names; appearance work does not need frontend JavaScript or new markup inside every anchor.

### Current pack CSS is duplicated and not actually identical

Every icon set ships a stylesheet. The four historical packs mostly use 32 px anchors and a 1.5x hover scale; Prajin has 41x32 rectangular square assets and special left-rail margins. Bootstrap Solid and Tabler Outline use 36 px anchors, a 1.06 scale, `:focus-visible`, and reduced-motion handling. Shared placement, mobile, separator, sizing, and interaction rules are repeated in each file ([historical styles](https://github.com/alimuzzaman/html-social-share-buttons/tree/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/iconset), [newer styles](https://github.com/alimuzzaman/html-social-share-buttons/tree/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/assets/iconsets)).

The six CSS files total about 10 KB uncompressed. Keeping modern rules in every pack would repeat the same maintenance problem and would not cover third-party packs. Leave these files untouched for `legacy`; let a later, core-owned stylesheet override their anchor presentation only when a modern modifier exists.

### The asset contract is mixed PNG/SVG and mixed aspect ratio

The six manifests expose eleven built-in set/shape combinations. Four packs contain PNG artwork plus a few SVG additions; two are entirely SVG. Most artwork is square, but Prajin's square assets are 106x83 and its existing CSS deliberately uses a rectangular 41x32 box ([manifests](https://github.com/alimuzzaman/html-social-share-buttons/tree/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/resources/iconsets), [Prajin CSS](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/iconset/prajin/style.css)).

Modern rules must not stretch artwork. Use a fixed clickable target, content-box inset, `background-origin: content-box`, `background-size: contain`, centered positioning, and no clipping of the artwork. This lets a 106x83 image become approximately 36x28 inside a 44x44 Minimal target instead of being forced into a square.

Third-party icon sets pass through `LegacyIconSetAdapter`, which guarantees slugged IDs, shapes, plain icon filenames, a stylesheet, and registered filesystem/URL roots, but it cannot guarantee polite CSS specificity or the absence of `!important` ([LegacyIconSetAdapter.php](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/Compatibility/Legacy/Api/LegacyIconSetAdapter.php)). Supporting the adapter's standard background-image contract is required; defeating arbitrary extension CSS is not.

### Current settings terminology needs correction

The React settings page titles the section “Icon Style,” labels the icon-set dropdown “Button style,” and previews only the selected pack's static preview image ([SettingsPayloadBuilder.php](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/Presentation/Admin/SettingsPayloadBuilder.php), [settings-renderer.js](https://github.com/alimuzzaman/html-social-share-buttons/blob/d4b78429d5cecc8fdc3edbbc8668805c4b625a24/src/js/admin/settings/settings-renderer.js)). The existing placement editor correctly calls square/circle “Button shape.”

Recommended copy:

- Section: **Appearance**
- Description: **Choose the icon set and how the buttons are presented on your site. Appearance applies everywhere the plugin renders buttons.**
- Existing control: **Icon set**
- New control: **Button appearance**
- Legacy help: **Keep the current size, spacing, and hover behavior for the selected icon set.**
- Minimal help: **Use consistent 44-pixel targets, balanced spacing, and a subtle hover lift.**
- Framed help: **Add a clean, shape-aware outline around each button.**
- Soft shadow help: **Place each icon on a quiet raised surface with a subtle shadow.**

## Why these four choices

### 1. Legacy (current) — `legacy`

This is a compatibility mode, not a design endorsement. It preserves all pack-specific dimensions, margins, rail behavior, hover scaling, focus behavior, CSS requests, and selector interactions exactly as they work at release 3.0.0. It must remain the default for existing and new option records until a user opts in.

### 2. Minimal (recommended) — `minimal`

Minimal solves the largest product problems without drawing a new card around artwork that already contains a brand-colored square or circle. It normalizes the hit area, spacing, focus, and motion while leaving the selected artwork recognizable. It is the safest recommendation across both old raster packs and the newer SVG packs.

### 3. Framed — `framed`

Framed adds a visible target boundary for themes where isolated icons feel loose. The frame follows the selected shape, but the icon image remains unchanged. This is meaningfully different from Minimal without becoming decorative.

### 4. Soft shadow — `soft-shadow`

Soft shadow provides the more contemporary raised-card treatment requested by the feature, while remaining restrained. The shadow is static; hover animates only transform. This avoids expensive, distracting shadow animation and allows a clear forced-colors fallback.

Stop at these options. Current listings show that the category often expands into gradients, glass, glow, shine, leaf, diamond, many shadow strengths, and separate hover-effect menus ([Webworq Social Share](https://wordpress.org/plugins/webworq-social-share/), [Lion Social Share](https://en-gb.wordpress.org/plugins/lion-social-share/)). Those lists demonstrate demand for customization, not a reason to ship a novelty matrix. WordPress Core's three Social Icons styles and the simpler axes in AddToAny, Jetpack, and Sassy Social Share support a compact initial set ([AddToAny](https://wordpress.org/plugins/add-to-any/), [Jetpack Sharing Buttons](https://jetpack.com/support/sharing/), [Sassy Social Share](https://wordpress.org/plugins/sassy-social-share/)).

Do not add Pill yet. A useful pill needs a visible service label, while the current anchors are icon-only. Visible labels are a separate semantic and layout feature, not a CSS skin. Do not add monochrome, outline-art, or recolored variants either: the existing assets bake their brand background and glyph into PNG/SVG files, so CSS filters would be inconsistent and could damage contrast or brand recognition.

## Visual contract

| Property | Legacy | Minimal | Framed | Soft shadow |
|---|---:|---:|---:|---:|
| Click target | Pack-owned (currently 32, 36, or 41x32/41) | 44x44 px | 44x44 px | 44x44 px |
| Artwork box | Pack-owned, usually `cover` | max 36x36, contain | max 30x30, contain | max 30x30, contain |
| Group gap | Pack-owned margins | 8 px | 8 px | 8 px |
| Outer background | Pack-owned | Transparent | Transparent | `--hssb-surface`, default `Canvas` with `#fff` fallback |
| Border | Pack-owned | None | 1 px `--hssb-border` | 1 px `--hssb-border` |
| Square radius | Pack-owned | Focus ring: 10 px | 10 px | 10 px |
| Circle radius | Pack-owned | Focus ring: 50% | 50% | 50% |
| Resting shadow | Pack-owned | None | None | `0 2px 8px rgb(0 0 0 / 16%)` |
| Hover | Pack-owned | `translateY(-1px) scale(1.03)` | same | `translateY(-2px) scale(1.02)` |
| Active | Pack-owned | no lift, `scale(.98)` | same | same |
| Transition | Pack-owned | transform, 160 ms | transform, 160 ms | transform, 160 ms |

All width and height values refer to CSS pixels. A 44x44 target is stronger than WCAG 2.2 AA's 24x24 minimum and directly follows WAI Technique C44 for the enhanced 44x44 target ([Target Size Minimum](https://www.w3.org/WAI/WCAG22/Understanding/target-size-minimum.html), [Technique C44](https://www.w3.org/WAI/WCAG22/Techniques/css/C44.html)). The plugin can exceed AA without claiming that the whole product meets AAA.

The 44 px target is the anchor itself, not invisible margin around a 32 px link. Adjacent targets therefore remain reliably clickable. The group must use flex layout, wrap in horizontal contexts, and retain the established mobile behavior that turns fixed side rails into centered static rows at 600 px. Also verify a 320 px viewport and 400% zoom against WCAG reflow guidance ([Reflow](https://www.w3.org/WAI/WCAG22/Understanding/reflow.html)).

### Shape behavior

- `square`: the outer frame/focus ring uses 10 px radius. The artwork is never clipped, so existing internally square, rounded, or rectangular art remains intact.
- `circle`: the outer frame/focus ring uses 50% radius. The artwork uses the icon set's circle directory.
- A third-party shape slug that is neither `square` nor `circle` gets the square-radius fallback. Do not infer unsupported geometry from filenames.
- Minimal has no visible outer frame, but its focus indicator should still follow the shape class.

### Mixed share and profile links

The same appearance applies to both share anchors and `.zmshbt-profile-link`. Preserve the semantic separator. Modern shared CSS should own one separator rule: a 1x32 vertical separator in rows and a 32x1 horizontal separator in desktop rails, with the same 8 px rhythm. Do not let each icon set redefine it for modern appearances.

## Interaction and accessibility contract

WordPress expects ecosystem code to target WCAG 2.2 AA ([WordPress accessibility coding standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/accessibility/)). These rules apply to every modern appearance:

1. Keep the existing native `<a href>` elements and functional accessible names. Do not replace them with `div`/JavaScript controls. Functional images/links need names that describe the action, which the current `aria-label` already provides ([WAI functional images](https://www.w3.org/WAI/tutorials/images/functional/)).
2. Use a two-color keyboard focus indicator. WAI C40 recommends two bands at least 2 px thick with colors at least 9:1 apart, allowing one band to maintain 3:1 contrast on an unknown solid background ([Technique C40](https://www.w3.org/WAI/WCAG22/Techniques/css/C40.html)). A suitable baseline is an inner `2px #f9f9f9` outline and outer `4px #193146` box-shadow. Keep an outline because forced-colors mode suppresses box-shadow.
3. Provide a `:focus` fallback, then narrow pointer-only suppression with `:focus:not(:focus-visible)` in supporting browsers. Never ship `outline: none` without the replacement. `:focus-visible` lets the browser decide when a strong focus indicator is appropriate ([MDN `:focus-visible`](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/Selectors/%3Afocus-visible)).
4. Restrict hover transforms to `@media (hover: hover) and (pointer: fine)`. Touch browsers can have absent or sticky hover behavior ([MDN hover media feature](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/%40media/hover)).
5. Keep motion under 0.2 seconds, use transforms, and avoid bounce/rotation. WordPress's animation guidance explicitly recommends fast, simple, consistent transform-based motion and respect for Reduce Motion ([WordPress animation guidance](https://developer.wordpress.org/block-editor/explanations/user-interface/animation/)).
6. Under `prefers-reduced-motion: reduce`, remove transition and transform for hover and active states. The media query reflects the user's operating-system request to reduce nonessential motion ([MDN reduced motion](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/%40media/prefers-reduced-motion)).
7. Under `forced-colors: active`, do not set `forced-color-adjust: none`. Shadows disappear in forced colors, so add a 2 px `ButtonText` border for framed/soft targets and use `Highlight` for focus. URL background images are not the non-URL gradients that forced-colors removes, but this still needs a real Windows High Contrast test ([MDN forced colors](https://developer.mozilla.org/en-US/docs/Web/CSS/Reference/At-rules/%40media/forced-colors)).
8. Do not promise that every legacy brand asset meets non-text contrast. The appearance layer must not make it worse, but the existing raster/SVG artwork needs a separate icon contrast audit. WCAG's non-text criterion uses 3:1 for graphical objects needed to identify a control ([Non-text Contrast](https://www.w3.org/WAI/WCAG22/Understanding/non-text-contrast)).

The current 1.5x hover scale in historical packs can overlap neighboring buttons. Modern variants cap the scale at 1.03 and move no more than 2 px. Keyboard focus does not move the target.

The existing forced `target="_blank"` behavior is outside this appearance feature. WordPress guidance generally favors notifying users about new tabs; record it as separate accessibility follow-up rather than expanding this implementation.

## Proposed CSS architecture

Create one file, for example `assets/frontend/button-appearance.css`, and register it under a prefixed handle such as `hssb-button-appearance`. WordPress directs plugins to enqueue CSS with `wp_enqueue_style()` and to use unique prefixes to avoid collisions ([plugin assets guidance](https://developer.wordpress.org/plugins/plugin-basics/determining-plugin-and-content-directories/), [plugin naming guidance](https://developer.wordpress.org/plugins/plugin-basics/best-practices/)).

Suggested selectors:

```css
.zmshbt.hssb-appearance--minimal,
.zmshbt.hssb-appearance--framed,
.zmshbt.hssb-appearance--soft-shadow { /* shared group layout */ }

.zmshbt.hssb-appearance--minimal a,
.zmshbt.hssb-appearance--framed a,
.zmshbt.hssb-appearance--soft-shadow a { /* shared 44px target */ }
```

The appearance modifier gives higher specificity than current `.zmshbt.{iconset} a` rules. Enqueue the shared stylesheet after all collected icon-set styles. Do not use `!important` for normal appearance rules. The existing `!important` mobile/static and auto-hide rules need explicit contract tests.

Suggested tokens:

| Token | Default | Purpose |
|---|---|---|
| `--hssb-target-size` | `44px` | Clickable anchor width/height |
| `--hssb-art-size-minimal` | `36px` | Minimal content box |
| `--hssb-art-size-surface` | `30px` | Framed/Soft content box |
| `--hssb-gap` | `8px` | Space between targets |
| `--hssb-square-radius` | `10px` | Square outer frame/focus geometry |
| `--hssb-circle-radius` | `50%` | Circle outer frame/focus geometry |
| `--hssb-surface` | `Canvas` (`#fff` fallback first) | Soft target surface |
| `--hssb-border` | `#c3c4c7` | Resting frame |
| `--hssb-shadow` | `0 2px 8px rgb(0 0 0 / 16%)` | Static Soft elevation |
| `--hssb-focus-inner` | `#f9f9f9` | Inner focus band |
| `--hssb-focus-outer` | `#193146` | Outer focus band; >9:1 from inner |
| `--hssb-motion-duration` | `160ms` | Transform duration |
| `--hssb-motion-easing` | `cubic-bezier(.2,.8,.2,1)` | Restrained movement |

Declare literal fallbacks before custom-property declarations where losing the value would break target size or focus. WordPress dropped IE11 support in 5.8, so current browser-facing enhancements can use custom properties and flex gap; legacy output remains available to any older client ([official IE11 phase-out](https://make.wordpress.org/core/2021/04/22/ie-11-support-phase-out-plan/)).

The shared base should set `box-sizing:border-box`, `display:block`, 44 px width/height, zero legacy margins, centered/no-repeat background, and `background-origin:content-box`. Use padding of 4 px for Minimal and 7 px for Framed/Soft, with `background-size:contain`. It should also neutralize pack-specific 1.5x transforms only inside a modern modifier.

### Floating rails and auto-hide

Modern appearance must not accidentally disable the existing Auto hide setting. Because Prajin historically hides part of a child with negative margins while other packs offset the wrapper, overriding anchor margins can change that behavior. Avoid per-icon-set exceptions. For modern appearances only, add a core-owned modifier such as `hssb-rail--auto-hide` to automatic floating wrappers and define one generic reveal behavior:

- hide the rail with a wrapper transform while leaving a 12 px affordance;
- reveal on `:hover` and `:focus-within`;
- remove the transform under reduced motion rather than animating it;
- make the rail static, centered, wrapped, and fully visible at the established 600 px breakpoint;
- do not emit this class or CSS behavior for Legacy.

This preserves the setting's intent and makes it keyboard-operable without carrying Prajin-specific rules into the new layer. It requires an explicit render option from automatic placement; blocks, widgets, shortcodes, and builders are not auto-hide rails.

### Loading and performance

- Legacy: no additional class, stylesheet, image, request, or frontend script.
- Modern: one small cacheable CSS file; no new icon files and no JavaScript.
- Collect/enqueue the appearance stylesheet only after a modern outcome is rendered. Deduplicate the handle per request.
- Elementor may need the registered handle in `get_style_depends()` so its preview iframe paints correctly; because Elementor already lists all pack handles, measure rather than claim conditional loading there.
- Animate only `transform`; keep the Soft shadow static and do not add persistent `will-change` layers.
- All icon URLs remain local and continue through the existing resolver/collector.

## Admin preview requirement

The existing pack preview image cannot demonstrate target size, frame, gap, focus geometry, or hover. Add a live, non-navigating preview below the two dropdowns using the per-network preview URLs already present in `IconSetPayloadBuilder`.

The preview should:

- render three representative networks from the selected icon set;
- use the same shared appearance classes/tokens as frontend output;
- show square and circle rows when the set supports both, otherwise the one supported shape;
- update immediately when Icon set or Button appearance changes;
- have an accessible text label while decorative preview icons use empty alt text;
- include a visible “Keyboard focus” sample or a focusable, non-activating preview control so the focus treatment can be inspected;
- never use the full frontend share URLs or open a network.

Enqueue the shared appearance CSS on this settings screen for preview parity. Do not maintain a second hand-copied React style model.

## Settings and render model

Recommended domain surface:

```text
final class ButtonAppearance
  LEGACY = 'legacy'
  MINIMAL = 'minimal'
  FRAMED = 'framed'
  SOFT_SHADOW = 'soft-shadow'
  all(): array
  supports(string): bool

Settings::buttonAppearance(): string
RenderRequest::buttonAppearance(): string
```

`SettingsSchema` should expose/support the fixed appearances, or the fixed `ButtonAppearance` allowlist can remain independent because icon-set/network schemas are extension-filtered while presentation CSS is core-owned. Do not let third parties add a stored appearance ID unless there is also a formal API to register its CSS and preview. An extension filter that only adds a slug would create broken output.

Resolution policy:

1. Durable setting missing/invalid -> `legacy`.
2. Actual plugin runtime render with no per-request appearance -> resolved global `Settings::buttonAppearance()`.
3. Standalone/test `RenderFacade` constructed without a settings repository -> `legacy`.
4. Version one does not accept instance overrides from block attributes, widget records, Elementor data, WPBakery params, or shortcode attributes.
5. If instance overrides are added later, use `inherit|legacy|minimal|framed|soft-shadow`; old absent attributes should mean `inherit` only after an explicit deprecation/compatibility decision, not silently in this release.

This policy lets a user select Minimal once and see it across the plugin, while an untouched upgrade remains Legacy everywhere.

## Integration behavior

| Surface | Version-one behavior | Storage/API change |
|---|---|---|
| Left/right/before/after automatic placement | Pass resolved global appearance; auto-hide modifier only on modern floating rails | Global option only |
| Social Share block | Server render uses global; editor preview receives localized resolved appearance | No new serialized block attribute |
| Social Links block | Same as Social Share, including profile-only output | No new serialized block attribute |
| Classic widget | Render uses global in addition to saved set/shape/networks | Do not add a widget saved key; keep baseline keys intact |
| Elementor | Render and editor iframe use global; register shared style dependency | No new Elementor control/data in v1 |
| WPBakery | Render uses global; builder preview/control schema remains set/shape only | No new element param in v1 |
| Shortcode | Existing shortcode inherits global through canonical facade | Do not add an attribute in v1; existing shortcode strings remain valid |
| PHP/legacy `zm_sh_btn()` | Runtime facade inherits global; a facade without Settings falls back to Legacy | No breaking function signature |
| Third-party icon set | Modern core selector wraps the adapted set's background assets | No manifest/add-on change; document `!important` boundary |

The canonical facade is the best single resolution point because every integration already converges there. Avoid copying `button_appearance => $settings->buttonAppearance()` into seven controllers unless a surface genuinely needs an explicit modifier such as floating auto-hide.

## Placement overrides: later, not now

The current settings screen already has four automatic placement panels, two shape choices, icon-set selection in multiple integrations, network toggles, and profile-link modes. Adding appearance to every placement now creates a configuration matrix without evidence that users need different skins above and below content.

Global-first also gives a clean testable promise: one selected appearance everywhere. Revisit overrides only with concrete demand such as “floating rail should be Soft while inline buttons should be Minimal.” If that demand appears, add `inherit` controls at placement/integration boundaries while keeping the global value as the single default. Do not encode placement into appearance IDs (`soft-left`, `minimal-inline`).

## Extensibility

Version one should make CSS tokens public enough for theme authors to adjust target surface, border, focus bands, radius, and shadow without registering a new appearance. Document that overrides must be scoped below `.zmshbt.hssb-appearance--*`.

Do not expose an appearance-registration filter yet. A valid extension contract would need an ID, translated label/help, wrapper class, frontend stylesheet handle, admin preview support, compatibility declaration for all shapes, and fallback behavior. The current icon-set extension API has assets and stylesheets; a slug-only settings-schema filter is insufficient.

If custom appearances are later justified, introduce a real `ButtonAppearanceRegistry` rather than allowing arbitrary CSS class strings through the renderer.

## Test matrix

Avoid a blind full Cartesian suite, but cover every independent contract and every built-in asset geometry.

### Unit and storage

- `ButtonAppearance::all()` and support checks.
- Codec: missing, empty, invalid slug, array/object, and each valid value.
- Encode/decode round trip and preservation of unrelated/extension keys.
- Upgrade fixture without key -> Legacy; save Minimal -> scalar key; simulated old codec ignores/preserves key.
- Request sanitizer allowlist and CSRF/capability path through the existing AJAX controller.
- Settings React normalization and dirty/save behavior.

### HTML and asset contracts

- Existing golden-master scenarios remain byte-for-byte identical when absent/Legacy.
- Modern output adds exactly one expected wrapper modifier; anchor order, URLs, `rel`, target, `aria-label`, profile markers, and newlines remain otherwise unchanged.
- Unknown/malformed appearance never becomes a class.
- Legacy collects only current pack styles; modern collects pack plus one deduplicated shared style, in the required cascade order.
- Mixed share/profile separator remains present and styled once.

### Built-in appearance geometry

Exercise all eleven supported pairs:

- Default square.
- Flat square/circle.
- Long Shadows square/circle.
- Prajin square/circle.
- Bootstrap Solid square/circle.
- Tabler Outline square/circle.

For each modern appearance, assert 44x44 computed targets, contained/non-stretched background artwork, expected radius/frame/shadow, 8 px spacing, and no legacy 1.5x hover transform. Include a registered third-party icon set with a nonstandard shape slug and mixed PNG/SVG files.

### Surfaces

- Four automatic placements, including auto-hide off/on.
- Shortcode and `zm_sh_btn()` PHP output.
- Classic widget.
- Social Share and Social Links dynamic blocks, editor and frontend.
- Elementor editor iframe and frontend.
- WPBakery editor/shortcode frontend.
- Profile-only and mixed share/profile groups.

### Accessibility and responsive browser checks

Run the existing Chrome, Edge, Firefox, and WebKit matrix on current desktop and mobile viewports. Add assertions/checks for:

- mouse hover only on fine hover-capable pointers;
- keyboard Tab focus and two-color focus visibility on light, dark, and brand-colored theme backgrounds;
- `prefers-reduced-motion: reduce` removes transition/transform;
- Chromium forced-colors emulation plus a real Windows High Contrast check before release;
- 320 px viewport and 400% zoom/reflow;
- fixed rails become static rows at <=600 px;
- 44x44 touch targets do not overlap and rows wrap;
- auto-hidden rail reveals with `:focus-within` and remains reachable;
- RTL themes and common theme link resets (`box-shadow`, `text-decoration`, margins).

Do not mark browser/visual acceptance complete from PHP, DOM, or static CSS tests alone.

## Implementation phases and likely files

### Phase 0: lock Legacy

1. Add explicit golden tests that stored missing, request missing, and `legacy` produce the current HTML and stylesheet list.
2. Add computed-style/screenshot baselines for all eleven set/shape pairs before modern CSS exists.
3. Record current auto-hide behavior so intentional modern normalization is distinguishable from a Legacy regression.

Likely files: `tests/frontend-output-scenarios.json`, `tests/fixtures/frontend-output-baseline.json`, `tests/phpunit/CanonicalRendererParityTest.php`, `tests/e2e/browser-matrix/iconsets.spec.js`, `tests/iconset-css-contract.php`.

### Phase 1: domain, storage, and admin control

1. Add `ButtonAppearance` and the `Settings` property/getter.
2. Decode missing/invalid as Legacy; encode the scalar key without migration.
3. Map/sanitize the admin field through both canonical and legacy-compatible form paths.
4. Rename existing UI copy and add the dropdown/help.
5. Add a live preview that reuses core appearance CSS.

Likely files: `src/Domain/Settings/ButtonAppearance.php`, `Settings.php`, `SettingsDefaults.php`, `SettingsSchema.php`, `OptionSettingsCodec.php`, `OptionSettingsRequestMapper.php`, `SettingsRequestSanitizer.php`, `SettingsPayloadBuilder.php`, `src/js/admin/settings/app.js`, `settings-model.js`, `settings-renderer.js`, `components.js`, `assets/admin.css`, translation catalogs, and admin tests/fixtures.

### Phase 2: render modifier and shared CSS

1. Resolve global appearance in `RenderFacade`, falling back to Legacy without Settings.
2. Carry the value in `RenderRequest` and append only known non-Legacy modifiers in `HtmlRenderer`.
3. Register/collect/enqueue the shared style after pack styles only for modern output.
4. Add the modern separator and generic auto-hide rail modifier.
5. Keep all legacy pack stylesheets byte-unchanged.

Likely files: `RenderRequest.php`, `RenderRequestMapper.php`, `RenderFacade.php`, `HtmlRenderer.php`, `RenderOutcome.php` if asset metadata changes, `AssetCollector.php`, `FrontendController.php`, `PluginFactory.php`, `PluginPaths.php` or `PluginConfig.php`, and new `assets/frontend/button-appearance.css`.

### Phase 3: integration/editor parity

1. Localize resolved appearance for block previews.
2. Register the appearance style for Elementor's editor iframe and verify WPBakery preview behavior.
3. Confirm widgets, shortcodes, PHP, profile links, and third-party sets converge on the facade default without new stored instance keys.

Likely files: `BlockRegistrar.php`, `src/js/blocks/social-share/register.js`, `src/js/blocks/social-links/register.js`, `ElementorShareWidget.php`/`ElementorRegistrar.php`, `WpBakeryRegistrar.php`, and their contract/e2e tests.

### Phase 4: release evidence

Run unit, settings, archive, integration, E2E, and browser-matrix gates; capture new modern screenshots only after the assertions pass. Document Legacy parity, conditional asset size, supported appearances, and theme override tokens. Do not advertise per-placement controls or full WCAG conformance.

## Acceptance criteria

The feature is ready only when all are true:

- Upgrading a 3.0.0 option with no new key renders byte-identical Legacy HTML and requests no new stylesheet.
- The dropdown defaults/falls back to Legacy for missing, invalid, and non-scalar values.
- Minimal, Framed, and Soft shadow render one safe modifier and one shared stylesheet across every built-in icon set.
- All eleven built-in set/shape pairs preserve aspect ratio; Prajin square is not stretched.
- Modern targets compute to 44x44 with 8 px group spacing and do not overlap at hover/active.
- Focus is visible with a two-color indicator; reduced motion and forced colors have explicit fallbacks.
- Modern rows wrap at 320 px and fixed rails become static at the existing mobile breakpoint.
- Auto hide still works for modern floating rails, reveals on keyboard focus, and does not change Legacy.

- Automatic placement, both blocks, widget, Elementor, WPBakery, shortcode, PHP, share/profile mixtures, and an adapted third-party set all use the same global selection.
- Admin preview uses the frontend appearance CSS rather than duplicated style logic.
- Legacy pack CSS is not mechanically rewritten or deduplicated in this feature.
- No frontend JavaScript, remote asset, icon duplication, or unconditional modern stylesheet is added.

## Expanded market, design-system, and icon-license research

Three focused follow-up reviews validate the compact appearance model and keep
icon artwork selection separate from presentation styling:

- [WordPress sharing-plugin patterns](research/FRONTEND-ICON-PRESENTATION-PLUGIN-PATTERNS.md)
  compares current first-party source and documentation for AddToAny, Jetpack,
  Sassy Social Share, Hubbub/Social Pug, Shariff Wrapper, and Scriptless Social
  Sharing. The recurring useful pattern is a 32-45 px target with a small,
  consistent gap and one wrapper-level presentation modifier. The recurring
  failure mode is a large matrix of unnamed styles, per-location geometry,
  hover-only information, or focus resets without a visible replacement.
- [Popular-site and design-system guidance](research/FRONTEND-ICON-PRESENTATION-SITES-AND-GUIDANCE.md)
  collects direct demos and primary guidance from W3C/WAI, WordPress Core,
  GitHub Primer, Material, Apple, Carbon, USWDS, GOV.UK, AddToAny, and
  ShareThis. It supports keeping labels, counters, placement, icon artwork,
  and appearance as separate controls. It also reinforces the 44 px target,
  functional accessible names, explicit focus, pointer-safe hover, reduced
  motion, forced-colors fallbacks, and wrapping at narrow widths.
- [Open-license icon-set candidates](research/OPEN-LICENSE-ICONSET-CANDIDATES.md)
  reviews upstream repositories and license files for Bootstrap Icons, Tabler,
  Simple Icons, Font Awesome Free, Lucide, Remix Icon, Phosphor, Heroicons,
  Material Symbols, Feather, and Radix Icons. Bootstrap Solid and Tabler
  Outline remain the best current bundled packs. Simple Icons is a possible
  future brand-source pipeline only with per-icon license and trademark review;
  Font Awesome Free is viable but notice-heavy. Generic UI sets are useful for
  Share, Copy, Link, Mail, and More controls but cannot replace a complete
  social-brand pack. Remix Icon's current custom license should stay out of a
  GPL bundle pending legal review.

These reviews do not justify adding more version-one appearances. They
strengthen the existing recommendation: ship Legacy, Minimal, Framed, and Soft
shadow; make Minimal the recommended opt-in; and treat any new icon pack as an
independent provenance, license, trademark, generation, and coverage project.

## Risks and mitigations

| Risk | Why it matters | Mitigation |
|---|---|---|
| Cascade order/specificity | Pack and theme CSS already target `.zmshbt ... a` | Modifier specificity, enqueue after packs, computed-style tests; no routine `!important` |
| Larger targets wrap | Current buttons can be 32 px | 44 px is intentional only in opt-in modes; flex wrap, 320 px tests, clear preview |
| Raster/aspect distortion | Prajin square is rectangular; files vary from 83 to 241 px | content-box inset plus `contain`; test all pairs |
| Auto-hide regression | Some packs hide wrappers, Prajin moves children | core-owned modern rail modifier; leave Legacy CSS untouched |
| Theme background contrast | Focus/borders sit on unknown pages | two-color focus, CSS tokens, light/dark/brand background tests |
| Forced colors removes shadow | Soft could lose its boundary | system-color border/outline; real High Contrast check |
| Third-party CSS uses `!important` | The adapter cannot police stylesheets | document standard contract; test one compliant extension; do not escalate selector arms race |
| Editor/frontend drift | Existing preview is a static pack image | share the same appearance CSS/tokens and icon URLs |
| Option explosion | Competitors expose many overlapping effects | ship four mutually distinct choices; defer labels, colors, sizes, and per-placement overrides |
| False accessibility claims | Existing brand assets were not audited here | claim the new interaction/target improvements only; run a separate asset contrast audit |

## Non-goals

- Recoloring brand artwork or converting historical PNG sets to SVG.
- Adding visible network labels, pills, counters, tooltips, native share, or copy-link behavior.
- Adding per-network, per-placement, per-post, or per-builder appearance overrides in version one.
- Reordering networks or changing share URLs/analytics.
- Rewriting the six Legacy stylesheets.
- Removing historical public classes or changing output quote/newline serialization.
- Guaranteeing compatibility with arbitrary third-party `!important`/inline CSS.
- Claiming browser visual validation before it is actually run.

## Decisions still requiring product confirmation

The research recommendation is firm, but two product choices should be confirmed before implementation:

1. Whether the UI should display **Minimal (recommended)** or simply **Minimal**. The former helps discovery while Legacy remains the compatibility default.
2. Whether Soft shadow should use the user-facing label **Soft shadow** (clear effect) or **Elevated** (more design-system language). Keep the stored value `soft-shadow` either way.

Everything else can proceed without a migration or per-placement schema.
