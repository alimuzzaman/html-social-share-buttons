# Rewrite gap review after 2.2.6

## Release-readiness summary

`v2.2.6` (`620f1ae66`) is the published baseline. The `latest` working tree
contains the rewrite and its current uncommitted release-hardening changes; it
is not a public 3.0 release. The plugin header, `block.json`, and `Readme.txt`
still carry version 2.2.6, and the stable tag must not change during this work.

The architecture and contracts show substantial implementation progress, but
they are not sufficient release evidence by themselves. In particular, this
repository does not contain evidence that a commercial WPBakery editor passed,
that cross-browser screenshots passed, that rollback was rehearsed, or that a
14-day soak passed. Those claims must remain absent until evidence is recorded.

## Current implementation evidence

- Production bootstrap loads Composer’s autoloader and then the legacy runtime;
  the new service graph owns canonical settings, registries, rendering,
  placement, translations, and migration infrastructure while legacy globals
  and adapters live below `Compatibility/Legacy`.
- Root `block.json` now owns dynamic-block metadata. The block editor imports
  it; server registration reads it with a WordPress-5.3-compatible fallback;
  `save()` remains `null`; and server rendering uses `renderCanonical()` rather
  than the shortcode callback.
- Canonical share URL selection resolves a block context post, loop/global
  post, queried post, or AJAX-request post before the legacy fallback. The
  static contract specifies one encoded permalink for omitted and recognized
  historical placeholder URLs across shortcode, block, Elementor-compatible
  input, WPBakery stored shortcode, and direct PHP paths.
- A missing production Composer loader blocks runtime boot, fails activation
  with remediation guidance, and adds administrative notices. The packaging
  script requests a no-dev classmap-authoritative loader and stages a
  symlink-free distribution tree; archive verification requires the essential
  Composer maps, PHP sources, `block.json`, built bundles, and icon assets.
- Settings UI text is supplied in a PHP translation payload, block editor text
  uses `wp.i18n`, and script translations are registered when supported. The
  tracked localization contract covers settings/profile/template/modal/status
  source keys, but this is not a translated-language acceptance test.
- Canonical manifests define six built-in icon sets and the exact supported
  network/shape combinations listed in `ICON-COVERAGE-MATRIX.md`. Canonical
  files exist for those manifest cells; their browser parity and license status
  are separate questions.

## What is not yet proven for this candidate

| Area | Current state | Required evidence |
|---|---|---|
| PHP, JavaScript, icon, settings, block, localization contracts | Sources and commands are present | Candidate-revision logs from the configured commands |
| WordPress support declaration | Header/Composer say WP 5.3+ and PHP 7.0+ | Matrix runs; note that the configured WP 5.3 row does not run full PHPUnit contracts |
| Archive | Build script and archive contract are present | Two matching ZIP checksums and fresh WordPress installation/activation |
| Frontend compatibility | Contract/fixture sources are present | Candidate output results and manual checks for all supported surfaces |
| Gutenberg | Metadata and dynamic renderer are present | Editor and frontend captures on supported WordPress versions |
| Elementor | Optional integration and deterministic stubbed storage contract are present | Real plugin editor/frontend fixture and persisted-data capture |
| WPBakery | Optional integration, stored-shortcode contracts, and bundle source are present | Licensed editor environment, editor/frontend fixture, and persisted-data capture |
| Icon packs | Manifest coverage is present | Cross-browser matrix and source/redistribution/trademark review |
| Rollback | No schema migration is designed; 2.2.6 is the target | Completed rehearsal with before/after storage and output comparison |
| Staging | Plan only | Fourteen consecutive days of recorded evidence |

The GitHub Actions workflow is a real tracked CI workflow, not an out-of-scope
future proposal. It configures PHP syntax, JavaScript/contracts, selected
WordPress combinations, and archive installation checks. It does not replace
the manual browser, real-builder, provenance, rollback, or soak gates.

## Release blockers

1. Historical PNG source and redistribution rights remain unresolved. The
   “probable” attributions in `resources/iconsets/ASSET-SOURCES.md` are not
   license clearance. Do not claim those packs are cleared or redistribute them
   in 3.0 without verifiable evidence or an approved replacement decision.
2. No candidate-specific command, archive, or fresh-install evidence is
   recorded. The audit environment used for this document had no `php` or
   `pnpm`, so it could not supply that evidence.
3. No manual Chrome, Firefox, Safari, and Edge desktop/mobile parity evidence
   is recorded.
4. Real Elementor editor/frontend captures are missing. A licensed WPBakery
   editor environment and its verification are also missing.
5. A 14-day staging soak and rollback rehearsal have not started or passed.
6. Placement-level profile-link controls remain unimplemented; decide whether
   that is a 3.0 requirement or defer it explicitly before RC approval.
7. Version/listing alignment, screenshots, FAQ, and release copy need final
   review after the functional gates pass.

## Release sequence

1. Run and record all configured quality, WordPress, Plugin Check, archive, and
   fresh-install gates against a frozen candidate revision.
2. Complete provenance decisions and the supported icon/browser matrix.
3. Capture real Gutenberg and Elementor evidence and verify the WPBakery editor
   in a licensed environment.
4. Approve a candidate artifact, run the 14-day staging plan, and complete the
   rollback rehearsal.
5. Obtain release-owner approval, then—and only then—align versions, stable
   tag, listing materials, and publication artifacts.
