# 3.0 release-candidate validation record

This is an evidence ledger, not release approval.  It must not be read as
authority to create a tag, change the stable version, upload to WordPress.org,
publish an article, or deploy an archive.

## Support declaration and evidence scope

The distributed plugin currently declares **WordPress 5.3+** and **PHP 7.0+**
in both the plugin header and `Readme.txt`; Composer also requires PHP 7.0+.
`Readme.txt` declares “Tested up to: 7.0”. These are repository declarations,
not a statement that this candidate has been manually validated in every
intermediate WordPress/PHP/browser combination.

The tracked GitHub workflow defines these automated combinations:

| Scope | Configured combinations | What the workflow does not establish |
|---|---|---|
| PHP syntax/bootstrap | PHP 7.0, 7.4, 8.0, 8.3, 8.5 | Browser or builder-editor behavior |
| WordPress activation | WP 5.3/PHP 7.0; WP 6.8/PHP 8.3; `latest`/PHP 8.3 | Full PHPUnit contracts for the WP 5.3 row |
| WordPress contracts | WP 6.8/PHP 8.3 and `latest`/PHP 8.3 | A real Elementor or WPBakery editor |
| Archive | PHP 8.3 with Node 22, pnpm 11.5.2, Composer, and the deterministic Node archive builder | A staging soak, rollback rehearsal, or cross-browser review |

The following evidence was recorded on 2026-08-11 against the candidate
working tree immediately before its review commit. WordPress execution used
the global Sandbox CLI; PHP quality ran in its PHP 8.3.33 WordPress container;
JavaScript and deterministic packaging ran on the host. This is candidate
evidence, not approval of the declared WordPress 5.3/PHP 7.0 support matrix.

- Regular PHPUnit: 160 tests, 2,259 assertions, one expected multisite-only
  skip. AJAX: 8 tests, 28 assertions. Full unfiltered multisite: 160 tests,
  2,266 assertions, no skips.
- The strict frontend golden comparison passed all 33 scenarios. PHP syntax
  passed for the main file and all 77 `src` PHP files. Composer validation,
  PHPStan, PHPCS, JS lint, icon determinism, four-bundle build, both JS smoke
  tests, and every tracked static architecture/localization/integration
  contract passed.
- The isolated fresh Sandbox browser worker passed the Gutenberg stored-block,
  settings accessibility, real Elementor editor/public, and stored WPBakery
  public fixtures. Five tests passed; the licensed WPBakery editor-picker test
  was the one explicit skip.
- Two archive builds each contained 231 files and 665,359 bytes, with matching
  SHA-256
  `ff85bfc7c2d1c8af1d243ac3e6189e412a4b6a334c52b2d91d1fac5d3eac2e57`.
  The distribution contract passed. A fresh Sandbox activated the extracted
  archive without Composer and verified the production autoloader, both
  shortcodes, both blocks, canonical permalink rendering, direct PHP output,
  and inherited Social Links profiles.
- Plugin Check 2.0.0 on the clean archive reported two errors and 57 warnings.
  Both errors are `block_api_version_too_low` because the two block metadata
  files intentionally retain API version 1 for the WordPress 5.3 floor while
  WordPress 7.0 recommends API version 3. Warnings are compatibility/public-
  API/manual-translation/static-nonce findings, including the intentionally
  omitted Composer manifest in the production ZIP. No baseline was created.

## Implementation and contract evidence present in the working tree

The following are source/contract facts, not completed release gates.

- Root `block.json` defines the existing share block and
  `blocks/social-links/block.json` defines the separate profile-links block.
  `BlockRegistrar` registers both dynamic blocks from metadata (with a
  metadata-reading fallback), and each editor imports its own metadata.
  `save()` is `null`; previews are local and PHP produces frontend HTML.
- Each block invokes the canonical `RenderFacade` directly. Registered render
  paths do not call the shortcode callback. Elementor and WPBakery use their
  canonical controller/adaptor inputs and the same facade for frontend output.
- `ShareContextFactory` selects an explicit block context post ID, then the
  loop/global post, then a queried `WP_Post`, and for AJAX checks the documented
  request IDs before using the historical current-page fallback. `RenderFacade`
  supplies that canonical URL when a request omits a URL or supplies a
  recognised historical permalink placeholder. `ShareUrlResolutionContractTest`
  describes singular post/page, archive-loop, explicit URL, historical
  placeholder, and AJAX expectations, including one encoding pass. It still
  requires execution evidence.
- The bootstrap refuses to boot when `vendor/autoload.php` is unreadable:
  activation displays remediation through `wp_die`, and active installs show
  single-site and network-admin notices. Composer is prepared with `--no-dev
  --classmap-authoritative` before packaging, and the `zip` script rejects any
  development or non-authoritative loader; the archive contract requires the
  Composer autoloader, PSR-4 map, static map, PHP source, `block.json`, built
  bundles, and icon assets, while excluding source JS, tests, docs, package
  metadata, and Composer metadata. These are safeguards in source until the
  deterministic archive is built and installed.
- Settings UI strings are provided in a translated PHP payload where
  appropriate; admin, builder, and block source uses the plugin text domain,
  and block scripts use `wp.i18n`/register script translations when WordPress
  provides that API. The localization contract checks the tracked
  settings/profile/template/modal/status and block sources. Translation catalog
  completeness and rendered-language review remain pending.

## Integration evidence status

| Integration | Repository contract/source evidence | Real editor evidence | Real frontend evidence | Release status |
|---|---|---|---|---|
| Automatic placement, widget, metabox | Canonical controllers and PHPUnit contracts | N/A | PHPUnit and 33-scenario golden comparison passed | Manual browser matrix pending |
| Shortcode | Canonical controller and output contracts for both public tags | N/A | Fresh-ZIP smoke passed for both tags | Passed automated candidate gate |
| Gutenberg | `block.json`, registration, stored-attribute and render contracts | Real stored block passed in isolated Sandbox | Canonical URL fixture passed | Passed automated candidate gate |
| Elementor | Canonical widget, persisted-data and asset lifecycle contracts | Real `save_builder` document and visible icon preview passed | Real stored document and canonical URL passed | Passed automated candidate gate; manual matrix pending |
| WPBakery | Canonical `vc_map`, stored-shortcode contracts and bundle smoke | **Not verified**; a licensed editor environment is required | Real stored shortcode and canonical URL passed | Editor gate blocked pending licensed environment |
| Direct PHP API | Render and canonical-permalink contracts | N/A | Fresh-ZIP smoke and PHPUnit passed | Passed automated candidate gate |

## Required command gates

Record the revision, exact command, tool versions, result, and durable log or
artifact link for each row. A passing result from an earlier commit is not
evidence for this uncommitted candidate.

| Gate | Current state | Evidence required before RC approval |
|---|---|---|
| JS lint, icon determinism, settings/block/localization contracts | Passed 2026-08-11 | Repeat after code changes |
| PHP quality | Composer validation, PHPStan and PHPCS passed on PHP 8.3.33 | Declared PHP/WP support matrix remains required |
| Regular, AJAX, and multisite WordPress contracts | Passed in Sandbox with summaries above | Repeat for approved candidate revision |
| Plugin Check | Completed: 2 intentional API-version errors, 57 warnings | Resolve API-version/support-floor decision; do not hide with a baseline |
| Archive reproducibility and fresh-install activation | Passed; matching checksum and clean activation recorded above | Repeat after version/package changes |
| Browser accessibility and visual parity | Pending manual work | Captures and issue disposition for Chrome, Firefox, Safari, and Edge at desktop and mobile widths |
| Elementor and WPBakery | Pending / blocked | Real licensed plugin fixtures, editor and frontend captures, and persisted-data comparison |

## Rollback rehearsal

The proposed rollback target is 2.2.6. No rehearsal has been run or passed.
The implementation is designed to retain `zm_shbt_fld` and
`_zm_sh_disable_share`, write no rewrite schema option, and require no reverse
migration; that design is not a substitute for rehearsal evidence.

1. Snapshot a fresh single-site staging database and uploads.
2. Install the deterministic candidate ZIP without running Composer in the
   WordPress instance.
3. Save representative settings, block content, an Elementor document, and a
   WPBakery shortcode fixture; capture frontend output and serialized option/
   post-meta values.
4. Replace the candidate with the published 2.2.6 package without uninstalling
   or deleting data.
5. Confirm that 2.2.6 reads the unchanged option and meta, renders supported
   saved content, and writes no rewrite schema option.
6. Append commands, environment, checksums, output comparison, and result.

## Fourteen-day staging soak

The soak has not started and has not passed. It starts only after a human
approves a specific candidate artifact and staging environment. Record the
candidate SHA-256, WordPress/PHP/theme/plugin versions, option checksum,
fixture IDs, rollback archive checksum, and daily evidence. Review errors,
share URLs, HTTP failures, placements, cache behavior, persisted-data drift,
and the available editor fixtures daily; conduct browser checks on days 1, 7,
and 13 and a rollback dry run on day 7. Any unexplained error, missing browser,
or unavailable builder pauses the clock. Fourteen elapsed days of evidence and
a final rollback rehearsal are required; automated tests do not substitute for
time.

## Open publication blockers

- Historical PNG provenance and redistribution rights are unresolved; see
  `resources/iconsets/ASSET-SOURCES.md` and `ICON-COVERAGE-MATRIX.md`.
- Cross-browser visual/accessibility evidence is absent.
- Real Elementor editor/frontend evidence passed in the isolated worker. A
  licensed WPBakery editor environment has not been documented.
- The declared WordPress/PHP support matrix, manual browser gate, soak, and
  rollback rehearsal are pending.
- The version and stable tag remain 2.2.6. Do not change them until every
  required gate is green and a release owner approves the candidate.
