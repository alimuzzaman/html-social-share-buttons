# Current compatibility report

This report describes the post-2.2.6 rewrite on the `latest` branch. It is a
source and evidence summary, not release approval. Exact commands, archive
hashes, browser versions, and remaining operational gates are recorded in
`RELEASE-CANDIDATE-VALIDATION.md`.

## Declared support

| Surface | Current declaration | Evidence boundary |
|---|---|---|
| WordPress | 5.3 or newer; tested-up-to header 7.1 | Fresh WP 5.3/PHP 7.0 activation and legacy-shortcode smoke; modern contract suites on current WordPress |
| PHP | 7.0 or newer | PHP 7.0 syntax/bootstrap floor plus PHP 7.4, 8.0, 8.3, and 8.5 workflow rows |
| Browsers | Modern Chrome, Firefox, Edge, WebKit, and Safari evidence | Automated desktop/mobile fixtures are not universal theme or physical-device certification |
| Block editor | Dynamic Social Share and Social Links blocks | API v3 on WordPress 6.3+; API v1 compatibility registration on WordPress 5.3-6.2; forced-iframe editor verified on WordPress 7.1 final |
| Elementor | Editor persistence and public rendering | Version-specific fixture evidence, not every Elementor release |
| WPBakery | `vc_map()` mapping, storage, compiled bundle, and public rendering | Official documentation contract; no live paid-editor claim |

## Preserved upgrade surface

- The `zm_shbt_fld` option and `_zm_sh_disable_share` metadata remain in place.
- Historical shortcodes, widget identity/storage, block content, Elementor
  data, WPBakery shortcode/meta, public PHP symbols, hooks, classes, handles,
  CSS classes, icon paths, and the `twitter` compatibility identifier for X are
  retained through `src/Compatibility/Legacy/Api`.
- Runtime rendering, URL resolution, settings access, integrations, and
  presentation controllers are owned by canonical namespaced services.
- No replacement option, custom table, schema-version option, or data
  migration is introduced.
- Three additive audience booleans control the content author, other logged-in
  users, and logged-out visitors across every rendering integration. Missing
  keys mean visible, so upgrades preserve existing output without a migration.
- Bootstrap Solid is the default for a missing option on a new installation.
  Existing options with no icon-set key and explicit `default` selections
  continue to resolve to the historical Default pack. The pack remains in the
  runtime registry and archive but is omitted from fresh selection lists.

## Supported networks and icon sets

Share actions: Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and Email.
Optional profile/contact links are configured independently.

Bootstrap Solid is the default for new installations. The retained historical
Default set supports square buttons and is shown as `Default (legacy)` only
when already selected. Flat, Long Shadow, Prajin, Bootstrap Solid, and Tabler
Outline support square and circle buttons. At 600px and below, floating
left/right rails become static, centered, wrapping rows.

## Current limitations and accepted dispositions

- The Default PNG pack is retained under an accepted compatibility exception;
  this is not an independent provenance or clearance claim.
- WPBakery evidence uses official documentation and executable repository
  contracts when the paid editor is unavailable.
- The maintained metadata is API v3. On WordPress 5.3-6.2, PHP registers the
  dynamic blocks through the metadata-reading fallback and the editor selects
  API v1, preserving the support floor without presenting unsupported v3
  semantics to old Core.
- Physical iPhone and high-contrast review are outside the recorded browser
  evidence.
- The canonical implementation rewrite is complete. The release owner waived
  the earlier fourteen-day soak on 2026-08-13 after a fresh exact-archive
  manual review. Release completion still requires an immutable candidate
  revision and explicit listing/release approval.

## Authoritative records

- `REWRITE-COMPATIBILITY-DECISIONS.md`
- `LEGACY-COMPATIBILITY-INVENTORY.md`
- `BROWSER-VALIDATION.md`
- `ICON-COVERAGE-MATRIX.md`
- `RELEASE-CANDIDATE-VALIDATION.md`
- `resources/iconsets/ASSET-SOURCES.md`
