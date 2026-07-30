# Rewrite gap review after 2.2.6

## Executive status

The latest published baseline is `v2.2.6` at commit `620f1ae66`. The current
branch has one committed accessibility/release-verification change after that
tag plus the uncommitted rewrite work described here.

The rewrite now owns production share-button construction and keeps exact
public output through a compatibility renderer. Historical globals and
WordPress adapters are isolated under `Compatibility/Legacy`; settings and
legacy asset URLs remain deliberately behind that boundary.

## What is now established

- 33 deterministic frontend scenarios cover the renderer plus shortcode,
  dynamic block, content-filter, and footer entrypoints.
- The settings fixture freezes the option name, 29 submitted fields, defaults,
  sanitizer output, nested shapes, and runtime X alias behavior.
- The WordPress surface fixture freezes shortcode, widget, block, builder,
  settings, metabox, AJAX, asset-handle, and persisted identifier values.
- JavaScript source is organized as 15 build-time modules. WordPress loads
  only `build/admin-react.js`, `build/social-share.js`, and
  `build/vc-scripts.js`.
- The new PHP namespace is Composer/PSR-4 autoloaded and contains settings
  value objects, seven explicit network definitions, four explicit icon-set
  manifests, validated registries, and migration infrastructure.
- Legacy settings, X CSS/file naming, and the old long-shadow directory name
  are mapped only under `Compatibility/Legacy`.
- The new-core boundary check rejects legacy prefixes outside the compatibility
  module.
- The release archive now stages a symlink-free tree before invoking
  `wp dist-archive`, includes the optimized Composer loader and PHP source, and
  excludes JavaScript source and development files.
- No custom database table or rewrite-owned option exists. No data-copy
  migration is required or registered at this stage.
- The root entry file now contains only the plugin header, Composer bootstrap,
  new runtime boot, and one compatibility bootstrap include.
- Fifteen historical root PHP paths are implementation-free shims into
  `Compatibility/Legacy`, preventing direct-include breakage without restoring
  old code outside that module.
- The settings option is read and written only by `OptionSettingsRepository`.
  The historical settings class is now a facade over separate settings,
  asset-payload, content-lookup, and AJAX services.
- Block, WPBakery, widget, metabox, and Elementor stored-data and render
  contracts are committed. Elementor controls, defaults, empty selection, and
  rendered output now run under a deterministic stubbed integration contract.
- The Sandbox wrapper now requires PHPUnit completion markers, so an install
  or bootstrap failure cannot exit zero as a false-green test run.
- Canonical `assets/iconsets/<id>/<shape>/<network>` files now exist. Tests
  verify every manifest asset, byte parity with 2.2.6, canonical paths, and
  basic SVG active-content exclusions.
- Built-in historical icon-set objects are now hydrated from the canonical
  registry. Compatibility supplies only old labels, CSS classes, filenames,
  public properties, and URLs; add-on icon sets still use the historical
  registration bridge.
- `PluginFactory` builds the canonical production service graph. Content
  exclusion, content/floating placement, translation loading, settings request
  sanitization, registries, migrations, and canonical asset validation are
  owned by the new graph; the legacy runtime supplies storage/public adapters.

## Compatibility gates completed

### Public and persisted surface

The committed contracts now freeze malformed and mixed-type builder inputs,
all unconditional hook registrations plus the conditional content/admin-load
hooks, render-time filter order, legacy constants/functions/globals/classes/
methods/properties/signatures, frontend asset collection, AJAX response shapes,
network-local settings, translations, and representative upgrade and rollback
data. `tests/fixtures/legacy-public-api-baseline.json` is the machine-readable
public API inventory.

### Canonical settings request cutover

The new graph owns frontend button construction, registry adaptation, URL
resolution, a canonical settings schema, and a WordPress request sanitizer.
All plugin option access now runs through the new repository, and the historical
controller is a small facade over modular services. Classic and AJAX saves now
map the old request into `SettingsRequestSanitizer`, then map the validated
value back to the exact historical submitted shape. Custom icon sets, shapes,
networks, placement extensions, and unknown truthy keys remain preserved by
the compatibility mapper.

## Release blockers

### P0 — canonical icon asset release evidence is incomplete

The canonical tree and resolver are populated, byte-verified, and used in
production to validate every built-in asset before compatibility objects are
hydrated. Public frontend URLs still use the old tree so output remains
unchanged. Licensing/source records and a complete enqueue/URL contract are
required before switching those URLs.

Dimensions, SVG structure, active-content exclusions, and byte parity are
covered. The remaining blocker is source/license provenance for the
pre-existing PNG packs. Browser visual parity and an approved URL transition
are also required before public URLs move to the canonical tree.

## High-priority engineering gaps

### P1 — settings boundary

- `SettingsSchema` and `SettingsRequestSanitizer` now own the canonical field
  vocabulary and types.
- `OptionSettingsRepository` is now the only settings caller of
  `get_option()`/`update_option()`.
- Absent-option defaults and partial saved arrays are distinguished.
- Unknown-key, malformed-array, boolean, and option-autoload behavior are now
  frozen; new options autoload as before and existing non-autoload decisions
  remain unchanged.
- A representative 2.2.6 option round-trips byte-for-byte without activation
  mutation, schema-version writes, or data migration.

### P1 — renderer boundary

- Canonical URL context and rendering are implemented; WordPress filters and
  historical HTML remain isolated in compatibility.
- Hook-order tests now ensure template and placeholder filters interleave per
  rendered button and do not fire for missing/unsupported icons.
- Direct-renderer malformed keys, custom wrapper/icon classes, invalid
  unrelated icon sets, empty external templates, and settings state
  transitions have explicit compatibility tests.
- Bluesky `%0A`, analytics inline-script quirks, attribute order, and
  historical CSS classes are explicitly preserved in
  `REWRITE-COMPATIBILITY-DECISIONS.md`.
- Asset collection is separate from HTML construction and de-duplicates
  frontend style handles and inline icon CSS.

### P1 — characterized defects requiring an explicit compatibility decision

- Widget saves store selected networks as a numeric list while the renderer
  expects associative keys, producing an empty button wrapper after a normal
  widget save. This is now frozen by a contract and should be fixed only with
  an upgrade/rollback decision.
- The AJAX settings handler previously sanitized the complete URL-encoded form
  before parsing, which discarded every field after the first separator. It
  now parses first and applies the existing field sanitizer; the full saved
  schema is covered by an AJAX integration test.
- Icon-set AJAX handlers used raw `die()`, terminating the PHPUnit process and
  allowing false-positive test runs. They now terminate through WordPress while
  preserving their response bodies.
- WPBakery previously loaded its admin bundle through two paths and assumed an
  AJAX string response. It now enqueues once, uses the existing nonce, accepts
  WordPress string or parsed-object responses, and renders labels with DOM text
  nodes.
- Canonical extension hooks now own `hssb/networks`, `hssb/icon_sets`,
  `hssb/share_templates`, `hssb/share_template`, `hssb/share_title`,
  `hssb/share_url`, and `hssb/settings_schema`. Compatibility runs the matching
  legacy share hooks once, after the canonical hook, with recursion protection.

### P1 — JavaScript decomposition

Runtime modularity is correct: 15 source modules are bundled at build time and
no module loader ships to WordPress. Exclusion search, template editing,
modal/focus behavior, rendering, and mounting are isolated. The application
controller is now 318 lines and the presentation renderer is 391 lines; further
section-level splitting is optional unless either module grows again.

The splits should follow behavior boundaries and keep one stable bundle
entrypoint.

### P1 — migration lifecycle

The migration runner is intentionally empty and therefore writes no schema
version. Before the first real migration:

- register activation/network-activation behavior;
- make each step resumable and idempotent;
- define failure reporting and rollback behavior;
- test multisite batching and interrupted upgrades;
- never rewrite the existing option merely to normalize it.

## Verification and release gaps

- CI is configured to syntax-check PHP 7.0–8.5, smoke-test the minimum
  WordPress 5.3/PHP 7.0 pair, and test WordPress 6.8/latest on PHP 8.3; the
  updated remote workflow has not run yet.
- CI runs the PHPUnit regular, AJAX, and multisite suites on WordPress 6.8 and
  latest. WordPress 5.3 remains an activation/shortcode smoke because its old
  test library is incompatible with the PHPUnit 9 harness.
- CI builds, inspects, installs, and activates the release ZIP, but the updated
  remote workflow has not run yet.
- Builder browser tests skip when Elementor or WPBakery is unavailable; the
  settings accessibility test passes in the healthy Sandbox instance.
- The fresh-instance E2E orchestrator currently fails on a stale Docker network;
  this is test infrastructure state, not a plugin failure.
- The healthy cached Sandbox instance passes 108 regular tests/1,382 assertions
  (one intentional multisite skip), seven AJAX tests/25 assertions, and the
  multisite contract/7 assertions. The strict frontend CLI independently
  passes all 33 scenarios.
- Plugin Check reports warnings only, all from the legacy global surface that
  the compatibility phase is designed to isolate.
- A clean-checkout build and byte-for-byte reproducibility check remain.
- PHPStan/PHPCS and a committed warning baseline for the new namespace remain.
- The package header and archive name still say `2.2.6`; a release-candidate
  version must be selected before publication.

## Product decisions still required before 3.0

- Confirm WordPress 5.3 and PHP 7.0 remain the actual support floor.
- Confirm exact bug compatibility versus approved fixes for known output
  quirks; the currently implemented corrections are listed as pending sign-off
  in `REWRITE-COMPATIBILITY-DECISIONS.md`.
- Confirm the legacy API remains supported throughout 3.x and the planned
  deprecation/removal policy for 4.0.
- Confirm whether third-party icon sets register through only the new filter or
  also through a documented manifest API.
- Define upgrade notices, rollback instructions, and the release-candidate
  soak period.

## Completion definition

The rewrite is complete only when production boots the new service graph, all
business behavior lives under the new namespace, all old symbols and mappings
live under `Compatibility/Legacy`, every persisted/public surface has a
contract, the actual ZIP passes the supported matrix, and rollback to 2.2.6
preserves user data.
