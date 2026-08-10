# Rewrite compatibility decisions

These decisions identify preserved 2.2.6 surfaces and approved changes in the
rewrite. They describe the current working-tree implementation; they do not
mark a release gate as passed. See `RELEASE-CANDIDATE-VALIDATION.md` for the
evidence ledger.

## Preserved public and persisted surfaces

- The `zm_shbt_fld` option name, submitted field shapes, defaults, unknown
  extension keys, existing autoload behavior, and `_zm_sh_disable_share` post
  meta remain the compatibility storage surface.
- Legacy constants, globals, functions, classes, interfaces, public methods,
  public properties, hooks, asset handles, builder identifiers, and stored
  builder representations remain available through `Compatibility/Legacy`.
- Compatibility continues to emit historical icon URLs and IDs, including the
  `long_shadow` directory and `twitter` filename/CSS naming, while canonical
  assets remain behind the registry boundary.
- Historical output details retained by the renderer include legacy classes,
  filter order, Bluesky’s `%0A` template suffix, and the analytics inline
  script behavior. They require the existing output contracts to be re-run for
  this candidate before they can be called byte-compatible in a release.

## Approved compatibility-safe corrections

- Widget saves use the associative network-selection shape consumed by the
  renderer. Numeric-list legacy widget instances are normalized at render time.
- The AJAX settings request is parsed before the legacy field sanitizer runs,
  avoiding the historical loss of fields after the first URL-encoded separator.
  Response shape remains the legacy response shape.
- AJAX handlers use WordPress response termination rather than raw `die()`.
- WPBakery enqueues its compiled admin bundle once, localizes its nonce once,
  accepts a JSON string or already-parsed response, and inserts labels as text.
  This is code/contract evidence only; no commercial WPBakery editor has been
  verified for the candidate.
- Malformed mixed-type shortcode, block, Elementor, and WPBakery-compatible
  attributes normalize to safe empty/default values. Valid persisted values are
  intended to retain their prior rendered output.

## Dynamic block decision

- `block.json` is the source of truth for the existing block name,
  attributes/defaults, editor handle, text domain, and declared context.
- Server registration reads that metadata, using the WordPress metadata API
  where available and a metadata-reading fallback for the WordPress 5.3 floor.
- The editor imports the same metadata and `save()` returns `null`.
- The server render callback sends normalized stored attributes directly to the
  canonical render facade. It deliberately does not route through the
  shortcode callback. The editor’s local preview is not evidence of frontend
  browser parity.

## Canonical share URL decision

An omitted URL, and the recognized historical permalink placeholder forms,
resolve through `CurrentPostPermalink`: block context post ID first, then the
current loop/global post, queried `WP_Post`, and AJAX request post IDs before
the legacy current-page fallback. The facade provides the resolved URL to the
share-template resolver, which encodes placeholder values once. Literal or
double-encoded permalink tokens are not supported output. The tracked contract
covers shortcode, dynamic block, Elementor-compatible shortcode input,
WPBakery stored shortcode, direct PHP, singular posts/pages, an archive loop,
an explicit URL, and AJAX; execution evidence remains required.

## Autoloading, distribution, and localization

- A source checkout without a readable `vendor/autoload.php` stops before
  runtime boot, fails activation with remediation guidance, and registers
  single-site and network-admin notices for an already active incomplete
  checkout.
- The current `zip` script runs `composer install --no-dev --prefer-dist
  --no-interaction --classmap-authoritative`, builds bundles, and stages a
  symlink-free tree for `wp dist-archive`. Its contract requires the production
  Composer loader/maps and excludes development source and metadata. A built,
  repeatable, freshly installed ZIP is still a release gate, not a completed
  fact in this record.
- Settings/profile/template/modal/status copy comes from the PHP translation
  payload. Block copy uses `wp.i18n`; the block metadata and runtime use the
  `html-social-share-buttons` text domain and register script translations
  where supported. The static localization contract must be run, and catalog
  completeness plus visual translation review remain pending.

## Storage, migration, and rollback

- There is no custom table, replacement option, registered migration step, or
  rewrite schema-version option in the current design.
- Canonical values normalize in memory; saves map to the historical shape.
- The intended rollback target is 2.2.6, which should read the unchanged option
  and post meta without reverse migration. This has not been rehearsed.

## API lifetime and release policy

- The declared support floor is WordPress 5.3 and PHP 7.0. `Readme.txt`
  declares testing through WordPress 7.0; this is not blanket browser/builder
  certification.
- Third-party icon sets retain the legacy registration bridge and canonical
  filter. A public manifest API remains deferred.
- Historical PNG provenance is unresolved. Their retention is not license
  clearance; see `ICON-COVERAGE-MATRIX.md` and
  `resources/iconsets/ASSET-SOURCES.md`.
- The target may be 3.0.0 with an internal `3.0.0-rc.1`, but the header and
  stable tag stay at 2.2.6 until all candidate gates are evidenced and a
  release owner approves the change.
