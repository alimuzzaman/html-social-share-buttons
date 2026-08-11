# Rewrite compatibility decisions

These decisions identify the preserved 2.2.6 surfaces and the approved
canonical-first rewrite. They describe implementation and contract intent, not
release approval or completed operational evidence. See
`RELEASE-CANDIDATE-VALIDATION.md` for the evidence ledger.

## Canonical architecture and compatibility boundary

- `Bootstrap\\PluginFactory` builds one canonical kernel: configuration,
  settings repository, network and icon-set registries, rendering facade,
  presentation controllers, and one hook registrar. No compatibility runtime
  is booted alongside it.
- `Compatibility/Legacy/Api` is the only retained compatibility boundary. It
  loads public legacy symbols before factory construction where necessary,
  injects third-party icon sets before schema construction, and after boot
  delegates legacy globals, classes, hooks, constants, and value adapters to
  the canonical services. It does not own factories, request-time rendering,
  option access, controller hooks, or a parallel service graph.
- Root historical PHP forwarding files and the old `LegacyRuntime`/global
  implementation files are intentionally gone. This is a structural rewrite,
  not a relocation of the old plugin below `Compatibility/Legacy`.
- `PluginConfig` contains the retained external identifiers and maps them into
  canonical consumers: `zm_shbt_fld`, `_zm_sh_disable_share`, both shortcode
  tags, handles, admin actions, builder IDs, and wrapper classes. Extensions
  continue to receive the existing values rather than new storage names.

## Preserved public and persisted surfaces

- The `zm_shbt_fld` option name, submitted field shapes, defaults, unknown
  extension keys, existing autoload behavior, and `_zm_sh_disable_share` post
  meta remain the persistence contract.
- Historical constants, globals, functions, classes, interfaces, public
  methods/properties, hooks, asset handles, builder identifiers, stored
  builder representations, shortcode aliases, and documented HTML classes
  remain callable through thin compatibility delegates.
- Historical output details retained by the canonical renderer include CSS
  classes, filter ordering, the Bluesky `%0A` template suffix, analytics
  inline-script behavior, and profile-link inheritance. Candidate output
  contracts still need execution before byte compatibility is claimed.
- Historical icon IDs/URLs remain externally visible, including the
  `long_shadow` directory and `twitter` filename/CSS naming. Manifest metadata
  resolves those values to the retained released icon-pack trees.

## Approved compatibility-safe corrections

- Widget numeric-list instances normalize at render time to the associative
  selection shape used by canonical rendering; saves preserve the historical
  storage form.
- AJAX settings input is parsed before field sanitation, preventing the old
  truncation after the first URL-encoded separator while retaining the response
  shape and WordPress termination behavior.
- WPBakery enqueues its compiled editor bundle once, localizes its nonce once,
  accepts JSON-string or parsed icon-set responses, and inserts labels as text.
  This does not substitute for a licensed WPBakery editor fixture.
- Mixed or malformed shortcode, block, Elementor, and WPBakery attributes
  normalize safely. Valid persisted values retain their established renderer
  inputs and markup contract.

## Renderer and URL-resolution decision

- `Presentation\\Rendering\\RenderFacade` is the server-side rendering
  boundary for automatic placement, public PHP, shortcode, blocks, widget,
  Elementor, and WPBakery. It maps the established adapter shapes into a
  canonical render request; JavaScript does not generate frontend markup.
- An omitted URL and recognised historical placeholder forms (including
  encoded variants) use the current post permalink. Resolution considers a
  dynamic block context post ID first, then loop/global post, queried post,
  documented AJAX request IDs, then the historical current-page fallback.
- The URL is decoded only to recognise a legacy placeholder and then supplied
  once to template expansion/escaping. Share links must not emit
  `%%permalink%%`, `%25%25permalink%25%25`, a placeholder URL, or a
  double-encoded permalink. Literal custom URLs remain explicit URLs.
- Both `[zm_sh_btn]` and `[html-social-share-buttons]` call the canonical
  shortcode controller. The historic global callback remains a compatibility
  bridge; blocks deliberately do not depend on it.

## Dynamic block decision

- Root `block.json` is canonical metadata for the existing
  `html-social-share/social-share` block and retains its name,
  attributes/defaults, text domain, editor handle, and context.
- `blocks/social-links/block.json` defines the separate
  `html-social-share/social-links` block for inherited or custom profile links.
  It does not alter stored share-block content.
- The server registers both metadata files using WordPress’s metadata API when
  available and reads equivalent metadata for the WordPress 5.3 fallback. Both
  are dynamic: the editor imports the metadata and `save()` returns `null`.
- PHP render callbacks send normalized attributes directly to the canonical
  facade. Existing block empty-selection behavior, defaults, classes, profile
  inheritance, filters, and frontend HTML remain the compatibility objective.
  Editor preview is local-only and not browser-parity evidence.

## Assets, icon metadata, and third-party extensions

- `resources/iconsets/` contains canonical PHP manifests, not duplicate packs.
  Each manifest supplies the stable ID, label, supported shapes, filename map,
  stylesheet/preview names, and the physical `asset_path` used by the resolver.
- Historical PNG/SVG trees remain under `iconset/default`, `iconset/flat`,
  `iconset/long_shadow`, and `iconset/prajin`. Duplicate copies formerly under
  `assets/iconsets/` were removed. New Bootstrap and Tabler vector assets,
  generator records, and licence material remain in their dedicated assets
  directories.
- The existing `zm_sh_add_iconset` route is retained. Legacy add-ons are
  converted into canonical registries before schema construction, and their
  external filesystem/public asset locations are validated together. A new
  public manifest API is intentionally deferred.
- Retaining a historical asset is not provenance clearance. See
  `ICON-COVERAGE-MATRIX.md` and `resources/iconsets/ASSET-SOURCES.md`.

## Autoloading, distribution, and localization

- A source checkout without readable `vendor/autoload.php` stops before any
  runtime service is created, fails activation with remediation guidance, and
  reports single-site/network admin notices for an incomplete active checkout.
- Before `pnpm run zip`, the release owner prepares Composer with
  `--no-dev --prefer-dist --no-interaction --classmap-authoritative`. The `zip`
  script refuses to package a development or non-authoritative autoloader,
  builds editor bundles, and creates a deterministic, symlink-free archive. The
  distribution contract requires the production Composer maps, PHP sources,
  block metadata, built bundles, and required assets. A repeatable archive and
  fresh installation are still evidence gates.
- Settings/profile/template/modal/status PHP payload strings use the plugin
  text domain. Block/admin/builder JavaScript uses `wp.i18n` or translated PHP
  payload keys; block scripts register translations when supported. Static
  localization coverage does not prove every translated catalog or visual
  language flow.

## Storage, migration, rollback, and release policy

- No custom table, replacement option, rewrite schema-version option, or
  migration step is introduced. Canonical values normalize in memory and saves
  map to the historical option shape.
- The intended rollback target is 2.2.6, which should read unchanged option
  and post-meta data without reverse migration. That remains a design claim
  until a documented rehearsal passes.
- The declared floor remains WordPress 5.3 and PHP 7.0. `Readme.txt` declares
  testing through WordPress 7.0; this is not browser/builder certification.
- Historical PNG provenance, browser parity, a real Elementor fixture, a
  licensed WPBakery fixture, rollback, and soak evidence remain open. Plugin
  Check API-v1 findings required for the 5.3 floor also need explicit
  disposition.
- `3.0.0-rc.1` is only a future candidate label. Header version and stable tag
  remain 2.2.6 until every required gate is evidenced and a release owner
  authorizes the change.
