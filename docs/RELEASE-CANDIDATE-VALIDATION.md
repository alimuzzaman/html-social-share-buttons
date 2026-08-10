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
| Archive | PHP 8.3 with Node 22, pnpm 11.5.2, Composer, WP-CLI and `wp dist-archive` | A staging soak, rollback rehearsal, or cross-browser review |

No successful run ID, artifact checksum, or report for the current
uncommitted working tree is recorded here. Consequently every command-backed
gate remains pending until it is run against the candidate revision and its
output is linked or appended below. The audit environment used to update this
record had neither `php` nor `pnpm`, so it did not execute the PHP, JavaScript,
archive, or Sandbox commands.

## Implementation and contract evidence present in the working tree

The following are source/contract facts, not completed release gates.

- Root `block.json` defines the block name, attributes/defaults, text domain,
  editor handle, and context. `BlockAdapter` registers the dynamic block from
  that metadata (with a metadata-reading fallback), and the editor imports the
  same file. `save()` is `null`; the editor preview is local and PHP produces
  frontend HTML.
- The block invokes `renderCanonical()` directly. Its registered render path
  does not call the shortcode callback. Elementor and WPBakery continue to
  use their legacy shortcode-compatible paths.
- `CurrentPostPermalink` selects an explicit block context post ID, then the
  loop/global post, then a queried `WP_Post`, and for AJAX checks the documented
  request IDs before using the legacy current-page fallback. `LegacyRenderFacade`
  supplies that canonical URL when the render request omits a URL or supplies a
  recognized historical permalink placeholder. `ShareUrlResolutionContractTest`
  describes singular post/page, archive-loop, explicit URL, historical
  placeholder, and AJAX expectations, including one encoding pass. It still
  requires execution evidence.
- The bootstrap refuses to boot when `vendor/autoload.php` is unreadable:
  activation displays remediation through `wp_die`, and active installs show
  single-site and network-admin notices. The `zip` script runs Composer with
  `--no-dev --classmap-authoritative`; the archive contract requires the
  Composer autoloader, PSR-4 map, static map, PHP source, `block.json`, built
  bundles, and icon assets, while excluding source JS, tests, docs, package
  metadata, and Composer metadata. These are safeguards in source until the
  deterministic archive is built and installed.
- Settings UI strings are provided in the PHP localization payload; the block
  uses `wp.i18n`, declares the plugin text domain in metadata, and registers
  script translations when WordPress provides that API. The localization
  contract checks the tracked settings/profile/template/modal/status sources.
  Translation catalog completeness and rendered-language review remain
  pending.

## Integration evidence status

| Integration | Repository contract/source evidence | Real editor evidence | Real frontend evidence | Release status |
|---|---|---|---|---|
| Automatic placement, widget, metabox | Legacy adapters and PHPUnit contracts | N/A | Not recorded for this candidate | Pending command/run evidence |
| Shortcode | Adapter and output contracts | N/A | Not recorded for this candidate | Pending final capture |
| Gutenberg | `block.json`, registration, stored-attribute and render contracts | Not recorded | Not recorded | Pending browser/editor capture |
| Elementor | Optional widget source and deterministic stubbed storage contract | Not recorded | Not recorded | Pending a real fixture and captures |
| WPBakery | Optional `vc_map` source, stored-shortcode contract, and bundle smoke/contract sources | **Not verified**; a licensed editor environment is required | Not recorded | Blocked pending a licensed environment and fixture |
| Direct PHP API | Render and canonical-permalink contract sources | N/A | Not recorded for this candidate | Pending command/run evidence |

## Required command gates

Record the revision, exact command, tool versions, result, and durable log or
artifact link for each row. A passing result from an earlier commit is not
evidence for this uncommitted candidate.

| Gate | Current state | Evidence required before RC approval |
|---|---|---|
| JS lint, icon determinism, settings/block/localization contracts | Pending execution | `pnpm run lint:js`, `pnpm run icons:check`, `pnpm run settings:check` logs |
| PHP quality | Pending execution | `composer` validation/quality logs on the declared support floor and supported matrix |
| Regular, AJAX, and multisite WordPress contracts | Pending execution | Sandbox/CI logs with PHPUnit summaries |
| Plugin Check | Pending execution | Tool version and complete report; distinguish compatibility warnings from errors |
| Archive reproducibility and fresh-install activation | Pending execution | Two matching ZIP checksums, archive contract output, and fresh-install log |
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
- Real Elementor evidence is absent and a licensed WPBakery editor environment
  has not been documented.
- The command gates, deterministic archive/fresh-install evidence, soak, and
  rollback rehearsal are pending.
- The version and stable tag remain 2.2.6. Do not change them until every
  required gate is green and a release owner approves the candidate.
