# Post-2.2.6 rewrite and release status

This file replaces the superseded post-2.2.6 checklist. It does not authorize a
tag, WordPress.org upload, production deployment, or version change.

## Completed implementation work

- Canonical namespaced runtime and thin legacy compatibility boundary.
- Preserved settings, metadata, shortcodes, widgets, blocks, builders, hooks,
  public symbols, frontend markup, and historical asset paths.
- Separate profile/contact links and the dynamic Social Links block.
- Bootstrap Solid and Tabler Outline generated icon sets.
- Responsive correction for floating rails at 600px and below.
- Deterministic production archive, fresh-install smoke, support-floor smoke,
  integration/browser contracts, and local exact-archive rollback rehearsal.

## Accepted evidence dispositions

- Default PNG provenance is an accepted compatibility exception, not a
  clearance claim.
- Unavailable paid WPBakery editor behavior is checked against official
  `vc_map()`/shortcode documentation plus executable repository contracts.
- Block API v1 and the WordPress 5.3 floor remain. The two corresponding Plugin
  Check findings are accepted; no clean Plugin Check result is claimed.

## Remaining release operations

- [ ] Freeze and approve the exact candidate archive for staging.
- [ ] Complete 14 consecutive real soak days with recorded daily evidence.
- [ ] Complete the day-7 dry rollback and final staging rollback against the
      exact candidate and published 2.2.6 archives.
- [ ] Align version header, stable tag, changelog, release ZIP, listing copy,
      screenshots, and WordPress.org state after explicit release approval.
- [ ] Tag/upload/deploy/publish only with separate authorization.

The dated evidence ledger is `RELEASE-CANDIDATE-VALIDATION.md`; the active soak
record is `STAGING-SOAK.md`.
