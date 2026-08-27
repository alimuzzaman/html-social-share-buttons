# Open-license icon-set candidates for HSSB

Research date: 2026-08-24  
Repository: HTML Social Share Buttons (`GPL-2.0-or-later`)  
Scope: upstream icon assets, licenses, brand-logo coverage, visual styles, and
whether a candidate is suitable for a bundled WordPress pack or only for
generic presentation controls.

This is product and engineering research, not legal advice. A copyright
license for an SVG does not grant permission to use the represented company
name, logo, trade dress, or trademark. Before release, preserve the upstream
license/notice, pin the exact revision, inspect each selected asset's metadata,
and review the relevant platform brand guidelines.

## Executive recommendation

1. Keep **Bootstrap Icons** and **Tabler Icons** as the first bundled modern
   candidates. They already cover HSSB's complete network set (Facebook, X,
   LinkedIn, Pinterest, Telegram, Bluesky, and email), provide raw SVGs, and
   use MIT licensing. The repository already has reproducibly generated
   `bootstrap-solid` and `tabler-outline` packs with source records in
   [`resources/iconsets/ASSET-SOURCES.md`](../../resources/iconsets/ASSET-SOURCES.md).
2. Treat **Simple Icons** as a brand-source/generator candidate, not a blanket
   license for every icon. The repository is CC0, but its own disclaimer says
   individual icon licenses can differ, can become outdated, and must be
   checked regularly. It is excellent for broad brand coverage and SVG
   tree-shaking, but every shipped brand asset needs a pinned per-icon record.
3. Treat **Font Awesome Free** as viable only with an explicit third-party
   notice. Its SVG/JS icons are CC BY 4.0, fonts are SIL OFL 1.1, and code is
   MIT. It covers the complete HSSB set and says it is GPL-friendly, but CC BY
   attribution and brand-trademark caveats are release obligations.
4. Use **Lucide**, **Phosphor**, **Heroicons**, **Material Symbols**, **Feather**,
   or **Radix Icons** for generic controls (share, link, mail, close, etc.),
   not as the sole social-brand pack. Lucide explicitly does not accept brand
   logos; Google Material explicitly excludes third-party logos.
5. Do **not** make current **Remix Icon** the default source for a GPL plugin.
   Its January 2026 Remix Icon License v1.0 prohibits competing icon libraries
   and says it may be incompatible with strong copyleft/share-alike licenses.

## HSSB coverage needed

The network row below is the minimum target. “Generic” means UI artwork such as
share, link, envelope, or profile; it is not a social-company logo.

| Candidate | Facebook | X | LinkedIn | Pinterest | Telegram | Bluesky | Email | Generic UI | SVG/source form | Visual families | Bundling verdict |
|---|---:|---:|---:|---:|---:|---:|---:|---:|---|---|---|
| Bootstrap Icons | Yes | Yes (`twitter-x`) | Yes | Yes | Yes | Yes | Yes (`envelope`) | Yes (`share`) | Raw SVG; optimized build | Filled glyphs; `fill` paths | **Strong first pack** |
| Tabler Icons | Yes | Yes (`brand-x`) | Yes | Yes | Yes | Yes | Yes (`mail`) | Yes (`share-2`) | Raw SVG; packages and source tree | Outline plus filled families; 2 px stroke | **Strong first pack** |
| Simple Icons | Yes | Yes | Yes | Yes | Yes | Yes | No brand email; generic UI absent | No | Raw SVG; per-icon JSON metadata | Monochrome brand paths; official brand color metadata | **Brand source with per-icon clearance** |
| Font Awesome Free | Yes | Yes | Yes | Yes | Yes | Yes | Yes (`envelope`) | Yes (`share`) | Raw SVG and font files | Brands, solid, regular; multiple logo variants | **Possible, notice required** |
| Lucide | No (policy) | No | No | No | No | No | Generic envelope only | Yes | Raw SVG and tree-shakable icon data | Consistent 24 px outline, configurable stroke | **Generic controls only** |
| Remix Icon | Yes | Yes (`twitter-x`) | Yes | Yes | Yes | Yes | Yes (`mail`) | Yes (`share-2`) | Raw SVG; line/fill pairs | Line and fill; broad logo catalog | **Reject for GPL bundle unless counsel clears** |
| Phosphor | Yes | Twitter (not a current X glyph) | Yes | Yes | Yes | No | Generic envelope | Yes (`share`, `share-network`) | Raw SVG assets and catalog | Thin, light, regular, bold, fill, duotone | **Generic plus partial brand use** |
| Heroicons | No (UI set) | No | No | No | No | No | Generic envelope | Yes (`share`) | Raw SVG; optimized source | 16/20/24 px outline and solid | **Generic controls only** |
| Material Symbols | No (Google excludes third-party logos) | No | No | No | No | No | Generic mail | Yes (`share`) | Static SVG plus variable-font source | Outline, rounded, sharp; weight/grade/fill/optical-size axes | **Generic controls only** |
| Feather | No | No | No | No | No | No | Generic mail | Yes (`share`) | Raw SVG; JS package | 24 px outline, 2 px stroke | **Generic controls only; maintenance is slow** |
| Radix Icons | No | No | No | No | No | No | Generic envelope | Yes (`share-2`) | Raw SVG and React package | Small, consistent 15 px UI glyphs | **Generic controls only** |

Coverage was checked against the upstream repositories' icon trees and readmes
on the research date. Do not infer that a similarly named glyph is an approved
brand mark; the platform's current guidelines remain authoritative.

## Candidate review

### Bootstrap Icons — MIT — recommended bundled pack

* **Primary evidence:** [official repository](https://github.com/twbs/icons),
  [README](https://raw.githubusercontent.com/twbs/icons/main/README.md), and
  [LICENSE](https://raw.githubusercontent.com/twbs/icons/main/LICENSE).
* The upstream readme describes an official open-source SVG library with over
  2,000 icons. It publishes processed SVGs, supports copy-paste/`img`/sprite
  use, and runs SVGO in its icon generation pipeline.
* The license is MIT. Redistributed copies or substantial portions must retain
  the copyright and permission notice.
* It has the exact HSSB network row, including the current `twitter-x` glyph,
  Bluesky, Telegram, and generic share/envelope controls. The single filled
  `fill`-path visual language is a good fit for HSSB's existing
  `bootstrap-solid` pack.
* **Maintenance:** active first-party repository with automated tests and
  tagged releases; pin a release rather than tracking `main`.
* **Risks:** brand artwork is still subject to the platform owner's trademark
  and usage rules. Keep the upstream source URL and version in the generator
  record; do not rename a glyph to imply platform endorsement.

### Tabler Icons — MIT — recommended bundled pack

* **Primary evidence:** [official repository](https://github.com/tabler/tabler-icons),
  [README](https://github.com/tabler/tabler-icons/blob/main/README.md), and
  [LICENSE](https://raw.githubusercontent.com/tabler/tabler-icons/main/LICENSE).
* Tabler documents more than 6,000 SVG icons on a 24×24 grid with a 2 px
  stroke. It exposes both outline and filled sets, raw SVG use via `img`, CSS,
  or inline HTML, and framework packages. The source tree contains
  `brand-facebook`, `brand-x`, `brand-linkedin`, `brand-pinterest`,
  `brand-telegram`, `brand-bluesky`, and `mail`.
* The license is MIT; retain the copyright and permission notice in
  redistributed copies or substantial portions.
* **Maintenance:** very active first-party repository and release tooling;
  pin the exact tag because brand glyph names and artwork can evolve.
* **Risks:** the open-source license is not a trademark license. Keep brand
  icons used only to link to their corresponding services and follow each
  service's current identity rules.

### Simple Icons — CC0 repository, per-icon licensing — brand-source candidate

* **Primary evidence:** [repository README](https://raw.githubusercontent.com/simple-icons/simple-icons/develop/README.md),
  [CC0 repository license](https://raw.githubusercontent.com/simple-icons/simple-icons/develop/LICENSE.md),
  [legal disclaimer](https://raw.githubusercontent.com/simple-icons/simple-icons/develop/DISCLAIMER.md),
  and [icon metadata](https://raw.githubusercontent.com/simple-icons/simple-icons/develop/data/simple-icons.json).
* The project publishes more than 3,400 brand SVGs and exposes `source`,
  optional `guidelines`, `license`, `hex`, and `path` metadata. Its readme
  recommends importing only selected icons and using tree-shaking. This is a
  strong mechanical fit for generating a tiny HSSB brand subset.
* The repository itself is CC0, but the disclaimer explicitly says that does
  **not** mean every contained icon is CC0. Individual license data may be
  absent or stale, and the project asks users to re-check changes. Brands may
  request updates or removal; normal updates are generally weekly and
  removals that no longer meet criteria generally land in major releases twice
  a year (with emergency exceptions).
* **Attribution/notice:** CC0 generally does not require attribution, but a
  selected icon can have another license. Preserve the per-icon metadata and
  source URL in `THIRD-PARTY-NOTICES.txt` or an equivalent asset manifest.
* **Risks:** the disclaimer says trademarks and brand guidelines remain the
  user's responsibility and that Simple Icons cannot be held responsible for
  legal activity. A brand removal, slug rename, or license-data correction can
  break a build or change redistribution obligations. Pin a commit, fail the
  generator when an icon disappears, and review the pinned metadata before
  each release.
* **Verdict:** ideal as an optional brand-asset source; not a blanket drop-in
  pack unless each HSSB asset is individually cleared and archived.

### Font Awesome Free — mixed CC BY/OFL/MIT — broad but notice-heavy

* **Primary evidence:** [official repository](https://github.com/FortAwesome/Font-Awesome),
  [Free license file](https://raw.githubusercontent.com/FortAwesome/Font-Awesome/7.x/LICENSE.txt),
  and [official license page](https://fontawesome.com/license/free).
* The current free tree contains all HSSB brands (including Bluesky and X),
  envelope/share controls, SVGs, and font assets. It offers distinct brand,
  solid, and regular families and many square/wordmark variants.
* The license file splits the distribution: SVG/JS icons are CC BY 4.0,
  web/desktop fonts are SIL OFL 1.1, and non-font/non-icon code is MIT. The
  file says Font Awesome Free is GPL-friendly, but it also says attribution is
  required by those licenses and that downloaded files already contain
  attribution comments.
* Brand icons are trademarks of their respective owners. The upstream rule is
  to use a brand logo only to represent the corresponding company, product, or
  service, without implying endorsement.
* **Verdict:** a technically strong optional pack, especially if HSSB wants
  multiple logo variants. Bundle only a small pinned SVG subset, retain the
  license/attribution comments, and add a visible or distributed notice. Do
  not silently treat the SVGs as MIT or GPL artwork.

### Lucide — ISC plus MIT-derived Feather icons — generic controls only

* **Primary evidence:** [repository README](https://github.com/lucide-icons/lucide/blob/main/README.md),
  [LICENSE](https://raw.githubusercontent.com/lucide-icons/lucide/main/LICENSE),
  [package metadata](https://github.com/lucide-icons/lucide/blob/main/packages/lucide/package.json),
  and the [tree-shakable icon-data guide](https://lucide.dev/guide/packages/icons).
* Lucide provides lightweight SVGs and a tree-shakable icon-data package; its
  builders output `currentColor` SVGs with configurable size and stroke width.
  This is excellent for a modern generic share/link/mail presentation layer.
* The main license is ISC and requires the copyright and permission notice.
  The license separately identifies icons derived from Feather under MIT, so
  retain both notices when redistributing source assets.
* Lucide's official README states that it does not accept brand logos and does
  not plan to add them because of legal, consistency, and maintenance concerns.
* **Verdict:** use for generic share/profile/mail controls only. It cannot
  replace HSSB's brand-logo set.

### Remix Icon — Remix Icon License v1.0 (January 2026) — limited/rejected

* **Primary evidence:** [official README](https://github.com/Remix-Design/RemixIcon/blob/master/README.md),
  [current license](https://raw.githubusercontent.com/Remix-Design/RemixIcon/master/License),
  and the [official license-change notice](https://github.com/Remix-Design/RemixIcon/issues/1069).
* The icon tree has line/fill pairs for all HSSB brands, including Bluesky and
  X, plus mail/share controls. It is visually attractive for a modern variant
  and is available as raw SVG.
* The current license grants use, modification, and distribution as part of a
  larger work, but prohibits standalone icon sales, competing icon libraries,
  and using icons as logos/trademarks. It says the license may be incompatible
  with strong copyleft or share-alike terms. Attribution is optional for
  individual use, while substantial-library redistribution calls for the
  license and copyright notices.
* **Verdict:** do not bundle into HSSB's GPL plugin without written legal
  clearance. The old Apache-2.0 history does not make the current checkout
  safe; pinning an old revision would require a separate provenance and
  version-specific review.

### Phosphor — MIT — useful generic family, incomplete brand coverage

* **Primary evidence:** [core repository](https://github.com/phosphor-icons/core),
  [raw SVG/catalog README](https://github.com/phosphor-icons/core/blob/main/README.md),
  and [LICENSE](https://raw.githubusercontent.com/phosphor-icons/core/main/LICENSE).
* Core ships raw SVG assets and catalog data in six weights: thin, light,
  regular, bold, fill, and duotone. It includes generic share/envelope glyphs
  and Facebook, LinkedIn, Pinterest, Telegram, and legacy Twitter logos, but
  no Bluesky glyph in the current tree.
* The license is MIT; retain the copyright and permission notice.
* **Verdict:** a strong candidate for a modern generic control family or a
  partial brand pack where a missing Bluesky/X asset can be supplied by a
  separately cleared source. Do not mix its legacy Twitter mark into an HSSB
  button labeled X without a deliberate brand decision.

### Heroicons — MIT — UI controls only

* **Primary evidence:** [official repository](https://github.com/tailwindlabs/heroicons),
  [README](https://github.com/tailwindlabs/heroicons/blob/master/README.md),
  and [LICENSE](https://github.com/tailwindlabs/heroicons/blob/master/LICENSE).
* Heroicons provides optimized SVGs in outline and solid styles at 16, 20,
  and 24 px. The readme supports copy-paste SVG and per-icon React/Vue
  imports; the assets are easy to subset and recolor with `currentColor`.
* The license is MIT with the usual notice-retention condition. The official
  set is UI-focused and has no HSSB brand-logo row.
* **Verdict:** excellent for generic share/link/mail affordances, not social
  brand artwork.

### Material Symbols / Material Icons — Apache-2.0 — UI controls only

* **Primary evidence:** [Google's official repository README](https://github.com/google/material-design-icons/blob/master/README.md)
  and [Apache-2.0 license](https://raw.githubusercontent.com/google/material-design-icons/master/LICENSE).
* Material Symbols is the current set; it supports static SVGs and variable
  font axes for optical size, weight, grade, and fill, with outlined, rounded,
  and sharp families. The classic Material Icons set is no longer updated.
* Google explicitly says it does not include third-party logos for legal
  reasons and has removed some that existed in the past. The icons are Apache
  2.0; the README requests attribution in an About screen but says it is not
  required. Apache redistribution still requires the license, notices, and
  modified-file notices where applicable.
* **Verdict:** useful for generic share/mail/profile controls and modern weight
  variants. It cannot cover HSSB's social brands.

### Feather — MIT — generic controls, slow upstream cadence

* **Primary evidence:** [official repository](https://github.com/feathericons/feather),
  [README](https://github.com/feathericons/feather/blob/main/README.md), and
  [LICENSE](https://github.com/feathericons/feather/blob/main/LICENSE).
* Feather is a simple 24×24 outline SVG set with a configurable 2 px stroke,
  and can be used as raw SVG, `img`, or inline markup. It has generic share,
  mail, and link controls but no maintained social-brand catalog.
* The license is MIT. The upstream releases page reports v4.29.2 (May 2024),
  so maintenance is slower than Tabler, Bootstrap, or Simple Icons.
* **Verdict:** acceptable for a stable generic fallback, but Lucide is the
  more actively developed Feather-derived option.

### Radix Icons — MIT — small generic UI set

* **Primary evidence:** [official repository](https://github.com/radix-ui/icons),
  [README](https://github.com/radix-ui/icons/blob/master/README.md), and
  [LICENSE](https://github.com/radix-ui/icons/blob/master/LICENSE).
* Radix provides a compact, carefully normalized SVG/React UI vocabulary. It
  has generic controls but no social-brand coverage, and its 15×15 design grid
  is less directly suited to HSSB's 32–44 px artwork than Tabler or Lucide.
* The license is MIT with notice retention.
* **Verdict:** generic controls only; lower priority than Lucide or Phosphor.

## License and GPL redistribution notes

The plugin can keep its own PHP/CSS/HTML under GPL-2.0-or-later while
redistributing third-party artwork under its own upstream terms. The practical
release pattern is a separate asset directory, a pinned source URL/revision,
an unmodified upstream license/notice, and a `THIRD-PARTY-NOTICES.txt` entry.
Do not relicense third-party icons as GPL merely because they are shipped in a
GPL plugin.

| License | HSSB handling |
|---|---|
| MIT | Generally straightforward for bundled SVG subsets; retain copyright and permission text. Bootstrap, Tabler, Phosphor, Heroicons, Feather, and Radix use MIT. |
| ISC | Permissive and suitable for separate assets; retain the ISC notice. Lucide also has a separately identified MIT-derived Feather subset. |
| Apache-2.0 | Generally workable for separate assets; preserve the license, notices, and modified-file notices, and do not use the upstream name as an endorsement. Material Symbols is Apache-2.0. |
| CC0 | No copyright attribution is normally required, but Simple Icons says individual brand files can have different licenses. Treat the repository CC0 as insufficient by itself. |
| CC BY 4.0 | Can be distributed with attribution, but do not omit the attribution or assume it is GPL artwork. Font Awesome SVG/JS icons use CC BY 4.0. Confirm the exact WordPress.org packaging and notice presentation before release. |
| SIL OFL 1.1 | Applies to Font Awesome font files, not its SVG icon files. Do not substitute OFL rules for the SVG/CC BY terms. |
| Custom/restrictive | Remix Icon License v1.0 includes no-competing-library and strong-copyleft compatibility language; keep it out pending legal clearance. |

This table is an engineering compatibility screen, not a legal conclusion.
Have counsel review any mixed-license pack, especially CC BY assets, brand
logos, or a distribution that exposes a large reusable icon collection.

## Brand, trademark, and update risks

- A brand icon's copyright license and the brand owner's trademark permission
  are different questions. Use each logo only to identify/link to that service,
  avoid claims of endorsement, and retain a link to current brand guidance.
- **Simple Icons:** metadata and licenses can change; brands can request
  removal; the project says removals normally occur in major releases twice a
  year and normal updates are generally weekly. Pin commits and record the
  exact metadata for every shipped slug.
- **Bootstrap/Tabler/Font Awesome/Remix:** brand renames and alternate glyphs
  can create aliases (`twitter` versus `twitter-x`, `brand-twitter` versus
  `brand-x`). Map HSSB's stable `x` ID to a pinned source path and keep a
  compatibility alias in the generator rather than relying on upstream names.
- **Phosphor:** its current Twitter logo is not proof of a current X mark. Do
  not silently relabel it.
- **Google Material and Lucide:** both intentionally avoid third-party logos,
  reducing brand risk but making them unsuitable as the sole social pack.
- Never fetch a CDN at runtime for bundled frontend output. Download, pin,
  checksum, subset, and serve the local SVGs so privacy/no-tracking and
  reproducibility remain intact.

## Release due diligence checklist

Before adding any new pack:

1. Pin an upstream tag or commit and record a SHA-256 for every source SVG.
2. Copy the exact upstream license into the distributed notices directory.
3. For Simple Icons or other aggregators, record each icon's `license`,
   `source`, and `guidelines` fields; fail generation when metadata is absent
   or the slug disappears.
4. Confirm the seven HSSB network files and email/generic fallbacks exist in
   every shape/style variant used by the manifest.
5. Preserve source comments and required attribution; do not strip license
   headers during SVG optimization.
6. Review each platform's current brand guidelines and keep brand-logo usage
   limited to identifying/linking to that platform.
7. Run HSSB's asset, manifest, deterministic-generation, accessibility, and
   browser checks. Verify that the new appearance stylesheet changes layout,
   not the destination URL or accessible name.
8. Re-check upstream license and brand status before every release; do not
   assume a previously cleared brand asset remains unchanged.

## Primary source index

- [Bootstrap Icons repository](https://github.com/twbs/icons) · [MIT license](https://raw.githubusercontent.com/twbs/icons/main/LICENSE)
- [Tabler Icons repository](https://github.com/tabler/tabler-icons) · [MIT license](https://raw.githubusercontent.com/tabler/tabler-icons/main/LICENSE)
- [Simple Icons repository](https://github.com/simple-icons/simple-icons) · [CC0 license](https://raw.githubusercontent.com/simple-icons/simple-icons/develop/LICENSE.md) · [disclaimer](https://raw.githubusercontent.com/simple-icons/simple-icons/develop/DISCLAIMER.md)
- [Font Awesome Free repository](https://github.com/FortAwesome/Font-Awesome) · [mixed license](https://raw.githubusercontent.com/FortAwesome/Font-Awesome/7.x/LICENSE.txt)
- [Lucide repository](https://github.com/lucide-icons/lucide) · [ISC/MIT license](https://raw.githubusercontent.com/lucide-icons/lucide/main/LICENSE)
- [Remix Icon repository](https://github.com/Remix-Design/RemixIcon) · [Remix Icon License v1.0](https://raw.githubusercontent.com/Remix-Design/RemixIcon/master/License)
- [Phosphor core](https://github.com/phosphor-icons/core) · [MIT license](https://raw.githubusercontent.com/phosphor-icons/core/main/LICENSE)
- [Heroicons repository](https://github.com/tailwindlabs/heroicons) · [MIT license](https://github.com/tailwindlabs/heroicons/blob/master/LICENSE)
- [Google Material Design Icons repository](https://github.com/google/material-design-icons) · [Apache-2.0 license](https://raw.githubusercontent.com/google/material-design-icons/master/LICENSE)
- [Feather repository](https://github.com/feathericons/feather) · [MIT license](https://github.com/feathericons/feather/blob/main/LICENSE)
- [Radix Icons repository](https://github.com/radix-ui/icons) · [MIT license](https://github.com/radix-ui/icons/blob/master/LICENSE)
