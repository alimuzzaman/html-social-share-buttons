# Browser validation evidence

This document records automated visual/browser evidence; it is not a browser
support declaration, a Safari result, or a release approval.

## 2026-08-12 icon-set matrix

The global Sandbox CLI local instance `html-social-share-latest` was healthy at
`http://localhost:8189`. The matrix created an authenticated WordPress page
through the REST API, rendered each declared icon-set/shape combination with
the public `[zm_sh_btn]` adapter, asserted the canonical wrapper classes, link
count, visible painted buttons, and non-empty `background-image`, then captured
a full-page PNG. The fixture covers 11 declared cells:

- Default square;
- Flat square and circle;
- Long Shadows square and circle;
- Prajin square and circle (six networks; the manifest intentionally omits
  Email);
- Bootstrap Solid square and circle; and
- Tabler Outline square and circle.

Command:

```sh
HSSB_BROWSER_ARTIFACT_DIR=docs/evidence/browser-validation/2026-08-12 \
SANDBOX_E2E_BASE_URL=http://localhost:8189 \
pnpm exec playwright test --config tests/browser-matrix.playwright.config.js --reporter=line
```

| Engine | Version | Desktop (1440×1024) | Mobile viewport (390×844) | Evidence |
|---|---:|---|---|---|
| Google Chrome | 151.0.7922.137 | Passed | Passed | `chrome-desktop.png`, `chrome-mobile-viewport.png` |
| Mozilla Firefox | 151.0 | Passed | Passed | `firefox-desktop.png`, `firefox-mobile-viewport.png` |
| Microsoft Edge | 151.0.4129.78 | Passed | Passed | `edge-desktop.png`, `edge-mobile-viewport.png` |
| Playwright WebKit | 26.5 | Passed | Passed | `webkit-desktop.png`, `webkit-mobile-viewport.png` |
| Safari | N/A | Not tested | Not tested | Playwright WebKit is **not** Safari evidence |

Screenshot SHA-256 values:

| Artifact | SHA-256 |
|---|---|
| `chrome-desktop.png` | `c8213628773f2f64daba639dd75982fc814331ba0a394e09ae1598904dc02001` |
| `chrome-mobile-viewport.png` | `73086af23dcff13bca9e9c9af86dbf9530ac9add4b6e6558ed2d8acdca0b843c` |
| `firefox-desktop.png` | `e94310c86b83dc694a30282e1cef649833d3ff7177059d80e817e0f887d779b6` |
| `firefox-mobile-viewport.png` | `9a448698d7cc10aa9d1afa95bfc46fb89d85f45adf3ab76b32ffdeddefb53640` |
| `edge-desktop.png` | `89e3f9965bff4b21912056fe1c6529e163c1ece70ccd55ad6ce5a6258b1c5fde` |
| `edge-mobile-viewport.png` | `837e42852a7e20b490a41a00f6b7d07cd5ec98f324f214403fc57fadec28f14b` |
| `webkit-desktop.png` | `cb4493199d3be0390b807680acddc0fe21414f09e0d3f6c6c2183d7835b7aa06` |
| `webkit-mobile-viewport.png` | `b3cbd1c7c0b78f64a9d9bfec69e34e14dee359b39e7fa0490a8f1509dd8d53b3` |

The initial full command found missing WebKit GTK/GStreamer dependencies. After
installing Playwright's documented WebKit host dependencies, both WebKit
projects were rerun and passed. All eight projects produced the artifacts
above. These are browser-engine desktop/mobile-viewport checks, not Safari or
tests on physical mobile devices.

## 2026-08-12 isolated-worker repeat

After Docker capacity was restored, the complete matrix was repeated through
the global Sandbox CLI in a newly provisioned strict worker:

```sh
HSSB_BROWSER_ARTIFACT_DIR=docs/evidence/browser-validation/2026-08-12-isolated \
sb e2e --local --workers 1 --strict-provision \
  --playwright-config tests/browser-matrix.playwright.config.js \
  --timeout 1200 --json
```

The first fresh worker passed the four Chrome/Edge projects and reported four
pre-launch environment failures because the exact Playwright Firefox and
WebKit executables were not installed on the host. After
`pnpm exec playwright install firefox webkit`, the command provisioned another
fresh WordPress 7.0.3 worker and all eight projects passed: Chrome desktop and
mobile, Firefox desktop and mobile, Edge desktop and mobile, and WebKit desktop
and mobile. This closes the isolated-worker repetition gate; it does not turn
WebKit into Safari evidence or cover physical mobile devices.

Isolated screenshot SHA-256 values:

| Artifact | Dimensions | SHA-256 |
|---|---:|---|
| `chrome-desktop.png` | 1440×3550 | `b6d66e778062cda2fb3d566f3f473d947ab8ffe08a1141ce7ae4cd240341485f` |
| `chrome-mobile-viewport.png` | 390×3497 | `a48ed5809eeb8d71273abdca7564fecc71860086693b5edaae2c30d568574acc` |
| `firefox-desktop.png` | 1440×3580 | `cf308abe0d5f92eaf0fdb1834f40bba17cf3d4d2c1d93d1812392eb15e731a36` |
| `firefox-mobile-viewport.png` | 390×3505 | `4a3b6ee2e1b27e5d770f97bb0eee3d214d1da5efd008ccece7f9ce916c9565b0` |
| `edge-desktop.png` | 1440×3586 | `68f71d39aa0e2b2c58825650c4e3cdd24e36053fb2cd98e0f4b420a8d5e36449` |
| `edge-mobile-viewport.png` | 390×3517 | `7db31b02893c12bd44386ef928266dfae898e165a7dffe228cca2a8f79541666` |
| `webkit-desktop.png` | 1440×3625 | `e5379c9c0c33cb284fdbd582c573a61beab4a25bbcec52ff2ca30b08b7d1c071` |
| `webkit-mobile-viewport.png` | 390×3505 | `f3805f1a7413f0949883ac389e0b54f9fea7ab28c9a48b34133bdc0cf61b483f` |

The eight files are stored in
`docs/evidence/browser-validation/2026-08-12-isolated/`.

Visual sampling of the isolated artifacts found that the fixed left placement
overlaps the matrix heading near the top of the 390-pixel viewport. The
automated assertions intentionally verify painted assets, wrapper classes,
link counts, and basic geometry; they do not certify collision-free responsive
placement. Treat the overlap as an unresolved manual mobile-layout review item
until a release owner decides whether the retained placement is compatible
behavior or requires a separately approved runtime change.

## Defect found and corrected

The first Chrome run found that the canonical wrapper uses lowercase `prajin`,
while the retained historical stylesheet targeted uppercase `.Prajin` only.
The wrapper consequently had no painted height in shortcode placement. The
stylesheet now retains every historical uppercase selector and adds lowercase
canonical aliases, including `.prajin.in_shortcode a`. The static
`tests/iconset-css-contract.php` contract protects those aliases; the complete
Chrome, Firefox, Edge, and WebKit matrix above passed after the correction.

## Still required

- Test current Safari on macOS/iOS hardware or a separately provisioned Safari
  service; do not substitute Playwright WebKit.
- Conduct manual interaction/accessibility review (keyboard focus, hover,
  high-contrast and real-device layout), including the observed 390-pixel
  fixed-rail/heading overlap; the automated checks establish painted asset
  presence and basic layout only.
