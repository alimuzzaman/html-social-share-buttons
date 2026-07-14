# Post-release plan: 2.2.6 → 2.2.7 → 2.3.0

## 2.2.6 release verification

- [x] Confirm `master`, `origin/master`, and tag `v2.2.6` point to the release commit.
- [x] Run the settings, integration, and frontend-drift contract checks.
- [x] Run WordPress Plugin Check; it reports no errors.
- [x] Run JavaScript lint and rebuild the committed editor assets.
- [x] Run PHPUnit through the local WordPress PHP image and provisioned test database: 6 tests, 12 assertions passed.
- [x] Run the Playwright suite with the local Chrome channel: 1 settings test passed, 4 optional builder tests skipped because content/plugins are absent, 0 failures.
- [ ] Perform a staging-site smoke test with the release ZIP.

## 2.2.7 hardening scope

The first post-release patch should remain small and compatibility-focused:

- [x] Make code-generator actions real buttons instead of navigation links.
- [x] Give the generated-code overlay dialog semantics and a labelled heading.
- [x] Move focus into the dialog and restore focus to the triggering control on close.
- [x] Add an automated browser assertion for keyboard opening, closing, and focus restoration.
- [x] Review the settings page with keyboard-only navigation and the desktop accessibility tree on local Sandbox.
- [ ] Repeat the review with a screen reader on external staging.
- [ ] Fix only regressions found by that review; preserve shortcode and frontend markup.

## 2.3.0 candidate backlog

Prioritize these only after the 2.2.7 verification gate:

1. Improve block-editor controls and preview fidelity, including clearer selected-network state.
2. Measure and, if beneficial, restore multiple image/icon assets without regressing page weight.
3. Expand analytics integrations only with an explicit privacy and opt-in design.
4. [x] Add PHP compatibility CI plus WordPress 6.8/latest activation and shortcode-rendering jobs.
5. [x] Refresh README copy and examples to match Telegram, Bluesky, blocks, and Elementor.

## Monitoring checklist

For the first week after release, record date, plugin version, WordPress/PHP versions, symptom, reproduction steps, and whether the issue affects frontend output, settings, or an integration. Do not include credentials or private site data. Review WordPress.org support/review activity and the project issue tracker at least once per business day, then convert confirmed regressions into a minimal patch with a regression test.
