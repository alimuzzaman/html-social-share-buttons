# Ground-up rewrite architecture and compatibility plan

## Status

Active phased rewrite after 2.2.6. Rendering and settings storage access have
crossed the production boundary; canonical asset URLs have not.

Current checkpoint:

- Phase 0 has deterministic settings and frontend fixtures, including 33
  renderer/public-entrypoint scenarios.
- Phase 1 has Composer/PSR-4 loading, settings value objects, explicit network
  and icon-set registries, legacy mappers, and an idempotent migration runner.
- JavaScript runs only from three compiled bundles; source is split into 15
  build-time modules and excluded from the distribution.
- Production rendering now runs through the new application/domain pipeline.
  Exact historical markup, hooks, CSS classes, footer asset collection, and
  public global symbols are supplied by adapters under
  `Compatibility/Legacy`.
- Shortcode, block, widget, metabox, Elementor, WPBakery, settings UI, icon-set
  globals, and the main runtime facade have been isolated under
  `Compatibility/Legacy`; the root plugin file is bootstrap-only.
- Historical root PHP include paths remain as implementation-free shims into
  `Compatibility/Legacy`; they are checked as a 15-path contract.
- Settings reads and writes are centralized in `OptionSettingsRepository`.
  The historical settings class delegates assets, content lookup, AJAX, and
  sanitization to separate namespaced modules.
- `PluginFactory` now builds the canonical service graph. The production
  compatibility layer supplies the historical option codec and public symbols,
  while canonical services own settings sanitization, exclusion, placement,
  translation loading, registries, migrations, and asset validation.
- Canonical extension hooks are defined in `ExtensionHooks`; registry/schema
  hooks are typed, share hooks run canonical-before-legacy through one guarded
  compatibility bridge, and old hook names do not appear in the new core.
- Canonical path-normalized icon assets and a resolver now exist and are
  byte-compared with the released assets, but production URLs intentionally
  remain on the historical tree until the asset contract is complete.
- Built-in legacy icon-set objects are hydrated from the canonical manifest
  registry; only old public property values and asset names are mapped in
  compatibility. Third-party legacy registrations remain supported.
- No data migration is registered. The plugin has no custom tables and the
  first rewritten release will preserve the existing option in place.

The rewrite may replace every production implementation detail, but it must not
silently change the public frontend output or the persisted settings contract.
The current plugin is treated as the executable specification until a behavior
is explicitly reclassified as a bug or an intentionally breaking change.

## Goals

1. Build a new, modular implementation using new names and explicit
   dependencies.
2. Preserve frontend rendering and the `zm_shbt_fld` settings contract.
3. Put every legacy constant, global, function, class, interface, hook, option
   adapter, and alias in one `Compatibility/Legacy` module.
4. Replace implicit globals and filesystem discovery with explicit registries.
5. Make icon sets and social networks independently extensible and testable.
6. Preserve supported WordPress, PHP, shortcode, widget, block, Elementor, and
   WPBakery behavior through contract tests.
7. Make rollback possible at every migration phase.

## Non-goals

- No visual redesign during the rewrite.
- No database schema migration in the first rewritten release.
- No cleanup of strange historical output unless approved separately.
- No removal of a public hook or symbol merely because repository search shows
  no internal use.
- No speculative framework, container, trait, or abstraction.
- No telemetry or remote calls.

## Working compatibility decisions

These are the safe defaults until explicitly changed:

| Decision | Working default |
|---|---|
| Release line | Ship the completed rewrite as 3.0.0, not a 2.2.x patch |
| Minimum WordPress | Preserve 5.3 |
| Minimum PHP | Preserve 7.0 |
| Saved option | Continue using `zm_shbt_fld` unchanged |
| Frontend HTML | Exact output compatibility, including ordering and escaping |
| Legacy API lifetime | Keep for the complete 3.x line; review removal for 4.0 |
| Legacy deprecations | No runtime notices in 3.0; document them first |
| Data migration | Normalize legacy values in memory; do not rewrite saved data |
| Rollout | Staged replacement behind contract tests, not a one-shot switch |

Raising the PHP baseline would materially improve the type system available to
the new code. That must be a deliberate product decision because PHP 7.0 rules
out typed properties, union types, attributes, enums, and readonly objects.

## Compatibility contracts

### Frontend rendering

The golden master must cover:

- HTML element order, class names, attribute order, quote style, and newlines;
- selected-network order and the historical `twitter` CSS class for X;
- share URL templates, placeholder resolution, encoding, and escaping;
- `target`, `rel`, `nofollow`, title, wrapper, and placement behavior;
- default, flat, long-shadow, and Prajin icon sets and their supported shapes;
- left, right, before-content, after-content, shortcode, widget, and block
  contexts;
- empty, malformed, unknown, legacy, and partial inputs;
- content exclusion and `_zm_sh_disable_share` behavior;
- stylesheet handles, inline icon CSS, asset URLs, and footer output;
- filters that modify title, templates, placeholders, and widget titles.

The committed frontend fixture currently contains 33 deterministic scenarios,
including the shortcode, block, content-filter, and footer entrypoints.
Any fixture update requires an explanation of why the public contract changed.

### Settings storage

The settings schema fixture must remain authoritative for:

- option name and autoload behavior;
- exact default keys and scalar/array shapes;
- field names posted by the settings page;
- truthy, falsey, omitted, unknown, and malformed value behavior;
- placement and icon maps;
- share-template keys and sanitization;
- the stored `twitter` value and runtime mapping to `x`;
- AJAX nonce, capability, status code, and response shape;
- classic `options.php` registration and save behavior.

The first rewritten release reads the existing option and converts it into a
new runtime value object. It writes the same legacy array shape back to the same
option. A new database shape is a separate future migration.

### Public WordPress surface

Before replacing a module, freeze its observable API:

- plugin header, slug, text domain, translation catalogs, and action links;
- shortcode name and attributes;
- widget ID, class, fields, saved instance shape, and output;
- block name, attributes, defaults, script handles, and render callback;
- Elementor widget name, controls, defaults, and output;
- WPBakery element name, parameters, and output;
- post-meta key, nonce, capability, and save semantics;
- admin menu slug, AJAX action names, nonce action, and JSON payloads;
- script/style handles, dependencies, versions, and localized object names;
- cron events, REST routes, rewrite rules, or scheduled data if added later;
- activation, deactivation, uninstall, multisite, and network-activation
  behavior;
- PHP constants, globals, functions, classes, interfaces, and public methods;
- action/filter names, priorities, accepted argument counts, and return shapes.

Repository search is only the starting point. Third-party themes and plugins
may use public symbols that never appear in this repository.

## Target package structure

```text
html-social-share-buttons.php
src/
  Bootstrap/
    Plugin.php
    ServiceProvider.php
  Domain/
    Network/
      Network.php
      NetworkId.php
      NetworkRegistry.php
      ShareUrlTemplate.php
    IconSet/
      Icon.php
      IconSet.php
      IconSetId.php
      IconSetRegistry.php
    Settings/
      Settings.php
      SettingsDefaults.php
      SettingsSchema.php
      SettingsSanitizer.php
    Rendering/
      RenderContext.php
      RenderRequest.php
      RenderResult.php
      Placement.php
  Application/
    RenderShareButtons.php
    ResolveShareUrl.php
    ResolveExcludedContent.php
  Infrastructure/
    WordPress/
      HookRegistrar.php
      OptionSettingsRepository.php
      WordPressUrlContext.php
      AssetManager.php
      TranslationLoader.php
  Presentation/
    Frontend/
      HtmlRenderer.php
      FooterRenderer.php
      ContentInjector.php
    Admin/
      SettingsController.php
      SettingsAssets.php
      Ajax/
    Integration/
      Shortcode/
      Widget/
      Block/
      Elementor/
      WPBakery/
      MetaBox/
  Compatibility/
    Legacy/
      LegacyConstants.php
      LegacyGlobals.php
      LegacyFunctions.php
      LegacyClasses.php
      LegacyHooks.php
      LegacyIconSetAdapter.php
      LegacySettingsAdapter.php
resources/
  iconsets/
    default/manifest.php
    flat/manifest.php
    long-shadows/manifest.php
    prajin/manifest.php
assets/
  iconsets/
    <iconset>/<shape>/<network>.<ext>
tests/
  Unit/
  Contract/
  Integration/
  EndToEnd/
```

The exact namespace should be chosen once and never changed casually.
`Alimuzzaman\HtmlSocialShareButtons` is the current recommendation. Production
global functions should be avoided; WordPress callbacks can use service
methods. Traits should be used only for genuinely shared implementation, with
composition preferred.

## Module responsibilities

### Bootstrap

- Define only new bootstrap constants.
- Load Composer's optimized PSR-4 autoloader.
- Build the small service graph.
- Register WordPress hooks through explicit registrars.
- Boot the compatibility module after the new services exist.
- Avoid executing integration logic merely by including a PHP file.

### Settings

- `SettingsSchema` owns known keys, defaults, types, and normalization rules.
- `SettingsSanitizer` converts request data into a validated runtime value.
- `OptionSettingsRepository` is the only module that calls `get_option()` or
  `update_option()` for plugin settings.
- `LegacySettingsAdapter` converts between the new runtime value and the exact
  persisted legacy array.
- Admin UI, renderer, and integrations receive `Settings`; they do not read
  the option or globals.

### Networks

A network definition owns:

- stable ID and label;
- CSS class;
- default share URL template;
- supported placeholders;
- default enabled state;
- optional privacy or capability metadata.

Network definitions do not own icon-set image paths. This prevents URL behavior
from being duplicated across visual themes.

### Icon sets

Each built-in icon set has an explicit manifest:

```php
return array(
    'id'         => 'flat',
    'label'      => 'Flat',
    'stylesheet' => 'style.css',
    'preview'    => 'preview.png',
    'shapes'     => array( 'square', 'circle' ),
    'icons'      => array(
        'facebook' => 'Facebook.png',
        'x'        => 'Twitter.png',
    ),
);
```

Rules:

- no `scandir()` class discovery;
- no class name derived from a directory name;
- no network share URLs inside icon-set manifests;
- one canonical icon-set ID (`long-shadows`, not a directory/class variation);
- registry validation for IDs, shapes, files, and network references;
- predictable theme/add-on registration through a new filter;
- legacy icon-set objects translated only by `LegacyIconSetAdapter`;
- asset existence, case sensitivity, SVG safety, dimensions, and licensing
  checked in tests.

### Renderer

`RenderShareButtons` accepts a `RenderRequest` and returns a `RenderResult`.
Only `HtmlRenderer` constructs frontend HTML. It receives already resolved
networks, templates, URLs, icon metadata, title, image, placement, and rel
tokens.

This separation allows:

- exact golden-master comparison;
- URL and placeholder unit tests without HTML;
- output escaping tests at one boundary;
- builder integrations to share the same application service;
- no reads from globals, request data, the database, or WordPress templates in
  the domain renderer.

### Integrations

Shortcode, widget, block, Elementor, WPBakery, metabox, content injection, and
footer output are separate adapters. Each adapter:

1. receives WordPress input;
2. sanitizes and maps it to a `RenderRequest`;
3. calls the application service;
4. returns or echoes according to the existing contract.

They must not implement separate network, icon-set, or rendering rules.

## Compatibility module design

The compatibility module is an anti-corruption layer, not a second core.

### New-to-legacy mapping

- New services are registered first.
- Legacy global functions delegate to those services.
- Legacy wrapper classes delegate to or adapt new classes.
- Legacy constants point to new canonical path/URL values.
- Required globals expose compatibility facades or legacy-shaped snapshots.
- Legacy hooks receive legacy argument and return shapes.
- Old icon-set objects are translated into validated new definitions.

### Hook bridging

Maintain a mapping table containing:

- new hook;
- legacy hook;
- direction of propagation;
- priority;
- accepted argument count;
- input adapter;
- output adapter;
- recursion guard.

Never fire old and new hooks from both sides without a recursion guard. Prefer
one canonical new hook with a single compatibility bridge.

### Boundary enforcement

Add a build test that scans all new production files outside
`src/Compatibility/Legacy` and fails on:

- `zm_sh`;
- the old option name;
- old shortcode/widget/block identifiers when not represented by a neutral
  value object;
- old hook names;
- direct inclusion of old PHP files;
- access to legacy globals.

The compatibility module may depend on the new core. The new core may never
depend on the compatibility module.

## Rewrite phases and gates

### Phase 0 — freeze behavior

- Complete frontend golden master.
- Complete settings schema fixture.
- Inventory public symbols, hooks, handles, entry points, and storage.
- Add malformed-input, security, multisite, and builder coverage.

Gate: all current tests pass against 2.2.6/2.2.7 behavior.

### Phase 1 — scaffold the new core

- Add namespace/autoloading and bootstrap.
- Add settings, network, icon-set, and render value objects.
- Add unit tests.
- Do not route production behavior to new code.

Gate: distribution archive includes the autoloader and new tests pass across
the supported PHP matrix.

### Phase 2 — settings and registry cutover

- Read the existing option through the new repository and legacy adapter.
- Introduce explicit network and icon-set registries.
- Keep old renderer and integrations temporarily.

Gate: settings schema, admin save, AJAX, icon-set inventory, and generated code
remain identical.

### Phase 3 — renderer cutover (complete)

- Implement URL context, template resolution, and HTML renderer.
- Route one integration at a time to the new renderer.

Gate: zero golden-master differences for every migrated integration.

### Phase 4 — integration cutover (complete)

Recommended order:

1. shortcode;
2. block;
3. widget;
4. content/footer placement;
5. metabox/exclusions;
6. Elementor;
7. WPBakery;
8. settings UI and AJAX.

Gate: each adapter passes its contract before the next one moves.

### Phase 5 — legacy isolation (complete)

- Move all old symbol definitions and bridges into `Compatibility/Legacy`.
- Delete superseded implementation files.
- Enable the forbidden-legacy-reference scan.

Gate: no legacy reference outside the compatibility directory and fixtures.

### Phase 6 — release candidate (in progress)

- Build a clean ZIP from a clean checkout.
- Run PHP, JavaScript, PHPCS, PHPStan, PHPUnit, Playwright, Plugin Check, and
  compatibility matrices.
- Test upgrade from representative 2.x saved data and widget instances.
- Test rollback from the release candidate to 2.2.6.
- Perform accessibility and real-site staging checks.

Gate: human approval of the compatibility report and every intentional diff.

## Required test layers

### Characterization

- exact frontend HTML golden master;
- settings schema and sanitizer fixture;
- generated shortcode/PHP snippets;
- hook call order and accepted arguments;
- public symbol and asset-handle inventory;
- legacy icon-set registration fixtures.

### Unit

- settings normalization;
- placeholder resolution and URL encoding;
- exclusion matching;
- network and icon-set registry validation;
- render-request validation;
- compatibility adapters and recursion guards.

### WordPress integration

- activation, upgrade, deactivation, and uninstall;
- options API and autoload behavior;
- shortcode, widget, block, metabox, content filters, and footer actions;
- AJAX permissions, nonces, error codes, and JSON;
- translations and text domains;
- multisite and network activation;
- PHP 7.0 through supported current PHP;
- WordPress 5.3, 6.8, and latest.

### Browser and builder

- keyboard and screen-reader settings flow;
- save/reload round trip for every settings field;
- block editor insert/edit/save/render;
- Elementor and WPBakery controls and frontend output;
- frontend output with common themes and caching/minification enabled;
- no console errors or unexpected network requests.

### Distribution

- deterministic build;
- committed/generated asset parity;
- ZIP allowlist/denylist;
- Plugin Check;
- no development fixtures, credentials, or source-only dependencies;
- install and activate from the actual ZIP, not the working tree.

## Additional concerns that were missing from the initial outline

1. **Bug compatibility:** exact preservation can freeze bugs. Every discovered
   quirk needs a preserve/fix decision and, if fixed, a separate changelog item.
2. **Third-party API discovery:** repository search cannot reveal external use
   of globals, classes, hooks, or custom icon-set classes.
3. **Widget and block persistence:** options are not the only stored data;
   widget instances, block comments, Elementor documents, WPBakery shortcodes,
   and post meta must round-trip.
4. **Hook timing:** keeping hook names is insufficient if registration or
   execution priority changes.
5. **Asset compatibility:** CSS handles, file names, case, URLs, DOM classes,
   and cache versions can be public contracts.
6. **Translations:** preserve both canonical and legacy text-domain behavior
   while moving strings.
7. **Multisite:** decide network activation, per-site options, and uninstall
   semantics.
8. **Security:** revalidate capabilities, nonces, escaping, URL protocols, SVG
   handling, and generated code at every adapter boundary.
9. **Performance:** set budgets for autoloaded options, frontend queries,
   filesystem access, CSS/JS weight, and hook count.
10. **Privacy:** analytics must remain opt-in and no new telemetry may be added.
11. **Rollback:** the new version must not rewrite data in a form that 2.x
   cannot read.
12. **Release engineering:** test the packaged ZIP, PHP matrix, dependency
   trust, reproducible build, and WordPress.org deployment path.
13. **Support policy:** publish the duration and removal criteria for the
   compatibility layer.
14. **Ownership and licensing:** record icon source, license, attribution, and
   SVG sanitization requirements.
15. **Observability:** use local debug logging only when `WP_DEBUG` is enabled;
   avoid user-facing notices and remote reporting.

## Current characterization findings

These are observations, not proposed fixes:

- The former frontend baseline fixture contained zero scenarios.
- Legacy defaults depend on file-scope globals and can disappear when a loader
  includes the plugin inside a function or closure.
- False-valued entries in the icon map can still render because the renderer
  checks key presence.
- A legacy `twitter` key is preserved in storage and mapped to `x` at runtime.
- An unknown icon-set ID falls back to default icon assets while keeping the
  requested ID in the wrapper CSS class.
- String `"0"` placement values are omitted by the current sanitizer.
- Unknown truthy settings keys are stored as boolean `true`; unknown falsey
  keys are omitted.
- Current Bluesky placeholder encoding removes the intended `%0A` separator in
  the final escaped URL.

The rewrite should initially reproduce these findings. Each correction should
be proposed and tested independently so a clean architecture is not confused
with an unannounced behavior change.

## Definition of done

- Every new production symbol uses the chosen namespace/prefix.
- No legacy reference exists outside the compatibility module and approved
  fixtures.
- Frontend golden-master and settings-schema tests pass unchanged.
- All public integrations and stored data upgrade without manual action.
- Supported PHP/WordPress matrices, static analysis, browser tests, Plugin
  Check, ZIP installation, staging, accessibility, and rollback pass.
- Intentional differences are documented and approved individually.
- The compatibility module has an explicit support and eventual removal policy.
