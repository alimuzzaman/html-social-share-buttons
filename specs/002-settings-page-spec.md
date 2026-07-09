# [SPECKIT-002] Spec: Settings Page Revamp (2.2.4-preserving behavior)

## Metadata
- **Spec Number:** SPECKIT-002
- **Spec ID:** SPEC-SETTINGS-REVAMP-2026-07-09
- **Title:** Settings page presentation-only revamp
- **Owner:** Product + UI + Engineering
- **Status:** Draft
- **Created:** 2026-07-09
- **Last Updated:** 2026-07-09
- **Version:** 1.1
- **Source of Truth:** Single file in `/specs/`

## 1.0 Context
### 1.1 Problem Statement
Improve settings-page clarity and sectioning while preserving all existing behavior and all user options. This is a presentation-first revamp.

### 1.2 Why this matters
Users rely on current settings and may have significant persisted values under `zm_shbt_fld`; preserving behavior avoids silent breakage.

## 2.0 Scope
### 2.1 In Scope
- Reorganize settings page visual layout into sections:
  - Header
  - Icon style
  - Display placement
  - Social networks
  - Advanced options
  - Code generator
- Update admin styling and JS interaction only as needed for layout/behavior improvements.

### 2.2 Out of Scope
- No option key/schema changes (`zm_shbt_fld` contract).
- No route or form submission changes.
- No front-end rendering logic changes.
- No rewrite to React/REST/block settings.

## 3.0 Functional Requirements
- FR-001: Settings page remains available at `admin.php?page=zm_shbt_opt`.
- FR-002: Save path remains `options.php` under setting group `zm_shbt_opt`.
- FR-003: All existing form field names remain as `zm_shbt_fld[...]` with same default handling.
- FR-004: Existing sanitize flow must continue to produce the same saved values for equivalent input.
- FR-005: Code generator modal and outputs remain available and unchanged in format.
- FR-006: Styling updates remain scoped to plugin settings page and avoid side effects to unrelated WP admin surfaces.
- FR-007: Settings page UI is rendered in React (wp-element) with the legacy field contract untouched (`zm_shbt_fld[...]`), including generated field names and code generator controls.
- FR-008: React settings implementation must stay compatible with the plugin's WordPress 5.0+ baseline; avoid relying on modern `wp.element` APIs that may be absent there.

## 4.0 Data and Contract Preservation
- Preserve these existing keys and nested shape in `zm_shbt_fld`:
  - `title`, `excludes`, `g_analytics`, `auto_hide_btn`, `use_port`, `nofollow`, `iconset`, `show_in.*`, `show_left`, `show_right`, `show_before_post`, `show_after_post`, `icons`, `iconset_type`.
- Preserve runtime compatibility behavior (including legacy `twitter` -> `x` migration).

## 5.0 UX Requirements
- Introduce clear section headings and logical grouping.
- Keep the same labels and helper text unless explicitly approved for wording edits.
- Keep modal code generation easy to find and one-click use.

## 6.0 Frontend Regression Protocol (Mandatory, Per-Change)
### 6.1 Before-change baseline capture (required)
For every proposed settings UI change, run all scenarios below and store expected outputs:
- default fixture (fresh install + defaults)
- all four display mode combinations
- iconset + iconset_type variations
- icon enable/disable permutations (minimum: all on / subset)
- nofollow on/off
- g_analytics on/off
- excludes active/inactive
- Store scenario set and base outputs via `tests/frontend-output-scenarios.json` and `tests/fixtures/frontend-output-baseline.json`.

### 6.2 After-change verification
- Re-run the exact same fixture set.
- Diff expected vs actual frontend output for each scenario.
- Approve only expected, intentional deltas before implementation can proceed.
- Use `tests/frontend-output-regression.php compare` for deterministic output checks.
- CI/local execution path: `make frontend-compare` (set `WP_ROOT`), then verify all mismatches before merge.

- The implementation now mounts the settings UI from `assets/admin-react.js` at `#zmsh-react-settings-root` to complete the React conversion while preserving backend form contract.

### 6.3 Acceptance
- [ ] No unapproved frontend output changes in existing scenarios.
- [ ] Settings page save/reload preserves values.
- [ ] Shortcut/modal output remains correct and syntactically unchanged for identical selections.
- [ ] `make admin-react-smoke` passes after every React settings UI change.
- [ ] `make settings-sanitize-contract` passes after every settings form/save change.
- [ ] `make frontend-drift-surface` passes for settings-only implementation work.

## 7.0 Risks and Hard Rules
- Hard rule: this spec accepts no breaking change to output for unchanged settings values.
- Hard rule: do not alter option schema without a dedicated migration section and separate schema section update in `SPECKIT-001`.

## 8.0 Open Questions
- Should we standardize output fixture format as HTML snapshot only, or HTML + screenshot pair?
- Should section heading text be localized now or in a later i18n cleanup pass?
