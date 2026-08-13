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
| Block editor | Dynamic Social Share and Social Links blocks | API v1 is retained for WordPress 5.3; two corresponding Plugin Check findings are accepted |
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

## Supported networks and icon sets

Share actions: Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and Email.
Optional profile/contact links are configured independently.

The Default set supports square buttons. Flat, Long Shadow, Prajin, Bootstrap
Solid, and Tabler Outline support square and circle buttons. At 600px and
below, floating left/right rails become static, centered, wrapping rows.

## Current limitations and accepted dispositions

- The Default PNG pack is retained under an accepted compatibility exception;
  this is not an independent provenance or clearance claim.
- WPBakery evidence uses official documentation and executable repository
  contracts when the paid editor is unavailable.
- Block API v1 remains necessary for WordPress 5.3. Plugin Check therefore has
  two accepted `block_api_version_too_low` findings; no clean result or baseline
  is claimed.
- Physical iPhone and high-contrast review are outside the recorded browser
  evidence.
- The canonical implementation rewrite is complete. Release completion still
  requires the real 14-day staging soak, day-7/final staging rollback evidence,
  and explicit version/listing/release approval.

## Authoritative records

- `REWRITE-COMPATIBILITY-DECISIONS.md`
- `LEGACY-COMPATIBILITY-INVENTORY.md`
- `BROWSER-VALIDATION.md`
- `ICON-COVERAGE-MATRIX.md`
- `RELEASE-CANDIDATE-VALIDATION.md`
- `resources/iconsets/ASSET-SOURCES.md`
