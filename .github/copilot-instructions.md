Copilot / Coding Agent Instructions

- Preserve existing rules:
  - Remember to use GitHub MCP for Git/GitHub related works in future.
  - Always use pnpm instead of npm.
  - Always prioritise @wordpress/* packages when they provide appropriate functionality or integrations for WordPress admin UI work.

Quick summary (big picture)
- This repository is a WordPress plugin with a PHP backend and a React-based admin UI under `src/admin-ui`.
- Build artifacts are emitted to `build/` and enqueued by PHP (`src/Admin/ReactAdminInterface.php`). The admin app is localized via `hssAdminConfig` (contains `pluginUrl`, REST endpoints, nonce, etc.).
- Frontend share button assets live under `assets/` (iconsets are uploaded/managed manually by the author). Blocks live under `src/Blocks`.

Developer workflows (must-know commands)
- Install deps: `pnpm install`
- Dev server (hot reload): `pnpm start` (uses `wp-scripts start`)
- Build for production: `pnpm run build` (uses `wp-scripts build`)
- Lint JS: `pnpm run lint` (wp-scripts lint-js), fix with `pnpm run lint:fix`
- Test unit: `pnpm test` (wp-scripts test-unit-js) — repo currently reports no JS unit tests; use `--passWithNoTests` when required in CI.
- E2E / Playwright: look at `playwright/` and scripts under `package.json` (e.g., `pnpm run test:e2e`).

Project-specific conventions & patterns
- Styling: Tailwind CSS (see `tailwind.config.js` and `src/admin-ui/index.css`). Use utility classes in components; avoid creating new global CSS unless necessary.
  - Preferred input/button patterns are documented in `tasks/tailwind-implementation.md`.
- Components: React components are organized under `src/admin-ui/components` with subfolders `ui/` and `tabs/`. Look at `FormFields.tsx`, `ValidatedFields.tsx`, `Tabs.tsx`, `ShareCountsTable.tsx` for canonical patterns.
- Data & integration:
  - REST calls use `rest_url('html-social-share/v1/')` and the admin app uses localized `hssAdminConfig` for endpoints and nonces (see `src/Admin/ReactAdminInterface.php`).
  - WordPress packages used extensively: `@wordpress/components`, `@wordpress/i18n`, `@wordpress/api-fetch`, `@wordpress/element`, etc. Prefer these for WP-specific functionality where appropriate.

Icon policy (explicit)
- Admin UI should prefer icons from `@wordpress/icons` when available.
- If a matching `@wordpress/icons` export is not available, fall back to `lucide-react` imports (tree-shakable; include only imports used).
- Do NOT add new generic icon libraries for iconsets that are plugin assets. The plugin author will upload hand-picked icons into `assets/iconset/` — access those at runtime via `hssAdminConfig.pluginUrl + 'assets/iconset/<set>/<name>.png'` rather than bundling them.
- **CRITICAL**: Frontend share buttons MUST load icons ONLY from `assets/iconset/` directory. NEVER use inline SVG, embedded icons, or any other icon sources for frontend display. Always ensure the IconRegistry loads PNG images from assets/iconset and renders them via CSS background-image, never as inline SVG elements.
- If frontend is showing SVG icons instead of PNG from assets/iconset, this is a BUG that must be fixed immediately. Check IconRegistry.php and ensure it's not falling back to builtin SVG mode.

Files & locations to inspect first (fast ramp)
- `src/admin-ui/` — React app and components (start here)
- `src/admin-ui/components/ui/` — shared UI primitives (FormField, Button, LoadingSpinner, AdminIcon)
- `src/Admin/ReactAdminInterface.php` — where assets are enqueued and `hssAdminConfig` is localized; critical for runtime paths
- `tailwind.config.js` and `src/admin-ui/index.css` — how Tailwind is configured and included
- `tasks/tailwind-implementation.md` — design system and Tailwind conversion decisions
- `assets/iconset/` — icon uploads (author-managed)
- `package.json` — build and test scripts (use pnpm)

Behavioral guidance for AI edits
- Preserve WordPress integration points: do not remove or rename localized keys (e.g., `hssAdminConfig`) or REST endpoints.
- When adding dependencies prefer `@wordpress/*` first; add `lucide-react` as a secondary, tree-shakable fallback only when necessary.
- For UI changes prefer adding Tailwind utility classes to components over new CSS files; match the patterns in `ui/` components.
- When changing public PHP hooks or REST route names, update their usages in `src/admin-ui` and the localization payloads consistently.

Testing & CI notes
- Unit tests are JS-only through `wp-scripts`; there are currently no JS unit tests by default.
- E2E tests use Playwright; review `playwright/` setup and `tests/playwright` scripts before adding tests.

Behavioral testing rule (TDD-first)
- Write unit tests first before writing code or implementing a feature. Prefer adding a failing unit test that asserts the desired behavior, then implement the minimal change to make the test pass. Update or add tests for regressions and edge cases when refactoring.

Example quick tasks (how you would perform them)
- Replace a WordPress class in UI with Tailwind: edit `src/admin-ui/components/App.tsx` and remove `wp-heading-inline` -> replace with `text-2xl font-semibold` (see `FormFields.tsx` for examples).
- Add a new admin icon: prefer `AdminIcon` wrapper in `src/admin-ui/components/ui/Icons.tsx` with candidates pointing to the likely `@wordpress/icons` export; pass a `lucide` element for fallback.
- Add a new iconset asset: upload the image to `assets/iconset/<set>/<network>.png`; the admin UI will load it via `hssAdminConfig.pluginUrl`.

When to ask maintainers for help
- If you need to add or change a localized key in PHP (e.g., new `hssAdminConfig` field), request confirmation — those values are relied on by runtime UI code.
- If you plan to add a third-party dependency, document size and reason, and get explicit approval — this repo prefers minimal deps.

If anything in these instructions is unclear or missing (for example you want a strict mapping from network IDs to WP icon export names, or a preferred iconset folder name), tell me which part to expand and I will update this file.
