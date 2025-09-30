---
mode: agent
---

Goal
- Convert the admin React UI to a pure Tailwind utility implementation while preserving existing functionality and WordPress integration points.

Scope (high-level)
- Replace WordPress-specific UI classes, color tokens and any @apply usage in the admin React app with Tailwind utility classes.
- Standardize form, input, button, tab and notice components to use a defined Tailwind pattern.
- Ensure icon strategy: prefer `@wordpress/icons` when available, otherwise use `lucide-react` (tree-shakable) and finally local SVG or packaged icon assets (loaded at runtime via `hssAdminConfig.pluginUrl`).

Files / areas to modify (explicit)
- Primary UI primitives: `src/admin-ui/components/ui/FormFields.tsx`, `ValidatedFields.tsx`, `Tabs.tsx`, `Notice.tsx`, `LoadingSpinner.tsx`, `Button` component.
- Main shell and layout: `src/admin-ui/App.tsx`, `ReactAdminInterface.tsx`, `MainAdminInterface.tsx`.
- Data-driven components: `ShareCountsTable.tsx`, `RefreshControls.tsx`, Tabs under `src/admin-ui/components/tabs/*`.
- Global CSS: `src/admin-ui/index.css` and `tailwind.config.js` (update content paths or color tokens if required).

Constraints & non-goals
- Do not rename localized keys or REST endpoints in PHP (`hssAdminConfig`, `rest_url('html-social-share/v1/')`, etc.). Update usages consistently if you must change a key and ask maintainers.
- Do not introduce large new dependencies. Prefer `@wordpress/*` packages; only add `lucide-react` as a small, tree-shakable fallback when needed.
- Icon assets under `assets/iconset/` are managed manually by the plugin author — do not bundle these; compose runtime URLs from `hssAdminConfig.pluginUrl`.

Developer workflows (commands to run locally)
- Install: `pnpm install`
- Dev server: `pnpm start`
- Build: `pnpm run build` (verify no CSS or TypeScript errors)
- Lint: `pnpm run lint`
- Tests: `pnpm test` (JS unit, repo has none by default) and `pnpm run test:e2e` for Playwright E2E where needed

Coding patterns and examples (do this, not that)
- Inputs: replace `wp-text-input` with `className="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"`.
- Buttons: use the standard primary/secondary/tertiary utility patterns in `tasks/tailwind-implementation.md`.
- Tables: replace `wp-list-table` with Tailwind table utilities (`w-full table-fixed border-collapse` + `bg-gray-50` headers and `divide-y divide-gray-200` rows).
- Icons: use `AdminIcon` wrapper to prefer `@wordpress/icons` exports (dynamically), fall back to `lucide-react` and then to a small local SVG component.

Testing & acceptance criteria
- The app compiles with `pnpm run build` without errors.
- No remaining `wp-*` presentation classes in `src/admin-ui` (search `wp-`), and Tailwind utilities used consistently across edited components.
- Visual parity for all previously existing admin pages (no broken layout or missing functionality).
- Accessibility: focus states and keyboard navigation preserved for tabs, buttons, and form elements.
- Tailwind CSS bundle remains small (target ~27–30KB optimized CSS).

Deliverables
- Implementation changes across the files above with minimal new dependencies.
- Short changelog entry in `tasks/tailwind-implementation.md` (update completed/remaining tasks).
- A short PR description that lists files changed, why, and how to test locally.

If anything in this prompt is unclear (for example, which exact WordPress UI classes to preserve for compatibility), ask for clarification before making breaking changes.