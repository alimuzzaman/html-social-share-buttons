# Rewrite gap review after 2.2.6

## Release-readiness summary

`v2.2.6` (`620f1ae66`) remains the published baseline. The `latest` working
tree contains a canonical-first rewrite and release-hardening work; it is not a
public 3.0 release. The plugin header, both block metadata files, and
`Readme.txt` remain at 2.2.6. Do not change the stable tag, create a release,
or describe this work as a completed 3.0 release without explicit approval.

The rewrite substantially reduces the compatibility layer, but source and
contract coverage are not release evidence on their own. The repository still
does not establish PNG redistribution rights, a licensed WPBakery editor run,
cross-browser visual parity, a rollback rehearsal, or a 14-day staging soak.

## Completed implementation work

- The canonical bootstrap now composes the kernel in `Bootstrap`: immutable
  plugin configuration and paths, `PluginFactory`, `Plugin`, and a single
  `HookRegistrar`. Canonical presentation controllers own settings, frontend
  placement, shortcodes, blocks, widgets, Elementor, WPBakery, metaboxes, and
  admin requests. The old root PHP forwarding files and the old compatibility
  runtime have been removed.
- Rendering now flows through `Presentation\\Rendering\\RenderFacade`, with a
  request mapper, WordPress context factory, hook-aware URL resolver,
  application button builder, and HTML renderer. The facade is the common
  server-side renderer for automatic placement, the public API, shortcode,
  block, widget, Elementor, and WPBakery adapters. JavaScript remains editor
  controls and previews only; frontend markup remains PHP-owned.
- `Compatibility/Legacy/Api` is intentionally a thin public-symbol bridge,
  not a second runtime. It exposes retained constants, globals, functions,
  classes, hook translation, option/icon-set value adaptation, and external
  icon-set import by delegating into the already-booted canonical kernel.
  `zm_shbt_fld`, `_zm_sh_disable_share`, historical handles, APIs, hooks,
  builder identifiers, stored content, and documented markup stay public
  compatibility surfaces.
- The canonical `PluginConfig` owns stable identifiers while mapping them to
  their historical values. Both shortcode names, `[zm_sh_btn]` and
  `[html-social-share-buttons]`, are registered. Omitted URLs and recognised
  legacy permalink placeholders resolve from the block post context, loop or
  global post, queried post, AJAX post IDs, and then the historical fallback.
  The canonical resolver ensures the resulting share URL is escaped and
  percent-encoded once rather than exposing `%%permalink%%` or its encoded
  form.
- Root `block.json` remains the canonical definition of
  `html-social-share/social-share`. `blocks/social-links/block.json` defines
  the new separate `html-social-share/social-links` dynamic block. The server
  registers both from metadata, uses the metadata API where available and a
  WordPress-5.3 fallback otherwise, and uses PHP render callbacks directly to
  the facade. Both editor implementations import their metadata and have
  `save()` return `null`; neither block renderer calls the shortcode callback.
- A source checkout without `vendor/autoload.php` stops before runtime boot,
  fails activation with remediation guidance, and reports an admin notice for
  incomplete active installs. The release owner prepares a no-dev,
  classmap-authoritative Composer loader and the `zip` script verifies it before
  writing an archive; the distribution contract requires the
  production autoloader/maps, PHP source, metadata, built bundles, and assets.
- Settings, builder, block, and frontend strings now use the plugin text
  domain. PHP supplies the settings payload, editor bundles use `wp.i18n`, and
  block scripts register translations where WordPress supports the API. The
  localization contract covers the tracked source strings; translated-language
  visual review remains a separate gate.
- `resources/iconsets/*.php` is the small canonical metadata layer (ID,
  labels, shapes, filenames, and `asset_path`), not a second icon pack. The
  historical Default, Flat, Long Shadow, and Prajin files remain in their
  released `iconset/` locations; duplicate copies under `assets/iconsets/`
  were removed. New vector pack assets and their provenance records remain in
  their own `assets/iconsets/` locations. The exact declared support matrix is
  in `ICON-COVERAGE-MATRIX.md`.

## Evidence still required for a candidate

| Area | Implementation status | Evidence still required |
|---|---|---|
| PHP, JavaScript, icon, settings, block, localization contracts | Passed in the 2026-08-11 candidate validation; see `RELEASE-CANDIDATE-VALIDATION.md` | Repeat after any candidate code change |
| WordPress/PHP declaration | Header and readme declare WP 5.3+ and PHP 7.0+ | Supported matrix runs; the WP 5.3 path must not be represented as a full modern-suite certification |
| Archive and autoloader | Two builds matched and the ZIP activated on a clean Sandbox without Composer | Repeat for the approved versioned candidate |
| Frontend compatibility | PHPUnit, the 33-scenario golden comparison, fresh-ZIP smoke, and stored browser fixtures passed | Manual browser matrix and licensed WPBakery editor evidence |
| Gutenberg | Dynamic metadata registration and a real stored-block browser fixture passed | Supported-version and manual browser captures |
| Elementor | Real editor persistence, visible icon preview, and public stored-data fixture passed with Elementor 4.2.2 | Manual cross-browser captures and supported-version matrix |
| WPBakery | Canonical optional integration and stored-shortcode contracts are present | Licensed editor environment, fixture, persisted data, and frontend capture |
| Icon packs | Manifests, filenames, and support matrix are defined | Provenance/legal decision and desktop/mobile browser matrix |
| Rollback | No data migration/replacement schema is designed | Completed 2.2.6 rollback rehearsal with storage and output comparison |
| Staging | Plan only | Fourteen consecutive days of recorded evidence |

## Release blockers and decisions still needed

1. Historical PNG source, modification, redistribution, attribution, and
   trademark evidence remain unresolved. The notes in
   `resources/iconsets/ASSET-SOURCES.md` are not clearance. Keep the files for
   compatibility, but do not claim they are cleared or import replacements
   without an approved provenance record.
2. Manual current Chrome, Firefox, Safari, and Edge desktop/mobile icon and
   layout parity has not been recorded.
3. The isolated candidate E2E run now provisions a real Elementor document
   through Elementor's `save_builder` action and verifies its visible editor
   preview and public frontend. A licensed WPBakery editor fixture remains a
   hard external requirement; only its stored-shortcode public fixture passed.
4. Rollback rehearsal and the 14-day staging soak have not started or passed.
5. Placement-level profile-link controls remain a product decision: global
   inheritance and the Social Links block are implemented, but a per-placement
   profile-link control is not claimed complete.
6. Plugin Check must be recorded separately from compatibility warnings. Its
   API-v1 findings needed for the WordPress 5.3 floor require disposition; do
   not label those warnings as fixed without a compatibility decision.
7. Version/listing alignment, screenshots, FAQ, and release copy remain final
   release-owner work. The version is deliberately still 2.2.6, not
   `3.0.0-rc.1`.

## Release sequence

1. Freeze a candidate revision and record all syntax, quality, Sandbox,
   Plugin Check, archive, fresh-install, and compatibility-contract results.
2. Complete the provenance/legal decision and the browser/icon matrix.
3. Capture real Gutenberg and Elementor evidence and verify WPBakery in a
   licensed editor environment.
4. Approve a candidate artifact, perform the rollback rehearsal, and run the
   documented 14-day staging soak.
5. Obtain release-owner approval before changing version metadata, stable tag,
   listing material, publication artifacts, or deployment state.
