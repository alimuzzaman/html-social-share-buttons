# Icon-set coverage matrix

This is an inventory of the canonical manifests in `resources/iconsets/` and
their declared physical asset trees. Historical Default, Flat, Long Shadow,
and Prajin files are retained below `iconset/`; Bootstrap Solid and Tabler
Outline have dedicated asset directories below `assets/iconsets/`. The
manifests are metadata only, not duplicate asset packs. “Yes” means the
manifest declares that network and the repository contains the corresponding
asset for every declared shape. It does **not** mean that the cell has passed
visual, browser, trademark, or license review.

| Icon set | Shapes | Facebook | X | LinkedIn | Pinterest | Telegram | Bluesky | Email |
|---|---|---:|---:|---:|---:|---:|---:|---:|
| Default | square | Yes | Yes | Yes | Yes | Yes | Yes | Yes |
| Flat | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | Yes |
| Long Shadows | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | Yes |
| Prajin | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | No |
| Bootstrap Solid | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | Yes |
| Tabler Outline | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | Yes |

The Default set does not declare a circle shape. Prajin does not declare an
Email icon. For a built-in set, an unsupported requested shape falls back to
that set’s first declared shape; a selected network without an icon is omitted.
That is runtime behavior, not a promise of visual equivalence.

## Provenance and release status

| Asset group | Repository evidence | Publication status |
|---|---|---|
| Default PNG pack, utility assets, previews, stylesheets | Retained in `iconset/default` from 2.2.6; no verifiable upstream source, version, or license grant recorded | **Unresolved — release blocker** |
| Flat PNG brand assets | Retained in `iconset/flat`; only probable visual relationship to a historical source, and no adequate redistribution grant is recorded | **Unresolved — release blocker** |
| Long Shadows PNG brand assets | Retained in `iconset/long_shadow`; only probable historical attribution, and redistribution/modification rights are not verified | **Unresolved — release blocker** |
| Prajin PNG brand assets | Retained in `iconset/prajin`; only probable historical attribution, and no adequate redistribution/modification rights are verified | **Unresolved — release blocker** |
| Bundled Telegram and Bluesky SVG glyphs in historical sets | Source notes are recorded, but brand-guideline review remains required | Not cleared for publication by this record |
| Bootstrap Solid SVG set | Pinned Bootstrap Icons v1.13.1 inputs, checksums, deterministic generator, MIT license text, and manifest coverage are present | Source/license record present; browser and trademark review pending |
| Tabler Outline SVG set | Pinned Tabler Icons v3.46.0 inputs, checksums, deterministic generator, MIT license text, and manifest coverage are present | Source/license record present; browser and trademark review pending |

`resources/iconsets/ASSET-SOURCES.md` is the authoritative provenance note.
Its “probable” rows are explicitly not verification. Do not convert them into
cleared attribution, do not claim that the historical PNG packs may be freely
redistributed, and do not replace them with inferred/new brand artwork without
an approved compatibility decision and source record.

## Verification still required

- Run the manifest/asset and deterministic-generator checks for the candidate.
- Capture desktop and mobile rendering in current Chrome, Firefox, Safari, and
  Edge for every supported matrix cell.
- Complete trademark and platform-brand-guideline review for every network.
- Obtain verifiable source, modification, redistribution, and attribution
  evidence for each retained historical PNG—or approve a deliberate replacement
  and its visual migration.
