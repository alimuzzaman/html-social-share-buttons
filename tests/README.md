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

This verifies that core frontend renderer files such as `html-social-share.php`, `shortcode.php`, `function.php`, `filters.php`, and `iconsets.php` have no working-tree diff.

## Settings save contract check
Run this after changing settings form fields or sanitizer behavior:

```bash
make settings-sanitize-contract
```

This verifies that React-submitted settings still sanitize into the legacy `zm_shbt_fld` shape, including storing enabled `show_in[...]` placements as the selected iconset type string.

## Local settings suite
Run all non-WordPress-DB settings checks together:

```bash
make settings-local-checks
```

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
