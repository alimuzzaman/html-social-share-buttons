# Rewrite gap review after 2.2.6

## Release-readiness summary

`v2.2.6` (`620f1ae66`) remains the published baseline. The `latest` working
tree contains a canonical-first rewrite and release-hardening work; it is not a
public 3.0 release. Candidate metadata is aligned at 3.0.0 to prevent the new
archive from colliding with published 2.2.6. Do not create a tag, upload,
deploy, or describe this work as a completed 3.0 release without explicit
approval and completion of the release gates.

The rewrite substantially reduces the compatibility layer, but source and
contract coverage are not release evidence on their own. Maintainer
authorization is recorded for Flat, Long Shadow, and Prajin; the Default pack
lacks a verifiable source/license record but is retained under the release
owner's explicit compatibility exception. WPBakery uses its official documented
integration contract when the paid editor is unavailable. Cross-browser visual
evidence is recorded, including Safari 26.6 and the corrected 390-pixel case.
The first staging attempt started on 2026-08-12 and was reset on 2026-08-13
after this release-diff audit found candidate-byte defects. Its Day 1 evidence
is preserved as superseded and cannot count toward the corrected candidate. A
limited single-site candidate -> 2.2.6 -> candidate rollback rehearsal is
recorded in `RELEASE-CANDIDATE-VALIDATION.md`; it is not the staging-soak
rollback gate.

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
  builder identifiers, stored content, wrapper/classes, and link destinations
  stay public compatibility surfaces. The corrected 3.0.0 baseline adds
  translated accessible names to otherwise-empty share anchors.
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
| WordPress/PHP declaration | Header and readme declare WP 5.3+ and PHP 7.0+; configured WP 6.8/PHP 8.3 and current/PHP 8.3 contract rows passed; WP 5.3/PHP 7.0 has a functional smoke | Remaining supported matrix runs; the WP 5.3 path must not be represented as a full modern-suite certification |
| Archive and autoloader | Two builds matched and the ZIP activated on a clean Sandbox without Composer | Repeat for the approved versioned candidate |
| Frontend compatibility | PHPUnit, the 33-scenario golden comparison, fresh-ZIP smoke, and stored browser fixtures passed; the eight-project isolated fixture/non-overlap matrix passed; Safari 26.6 desktop/Responsive Design Mode captures are recorded | Physical-device/high-contrast review is not claimed |
| Gutenberg | Both API-v3 blocks passed forced-iframe inserter visibility, editor insertion, inspector persistence, save/reload, and frontend rendering on WordPress 7.1 final | Broader supported-version and manual browser captures |
| Elementor | Real editor persistence, visible icon preview, and public stored-data fixture passed with Elementor 4.2.2 | Manual cross-browser captures and supported-version matrix |
| WPBakery | Canonical mapping, stored-shortcode, bundle-smoke, and public-render contracts match the official `vc_map()`/shortcode model | Live paid-editor behavior is not claimed |
| Icon packs | Manifests, filenames, browser matrix, Safari review, and owner provenance dispositions are recorded; the 390-pixel collision is fixed and regression-tested | Physical-device/high-contrast review and trademark review are not claimed |
| Rollback | No data migration/replacement schema is designed; an isolated predecessor-candidate -> published 2.2.6 -> candidate rehearsal passed on WP 5.3/PHP 7.0 | Day-7 and final rollback with the corrected exact candidate bytes |
| Staging | Attempt 01 Day 1 passed, then the clock was reset because candidate bytes had to change | Exact installation and a new Day 1, followed by fourteen real elapsed days and final rollback |

## Closed decisions, scope limits, and remaining release operations

1. Flat, Long Shadow, and Prajin carry the maintainer's dated attestation of
   rights-holder authorization and their historical credits. The repository
   does not archive those written authorizations. The Default pack still lacks
   an upstream author, version, or licence grant, but the release owner accepted
   retaining it as a compatibility exception on 2026-08-12. Do not turn that
   exception into a claim of independent clearance.
2. Current Chrome, Firefox, Edge, and Playwright WebKit desktop/mobile fixture
   and non-overlap checks pass in a fresh isolated worker. Safari 26.6 desktop
   and 390×844 Responsive Design Mode captures are recorded. The mobile fixed
   rail now enters document flow at 600 pixels or below, and the browser test
   fails on heading intersection. Physical-device/high-contrast behavior is
   outside the recorded claim.
3. The isolated candidate E2E run provisions a real Elementor document and
   verifies its visible editor preview and public frontend. For unavailable
   paid builders, including WPBakery, the release owner selected official API
   documentation plus exact repository mapping, persistence, bundle, and public
   rendering contracts. This is not described as a live WPBakery editor run.
4. The isolated rollback rehearsal passed. Staging attempt 01 was reset after
   its Day 1 because the release audit required candidate-byte changes. The
   release owner waived a replacement fourteen-day attempt on 2026-08-13 after
   a fresh exact-archive manual review; the reviewed snapshot still needs an
   immutable candidate revision before release.
5. Placement-level profile-link controls are available for automatic placement
   and as opt-in `profile_links_mode` controls for the share block, shortcode,
   widget, Elementor, WPBakery, and direct facade inputs. Missing stored values
   continue to inherit global profile links. Custom per-placement profile URL
   maps remain intentionally out of scope; placement controls only inherit or
   suppress the configured global links.
6. Plugin Check remains recorded separately from compatibility warnings. The
   exact corrected archive has zero errors and 57 reviewed warnings: modern
   WordPress registers the API-v3 metadata while WordPress 5.3-6.2 uses the
   tested runtime API-v1 fallback. No baseline hides findings.
7. Candidate version, stable tag, changelog, and block metadata are aligned at
   3.0.0. Screenshots and WordPress.org publication remain final release-owner
   work; metadata alignment alone does not authorize a release.

## Release sequence

1. Freeze a candidate revision and record all syntax, quality, Sandbox,
   Plugin Check, archive, fresh-install, and compatibility-contract results.
2. Re-run the closed provenance/browser/builder contract gates after relevant
   candidate changes.
3. Preserve the real Gutenberg/Elementor evidence and WPBakery documentation-
   contract evidence with the candidate ledger.
4. Commit the reviewed snapshot, rebuild the production archive from that
   immutable revision, and reconfirm its identity and focused exact-archive
   gates.
5. Obtain release-owner approval before changing version metadata, stable tag,
   listing material, publication artifacts, or deployment state.
