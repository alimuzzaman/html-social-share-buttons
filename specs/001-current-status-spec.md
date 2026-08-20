# [SPECKIT-001] Spec: Current Status of HTML Social Share Buttons

## Metadata
- **Spec Number:** SPECKIT-001
- **Spec ID:** SPEC-BASELINE-2026-07-09
- **Title:** Current implementation baseline (single source of truth)
- **Owner:** Product + Engineering
- **Status:** Current
- **Created:** 2026-07-09
- **Last Updated:** 2026-08-13
- **Version:** 2.0
- **Source of Truth:** Single file in `/specs/`

## 1.0 Context
### 1.1 Purpose
Record the current implementation and compatibility baseline after the
canonical rewrite so later changes do not regress existing installations.

### 1.2 Why this is critical
The plugin is in active use with saved options in `wp_options`; any schema or serialization mismatch can break user settings or front-end output.

## 2.0 Baseline Scope
- Candidate metadata is `3.0.0`; publication remains gated by the exact-archive
  staging soak and rollback in `docs/STAGING-SOAK.md`.
- Production runtime ownership is canonical and namespaced. Thin legacy
  facades retain the published public symbols, hooks, identifiers, and storage
  contracts.
- Compatibility targets are WordPress `5.3+` and PHP `7.0+`.
- Settings, icon-set rendering, integrations, and frontend output share the
  canonical service graph. Runtime JavaScript is compiled into four bundles.

## 3.0 Persistence and DB Schema (Critical)
### 3.1 Storage table
- WordPress options table: `wp_options`

### 3.2 Critical option key contract
- Option name: `zm_shbt_fld`
- Storage type: serialized PHP array in `option_value`
- Must remain serialized structure-compatible for existing installations.

### 3.3 Existing `zm_shbt_fld` schema (current fields)
- `title` (string): page/share heading text.
- `iconset` (string): selected iconset id, default `default`.
- `use_port` (bool-like/int): URL port behavior toggle.
- `auto_hide_btn` (bool-like/int): floating button auto-hide behavior.
- `show_in` (array):
  - `show_left` (bool-like/int)
  - `show_right` (bool-like/int)
  - `show_before_post` (bool-like/int)
  - `show_after_post` (bool-like/int)
- `iconset_type` (string): one of iconset display variants.
- `icons` (assoc array): feature flags by icon id (for example `facebook`, `x`,
  `linkedin`, `pinterest`, `telegram`, `bluesky`, and `mail`).
- `share_templates` (assoc array): optional per-network share URL overrides.
- `profile_links` (assoc array): optional per-network profile/contact
  destinations.
- `profile_link_placements` (assoc array): explicit per-placement suppression;
  omitted values inherit configured profiles.
- Optional/legacy keys observed in runtime path:
  - `g_analytics` (bool-like/int)
  - `nofollow` (bool-like/int)
  - `excludes` (string list: IDs, titles, slugs)

### 3.4 Migration behavior that must be preserved
- Decode and submission boundaries map the historical `twitter` key to `x`
  without requiring a destructive stored-data migration.
- Unknown top-level and nested extension-owned option data must survive a core
  settings save.

### 3.5 Critical compatibility constraints
- Do not rename or flatten `zm_shbt_fld`.
- Do not change nested array keys without an explicit compatibility plan.
- Do not alter submission flow or option registration keys (`zm_shbt_opt`, `zm_shbt_fld`).

## 4.0 Runtime Baseline Behavior
### 4.1 Front-end generation
- Canonical shortcode, block, widget, Elementor, WPBakery, automatic-placement,
  and direct-PHP controllers delegate to one render facade.
- Frontend output retains the `zmshbt` wrappers/classes and established link
  behavior. Empty share anchors have translated `aria-label` values.
- Left/right rails render through the footer, and before/after placements use
  `the_content`.
- Exclusion behavior via settings `excludes` and post meta `_zm_sh_disable_share`.

### 4.2 Settings baseline
- Admin page remains at `admin.php?page=zm_shbt_opt`.
- Settings are registered with `register_setting('zm_shbt_opt', 'zm_shbt_fld', ...)` and sanitized.
- The settings page uses the canonical React application and AJAX controller;
  the option remains registered through the WordPress Settings API.

### 4.3 Admin assets baseline
- `assets/admin.css`, the four compiled `build/*.js` entry bundles, localized
  settings data, and block script-translation JSON are part of current
  behavior. JavaScript source lives under `src/js/` and is never loaded
  directly at runtime.

## 5.0 Requirement Baseline (Do Not Break)
- Preserve existing settings field names and data keys.
- Preserve output format for generated shortcode/PHP snippets.
- Preserve rendering behavior for icon choices, profile links, and visibility
  positioning.
- Treat additive accessibility markup as an explicit, regression-tested public
  contract change.

## 6.0 Frontend Regression Rules (Required Before Settings Work Starts)
### 6.1 Test protocol for any settings change
- Before each settings change, capture canonical frontend output fixtures for critical scenarios.
- After change, re-capture and diff against baseline.
- Any delta must be reviewed and justified before merge.
- Regression tooling is in `tests/frontend-output-regression.php` with scenario definitions in `tests/frontend-output-scenarios.json`.
- CI-friendly workflow can be run via `make frontend-capture` and `make frontend-compare` (set `WP_ROOT` environment variable).

### 6.2 Required scenarios
- Default option set render.
- Iconset + iconset_type selection permutations.
- `show_left`/`show_right` float placements.
- `show_before_post` / `show_after_post` toggles.
- Exclude behavior with a sample excluded identifier.
- `nofollow` on/off output.
- `g_analytics` on/off output.
- Baseline/final output fixtures are stored in `tests/fixtures/frontend-output-baseline.json`.

## 7.0 Risks and Open Questions
- Risk: accidental option schema drift may invalidate legacy options for existing users.
- Risk: inline script/asset coupling may create behavior drift if reordered without diff-based checks.
- Risk: candidate metadata can be mistaken for publication authorization; the
  release ledger and exact-archive soak remain authoritative.
- Browser screenshots are supporting evidence. The executable HTML golden
  fixture remains the deterministic rendering contract.
