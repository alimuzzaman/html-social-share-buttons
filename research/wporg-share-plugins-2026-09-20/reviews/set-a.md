# WordPress.org share-plugin review — set A

Scope: static review of the five downloaded plugin copies assigned to this set. No plugin source was modified. The review traces the value passed to each share endpoint, title/URL encoding, URL source selection, front-end and server-side integrations, configuration, accessibility, privacy/performance signals, and implications for `html-social-share-buttons`.

## Version and compatibility snapshot

| Plugin | Version | WordPress/PHP metadata | WordPress.org snapshot |
|---|---:|---|---|
| AddToAny Share Buttons | 1.8.18 | WP 4.5+, PHP 5.6+ | Tested 7.0.5; 300,000 active installs |
| Sassy Social Share | 3.3.79 | WP 2.5+; no PHP minimum in the downloaded metadata | Tested 6.8.9; 100,000 active installs |
| Simple Share Buttons Adder | 8.7.0 | WP 5.9+; no PHP minimum in the downloaded metadata | Tested 7.1.1; 40,000 active installs |
| Social Pug / Hubbub Lite | 1.36.3.1 | WP 5.3+; PHP 7.2.24+ | Tested 7.1.1; 30,000 active installs |
| Kiwi Social Share | 2.1.9.1 | WP 4.0+; PHP 7.4+ | Tested 7.0.5; 4,000 active installs |

Metadata source: `research/wporg-share-plugins-2026-09-20/metadata/add-to-any.json:L1`, `.../sassy-social-share.json:L1`, `.../simple-share-buttons-adder.json:L1`, `.../social-pug.json:L1`, and `.../kiwi-social-share.json:L1`. The plugin headers independently show the checked-out versions for Sassy (`.../plugins/sassy-social-share/sassy-social-share/sassy-social-share.php:L6-L14`), Simple (`.../plugins/simple-share-buttons-adder/simple-share-buttons-adder.php:L3-L9`), Hubbub (`.../plugins/social-pug/social-pug/index.php:L3-L13`), and Kiwi (`.../plugins/kiwi-social-share/kiwi-social-share/kiwi-social-share.php:L3-L12`).

## Executive findings

1. The safest URL pipeline in this set is Social Pug: it chooses a permalink (or an explicit shortcode URL), then applies filters and `rawurlencode()` to URL, title, and description in one place before formatting the network template (`.../social-pug/inc/functions.php:L299-L335`; permalink/filter source at `.../social-pug/inc/functions-post.php:L62-L75`). AddToAny also has a strong central encoder, but most share behavior is delegated to the AddToAny remote `page.js` runtime (`.../add-to-any/add-to-any.php:L1119-L1146`).
2. Simple Share Buttons Adder is the largest URL-construction risk. Its `get_share_url()` interpolates most `$title`, `$description`, `$share_url`, and `$image` values directly into templates; only its AI prompt is encoded (`.../simple-share-buttons-adder/php/class-buttons.php:L1001-L1018`). Its current-page shortcode URL is also assembled from raw request components (`.../php/class-buttons.php:L432-L462`).
3. Kiwi encodes most URLs but not all titles/text. Twitter and Telegram insert a stripped title without URL encoding; Skype builds a malformed-looking `&lang=<locale>=` parameter; Email escapes the title for HTML instead of query-string encoding it (`.../kiwi-social-share/includes/frontend/social-buttons/class-kiwi-social-share-social-button-twitter.php:L24-L47`, `.../telegram.php:L22-L36`, `.../skype.php:L22-L30`, `.../email.php:L22-L32`).
4. Sassy has broad network coverage, but several old SDK templates embed third-party scripts and nested anchors. Its direct-link templates are generally centrally substituted, while SDK providers receive raw or decoded values through data attributes (`.../sassy-social-share/includes/class-sassy-social-share-sharing-networks.php:L39-L59`; substitution at `.../public/class-sassy-social-share-public.php:L483-L525`).
5. Counts are not a free feature: every plugin in this set has at least one remote count/telemetry path. AddToAny limits built-in counters to Pinterest, Reddit, and Tumblr (`.../add-to-any/add-to-any.php:L318-L324`); Sassy, Simple, Kiwi, and Social Pug make server-side requests to social/count APIs. Hubbub additionally contains a product-data sync request carrying site and administrator data (`.../social-pug/inc/class-data-sync.php:L151-L181`).

## 1. AddToAny Share Buttons 1.8.18

### Share URL generation

AddToAny’s core helper accepts explicit `linkname`, `linkurl`, and `linkmedia`, with a `use_current_page` switch (`.../add-to-any/add-to-any.php:L52-L58`). If the title is omitted, current-page mode uses the site name on home/front pages or `wp_title()` elsewhere; post mode uses the post title after stripping tags and decoding entities (`L60-L69`). It then applies `rawurlencode()` to the title (`L71`) and URL (`L84`), and to media when present (`L86-L89`).

URL source behavior is explicit:

- current-page mode: `esc_url_raw( home_url( $_SERVER['REQUEST_URI'] ) )` (`L73-L82`), so the request path and query are intentionally shared;
- normal post mode: `get_permalink( $post->ID )` (`L74-L80`);
- explicit caller values win over both defaults (`L55-L58`, `L73-L82`);
- the helper does not itself select a separate canonical URL or Yoast URL. Filters/callers can provide one through `linkurl`.

The normal per-network fallback is not a hard-coded table in this file. It is generated as `https://www.addtoany.com/add_to/<service>?linkurl=<encoded URL>&linkname=<encoded title>` (`L338-L343`). A service with a custom `href` uses `A2A_LINKURL` and `A2A_LINKNAME`; those placeholders receive encoded values unless the service explicitly opts into JavaScript escaping (`L302-L316`). This makes custom service templates possible, but their correctness depends on the template author.

The universal button points to `https://www.addtoany.com/share` and, for feed/AMP output, appends an encoded `#url=...&title=...` fragment (`L420-L430`, `L473-L476`). It also exposes the unencoded caller values in `data-a2a-url`, `data-a2a-title`, and `data-a2a-media` for the AddToAny runtime (`L425-L427`).

### Runtime, external assets, counts, and privacy

- Front-end output is skipped in admin, feeds, disabled pages, and AMP; singular posts can opt out with the `sharing_disabled` meta key or a filter (`.../add-to-any/add-to-any.php:L1004-L1021`).
- The standard runtime is external `https://static.addtoany.com/menu/page.js`; it is registered with `defer`, and a local jQuery-dependent helper is optionally loaded (`L1119-L1146`).
- Optional local caching does not make the plugin fully self-contained: the cache refresh downloads a remote file list from `https://www.addtoany.com/ext/updater/files_list/` and then downloads each listed file into uploads, on a daily schedule (`L1152-L1192`).
- Built-in share counters are opt-in and limited to Pinterest, Reddit, and Tumblr (`L318-L324`). No counter endpoint is hard-coded in this rendering block; the AddToAny runtime handles those services.
- The AMP universal icon defaults to `https://static.addtoany.com/buttons/a2a.svg` (`L326-L332`, `L447-L452`).

### Configuration, accessibility, performance, compatibility

The generated ordinary service anchors get a title, a new-tab target, and `rel="nofollow noopener"` (unless basic/custom HTML changes that path) (`.../add-to-any/add-to-any.php:L338-L356`). The universal image has `alt="Share"` (`L456-L464`). Settings include counters and local caching; the enqueue path exposes a filter to disable the script (`L1004-L1012`). Deferred loading and AMP-specific output are positive performance signals, while remote menu JavaScript and remote cache downloads are external-runtime and supply-chain dependencies.

### Lessons and risks for `html-social-share-buttons`

- Keep AddToAny’s central input object idea: one function should resolve URL/title/media and produce both encoded query values and display values.
- Keep the explicit distinction between permalink mode and current-request mode. Do not silently use `REQUEST_URI` when a post permalink or a caller-supplied canonical URL is available.
- Treat service templates as untrusted configuration. Validate placeholders and encode by parameter at the final boundary; avoid a JavaScript-escape escape hatch unless a service truly needs a JS literal.
- Prefer a self-hosted small adapter over a mandatory remote runtime. If an external runtime is retained, make it an explicit opt-in with a clear privacy/performance note and a script-disable filter.

## 2. Sassy Social Share 3.3.79

### Share URL generation and network map

Sassy’s plugin header says version 3.3.79 (`.../sassy-social-share/sassy-social-share.php:L6-L14`). Its network registry combines SDK markup and direct links. The key templates are:

| Network | Template / behavior | Encoding at template boundary |
|---|---|---|
| Facebook Share/Like/Recommend | SDK elements with `data-href="%post_url%"` | data attributes receive the substituted post URL |
| Twitter | `https://twitter.com/share`, `data-url`, `data-counturl`, `data-text`, optional `data-via` | values are attributes; SDK performs final behavior |
| LinkedIn | `//platform.linkedin.com/in.js` + `IN/Share` with `data-url` | SDK markup |
| Pinterest | `//www.pinterest.com/pin/create/button/?url=%post_url%` plus `pinit.js` | URL placeholder in external widget markup |
| Buffer | `https://bufferapp.com/add` with `data-text` and `data-url` | data attributes |
| Xing / Yummly / Reddit | external share scripts | script/widget supplied values |
| Facebook direct | `https://www.facebook.com/sharer/sharer.php?u=%encoded_post_url%` | encoded URL |
| X/Twitter direct | `https://twitter.com/intent/tweet?...text=%wpseo_post_title%&url=%encoded_post_url%` | encoded URL and sanitized/encoded SEO title |
| LinkedIn direct | `https://www.linkedin.com/sharing/share-offsite/?url=%encoded_post_url%` | encoded URL |
| Reddit | `https://reddit.com/submit?url=%encoded_post_url%&title=%post_title%` | encoded URL and encoded title |
| MeWe | `https://mewe.com/share?link=%encoded_post_url%` | encoded URL |
| Gettr/Parler/Gab | network endpoint with `%post_title%` and `%encoded_post_url%` | title placeholder is the plugin’s encoded post title |
| Tumblr | `https://www.tumblr.com/widgets/share/tool?posttype=link&canonicalUrl=%encoded_post_url%&title=%post_title%&caption=` | encoded URL/title |
| Mastodon | `https://mastodon.social/share?text=%encoded_post_url%&title=%post_title%` | encoded URL/title, though the endpoint assigns URL to `text` |
| Email | JavaScript `mailto:` builder using `decodeURIComponent(%post_title%)` and encoded URL | title is decoded into JS then only `&` is replaced |
| Pinterest direct | JavaScript pinmarklet with raw `%post_url%` | bookmarklet path, not the normal encoded direct URL |

Evidence for the registry and templates is `.../sassy-social-share/includes/class-sassy-social-share-sharing-networks.php:L39-L69`. Placeholder substitution is at `.../public/class-sassy-social-share-public.php:L483-L525`: `%post_url%` is raw, `%encoded_post_url%` is `urlencode($post_url)`, `%post_title%` is the plugin’s `sanitize_post_title()` result, and `%decoded_post_title%` is a deliberately partially decoded form. `sanitize_post_title()` uses entity decode, `rawurlencode()`, and HTML escaping (`.../public/class-sassy-social-share-public.php:L281-L288`).

### URL source selection

Horizontal rendering starts with `get_permalink($post->ID)` (`.../public/class-sassy-social-share-public.php:L710-L715`). In the default target mode, if a query string exists or the permalink is empty, Sassy replaces it with the current request (`protocol + HTTP_HOST + REQUEST_URI`) (`L721-L727`). Users can instead choose the home URL (`L728-L731`) or a configured custom URL (`L732-L736`). A filter runs after this selection and can replace the target (`L739-L745`). Short-link generation then runs on the selected URL (`L745-L748`). The vertical renderer follows the same model in `L795-L836`.

### Runtime, external assets, counts, and privacy

- Front-end initialization enqueues local plugin JavaScript/CSS, but inline code can load Facebook SDK v23.0 from `//connect.facebook.net/.../sdk.js` and configures `cookie:!0`, `xfbml:!0` (`.../public/class-sassy-social-share-public.php:L104-L135`). The same area enables popup behavior and optional analytics/MyCred hooks.
- The registry embeds Twitter widgets, LinkedIn `in.js`, Pinterest `pinit.js`, Buffer’s CloudFront button script, Xing share.js, Yummly widget.js, and Reddit’s button script (`.../includes/class-sassy-social-share-sharing-networks.php:L43-L49`). These are third-party requests made from the visitor’s page.
- Optional Bitly shortening posts a JSON `long_url` to `https://api-ssl.bitly.com/v4/bitlinks`, disables SSL verification (`'sslverify' => false`), and stores the result in post meta (`.../public/class-sassy-social-share-public.php:L192-L231`). This is a concrete security/privacy risk and should not be copied.
- Share counts are fetched server-side with 15-second remote requests. The current source includes TwitCount, Reddit, Pinterest, Buffer, VK, Odnoklassniki, and Fintel endpoints (`.../public/class-sassy-social-share-public.php:L982-L1015`).
- Admin actions for Bitly/cache and GDPR notifications are nonce/capability checked (`.../admin/class-sassy-social-share-admin.php:L520-L551`, `L573-L584`). The admin text tells the site owner to update their privacy policy; this is not a proof that third-party SDKs are consent-gated.

### Accessibility, performance, compatibility

Most direct anchors carry `aria-label`, `title`, `target="_blank"`, and `rel="nofollow noopener"` in the registry (`.../includes/class-sassy-social-share-sharing-networks.php:L50-L69`). The SDK templates, however, contain nested `<a>` elements (for example Twitter, Pinterest, and Buffer), which is invalid interactive HTML and can confuse keyboard/screen-reader behavior (`L43-L46`). The “more sharing” JavaScript uses `navigator.share()` when available, otherwise creates a popup and concatenates several redirect URLs (`.../public/js/sassy-social-share-public.js:L5-L27`); those JS-only routes do not consistently encode title text.

### Lessons and risks for `html-social-share-buttons`

- Use Sassy’s useful target policy as a model: permalink first, current request only when query parameters make it necessary, then home/custom/filter overrides.
- Do not nest anchors to host SDK widgets. Render one semantic link or button per network and mount an SDK in a separate non-interactive container.
- Make third-party scripts opt-in and consent-aware. `cookie:!0` in the Facebook SDK initialization and several always-present widget `<script>` tags are high-risk defaults.
- Never disable TLS verification for link shortening. If shortening is offered, use a modern authenticated API over verified TLS, cache failures safely, and make it optional.

## 3. Simple Share Buttons Adder 8.7.0

### Share URL generation and network map

The plugin header identifies version 8.7.0 (`.../simple-share-buttons-adder.php:L3-L9`). Standard buttons use the post permalink and title; shortcode buttons accept an explicit `url` and `title`, or build the current request URL (`.../php/class-buttons.php:L265-L280`). The title is stripped and `esc_attr()`-escaped before being passed to the URL builder (`L276-L280`), but that is HTML escaping, not query-string encoding.

`get_share_url()` is a large template map (`.../php/class-buttons.php:L1001-L1091`). The important entries are:

| Network | Generated URL in source |
|---|---|
| Blogger | `https://www.blogger.com/blog-this.g?n={$title}&t={$description}&u={$share_url}` |
| Bluesky | `https://bsky.app/intent/compose?text={$title} | {$share_url}` |
| Buffer | `https://buffer.com/add?text={$title}&url={$share_url}` |
| Diaspora / Delicious / Douban | title and URL query parameters |
| Facebook | `https://www.facebook.com/sharer.php?t={$title}&u={$share_url}` |
| LinkedIn | `https://www.linkedin.com/shareArticle?title={$title}&url={$share_url}` |
| Messenger | Facebook dialog with raw `link={$share_url}`, app ID, and redirect URI |
| Pinterest | `https://pinterest.com/pin/create/button/?description={$title}&media={$image}&url={$share_url}` |
| Reddit | `https://reddit.com/submit?title={$title}&url={$share_url}` |
| Telegram | `https://t.me/share/url?url={$share_url}&text={$title}&to=` |
| Threads | `https://threads.net/intent/post?text={$share_url}` |
| Tumblr | `https://www.tumblr.com/share?t={$title}&u={$share_url}&v=3` |
| Twitter/X | `https://twitter.com/intent/tweet?text={$title}&url={$share_url}&via={$username}` |
| VK | `https://vk.com/share.php?url={$share_url}` |
| WhatsApp | `https://web.whatsapp.com/send?text={$share_url}`; mobile overrides to `whatsapp://send?text={$share_url}` |
| Yummly / Xing | vendor-specific URL templates |
| Email | `mailto:?subject={$title}&body={$share_url}` |
| AI assistants | ChatGPT/Claude/Grok/Perplexity encode a combined prompt; Copilot and Gemini open their home pages |
| Copy | returns the raw `$share_url` |

The same source shows malformed or incomplete query keys in some templates (`gmail` uses `&su{$title}&body{$share_url}`, `googlebookmarks` uses `&title{$title}&annotation{$description}`, and similar entries) (`L1037-L1049`). Mobile overrides are at `L1084-L1088`.

The key correctness problem is that almost all ordinary values are interpolated raw. The function creates an encoded `$ai_prompt` only (`L1016-L1018`), then inserts raw `$title`, `$description`, `$share_url`, `$image`, and related values (`L1020-L1081`). A title containing `&`, `#`, `?`, non-ASCII characters, or quotes can add/alter parameters. A URL with an existing query is not protected unless the caller happened to pre-encode it. `get_button()` passes the HTML-escaped title and the URL directly into this builder (`.../php/class-buttons.php:L639-L642`).

Shortcode current URL generation has two branches. Multisite mode uses `home_url($wp->request)` and a sanitized query string (`.../php/class-buttons.php:L439-L446`); otherwise it builds `http(s)://SERVER_NAME.REQUEST_URI` (`L448-L462`). The special server name `_` falls back to `get_permalink($post->ID)` (`L457-L460`). This is current-request behavior, not canonical URL resolution, and it has no explicit caller-level policy beyond the shortcode’s `url` override (`L270-L274`).

### Runtime, external assets, counts, and privacy

- The plugin enqueues its local `ssba.js`, but also registers Google Fonts, Font Awesome from MaxCDN, and (when configured) ShareThis’s external GDPR compliance script (`.../php/class-plugin.php:L52-L99`).
- When ShareThis terms are accepted, it loads `st_insights.js` from ShareThis with a hard-coded publisher UUID and `product=simpleshare`; a property ID switches to ShareThis’s platform API script (`.../php/class-styles.php:L49-L84`).
- An optional Facebook SDK is inserted with inline loader code and `autoLogAppEvents:true`, `xfbml:true`, and SDK v13.0 (`.../templates/facebook-sdk.php:L22-L46`).
- Share counts can use SharedCount (`https://<plan>.sharedcount.com/url?...`) with a six-second timeout (`.../php/class-buttons.php:L768-L803`) or legacy Facebook/Reddit/Pinterest/LinkedIn/StumbleUpon/Tumblr/Yummly endpoints (`L832-L843`). Responses are cached in object cache/transients (`L789-L795`, `L817-L821`). There appears to be a likely Facebook fallback bug: the fetched value is assigned to `$int_facebook_share_count` but `$int_share_count` is what gets cached/returned (`L810-L821`); this should be verified with a live regression test before relying on counts.
- The local JS intercepts clicks, opens AI/popup windows, and supplies a clipboard fallback (`.../js/ssba.js:L61-L119`, `L181-L231`).

### Configuration, accessibility, performance, compatibility

The output offers configurable `rel=nofollow`, new-window behavior, image/icon sets, text labels, and optional counts (`.../php/class-buttons.php:L628-L721`). Legacy image buttons expose `alt="Share on <network>"`; the newer button path includes visible network text (`L681-L703`). This is a useful baseline, although the dynamically assembled `href` values are not passed through a final URL-encoding/validation layer.

Admin ShareThis property/token and GDPR settings use AJAX handlers with nonces and sanitized values (`.../php/class-admin-bits.php:L137-L160`, `L317-L338`). The plugin still has a substantial third-party asset and telemetry surface, even though ShareThis terms are checked before the insights script is loaded.

### Lessons and risks for `html-social-share-buttons`

- Treat this plugin as the negative example for URL construction. `esc_attr()` protects an HTML attribute; it does not encode a value inside a URL query string.
- Build each endpoint with `add_query_arg()`/`http_build_query()` or a dedicated RFC 3986 encoder, then HTML-escape the complete resulting URL exactly once at output.
- Add automated vectors for ampersands, hashes, Unicode, quotes, existing query strings, and custom images. Include mailto and mobile-app URI schemes.
- Keep third-party analytics/count scripts behind explicit settings and consent. The ShareThis publisher ID and Facebook `autoLogAppEvents` show why “optional” should be observable and tested.

## 4. Social Pug / Hubbub Lite 1.36.3.1

### Share URL generation and network map

The downloaded plugin is named Hubbub Lite in its header, version 1.36.3.1, with WP 5.3 and PHP 7.2.24 requirements (`.../social-pug/index.php:L3-L13`). The free network registry defines:

| Network | Share format |
|---|---|
| Facebook | `https://www.facebook.com/sharer/sharer.php?u=%1$s&t=%2$s` |
| Twitter | `https://twitter.com/intent/tweet?text=%2$s&url=%1$s` |
| X alternative | `https://x.com/intent/tweet?text=%2$s&url=%1$s` |
| Pinterest | `https://pinterest.com/pin/create/button/?url=%1$s&media=%3$s&description=%2$s` |
| LinkedIn | `https://www.linkedin.com/shareArticle?url=%1$s&title=%2$s&summary=%4$s&mini=true` |
| Email | `mailto:?subject=%2$s&amp;body=%1$s` |
| Grow | `#` placeholder for the Grow widget |
| Print | `#` placeholder for client-side print behavior |

Evidence: `.../social-pug/inc/networks/class-networks.php:L53-L107`. The Pro network class is present in the downloaded source but is merged only when its Pro class exists (`L108-L110`). Its templates include Reddit, VK, WhatsApp, Pocket, Buffer, Tumblr, Xing, Flipboard, Telegram, Mix, Threads, Messenger, and Bluesky (`.../social-pug/inc/networks/class-pro-networks.php:L15-L143`).

The important design decision is centralized encoding. `dpsp_get_network_share_link()` obtains a default post URL/title/description, allows late filters, then applies `rawurlencode()` to URL, title, and description before passing them to the network object (`.../social-pug/inc/functions.php:L299-L335`). The custom image is separately encoded by the button outputter when it comes from post meta (`.../class-dpsp-network-buttons-outputter.php:L109-L118`). Network formatting is a straightforward `sprintf()` followed by a Twitter `via` option and a Pinterest behavior override (`.../inc/networks/class-network.php:L145-L166`).

The URL source is the post permalink passed through `dpsp_get_post_url` (`.../inc/functions-post.php:L62-L75`). For ordinary output, the button outputter uses that URL/title/description; a shortcode can replace the URL and description (`.../class-dpsp-network-buttons-outputter.php:L84-L107`). There is no current-request fallback in this core helper. This gives a stable canonical-ish post URL, but the site can still override it through filters or shortcode data.

### Runtime, external assets, counts, and privacy

- Share buttons use bundled front-end assets. The loader chooses free/pro and optional jQuery bundles, localizes an AJAX URL/nonce for the relevant feature, and enqueues them (`.../social-pug/inc/class-asset-loader.php:L94-L140`). It adds `async`, `data-noptimize`, and Cloudflare Rocket Loader opt-out attributes for its scripts (`L39-L67`). No social share SDK script was found in the free share-button path.
- Counts are server-side. The plugin URL-encodes the page URL and uses a ten-second timeout (`.../inc/functions-share-counts.php:L149-L163`). Facebook (when a token is configured), Pinterest, and Grow GraphQL endpoints are assembled and requested with `wp_remote_get()` (`L164-L203`).
- Facebook/Pinterest can optionally combine HTTP and HTTPS counts by requesting both forms and summing them (`.../inc/functions-share-counts.php:L82-L129`), which is a useful migration/permalink feature but increases remote requests.
- Settings expose UTM tracking fields (`utm_tracking`, `utm_source`, `utm_medium`, `utm_campaign`) in the API schema (`.../inc/api/v1/settings-partials.php:L176-L194`). This can intentionally alter shared destinations; it should be explicit in any product’s URL contract.
- The source also contains a product-data synchronization request carrying PHP/WP/MySQL versions, current user email, license, site URL/domain, and admin emails to `https://product-data-sync.herokuapp.com/record` (`.../inc/class-data-sync.php:L151-L181`). This appears to be product/admin telemetry rather than per-click share tracking, but it is material to a privacy review.

### Configuration, accessibility, performance, compatibility

The button output supports post-type/location settings, inline and floating bars, shortcode URLs, labels/counts, and an explicit `rel` filter. Default output uses `nofollow noopener` (`.../class-dpsp-network-buttons-outputter.php:L219-L229`); share links get a translated `title="Share on %s"` and the outputter can render semantic button elements for Pinterest/Grow/Mastodon/Messenger (`L201-L216`, `L234-L245`). The local asset loader is cited above; this review did not find a social SDK in the free share-button path.

### Lessons and risks for `html-social-share-buttons`

- Adopt Social Pug’s single late-encoding boundary. The useful contract is: resolve source values, apply documented filters, encode URL/title/description, then call a dumb template.
- Support a deliberate custom URL input for shortcodes/components, but do not silently convert every archive/request URL into the current request. Make canonical/permalink/current-request/custom modes visible in configuration and tests.
- Keep count retrieval server-side and cached, but isolate it from rendering so a failing network cannot block page output. Test protocol migration behavior if HTTP/HTTPS aggregation is offered.
- Treat UTM and telemetry as separate features with clear user-facing privacy settings. Product-data sync should be documented and consent/opt-out behavior verified before adoption.

## 5. Kiwi Social Share 2.1.9.1

### Share URL generation and URL source

The plugin header is version 2.1.9.1, requires WP 4.0, and declares tested up to 7.0 (`.../kiwi-social-share/kiwi-social-share.php:L3-L12`). The base social-button constructor uses the current post ID, but changes it to the marker `fp` for front page, archive, date, category, or home views (`.../includes/frontend/social-buttons/class-kiwi-social-share-social-button.php:L41-L52`). `get_current_page_url()` then returns `get_home_url()` for `fp`, otherwise `get_the_permalink($id)` (`L178-L184`). There is no general `REQUEST_URI` or custom canonical URL branch in these share-button classes.

The source contains Pro-only Bitly branches in the free package. If both configured Bitly credentials are present, the base constructor calls `set_short_link()` (`.../class-kiwi-social-share-social-button.php:L46-L52`); each network then replaces the permalink with `short_url` when available. The Bitly adapter calls the old v3 endpoint with `login`, `apiKey`, and `longUrl` query parameters (`.../includes/lib/class-kiwi-social-share-bitly.php:L39-L80`) and caches the result for one week (`.../class-kiwi-social-share-social-button.php:L88-L106`). This exposes credentials in a URL and is not suitable for a new implementation.

The network-specific URL builders are:

| Network | Generated URL and encoding |
|---|---|
| Facebook | `https://www.facebook.com/sharer.php?u=` + `rawurlencode($url)` (`.../social-button-facebook.php:L23-L34`) |
| Twitter | `https://twitter.com/intent/tweet?text=` + raw title + `&url=` + encoded URL + optional raw `via` (`.../social-button-twitter.php:L24-L47`) |
| LinkedIn | `https://linkedin.com/shareArticle?mini=true&url=` + encoded URL + `&title=` + `urlencode($desc)` (`.../social-button-linkedin.php:L23-L38`) |
| Pinterest | `https://pinterest.com/pin/create/button/?url=` + encoded URL + `&description=` + `urlencode($desc)`; media is appended raw (`.../social-button-pinterest.php:L23-L55`) |
| Reddit | `https://reddit.com/submit?url=` + encoded URL (`.../social-button-reddit.php:L23-L31`) |
| Email | `mailto:?subject=` + `esc_attr(get_the_title())` + `&body=` + encoded URL (`.../social-button-email.php:L22-L32`) |
| Telegram | `https://telegram.me/share/url?url=` + encoded URL + `&text=` + stripped, raw title (`.../social-button-telegram.php:L22-L36`) |
| WhatsApp | `https://wa.me/?text=Look at this: ` + `urlencode($desc)` + ` - ` + encoded URL (`.../social-button-whatsapp.php:L22-L37`) |
| Skype | `https://web.skype.com/share?url=` + encoded URL + `&lang=` + locale + `=&source=kiwi` (`.../social-button-skype.php:L22-L30`) |
| Mix | `https://mix.com/add?url=` + encoded URL (`.../social-button-mix.php:L22-L25`) |
| Fintel | `https://fintel.io/share?url=` + encoded URL (`.../social-button-fintel.php:L22-L25`) |

The central weakness is inconsistent title/text encoding. Twitter and Telegram insert plain stripped titles; Email uses HTML escaping in a `mailto:` query; WhatsApp mixes literal spaces, translated text, `urlencode()`, and `rawurlencode()`; Pinterest appends a media URL without encoding (`.../social-button-twitter.php:L24-L47`, `.../telegram.php:L22-L36`, `.../email.php:L22-L32`, `.../whatsapp.php:L22-L37`, `.../pinterest.php:L41-L55`). `esc_url()` at output protects the final HTML URL but cannot repair a title that already changed query parsing.

### Runtime, external assets, counts, and privacy

- Kiwi registers bundled local front-end JS/CSS and depends on jQuery (`.../includes/class-kiwi-social-share.php:L115-L119`, `L202-L218`). It also loads Google Fonts in the admin (`L227-L238`), not as a front-end sharing dependency.
- Popup clicks are intercepted by local JS, which calls `window.open()` and optionally sends Google Analytics events if a global `ga` function exists and tracking is enabled (`.../assets/js/kiwi.js:L443-L475`). Tracking is configured on the PHP-generated bar via `data-tracking="true"` (`.../includes/frontend/social-bars/class-kiwi-social-share-view-article-bar.php:L153-L158`, `.../class-kiwi-social-share-view-floating-bar.php:L83-L88`).
- Counts are synchronous server-side `wp_remote_get()` calls from the button base, cached in a two-hour transient (`.../social-button.php:L110-L161`). The network classes point at legacy HTTP/HTTPS count APIs: TwitCount (`twitter.php:L18`), Facebook Graph with app credentials (`facebook.php:L17`), LinkedIn (`linkedin.php:L17`), Pinterest (`pinterest.php:L17`), and Reddit (`reddit.php:L17`). Rendering a button can therefore cause a remote count request unless a transient is warm.
- The admin uses a `tab` cookie to remember the selected settings tab. It sanitizes and allow-lists the value server-side (`.../includes/backend/kiwi-social-share-backend.php:L27-L46`); the local admin JS writes `document.cookie='tab='+tab` (`.../assets/js/kiwi.js:L140-L151`). No front-end cookie setter was found in this static pass.
- Kiwi’s only obvious non-plugin front-end requests are the destination share links and server-side counts. The Bitly and optional checkout/licensing paths are external and should be kept out of a minimal/free runtime (`.../includes/lib/class-kiwi-social-share-bitly.php:L39-L96`; checkout navigation in `.../assets/js/kiwi.js:L430-L438`).

### Configuration, accessibility, performance, compatibility

Settings are registered under WordPress’s Settings API with `manage_options`, network ordering, colors, social identities, and advanced settings (`.../includes/lib/class-kiwi-social-share-settings.php:L53-L72`, `L88-L100`, `L121-L191`). Article and floating bars can be enabled independently, restricted by post type in the Pro-marked code paths, and are skipped on AMP for article output (`.../includes/frontend/social-bars/class-kiwi-social-share-view-article-bar.php:L27-L54`, `L78-L125`; floating setup at `.../class-kiwi-social-share-view-floating-bar.php:L10-L27`).

Buttons use `target="_blank"`, `rel="nofollow"`, a network data attribute, and icon spans (`.../social-button-facebook.php:L36-L41` and the analogous button classes). Floating labels are visible and text-labeled (`.../social-bars/class-kiwi-social-share-view-floating-bar.php:L91-L121`), but most individual anchors lack an explicit accessible name beyond the icon class/visible label and do not add `noopener` to `rel`. This is weaker than AddToAny/Social Pug.

### Lessons and risks for `html-social-share-buttons`

- Use a single resolver for post permalink, archive/home URL, and explicitly provided custom URL. Kiwi’s simple `fp` marker is understandable, but `get_home_url()` for every archive/current front context may not represent the actual filtered archive URL.
- Encode every title, description, image, and URL parameter at the final boundary. Copy Kiwi’s `rawurlencode()` use for URLs, not its raw titles/media.
- Do not make count requests synchronously during button construction. Queue/cache them or render counts asynchronously, with strict timeouts and a failure-safe page path.
- If analytics is supported, use a documented event API with consent integration. Detecting a global `ga` and then emitting an event is too implicit for a privacy-sensitive product.
- Add `noopener` whenever a link opens a new tab and provide a real accessible name (`aria-label` or visible text), not just a network CSS class.

## Cross-plugin implementation checklist for `html-social-share-buttons`

### URL contract

- Resolve one `ShareContext` containing `url`, `title`, `description`, and optional `image`, plus an explicit source mode: `permalink`, `current_request`, `home`, or `custom`.
- Default to the post permalink for singular content. Preserve query parameters only when the product explicitly chooses current-request mode.
- Apply filters/customization before encoding.
- Encode each query value with RFC 3986 semantics (`rawurlencode()`/equivalent), then escape the complete final URL for HTML output. Never treat `esc_attr()` or `esc_url()` as a substitute for query encoding.
- Keep provider templates data-only. Validate allowed schemes and required placeholders. Reject malformed templates instead of allowing arbitrary script URLs.
- Add fixtures for `A & B`, `100%`, `#fragment`, existing `?x=1&y=2`, Unicode, quotes, line breaks, and custom images.

### Runtime and privacy

- Keep ordinary share links local and static. Do not require a remote SDK for a button that can be represented by a normal link.
- Make counters, link shorteners, analytics, and remote widgets separate opt-in modules. Document each endpoint and whether it receives a visitor URL, title, IP, cookie, or credential.
- Use verified TLS; never copy Sassy’s `sslverify => false` or Kiwi’s credential-bearing Bitly v3 URL pattern.
- Cache count results with bounded timeouts and never block page rendering on a remote request.
- Add a clear “no remote requests” mode and test it with an HTTP-request interception harness.

### Markup, accessibility, and compatibility

- Render one anchor/button per network; do not nest provider anchors or inject multiple interactive elements inside one link.
- Use `aria-label="Share on <network>"`, visible labels where configured, `target="_blank"` only when useful, and `rel="nofollow noopener"` for external new-tab links.
- Support keyboard activation, reduced motion, RTL, and AMP/plain-HTML fallback without JavaScript.
- Declare and test the actual PHP/WP floor. The set ranges from old WP/PHP declarations (AddToAny/Kiwi) to PHP 7.2+ (Hubbub), so compatibility must be intentional rather than inferred from current development PHP.

## Uncertainty and coverage note

This is a static source review of the downloaded WordPress.org archives and metadata captured on 2026-09-20. It does not execute WordPress, instantiate the plugins, follow redirects, inspect provider responses, or prove which settings are enabled by default on a fresh site. Minified JavaScript was inspected where its behavior affected URLs, popups, tracking, or cookies, but not every duplicated minified asset was decompiled. Social Pug’s Pro-only network code is present in the package but is reported as conditional because the free runtime merges it only when the Pro class exists. AddToAny’s final provider behavior is partly in remote `page.js`, so the static PHP templates cannot prove every network’s current endpoint behavior. The report therefore distinguishes source-confirmed behavior from runtime-dependent behavior and flags live verification where it matters.
