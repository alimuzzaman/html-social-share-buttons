# Canonical rewrite implementation summary

The post-2.2.6 implementation rewrite is complete in source. Release operations
remain separate and are tracked in `RELEASE-CANDIDATE-VALIDATION.md` and
`STAGING-SOAK.md`.

## Runtime ownership

- `html-social-share.php` performs dependency checks, composes the canonical
  kernel through `Bootstrap\PluginFactory`, boots it, and then registers public
  compatibility aliases.
- `PluginFactory` constructs network/icon registries, settings, rendering,
  placement, translation, admin, shortcode, block, widget, Elementor,
  WPBakery, metabox, and extension-hook services.
- `Presentation\Rendering\RenderFacade` maps every public integration to the
  canonical server-side renderer.
- `Compatibility\Legacy\Api` is the sole compatibility boundary. It delegates
  preserved globals, functions, classes, hooks, settings shapes, and icon-set
  add-ons; it does not own a second service graph or rendering path.

## Persisted and public compatibility

The rewrite retains `zm_shbt_fld`, `_zm_sh_disable_share`, both shortcode tags,
widget identity/storage, both dynamic blocks, Elementor/WPBakery stored data,
legacy public PHP symbols and hooks, frontend classes/markup, and historical
asset URLs. Canonical values are normalized in memory. No replacement option,
custom table, schema version, or reverse migration is required.

## Additive product work

- Global profile/contact links with per-placement inherit or suppress controls.
- Dynamic Social Links block.
- Bootstrap Solid and Tabler Outline generated SVG sets.
- Responsive mobile correction for floating left/right rails.
- Current settings UI, template editor, exclusion search, localization, and
  compiled builder/block bundles.

## Verification model

Unit, AJAX, multisite, frontend golden, architecture/thinness, settings,
integration, localization, browser, archive, activation, support-floor, and
rollback contracts protect the rewrite. WPBakery's unavailable paid editor and
physical-device/high-contrast review remain documented evidence limits, not
unimplemented runtime paths.

The complete implementation does not imply release completion. The release
owner waived the earlier fourteen-day staging requirement on 2026-08-13 after
a fresh exact-archive manual review. The reviewed snapshot must still be
committed, rebuilt from that immutable revision, and explicitly approved
before publication.
