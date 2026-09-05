# Plugin update plan — Astra Medium

Date: 2026-09-05. Reviewed source: `b8bc5fd09decb026d7e75e4df43d7ae10c9bf9f5` (`master`). Input: Sol's full repository review, now preserved at `docs/reviews/2026-09-05-sol-repo-review.md`. This is a plan, not implemented or release-tested work.

## Outcome and scope

Ship a reliable settings experience and one new feature: **share-template preview with useful validation messages**. Fix existing behavior in separate changes first. Keep the canonical renderer, PHP 7.0 / WordPress 5.3 support, option names, extension hooks, legacy API, builder storage, asset handles, and existing HTML selectors. No architecture rewrite, new public tracking service, database migration, new network, or post-type expansion.

Recommend the next minor release for the combined feature and explicit deprecations; use `3.2.0` as a planning label only. Confirm the published version and tag availability before changing metadata. A fixes-only patch may be split out if the feature delays delivery; do not block a defaults fix on preview development.

## Corrections to the review

- **P1:** fresh-install settings disagree with effective public output; an unchanged save can change output. Fix before the next release.
- **P1 release-process defect:** tag publication is not gated by the separate compatibility workflow. This is a confirmed unsafe release path, not evidence that a bad release was published.
- **P2:** broken optional analytics. It is disabled by default and does not invalidate ordinary sharing. Retire the obsolete integration explicitly; do not present it as an unconditional P1 or claim a GA4 repair.
- **P2:** dead `use_port` control, inconsistent auto-hide, and build-tool advisories. Advisory counts describe Sol's observed lockfile audit, not shipped frontend vulnerability or a permanent count.
- **P3:** small spec contradictions.
- **Do not restore the fourteen-day soak.** `docs/STAGING-SOAK.md` says the owner waived it on 2026-08-13 and replaced it with manual exact-archive review. Preserve that replacement gate. Old runtime reports and fixture bytes provide context, never current acceptance evidence.

## Decisions

### Retire Google Social analytics and the port toggle

Stop emitting the plugin's `_gaq` script. Remove the active analytics toggle and replace it with a concise retired-feature note in Advanced settings. Google documents the Universal Analytics shutdown; repairing a local `_gaq` queue would not establish current analytics delivery. Do not add GA4, analytics consent behavior, network requests, or tracking scripts in this update. [Google's shutdown guidance](https://support.google.com/analytics/answer/11583528?hl=en)

Replace the `use_port` toggle with information that sharing uses WordPress canonical URLs. Keep `CurrentPostPermalink` resolution unchanged: explicit permalink override, post permalink, and existing WordPress `home_url` fallback remain authoritative. Never rebuild URLs from `HTTP_HOST`, `SERVER_PORT`, or forwarded headers. A configured canonical URL containing a port keeps that port regardless of the retired value.

Preserve both stored keys exactly on ordinary admin saves, including their absence, and retain codec fields, settings accessors, and existing legacy calls. Hidden/disabled form controls alone are insufficient: `OptionSettingsRequestMapper::toStoredReplacement()` currently deletes core-owned scalar fields before rebuilding them. Make the admin persistence path preserve retired fields from stored input explicitly; do not accidentally restore them from untrusted hidden fields. Keep existing legacy programmatic codec/API write contracts, documenting that values no longer control output. Cover both AJAX persistence and the Settings API sanitation route.

This is an explicit proposed compatibility exception to the analytics byte-preservation statement in `docs/REWRITE-COMPATIBILITY-DECISIONS.md`. Update that statement and the analytics golden fixture deliberately with the retirement change. All unrelated output remains compatible. Human release review must acknowledge the retirement; no secret access or release is authorized by this plan.

### Select one feature: template preview

The existing editor already has token insertion, parameter editing, and Restore defaults. Extend it with a **Preview sample** button and non-clickable resolved URL plus diagnostics; reuse the existing reset action. Benefit: admins can spot encoding errors or empty destinations before saving, without opening share services or adding public JavaScript.

| Candidate | Value | Cost and decision |
| --- | --- | --- |
| Template preview and validation | Directly improves an existing advanced control and supportability | Selected; admin-only, no new persisted setting. Reuse PHP resolution to prevent encoding drift. |
| Visible text labels | Better visual discoverability | Defer; alters every placement's markup and layout, with substantial icon/theme/builder coverage. |
| Custom post type controls | Useful for products and portfolios | Defer; requires new placement semantics and coordination across metabox, lookup, visibility, and existing sites. |

## Ordered implementation phases

### 1. Defaults and deprecated controls — small behavior changes

**Files:** `src/Presentation/Admin/SettingsPayloadBuilder.php`; `src/Bootstrap/PluginFactory.php` only for mapping dependencies; `src/Infrastructure/WordPress/Settings/OptionSettingsRequestMapper.php`; `src/Presentation/Admin/SettingsAjaxController.php`; `src/js/admin/settings/settings-renderer.js`; `src/Presentation/Frontend/FrontendController.php`; affected settings tests and analytics fixture; compatibility decisions and public copy.

Build visible core values from `$settings->load()` using the existing codec/legacy key mapping instead of another defaults table. Preserve raw extension values alongside those values. Keep “missing option” distinct from existing empty arrays, malformed scalar values, and partial arrays; the repository already makes this distinction. Do not change `SettingsDefaults`, activation behavior, or existing partial-option fallbacks simply to match the UI. Use canonical title values consistently; do not introduce a translated default that changes on an unchanged save.

Retire analytics and `use_port` as above in a separate reviewable commit. Leave public methods callable. Refresh generated admin bundles and localization artifacts through existing scripts after source changes.

**Acceptance:** for missing, empty, partial, malformed scalar, and full legacy options, every visible field matches loaded settings. Missing option shows left/after placements and Facebook/X/LinkedIn/Pinterest/mail enabled, Bootstrap Solid selected. Visiting settings writes nothing. An unchanged save preserves effective render and intended sparse/extension contracts. Retired values survive saves and upgrade/rollback; no plugin analytics script, console error, or tracking request appears with stored analytics true or false, with or without jQuery. Existing sharing remains functional.

### 2. Shared auto-hide behavior — bounded frontend correction

**Files:** `src/Presentation/Frontend/HtmlRenderer.php`, `src/Presentation/Frontend/AssetCollector.php`, `assets/frontend/button-appearance.css`, built-in pack CSS only where old offsets/transforms conflict; icon CSS generator/template if a touched file is generated. Tests: `tests/button-appearance-contract.php`, `tests/iconset-css-contract.php`, `tests/phpunit/FrontendAssetContractTest.php`, `tests/e2e/browser-matrix/iconsets.spec.js`.

Apply `hssb-rail--auto-hide` to both floating placements for every appearance, including Legacy. Ensure core rail CSS loads for Legacy too. Core owns conceal/reveal; neutralize old pack offsets and Prajin anchor movement under the core class so translations cannot compound. Keep existing classes and icon dimensions. With auto-hide off, preserve existing visible output; with it on, leave a 12px rail edge visible, reveal on hover and `:focus-within`, and keep it fully visible for coarse pointers or widths at/below 600px. Reduced-motion mode uses no transition and no concealment. Constrain movement to the rail's actual width, including rectangular packs; do not assume every pack is 44px wide.

**Acceptance:** all six built-in packs × four appearances × left/right × enabled/disabled pass computed visibility assertions. Tab and Shift-Tab reveal focused links fully; touch does not require a first tap to reveal. No horizontal overflow at 390px or 600px, no concealed focus, and no compounded movement. Non-floating rows retain layout. Golden changes are limited to intended auto-hide markup/CSS and analytics retirement; legacy API output contracts are reviewed separately from browser visibility.

### 3. Toolchain and release workflow — isolated maintenance

**Files:** `package.json`, `pnpm-lock.yaml`, `.github/workflows/compatibility.yml`, `.github/workflows/deploy.yml`; archive scripts/contracts only for verified API changes; new workflow contract test; release documentation.

Update direct `adm-zip` to a verified patched compatible version (advisory identifies `0.6.0`), then refresh the `@wordpress/scripts` dependency tree in a dedicated lockfile change. Verify registry metadata, upstream notes, Node requirements, and actual audit paths at implementation time. Do not blindly override transitive packages or raise plugin PHP/WP floors. Rebuild committed bundles, compare archive contents, and record justified remaining advisories by reachability and disposition. Sol's 44 findings are a review snapshot. [GitHub advisory](https://github.com/advisories/ghsa-xcpc-8h2w-3j85)

Convert compatibility into a reusable workflow with its existing branch/PR triggers retained, and remove its independent tag trigger. Tag workflow invokes that reusable workflow at the same commit with a `needs` chain. Include static quality, all compatibility rows, PHPUnit regular/AJAX/multisite, build/contracts, archive tests, Plugin Check and browser jobs in the required validation result. Build an immutable candidate ZIP; all archive runtime checks and final deployment consume that same ZIP. Reproducibility can use a second build only for byte comparison; never promote an untested rebuild.

Upload archive plus SHA-256/source SHA/version manifest. Final deploy downloads the exact artifact from the same run, validates hash and tag/header/stable-tag version, extracts it, and uses that directory with the existing pinned deploy action. Only deployment receives deployment secrets. A failed, cancelled, or skipped required check blocks publication; no `always()`/`continue-on-error` bypass. Gate publication on a protected release environment with owner approval; if the account cannot enforce required reviewers, fail closed and retain a separate explicit authorized manual publication step. Do not change remote environment/secrets while implementing source workflow changes. [GitHub dependency semantics](https://docs.github.com/en/actions/reference/workflows-and-actions/workflow-syntax), [deployment environments](https://docs.github.com/en/actions/concepts/workflows-and-actions/deployment-environments)

**Acceptance:** machine-check workflow graph and assert failure/skip paths cannot reach deploy; archive download/hash mismatch fails closed; no deploy-time install/build changes candidate bytes. Run no-secret CI failure injection before release authorization to demonstrate failed required jobs suppress publication. Required CI checks use exact commit and report terminal outcomes. Audit residuals are reviewed, not represented as frontend exploits. Stable/latest WordPress jobs record resolved versions; the existing 7.1 and 5.3 rows are boot/smoke checks where `test-library` is empty, not full PHPUnit coverage.

### 4. Add template preview — feature commit after settings contracts pass

**Files:** new `src/Presentation/Admin/ShareTemplatePreviewController.php` and focused preview service; `src/Bootstrap/PluginConfig.php`, `src/Bootstrap/PluginFactory.php`; `SettingsPayloadBuilder.php`; `src/js/admin/settings/template-editor-behavior.js`, `settings-renderer.js`; `assets/admin.css`; focused PHPUnit/AJAX/E2E tests. Reuse `ResolveShareUrl`, `ShareContext`, network registry, and existing sanitation. Keep new preview logic out of compatibility adapters.

Use a separate authenticated WordPress AJAX action `hssb_preview_share_template`, registered only with `wp_ajax_`, using the existing settings nonce action and `manage_options`. Submit one registered network ID and its unsaved template as strings; reject malformed or unknown network input and template payloads over 8192 bytes with HTTP 400. Resolve with fixed samples: `https://example.com/sample-post/?a=1&b=2`, title `Example title & café`, description `Example site description`, image `https://example.com/sample-image.jpg`. Display those samples in the UI. Never fetch URLs, access real/private post data, save options, or invoke third-party share endpoints.

Apply the same textarea sanitation used by save, then resolve through the canonical `ResolveShareUrl`. Preview is explicitly **before extension filters and with sample content**. Do not invoke runtime extension hooks during preview, since their behavior can depend on request context; make that limit visible. Detect supported `%%permalink%%`, `%%title%%`, `%%description%%`, `%%imageurl%%`, unknown tokens, and unresolved tokens; expose all four supported tokens in the editor's help. Empty template previews the registered default. A static valid destination is permitted and receives an informational message that it contains no content token.

Match the final URL treatment too: `HtmlRenderer::buttonUrl()` currently replaces Bluesky `%0A` with `%20` before `esc_url`. Extract that exact logic into one small presentation helper used by renderer and preview, with byte-parity tests; do not duplicate it in JavaScript or change existing output. Return a JSON text representation of the actual link destination (decode HTML entities only for text display). WordPress escaping can return an empty URL for disallowed protocols. Diagnose HTTP/HTTPS host absence, credentials, protocol-relative/relative URLs, unknown tokens, sanitizer changes, and unusual schemes; allow built-in `mailto:` with query parameters. Scheme/host advice must not become a new public rendering or save allowlist. [WordPress URL escaping](https://developer.wordpress.org/reference/functions/esc_url/)

**Response contract:** success envelope has `network`, `resolved_url`, `diagnostics` (array of stable `code`, `severity`, translated `message`), and `uses_default`. Valid request with unsafe/unusable template still returns successful analysis with error diagnostics; auth/shape failures use error responses. Render all values as text, never HTML or a clickable link. The UI has idle/loading/result/error states and a polite live region. Disable duplicate requests while pending; discard responses if network/template changed since submission. Existing Save and Restore defaults keep working even if preview fails. Validation is advisory in this release; it never deletes or blocks saving an existing custom template. Do not claim endpoint reachability or a successful social post.

**Acceptance:** resolved text equals the canonical rendered anchor destination for each built-in network and fixed samples, including Bluesky/mail and Unicode/ampersands; raw and percent-encoded placeholders are diagnosed; empty override uses default; unknown network, array input, oversize input, logged-out user, insufficient capability, and bad nonce are rejected. Authenticated preview leaves the option unchanged and performs zero external HTTP requests. XSS strings render inert. Slow stale responses never replace newer edits. Keyboard/screen-reader feedback works at desktop and 390px. Normal frontend asset requests and output remain unchanged by this feature.

### 5. Documentation and final candidate acceptance

**Files:** `specs/001-current-status-spec.md`, `specs/002-settings-page-spec.md`, `readme.txt`, `README.md` if present and relevant, `docs/REWRITE-COMPATIBILITY-DECISIONS.md`, `docs/RELEASE-CANDIDATE-VALIDATION.md`, focused new release evidence. Update only stale status/default/React statements and changed behavior. Keep historical evidence dated; no retroactive green claims. Update translations and compiled bundle metadata with repository scripts. Do not regenerate unrelated brand assets.

## Required test matrix

| Layer | Commands / scenarios | Required evidence |
| --- | --- | --- |
| Source and build | `composer validate --strict --no-check-publish`, `composer quality`, `pnpm run lint:js`, `pnpm run settings:check`, `pnpm run appearance:check`, both icon freshness checks; new workflow/preview contracts; existing staging probe/validator unit tests | Successful logs on final SHA; generated tracked output has no unexplained drift. |
| PHP compatibility | Syntax/autoload on PHP 7.0, 7.4, 8.0, 8.3, 8.5 | Each row's actual version and terminal exit. No new unsupported syntax/library requirement. |
| WordPress | 5.3/PHP 7.0 boot and focused feature/save smoke; existing 6.8 and 7.1 rows; latest stable/PHP 8.3; regular, AJAX and multisite PHPUnit where supported | Report smoke separately from PHPUnit. Latest resolves to explicit version. Missing library is blocked coverage, never pass. |
| Settings / data | New-install, legacy partial, malformed, full settings; retired keys absent/false/true; extension keys; two separate multisite sites; upgrade and rollback | Unchanged effective settings/render, no cross-site leakage, sparse/opaque data preserved. |
| Browser | Existing `tests/e2e/settings.spec.js`, new preview tests, `tests/e2e/browser-matrix/iconsets.spec.js`; Chromium, Firefox, WebKit at desktop and 390px; 600px boundary; keyboard, coarse pointer and reduced motion | Assertions and observed screenshots for changed UI; label actual Safari/Edge/device checks separately from emulation. |
| Integration | Existing shortcode, widget, dynamic blocks, Elementor and WPBakery suites; before/after content and floating rails; frontend output regression | Existing selectors/storage/hooks retained; no analytics script; URL/render parity. Paid builder limits documented under existing accepted fallback. |
| Distribution | Fresh isolated production Composer install; two `pnpm run zip` builds compared; `node tests/distribution-archive-contract.js`; fresh install of promoted ZIP; `pnpm run plugin:check` in supported test runtime | Matching ZIP SHA-256 and installed file manifest, no dev vendor files, zero new Plugin Check errors, warning dispositions reviewed. |
| Candidate manual gate | Exact ZIP settings desktop/mobile, template preview/save/reset, editor save/reload, frontend links/rails, browser console and bounded server logs | Candidate SHA/version/ZIP hash, observed versions, pass/fail evidence; no old soak requirement. |

Use existing Sandbox scripts only in an authorized bounded test environment; remote credentials, paid capacity, production or deploy actions require the user's existing or explicit authorization. Do not create a substitute local WordPress bootstrap that silently reduces the required matrix. Do not run destructive test teardown against an ambiguous existing instance.

## Dependencies, risks, and release gate

Phase 1 precedes the feature; phase 3 must gate any publication; phase 2 is independent but needs browser proof. Toolchain changes can alter every compiled bundle, so isolate their diff and rerun generated-asset and browser acceptance after integration. Theme CSS and third-party icon packs are the largest auto-hide risk: preserve override selectors and test one representative extension pack without claiming all extensions are proven.

Retirement conflicts with an explicit historical output promise; release notes and human review must acknowledge it. Preview adds an admin endpoint and a shared URL presentation helper: review authorization, escaping and no-write/no-fetch behavior before release. Advisory patch versions and latest WordPress are time-sensitive and must be refreshed at implementation time. Do not buy or access paid builder credentials to fill a test gap; preserve the documented fallback and its limits.

Release only after the final source is committed intentionally, all required jobs finish for that SHA, the exact candidate ZIP passes archive/manual checks, remaining warnings and compatibility exceptions have explicit dispositions, and the release owner authorizes publication. Reuse the reviewed ZIP and hash. Any packaged-byte change invalidates archive/runtime evidence. Keep a known prior release ZIP/hash for rollback; test option compatibility in a disposable site, never execute a rollback in production as part of this plan.

## Planning evidence and limits

Astra read Sol's report first and inspected current settings payload/defaults/repository/codec/request mapper/sanitizer/AJAX controller; template editor behavior and renderer; canonical resolver, hooked resolver and share context; frontend analytics, renderer/URL escaping and auto-hide assets; plugin composition/config references; package scripts; deploy and compatibility workflows; `docs/REWRITE-COMPATIBILITY-DECISIONS.md` and `docs/STAGING-SOAK.md`. The supplied personal AGENTS policy applies; Sol found no repository-local AGENTS file, and targeted discovery found none. Primary web checks above informed analytics retirement, advisory handling, URL semantics and deployment gating.

Astra ran source reads, searches, `git rev-parse HEAD` and `git status --short` only; no tests, builds, installs, credential access or remote mutations. HEAD remained the reviewed SHA. The parent's new `docs/reviews/` directory was visible as untracked and was preserved. Only `/tmp/hssb-astra-update-plan.md` was written by Astra. Sol's test results remain attributed to Sol; its blocked runtime checks remain unverified. Plan feasibility is source-grounded, not proof that the implementation or release passes.
