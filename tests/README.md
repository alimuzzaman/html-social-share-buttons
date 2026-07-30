# Frontend Regression Testing (Settings Changes)

This folder contains the regression contracts that freeze plugin frontend
output and persisted settings behavior during the ground-up rewrite.

## Files
- `tests/frontend-output-regression.php`: CLI tool to capture rendered share markup and compare against baselines.
- `tests/frontend-output-scenarios.json`: Scenario definitions used for captures.
- `tests/fixtures/frontend-output-baseline.json`: Baseline snapshot store.
- `tests/fixtures/settings-schema-baseline.json`: Option name, defaults, field
  names, template defaults, sanitizer behavior, and legacy aliases.
- `tests/fixtures/wordpress-surface-baseline.json`: Shortcode, widget, block,
  builder, settings, metabox, AJAX, asset-handle, and storage identifiers.
- `tests/fixtures/builder-storage-baseline.json`: Serialized block, WPBakery,
  and Elementor persisted settings.
- `tests/fixtures/legacy-public-api-baseline.json`: Constants, globals,
  functions, interfaces, class methods/properties, and magic access preserved
  by the compatibility module.
- `tests/support/frontend-output-contract.php`: Deterministic shared renderer
  context used by the CLI and PHPUnit contracts.
- `tests/phpunit/FrontendRenderContractTest.php`: Fails on any golden-master
  frontend difference.
- `tests/phpunit/UpgradeRollbackContractTest.php`: Proves representative 2.2.6
  settings round-trip unchanged and activation writes no migration state.
- `tests/phpunit/TranslationContractTest.php`: Covers canonical and legacy
  text-domain loading and fallback behavior.
- `tests/phpunit/FrontendAssetContractTest.php`: Covers style collection,
  legacy URLs, and inline CSS de-duplication.
- `tests/phpunit/WpBakeryIntegrationContractTest.php`: Freezes WPBakery
  registration, one-bundle ownership, dependencies, and nonce localization.
- `tests/vc-scripts-smoke.js`: Exercises the compiled WPBakery control with
  string/object AJAX responses and untrusted labels.
- `tests/phpunit/ExtensionHookContractTest.php`: Freezes canonical hook names,
  typed registry/schema fallbacks, canonical-before-legacy ordering, and
  recursion protection.
- `tests/phpunit/LegacyPublicApiContractTest.php`: Freezes public symbol
  existence, visibility, method/function signatures and defaults, magic
  properties, and built-in icon-set behavior.

## Prerequisites
- WordPress installation available for CLI testing.
- Plugin code available under a WordPress install, with this repository path mounted as the plugin directory.
- `WP_ROOT` optional env var or `--wp-root` argument.

## Capture baseline
Run before making settings-related UI/code changes:

```bash
php tests/frontend-output-regression.php capture \
  --wp-root=/path/to/wordpress \
  --scenario-file=tests/frontend-output-scenarios.json \
  --output=tests/fixtures/frontend-output-baseline.json
```

## Compare after changes
Run after a settings change and review any differences:

```bash
php tests/frontend-output-regression.php compare \
  --wp-root=/path/to/wordpress \
  --baseline=tests/fixtures/frontend-output-baseline.json \
  --scenario-file=tests/frontend-output-scenarios.json --strict
```

- Without `--strict`, the command exits 0 and prints differences.
- Use `--strict` to fail CI/automation on any mismatch.
- If WordPress cannot bootstrap (for example, database connection failure), the command exits non-zero before capture/compare starts.
- The baseline and scenario catalogs must contain the same ordered names; an
  empty or stale baseline fails.
- The comparison preserves output whitespace except for normalizing line
  endings.

The same command can run inside a WordPress WP-CLI context:

```bash
wp eval-file tests/frontend-output-regression.php compare strict
```

## React settings smoke check
Run this after editing the React settings page:

```bash
make admin-react-smoke
```

This rebuilds and executes `build/admin-react.js` under a mocked `wp.element`
runtime, verifies every field declared by the settings schema fixture, and
preserves shortcode/PHP generator output. Editable JavaScript lives under
`src/js/`; WordPress never loads those source modules directly.

The React settings script intentionally uses a `wp.element.Component` class
instead of hooks so it remains aligned with the plugin's WordPress `5.3+`
compatibility baseline.

## Static frontend drift surface check
Run this when a settings-only change should not touch frontend rendering code:

```bash
make frontend-drift-surface
```

This verifies that historical frontend globals are loaded from
`src/Compatibility/Legacy` and no superseded shortcode, widget, filter,
action, or interface implementation remains at the plugin root. Exact output
drift is covered by the Sandbox-backed PHPUnit golden-master suite.

## Local settings suite
Run all non-WordPress-DB settings checks together:

```bash
pnpm run settings:check
```

## PHPUnit contract suite

The repository includes a PHPUnit configuration and bootstrap for the current
procedural plugin architecture. The tests use `WP_UnitTestCase` and the
WordPress test library supplied by Sandbox:

Run the Sandbox MCP `run_tests` tool for this suite.

Sandbox supplies PHPUnit, the WordPress test library, and polyfills; no test
dependencies are installed in this plugin repository. The PHPUnit harness
requires PHP 7.4 or later; the distributed plugin itself supports PHP 7.0 or
later.

```bash
SANDBOX_LABEL=rewrite-contracts pnpm run test:unit
SANDBOX_LABEL=rewrite-contracts pnpm run test:multisite
```

The wrapper requires real PHPUnit completion markers and fails if Sandbox exits
before the suite runs.

AJAX tests are intentionally run as a separate WordPress group:

```bash
pnpm run test:ajax
```

They cover the settings save/search actions and all three icon-set endpoints,
including nonce, capability, persistence, and response-shape behavior.

The compatibility workflow runs regular, AJAX, and multisite PHPUnit suites on
WordPress 6.8 and latest. WordPress 5.3 receives activation and shortcode
smokes because that test library does not support the PHPUnit 9 harness.

## WordPress Plugin Check

The project Sandbox config installs and activates the WordPress Plugin Check
plugin automatically. Run the release-scoped check below. It excludes the
development-only files already omitted by `.distignore`.

```bash
pnpm run plugin:check
```

The committed `plugin-check-baseline.json` remains empty when the release
package has no known findings.

## Scenario authoring
Add new scenarios in `tests/frontend-output-scenarios.json` with the same schema:
- `name`: unique string
- `options`: associative array of `zm_shbt_fld` overrides to test

## Purpose
This is intentionally limited to output-diff validation so settings/visual refactors can proceed without changing frontend share HTML unexpectedly.

## Makefile shortcuts

After setting WP_ROOT, you can run:

```bash
make frontend-capture
make frontend-compare
```

Required:
- `WP_ROOT` environment variable must point to your WordPress root.
- Example: `WP_ROOT=/var/www/html make frontend-capture`
