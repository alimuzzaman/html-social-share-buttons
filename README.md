# HTML Social Share Buttons

HTML Social Share Buttons is a privacy-friendly WordPress plugin for share
actions and optional social profile/contact links. Its frontend is
server-rendered HTML and CSS, icon assets are served locally, and tracking is
off by default.

The current `latest` branch contains the completed canonical implementation
rewrite after the published 2.2.6 release. The plugin header and WordPress.org
stable tag intentionally remain 2.2.6 until the release owner approves the
candidate, its real 14-day staging soak and rollback have passed, and final
version/listing alignment is authorized.

## Features

- Facebook, X, LinkedIn, Pinterest, Telegram, Bluesky, and email share actions.
- Separate global profile/contact links with placement-level inherit or
  suppress controls.
- Automatic placement, dynamic Social Share and Social Links blocks, classic
  widget, Elementor, WPBakery, shortcodes, and a direct PHP facade.
- Six local icon sets: Default (square), plus Flat, Long Shadow, Prajin,
  Bootstrap Solid, and Tabler Outline (square and circle).
- Responsive floating rails that enter document flow as centered, wrapping
  rows at viewport widths of 600px and below.
- Existing `zm_shbt_fld` settings, `_zm_sh_disable_share` metadata, legacy
  symbols/hooks, builder identifiers, and public markup preserved through a
  thin compatibility boundary.

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
destinations and are not treated as share events.

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
pnpm run lint:js
pnpm run icons:check
pnpm run settings:check
pnpm run integration:check
pnpm run test:unit
pnpm run test:ajax
pnpm run test:multisite
pnpm run test:e2e
```

Build the deterministic distribution archive only with a production Composer
autoloader:

```sh
composer install --no-dev --prefer-dist --no-interaction --classmap-authoritative
pnpm run zip
```

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
- Both dynamic blocks retain Block API v1 for the WordPress 5.3 floor. The two
  resulting Plugin Check findings are accepted and documented; the project
  does not claim a clean Plugin Check result.
- The Default PNG pack is retained under an accepted compatibility exception.
  That is not an independent provenance or clearance claim.
- A local exact-archive rollback rehearsal has passed. The real 14-day staging
  soak and staging rollback are a separate time-bound release gate.

## Security and licensing

Report security issues privately to the maintainer rather than opening a public
proof-of-concept issue. The plugin is GPL-2.0-or-later. Generated icon sources
and third-party notices are recorded in
[resources/iconsets/ASSET-SOURCES.md](resources/iconsets/ASSET-SOURCES.md) and
`THIRD-PARTY-NOTICES.txt`.

No tag, WordPress.org upload, production deployment, or article publication is
authorized by the repository's candidate evidence alone.
