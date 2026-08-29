# Icon-set coverage matrix

This is an inventory of the canonical manifests in `resources/iconsets/` and
their declared physical asset trees. Historical Default, Flat, Long Shadow,
and Prajin paths are retained below `iconset/`; their X artwork now comes from
the pinned Bootstrap X source while the public `twitter` filenames stay in
place. Bootstrap Solid and Tabler
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
| Prajin | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | Yes |
| Bootstrap Solid | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | Yes |
| Tabler Outline | square, circle | Yes | Yes | Yes | Yes | Yes | Yes | Yes |

The Default set does not declare a circle shape. Every declared shape now
ships all seven built-in network assets. For a built-in set, an unsupported requested shape falls back to
that set’s first declared shape; a selected network without an icon is omitted.
That is runtime behavior, not a promise of visual equivalence.

## Provenance and release status

| Asset group | Repository evidence | Publication status |
|---|---|---|
| Default PNG pack, utility assets, previews, stylesheets | Retained in `iconset/default` from 2.2.6; no verifiable upstream source, version, or license grant recorded; release owner accepted this compatibility exception on 2026-08-12 | **Accepted exception; no independent clearance claim** |
| Flat PNG brand assets | Retained in `iconset/flat`; Hakan Ertan / Tonicons historical credit and a 2026-08-12 maintainer attestation of rights-holder authorization are recorded; no written authorization is archived | **Authorization attested; archival evidence incomplete** |
| Long Shadows PNG brand assets | Retained in `iconset/long_shadow`; Hakan Ertan / Tonicons historical credit and a 2026-08-12 maintainer attestation of rights-holder authorization are recorded; no written authorization is archived | **Authorization attested; archival evidence incomplete** |
| Prajin PNG brand assets | Retained in `iconset/prajin`; Prajin historical credit and a 2026-08-12 maintainer attestation of rights-holder authorization are recorded; no written authorization is archived | **Authorization attested; archival evidence incomplete** |
| Historical X artwork in the four PNG packs | X files are regenerated with the pinned MIT-licensed Bootstrap Icons `twitter-x.svg` source by `scripts/generate-legacy-x-assets.js`; historical `twitter`/`Twitter` paths remain unchanged | **Reproducible; trademark review still required** |
| Prajin square mail tile | Local geometric adaptation of the existing Prajin circle mail artwork; no external asset fetch | Covered by the historical Prajin authorization attestation; archival evidence remains incomplete |
| Bundled Telegram and Bluesky SVG glyphs in historical sets | Source notes are recorded, but brand-guideline review remains required | Not cleared for publication by this record |
| Bootstrap Solid SVG set | Pinned Bootstrap Icons v1.13.1 inputs, checksums, deterministic generator, MIT license text, and manifest coverage are present | Source/license record present; browser and trademark review pending |
| Tabler Outline SVG set | Pinned Tabler Icons v3.46.0 inputs, checksums, deterministic generator, MIT license text, and manifest coverage are present | Source/license record present; browser and trademark review pending |

`resources/iconsets/ASSET-SOURCES.md` is the authoritative provenance note.
The maintainer attestation records a representation of authorization, but does
not replace archived written permission or independently establish its scope.
Do not convert it into a claim that the historical PNG packs are independently
cleared or may be freely redistributed. The X replacement is an approved
compatibility-preserving regeneration from the pinned Bootstrap source; keep
the source checksum and generator together with the released files.

## Browser validation evidence

On 2026-08-12, all 11 declared set/shape cells passed automated full-page
rendering checks in current Google Chrome, Mozilla Firefox, Microsoft Edge,
and Playwright WebKit at 1440×1024 and 390×844 viewport sizes. Durable PNGs, checksums, exact
versions, and limitations are recorded in `BROWSER-VALIDATION.md`. The test
found and corrected the lowercase canonical Prajin CSS selector gap while
retaining its historical uppercase selectors.

That evidence predates the historical X-artwork regeneration. Re-run the
browser matrix for a release candidate containing the new PNG bytes; the
earlier screenshots remain historical evidence only.

Playwright WebKit passed after its documented host dependencies were installed.
The same eight-project matrix then passed in a newly provisioned strict Sandbox
worker, with separate durable screenshots and checksums. After the responsive
rail correction, all eight projects passed again and Safari 26.6 was reviewed
separately on macOS at desktop size and in Responsive Design Mode at 390×844.
WebKit remains separate from the Safari evidence.

## Verification still required

- Run the manifest/asset and deterministic-generator checks for the candidate.
- Physical-device iOS testing remains outside this local evidence set; Safari
  Responsive Design Mode used the iOS 26.4 iPhone user agent at 390×844.
- Complete trademark and platform-brand-guideline review for every network.
- Archive the written authorization or license terms that substantiate the
  maintainer attestation for Flat, Long Shadows, and Prajin when available.
  Default provenance is not a release gate under the explicit compatibility
  exception above.
