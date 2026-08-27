# WordPress.org listing copy — 3.1.0

This is the reviewed local copy for the 3.1.0 release candidate. The
repository contains the updated metadata and captions, but WordPress.org SVN
upload and publication remain separate authorized release actions.

## 1. Short description in `readme.txt`

The short description is now this 117-character line:

> HTML + CSS share buttons and profile links with blocks, builders, local icons, and no frontend JavaScript by default.

This stays below the WordPress.org 150-character limit, restores the
plugin's original positioning, and remains accurate because frontend
JavaScript is optional rather than absent in every configuration.

## 2. Main plugin header

The installed Plugins screen uses the same sentence:

> HTML + CSS share buttons and profile links with blocks, builders, local icons, and no frontend JavaScript by default.

The Plugin URI is HTTPS:

> https://wordpress.org/plugins/html-social-share-buttons/

## 3. Opening description

The opening description now reads:

> HTML Social Share Buttons adds server-rendered HTML and CSS share buttons and profile links to posts, pages, sidebars, and builder layouts. By default, the plugin adds no frontend JavaScript. Bundled icons load from your own site, and optional Google Social Analytics stays off unless you enable it.
>
> Use the Social Share or Social Links block, automatic placement, the classic widget, Elementor, WPBakery, a shortcode, or PHP. Choose from six bundled icon sets and square or circle buttons.

The opening summary is followed by these scannable groups before the shortcode
examples:

- **HTML + CSS by default:** Server-rendered share and profile links with no frontend plugin JavaScript unless optional analytics is enabled.
- **Private by default:** Bundled icons load from your site, with no remote icon CDN, and optional analytics is disabled until enabled.
- **Place anywhere:** Automatic before/after and floating placement, two blocks, a classic widget, Elementor, WPBakery, shortcodes, and PHP.
- **Share and follow separately:** Share actions open a composer for the current page; profile links open the configured profile or email destination.
- **Current destinations:** Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email.
- **Choose the presentation:** Legacy preserves the existing pack behavior; Minimal, Framed, and Soft shadow add modern frontend styles.

Keep the current detailed FAQ and shortcode examples after this summary.

## 4. Screenshots section

The local `readme.txt` now contains these captions. Upload matching current
images to WordPress.org SVN `/assets` during the authorized publication step:

```text
== Screenshots ==

1. Inline share buttons and a responsive floating rail on a post.
2. Appearance, placement, audience, and analytics controls in Settings.
3. Social Share and Social Links blocks in the block editor.
4. Separate profile-link destinations and viewer-audience controls.
```

The Elementor caption is intentionally omitted until the current Elementor UI
and public output are verified together. Use neutral content and no personal
data in every uploaded image.

## 5. Readme size and changelog

The updated `readme.txt` is 7,947 bytes, below WordPress.org's 10 KB warning
threshold. It keeps the 3.1.0 entry and moves the 3.0.0 and earlier history to
the distributable `changelog.txt` file.

The local stable tag is `3.1.0`. Update the authoritative tagged readme as part
of the normal SVN metadata workflow; changing only SVN trunk will not update
the public description.

## 6. Repository `README.md`

The developer README now describes the 3.1.0 appearance update after the
published 3.0.0 release:

> The current `master` branch contains the completed canonical implementation rewrite and the 3.1.0 frontend button-appearance update after the published 3.0.0 release. Version 3.1.0 adds a global appearance selector with Legacy, Minimal, Framed, and Soft shadow modes while keeping Legacy as the compatibility default.
>
> Repository evidence does not authorize a new tag, WordPress.org upload, or production deployment. Future releases still require an immutable reviewed revision and explicit approval.

The final evidence-boundary bullet records the historical 3.0.0 review and keeps
the fresh immutable-archive requirement for 3.1.0.
