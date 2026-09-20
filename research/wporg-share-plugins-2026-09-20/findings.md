# Share-plugin research findings and release-plan review

Snapshot: 2026-09-20 (Asia/Dhaka)

This is a static source review of ten WordPress.org packages downloaded on the
snapshot date. It also reviews the proposed “canonical-URL-safe, lightweight,
privacy-first” next-release plan against the current
`html-social-share-buttons` source. No plugin was activated, no WordPress site
was booted, and no share endpoint or remote SDK was called.

## Research set and evidence

The reproducible download record is in this directory:

- `README.md` — selection, endpoints, snapshot scope, and report map.
- `metadata/*.json` — WordPress.org API responses used to select versions.
- `archives/*.zip` — exact downloaded archives.
- `archives/SHA256SUMS` — archive integrity manifest.
- `plugins/<slug>/<slug>/` — unpacked source.
- `reviews/set-a.md` and `reviews/set-b.md` — line-cited Luna reviews.

The ten versions are:

| Slug | Version | Active installs in API snapshot |
| --- | ---: | ---: |
| `add-to-any` | 1.8.18 | 300,000 |
| `sassy-social-share` | 3.3.79 | 100,000 |
| `simple-share-buttons-adder` | 8.7.0 | 40,000 |
| `social-pug` (Hubbub Lite) | 1.36.3.1 | 30,000 |
| `simple-social-buttons` | 7.1.0 | 20,000 |
| `scriptless-social-sharing` | 3.3.1 | 10,000 |
| `sharethis-share-buttons` | 2.3.12 | 10,000 |
| `social-sharing-block` | 1.4.0 | 7,000 |
| `kiwi-social-share` | 2.1.9.1 | 4,000 |
| `super-web-share` | 2.5.2 | 2,000 |

Integrity check completed: all 10 ZIP hashes matched `archives/SHA256SUMS`, and
all 10 archives have an extracted source directory.

## Executive result

The proposed product lane is sound, but the plan is too aggressive for one
release. The strongest defensible position is:

> Local, server-rendered share links with a documented URL-source contract,
> correct query-component encoding, bundled assets, and no required third-party
> frontend SDK or share-count request.

The competitor set supports this lane. Scriptless Social Sharing and Social
Sharing Block are the closest source-level examples: both render local links and
encode query values before output. AddToAny and ShareThis demonstrate the trade:
large network coverage and managed updates, but a remote runtime. Sassy,
Simple Share Buttons Adder, Kiwi, Hubbub, and Simple Social Buttons show that
network volume, counters, shorteners, telemetry, and provider SDKs add real
privacy, compatibility, and encoding cost.

The plan should therefore be staged:

1. Specify and test the existing URL contract without breaking loop-post,
   shortcode, block, widget, builder, AJAX, or legacy behavior.
2. Fix or extend URL resolution only where the contract says it should change.
3. Add a small number of local networks or Copy Link after the resolver matrix
   is green.
4. Keep counts, shortening, analytics, and provider SDKs out of the default
   path.

## What the ten plugins actually do

### URL generation patterns

| Plugin | URL source and construction | Main finding |
| --- | --- | --- |
| AddToAny | Explicit URL/title/media override; otherwise post permalink or `home_url(REQUEST_URI)` in current-page mode; values are centrally `rawurlencode()`d. | Good central input model, but normal frontend behavior is delegated to remote `page.js`; counters and cache refresh add remote dependencies. |
| Sassy Social Share | Post permalink by default; current request when the permalink has a query or is empty; home/custom/filter modes; direct templates and SDK data attributes. | Broad and configurable, but many provider scripts, cookies/SDK paths, counts, and a Bitly path with TLS verification disabled. |
| Simple Share Buttons Adder | Post permalink or explicit shortcode URL; current URL assembled from request data for shortcode mode. Large static template map. | Most ordinary template values are interpolated without query-component encoding; `esc_attr()` is not a substitute. |
| Social Pug / Hubbub Lite | Post permalink or explicit shortcode URL, filtered, then URL/title/description are encoded once before `sprintf()`. | Cleanest centralized pipeline in set A; counts, UTM settings, and product-data sync are separate privacy surfaces. |
| Kiwi Social Share | Post permalink for singular views; home URL marker for front/archive views; optional Bitly short URL. | URLs are often encoded, but titles, email fields, and some media are not; counts are synchronous during button construction. |
| Social Sharing Block | Explicit block custom link; front/home, archive, or current permalink; `rawurlencode()` before a static service map. | Strong local/block model; it drops archive query parameters and contains HTTP Tumblr and `javascript:window.print()` entries. |
| Scriptless Social Sharing | Structured attributes; filterable permalink; per-network query arrays passed through `add_query_arg()` and a final `rawurlencode(html_entity_decode())` pass. | Strongest no-JS reference; SMS uses an `&amp;body` query key that needs a runtime check. |
| Super Web Share | Native Web Share uses OG/current browser metadata; fallback templates are filled in browser JS. | Fallback uses `encodeURI()` for query placeholders, so `&`, `?`, and `#` can change query structure; X template is HTTP. |
| ShareThis Share Buttons | Local PHP emits a container and `data-url`; remote SDK generates the actual network URLs and markup. | Exact share URLs and accessibility cannot be audited from the archive; remote JS/CDN/privacy behavior is part of the product. |
| Simple Social Buttons | Central helper encodes many templates, but legacy rendering branches build Bluesky/Telegram/Threads/Reddit URLs directly. | Duplicated branches bypass the safe helper; direct paths can misparse URLs with `&`, `?`, or `#`. Also includes counts, tracking, Pinterest/Snap SDKs, and telemetry. |

Detailed line evidence is in `reviews/set-a.md` and `reviews/set-b.md`.

### Common good pattern

The best pattern is the same in Social Pug, Scriptless Social Sharing, and the
safe portions of Social Sharing Block:

1. Resolve a structured share context (`url`, `title`, `description`, `image`).
2. Apply documented customization filters before encoding.
3. Encode each query-component value exactly once with RFC 3986 semantics.
4. Insert values into a data-only endpoint template.
5. Escape the complete URL at HTML output.

The main negative patterns are also consistent:

- `esc_attr()` or `esc_url()` applied after raw query interpolation does not
  repair query parsing.
- `encodeURI()` is wrong for a value placed inside a query parameter.
- A central safe helper is not enough if legacy rendering branches bypass it.
- Remote SDK containers make final URL, markup, loading, and privacy behavior
  provider-dependent.

### Counts, analytics, and remote behavior

Share counts are materially more complex than a button link. Sassy, Simple
Share Buttons Adder, Kiwi, Hubbub, and Simple Social Buttons all contain remote
count paths, credentials, transients, or synchronous/request-time behavior.
Simple Social Buttons also has first-party click tracking and optional Pinterest,
Snapchat, and telemetry integrations. AddToAny has optional provider counters.
ShareThis loads a remote SDK and CDN icons and documents engagement/usage data
collection. These are reasons to keep counts and analytics out of the default
HSSB path, not evidence that all competitors are tracking identically.

### Accessibility and fallback lessons

The strongest baseline is a real accessible anchor or button with an explicit
network label, `aria-hidden` decorative icon, and `rel="nofollow noopener"` for
new-tab links. Avoid `href="#"` as the only action and avoid nested provider
anchors. Scriptless Social Sharing and Social Sharing Block are useful local
examples. Super Web Share shows that native share and clipboard can be an
enhancement, but the no-JS/direct-link fallback must remain usable.

## Review of the proposed next-release plan

### Keep these decisions

**Product lane.** Competing on URL correctness, local output, privacy, and
small predictable behavior is more credible than competing with AddToAny/Sassy
on network count.

**No share counts yet.** This is a good boundary. If counts are added later,
they should be a separately documented, cached, opt-in module with bounded
timeouts and no render-blocking remote calls.

**Explicit custom URL support.** Competitors expose this, and HSSB already has
an explicit URL/permalink override path. Preserve that contract and document it
before adding a second global URL setting.

**Encoding and regression tests.** The plan correctly treats URL encoding as a
release concern. Test the final rendered anchor, not only an intermediate
resolver value.

### Correct these parts before implementation

#### 1. Do not make `wp_get_canonical_url()` the universal first resolver

Current HSSB resolution is intentionally context-sensitive:

- explicit context post ID;
- global `$post`;
- queried `WP_Post`;
- AJAX/editor post IDs;
- `get_permalink($postId)`;
- sanitized `REQUEST_URI` fallback.

See `src/Infrastructure/WordPress/Rendering/CurrentPostPermalink.php:6-48`.
The existing contract test explicitly requires an archive/search loop to use the
current loop post permalink (`tests/phpunit/ShareUrlResolutionContractTest.php:54-69`).
Putting `get_queried_object_id()` ahead of the loop post would change that
behavior. `wp_get_canonical_url()` can be a conditional singular fallback, but
it is not a general resolver for terms, archives, search, front page, AJAX, or
loop-post contexts.

Define source precedence first. A safe starting contract is:

1. Explicit caller URL when the caller selected custom mode.
2. Explicit context post ID.
3. Current loop/global post where the rendering contract requires it.
4. Explicit singular queried post.
5. AJAX/editor post ID.
6. Singular permalink/canonical fallback.
7. Explicit term/archive/search/front-page resolver.
8. Current-request fallback only when the selected mode permits it.

The exact order of steps 3–5 must be preserved or deliberately changed with
adapter and regression evidence.

#### 2. Do not strip tracking parameters by default without a URL policy

The plan proposes dropping `utm_*`, `fbclid`, `gclid`, and similar values. The
current HSSB contract preserves request shape; a test currently expects
`/privacy-policy/?preview=true` to remain intact (`tests/phpunit/SettingsContractTest.php:150-160`).
Archive plugins often drop all query parameters, while other plugins preserve
them. Some query arguments are functional, not tracking.

If stripping is wanted, make it an explicit policy with an allow/deny list and
tests for functional parameters, preview/editor parameters, encoded values,
fragments, Unicode, and repeated keys. Do not silently turn a canonical/current
request change into a migration-wide behavior change.

#### 3. A global “URL mode” setting may duplicate existing overrides

HSSB already supports a per-render URL override and tokenized templates. A new
global mode must state whether it affects only the fallback, shortcode/block
attributes, automatic placements, builder integrations, and legacy API calls.
Adding it without that matrix risks making a saved shortcode or builder block
render a different target. Treat this as a separate compatibility decision, not
an automatic consequence of adding canonical fallback logic.

#### 4. Copy Link needs a real no-JS contract

Copy Link is useful and appears in competitor implementations. The fallback
must be a usable link to the resolved URL, not only an `href="#"` control. The
enhancement can use Clipboard API, but must handle denial/failure, keyboard use,
screen-reader status, and a no-JS path. Adding frontend JS changes HSSB’s current
HTML/CSS-first promise; keep the script tiny, local, and opt-in to the feature.

#### 5. Network expansion is larger than adding templates

HSSB currently has Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and
Email built-ins, with URL and icon coverage tests. Adding Mastodon, Threads,
Reddit, or WhatsApp also requires:

- icon manifests, CSS, legacy identifiers, and defaults;
- templates and per-network encoding vectors;
- labels, accessibility, `rel`, and target behavior;
- shortcode/block/widget/builder compatibility;
- docs, translations, fixtures, and archive checks;
- a decision about whether Mastodon uses an instance URL, an intermediary, or
  a user-supplied share target.

Ship one or two high-value additions only after the resolver contract is stable.
Do not promise a “modern default set” before deciding which networks are enabled
by default and which endpoint semantics are supported.

#### 6. Scope privacy claims precisely

“No external frontend SDKs,” “no frontend share-count requests,” and “no
required third-party assets in the default public output” are testable claims.
“No jQuery dependency” is not true for the whole plugin: admin settings and
builder integrations use jQuery. “No tracking” must also be scoped to current
runtime behavior and the retired analytics path; legacy settings/compatibility
fields remain stored for compatibility. Use language such as “no required
frontend SDK or tracking request in the default share output” unless a full
runtime inventory proves a broader claim.

#### 7. Expand the test matrix beyond canonical permalink

The plan should cover at least:

- singular post/page and custom post type;
- archive loop post, term archive, date archive, search, front page, and 404;
- explicit shortcode/block/widget/builder URL;
- AJAX/editor post IDs;
- current request with functional and tracking query args;
- Unicode, ampersands, quotes, percent signs, fragments, line breaks, and
  already-encoded values;
- every built-in network, including mailto and Bluesky’s separator handling;
- no double-encoding and final HTML escaping;
- `target`, `rel`, accessible names, and no external requests in default output.

The current resolver and URL tests already protect important loop-post,
explicit-custom-URL, and once-only encoding behavior. Extend those tests rather
than replacing them.

#### 8. Qualify the competitor claims in the pasted plan

Novashare and Shared Counts were named in the plan but were not part of this
ten-package WordPress.org download sample (the former is premium and the latter
was not selected from the WordPress.org package set). Keep those claims as
external/contextual research, or replace them with the source-backed findings
above. The ten local packages are enough to support the proposed product lane.

## Recommended release shape

### Release A — URL contract and safety

- Write the resolver precedence and current-request query policy.
- Preserve loop-post and explicit caller behavior.
- Add conditional singular canonical handling only where it does not override
  the established context contract.
- Centralize final query-component encoding and final HTML escaping.
- Add the context/encoding/no-external-request matrix above.

### Release B — one small user-facing feature

Choose Copy Link or one/two modern network adapters. Reuse the stable context
and template pipeline. Keep share counts, link shortening, hosted providers,
and analytics out of the default path.

### Release C — optional integrations

If demand justifies them, add counts, native Web Share, or provider adapters as
separate opt-in modules with their own privacy, consent, timeout, and fallback
contracts. Do not make their presence part of the base plugin promise.

## Limits and follow-up evidence

The reports are static inspections of the downloaded source. They do not prove
fresh-site defaults, actual browser loading order, endpoint redirects, remote
SDK behavior, cookies, consent enforcement, or screen-reader behavior. Before
shipping a resolver or Copy Link change, run the repository’s supported tests
and a real WordPress/browser matrix. For any remote integration, capture the
actual request and final DOM separately from source review.
