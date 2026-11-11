# Next release research: native sharing and PHP compatibility

Date: 2026-11-11. Source baseline: `master` / `v3.4.0`, commit
`4742dae6de784c66ad1c768b2148b0b6e1c6a338`.

Status: research and proposed scope. No runtime changes, version bump, or
publication performed. Research was delegated to three Luna agents covering
competitors, blogs/social discussion, and PHP compatibility; the parent reviewed
the relevant sources and ran the local checks below.

## Recommendation

Use **3.5.0** as a planning label for one public feature: an optional native
**Share…** action, with a dependable Copy link/direct-link fallback. Combine it
with better PHP runtime coverage and current release documentation. Keep the
existing no-JavaScript default and all existing URL precedence rules.

This is a product-fit hypothesis, not demonstrated customer demand or a promise
of increased shares. HSSB already has Copy link and most common destinations.
Native sharing lets visitors choose their installed applications without HSSB
maintaining an ever-growing set of integrations. It is established competitor
functionality, not a unique invention.

If a smaller link-only feature is preferable, make **Threads** the alternative
release feature. Do not automatically bundle Threads, Mastodon, native sharing,
new URL modes, share counts, and styling controls into one release.

There is no confirmed PHP defect in this review that independently requires an
urgent 3.4.1. A maintenance patch is an option if the expanded runtime tests find
a reproducible problem before the feature is ready.

## Current product and release state

- All four origin branches were checked earlier in this session. `latest`,
  `release`, and `release-3.4.0` contain no commits missing from `master`.
  `master` matches `v3.4.0`; no open PRs or local stashes were found.
- 3.4.0 contains WhatsApp, Reddit, and Copy link. The October 9 manual
  WordPress.org publication workflow completed successfully:
  [publication run](https://github.com/alimuzzaman/html-social-share-buttons/actions/runs/37884724195).
- Existing strengths include local icons, server-rendered links, separate
  sharing/profile links, blocks/builders/shortcodes, audience controls, and
  optional feature-specific JavaScript.
- The [public listing](https://wordpress.org/plugins/html-social-share-buttons/)
  still identifies 3.4.0. Live directory metadata is not evidence that this
  session tested the complete plugin against every listed core/PHP version.
- The public support page contains old URL, Pinterest, placement, and metadata
  questions. These are historical context, not new bug reports or proof that
  today's code has those defects.

## Competitor comparison

These are documented features, not independent speed benchmarks. Paid and free
features are distinguished where the vendor makes that distinction.

| Product | Observed evidence | Implication for HSSB |
| --- | --- | --- |
| [AddToAny](https://wordpress.org/plugins/add-to-any/) | Broad destination coverage including Threads and Mastodon; floating/inline placement, image sharing, follow buttons, counters, analytics and shortener integrations. | HSSB should compete on predictable local output and configuration, rather than breadth alone. The evidence does not establish that AddToAny is slow on a particular site. |
| [Scriptless Social Sharing](https://wordpress.org/plugins/scriptless-social-sharing/) | Explicitly builds ordinary links without JavaScript; accessible names; content-type controls and a block. Version 4.0 modernizes blocks/SVG icons and raises the minimum to WP 6.9; directory PHP minimum is 7.4. | No-JavaScript sharing is shared positioning. HSSB's broader placement/builders and retained legacy compatibility are more specific distinctions. |
| [Novashare](https://novashare.io/docs/changelog/) | Version 1.7.0, October 20, 2026, fixes Pinterest media-URL encoding, malformed option values, and block-editor conflicts. Version 1.5.4, September 26, 2024, removed its general frontend JS by using new tabs, retaining conditional Pinterest JS. Its [network documentation](https://novashare.io/docs/social-networks/) lists Threads, Mastodon, SMS and Copy. | Lightweight output already exists commercially. Encoding, option normalization, and editor regression tests have practical value. Those competitor fixes are not evidence of the same bugs in HSSB. |
| [Social Sharing Block](https://wordpress.org/plugins/social-sharing-block/) | No frontend JS files; Threads and Bluesky; native WordPress controls for labels, sizes, colors, and layout. | Block presentation controls are a real HSSB gap, but expanding every placement's style contract is larger work than one release action. |
| [Social Snap](https://wordpress.org/plugins/socialsnap/) | Lite includes Copy link and Print; PRO advertises counters, analytics, shorteners, additional placements and many providers. A March 4, 2026 review reports incorrect follower counts and unanswered support. | Counts introduce ongoing provider/support obligations. The review is one anecdote, not a measured failure rate or independently validated defect. |
| [WordPress.com / Jetpack sharing block](https://wordpress.com/support/wordpress-editor/blocks/sharing-buttons-block/) | Documentation reviewed November 10, 2026 lists Native share, Threads, Mastodon and others; native share opens the device menu or copies the link on desktop. | Native sharing is a credible ecosystem convention. HSSB can offer it within its existing local, opt-in enhancement model. This block is not automatically present in every self-hosted WordPress installation. |
| [Super Web Share](https://wordpress.org/plugins/super-web-share/) | Dedicated native-sharing plugin with an unsupported-browser fallback. | Validates that the use case has existing products; vendor claims of higher sharing rates were not independently verified. |

## Blogs and social-discussion research

- **Andy Bell, April 3, 2025:** [Simplify sharing with built-in APIs and
  progressive enhancement](https://piccalil.li/blog/simplify-sharing-with-built-in-apis-and-progressive-enhancement/).
  Recommends Web Share plus Copy link, progressive enhancement, and keeping
  sharing controls subordinate to the main page CTA. This is informed design
  advice, not a controlled HSSB conversion study. Its URL-cleanup suggestion
  should not override HSSB's explicit URL/browser-URL contracts.
- **Luca Bertozzi, January 23, 2025:** [JavaScript-free Share
  Buttons](https://lucabtz.com/blog/javascript-free-share-buttons).
  Demonstrates static intent links. His slow-widget observation is explicitly
  anecdotal. His Mastodon example uses a third-party intermediary, which is not
  equivalent to HSSB's local-first model.
- **Orbit Media, 2025 comparison:** [Blog design
  research](https://www.orbitmedia.com/blog/how-to-design-a-blog/).
  Its review of 100+ blog designs reports sharing controls at the top on 34%,
  side on 27%, bottom on 23%, and absent on 27%; placements can overlap. This
  measures prevalence, not click performance or causation. It supports offering
  restrained placement choices, not making a floating rail mandatory.
- **Thomas Steiner:** [How to let the user share the website they are
  on](https://web.dev/articles/web-apps/share).
  Explains native sharing with classic intent-link fallback. This primary
  implementation guidance supports progressive enhancement; its document-wide
  canonical lookup should not replace HSSB's post-specific resolved context.

**Tweet limitation:** Luna searched both search systems and made a second,
focused attempt for posts by relevant web-platform and WordPress/PHP developers.
No relevant individual tweet with readable, verifiable contents was retrieved.
No tweet is presented as having been read. Blog and official-documentation
findings above are the actual usable evidence.

## PHP and WordPress compatibility

### Actual repository coverage

| Area | Current evidence | Next action |
| --- | --- | --- |
| Declared floor | PHP 7.0 in `composer.json`, `html-social-share.php`, and `readme.txt`; WP 5.3 minimum. | Retain for this proposed release unless there is a separate support-policy decision. Compatibility with old software is not a hosting recommendation. |
| PHP CI | `.github/workflows/compatibility.yml` checks Composer production installation and syntax on 7.0, 7.4, 8.0, 8.3 and 8.5. | Add explicit 8.2 and 8.4 syntax lanes while relevant; do not treat lint as runtime certification. |
| WordPress runtime CI | WP 5.3/PHP 7.0; WP 6.8/PHP 8.3; WP 7.1/PHP 8.3; latest/PHP 8.3. All activate the candidate and run functional surface assertions. | Add stable WordPress functional lanes on PHP 8.4 and 8.5; preferably use the existing supported test harness for regular/AJAX/multisite tests too. |
| PHPUnit scope | Full contracts are conditional on the configured test library; WP 5.3 and the fixed WP 7.1 row currently skip them. | Preserve support-floor smoke coverage, but describe it accurately. The moving latest row supplies current full contracts on PHP 8.3. |
| Deprecation contract | `tests/php-compatibility-contract.php` checks root entrypoint and `src/` for implicitly nullable typed parameters. | Keep it; add runtime `E_ALL` checks that fail for plugin-owned warnings/deprecations, with vendor/core findings identified separately. It is not a general PHP compatibility scanner. |
| Tooling | Modern PHPUnit/PHPStan/PHPCS are development dependencies; production Composer requirements are separate. | Upgrade tools separately when needed without accidentally raising the shipped runtime floor. |

The [PHP supported-versions table](https://www.php.net/supported-versions.php)
lists 8.2–8.5 as supported on this research date; 8.2 security support ends
December 31, 2026. PHP 7.0 is long out of support. HSSB's own user-version
distribution was not available, so there is insufficient evidence here to
estimate the impact of dropping legacy users.

The [WordPress PHP compatibility
table](https://make.wordpress.org/core/handbook/references/php-compatibility-and-wordpress-versions/),
updated November 4, marks WordPress 7.1 compatible with PHP 8.4 and 8.5. Therefore
HSSB can test those combinations now; there is no need to wait for a future
WordPress release to close that coverage gap.

[PHP 8.6's timetable](https://wiki.php.net/todo/php86) targets November 19, 2026
for GA. Add a non-blocking prerelease canary if practical; promote it to required
coverage only after stable release and confirmed WordPress/tooling support.
Do not claim 8.6 compatibility from this review.

[WordPress 7.1.4](https://wordpress.org/download/releases/) is current stable
on the research date; [7.2 is planned for December
10](https://wordpress.org/documentation/article/wordpress-versions/).
The repository's `Tested up to: 7.1` is therefore not behind a new stable major
line. Exercise 7.2 prereleases separately and update claims only after testing.

### Checks actually run in this session

Local interpreter: **PHP 8.5.0 CLI**. These are source-level checks, not a fresh
WordPress installation or current candidate-ZIP acceptance run.

- Syntax and parse-time deprecation checks with `error_reporting=-1` and
  `display_errors=1`: entrypoint plus all `src/` PHP, **86 files passed**.
- Six standalone contracts under the same reporting settings: PHP compatibility,
  share templates, frontend features, block integration, release metadata and
  Elementor integration: **all passed**.
- PHPStan: **passed**, using `--debug --no-progress` to avoid its parallel-worker
  local socket, which this execution environment rejects.
- PHPCS: **passed**, using `--parallel=1`.
- The initial `composer quality` wrapper stopped on that environment socket
  restriction; both underlying quality tools then completed successfully.

No complete WordPress/PHPUnit/browser suite, multi-PHP matrix, npm/pnpm advisory
audit, dependency refresh, or production ZIP rebuild was run in this research
session. No confirmed runtime compatibility defect was found by these checks.

## Proposed release scope and acceptance

### Required maintenance

1. Add real WordPress/PHP 8.4 and 8.5 execution, including frontend rendering,
   settings save/restore, AJAX, blocks and supported builder contracts. Test the
   production ZIP, rather than only the source tree.
2. Make plugin-owned runtime warnings/deprecations visible to CI. Preserve
   the PHP 7.0/WP 5.3 activation smoke if that floor is retained.
3. Add a focused Pinterest media-URL encoding vector with `&`, spaces, encoded
   characters and an existing image query string. HSSB already applies
   `rawurlencode()` and has ordinary Pinterest assertions; the competitor's
   October fix suggests useful regression coverage, not a proven HSSB bug.
4. Update current-status documents. `docs/PHP-COMPATIBILITY.md` still calls
   3.0.0 the candidate; `docs/RELEASE-CANDIDATE-VALIDATION.md` introduces 3.2.0
   as current. Preserve historical evidence and add dated current status.
5. Review the deferred build-tool major upgrade as its own maintenance diff;
   obtain a fresh advisory report before claiming dependencies are clean.

### One feature: optional native Share…

- Disabled by default. Add the feature through the existing frontend feature
  registry and per-feature asset collector; load one local runtime only where
  the enabled action renders. Reuse the shared frontend-JS notice everywhere
  the action can be enabled.
- Use the existing resolved URL and context title. Explicit custom URLs,
  post-specific loop items, legacy URL filters, and browser-URL eligibility
  remain authoritative. Do not use `document.title` or a global canonical tag
  as a universal replacement.
- Keep a useful server-rendered direct link. When enhancement is available,
  expose a properly labeled native-share button; otherwise show the Copy link
  affordance and existing direct-link fallback. Decide how to avoid duplicate
  Copy link controls when both actions are enabled before coding the UI.
- Feature-detect support and secure context. Trigger `navigator.share()`
  directly from user activation, with no preliminary network call. Respect
  policy restrictions and keep a fallback for unsupported/blocked browsers.
- Cancellation or `AbortError` must not automatically navigate, copy, or
  announce that the post was shared. Other errors should offer an explicit
  fallback. Promise resolution is not proof of downstream publication.
- Cover keyboard focus, accessible names/status messages, translations,
  reduced motion, repeated controls, and dynamic content. Ensure icons exist
  in all six sets and enabled actions work across maintained render paths.
- Reuse raw browser-URL handling where eligible. Do not silently strip tracking
  parameters, fragments, or functional query arguments.

The [Web Share specification](https://www.w3.org/TR/web-share/) requires user
activation and defines policy restrictions and cancellation/error behavior.
These requirements should determine the implementation, rather than assuming
that a browser exposing the function guarantees a successful share.

Acceptance should combine deterministic supported/unsupported/rejected/cancelled
API tests with real-device checks on iOS Safari and Android Chrome. Run desktop
Chrome/Edge, Safari and Firefox fallback checks, secure-context/policy checks,
and no-JavaScript checks. Browser automation mocks do not demonstrate that the
operating system share sheet or app handoff works.

Before publication, repeat the existing archive identity, compatibility,
regular/AJAX/multisite, accessibility/browser and manual publication gates.
Capture default frontend requests: no plugin JS for the ordinary configuration,
and no extra remote SDK/count/tracking request introduced by this feature.
Measure actual bytes and requests; do not advertise an unmeasured speed gain.

## Alternatives and later work

| Candidate | Priority and condition |
| --- | --- |
| Threads | Best smaller alternative to native sharing. Competitors already offer it. Verify the official intent endpoint and current domain/encoding behavior plus mobile handoff before implementation; Meta documentation could not be retrieved reliably in this session. All icon/default/translation/builder contracts still apply. |
| Visible labels, size and color controls | Useful editor-parity opportunity; prototype separately because markup/style changes span iconsets, floating placement, builders and themes. |
| Custom post-type placement controls | Potentially valuable for products/portfolios, but needs clear automatic-placement and exclusion semantics before release scope. |
| Mastodon | Defer until a concrete instance-selection design is selected. A third-party redirector, administrator-chosen instance and visitor-chosen instance have different tradeoffs. |
| Query cleanup / new URL modes | Defer; retain the established explicit/custom/loop/browser precedence and avoid deleting meaningful parameters. |
| Share counts, analytics and shorteners | Defer; require provider maintenance, caching, timeout and privacy decisions and are not prerequisites for the proposed feature. |
| Open Graph metadata generation | Keep outside this release; document SEO-plugin responsibilities rather than adding competing head output. |

Implementation planning now selects native sharing plus compatibility work in
the [3.5.0 release plan](2026-11-11-release-3.5.0-plan.md), created at the owner's
request. Threads remains the smaller alternative if native sharing is removed
from scope; neither feature has been implemented by this research.
