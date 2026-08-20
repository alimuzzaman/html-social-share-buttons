# Post-2.2.6 rewrite and release status

This file replaces the superseded post-2.2.6 checklist. It does not authorize a
tag, WordPress.org upload, or production deployment. Candidate metadata may be
aligned before the soak so the exact bytes carry their intended version.

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
- Maintained block metadata uses API v3 on WordPress 6.3+; WordPress 5.3-6.2
  receives an API v1 compatibility registration. WordPress 7.1 final's iframe
  editor passed the block insertion, Inspector, persistence, and frontend gate.

## Remaining release operations

- [x] Preserve staging attempt 01 as superseded evidence after the release
      audit required candidate-byte changes.
- [x] Replace the fourteen-day wait with the owner-approved 2026-08-13 manual
      exact-archive review; WordPress 7.1 final compatibility is recorded separately.
- [ ] Commit the reviewed snapshot, rebuild the production archive from that
      immutable revision, and reconfirm its SHA-256 and focused package gates.
- [x] Align candidate version header, stable tag, block metadata, and changelog
      at 3.0.0 before freezing the corrected exact archive.
- [ ] Align final listing copy, screenshots, and WordPress.org state only after
      explicit release approval.
- [ ] Tag/upload/deploy/publish only with separate authorization.

The dated evidence ledger is `RELEASE-CANDIDATE-VALIDATION.md`; the active soak
record and reset status are in `STAGING-SOAK.md`.
