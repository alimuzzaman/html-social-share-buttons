---
mode: agent
---
You are an autonomous engineering agent responsible for performing a full, end-to-end codebase review and delivering fixes across this repository. Follow the steps below exactly and produce changes as described.

Goals
- Identify all functional, security, and test issues in the codebase.
- Create reproducible failing unit tests (TDD-first) for each bug/regression you intend to fix.
- Implement minimal, well-tested fixes that make the tests pass.
- Keep changes small, well-documented, and fully committed with clear messages.

Scope
- Review PHP backend, WordPress REST endpoints, and React admin UI in `src/admin-ui/`.
- Verify REST schemas, sanitization, capability checks, and localization keys used by the admin UI.
- Verify that the React types in `src/admin-ui/types` and the `useSettings` hook match the REST API payloads and defaults.
- Review integration code for page builders and external plugins (Elementor, WPBakery, Divi, BetterLinks, etc.) and ensure graceful behavior when those plugins are not present.
- Run static checks (basic grep/semantic search), expand REST schema validation where missing, and add unit tests for REST controllers or pure PHP utility classes where practical.

Constraints
- Use the repository's existing testing framework (phpunit for PHP and wp-scripts/jest for JS). Add unit tests before code changes following the TDD-first rule.
- Prefer backward-compatible fixes — avoid breaking public REST routes, option keys, or localized payload keys unless the change is explicitly documented in the commit message.
- Do not add heavy new runtime dependencies. Small dev-only test helpers are acceptable.
- When modifying admin UI, prefer adding or updating TypeScript types and tests rather than changing runtime API keys or localization keys without maintainers' consent.

Acceptance criteria
1. All newly added unit tests for reported failures must initially fail, then pass after the corresponding fix is made.
2. REST endpoints should have explicit, comprehensive schema definitions for their expected payloads and should sanitize inputs consistently.
3. The `useSettings` hook must correctly map REST payloads to the frontend `PluginSettings` type, and `saveSettings` should send fields matching the REST schema.
4. Integration modules should check for their host plugin gracefully and register their hooks only when the host plugin is present; tests should assert that registration is skipped when the host plugin is absent.
5. Create a short CHANGELOG entry at `CHANGELOG.md` listing the fixes made (one-line per fix).

Deliverables (for each fix)
- One or more unit tests demonstrating the failure (placed in an appropriate tests/ directory).
- The minimal implementation change to make tests pass.
- A concise commit that includes the test and the fix; commit message must follow Conventional Commits (e.g., `fix(rest): validate settings schema in SettingsController`).
- A one-paragraph PR-style summary of the change in the commit body.

Process
1. Run a codebase scan to collect candidate issues (use semantic search and grep to find TODOs, mismatches between REST and UI, missing schema, missing sanitization, and missing integration guards).
2. Prioritize high-impact, low-effort issues first (security, data loss, REST validation, type mismatches), then address integration and UX gaps.
3. For each issue addressed:
   - Add a failing unit test.
   - Implement the fix.
   - Ensure tests pass.
   - Add a short CHANGELOG entry.
4. Commit each change as a focused commit. Push is not required.

When you finish
- Provide a summary list of the issues you found and fixed (one-line per fix, file references), and the commit SHAs for each fix.
- If any issue could not be fixed automatically (requires design decisions or maintainer input), list it clearly and explain why it needs manual review.

You may now begin the repository-wide review, following the TDD-first approach described above.