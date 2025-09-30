---
mode: agent
---

Goal
- Audit the admin UI implementation against docs/13-Current-Options-Reference.md, identify missing or partially-implemented options, and produce a prioritized implementation plan (with concrete code changes) to fully implement and persist all documented options.

Scope (high-level)
- Inspect the admin React app under `src/admin-ui/components/tabs/*`, the settings hook `src/admin-ui/hooks/useSettings.ts`, and relevant types and hooks that surface plugin options.
- Implement missing persistence and API wiring for any options that are currently only managed in local component state.
- Report mismatches between the docs and UI option sets (e.g., different enumerations) and propose a remediation.

Files / areas to inspect and modify (explicit)
- Primary inspection targets:
  - `docs/13-Current-Options-Reference.md` (source of truth)
  - `src/admin-ui/components/tabs/GeneralTab.tsx`
  - `src/admin-ui/components/tabs/NetworksTab.tsx`
  - `src/admin-ui/components/tabs/ProfilesTab.tsx`
  - `src/admin-ui/components/tabs/IntegrationsTab.tsx`
  - `src/admin-ui/components/tabs/AppearanceTab.tsx`
  - `src/admin-ui/components/tabs/PlacementTab.tsx`
  - `src/admin-ui/components/tabs/AdvancedTab.tsx`
  - `src/admin-ui/components/tabs/ShortcodeTab.tsx`
  - `src/admin-ui/hooks/useSettings.ts` (load/save wiring)
  - `src/admin-ui/hooks/useNetworks.ts` and the `useRestApi` helpers (if present)
  - `src/admin-ui/types/index.ts` (types to confirm fields)

Current-state summary (quick audit against docs)
- General Tab
  - Implemented: show_on_front_page, show_on_posts, show_on_pages, show_on_archives, default_size
  - Note: `default_style` options include `minimal` in the UI, while the docs list `outline`; this is a documented/enum mismatch.
- Networks Tab
  - Implemented UI: enabling/disabling networks, drag-and-drop ordering (local state), per-network label editing, and a Custom Networks form.
  - Missing/partial: custom networks and network order are managed in component-local state and are not persisted via `useSettings.saveSettings` or `updateSettings`.
- Profiles Tab
  - Implemented UI: profile CRUD UI and Default Profile selector control (local state).
  - Missing/partial: profiles array and default_profile are not persisted to the settings API (ProfilesTab uses local state, and `useSettings.saveSettings` does not include profiles/default_profile in its payload).
- Integrations Tab
  - Implemented: toggles and API key fields for BetterLinks and page builders; appears wired to settings context.
- Appearance Tab
  - Implemented: title, icon_style, button_size, button_spacing, custom_css — wired to settings context.
- Placement Tab
  - Implemented: auto_placement, placement_position, placement_post_types, exclude_pages — wired to settings context.
- Advanced Tab
  - Implemented: google_analytics, auto_hide_buttons, use_port_in_url, nofollow_links, cache_enabled, cache_duration, debug_mode — wired to settings context.
- Shortcode Tab
  - Implemented dynamic shortcode generation UI and parameters.

Top-priority missing items (recommend implementing these first)
1. Persist profiles and default_profile
   - Ensure `useSettings.loadSettings` loads `profiles` and `default_profile` from the API response and that `saveSettings` sends them back to the server.
   - Update `ProfilesTab.tsx` to read/write `profiles` from `useSettings` (use `updateSetting` / `updateSettings`) and call `saveSettings()` or mark as dirty for the global settings save UX.
2. Persist Custom Networks & Network Order & Enabled Networks
   - Make `NetworksTab.tsx` update `settings.custom_networks`, `settings.enabled_networks` and `settings.network_order` via `updateSettings` or the `updateSetting` helper, then call `saveSettings()` (or coordinate with `useNetworks` API when present).
3. Save and load flows consistency
   - `useSettings.saveSettings` currently omits `profiles` and `default_profile` from the POST payload. Add these keys to `apiData` and ensure the server API accepts them (if the REST API is strictly typed, ask the maintainer or add a backward-compatible shape).
4. Reconcile enum mismatch
   - Decide whether `minimal` should be renamed to `outline` (or vice-versa). Update docs or UI enums for consistency.

Suggested concrete code changes
- `useSettings.ts`
  - Include `profiles` and `default_profile` in both the `loadSettings` flattening and the `saveSettings` POST payload under appropriate keys (e.g., `profiles` and `default_profile` or under `profiles: { items: [], default: '' }` depending on API contract).
- `ProfilesTab.tsx`
  - Replace local-only `profiles` and `defaultProfileId` usage with `settings.profiles` and `settings.default_profile` via `useSettings`.
  - Use `updateSetting('profiles', updatedProfileList)` and `updateSetting('default_profile', id)` and call `saveSettings()` (or a batched save handled elsewhere).
- `NetworksTab.tsx`
  - When custom networks are added/removed, call `updateSettings('custom_networks', newArray)`.
  - When enabledNetworks or order changes, call `updateSettings('enabled_networks'|'network_order', updatedArray)`.
  - Ensure `saveSettings()` is invoked or provide a consistent "Save" UX that triggers `saveSettings` in `useSettings`.

Constraints & non-goals
- Do not change REST endpoint names or localized keys (e.g., `hssAdminConfig`) without explicit approval from maintainers.
- Prefer minimal API contract changes — try to use existing REST endpoints and fields; if a server-side change is required to accept `profiles`, open an issue/PR for the backend prior to client changes.
- Do not introduce large new third-party dependencies.

Developer workflows / commands
- Install deps: `pnpm install`
- Run dev UI: `pnpm start`
- Build: `pnpm run build`
- Lint & fix: `pnpm run lint` and `pnpm run lint:fix`
- Run full save/load integration locally against a WP dev environment that exposes `/html-social-share/v1/settings` endpoints.

Coding patterns and examples
- Use the settings hook API:
  - Read: `const { settings, updateSetting, saveSettings } = useSettings();`
  - Update profiles: `updateSetting('profiles', newProfilesArray);`
  - Update default profile: `updateSetting('default_profile', profileId);`
  - Persist: `await saveSettings();`
- When updating arrays like enabled networks and network order, always update the canonical `settings` object via `updateSetting('network_order', orderArray)` rather than only updating local component state.

Testing & acceptance criteria
- The UI compiles successfully: `pnpm run build` (no TypeScript errors).
- The finish state for all documented options is persisted across page reload via the settings API.
- `profiles` and `default_profile` appear in the POST payload when `saveSettings()` is invoked (verify in devtools/network).
- `custom_networks`, `network_order` and `enabled_networks` persist and are reflected in the UI after reload.
- No breaking changes to existing REST payloads unless coordinated; if the server requires a schema change, the PR must include a backend change or a maintainer approval note.

Deliverables
- Audit report (this prompt) plus a minimal set of code changes: 
  - `src/admin-ui/hooks/useSettings.ts` (save/load profiles/default_profile)
  - `src/admin-ui/components/tabs/ProfilesTab.tsx` (use settings hook and persist)
  - `src/admin-ui/components/tabs/NetworksTab.tsx` (persist custom networks + order + enabled list)
- A short PR description and a recommended testing checklist.

If anything here is unclear (for example, the exact REST API shape expected for `profiles`), request clarification before making server-side changes.