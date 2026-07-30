# [SPECKIT-001] Spec: Current Status of Html Social Share Buttons Codebase

## Metadata
- **Spec Number:** SPECKIT-001
- **Spec ID:** SPEC-BASELINE-2026-07-09
- **Title:** Current implementation baseline (single source of truth)
- **Owner:** Product + Engineering
- **Status:** Draft
- **Created:** 2026-07-09
- **Last Updated:** 2026-07-09
- **Version:** 1.1
- **Source of Truth:** Single file in `/specs/`

## 1.0 Context
### 1.1 Purpose
Capture the exact baseline behavior before any settings revamp work begins so we can prevent accidental regressions while iterating on admin UX.

### 1.2 Why this is critical
The plugin is in active use with saved options in `wp_options`; any schema or serialization mismatch can break user settings or front-end output.

## 2.0 Baseline Scope
- Plugin version is `2.2.6`; the production PHP is still legacy while the
  settings screen and block editor use compiled JavaScript bundles.
- Compatibility targets are WordPress `5.3+` and PHP `7.0+`.
- Settings page, form renderer, iconset rendering, and share output are all in PHP+JS and share existing option contracts.

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
- `icons` (assoc array): feature flags by icon id (e.g., `facebook`, `x`, `linkedin`, `pinterest`, `mail`).
- Optional/legacy keys observed in runtime path:
  - `g_analytics` (bool-like/int)
  - `nofollow` (bool-like/int)
  - `excludes` (string list: IDs, titles, slugs)

### 3.4 Migration behavior that must be preserved
- Runtime migration from `icons['twitter']` to `icons['x']` is expected for backward compatibility and must remain behaviorally intact.

### 3.5 Critical compatibility constraints
- Do not rename or flatten `zm_shbt_fld`.
- Do not change nested array keys without an explicit compatibility plan.
- Do not alter submission flow or option registration keys (`zm_shbt_opt`, `zm_shbt_fld`).

## 4.0 Runtime Baseline Behavior
### 4.1 Front-end generation
- Main output path via `zm_sh_btn` wrapper and internal renderer.
- Front page injection via footer for left/right floats, and `the_content` filter for before/after post placement.
- Exclusion behavior via settings `excludes` and post meta `_zm_sh_disable_share`.

### 4.2 Settings baseline
- Admin page remains at `admin.php?page=zm_shbt_opt`.
- Settings are registered with `register_setting('zm_shbt_opt', 'zm_shbt_fld', ...)` and sanitized.
- Form rendering uses `zm_form` helper methods in `form.php`.

### 4.3 Admin assets baseline
- `assets/admin.css`, compiled `build/admin-react.js`, and the localized
  settings data are part of current behavior. JavaScript source lives under
  `src/js/` and is never loaded directly at runtime.

## 5.0 Requirement Baseline (Do Not Break)
- Preserve existing settings field names and data keys.
- Preserve output format for generated shortcode/PHP snippets.
- Preserve rendering behavior for icon choices and visibility positioning.

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
- Open Question: should a dedicated output-capture fixture format be standardized (HTML + screenshot vs raw HTML-only)?
