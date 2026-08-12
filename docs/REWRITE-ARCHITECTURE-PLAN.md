# Canonical rewrite architecture record

## Status

The implementation phase is complete. This document records the delivered
architecture and compatibility constraints; it is no longer an active target
topology or an assertion that release operations have passed.

## Delivered architecture

- `Bootstrap\PluginFactory` composes one canonical service graph.
- Domain objects describe networks, icon sets, settings, placements, render
  requests/results, buttons, and profile links.
- Application services own URL resolution, share-button construction,
  exclusions, settings interfaces, and placement composition.
- Infrastructure owns WordPress option persistence, request sanitation,
  translation, manifests/assets, extension hooks, and no-op migration wiring.
- Presentation owns the HTML renderer, frontend/controller output, admin UI,
  shortcode, blocks, widgets, Elementor, WPBakery, metaboxes, and render facade.
- `Compatibility/Legacy/Api` is the single retained adapter boundary. Historical
  root forwarding implementations and parallel legacy runtime are gone.
- JavaScript builds four entry bundles from 20 source modules: settings admin,
  Social Share block, Social Links block, and WPBakery controls.

Historical icon filenames, directories, URLs, CSS classes, and `twitter`
identifiers deliberately remain externally visible. Canonical manifests and
resolvers own their metadata; retaining a compatibility path is not incomplete
runtime ownership.

## Preserved contracts

- WordPress 5.3+ and PHP 7.0+.
- `zm_shbt_fld` option and `_zm_sh_disable_share` metadata.
- Existing option/field shapes and unknown extension keys.
- Both shortcode tags, widget identity/data, stored block content,
  Elementor/WPBakery storage, legacy PHP API, hooks, handles, and markup.
- Exact historical frontend behavior except separately approved bug fixes.
- No replacement schema, custom table, destructive migration, telemetry, or
  remote runtime dependency.

## Approved corrections and additions

- Canonical one-pass permalink/template resolution.
- Safe mixed/malformed integration input normalization.
- Widget selection normalization without destructive storage rewrite.
- Correct AJAX parsing/sanitation and bounded exclusion search.
- Separate profile/contact links and Social Links block.
- Generated Bootstrap/Tabler icon sets.
- Mobile floating rails enter centered wrapping document flow at 600px and
  below.

## Executable enforcement

- Frontend golden and persisted-settings fixtures.
- Legacy API, hook, builder-storage, AJAX, and multisite contracts.
- Architecture-boundary and compatibility-thinness guards.
- Four-bundle/20-module JavaScript boundary and localization guards.
- Block, Elementor, WPBakery, icon/assets, archive, support-floor, browser, and
  rollback evidence.

## Remaining non-code gates

The real 14-day staging soak, its day-7 and final staging rollback, candidate
version/listing alignment, and explicit release approval remain outstanding.
These do not make the canonical implementation partial; they make the release
not yet complete.

See `REWRITE-COMPATIBILITY-DECISIONS.md` for exact retained surfaces and
`RELEASE-CANDIDATE-VALIDATION.md` for dated evidence and limitations.
