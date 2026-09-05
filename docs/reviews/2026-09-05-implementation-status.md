# Initial plugin update implementation record

This records the first implementation pass, before the user authorized completing release preparation. Its archive and open-check list are historical. Candidate metadata is now 3.2.0; see the dependency disposition for the refreshed audit. Final immutable-source acceptance is tracked separately.

Source work is implemented on `codex/plugin-update-preview`, based on
`b8bc5fd09decb026d7e75e4df43d7ae10c9bf9f5`. Changes are uncommitted. This is not
a release-ready verdict. All delegates used Astra Low as requested; the parent
integrated the changes and reviewed their evidence.

## Implemented

- Settings values derive from effective runtime settings. The first admin
  screen matches fresh-install defaults. Retired analytics and URL-port
  controls no longer submit values; normal saves preserve their old values or
  absence. Legacy programmatic interfaces remain callable.
- The obsolete analytics script is removed. Canonical WordPress URL resolution
  is unchanged. This documented output exception needs human release review.
- Auto-hide works across six built-in packs and four appearances. Rails reveal
  on hover or keyboard focus, and stay visible for mobile/coarse pointers and
  reduced motion. Core CSS owns movement and uses actual rail width.
- Admin template preview resolves fixed samples through the canonical PHP
  resolver and shared final URL treatment. It returns text diagnostics,
  requires nonce plus `manage_options`, makes no destination requests, and
  saves nothing. Edit/reset/unmount cancels stale results. Save remains separate.
- A shared template sanitizer preserves the four supported literal tokens.
  In particular, `%%description%%` now survives the WordPress percent-escape
  cleanup that previously removed its `%de` substring. Preview and save share
  the same cleanup; other encoded-escape behavior is retained.
- Tag pushes validate only. Publication requires explicit manual dispatch on
  the tag, `publish-vX.Y.Z` confirmation, the reviewed archive hash, and successful
  same-run validation. Deployment consumes the tested ZIP without rebuilding.
  No unprotected environment is treated as an approval boundary.
- Build tooling uses `adm-zip` 0.6.0 and `@wordpress/scripts` 34.2.0; both lockfiles
  are refreshed. Compiled admin assets and translation catalogs are regenerated.
  Version metadata remains 3.1.0 pending an intentional release decision.

## Observed checks

| Check | Result |
| --- | --- |
| `pnpm run settings:check` | Passed build, compiled admin/preview/builder smoke, mapper, localization, compatibility, rendering-surface and metadata contracts. |
| `composer quality` | Passed PHPStan on 84 source files and PHPCS. |
| `composer validate --strict --no-check-publish`, `pnpm run lint:js` | Passed. |
| `pnpm run appearance:check`, `icons:check`, `icons:legacy-x:check` | Passed. |
| `pnpm run i18n:build` | Passed; installed WP-CLI emitted a gettext PHP deprecation. New untranslated strings retain English fallback. |
| `pnpm run release:check` | Passed 18 workflow failure/cancel/skip paths, nine archive tests, and Plugin Check output validation cases. |
| Actionlint | Agent validated both workflow files successfully. Hosted execution was not run. |
| Static CSS Playwright matrix | Chromium, Firefox, WebKit passed 48 pack/appearance/side combinations, enabled/disabled, keyboard, hover, coarse pointer, reduced motion, 390px/600px. These are local stylesheet fixtures, not WordPress/theme proof. |
| WordPress browser suite discovery | 42 cases discovered across three engines and two viewports. Discovery is not execution. |
| Final disposable production archive | Passed offline production Composer install, autoloader validation, two identical ZIP builds, distribution contract, manifest verification, extraction and file parity. |
| Diff whitespace | Passed. No pre-existing files deleted; original vendor and existing ZIP preserved. |

Final archive: `/tmp/hssb-final-archive-pfgf41ja/candidate.zip`, 242 files,
SHA-256 `9d73b3f8d7ed09f06c80ecaa664ce4bef734525b3fba16a783df60f577197b07`.
Its sibling `snapshot.json`, `manifest.json`, and `validation.json` record the
checks. This is an uncommitted working-tree snapshot: the manifest's source SHA
is the base commit, not an immutable identity for the changed source. No claim
of WordPress installation or release acceptance is made for this ZIP.

Independent agent review found no further production issue in the preview or
settings changes. It corrected a test that submitted opaque localization data
instead of real form fields. Parent integration found and fixed the description
token issue and aligned browser project naming/platform key handling with CI.

## Open validation and risk

The Node audit now reports 37 development-tool advisories (28 high, nine
moderate), down from 44. The direct archive-parser advisory is removed. Residual
paths remain in the upstream WordPress build/lint/test toolchain, with peer
dependency mismatches also reported. This is not evidence of a shipped
frontend exploit, and is not a clean dependency audit. Review residuals before
release; detailed local evidence is `/tmp/hssb-audit-disposition.txt`.

WordPress PHPUnit, AJAX, multisite, support-floor execution, real settings and
preview browser runs, Plugin Check, hosted CI failure injection, paid-builder
runtime, and final manual exact-archive checks remain **UNVERIFIED**. New tests
exist for these paths; static checks do not establish their runtime outcome.
Before-change runtime capture could not bootstrap; the existing golden fixture
was retained except for deliberate removal of the obsolete analytics script.

The isolated local Sandbox attempt failed declared-plugin activation for
Elementor, this plugin and Plugin Check. It also unexpectedly emitted a login
credential. Work on that runtime stopped under the user-supplied AGENTS.md rule,
“After two failed attempts with the same approach, contradictory evidence,
material scope expansion, or a security concern, stop and report the evidence,
likely cause, options, and smallest needed decision.” Sanitized feedback IDs:
`6704dc49fbf1d94c5578a7857e34010e` (activation/output) and
`9ba6b04eb9a4ce74b23ef926dfdd3deb` (temporary-path setup).

The four disposable local containers were stopped and removed at the user's
request after verifying their exact IDs and isolated compose configuration.
The `sandbox-project` container inventory is now empty; `localhost:8188` no
longer serves this test runtime. Local files under
`/tmp/hssb-runtime-20260905-01` and the database volume were retained. The setup
never used the original Git checkout as an installation target. Runtime checks
still require a working isolated environment. No production, remote deployment,
publication, push, commit or tag was performed. The superseded fourteen-day soak
stays waived.
