# Frontend Regression Testing (Settings Changes)

This folder contains the new regression capture-and-compare flow for plugin frontend output.

## Files
- `tests/frontend-output-regression.php`: CLI tool to capture rendered share markup and compare against baselines.
- `tests/frontend-output-scenarios.json`: Scenario definitions used for captures.
- `tests/fixtures/frontend-output-baseline.json`: Baseline snapshot store.

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

## React settings smoke check
Run this after editing the React settings page:

```bash
make admin-react-smoke
```

This verifies that `assets/admin-react.js` parses, mounts under a mocked `wp.element` runtime, still renders all required legacy `zm_shbt_fld[...]` field names, and preserves the default shortcode/PHP generator outputs.

The React settings script intentionally uses a `wp.element.Component` class instead of hooks so it remains aligned with the plugin's WordPress `5.0+` compatibility baseline.

## Static frontend drift surface check
Run this when a settings-only change should not touch frontend rendering code:

```bash
make frontend-drift-surface
```

This verifies that core frontend renderer files such as `actions.php`,
`filters.php`, `iconsets.php`, `interfaces.php`, `shortcode.php`, and
`widget.php` have no working-tree diff. The current URL sanitizer is covered by
the Sandbox-backed PHPUnit contract suite.

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
