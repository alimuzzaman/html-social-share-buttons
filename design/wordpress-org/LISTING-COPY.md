# Recommended WordPress.org listing copy

The 3.0.0 listing is live and substantially current. This is a focused maintenance pass, not a rewrite. Apply it separately from the artwork and only through the normal authorized release/SVN workflow.

## 1. Short description in `readme.txt`

Replace the current line, which ends with the absolute claim “no tracking,” with this 117-character line:

> HTML + CSS share buttons and profile links with blocks, builders, local icons, and no frontend JavaScript by default.

This stays below the WordPress.org 150-character limit, restores the plugin's original positioning, and remains accurate because frontend JavaScript is optional rather than absent in every configuration.

## 2. Main plugin header

Use the same sentence for the installed Plugins screen:

> HTML + CSS share buttons and profile links with blocks, builders, local icons, and no frontend JavaScript by default.

Also change the Plugin URI to:

> https://wordpress.org/plugins/html-social-share-buttons/

## 3. Opening description

Replace the first three introductory paragraphs with:

> HTML Social Share Buttons adds server-rendered HTML and CSS share buttons and profile links to posts, pages, sidebars, and builder layouts. By default, the plugin adds no frontend JavaScript. Bundled icons load from your own site, and optional Google Social Analytics stays off unless you enable it.
>
> Use the Social Share or Social Links block, automatic placement, the classic widget, Elementor, WPBakery, a shortcode, or PHP. Choose from six bundled icon sets and square or circle buttons.

Follow with these four scannable groups before the shortcode examples:

- **HTML + CSS by default:** Server-rendered share and profile links with no frontend plugin JavaScript unless optional analytics is enabled.
- **Private by default:** Bundled icons load from your site, with no remote icon CDN, and optional analytics is disabled until enabled.
- **Place anywhere:** Automatic before/after and floating placement, two blocks, a classic widget, Elementor, WPBakery, shortcodes, and PHP.
- **Share and follow separately:** Share actions open a composer for the current page; profile links open the configured profile or email destination.
- **Current destinations:** Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email.

Keep the current detailed FAQ and shortcode examples after this summary.

## 4. Screenshots section

Add this section to `readme.txt` when the matching, current screenshots are ready in WordPress.org SVN `/assets`:

```text
== Screenshots ==

1. Inline share buttons and a responsive floating rail on a post.
2. Placement, icon style, button shape, audience, and analytics controls in Settings.
3. Social Share and Social Links blocks in the block editor.
4. Separate profile-link destinations and viewer-audience controls.
5. Elementor controls and matching public output.
```

Do not add item 5 until the current Elementor UI and output have been verified together. Use neutral content and no personal data.

## 5. Readme size and changelog

The current `readme.txt` is 10,440 bytes. WordPress.org warns that files larger than 10 KB may error. Keep the 3.0.0 changelog entry in `readme.txt`, move older entries to `changelog.txt`, and validate the final readme with the official validator.

The stable tag is `3.0.0`, so changing only SVN trunk will not update the public description. Update the authoritative tagged readme as part of the authorized metadata workflow.

## 6. Repository `README.md`

The developer README still describes 3.0.0 as an unpublished candidate after 2.2.6, but this checkout is `master` at the published `v3.0.0` commit. Replace that release-status paragraph with:

> Version 3.0.0 is the current published release. It contains the canonical implementation rewrite, separate profile links, local Bootstrap and Tabler icon sets, audience controls, builder integrations, and WordPress 7.1 compatibility work documented below.
>
> Repository evidence does not authorize a new tag, WordPress.org upload, or production deployment. Future releases still require an immutable reviewed revision and explicit approval.

Also revise the final evidence-boundary bullet that says an immutable 3.0.0 candidate and final approval are still required. Keep the durable rule about future releases, but remove the now-stale candidate state and the historical pre-release soak narrative from the README's opening.
