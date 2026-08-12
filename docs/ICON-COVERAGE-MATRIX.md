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
| Flat PNG brand assets | Retained in `iconset/flat`; Hakan Ertan / Tonicons historical credit and a 2026-08-12 maintainer attestation of rights-holder authorization are recorded; no written authorization is archived | **Authorization attested; archival evidence incomplete** |
| Long Shadows PNG brand assets | Retained in `iconset/long_shadow`; Hakan Ertan / Tonicons historical credit and a 2026-08-12 maintainer attestation of rights-holder authorization are recorded; no written authorization is archived | **Authorization attested; archival evidence incomplete** |
| Prajin PNG brand assets | Retained in `iconset/prajin`; Prajin historical credit and a 2026-08-12 maintainer attestation of rights-holder authorization are recorded; no written authorization is archived | **Authorization attested; archival evidence incomplete** |
| Bundled Telegram and Bluesky SVG glyphs in historical sets | Source notes are recorded, but brand-guideline review remains required | Not cleared for publication by this record |
| Bootstrap Solid SVG set | Pinned Bootstrap Icons v1.13.1 inputs, checksums, deterministic generator, MIT license text, and manifest coverage are present | Source/license record present; browser and trademark review pending |
| Tabler Outline SVG set | Pinned Tabler Icons v3.46.0 inputs, checksums, deterministic generator, MIT license text, and manifest coverage are present | Source/license record present; browser and trademark review pending |

`resources/iconsets/ASSET-SOURCES.md` is the authoritative provenance note.
The maintainer attestation records a representation of authorization, but does
not replace archived written permission or independently establish its scope.
Do not convert it into a claim that the historical PNG packs are independently
cleared or may be freely redistributed, and do not replace them with
inferred/new brand artwork without an approved compatibility decision and source
record.

## Browser validation evidence

On 2026-08-12, all 11 declared set/shape cells passed automated full-page
rendering checks in current Google Chrome, Mozilla Firefox, Microsoft Edge,
and Playwright WebKit at 1440×1024 and 390×844 viewport sizes. Durable PNGs, checksums, exact
versions, and limitations are recorded in `BROWSER-VALIDATION.md`. The test
found and corrected the lowercase canonical Prajin CSS selector gap while
retaining its historical uppercase selectors.

Playwright WebKit passed after its documented host dependencies were installed.
The same eight-project matrix then passed in a newly provisioned strict Sandbox
worker, with separate durable screenshots and checksums. WebKit is not Safari,
and no Safari claim is made.

## Verification still required

- Run the manifest/asset and deterministic-generator checks for the candidate.
- Capture current Safari desktop/iOS evidence separately.
- Resolve or explicitly accept the 390-pixel fixed-rail/heading overlap noted
  in `BROWSER-VALIDATION.md` during real-device/manual layout review.
- Complete trademark and platform-brand-guideline review for every network.
- Archive the written authorization or license terms that substantiate the
  maintainer attestation for Flat, Long Shadows, and Prajin; obtain source and
  license evidence for Default—or approve a deliberate replacement and its
  visual migration.
