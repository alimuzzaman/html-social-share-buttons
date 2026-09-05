# HTML Social Share Buttons: full repository review

Planning handoff: [Astra Medium's accepted update plan](2026-09-05-astra-update-plan.md) follows this review. It corrects two recommendations below: optional analytics is P2, and the superseded fourteen-day staging soak is not a release gate. Use the plan's exact-archive replacement gate. The original Sol findings and check results are retained below for traceability.

Scope: read-only review of `/Users/alim/Sites/git/html-social-share-buttons` at clean `master` commit `b8bc5fd09decb026d7e75e4df43d7ae10c9bf9f5` (`Complete icon set share URL coverage`). Review date: 2026-09-05. The working tree stayed clean. No repository file, dependency, remote service, credential, tag, or deployment was changed.

## Verdict

The canonical PHP structure is clear, legacy adapters are thin, output escaping and admin authorization are generally sound, and all available static and contract checks passed. I found three release-blocking behavior/process defects, three medium-priority maintenance/accessibility defects, and low-priority spec drift. The WordPress-backed PHPUnit, browser, builder, multisite, Plugin Check, archive rebuild, staging, and deployment paths remain unverified in this review.

## Findings

### P1 — a version tag can publish before its checks finish or even when they fail

Evidence: `.github/workflows/deploy.yml:2-5` starts a deploy on every `v*` tag. Its only job installs dependencies, creates an archive, and invokes the WordPress.org deploy action (`.github/workflows/deploy.yml:10-47`). It does not run or depend on PHPUnit, PHPStan, PHPCS, JS lint, compatibility matrices, Plugin Check, or browser tests. Compatibility is a separate workflow that starts on the same tag (`.github/workflows/compatibility.yml:3-8`), so it cannot gate this job. `pnpm run zip` also covers only generated-icon freshness, production-autoloader verification, JS build, archive creation, and archive contents (`package.json:9-12`).

Impact: WordPress.org publication may complete for a tag whose separate compatibility workflow later fails. A reviewed local revision and green branch checks do not remove this race.

Action: make deploy depend on required checks for the exact tagged commit. The simplest robust shape is one tag workflow with test/quality/compatibility/package jobs and a final deploy job using `needs`. Preserve the archive produced and checked by the package job instead of rebuilding after the gate. Add a workflow contract test that proves deploy cannot start when a required job fails.

### P1 — optional Google Social Analytics does not reliably send an event

Evidence: `FrontendController::analyticsScript()` emits a `jQuery(document).ready(...)` handler but the frontend path does not enqueue jQuery (`src/Presentation/Frontend/FrontendController.php:381-386`; repository search found frontend enqueues only for styles and editor/admin integrations). Inside both function scopes it declares `var _gaq = _gaq || []`, which creates local arrays instead of using `window._gaq`. Every `switch` case lacks `break`, so all recognized cases fall through to `action = 'Share'`; `action` is undeclared and leaks globally; the handler also logs to the console. The golden fixture preserves only the emitted bytes (`tests/fixtures/frontend-output-baseline.json:102`). The repository's own source note says the fixture does not prove delivery to Google (`docs/WORDPRESS-ORG-TAGLINE-RESEARCH.md:89-102`).

Impact: with analytics enabled, themes without jQuery fail before binding. Themes with jQuery push into a local queue that Google Analytics cannot see, and recorded actions lose their intended labels. The setting and public copy imply optional analytics support that this implementation does not establish.

Action: first decide whether this legacy feature is still supported. If yes, replace it with a small dependency-free handler and an explicit supported analytics contract; write a browser-level test that supplies the expected global API and proves one click sends exactly one correctly labelled event. If compatibility requires `_gaq`, write to `window._gaq`. If support is retired, deprecate the option and stop advertising it rather than silently changing stored data.

### P1 — a fresh install renders enabled defaults while Settings shows them disabled

Evidence: when the option is missing, the runtime repository returns `SettingsDefaults::create()` (`src/Infrastructure/WordPress/Settings/OptionSettingsRepository.php:26-32`). Those defaults enable left and after-content placement (`src/Domain/Settings/SettingsDefaults.php:13-23`) plus Facebook, X, LinkedIn, Pinterest, and mail (`src/Domain/Settings/SettingsDefaults.php:30-38`). The Settings payload independently detects a missing option (`src/Presentation/Admin/SettingsPayloadBuilder.php:35-38`) but presents all four placements off and `icons` empty (`src/Presentation/Admin/SettingsPayloadBuilder.php:59-80`).

Impact: before the first save, public pages can show a left rail and after-content buttons while the admin UI says every placement and network is disabled. Saving the apparently unchanged screen turns that output off. This makes the first settings view unsafe and makes support reports hard to reproduce.

Action: use one canonical defaults mapping for runtime and admin payloads. Add a missing-option integration test that compares every visible Settings value to the loaded runtime `Settings`, then submits that unchanged form and proves the effective render is unchanged. Keep legacy decode behavior separate: an existing partial option still needs legacy-compatible fallbacks.

### P2 — `use_port` is a saved compatibility field with no runtime consumer

Evidence: `Settings` stores and exposes `preserveUrlPort` (`src/Domain/Settings/Settings.php:18,38,57,107-108`). The admin exposes `use_port` (`src/Presentation/Admin/SettingsPayloadBuilder.php:80`; `src/js/admin/settings/settings-renderer.js:383-388`), and the codec reads and writes it (`src/Infrastructure/WordPress/Settings/OptionSettingsCodec.php:73,128`). No production code calls `preserveUrlPort()` beyond mapping/storage. Current URL resolution uses `get_permalink()` or `home_url($requestUri)` and never receives Settings (`src/Infrastructure/WordPress/Rendering/CurrentPostPermalink.php:5-49`). Git history at `812eb42:html-social-share.php:315-326` shows the legacy function consulted `use_port` while assembling the request URL.

Impact: changing the control has no effect. This is both dead UI and a lost legacy contract. The old host/port construction should not simply be restored because canonical WordPress permalinks are safer and account for proxy/site configuration.

Action: define the intended canonical behavior before coding. Prefer keeping `get_permalink()` for posts and specify whether the toggle only affects the `home_url($requestUri)` fallback. Pass that policy explicitly into the permalink adapter and cover standard, non-standard, proxied HTTPS, explicit shortcode URL, AJAX editor, and missing-post cases. If the option has no defensible modern use, hide/deprecate it while preserving stored values and public compatibility accessors.

### P2 — Auto hide depends on icon pack and older packs reveal only on hover

Evidence: core emits `hssb-rail--auto-hide` only for a non-Legacy appearance (`src/Presentation/Frontend/HtmlRenderer.php:31-38`). Modern CSS reveals on `:focus-within` and on hover-capable devices (`assets/frontend/button-appearance.css:155-179`). Legacy Default, Flat, and Long Shadow hide rails with negative offsets and reveal only on `:hover` (`iconset/default/style.css:20-30`, `iconset/flat/style.css:20-30`, `iconset/long_shadow/style.css:20-30`); Prajin similarly uses hover-only movement on its right rail and left anchors (`iconset/prajin/style.css:13-36`). `AssetCollector` merely forces all rails visible when Auto hide is false (`src/Presentation/Frontend/AssetCollector.php:102-104`). Bootstrap Solid begins at `left:0/right:0` and has no pack-owned hide rule (`assets/iconsets/bootstrap-solid/style.css:1-6`), so Legacy + Bootstrap Solid does not hide when the setting is true.

Impact: the same setting has no effect for a common new-install icon pack, while older packs partly conceal keyboard-focusable links and do not reveal the rail on keyboard focus. Touch behavior also depends on old pack CSS.

Action: make core own auto-hide behavior for every pack and appearance. Add a generic class for all floating rails, preserve the old visual distance where required, reveal with `:focus-within`, disable concealment for coarse pointers/small screens and reduced motion as appropriate, and test every built-in pack in left/right, keyboard, touch, and 600px layouts. Treat the CSS change as a compatibility-visible change.

### P2 — the locked JS build toolchain has 44 known advisories

Evidence: `pnpm audit --audit-level=low` against the current lockfile exited 1 with 44 advisories: 29 high and 15 moderate. All declared Node packages are `devDependencies` (`package.json:33-39`), so this is a developer/CI/build-chain risk, not evidence of a shipped frontend vulnerability. One direct dependency, `adm-zip` 0.5.18 (`package.json:36`), is included in GHSA-xcpc-8h2w-3j85, whose audit record says `<0.6.0` is vulnerable and `>=0.6.0` is patched. Most remaining paths come through `@wordpress/scripts`.

Impact: crafted inputs or compromised CI context can reach vulnerable build/test tooling. The direct archive parser deserves early attention because release contracts inspect ZIPs. This audit result is current as of 2026-09-05 and will drift as registry advisories change.

Action: update the direct archive dependency and the `@wordpress/scripts` tree in a dedicated toolchain change. Review lockfile provenance; rerun lint, all contract tests, a clean production build, byte/content archive checks, PHPUnit, Plugin Check, and browser tests. Do not use a blanket audit override without mapping each remaining path to how this repository invokes it. Composer's locked audit was clean.

### P3 — current specs contradict shipped source and metadata

Evidence: the status spec labels candidate metadata `3.0.0` and the icon default `default` (`specs/001-current-status-spec.md:22-30,41-45`), while the plugin/README are 3.1.0 and source defines the new-install default as `bootstrap-solid` (`html-social-share.php:3-12`; `src/Domain/IconSet/IconSetSelectionPolicy.php:9-10`). The Settings spec says React is out of scope, then requires and describes the implemented React UI (`specs/002-settings-page-spec.md:36-42,51-52,116-118`).

Impact: future work can follow mutually exclusive requirements and restore obsolete behavior.

Action: update only the stale status/default/version and React scope statements. Keep the valuable option/output compatibility rules. Date the update and distinguish completed source behavior from old release/staging evidence.

## Subsystem coverage

| Area | Source inspected | Result |
|---|---|---|
| Bootstrap and composition | plugin entrypoint, `PluginFactory`, hooks, config, missing-autoloader path | Canonical service graph and guarded activation; no duplicate runtime owner found. |
| Domain/application | settings, networks, icon sets, rendering, placement/exclusion policies | Clear value flow; defaults divergence and dead port setting are findings. |
| Settings/admin | repository/codec/request mapper/sanitizer, AJAX, payload, React model/renderer, assets, metabox | AJAX requires nonce plus `manage_options`; inputs are normalized; unknown stored extension keys are preserved by codec. Fresh-install display is wrong. |
| Frontend | automatic placement, render facade, URL resolver, HTML renderer, assets, audience/exclusion gates | Share/profile URLs and HTML attributes are escaped; `_blank` links get `noopener noreferrer`; optional analytics is broken. |
| Integrations | dynamic blocks, shortcodes, widget, Elementor, WPBakery, generated PHP/legacy API | All route through the canonical facade; source and storage compatibility contracts exist. Runtime builder/editor behavior was not exercised. |
| Compatibility | global functions/classes, legacy icon imports, option aliases/hooks | Thin adapter boundary confirmed by source and contract check; no parallel core renderer found. |
| Assets/accessibility | six built-in packs, appearance CSS, local SVG/PNG generation, labels/focus rules | Bundled assets resolve locally; anchors have translated `aria-label`; auto-hide has pack and keyboard gaps. Extension icon sets may point to external URLs, so privacy wording correctly says bundled icons are local. |
| Security-sensitive paths | AJAX/settings auth, metabox nonce/capability, template/profile sanitization, output escaping, external icon registration | No confirmed exploitable PHP vulnerability found in static review. Runtime/plugin ecosystem and build-tool findings remain as stated. |
| Build/release/CI | Composer/pnpm metadata, scripts, archive builder/contracts, GitHub workflows, release docs | Good pinned Actions and archive checks; deploy gating and Node advisory backlog need work. |
| Tests/docs | static contracts, PHPUnit/E2E inventory, golden fixtures, specs/readmes/research | Broad contract coverage; WordPress-backed suite unavailable locally; several golden checks preserve bytes without proving browser behavior. |

Inventory: 474 tracked files, including 156 PHP and 47 JS files. No repository-local `AGENTS.md` or `SECURITY.md` was present; the supplied personal `AGENTS.md` governed the review.

## Checks run

Environment: Node 22.18.0, pnpm 11.5.2, PHP 8.5.8, Composer 2.10.1, PHPUnit 9.6.36.

- `git rev-parse HEAD`, `git status --short --branch`, and final `git diff --exit-code`: exact requested commit, branch `master...origin/master`, clean before and after.
- `git ls-files '*.php' | ... php -l`: PASS, all 156 tracked PHP files.
- `vendor/bin/phpstan analyse --configuration phpstan.neon.dist --memory-limit=1G --no-progress`: PASS, no errors.
- `vendor/bin/phpcs --standard=phpcs.xml.dist --report=full`: PASS, exit 0, no findings.
- `pnpm exec wp-scripts lint-js "src/js/**/*.js"`: PASS, exit 0, no findings.
- `composer validate --strict --no-check-publish`: PASS, `composer.json` valid.
- `composer audit --locked`: PASS, no PHP advisories.
- Static PHP contracts all PASS: `php tests/js-build-boundary-contract.php` (21 source modules, four runtime bundles), `php tests/javascript-localization-contract.php` (75 strings), `php tests/php-compatibility-contract.php`, `php tests/admin-settings-form-mapper-contract.php`, `php tests/share-template-contract.php`, `php tests/iconset-css-contract.php`, `php tests/exclude-contract.php`, `php tests/elementor-integration-contract.php`, `php tests/block-integration-contract.php`, `php tests/missing-autoloader-contract.php`, `php tests/settings-menu-contract.php`, `php tests/release-metadata-contract.php`, `php tests/compatibility-api-thinness-contract.php`, `php tests/architecture-boundary-contract.php`, `php tests/button-appearance-contract.php`, and `php tests/test-vulnerability-fixes.php`.
- Node/generator contracts all PASS: `node tests/admin-react-smoke.js` (43 legacy field names), `node tests/vc-scripts-smoke.js`, `node tests/staging-soak-probe.test.js`, `node tests/staging-soak-evidence-validator.test.js`, `node scripts/generate-iconsets.js --check`, and `node scripts/generate-legacy-x-assets.js --check`.
- `vendor/bin/phpunit --configuration phpunit.xml.dist`: BLOCKED before test discovery; exited 1 with `WP_TESTS_DIR must point to the WordPress test library. Run this suite through Sandbox.` No remote Sandbox action was authorized for this read-only pass.
- `php tests/frontend-output-regression.php`: BLOCKED before capture; exited 1 because WordPress bootstrap could not start in this checkout.
- `node scripts/verify-production-autoloader.js`: expected local failure because installed Composer dependencies are the development loader, not an authoritative production loader.
- `node tests/distribution-archive-contract.js`: failed against sibling `../html-social-share-buttons.3.1.0.zip`; that file predates the reviewed commit by about four minutes, contains 1,872 files/16,257,326 bytes, and includes development-only vendor content. It is a stale local artifact, not evidence about a release ZIP from current source.
- `pnpm audit --audit-level=low`: FAIL, 44 dev/build advisories (29 high, 15 moderate), as detailed above.

## Unverified runtime boundaries

- No WordPress runtime, database, multisite, AJAX request, migration, shortcode, block editor, Elementor, WPBakery, or theme was started.
- No PHPUnit assertions ran in this pass. Historical documents and fixtures were used only to understand intended coverage, not as proof that the current commit passes remotely.
- No Chrome/Firefox/Edge/WebKit/Safari/mobile keyboard or screen-reader test ran.
- No source build was run because it writes tracked `build/` output. No dependency was installed or updated.
- No production Composer autoloader or new archive was created. The stale sibling ZIP is unsuitable as current release evidence.
- No Plugin Check, staging soak, WordPress.org deploy, remote CI, external share-network request, or analytics delivery test ran.
- Built icon URLs were not fetched from the internet in this review. Local manifest/path contracts passed; reachability and third-party endpoint behavior remain time-sensitive runtime concerns.

## Priority backlog for the update plan

1. Align missing-option admin values with runtime defaults and add save-without-change integration coverage.
2. Decide the analytics support contract; repair or explicitly deprecate it, with a real browser event-delivery test.
3. Gate WordPress.org deployment on exact-commit required checks and promote the already-tested archive.
4. Define and implement/deprecate `use_port` semantics without restoring unsafe request-host reconstruction.
5. Move auto-hide behavior into core CSS and verify every pack with keyboard, touch, responsive, and reduced-motion cases.
6. Refresh the Node toolchain, beginning with direct `adm-zip`, then revalidate build/archive output and all runtime gates.
7. Repair the two small spec contradictions.
8. Only after these changes: run WordPress 5.3/PHP 7.0 floor, current WordPress/PHP, multisite, AJAX, full PHPUnit, Plugin Check, browser/builder matrix, clean production archive contract, and exact-archive staging soak before release review.

## Source-grounded new feature candidates

1. **Admin share-template preview and validation (recommended).** The current editor accepts sanitized text (`SettingsRequestSanitizer.php:57-82`) and runtime performs placeholder replacement (`src/Application/Rendering/ResolveShareUrl.php:15-31`), but authors cannot see the resolved sample URL or learn that a malformed template may escape to an empty link. Add an admin-only preview using fixed sample title/permalink/image data, supported-token checks, scheme/host feedback, and reset-to-default. This keeps public output server-rendered and adds no frontend JavaScript.
2. **Visible text labels as an appearance option.** Current share anchors are image-backed and empty, with accessible names supplied by `aria-label` (`specs/001-current-status-spec.md:75-82`). A server-rendered icon-plus-label mode would improve visual discoverability and low-vision use. Make it additive, use translated network labels, keep current modes byte-compatible, and cover wrapping in each placement/integration.
3. **Custom post type placement and exclusion support.** Automatic singular rendering can reach more content types, while the exclusion search and per-content metabox are hard-coded to posts/pages (`src/Presentation/Admin/ExcludedContentLookup.php:34-46,66-84`; `src/Presentation/Admin/MetaboxController.php:24-35`). Add a settings-level supported-post-type selector based on public types, default it to current behavior, and apply it consistently to automatic placement, search, and metabox registration. It requires no frontend JavaScript and fits WordPress sites using products, portfolios, and other public content.

These candidates are plans only. Astra should first resolve the P1 contract choices, then select one feature by user value, compatibility cost, and the runtime proof available.
