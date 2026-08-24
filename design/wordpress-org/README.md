# WordPress.org banner and icon

This folder contains upload-ready WordPress.org directory artwork and its editable vector source. It is intentionally separate from the plugin's runtime `assets/` directory and from the WordPress.org SVN checkout.

## Art direction

The identity joins angle brackets (HTML) with a three-node share path (sharing and profile links). The banner uses the exact, source-backed message:

> HTML Social Share Buttons  
> HTML + CSS sharing. No frontend JS by default.

The support line restores the plugin's original HTML/CSS and no-frontend-JavaScript positioning while staying accurate today. Bundled local icons and analytics being disabled by default remain supporting listing points, where their qualifications fit.

The outer tiles are generic interface shapes, not social-network marks. The centered layout keeps the English banner balanced on left-to-right and right-to-left directory pages. No CTA, rating, install count, version, network count, third-party logo, WordPress mark, or unverifiable performance claim is present.

The XHigh refinement intentionally did not use image generation. The dark field, decorative tiles, mark, and text are original deterministic vectors; generated background art would add variation without improving the product story or provenance.

## Deliverables

- `banner-772x250.png` — required standard banner
- `banner-1544x500.png` — required 2x banner; same master and composition
- `icon-128x128.png` — required standard icon
- `icon-256x256.png` — required 2x icon
- `icon.svg` — clean static vector icon; keep the PNG fallbacks
- `source/banner-master.svg` — editable 1544 by 500 vector master with outlined type
- `source/type/*.outlined.svg` — HarfBuzz-shaped Inter outlines used by the master
- `source/build-artwork.mjs` — single source of truth for mark geometry, palette, and composition
- `source/generate-tagline-outline.sh` — verifies the pinned Inter 4.1 archive and regenerates the exact support-line outlines
- `source/export-assets.sh` — deterministic SVG-to-PNG export
- `source/tag-srgb.mjs` — adds the standard PNG sRGB rendering-intent chunk without changing pixels
- `source/validate-assets.mjs` — validates dimensions, modes, size limits, SVG safety, exact accessible copy, and contrast

## Provenance

- Mark, background, decorative geometry, and composition: original project artwork created for HTML Social Share Buttons on 2026-08-24. No third-party logo geometry or generated imagery is included.
- Typeface: Inter 4.1 Bold and Medium by Rasmus Andersson and contributors, SIL Open Font License 1.1. Source: <https://github.com/rsms/inter/releases/tag/v4.1>. The downloaded release archive used to create the committed outlines had SHA-256 `9883fdd4a49d4fb66bd8177ba6625ef9a64aa45899767dde3d36aa425756b11e`.
- The Inter license is preserved at `source/LICENSE-Inter.txt`. The production PNG files do not depend on an installed font; the banner master contains vector outlines.
- Product claims and WordPress.org requirements: `docs/WORDPRESS-ORG-BRAND-ASSET-RESEARCH.md`.

No image-generation prompt exists because image generation was not used. If an alternate abstract background is explored later, the prompt must require no text, letters, words, logos, platform symbols, or watermark; the current mark and outlined text must remain deterministic overlays.

## Export

Requirements: Node.js, HarfBuzz only when changing copy/type, and `rsvg-convert` from librsvg for normal exports.

To rebuild the vector composition and all production PNGs:

```bash
./design/wordpress-org/source/export-assets.sh
node ./design/wordpress-org/source/validate-assets.mjs
```

Both banners are exported from `source/banner-master.svg`; do not compose the 1x and 2x files separately. To regenerate the current support-line outlines from the pinned Inter release, run `./design/wordpress-org/source/generate-tagline-outline.sh /path/to/Inter-4.1.zip`, then run the export script. The outline generator verifies the archive SHA-256 before shaping the exact copy with Inter Medium at the committed size and a transparent background.

Before SVN upload, verify the exact filenames, dimensions, RGB/RGBA color mode, and size limits. Set `svn:mime-type` to `image/png` for PNG files if needed. Upload and cache verification are separate release actions and require explicit authorization.
