# HTML Social Share Buttons

HTML Social Share Buttons is a privacy-friendly WordPress plugin for share
actions and optional social profile/contact links. Its frontend is
server-rendered HTML and CSS, icon assets are served locally, and tracking is
off by default.

The current `master` branch contains the completed canonical implementation
rewrite and the 3.1.0 frontend button-appearance update after the published
3.0.0 release. Version 3.1.0 adds a global appearance selector with Legacy,
Minimal, Framed, and Soft shadow modes while keeping Legacy as the compatibility
default. Repository evidence does not authorize a new tag, WordPress.org
upload, or production deployment. Future releases still require an immutable
reviewed revision and explicit approval.

## Features

- Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email share actions.
- Separate global profile/contact links with placement-level inherit or
  suppress controls.
- Audience controls independently show or hide every share-button surface for
  the content author, other logged-in users, and logged-out visitors. Existing
  installations default all three audiences to visible.
- Automatic placement, dynamic Social Share and Social Links blocks, classic
  widget, Elementor, WPBakery, shortcodes, and a direct PHP facade.
- A global Button appearance selector with Legacy (current), Minimal, Framed,
  and Soft shadow modes. Modern modes use one shared CSS layer across every
  rendering integration; Legacy keeps the established pack-specific output.
- Six local icon sets. Bootstrap Solid is the new-install default. The
  historical Default pack remains available only to existing selections;
  Flat, Long Shadow, Prajin, Bootstrap Solid, and Tabler Outline support
  square and circle buttons.
- Responsive floating rails that enter document flow as centered, wrapping
  rows at viewport widths of 600px and below.
- Existing `zm_shbt_fld` settings, `_zm_sh_disable_share` metadata, legacy
  symbols/hooks, builder identifiers, wrapper/CSS contracts, and link behavior
  preserved through a thin compatibility boundary. Share anchors add translated
  `aria-label` values without changing their visual classes or destinations.

## Requirements

- WordPress 5.3 or newer.
- PHP 7.0 or newer.
- A packaged release for normal installation. A source checkout requires the
  Composer production autoloader and compiled JavaScript assets.

## Installation and use

Install a release ZIP through **Plugins > Add New > Upload Plugin**, activate
it, and open **Settings > HTML Social Share**.

Historical and descriptive shortcode tags are both supported:

```text
[zm_sh_btn iconset="long-shadows" iconset_type="square" icons="facebook,x,linkedin,pinterest,mail"]
[html-social-share-buttons iconset="tabler-outline" iconset_type="circle" profile_links_mode="inherit"]
```

Share actions use the current page by default. Profile links are separate
destinations and are not treated as share events. By default, the public output
uses server-rendered HTML and CSS without frontend plugin JavaScript; optional
Google Social Analytics remains disabled until enabled in settings.

Fresh installations do not offer the historical Default pack in normal icon
style selectors. Existing settings, blocks, widgets, shortcodes, and builder
content that explicitly use `default` continue to render the retained assets.

The maintained block metadata uses Block API v3 on WordPress 6.3 and newer.
The server and editor select the historical API v1 registration path on older
supported WordPress versions, preserving the declared WordPress 5.3 floor.

## Architecture

`src/Bootstrap/PluginFactory.php` composes the canonical services, registries,
renderer, settings repository, presentation controllers, and integrations.
`src/Compatibility/Legacy/Api/` is the only legacy boundary; it exposes the
retained public API as delegates to canonical services and does not own a
parallel runtime.

Frontend rendering is owned by `Presentation/Rendering/RenderFacade` and the
canonical renderer. Settings remain in the historical `zm_shbt_fld` option and
are normalized in memory without a replacement schema or data migration. This
is what makes downgrade to published 2.2.6 possible without reverse migration.

See [rewrite compatibility decisions](docs/REWRITE-COMPATIBILITY-DECISIONS.md),
the [legacy compatibility inventory](docs/LEGACY-COMPATIBILITY-INVENTORY.md),
and the [release-candidate evidence ledger](docs/RELEASE-CANDIDATE-VALIDATION.md).

## Development

Install dependencies with Composer and pnpm, then use the focused checks:

```sh
composer validate --strict
composer test
composer quality
pnpm run lint:js
pnpm run icons:check
pnpm run i18n:build
pnpm run settings:check
pnpm run integration:check
pnpm run test:unit
pnpm run test:ajax
pnpm run test:multisite
pnpm run test:e2e
pnpm run plugin:check
```

Build the deterministic distribution archive only with a production Composer
autoloader:

```sh
composer install --no-dev --prefer-dist --no-interaction --classmap-authoritative
pnpm run zip
```

The archive builder refuses to overwrite an existing ZIP. Set
`HSSB_ARCHIVE_PATH` to a new path for reproducibility comparisons or another
candidate build.

The test commands use the project's Sandbox WordPress runtime. See
[tests/README.md](tests/README.md) for the contract and fixture inventory.

## Evidence boundaries

- The current isolated browser matrix covers Chrome, Firefox, Edge, and
  Playwright WebKit at desktop and 390px mobile viewports. Separate Safari 26.6
  desktop and Responsive Design Mode captures exist. They are not
  physical-iPhone or universal-theme certification.
- Elementor editor/public fixtures pass. When the paid WPBakery editor is not
  available, evidence is limited to official `vc_map()`/shortcode documentation
  plus mapping, persistence, compiled-bundle, and public-render contracts; no
  live paid-editor run is claimed.
- Both dynamic blocks use Block API v3 in modern WordPress and were exercised
  in WordPress 7.1 final's iframe editor. WordPress 5.3-6.2 use the tested API v1
  compatibility registration instead of loading unsupported v3 semantics.
- The Default PNG pack is retained under an accepted compatibility exception.
  That is not an independent provenance or clearance claim.
- The 3.0.0 release's exact-archive rollback rehearsal and WordPress 7.1 final
  manual review remain historical evidence. The 3.1.0 appearance update still
  requires a fresh immutable archive review before publication.

## Security and licensing

Report security issues privately to the maintainer rather than opening a public
proof-of-concept issue. The plugin is GPL-2.0-or-later. Generated icon sources
and third-party notices are recorded in
[resources/iconsets/ASSET-SOURCES.md](resources/iconsets/ASSET-SOURCES.md) and
`THIRD-PARTY-NOTICES.txt`.

No tag, WordPress.org upload, production deployment, or article publication is
authorized by the repository's candidate evidence alone.
