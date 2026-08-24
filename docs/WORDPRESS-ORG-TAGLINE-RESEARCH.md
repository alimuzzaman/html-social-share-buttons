# WordPress.org banner tagline research

Research date: 2026-08-24

## Decision

Use this as the banner support line:

> **HTML + CSS sharing. No frontend JS by default.**

This restores the product's original reason for existing, matches the name
"HTML Social Share Buttons," and is accurate for the current default public
page. The word **frontend** is essential: the plugin uses JavaScript in the
WordPress editor and administration UI. The words **by default** are also
essential: enabling optional Google Social Analytics emits an inline frontend
script.

Two defensible backups are:

1. **HTML + CSS share buttons. Frontend JS optional.**
2. **Share buttons built with HTML + CSS.**

The current line, **"Local icons. Tracking off by default."**, should move from
the banner to supporting listing copy. Both facts are useful, but neither is as
distinctive, memorable, or well-supported by the plugin's own history and
reviews as the HTML/CSS and no-default-frontend-JavaScript story.

## What the plugin originally promised

The initial 2.0.0 readme, committed on 2014-12-02, led with two linked claims:
the buttons were "only with html" and the plugin did not use JavaScript like
other plugins. Its longer description repeated "html css," tied this to a
10 to 11 KB payload, and contrasted one image request with five. This was not a
minor implementation detail; it was the original positioning.[^history-200]

That positioning survived product expansion:

- In 2.0.5, after Google Social Analytics was added, the short description
  still said the plugin was lightweight and did not use JavaScript, while the
  feature list described analytics separately.[^history-205]
- The 2.1.14 readme in 2021 still opened with "lightweight" and "does not use
  JavaScript," then said the analytics feature had to be enabled in
  settings.[^history-2114]
- The 2.2.6 release corrected the absolute wording to "Lightweight HTML and CSS
  share buttons. Settings and block editing use WordPress JavaScript."[^history-226]
- The current plugin header retains that precise distinction between the
  HTML/CSS public output and JavaScript-powered editing screens.[^header]

Conclusion: **HTML/CSS with no normal frontend JavaScript is the durable brand
idea.** "Local icons" and "tracking off" are later proof points beneath it.

## What the current frontend actually does

### Default public page

With analytics disabled, the plugin's normal public output is HTML and CSS:

- `HtmlRenderer` builds headings, wrapper elements, and ordinary share/profile
  anchors on the server.[^renderer]
- `AssetCollector` enqueues only icon-set stylesheets and emits deduplicated
  inline `background-image` CSS.[^assets]
- The dynamic blocks declare only `editorScript`; they have no `script`,
  `viewScript`, or frontend style handle. Their saved public view is produced by
  PHP render callbacks.[^block-share][^block-links][^block-registrar]
- A repository-wide inspection finds no normal frontend `wp_enqueue_script()`.
  Plugin JavaScript is scoped to settings, the block editor, Elementor's editor,
  WPBakery editing, and the optional inline analytics path.[^admin-assets][^elementor]

The built-in icon sets resolve to URLs below the plugin installation, so their
CSS and images are served by the WordPress site rather than an icon CDN.[^icon-resolver]
The extension API can register a third-party icon set at an external URL, so the
most precise listing wording is **"Bundled icons load from your site"**, not an
unqualified promise that every possible extension icon is local.

### Exactly when frontend JavaScript appears

Analytics is `false` for new settings and for a missing legacy
`g_analytics` option.[^defaults][^codec] When it remains off, this plugin does
not enqueue or print plugin JavaScript on the public page.

When a site owner enables Google Social Analytics, the frontend footer prints
an inline `<script>` if all of these gates pass:

1. the request is not in WordPress admin;
2. the current item is not excluded;
3. the viewer-audience policy allows the buttons; and
4. `analyticsEnabled()` is true.[^frontend-footer]

The browser executes that inline block in the footer. It calls
`jQuery(document).ready(...)`; if jQuery exists, it attaches a click handler to
share anchors, excluding `.zmshbt-profile-link` when profile links are
configured. A click pushes a legacy `_trackSocial` tuple. The plugin does **not**
enqueue jQuery or load a Google Analytics library on this path.[^analytics]

There is an implementation caveat relevant to future copy, though not needed on
the banner: the emitted script declares `_gaq` inside local function scopes.
On source inspection, those pushes do not obviously reach `window._gaq`; if
jQuery is absent, the footer script also fails before binding. The repository's
golden fixture proves the script is emitted, not that an analytics event reaches
Google.[^analytics][^fixture] Therefore avoid stronger claims such as "built-in
analytics" or "tracks shares" in prominent marketing copy until the optional
path receives a focused functional test.

### Where the other JavaScript runs

- The Social Share and Social Links bundles are registered as block-editor
  scripts and run while editing blocks, not as public `viewScript` assets.[^block-registrar]
- `admin-react.js` is enqueued only on the plugin settings screen.[^admin-assets]
- `vc-scripts.js` is enqueued on eligible WPBakery post-edit screens and in the
  Elementor editor.[^admin-assets][^elementor]

This is why **"No JavaScript"** by itself is no longer defensible, while
**"No frontend JS by default"** is.

## Evidence from the live listing and reviews

The current WordPress.org page already describes server-rendered HTML/CSS,
locally served icons, and analytics disabled by default.[^live-listing] More
importantly, the reviews reveal what users noticed:

- Featured reviews emphasize that the plugin is simple, fast, unobtrusive, and
  does what it promises.[^live-listing]
- One featured review explicitly contrasts it with plugins that generate large
  amounts of JavaScript and praises that this one "just adds a small amount of
  html."[^live-listing]
- Review titles include "Simple, fast" and "Better, faster, lightweight," which
  reinforces performance/simplicity as the perceived outcome.[^reviews]

The evidence does **not** show users choosing the plugin because its icons are
local or because tracking starts disabled. Those remain sensible trust signals,
but the user-language evidence favors simple, fast, low-conflict HTML output.

## Category positioning

Current first-party WordPress.org peer pages show a crowded market for generic
privacy, speed, icon, and placement claims:

| Plugin | Prominent position on WordPress.org | Messaging implication |
|---|---|---|
| AddToAny | Universal breadth, many networks, counters, follow buttons, analytics, asynchronous loading | HSSB should not compete on network count or generic "fast" language.[^addtoany] |
| Sassy Social Share | 100+ services, no cookies, GDPR, no middle layer, SVG icons, lightweight code | Privacy, local execution, SVG, and lightweight claims are already crowded.[^sassy] |
| Social Media Feather | Lightweight, performance, simplicity, sharing plus follow buttons | "Lightweight" and share/follow are not distinctive on their own.[^feather] |
| Scriptless Social Sharing | Basic network links with "no JavaScript" | The no-JS need is real, but this peer is flagged as not tested with the latest three major WordPress releases and deliberately offers a simpler feature set.[^scriptless] |

HSSB's useful category position is the combination:

> **The original HTML/CSS, no-default-frontend-JS model, now with current
> WordPress blocks, builders, automatic placements, and separate profile links.**

Only the first half belongs on the banner. The integration breadth belongs in
the opening listing copy and screenshots. Trying to put both in one line would
weaken the memorable claim.

## Benefit priority

1. **No frontend JS by default.** This is the strongest differentiator. It is
   authentic to the plugin's history and directly supported by current source
   and a featured user review. It communicates likely speed and reduced script
   conflicts without making an unmeasured speed claim.
2. **HTML + CSS or server-rendered sharing.** This concrete mechanism reinforces
   the product name and explains how benefit 1 is achieved.
3. **Works across blocks, builders, widgets, shortcodes, and automatic
   placement.** This breadth matters, especially beside a scriptless "just
   links" product, but it is too long for the support line.
4. **Bundled icons load from your site.** This supports the low-dependency and
   privacy story. Peers make similar claims, and the third-party icon extension
   API requires a qualification.
5. **Tracking off by default.** This is accurate for settings, but weaker as a
   buying reason. Privacy-conscious plugins commonly make similar claims, and
   the optional legacy analytics path complicates the message.
6. **Separate share and profile links.** This is useful, but AddToAny, Sassy,
   and Feather also advertise follow or profile functionality.
7. **Fast or lightweight.** Users care about the outcome, but competitors all
   say it and the repository has no comparative performance benchmark.
   Communicate the verifiable cause, no frontend JS by default, instead of a
   broad superlative.

## Ranked tagline candidates

Scores are 1 to 5 for accuracy, differentiation, immediate clarity, longevity, and
fit in the current 772×250 composition. Width is the estimated 1× rendered width
in Inter Medium at the current tagline scale. The selected line was subsequently
shaped from the pinned Inter 4.1 release and measures exactly 923 px in the 2×
master, or 461.5 px at 1×. The other estimates were normalized to the former
683 px outline. The safe text area is about 628 px wide, so every listed candidate
fits geometrically; lower fit scores reflect density, not overflow.

| Rank | Candidate | Acc. | Diff. | Clear | Long | Fit | Total | Est. width | Judgment |
|---:|---|---:|---:|---:|---:|---:|---:|---:|---|
| 1 | **HTML + CSS sharing. No frontend JS by default.** | 5 | 5 | 5 | 5 | 4 | **24** | 461.5 px | Best balance of heritage, truth, and user benefit. |
| 2 | **HTML + CSS share buttons. Frontend JS optional.** | 5 | 5 | 4 | 5 | 4 | **23** | Precise backup; "optional" is slightly less immediate than "by default." |
| 3 | **HTML-first sharing. No frontend JS by default.** | 5 | 5 | 4 | 5 | 4 | **23** | Strong and compact, but "HTML-first" is product jargon. |
| 4 | **Share buttons built with HTML + CSS.** | 5 | 3 | 5 | 5 | 5 | **23** | Safest plain-language backup; loses the explicit no-JS benefit. |
| 5 | **No-JS share buttons by default.** | 4 | 5 | 4 | 5 | 5 | **23** | Punchy, but readers may miss that admin/editor JavaScript still exists. |
| 6 | **Server-rendered sharing. Frontend JS optional.** | 5 | 4 | 4 | 5 | 4 | **22** | Technically exact; less approachable and less connected to the name. |
| 7 | **Simple sharing. No frontend JS by default.** | 4 | 4 | 5 | 4 | 5 | **22** | User-aligned, but "simple" is subjective and generic. |
| 8 | **Lightweight HTML + CSS sharing.** | 4 | 3 | 5 | 4 | 5 | **21** | Familiar, but "lightweight" is crowded and not benchmarked. |
| 9 | **Local icons. Tracking off by default.** | 5 | 2 | 5 | 4 | 5 | **21** | Accurate supporting copy, weak primary differentiation. |
| 10 | **HTML-only sharing by default.** | 2 | 5 | 4 | 4 | 5 | **20** | Reject: the public presentation also requires CSS and icon assets. |

## Copy guardrails

Use:

- **No frontend JS by default.**
- **HTML + CSS share buttons.**
- **Bundled icons load from your site.**
- **Analytics is disabled by default.**

Avoid:

- **No JavaScript.** Admin/editor JavaScript exists, and optional analytics
  emits frontend JavaScript.
- **HTML only.** CSS and icon assets are part of the public presentation.
- **No tracking.** Optional analytics can be enabled.
- **Zero external requests.** A click necessarily opens a network, and extension
  icon sets can use external URLs.
- **Fastest**, **zero performance impact**, or exact payload/request counts
  without a current comparative benchmark.

## Design handoff

Replace only the current banner support line with:

> **HTML + CSS sharing. No frontend JS by default.**

At the current Inter Medium scale, its outlined width is 461.5 px at 772×250,
leaving about 83.25 px of breathing room on each side inside the 628 px safe
zone. Keep it on one line. The current composition and icon do not need a new
concept: the angle brackets already signal HTML, so this copy makes the symbol
and message reinforce one another.

Do not put "local icons" or analytics language elsewhere in the artwork. Keep
those points in the listing's first two paragraphs or a screenshot caption,
where their necessary qualifications remain readable.

## Sources

[^history-200]: Repository history, [`Readme.txt` at commit `ef314e093aa8ce91905c0f2f7a2a3fffc7b7736a`](https://github.com/alimuzzaman/html-social-share-buttons/blob/ef314e093aa8ce91905c0f2f7a2a3fffc7b7736a/Readme.txt) (2014-12-02).
[^history-205]: Repository history, [`Readme.txt` at commit `865353510c6b25784a10fbcefcaecdc98f0859ea`](https://github.com/alimuzzaman/html-social-share-buttons/blob/865353510c6b25784a10fbcefcaecdc98f0859ea/Readme.txt) (2015-02-15).
[^history-2114]: Repository history, [`Readme.txt` at commit `084bd139853d6a12ecf6cf14eac44ac5954662b3`](https://github.com/alimuzzaman/html-social-share-buttons/blob/084bd139853d6a12ecf6cf14eac44ac5954662b3/Readme.txt) (2021-12-05).
[^history-226]: Repository history, [`Readme.txt` at commit `620f1ae660a15b8b9dce663acc286e4f5f80e01f`](https://github.com/alimuzzaman/html-social-share-buttons/blob/620f1ae660a15b8b9dce663acc286e4f5f80e01f/Readme.txt) (2.2.6 release preparation).
[^header]: Current source, [`html-social-share.php`](../html-social-share.php#L3-L5).
[^renderer]: Current source, [`src/Presentation/Frontend/HtmlRenderer.php`](../src/Presentation/Frontend/HtmlRenderer.php#L16-L80).
[^assets]: Current source, [`src/Presentation/Frontend/AssetCollector.php`](../src/Presentation/Frontend/AssetCollector.php#L59-L102).
[^block-share]: Current block metadata, [`block.json`](../block.json).
[^block-links]: Current block metadata, [`blocks/social-links/block.json`](../blocks/social-links/block.json).
[^block-registrar]: Current source, [`src/Presentation/Integration/Block/BlockRegistrar.php`](../src/Presentation/Integration/Block/BlockRegistrar.php#L58-L85) and its [editor-script registration](../src/Presentation/Integration/Block/BlockRegistrar.php#L227-L254).
[^admin-assets]: Current source, [`src/Presentation/Admin/SettingsAssetEnqueuer.php`](../src/Presentation/Admin/SettingsAssetEnqueuer.php#L25-L108).
[^elementor]: Current source, [`src/Presentation/Integration/Elementor/ElementorRegistrar.php`](../src/Presentation/Integration/Elementor/ElementorRegistrar.php#L40-L66).
[^icon-resolver]: Current source, [`src/Infrastructure/Asset/IconSetAssetResolver.php`](../src/Infrastructure/Asset/IconSetAssetResolver.php#L94-L132).
[^defaults]: Current source, [`src/Domain/Settings/SettingsDefaults.php`](../src/Domain/Settings/SettingsDefaults.php#L13-L46).
[^codec]: Current source, [`src/Infrastructure/WordPress/Settings/OptionSettingsCodec.php`](../src/Infrastructure/WordPress/Settings/OptionSettingsCodec.php#L51-L78).
[^frontend-footer]: Current source, [`src/Presentation/Frontend/FrontendController.php`](../src/Presentation/Frontend/FrontendController.php#L193-L209).
[^analytics]: Current source, [`src/Presentation/Frontend/FrontendController.php`](../src/Presentation/Frontend/FrontendController.php#L381-L387).
[^fixture]: Current regression fixture, [`tests/fixtures/frontend-output-baseline.json`](../tests/fixtures/frontend-output-baseline.json).
[^live-listing]: [HTML Social Share Buttons on WordPress.org](https://wordpress.org/plugins/html-social-share-buttons/) (live page inspected 2026-08-24).
[^reviews]: [HTML Social Share Buttons reviews on WordPress.org](https://wordpress.org/support/plugin/html-social-share-buttons/reviews/) (inspected 2026-08-24).
[^addtoany]: [AddToAny Share Buttons on WordPress.org](https://wordpress.org/plugins/add-to-any/) (inspected 2026-08-24).
[^sassy]: [Sassy Social Share on WordPress.org](https://wordpress.org/plugins/sassy-social-share/) (inspected 2026-08-24).
[^feather]: [Social Media Feather on WordPress.org](https://wordpress.org/plugins/social-media-feather/) (inspected 2026-08-24).
[^scriptless]: [Scriptless Social Sharing on WordPress.org](https://wordpress.org/plugins/scriptless-social-sharing/) (inspected 2026-08-24).
