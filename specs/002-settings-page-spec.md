# [SPECKIT-002] Spec: Settings Page Revamp (3.1-preserving behavior)

## Metadata
- **Spec Number:** SPECKIT-002
- **Spec ID:** SPEC-SETTINGS-REVAMP-2026-07-09
- **Title:** Settings page revamp and configuration controls
- **Owner:** Product + UI + Engineering
- **Status:** Draft
- **Created:** 2026-07-09
- **Last Updated:** 2026-07-12
- **Version:** 2.9
- **Source of Truth:** Single file in `/specs/`

## 1.0 Context
### 1.1 Problem Statement
Improve settings-page clarity and configuration workflows while preserving the existing saved-value contract. The page must make exclusions and platform share templates directly manageable without requiring users to edit opaque comma-separated values or source code.

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
- Replace the plugin top-level admin menu with a Settings submenu while preserving the existing page slug.
- Update the Plugins page Settings action link to target the Settings submenu page.
- Add searchable page/post selection for exclusions, while preserving the existing custom exclusion input and comma-separated storage format.
- Add per-platform share template controls that expand only for enabled platforms.

### 2.2 Out of Scope
- No destructive option key/schema changes. New `share_templates` data must be additive, default to canonical templates, and remain backward compatible with existing `zm_shbt_fld` values.
- No route or form submission changes.
- No front-end rendering logic changes.
- No rewrite to React/REST/block settings.

## 3.0 Functional Requirements
- FR-001: Settings page remains available at `admin.php?page=zm_shbt_opt`.
- FR-002: Save path remains `options.php` under setting group `zm_shbt_opt`.
- FR-003: All existing form field names remain as `zm_shbt_fld[...]` with same default handling.
- FR-004: Existing sanitize flow must continue to produce the same saved values for equivalent input.
- FR-005: Code generator modal and outputs remain available and unchanged in format.
- FR-006: Styling updates remain scoped to plugin settings page and avoid side effects to unrelated WP admin surfaces. The settings stylesheet and script must use their file modification time as an asset version so UI releases do not combine fresh markup with stale CSS.
- FR-007: Settings page UI is rendered in React (wp-element) with the legacy field contract untouched (`zm_shbt_fld[...]`), including generated field names and code generator controls.
- FR-008: React settings implementation must stay compatible with the plugin's WordPress 5.0+ baseline; avoid relying on modern `wp.element` APIs that may be absent there.
- FR-009: Settings page is registered as a submenu under WordPress Settings, not as a top-level menu.
- FR-010: The Plugins page Settings action links to `options-general.php?page=zm_shbt_opt`.
- FR-011: Exclusions provide a searchable multi-select for published pages and posts, with results loaded through an authenticated admin request and rendered with WordPress React components.
- FR-012: Exclusion selection stores selected post IDs in the existing comma-separated `zm_shbt_fld[excludes]` value.
- FR-013: Exclusions use one WordPress `FormTokenField` combobox. Published pages/posts appear as searchable suggestions, and free-form values entered with Enter or comma become selected custom tokens in the same field.
- FR-014: Existing custom exclusion values are rendered as selected tokens in the same combobox and remain editable/removable without a separate mode or textarea.
- FR-015: Social Networks lists every built-in platform, including opt-in Telegram and Bluesky buttons. On desktop, the cards render in two independent, source-ordered vertical columns to avoid shared-row-height gaps; on mobile, the same order collapses to one column. When a platform is enabled, its share-template editor expands beneath it; disabled platforms do not show template controls. The editor keeps the canonical URL prefix read-only as static code context, rather than as a writable or disabled form field, and displays existing query parameters as rows in one textarea-like rich-text surface with non-editable parameter names and inline-editable values. Custom text renders as ordinary text; supported placeholders render as subtly distinguished inline syntax rather than chips, buttons, or separate inputs. Typing `%%` opens a caret-anchored autocomplete menu, with keyboard navigation and insertion at the caret. The editor distinguishes saved overrides from canonical defaults and offers a layout-stable reset-to-default action.
- FR-016: Platform templates persist as an additive `zm_shbt_fld[share_templates][<platform>]` map, fall back to canonical templates when unset, and continue to support existing placeholder tokens.
- FR-017: Template values are sanitized as text while preserving supported placeholder tokens and are applied by the frontend share-link renderer. Telegram defaults to `https://t.me/share/url?url=%%permalink%%&text=%%title%%`. Bluesky defaults to `https://bsky.app/intent/compose?text=%%title%%%0A%%permalink%%` and is presented as its single documented `text` parameter.
- FR-018: Display placement and Social Networks use one reusable expandable toggle-panel component and one card anatomy. Every card header contains a 48px visual marker, title, one-line purpose, and right-aligned state switch; network markers use the active icon set while placement markers diagram the destination. A disabled item shows only its header and toggle; enabling it reveals its existing detail panel directly beneath the header without changing the card background or saved option contract.
- FR-019: Every built-in platform is represented by a local icon asset in each supported icon-set shape. New platform marks must match the established artwork scale and the host set's exact treatment: Default uses its inset dotted frame, Flat uses unshadowed square/circle color blocks, Long Shadows casts the mark down-left within the button shape, Prajin square uses a short softened down-right cast, and Prajin circle remains unshadowed.

## 4.0 Data and Contract Preservation
- Preserve these existing keys and nested shape in `zm_shbt_fld`:
  - `title`, `excludes`, `g_analytics`, `auto_hide_btn`, `use_port`, `nofollow`, `iconset`, `show_in.*`, `show_left`, `show_right`, `show_before_post`, `show_after_post`, `icons`, `iconset_type`.
- Additive key: `share_templates.<platform>` for built-in platforms only; missing values resolve to canonical defaults. New `telegram` and `bluesky` icon flags are additive and disabled by default.
- Preserve runtime compatibility behavior (including legacy `twitter` -> `x` migration).

## 5.0 UX Requirements
- Introduce clear section headings and logical grouping.
- Keep the same labels and helper text unless explicitly approved for wording edits.
- Keep modal code generation easy to find and one-click use.
- Exclusion search must support both pages and posts, show title/type context, and allow multiple selections.
- Custom exclusion values must be visually indistinguishable as selected tokens while remaining editable/removable in the same combobox.
- Platform template controls must be grouped with the corresponding platform toggle and expand without shifting unrelated sections incoherently.
- Display placement items must use neutral WordPress admin surfaces in both enabled and disabled states. The enabled state is conveyed by the native toggle and revealed controls, never by a tinted background or an extra-thick outline.
- Expanded placement and social-network items must visually join their toggle header and detail panel: the shared component owns the corner-radius, padding, and continuous 2px Administration Color Scheme light-accent rail, so the header and detail panel begin directly adjacent without a white gap or doubled border. Collapsed placement and social-network items retain the same neutral header height, identity hierarchy, surface, spacing, and 2px accent rail; the narrower rail keeps them subordinate to the 3px top-level section border.
- Every top-level settings section uses the Administration Color Scheme light accent as a restrained 3px left border, matching the page header hierarchy while preserving a neutral panel surface.
- Display placement uses explicit independent desktop stacks: Left side and Before post on the left, Right side and After post on the right. Expanding a placement never creates a blank vertical gap beneath a collapsed item in the neighboring column. On mobile, the placement items collapse to one column.
- Social Network cards must use two independent vertical stacks at desktop widths, so a taller template card does not create empty space below a neighboring card. Their DOM, keyboard, and visible order must remain aligned; the stacks collapse to one column at mobile widths.
- All nested toggle-panel headers and expanded details use the existing Administration Color Scheme light accent as a continuous 2px left border, matching the page hierarchy while keeping the panel background neutral.
- Platform template editors must show the canonical URL as contextual default guidance rather than implying it is a saved override. Resetting a customized template must clear only that platform's additive override and restore runtime fallback behavior.
- Platform template editors must display the URL prefix through the question mark as a non-editable, code-style context block rather than a text input, and let users edit only query-parameter values. Existing parameters render as rows in one textarea-like editor surface, with a read-only parameter-name column and borderless inline-editable values, so users do not need to type parameter names, equals signs, or separators. The editor uses a neutral WordPress form border at rest; only focus and active autocomplete states use the current WordPress Administration Color Scheme accent.
- Custom parameter text must remain visually continuous and directly editable. Supported placeholders (`%%permalink%%`, `%%title%%`, and `%%imageurl%%`) must blend into that text with a subtle tint and underline, without token pills, removal buttons, or per-placeholder input borders.
- Typing `%%` in an editable parameter value must open a suggestion menu positioned directly beneath the caret. The menu must support Arrow Up/Down, Enter, and Escape, show friendly placeholder labels plus their stored syntax, insert the selected placeholder at the caret, and expose an accessible combobox/listbox relationship. `Ctrl+Space` may reopen suggestions without modifying text. Opening suggestions after every space is explicitly out of scope.
- Existing or pasted supported placeholder syntax must be recognized and receive the same inline treatment. The submitted value must reconstruct the existing full template URL format without changing parameter order or the saved placeholder syntax.
- The Settings submenu and Plugins action link must use the same `zm_shbt_opt` page slug.

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

- The implementation mounts the settings UI from the compiled
  `build/admin-react.js` bundle at `#zmsh-react-settings-root`; its modular
  source lives under `src/js/` and preserves the backend form contract.

### 6.3 Acceptance
- [ ] No unapproved frontend output changes in existing scenarios.
- [ ] Settings page save/reload preserves values.
- [ ] Existing custom exclusions open as selected tokens in the single combobox and remain unchanged after save when not edited.
- [ ] Search-selected page/post exclusions serialize as comma-separated IDs in `zm_shbt_fld[excludes]`.
- [ ] Enabled platform template edits save and affect generated share URLs; disabled platform templates are not shown.
- [ ] Social Network cards have no shared-row-height gaps on desktop and collapse to one source-ordered column on mobile.
- [ ] Expanded template panels show the scheme-aware left accent border without changing parameter-editor contrast or layout.
- [ ] Canonical platform templates appear as default guidance, while saved overrides remain editable and can be reset per platform.
- [ ] The Share URL prefix is presented as non-editable code context, never as a writable or input-like control.
- [ ] Placeholder insertion preserves the supported `%%permalink%%`, `%%title%%`, and `%%imageurl%%` tokens.
- [ ] Typing `%%` opens the placeholder menu directly beneath the caret; Arrow Up/Down, Enter, Escape, and `Ctrl+Space` behave as specified without interrupting ordinary space-separated text entry.
- [ ] Custom text remains plain inline text, while existing, pasted, and newly inserted placeholders receive the same subtle non-chip treatment.
- [ ] The parameter surface uses a neutral border at rest and the active WordPress Administration Color Scheme only for focus and selection states.
- [ ] Editing a parameter value preserves its read-only parameter name, the order of other parameters, and the full URL serialization contract.
- [ ] Enabling a display placement reveals only its existing button-shape control beneath the neutral toggle header; disabling it hides that control without changing its saved value.
- [ ] Expanded placement and social-network detail panels have no visible gap or rounded-corner seam between their header and body.
- [ ] Display placement and Social Networks render through the same expandable toggle-panel component, including their collapsed header treatment.
- [ ] An expanded placement does not create an empty same-row gap below a collapsed placement in the neighboring desktop column.
- [ ] Each top-level settings section has the scheme-aware 3px left accent border.
- [ ] Settings submenu and Plugins action link resolve to the settings page.
- [ ] Shortcut/modal output remains correct and syntactically unchanged for identical selections.
- [ ] `make admin-react-smoke` passes after every React settings UI change.
- [ ] Sandbox `run_tests` passes after every settings form/save change, including the `WP_UnitTestCase` save-contract coverage.
- [ ] `make frontend-drift-surface` passes for settings-only implementation work.

## 7.0 Risks and Hard Rules
- Hard rule: this spec accepts no breaking change to output for unchanged settings values.
- Hard rule: do not alter option schema without a dedicated migration section and separate schema section update in `SPECKIT-001`.

## 8.0 Open Questions
- Should we standardize output fixture format as HTML snapshot only, or HTML + screenshot pair?
- Should section heading text be localized now or in a later i18n cleanup pass?
