# 3.0 release-candidate validation record

This is an evidence ledger, not release approval.  It must not be read as
authority to create a tag, upload to WordPress.org, publish an article, or
deploy an archive. Candidate metadata is aligned before the soak; that is not
publication.

## Support declaration and evidence scope

The distributed plugin currently declares **WordPress 5.3+** and **PHP 7.0+**
in both the plugin header and `readme.txt`; Composer also requires PHP 7.0+.
`readme.txt` declares “Tested up to: 7.1”. These are repository declarations,
not a statement that this candidate has been manually validated in every
intermediate WordPress/PHP/browser combination.

The tracked GitHub workflow defines these automated combinations:

| Scope | Configured combinations | What the workflow does not establish |
|---|---|---|
| PHP syntax/bootstrap | PHP 7.0, 7.4, 8.0, 8.3, 8.5 | Browser or builder-editor behavior |
| WordPress functional smoke | WP 5.3/PHP 7.0; WP 6.8/PHP 8.3; `latest`/PHP 8.3; WP 7.1 final/PHP 8.3 | Full PHPUnit contracts for the WP 5.3 row |
| WordPress contracts | WP 6.8/PHP 8.3 and `latest`/PHP 8.3 | A real Elementor or WPBakery editor |
| Archive | PHP 8.3 with Node 22, pnpm 11.5.2, Composer, and the deterministic Node archive builder | A staging soak, rollback rehearsal, or cross-browser review |

## 2026-08-13 full post-2.2.6 release-diff audit

The complete `v2.2.6..latest` change set was inventoried and independently
reviewed across PHP/bootstrap/storage, JavaScript/admin/blocks, security,
packaging, CI, documentation, and test coverage. The audit found no reportable
security vulnerability. It did find release-changing defects, so staging
attempt 01 was reset rather than allowing known-bad bytes to accrue time.

Corrected candidate work now includes:

- settings saves retain a custom template while its network panel is disabled,
  and replace core-owned fields without deleting opaque top-level or nested
  extension data;
- canonical service composition again occurs at the published `init` priority,
  allowing icon-set callbacks registered during `plugins_loaded` to participate;
- PHP 8.4+ implicit-nullability deprecations are removed while distributed PHP
  remains valid at the declared PHP 7.0 floor;
- current POT/PO/MO catalogs and two block-editor JSON catalogs are generated
  reproducibly, and French builder tests no longer depend on translated labels;
- share anchors have translated accessible names while their classes,
  destinations, visual behavior, and wrapper structure remain stable;
- one canonical viewer policy applies three independent booleans to the content
  author, other logged-in users, and logged-out visitors across automatic,
  shortcode, widget, block, Elementor, WPBakery, and direct-PHP rendering;
- Bootstrap Solid is the new-install icon default and the historical Default
  pack is omitted from fresh selectors, while existing saved `default`
  selections, explicit content, assets, and runtime lookups remain supported;
- dynamic blocks and historical shortcode placeholders now resolve the current
  post permalink at the canonical render boundary before the share template is
  encoded, while explicit custom URLs remain supported and single-encoded;
- both maintained blocks use API v3 on WordPress 6.3+ and a runtime-selected
  API v1 compatibility registration on WordPress 5.3-6.2;
- candidate metadata is 3.0.0, preventing collision with published 2.2.6;
- archive builds refuse overwrite, CI compares separate outputs with a real
  production autoloader, `latest` receives CI, PHPStan/PHPCS are gated, tag
  deployment builds the exact distribution directory, and the deploy action is
  commit-pinned;
- the WP 5.3/PHP 7.0 workflow smoke now covers both shortcodes, both dynamic
  blocks, legacy symbols, profile-link storage, extension-key retention, and
  disabled-meta placement suppression. It remains a smoke, not a claim that
  the modern PHPUnit suite ran on PHP 7.0.

Observed post-fix checks on the working tree: JavaScript lint, icon determinism,
settings/block/localization/integration/static contracts, Composer validation,
PHPStan and PHPCS passed; regular PHPUnit passed 187 tests / 2,387 assertions
with one expected skip, AJAX passed 10 / 39, and the focused multisite contract
passed 1 / 7. Exact-archive and WordPress 7.1 evidence is recorded below. A new
staging Day 1 is recorded only after a separately authorized exact staging
installation and its own observation pass.

### 2026-08-13 WordPress 7.1 corrected exact candidate

The exact WordPress.org 2.2.6 ZIP was downloaded and independently identified
at SHA-256
`f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941`
(564,795 bytes, 134 entries). On WordPress 7.1-RC3 its stored Social Share
block reproduced the published defect: Facebook received
`http%3A%2F%2F%25%25permalink%25%25`, while automatic placement used the real
post permalink. This isolated the regression to the block/shortcode boundary.

The corrected implementation resolves omitted and historical-placeholder URLs
through `RenderFacade` before the network template encodes them. Focused
contracts cover persisted dynamic blocks, automatic placement, both historical
shortcodes, every maintained integration, explicit custom URLs, single
encoding, and absence of raw or encoded permalink placeholders.

Two production builds were byte-identical at SHA-256
`0900bc11b58b5e866bd4d359071cb26bc8216245e136426de1e5e3aa30ecee92`
(682,334 bytes, 234 entries). The distribution and authoritative production
autoloader contracts passed. Every archive entry is a regular file with mode
0644; an archive contract now rejects missing or malformed Unix permissions.
The exact ZIP was installed, rather than source-linked, in a disposable
WordPress 7.1-RC3 Sandbox as plugin 3.0.0. Its sorted per-file manifest matched
the extracted archive at SHA-256
`474f5bbf3e5e5f855378f53c219db47829718aff97219f07cc5aa9d225f3ee0c`.
Archive-level execution verified both block registrations at API v3, the real
post permalink in dynamic-block and automatic-placement output, no raw or
encoded placeholder, an explicit custom URL encoded exactly once, and the
logged-out audience setting suppressing shortcode output. It also verified
that a missing option selects Bootstrap Solid and hides Default from the fresh
settings payload, while an existing explicit Default selection round-trips,
remains selectable, and still renders its retained assets.

Plugin Check 2.0.0 against that exact ZIP exited successfully with zero errors
and 57 warnings. No baseline was created. The warnings are the already reviewed
production-manifest exclusion, discouraged `load_plugin_textdomain`, static
nonce/context heuristics, and retained legacy globals/hooks/prefixes; there are
no `block_api_version_too_low` findings.

The exact-archive Playwright suite on WordPress 7.1-RC3 passed nine tests with
one explicit licensed-WPBakery-editor skip. The forced-iframe editor was
visible, both maintained blocks were present in Core's inserter items and
inserted through the WordPress block data API, inspector changes persisted
through save and reload, and the frontend contained the expected permalink,
icons, and styles without placeholder, console, page, or block-recovery errors.
The settings E2E also persisted all three audience values as false, reloaded
them as false, and restored all three defaults to true.

The other reviewed WordPress 7.1 surfaces require no plugin change: this code
does not use the removed Navigation component, persistent-toolbar ownership,
jQuery UI, client-side media processing, the SVG Icon API, or the Abilities API.
Obsolete 40-pixel opt-in component props were removed from the settings UI.
After these checks, `readme.txt` was aligned to `Tested up to: 7.1`.

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
  Both errors are `block_api_version_too_low`: the two metadata files
  intentionally retain API version 1 for the WordPress 5.3 floor. The
  [official Block API-version reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-api-versions/)
  sets API v2 at WordPress 5.6+ and API v3 at WordPress 6.3+; the
  [metadata reference](https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#api-version)
  records v1 as the default. Consequently, no safe metadata upgrade exists
  without changing the declared support floor or maintaining incompatible
  definitions. Warnings are compatibility/public-API/manual-translation/
  static-nonce findings, including the intentionally omitted Composer manifest
  in the production ZIP. **No Plugin Check baseline was created or updated.**

### 2026-08-12 isolated support-floor and rollback evidence

The global Sandbox CLI provisioned a fresh isolated instance
`hssb-wp53-php7-wp53php70` with **WordPress 5.3** and **PHP 7.0.33**. The
candidate archive installed and activated there without Composer. A real saved
legacy shortcode post rendered four buttons with a canonical permalink, and
the candidate emitted neither `%%permalink%%` nor its double-encoded form.
This is a functional smoke at the declared floor, not a full PHPUnit or
browser-matrix certification: the current test runner requires PHP 7.4+.

The same clean instance was used for a candidate -> published 2.2.6 ->
candidate rollback rehearsal. Archive identities were:

- Candidate archive SHA-256:
  `ff85bfc7c2d1c8af1d243ac3e6189e412a4b6a334c52b2d91d1fac5d3eac2e57`.
- Published WordPress.org 2.2.6 archive, downloaded during the rehearsal,
  SHA-256:
  `f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941`.

The rehearsal created a published post with a saved `[zm_sh_btn]` fixture,
the `zm_shbt_fld` option, `_zm_sh_disable_share`, Elementor document/meta,
and WPBakery markers/meta. It replaced the active candidate with the published
archive without uninstalling or deleting data, activated 2.2.6, then repeated
the replacement/activation with the candidate. Across all three states, the
option serialization SHA-256 remained
`178b3be2ce509176e9c6ba3e0ebed14c03de22d4da9a613a0cf87a7a6895b54a`;
the disabled-meta value remained `on`; Elementor JSON remained
`6c7cb3a70677f497dbcc39152e8db181ea89f6e92f12915ab4f65eb5aff8e79f`; and
the WPBakery VC meta remained
`8fea5c492f1c500b1fc902976f65056c658b426297800280dcd9e6ee1794e8cc`.

The candidate's initial and restored shortcode HTML matched exactly (SHA-256
`b87e7383ffe9ccb04007662e36ab0d72a5623bbdd31fd0efd2b0e5fa3e63e2c1`),
with four anchors, a canonical encoded permalink, no raw placeholder, no
double-encoded placeholder, and no rewrite schema option. Published 2.2.6
also rendered the saved shortcode and retained all data, but its HTML hash was
intentionally different (`7254c00395a6349dcb2bdbd448e2ec5786ffc9ef0ff10a5df0548847981b0f56`)
because it retains the historical `%25%25permalink%25%25` bug fixed by this
candidate. That difference is a corrected behavior, not content loss. This
rehearsal does not validate the unlicensed WPBakery editor or Elementor editor
on the historical package.

The first attempts to provision additional WP 6.8/PHP 8.3 and latest/PHP 8.3
matrix instances stopped when Docker's predefined address pools were fully
subnetted. Capacity was restored later on 2026-08-12 and both disposable rows
were provisioned and tested as recorded below; the failed attempts are not
treated as evidence.

### 2026-08-12 additional WordPress/PHP rows and isolated repeat

The global Sandbox CLI provisioned disposable rows and independently reported
their live versions before tests:

| Row | Live runtime | WordPress test library | Regular | AJAX | Multisite |
|---|---|---|---:|---:|---:|
| `wp68php83` | WordPress 6.8 / PHP 8.3.33 | WordPress 6.8 | 171 tests, 2,296 assertions, 1 skip | 9 tests, 32 assertions | 171 tests, 2,303 assertions |
| `wplatestphp83` | WordPress 7.0.3 / PHP 8.3.33 | WordPress trunk | 171 tests, 2,296 assertions, 1 skip | 9 tests, 32 assertions | 171 tests, 2,303 assertions |

All six commands passed. The first WP 6.8 regular run found eight test-only
failures caused by `wp_kses_post()` choosing a different valid quote/entity
serialization than the newer core test environment. The runtime renderer and
published 2.2.6 both emit the same historical pre-KSES quote style. Tests were
made portable only at WordPress-owned KSES boundaries; the direct canonical
renderer and its golden master remain byte-strict. The passing results above
are the post-correction reruns.

A separate strict `sb e2e --local --workers 1 --strict-provision` run then
provisioned a fresh WordPress 7.0.3 worker. The complete functional suite passed
six tests and skipped only the licensed WPBakery editor picker. A second fresh
worker passed all eight browser-matrix projects after the exact Playwright
Firefox and WebKit executables were installed. Durable screenshots, dimensions,
checksums, command, and limitations are recorded in `BROWSER-VALIDATION.md`.

### 2026-08-12 current working-tree validation

After adding placement-level profile-link inheritance/suppression controls and
the Prajin lowercase-selector compatibility fix, the current working tree was
revalidated through the global Sandbox CLI and the host JavaScript toolchain:

- PHP syntax passed for every `src` PHP file. Composer strict validation
  passed, PHPStan reported no errors, and PHPCS passed after applying its
  array-alignment formatter to the changed PHP files.
- Regular PHPUnit passed 171 tests and 2,296 assertions with one expected
  multisite-only skip; AJAX passed 9 tests and 32 assertions; full unfiltered
  multisite passed 171 tests and 2,303 assertions with no skips.
- The strict frontend comparison passed all 33 scenarios. JavaScript lint,
  deterministic icon generation, the four-bundle build, admin React smoke (40
  persisted legacy fields), and WPBakery bundle smoke passed.
- A serial Playwright run against the healthy global Sandbox instance passed
  six tests: the icon matrix, Elementor picker and stored public fixture,
  Gutenberg stored block, settings keyboard dialog, and WPBakery stored public
  fixture. The licensed WPBakery editor-picker test was the sole explicit skip.
- The same functional coverage passed in a newly provisioned strict Sandbox
  worker (six passed, one licensed-WPBakery skip), and a second fresh worker
  passed all eight browser-matrix desktop/mobile projects.
- Two production archive builds were byte-identical before the responsive-rail
  correction. Each contained 231 files and was 667,676 bytes; SHA-256 was
  `d4584d5d99f2389683a56446e9687d16ebeeadb6b6302ed456819ef84bedebd5`.
  The optimized production-autoloader and distribution contracts passed. The
  exact archive then replaced the earlier candidate on the isolated WordPress
  5.3/PHP 7.0.33 instance and activated without Composer; both shortcodes and
  dynamic blocks registered, the canonical kernel autoloaded, and a two-link
  shortcode smoke contained neither raw nor double-encoded placeholders.
- Plugin Check 2.0.0 against the clean extracted then-current archive reported the
  same two `block_api_version_too_low` errors and 57 warnings documented above;
  it reported no source-tree `application_detected` errors. No baseline was
  created or updated.

After the responsive-rail correction and deterministic-generator update, two
fresh production archive builds again matched byte-for-byte. That pre-soak
archive contains 231 files, is 668,212 bytes, and has SHA-256
`1a8fc417b85d3b6ec261a9708609b8cb6a83b2eed7ba77eb882f7074afe523dc`.
The distribution contract passes. Fresh-archive activation and exact-checksum
rollback results are recorded separately below; they are not inferred from the
earlier candidate.

The exact archive was then copied into a disposable WordPress 7.0.3 / PHP
8.3.33 Sandbox and installed through WordPress without host Composer. It
activated as version 2.2.6; packaged `vendor/autoload.php` was readable and
Composer's installed metadata reported `dev: false`. The `zm_sh_btn` shortcode
rendered 884 bytes with its explicit canonical URL percent-encoded, and the
`html-social-share/social-share` dynamic block registered and rendered 876
bytes containing the canonical wrapper. The homepage returned HTTP 200. The
disposable instance, containers, volume, network, runtime, snapshots, and
temporary project directory were removed after the smoke. This is a focused
archive install/render proof, not a browser or full-settings matrix.

The post-correction strict functional E2E suite was also repeated in a fresh
WordPress 7.0.3 worker. Six tests passed: the icon fixture, Elementor picker and
stored frontend, Gutenberg stored block, keyboard settings dialog, and stored
WPBakery shortcode frontend. The unavailable paid WPBakery editor picker was
the one explicit skip, handled by the documentation/contract disposition below.

### 2026-08-12 pre-soak-archive rollback repeat

A second disposable WordPress 5.3 / PHP 7.0.33 Sandbox repeated candidate ->
published 2.2.6 -> candidate using the then-current archive SHA-256
`1a8fc417b85d3b6ec261a9708609b8cb6a83b2eed7ba77eb882f7074afe523dc`.
The freshly downloaded WordPress.org 2.2.6 archive SHA-256 was
`f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941`.

Across all three states, the `zm_shbt_fld` option hash remained
`096b811ecfea73e548c45b1fe0132b945f873418619499e88567941aae5d2e49`;
`_zm_sh_disable_share` remained `on` with hash
`e02909b77771044bfd812e8a931e3fc7180af1130607d326fc81b2e61bcdcf08`;
Elementor JSON remained
`3faa3138838ff2e49efa9e9e3101652d914523fae050d1d12192d9d729086c69`;
and WPBakery meta remained
`bd33979b214c285bde7c67ea252ae69df7537e88edf719bf954bcfa5bc2bb31d`.
No `hssb_schema_version` option was created.

The initial and restored candidate HTML matched exactly at
`a1cdc23090aa1f9b9b68e0f2ccf665ea679dd06fa94f317001c0e1a6c667aa5e`
with seven anchors and no raw or double-encoded placeholder. Published 2.2.6
rendered five anchors with hash
`5c46e54e1bc641650cdfa4d3df4cbfadf0f7d5483ee56dda481523afe32f2ec4`
and reproduced its known double-encoded placeholder. The isolated instance,
containers, DB volume, network, runtime/snapshots, archive server, and temporary
fixtures were removed. This closes the local rehearsal for those predecessor
bytes; the corrected candidate still requires a new exact-archive rehearsal
and the time-bound staging rollback.

### 2026-08-12 superseded soak candidate and Day 1 baseline

After the public/release documentation and metadata alignment, two final
archive builds were byte-identical. The now-superseded candidate contains 231
files, is 668,318 bytes, and has SHA-256
`d6575a33ff120ec768b6f71a4ea29f51a083760d016cd5f9a599aa0982945b05`.
It was installed, rather than source-linked, on the persistent Sandbox staging
instance `html-social-share-button` (WordPress 7.0.3 / PHP 8.3.33). The sorted
per-file manifest hash matched independently between a local archive extraction
and the installed staging tree at
`8ede5b6e6789c218a10c8efe5f395a4db0d6b928167a4050334da2f78432c42b`.

Attempt 01's clock started at `2026-08-12T09:04:40Z`, after the public probe,
desktop and 390×844 browser checks, fixture-data hashes, exact-byte check, and
error review passed. Its former earliest completion was
`2026-08-26T09:04:40Z`, but that timestamp is void after the reset. Day 1 and
the pre-start bootstrap corrections are recorded in
`docs/evidence/staging-soak-superseded/attempt-01-2026-08-12/2026-08-12/day-01.md`.
The attempt was reset on 2026-08-13 after the release-diff audit found defects
that require different packaged bytes and a distinct 3.0.0 version. The real
observation remains historical evidence but cannot count toward a corrected
candidate soak.

## Implementation and contract evidence present in the working tree

The following are source/contract facts, not completed release gates.

- Root `block.json` defines the existing share block and
  `blocks/social-links/block.json` defines the separate profile-links block.
  On WordPress 6.3+ `BlockRegistrar` registers both at API v3 from metadata; its
  metadata-reading compatibility path and localized editor configuration use
  API v1 on WordPress 5.3-6.2. Each editor imports its own metadata, `save()` is
  `null`, previews are local, and PHP produces frontend HTML.
- Each block invokes the canonical `RenderFacade` directly. Registered render
  paths do not call the shortcode callback. Elementor and WPBakery use their
  canonical controller/adaptor inputs and the same facade for frontend output.
- `ShareContextFactory` selects an explicit block context post ID, then the
  loop/global post, then a queried `WP_Post`, and for AJAX checks the documented
  request IDs before using the historical current-page fallback. `RenderFacade`
  supplies that canonical URL when a request omits a URL or supplies a
  recognised historical permalink placeholder. `ShareUrlResolutionContractTest`
  describes singular post/page, archive-loop, explicit URL, historical
  placeholder, and AJAX expectations, including one encoding pass. These paths
  passed in the candidate PHPUnit and fresh-archive smoke runs recorded above.
- The bootstrap refuses to boot when `vendor/autoload.php` is unreadable:
  activation displays remediation through `wp_die`, and active installs show
  single-site and network-admin notices. Composer is prepared with `--no-dev
  --classmap-authoritative` before packaging, and the `zip` script rejects any
  development or non-authoritative loader; the archive contract requires the
  Composer autoloader, PSR-4 map, static map, PHP source, `block.json`, built
  bundles, and icon assets, while excluding source JS, tests, docs, package
  metadata, and Composer metadata. The deterministic archive build and clean
  install recorded above exercised these safeguards.
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
| Gutenberg | API-v3 metadata, compatibility registration, stored-attribute and render contracts | Both blocks passed insert, inspector, save/reload, and forced-iframe checks on WordPress 7.1 final | Exact-ZIP frontend passed permalink, icon, style, and placeholder checks | Passed automated candidate gate |
| Elementor | Canonical widget, persisted-data and asset lifecycle contracts | Real `save_builder` document and visible icon preview passed | Real stored document and canonical URL passed | Passed automated candidate gate; manual matrix pending |
| WPBakery | Canonical `vc_map`, stored-shortcode contracts and bundle smoke | Official-document contract accepted when the paid editor is unavailable | Real stored shortcode and canonical URL passed | Passed the owner-approved documentation/contract gate |
| Direct PHP API | Render and canonical-permalink contracts | N/A | Fresh-ZIP smoke and PHPUnit passed | Passed automated candidate gate |

The WPBakery disposition follows its official developer documentation:
[`vc_map()`](https://kb.wpbakery.com/docs/inner-api/vc_map/) registers an
element, `base` is its shortcode tag, and each editable attribute's
`param_name` must match the shortcode parameter; the official
[custom-element guide](https://kb.wpbakery.com/docs/developers-how-tos/how-to-create-custom-element/)
also defines elements as WordPress shortcodes registered through `vc_map()`.
The canonical registrar uses base `zm_sh_btn` and matching `title`, `iconset`,
`iconset_type`, `icons`, and `profile_links_mode` names. PHPUnit protects that
map and stored-value behavior, the JavaScript bundle smoke protects editor-side
loading, and the stored-shortcode E2E fixture protects public rendering. Under
the release owner's 2026-08-12 instruction, that is the required gate when the
paid editor itself is unavailable; it is not represented as a live-editor run.

## Required command gates

Record the revision, exact command, tool versions, result, and durable log or
artifact link for each row. A passing result from an earlier commit is not
evidence for this uncommitted candidate.

| Gate | Current state | Evidence required before RC approval |
|---|---|---|
| JS lint, icon determinism, settings/block/localization contracts | Passed 2026-08-13 after the WordPress 7.1, permalink, and audience changes | Repeat after further included-source changes |
| PHP quality | Composer validation, PHP syntax, PHPStan, and PHPCS passed on PHP 8.3.33 on 2026-08-13 | Declared PHP/WP support matrix remains required |
| Regular, AJAX, and multisite WordPress contracts | Passed: 187 tests / 2,387 assertions / one expected skip; AJAX 10 / 39; multisite 1 / 7 | Repeat for the finally approved candidate revision if included source changes |
| Plugin Check | Exact corrected ZIP: zero errors and 57 reviewed warnings; no baseline | Re-run if candidate bytes change |
| Archive reproducibility and fresh-install activation | The earlier working-tree archive `0900bc11...cee92` passed exact-install smoke on WordPress 7.1 RC3; it is superseded evidence, not the final immutable archive | Commit the reviewed snapshot, rebuild from that revision, and reconfirm the exact ZIP identity before release |
| Browser matrix evidence | Chrome, Firefox, Edge, and Playwright WebKit fixture rendering/non-overlap passed after the responsive correction; the 390-pixel collision is now an executable assertion; Safari 26.6 desktop and 390×844 Responsive Design Mode captures are recorded | Physical iOS and high-contrast review are scope limits, not claims made by this record |
| Elementor and WPBakery | Elementor editor/frontend passed; WPBakery frontend passed and its `vc_map` contract matches official documentation | Repeat the repository contracts after further integration changes |

## Rollback rehearsal

The rollback target is the published 2.2.6 archive. Single-site rehearsals,
including a then-current-archive repeat, passed on 2026-08-12; environment,
checksums, retained data, and the intentional URL-correction output difference
are recorded above. The current repeat
demonstrates a candidate -> 2.2.6 -> candidate file replacement with no
uninstall, no option/meta loss, no reverse migration, and no rewrite schema
option. The release owner waived the elapsed fourteen-day staging requirement
on 2026-08-13. The rollback rehearsal remains useful exact-archive evidence,
but is no longer a timed-soak prerequisite.

## Manual release review and timed-soak waiver

Attempt 01 started at `2026-08-12T09:04:40Z` and was reset on 2026-08-13 when
the release audit required candidate-byte changes. The corrected 3.0.0 soak
never started. On 2026-08-13, the release owner explicitly waived the
fourteen-day wait and selected a manual exact-archive review with a 3–4 day
release schedule before WordPress 7.1. The recurring soak automation was
deleted and the superseded evidence remains historical only.

The earlier RC3 manual review installed exact ZIP SHA-256
`0900bc11b58b5e866bd4d359071cb26bc8216245e136426de1e5e3aa30ecee92`
on WordPress 7.1-RC3 / PHP 8.3.33 as plugin 3.0.0. The in-container archive
identity matched. Desktop and 390×844 settings layouts were reviewed; the
fresh Bootstrap Solid default and hidden historical Default selector state
were visible; all three audience controls were present and the logged-out
control persisted false across a save/reload before being restored true.

Both maintained blocks appeared in the inserter and saved from the forced
iframe editor. The focused real-Chromium test passed 2/2, proving API v3,
visible canvas previews, Inspector edits, save/reload persistence, and frontend
rendering. Manual frontend review confirmed desktop and mobile layouts without
horizontal overflow, accessible link names, real encoded permalinks, loaded
icon styles, and no `%%permalink%%` or `%25%25permalink%25%25`. Plugin Check
reported zero errors and the same 57 reviewed warnings. The bounded nginx and
PHP-FPM review window contained no 5xx, fatal, uncaught, or error entry.
The editor console contained two non-HSSB deprecation warnings for
`core/edit-post.isEditorPanelEnabled` and custom toolbar controls; neither API
is referenced by the plugin source or generated bundles, and the focused test
recorded no console error or page error.

This owner-approved replacement removes elapsed soak time as a blocker. It
does not authorize a tag, upload, deployment, or release, and it does not
remove the need to commit the reviewed source, rebuild from that immutable
revision, and reconfirm the exact archive before release.

## Remaining release-operation gates and accepted exceptions

- Default-pack provenance is an explicit release-owner compatibility exception;
  the repository makes no independent clearance claim. See
  `resources/iconsets/ASSET-SOURCES.md`.
- Chrome, Firefox, Edge, Playwright WebKit, and Safari evidence is recorded in
  `BROWSER-VALIDATION.md`. The 390-pixel collision is fixed and protected by an
  executable geometry assertion. Physical iOS and high-contrast review remain
  outside the evidence claim.
- Real Elementor editor/frontend evidence passed in the isolated worker.
  Because a paid WPBakery editor is unavailable, the release owner accepted
  its official `vc_map`/shortcode documentation plus the repository's exact
  mapping, persistence, bundle, and public-render contracts.
- The configured WP 6.8/PHP 8.3 and current/PHP 8.3 contract rows passed, and
  the WP 5.3/PHP 7.0 floor has a functional smoke. The full declared support
  matrix remains pending; the floor smoke is not a full modern PHPUnit
  certification. This record's Safari evidence is limited to desktop/Responsive
  Design Mode captures; physical iOS and high-contrast modes are outside its
  scope.
- Staging attempt 01 is superseded after one real Day 1. The release owner
  waived the fourteen-day replacement attempt on 2026-08-13 in favor of the
  recorded manual exact-archive review and a 3–4 day release schedule.
- Maintained metadata is API v3 and Plugin Check reports no API-version errors.
  WordPress 5.3-6.2 retain the explicit runtime API v1 compatibility path, so
  the support floor does not receive unsupported v3 editor semantics.
- Candidate version and stable tag are aligned at 3.0.0 to prevent collision
  with published 2.2.6. This does not authorize a tag, upload, or release.
