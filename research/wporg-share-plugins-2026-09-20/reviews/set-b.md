# WordPress.org share-plugin review: set B

Static review of the five downloaded plugin directories. No plugin source was changed. The version and compatibility values below come from the downloaded WordPress.org API metadata JSON files; source claims are cited to the unpacked plugin files.

## Versions and package metadata

| Plugin | Version | WordPress / PHP metadata | Download metadata |
|---|---:|---|---|
| Social Sharing Block | 1.4.0 | WP 6.6+, tested 7.1.1; PHP 7.4 | `metadata/social-sharing-block.json` |
| Scriptless Social Sharing | 3.3.1 | WP 6.2+, tested 6.8.9; PHP 7.4 | `metadata/scriptless-social-sharing.json` |
| Super Web Share | 2.5.2 | WP 3.0.1+, tested 7.1.1; PHP 7.0 | `metadata/super-web-share.json` |
| ShareThis Share Buttons | 2.3.12 | WP 5.5+, tested 7.1.1; metadata `requires_php` is `false`; source runtime check is PHP 5.3+ | `metadata/sharethis-share-buttons.json`; `plugins/sharethis-share-buttons/sharethis-share-buttons/sharethis-share-buttons.php:37-45` |
| Simple Social Buttons | 7.1.0 | WP 4.0+, tested 7.0.5; PHP 5.6 | `metadata/simple-social-buttons.json` |

Paths in the rest of this report are absolute and rooted at `/Users/alim/Sites/git/html-social-share-buttons/research/wporg-share-plugins-2026-09-20/plugins/`.

## Executive findings

1. **Best local URL-generation patterns:** Social Sharing Block and Scriptless Social Sharing build ordinary server-rendered links, encode each query value, and have clear source selection. Simple Social Buttons has a good central helper, but several older duplicated rendering branches bypass it and insert a complete URL directly into a query value.
2. **Main correctness risk:** Super Web Share's fallback replacement uses JavaScript `encodeURI()` on `{url}`, `{title}`, and `{description}`. Reserved `&`, `?`, and `#` characters are therefore not safely encoded as query-component values. The native Web Share path is separate and does not have this exact problem.
3. **Main privacy/performance split:** ShareThis is a remote SDK product, not a local URL generator. It loads ShareThis JavaScript and CDN icons, and its own privacy text says it may collect button engagement/usage data. Simple Social Buttons adds first-party click-count tracking, remote count APIs, Pinterest's remote pinmarklet, optional Snapchat SDK, and a bundled opt-in/telemetry SDK.
4. **No Sol/Astra work was used for this review.** This report is static source evidence only; no WordPress runtime, browser, network capture, or remote ShareThis SDK behavior was exercised.

## 1. Social Sharing Block 1.4.0

### URL source and exact share URL construction

The implementation is server-side. `get_link_services()` chooses a custom URL from block context when `shareCustomLink` is enabled; otherwise, outside the loop it uses `home_url()` for the front/home page or `home_url($wp->request)` for an archive; in the remaining case it uses the current post permalink, title, and a large featured image. See `/Users/alim/Sites/git/html-social-share-buttons/research/wporg-share-plugins-2026-09-20/plugins/social-sharing-block/social-sharing-block/build/social-sharing-link/utils.php:75-113`. The package readme explicitly documents custom links, home/front-page support, and archive support at `readme.txt:154-157`.

The permalink, title, and image are `rawurlencode()`d before the service strings are assembled (`utils.php:80-112`). The title then receives targeted replacements for encoded HTML dashes, apostrophes, and ampersands, and optional encoded `title`, `text`, or `description` suffixes are prepared at `utils.php:115-125`. A final `social_sharing_block_services_data` filter lets a theme/plugin alter the complete descriptor array (`utils.php:127-220`).

| Network | Source form in the downloaded code | URL and title/media behavior |
|---|---|---|
| Bluesky | selected permalink plus title | `https://bsky.app/intent/compose?text={title separator}{url}` (`utils.php:127-131`) |
| Facebook | selected permalink | `https://www.facebook.com/sharer/sharer.php?u={url}&title={title}` (`utils.php:133-136`) |
| Flipboard | selected permalink | `https://share.flipboard.com/bookmarklet/popout?url={url}&title={title}` (`utils.php:138-141`) |
| LINE | selected permalink | `https://lineit.line.me/share/ui?url={url}&text={title}` (`utils.php:143-146`) |
| LinkedIn | selected permalink | `https://www.linkedin.com/shareArticle?mini=true&url={url}&title={title}` (`utils.php:148-151`) |
| Email | selected title or URL as subject; title plus URL as body | `mailto:?subject={title-or-url}&body={title separator}{url}` (`utils.php:153-156`) |
| Pinterest | selected permalink, optional featured image | `https://pinterest.com/pin/create/button/?&url={url}&description={title}&media={image}` (`utils.php:158-161`) |
| Pocket | selected permalink | `https://getpocket.com/save/?url={url}&title={title}` (`utils.php:163-166`) |
| Print | no share URL | `javascript:window.print()` (`utils.php:168-171`) |
| Reddit | selected permalink | `https://www.reddit.com/submit?url={url}&title={title}` (`utils.php:173-176`) |
| Skype | selected permalink | `https://web.skype.com/share?url={url}` (`utils.php:178-181`) |
| SMS | title plus URL in body | `sms:?&body={title separator}{url}` (`utils.php:183-186`) |
| Telegram | selected permalink | `https://telegram.me/share/url?url={url}&text={title}` (`utils.php:188-191`) |
| Threads | selected permalink only | `https://www.threads.net/intent/post?text={url}`; the title is not included (`utils.php:193-196`) |
| Tumblr | selected permalink | **HTTP** `http://tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}` (`utils.php:198-201`) |
| Viber | title plus URL | `viber://forward?text={title separator}{url}` (`utils.php:203-206`) |
| WhatsApp | title plus URL | `https://api.whatsapp.com/send?text={title separator}{url}` (`utils.php:208-211`) |
| X | selected permalink plus title | `https://x.com/share?url={url}&text={title}` (`utils.php:213-216`) |

### JS, SDKs, tracking, cookies, and remote assets

The readme states that the plugin adds no frontend JavaScript files (`readme.txt:38-44`). The inspected front-end path is PHP block rendering plus bundled inline SVGs; no external SDK, counter endpoint, cookie, analytics call, or remote icon asset was found in the plugin source. The only network activity represented by these descriptors is the user's later click on the destination URL.

### Markup, accessibility, admin/configuration, compatibility

The renderer emits an `<li>` and anchor with a translated `aria-label`; labels are either visible or given the `screen-reader-text` class, and non-email/non-print links receive `rel="noopener nofollow" target="_blank"` (`build/social-sharing-link/render.php:15-46`). SVGs are marked `aria-hidden="true"` and `focusable="false"` in the service data, for example `utils.php:127-136`. Registration is two dynamic blocks at `social-sharing-block.php:19-27`; configuration is block context/attributes rather than a large settings page.

### Lessons and risks for html-social-share-buttons

- Keep the server-rendered descriptor approach and encode every value before concatenating it into a service query string.
- The explicit source precedence (custom link, front/home, archive, singular permalink) is useful and more predictable than blindly using the browser URL.
- Preserve an extension filter for service descriptors, but make the default network definitions immutable and testable.
- Do not copy the HTTP Tumblr endpoint (`utils.php:198-201`); use HTTPS only.
- Treat `javascript:window.print()` (`utils.php:168-171`) as a separate action instead of a URL if CSP and strict link sanitization matter.
- Add a title to Threads if product requirements expect title text; this implementation only forwards the URL (`utils.php:193-196`).

## 2. Scriptless Social Sharing 3.3.1

### URL source and exact share URL construction

The base class builds each URL with `add_query_arg()` and then escapes a valid URL (`includes/buttons/class-scriptlesssocialsharing-button.php:43-56`). Before that, it removes empty values and applies `rawurlencode(html_entity_decode($value))` to every query value; the code comment specifically calls out preventing hashtags from breaking Twitter titles (`:58-77`). The permalink comes from the `attributes['permalink']` value and is filterable through `scriptlesssocialsharing_get_permalink` (`:107-120`). Normal attributes use `get_the_permalink()`, `home_url()`, a title from `the_title_attribute()`, and a featured/fallback image (`includes/output/class-scriptlesssocialsharing-output-attributes.php:47-82`). There is no normal browser-current-URL resolver for archives: automatic output is restricted to the main singular query unless a block/shortcode/filter overrides it (`includes/output/class-scriptlesssocialsharing-output.php:30-65`).

| Network | URL base and query values | Source/title/media notes |
|---|---|---|
| X/Twitter | `https://twitter.com/intent/tweet` with `text`, `url`, and optional `via`/`related` (`button-twitter.php:17-37`) | Title can be replaced by Yoast `_yoast_wpseo_twitter-title`; handle is filterable (`:47-62`). |
| Facebook | `https://www.facebook.com/sharer/sharer.php` with `u` (`button-facebook.php:17-31`) | Permalink only. |
| Pinterest | `https://pinterest.com/pin/create/button/` with `url`, `media`, `description` (`button-pinterest.php:17-35`) | Uses featured/custom image and a custom post/image-alt description when available (`:44-64`, `:80-94`). |
| LinkedIn | `https://www.linkedin.com/shareArticle` with `mini`, `url`, `title`, `source`, optional `summary` (`button-linkedin.php:17-39`) | `source` is the configured home URL; title/description are attributes. |
| Email | `mailto:` with `body` and `subject` (`button-email.php:17-32`) | Both body and subject are filterable (`:34-48`). |
| Reddit | `https://www.reddit.com/submit` with `url` (`button-reddit.php:17-31`) | No title query is added by this class. |
| WhatsApp | `https://api.whatsapp.com/send` with `text={title} &#8212; {url}` (`button-whatsapp.php:17-31`) | Base-class HTML decode/encode converts the entity safely. |
| Pocket | `https://getpocket.com/save` with `url`, `title` (`button-pocket.php:17-32`) | — |
| Telegram | `https://telegram.me/share/url` with `url`, `text` (`button-telegram.php:20-35`) | — |
| Hatena | `https://b.hatena.ne.jp/entry/panel/` with `url`, `btitle`, optional `summary` (`button-hatena.php:17-37`) | — |
| SMS | `sms:` with a query key literally written as `&amp;body` (`button-sms.php:17-31`) | This is a likely defect: the HTML entity is passed into `add_query_arg()` as a key, so the generated URI should be tested in a real browser. |
| Bluesky | `https://bsky.app/intent/compose` with `text={twitter title}: {permalink}` (`button-bluesky.php:17-33`) | Uses the same Yoast/filterable Twitter title logic (`:42-48`). |

### JS, SDKs, tracking, cookies, and remote assets

The package describes itself as building basic links with no JavaScript (`readme.txt:17-23`). Frontend share generation is PHP anchors and SVGs. The block editor does have a local editor script (`includes/output/class-scriptlesssocialsharing-output-block.php:29-40`, `:108-125`), but that is admin/editor behavior, not a front-end sharing SDK. Styles are local by default, SVG icons are the default setting, and Font Awesome is an optional external stylesheet only when the Font Awesome setting is enabled (`includes/settings/defaults.php:6-25`; `includes/class-scriptlesssocialsharing-enqueue.php:79-114`). I found no counts, click tracking, cookies, analytics, or third-party share SDK in the inspected source.

### Markup, accessibility, admin/configuration, compatibility

Output is limited to the main singular query, feed/non-singular pages are disabled by default, and developers can override the decision with a filter (`class-scriptlesssocialsharing-output.php:30-45`). Anchors default to `rel="noopener noreferrer nofollow"`, open in a new tab except email, and include a visually hidden “Share on …” label when icon-only mode is selected (`class-scriptlesssocialsharing-output.php:118-203`). Settings include enabled networks, styles, SVG vs Font Awesome, heading, post types, placement, and order (`includes/settings/defaults.php:6-44`; network inventory at `includes/settings/networks.php:6-72`).

### Lessons and risks for html-social-share-buttons

- The `rawurlencode(html_entity_decode())` pass over an array of query values is a strong, simple safety pattern (`class-scriptlesssocialsharing-button.php:67-75`).
- Keep permalink/title/media as a structured attribute object and expose filters for source and per-network arguments.
- Fix or avoid the SMS `&amp;body` key (`button-sms.php:17-20`). A URI scheme should receive `body`, not an HTML-escaped query key.
- The singular-only default is privacy/performance friendly, but a product that supports archive/home sharing should make that behavior explicit rather than relying on a hidden filter.
- The optional remote Font Awesome path shows how to offer a compatibility fallback without making it the default (`class-scriptlesssocialsharing-enqueue.php:83-95`).

## 3. Super Web Share 2.5.2

### Native path, fallback path, and URL source

Super Web Share has two distinct paths. The button is local markup and the plugin registers one local CSS and one local deferred JS file (`public/class-super-web-share-public.php:108-140`). On click, `getPageMeta()` chooses `og:description`/description/document title, `og:title`/`twitter:title`/document title, and `og:url`/`window.location.href` (`public/js/super-web-share-public.js:89-113`). The native branch calls `navigator.share({title, text: description, url})` (`:69-87`). Shortcodes can provide explicit `title`, `link`, and `description` data attributes (`public/functions-super-web-share-public.php:598-660`), so a custom URL is possible; otherwise the current page metadata/current browser URL is used.

Fallback network templates are declared in PHP and expanded in the browser:

| Network | Template in `sws_get_social_networks()` |
|---|---|
| Facebook | `https://www.facebook.com/sharer/sharer.php?u={url}` (`admin/functions-super-web-share-admin.php:973-979`) |
| X | **HTTP** `http://x.com/share?text={title}&url={url}` (`:980-985`) |
| WhatsApp | `https://api.whatsapp.com/send?text={title}{nl}{nl}{url}` (`:986-990`) |
| LinkedIn | `https://www.linkedin.com/sharing/share-offsite?url={url}` (`:992-996`) |
| Pinterest | `https://www.pinterest.com/pin/create/bookmarklet/?url={url}&pinFave=1&color=%238fbfb3&h=237&w=237&description={title}` (`:998-1002`) |
| Telegram | `https://t.me/share/url?url={url}&text={title}` (`:1004-1008`) |
| Email | `mailto:?subject={title}&body={url}` (`:1010-1014`) |
| Mastodon | `https://mastodonshare.com/?text={title}&url={url}` (`:1016-1020`) |

The template contract says `{url}` is the current URL or shortcode URL, `{title}` is the share/site title, and `{nl}` is a newline (`admin/functions-super-web-share-admin.php:961-967`). The browser replacement implementation sets `nl` and calls `encodeURI()` for every placeholder (`public/js/super-web-share-public.js:183-188`). `encodeURI()` does not encode query delimiters. Therefore a page URL such as `https://example.test/p?a=1&b=2#part` can inject/alter the fallback template's `&` or `#`; titles/descriptions containing `&` have the same class of problem. This is the most concrete URL correctness issue in this set. Use `encodeURIComponent()` for each query-component placeholder, or construct the query with a URL/URLSearchParams API.

### JS, SDKs, tracking, cookies, and remote assets

The package uses local JavaScript for capability detection, modal behavior, native sharing, clipboard copy, and fallback popup construction (`public/js/super-web-share-public.js:28-188`). No external JS SDK, cookie, share count, analytics endpoint, or tracking code was found. The fallback modal serializes settings into an inline `window.superWebShareFallback` object (`public/functions-super-web-share-public.php:267-277`). The readme claims no cookies, tracking scripts, or stored user data (`README.txt:93-100`); this is consistent with the inspected source but was not runtime-tested.

### Markup, accessibility, admin/configuration, compatibility

Floating buttons have `aria-label="Share"` (`public/functions-super-web-share-public.php:35-42`), and fallback links carry `rel="nofollow noreferrer noopener"` (`:225-236`). The fallback links initially use `href="#"` and are activated by JavaScript (`:231-250`), so keyboard/no-JS behavior is weaker than a direct anchor. The normal inline button in `superwebshare_inline_button()` has no `aria-label` of its own (`:126-141`). AMP has a separate `amp-social-share` path (`:296-318`). The admin has floating, inline, fallback, appearance, per-page, shortcode, Gutenberg, Elementor, and AMP-related configuration, as summarized by `README.txt:21-39` and `:118-145`.

### Lessons and risks for html-social-share-buttons

- Native Web Share is a useful enhancement, but keep a deterministic direct-link fallback for every network.
- Never use `encodeURI()` when replacing a value inside a query template (`public/js/super-web-share-public.js:183-188`). Encode components independently.
- Remove HTTP endpoints: the X template is HTTP (`admin/functions-super-web-share-admin.php:980-985`), and `mastodonshare.com` is an intermediary relay rather than a direct Mastodon instance (`:1016-1020`).
- Ensure every interactive button has an accessible name, including the inline button (`public/functions-super-web-share-public.php:126-141`).
- Clipboard, native share, and popup actions should degrade to real links rather than `href="#"` where possible.

## 4. ShareThis Share Buttons 2.3.12

### URL source and what is not locally inspectable

This plugin does not locally generate one URL per network. Its PHP helper emits an empty `<div class="sharethis-inline-share-buttons"></div>` (`sharethis-share-buttons.php:63-70`), and the browser-side ShareThis SDK fills the container and owns the network-specific URL generation. The frontend SDK URL is assembled from a stored ShareThis property/product identifier and registered from `php/class-plugin.php:37-56`; it is enqueued by `php/class-share-buttons.php:370-377`. The exact remote script body is not included in this download, so exact final Facebook/X/WhatsApp/etc. URL templates cannot be confirmed from the local package.

The local source does control the URL passed to that SDK. A shortcode accepts `url`, but archive/front/tag contexts override it with `get_permalink($post->ID)` (`php/class-minute-control.php:189-208`). Automatic excerpt containers likewise use the excerpt post permalink (`:251-270`). A normal container without `data-url` leaves URL resolution to the remote SDK/current page. The shortcode path contains no local title or description attributes (`:197-208`).

| Local network/share URL finding | Evidence |
|---|---|
| ShareThis has 40+ channels and AI assistant buttons | `readme.txt:25-37`, `:39-73`; local AI list is ChatGPT/Claude/Copilot/Gemini/Grok/Perplexity at `php/class-plugin.php:118-136`. |
| The local code passes page URL as `data-url` but does not build network query strings | `php/class-minute-control.php:197-208`, `:260-270`. |
| Exact provider URL generation is remote and configuration-dependent | SDK URL at `php/class-plugin.php:42-56`; local provider JS is absent. |
| Configuration can select inline/sticky/page exclusions and GDPR options | `readme.txt:75-125`; admin menu requires `manage_options` (`php/class-share-buttons.php:352-367`, `:472-489`). |

### JS, SDKs, tracking, cookies, and remote assets

This is the strongest third-party-runtime case in the set. The frontend loads `//platform-api.sharethis.com/js/sharethis.js#property=...` (`php/class-plugin.php:48-55`) and the admin also loads ShareThis JavaScript (`:72-79`). Non-AI network icons load from `https://platform-cdn.sharethis.com/img/<name>.svg` (`:115-124`). The admin credential/setup script calls ShareThis APIs (`js/set-credentials.js:132-187`, `:299-305`), and the plugin fetches the IAB vendor list from `https://vendorlist.consensu.org/v2/vendor-list.json` (`php/class-share-buttons.php:1766-1775`).

The readme says loading is asynchronous (`readme.txt:20-21`, `:103-107`) and states that ShareThis may collect interaction data related to button engagement and usage (`:176-184`). The GDPR UI is optional and explicitly offers enabling/disabling a consent-management tool (`templates/general/gdpr/gdpr-config.php:13-19`, `:104-119`). Its purpose list includes storing/accessing device information through cookies/site identifiers and advertising/profile/measurement purposes (`templates/general/gdpr/purposes.php:17-70`, with additional purposes continuing through `:90-210`). In other words, the plugin is not suitable as a “no remote request/no tracking by default” reference without an explicit privacy boundary and remote-script consent design.

### Markup, accessibility, performance, and compatibility

The downloaded package only emits empty containers; the final button semantics, focus behavior, `rel`, and accessibility labels are supplied by the remote SDK and cannot be audited from this archive. The provider script is registered with `in_footer=false` (`php/class-plugin.php:48-55`), while the readme markets asynchronous loading (`readme.txt:103-107`); actual runtime loading order and cost require browser/network testing. Admin configuration is broad (inline/sticky, block/widget/shortcode/template integration, per-page controls, network ordering, colors, and GDPR) but entails ShareThis property/token/secret configuration (`php/class-share-buttons.php:386-469`).

### Lessons and risks for html-social-share-buttons

- A remote provider can offer many channels and update endpoint behavior without plugin releases, but exact URL behavior, accessibility, and performance become an external dependency.
- Keep the core local and deterministic if privacy and auditability are product goals; make any provider integration an explicit opt-in adapter.
- If a provider is used, document the exact data URL/title contract, load timing, consent gate, subresource policy, and failure fallback. Do not claim “lightweight” from the readme alone.
- Empty containers are not an accessibility guarantee. Render stable accessible labels/fallback links before or alongside provider enhancement.

## 5. Simple Social Buttons 7.1.0

### URL source and exact URL construction

The main renderer resolves a post ID from `get_the_ID()` or the queried object, allows `ssb_get_share_post` to replace it, then uses `get_permalink($post_id)` and an HTML-decoded/rawurlencoded title (`simple-social-buttons.php:1818-1861`). If no post exists it falls back to the site URL/name; image mode shares the image URL and alt/site name (`:1890-1903`). A shortcode `post_url` suffix is appended to the permalink and escaped (`:1920-1921`). This is a post permalink, not a browser-current-URL resolver, with explicit filter and shortcode overrides.

The central helper decodes the permalink and selects network templates in `inc/ssb-utils.php:1364-1417`; its query values consistently use `rawurlencode()`:

| Network | Helper URL form |
|---|---|
| WhatsApp | `https://api.whatsapp.com/send?text={url}` (`inc/ssb-utils.php:1251-1262`) |
| Viber | `viber://forward?text={url}` (`:1264-1275`) |
| LinkedIn | `https://www.linkedin.com/sharing/share-offsite/?url={url}` (`:1277-1288`) |
| X/Twitter | `https://twitter.com/intent/tweet?text={title}&url={url}[&via={handle}]` (`:1290-1307`) |
| Pinterest | `https://www.pinterest.com/pin/create/button/?url={url}[&media={image}][&description={description}]` (`:1337-1361`) |
| Facebook | `https://www.facebook.com/sharer/sharer.php?u={url}` (`:1379-1394`) |
| Reddit | `https://www.reddit.com/submit?url={url}&title={title}` (`:1395-1396`) |
| Threads | `https://www.threads.net/intent/post?text={title} {url}` (`:1398-1398`) |
| Bluesky | `https://bsky.app/intent/compose?text={title} {url}` (`:1400-1400`) |
| Tumblr | `https://www.tumblr.com/widgets/share/tool?canonicalUrl={url}&title={title}` (`:1402-1403`) |
| LINE | `https://social-plugins.line.me/lineit/share?url={url}` (`:1405-1405`) |
| Mastodon helper | `https://mastodonshare.com/?text={title}&url={url}` (`:1406-1408`) |
| VK | `https://vk.com/share.php?url={url}` (`:1409-1410`) |
| Telegram | `https://t.me/share/url?url={url}&text={title}` (`:1411-1413`) |
| Snapchat | `https://www.snapchat.com/share?link={url}` (`inc/ssb-utils.php:1419-1427`) |

**Important duplication defect:** not every rendered branch calls the central helper. The direct Bluesky, Telegram, and Threads branches insert `$permalink` directly after `text=`/`url=` (`simple-social-buttons.php:2104-2124`, `:2134-2155`, `:2166-2202`). The direct Reddit branches likewise concatenate `$permalink` and `$title` (`:2403-2408`, `:2471-2474`). `$permalink` is escaped as a URL at `:1920-1921`, but it is not rawurlencoded as the value of the outer share URL query parameter in these branches. A source URL containing `&`, `?`, or `#` can therefore produce a different query structure than the helper's safe version. This is especially important because the 7.1.0 readme claims a special-character encoding fix (`readme.txt:162-185`) while these legacy branches still exist.

The Tumblr rendering branch also keeps an HTTP endpoint (`simple-social-buttons.php:2710-2715`), while the helper uses HTTPS. The two paths should be unified before copying this design.

### JS, SDKs, tracking, counts, cookies, and remote assets

Front-end local JS/CSS are enqueued when needed (`simple-social-buttons.php:1317-1365`). The JS handles popup/mailto/print/copy actions and click delegation (`assets/js/front.js:121-188`). It sends a beacon or jQuery POST to the WordPress AJAX URL with a nonce, post ID, and network for internal click tracking (`assets/js/front.js:191-239`). The PHP endpoint validates the nonce, rate-limits, and queues an internal count in the `ssb_share_queue` option (`simple-social-buttons.php:895-938`; queue update in `inc/ssb-utils.php:688-779`). A cron later flushes the queue into post metadata/history (`simple-social-buttons.php:940-992`). This is first-party analytics/counting even though it does not use third-party cookies.

The plugin also fetches remote share counts. The fresh-count path builds adapter links and uses `wp_safe_remote_get()` with a five-second timeout and SSL verification (`inc/ssb-utils.php:125-180`, `simple-social-buttons.php:764-792`). Concrete adapters include Facebook Graph (`ssb-social-counts/ssb-facebook.php:59-80`), TwitCount (`ssb-social-counts/ssb-twitter.php:24-36`), LINE metrics (`ssb-social-counts/ssb-line.php:22-32`), and Tumblr stats (`ssb-social-counts/ssb-tumblr.php:38-47`). Count display and API/internal merging are implemented at `inc/ssb-utils.php:620-665` and `simple-social-buttons.php:1926-1933`.

Remote browser assets are conditional: Pinterest injects `https://assets.pinterest.com/js/pinmarklet.js` on a Pinterest action (`assets/js/front.js:160-182`), and selected Snapchat buttons load `https://sdk.snapkit.com/js/v1/create.js` (`simple-social-buttons.php:660-690`). The bundled WPBrigade SDK is initialized at plugin load with an opt-in setting and telemetry configuration (`simple-social-buttons.php:41-117`); its endpoint is `https://app.telemetry.wpbrigade.com/api/v2` (`lib/wpb-sdk/config.php:1-30`). The source clearly contains an opt-in/opt-out flow, but the exact production payload behavior was not runtime-tested.

### Markup, accessibility, admin/configuration, compatibility

The generated controls generally include `aria-label`, text labels, SVG `aria-hidden`, `rel="nofollow"`, and `_blank` behavior; the renderer's base attributes are at `simple-social-buttons.php:1988-2029` and network branches show labels such as `:2070-2097`. The admin is much broader than the other local plugins: positions include inline/sidebar/media/popup/fly-in, network ordering/custom buttons, count settings, social/Snapchat options, and a React settings UI (readme `:20-78`, `:162-207`; admin assets are registered/enqueued around `simple-social-buttons.php:1300-1375`). Compatibility logic includes AMP-specific inline handlers (`:2020-2045`), NextGEN/other integrations in the package, and a low PHP 5.6 metadata floor despite modern JS/admin code.

### Lessons and risks for html-social-share-buttons

- Centralize all network templates and make every rendering mode call the same encoder. This plugin is a direct example of how duplicated legacy branches reintroduce URL bugs after a helper is fixed.
- Keep `rawurlencode()` on each query-component value and `html_entity_decode()` before encoding, as in `inc/ssb-utils.php:1259-1261`, `:1299-1307`, and `:1348-1361`.
- Treat counts as a separate optional feature with explicit first-party storage and remote API disclosures. Do not make click tracking implicit in a privacy-focused default.
- Do not ship third-party Pinterest/Snap SDKs in the base path. If integrations are needed, gate them by network and document their domains/data.
- Use HTTPS consistently; avoid the HTTP Tumblr branch at `simple-social-buttons.php:2713-2715`.
- The plugin's broad placement/admin surface is useful product research, but it also increases compatibility and test burden. Start with a small explicit placement/source model.

## Cross-plugin conclusions for html-social-share-buttons

### URL generation

- **Local, deterministic:** Social Sharing Block and Scriptless Social Sharing are the cleanest references. Both use structured source/title/image data and encode query values before output.
- **Local, but duplicated:** Simple Social Buttons has the broadest network coverage and a good helper, but direct markup branches bypass it (`simple-social-buttons.php:2104-2202`, `:2403-2474`).
- **Browser-generated:** Super Web Share supports native sharing and fallback templates, but placeholder replacement must use component encoding (`public/js/super-web-share-public.js:183-188`).
- **Remote/opaque:** ShareThis delegates final buttons to a property-configured SDK (`php/class-plugin.php:42-56`), so exact URLs cannot be audited from the downloaded plugin.

### Source URL policy

The safest reusable model is an explicit resolver with a documented precedence: custom URL when requested, otherwise post permalink, otherwise an explicit front/home/archive URL rule, and only then browser current URL. Social Sharing Block demonstrates custom/front/archive/singular branches (`utils.php:75-113`); Scriptless makes permalink filterable (`class-scriptlesssocialsharing-button.php:113-120`); Super Web Share uses OG/current metadata (`public/js/super-web-share-public.js:89-124`). Simple Social Buttons provides a post filter and shortcode suffix (`simple-social-buttons.php:1840-1850`, `:1920-1921`). ShareThis is less explicit because the remote SDK resolves containers without a local title contract.

### Privacy/performance baseline

For an HTML-first plugin, the best baseline is local PHP/SVG links, no SDK, no count requests, no cookies, no click beacon, and no remote assets on initial render. Social Sharing Block and Scriptless are closest. Super Web Share adds a small local JS enhancement. Simple Social Buttons and ShareThis should be treated as optional “analytics/provider integrations” rather than baseline references because of remote APIs, SDKs, or counts.

### Accessibility and security baseline

Render a real anchor or button with an accessible name, visible or screen-reader text, `aria-hidden` icons, and safe `rel` on new-tab links. Avoid `href="#"` as the only fallback action. Encode query components, use HTTPS, and avoid `javascript:` URLs unless the action is intentionally handled as a button. Keep custom URL/title filters narrowly scoped and escape the final attribute at output.

## Coverage and uncertainty

- This is static inspection of the five downloaded WordPress.org packages and their local metadata on 2026-09-20. No plugin source was edited.
- No WordPress install, PHP execution, browser automation, CSP test, screen-reader test, or network capture was run. “No evidence found” for a cookie/tracker in a local package is not proof about runtime behavior of a remote SDK.
- ShareThis's final share URL templates, generated markup, network calls, cookies, consent enforcement, and actual async timing live partly or wholly in `platform-api.sharethis.com` and were not included in the archive; those findings are limited to the local SDK loader, containers, privacy text, and configuration code.
- The Simple Social Buttons helper and legacy direct branches were reviewed separately because the package contains both. The direct-branch encoding risks should be verified with URLs containing `&`, `?`, `#`, quotes, Unicode, and HTML entities before any implementation decision.
