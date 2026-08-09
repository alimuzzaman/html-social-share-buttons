# Rewrite gap review after 2.2.6

## Executive status

The latest published baseline is `v2.2.6` at commit `620f1ae66`. The current
branch and working tree contain the accessibility, release-verification, and
rewrite work described here after that tag.

The architectural rewrite is implementation-complete: production boots the
new service graph, which owns share-button construction and keeps exact
public output through a compatibility renderer. Historical globals and
WordPress adapters are isolated under `Compatibility/Legacy`; settings and
legacy asset URLs remain deliberately behind that boundary. Release hardening
is still in progress, so this is not yet a published 3.0 release.

## What is now established

- 33 deterministic frontend scenarios cover the renderer plus shortcode,
  dynamic block, content-filter, and footer entrypoints.
- The settings fixture freezes the option name, 36 submitted fields, defaults,
  sanitizer output, nested shapes, and runtime X alias behavior.
- The WordPress surface fixture freezes shortcode, widget, block, builder,
  settings, metabox, AJAX, asset-handle, and persisted identifier values.
- JavaScript source is organized as 16 build-time modules. WordPress loads
  only `build/admin-react.js`, `build/social-share.js`, and
  `build/vc-scripts.js`.
- The new PHP namespace is Composer/PSR-4 autoloaded and contains settings
  value objects, seven explicit network definitions, six explicit icon-set
  manifests, validated registries, and migration infrastructure.
- Legacy settings, X CSS/file naming, and the old long-shadow directory name
  are mapped only under `Compatibility/Legacy`.
- The new-core boundary check rejects legacy prefixes outside the compatibility
  module.
- The release archive now stages a symlink-free tree before invoking
  `wp dist-archive`, includes the optimized Composer loader and PHP source, and
  excludes JavaScript source and development files. Staged timestamps are
  normalized, and CI rejects byte-level differences between consecutive builds.
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
- Optional global profile/contact destinations are modeled separately from
  share actions. Compatibility maps their persisted field names and renders
  accessible profile anchors after the share buttons without changing output
  when no profiles are configured. Shortcode, block, widget, Elementor, and
  direct PHP renders inherit those global destinations.
- Bootstrap Solid and Tabler Outline are complete, generated SVG sets covering
  all seven current networks in both square and circle shapes. Their pinned
  MIT-licensed inputs, checksums, distributed licenses, and deterministic
  generator are part of the repository.
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

Dimensions, SVG structure, active-content exclusions, and historical byte
parity are covered. The new generated SVG sets are cleared, but the remaining
blocker is source/license provenance for the pre-existing PNG packs. A release
decision must either remove/replace those packs with cleared SVGs or obtain a
redistribution-compatible license. Browser visual parity and an approved URL
transition are also required before historical public URLs move.

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

- Widget saves now store the associative selection shape consumed by the
  renderer. Render-time normalization preserves existing numeric-list widget
  instances. Both paths have regression coverage.
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

Runtime modularity is correct: 16 source modules are bundled at build time and
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

- CI changes are intentionally out of scope for this cycle. Local Sandbox and
  archive checks are the active gates.
- A fresh Sandbox instance passes 116 regular tests/2,009 assertions, seven
  AJAX tests/25 assertions, and the multisite contract/seven assertions.
- The isolated fresh-instance Playwright worker passes. The settings keyboard
  accessibility scenario runs; Elementor fixture-dependent cases and
  WPBakery-unavailable cases remain conditional.
- Plugin Check reports no errors. Remaining warnings are compatibility names,
  the intentionally staged legacy settings sanitizer, and WordPress's modern
  translation-loading advisory.
- The release build produces a valid 311-file, 773 KB archive and passes the
  distribution contract.
- PHPStan level 5, WordPress-Core PHPCS rules, and a committed clean baseline
  cover the rewritten namespace. PSR-4 file names and camelCase domain APIs are
  explicitly excluded from WordPress's procedural naming sniffs.
- The package header and archive name still say `2.2.6`; a release-candidate
  version must be selected before publication.

Still unfinished before a public 3.0 release:

- source and redistribution evidence for the retained historical PNG packs;
- manual cross-browser visual parity for all historical and generated icon
  sets;
- real Elementor and WPBakery fixture coverage rather than conditional skips;
- the complete JavaScript localization pass;
- `block.json` registration and a direct canonical dynamic-block renderer;
- a 14-day staging soak, rollback rehearsal, and final version/readme alignment.

## Product decisions recorded for 3.0

- WordPress 5.3 and PHP 7.0 remain the support floor.
- The compatibility-safe defect corrections in
  `REWRITE-COMPATIBILITY-DECISIONS.md`, including the widget fix, are approved.
- The legacy API remains supported throughout 3.x; removal is considered only
  for 4.0 after a documented deprecation period and usage audit.
- Third-party icon sets keep both the legacy bridge and canonical filter. A
  documented public manifest API is deferred.
- The target is 3.0.0, beginning with an internal 3.0.0-rc.1 and a 14-day
  staging soak. Rollback is to 2.2.6 with no reverse data migration.

## Next implementation plan

1. Finish the local regular, AJAX, multisite, browser, Plugin Check, and archive
   gates; do not add CI in this cycle.
2. Register the existing dynamic block from `block.json` and call the canonical
   renderer directly instead of routing through the shortcode callback. Keep
   JavaScript responsible for editor controls and preview, not frontend HTML.
3. Record source, version, checksum, license, and brand-guideline metadata for
   each additional vector icon. Keep the historical PNG packs while their
   provenance is documented.
4. Complete WordPress.org listing copy, screenshots, FAQ, and the accompanying
   release article before changing the version to 3.0.0-rc.1.
5. Run the 14-day staging soak, verify rollback to 2.2.6, then make the final
   publish decision.

## Completion definition

The rewrite is complete only when production boots the new service graph, all
business behavior lives under the new namespace, all old symbols and mappings
live under `Compatibility/Legacy`, every persisted/public surface has a
contract, the actual ZIP passes the supported matrix, and rollback to 2.2.6
preserves user data.
