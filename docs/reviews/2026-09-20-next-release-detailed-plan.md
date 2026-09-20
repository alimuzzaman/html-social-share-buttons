# Detailed next-release plan: browser URL enhancement and shared JS warning

Status: implementation complete on the current worktree. The option, persistence
path, shared admin JS badge, eligible render marker, local frontend bundle,
structured URL descriptor, fallback behavior, distribution build, and focused
regressions are wired. Release remains gated on the real Sandbox PHPUnit run,
full browser acceptance evidence, and review of the final diff.

Planning model: Astra Medium, requested by the user.

## Outcome

Add an opt-in Advanced option named **Use browser URL for sharing** while keeping
HSSB server-first, local, accessible, and backward compatible.

The option is stored as `use_browser_url`, defaults to false, and adds a small
bundled frontend enhancement only for eligible page-level share links. The
server-generated link remains the fallback when JavaScript is unavailable,
blocked, malformed, or not safe to apply.

Every setting that enables public frontend JavaScript must show the same visible
`JS` badge. The badge must explain the public-JavaScript cost on hover, keyboard
focus, and touch. Future frontend-JS features reuse the same catalog entry
pattern and badge component.

## Non-goals

Do not include these in this release:

- Copy Link or native Web Share;
- new social networks;
- share counts or link shortening;
- hosted provider SDKs;
- analytics, click tracking, or telemetry;
- tracking-parameter cleanup;
- a universal canonical resolver rewrite;
- remote frontend assets or requests.

## Product and compatibility rules

1. Explicit custom URLs remain authoritative.
2. Explicit post targets and archive-loop post URLs remain authoritative.
3. Unknown or ambiguous caller provenance remains server-only.
4. The option is page-level enhancement, not a replacement for every rendered
   permalink.
5. JavaScript is local, dependency-free, and loaded only when an eligible link
   requests it.
6. Public links remain real anchors; never replace them with `href="#"` buttons.
7. Existing templates, hooks, legacy aliases, profile links, and output remain
   unchanged when the option is off.

## URL semantics

When enabled for an eligible page-level link, the browser value is exactly
`window.location.href`, including query parameters, repeated parameters, port,
encoded path, and fragment. Do not silently decode it, strip tracking fields,
replace it with Open Graph metadata, or merge it with a server URL.

The UI must warn that preview, query, and fragment data may be shared.

### Precedence

| Render context | Browser URL option |
| --- | --- |
| Explicit custom URL | Keep existing server URL |
| Caller-supplied `ShareContext` or explicit post target | Keep existing server URL |
| Archive/search/query loop post | Share that post; never use the container URL |
| Page-level floating controls | Eligible |
| Main singular page with inherited URL | Eligible after provenance is proven |
| Ambiguous widget, builder, or legacy call | Server-only |
| Admin, editor, AJAX preview, feed, REST | Server-only |
| Profile/contact link or template without permalink token | Unchanged |

The source-purpose descriptor must be captured before `RenderFacade` normalizes
empty or placeholder URLs. After normalization, explicit and inherited values
cannot be reliably distinguished.

## Settings and persistence

Add a trailing optional `useBrowserUrl` argument and `useBrowserUrl()` getter to
`src/Domain/Settings/Settings.php`; the trailing default preserves existing
positional callers.

Update together:

- `SettingsDefaults.php` — default false;
- `OptionSettingsCodec.php` — decode/encode stored key;
- `OptionSettingsRequestMapper.php` — canonical, regular-save, and replacement
  paths;
- settings request sanitation and truth-value handling;
- `SettingsPayloadBuilder.php` — explicit effective boolean for the admin app;
- `src/js/admin/settings/app.js` and `settings-model.js` — defaults and
  normalization;
- settings fixtures and compatibility contracts.

False should remain absent in sparse stored options. True should be stored as a
boolean. Missing old values stay off without a migration write. Reject arrays,
objects, arbitrary strings, and malformed values for this new field without
tightening unrelated historical truthiness behavior.

Preserve opaque extension keys, retired `g_analytics`/`use_port` data, network
aliases, profile settings, per-site isolation, rollback data, and serialized
shortcode/block compatibility.

## Shared JS badge

Create one data-only frontend-feature catalog, for example:

`src/Domain/Frontend/FrontendFeatureRegistry.php`

The first entry is:

```text
id: browser_url
setting: use_browser_url
requires_frontend_js: true
```

The catalog is the shared identity between the admin warning and the runtime
asset requirement. It is not a new broad public extension API.

Create one reusable admin component, for example:

`src/js/admin/settings/components.js` → `FrontendJsBadge`

Badge behavior:

- visible text: `JS`;
- accessible name: `Adds frontend JavaScript`;
- generic explanation: `Enabling this feature adds JavaScript to public pages on your site.`;
- browser-option explanation: `Uses the browser address for page-level sharing. Includes query parameters and fragments. Custom URLs and post-specific links keep their existing targets.`;
- visible focus ring;
- hover, keyboard focus, and tap/click support;
- Escape and outside/blur dismissal;
- stable tooltip ID with `aria-describedby`;
- warning included in the checkbox accessible description, not only the badge;
- badge never toggles the setting or submits the form;
- RTL-safe spacing, mobile wrapping, sufficient contrast, and long-translation safety;
- no reliance on HTML `title` alone.

`CheckboxInput`, `ToggleInput`, and future expandable controls should accept the
feature descriptor and render the same badge/header wrapper. Admin React’s own
JavaScript is not public frontend JavaScript and does not receive this badge.

Every future feature that adds public frontend JS must:

1. add a catalog entry;
2. reference its feature ID on every enabling control, including builder/block
   surfaces;
3. use `FrontendJsBadge`;
4. declare its runtime asset requirement;
5. add catalog/UI coverage proving warning metadata exists.

CSS-only and data-only features do not receive the badge.

## Share URL descriptor and runtime

Do not duplicate the seven-network endpoint map in JavaScript. Extend the PHP
resolution path to emit the ordinary resolved URL plus a structured enhancement
descriptor containing trusted literal segments and typed browser-permalink slots.

The descriptor must be derived from the final selected template after existing
template hooks. If a final-URL hook changes the result so the descriptor is no
longer trustworthy, keep that anchor server-only. Do not run existing final URL
hooks a second time with fake data.

Use a local entry such as `src/js/frontend/browser-url.js` with no jQuery,
React, WordPress editor modules, inline handlers, `eval`, or remote library.

The runtime should:

- read `window.location.href` at activation time;
- support pointer, keyboard, modifier, and middle-click paths without replacing
  normal navigation;
- observe `hashchange`, `popstate`, and `pageshow` where useful;
- remain idempotent and delegated for already-rendered eligible groups;
- restore the server URL if the browser value or descriptor becomes invalid;
- use an RFC3986 component encoder equivalent to PHP `rawurlencode()`;
- never use `encodeURI()` for query-component values;
- preserve existing Bluesky and mailto presentation behavior;
- set only `href`, never `innerHTML`.

Track the requirement in `RenderOutcome` and the request-local
`AssetCollector`. Enqueue one bundled script only when an eligible share anchor
exists. Default-off pages must emit neither descriptors nor the public script.
Cover automatic floating output, shortcode/block/widget/builder collection,
isolated legacy collectors, and footer timing.

## File ownership

| Area | Main paths |
| --- | --- |
| Settings/backend | `src/Domain/Settings`, `src/Infrastructure/WordPress/Settings`, feature catalog |
| Admin UI | `SettingsPayloadBuilder.php`, `src/js/admin/settings/{app,settings-model,settings-renderer,components}.js`, `assets/admin.css` |
| Rendering/runtime | `RenderFacade`, `RenderOutcome`, `RenderRequestMapper`, `HtmlRenderer`, `AssetCollector`, `FrontendController`, new frontend entry |
| Integrator | bootstrap wiring, provenance propagation, build files, generated assets/catalogs, docs, and tests |

The integrator owns shared integration files and final acceptance.

## Execution phases

### Phase 1 — contract and design

- Record HEAD, dirty state, and active ownership.
- Inspect all settings construction, save, render, adapter, and asset paths.
- Finalize the eligibility matrix and descriptor schema.
- Define the JS badge catalog contract and localized strings.
- Do not claim tests or acceptance yet.

### Phase 2 — complete implementation

- Finish settings persistence and admin UI.
- Add the shared badge and future-feature registration rule.
- Preserve source provenance before URL normalization.
- Add the descriptor, local runtime, asset collection, and server fallback.
- Add docs and generated build/catalog output.
- Keep all other feature work out of scope.

### Phase 3 — real workflow verification

In the authorized local WordPress Sandbox, exercise:

- enable/save/reload/disable;
- fresh, sparse, legacy, and malformed settings;
- page-level browser URL behavior;
- archive loops and explicit custom URLs;
- query strings, fragments, history changes, and invalid values;
- JavaScript disabled or blocked;
- badge hover, focus, touch, Escape, mobile, RTL, and long translations;
- captured frontend requests and final anchor destinations.

Correct observed defects before writing regression tests.

### Phase 4 — focused regressions and gates

After real workflow runs, add and run focused tests for observed behavior, then
execute repository-required quality, build, integration, browser, archive, and
distribution gates. Treat Sandbox readiness as infrastructure state, not test
success.

## Acceptance matrix

### Settings

- Fresh/sparse/legacy/malformed settings are off.
- Regular and AJAX saves enable and disable correctly after reload.
- Retired, opaque, extension, and per-site values survive.
- Existing settings schema and legacy mapper contracts remain valid.

### URL behavior

- Final PHP and browser-updated hrefs match for every built-in network.
- Cover Unicode, `&`, `?`, `#`, `%`, quotes, repeated parameters, existing
  encoding, mailto, and Bluesky.
- Explicit custom URLs, loop posts, profile links, final URL hooks, and legacy
  placeholder aliases remain unchanged.
- PHP and JavaScript share equivalent encoding vectors.

### Assets and fallback

- Option off: zero public runtime metadata/request.
- Option on and eligible: exactly one local runtime, no admin/editor dependency.
- Option on but only ineligible or hidden groups: no runtime.
- JavaScript disabled, blocked, missing, or invalid: normal server link works.
- No provider navigation is contacted during tests.

### Badge

- Badge is visible while setting is on or off.
- Hover, keyboard focus, and touch expose the same warning.
- Escape works; badge does not toggle or submit.
- Accessible description includes the warning.
- Mobile, RTL, and long translations do not clip.
- A second mock cataloged JS feature reuses the same component.
- CSS-only controls have no JS badge.

## Risks and limits

- Losing URL provenance during normalization can change custom or loop targets.
- Final URL hooks cannot safely be replayed in the browser.
- Browser URLs may expose preview, authentication, or tracking data.
- Footer timing, dynamic injection, modifier clicks, CSP, and old WordPress UI
  behavior need observed browser evidence.
- This plan is not implementation or release proof.

## Research basis

- [Consolidated competitor findings](../../research/wporg-share-plugins-2026-09-20/findings.md)
- [Luna review set A](../../research/wporg-share-plugins-2026-09-20/reviews/set-a.md)
- [Luna review set B](../../research/wporg-share-plugins-2026-09-20/reviews/set-b.md)
- [Short next-release plan](2026-09-20-next-release-plan.md)
