# Browser validation evidence

This document records automated visual/browser evidence and a separate manual
Safari run; it is not a general browser support declaration or release
approval.

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
- Prajin square and circle (seven networks);
- Bootstrap Solid square and circle; and
- Tabler Outline square and circle.

The archived screenshots below predate the regenerated X PNGs and the added
Prajin square mail asset. Re-run this matrix for a release candidate that
contains those bytes; the earlier screenshots are historical evidence only.

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
| Safari | N/A | Not tested in this automated run | Not tested in this automated run | Separate manual Safari evidence appears below; Playwright WebKit is **not** Safari evidence |

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
overlapped the matrix heading near the top of the 390-pixel viewport. That
finding led to the mobile-rail correction documented below. It is historical
failure evidence, not a current unresolved item.

## 2026-08-12 mobile-rail correction and Safari review

All six shipped icon-set styles now change fixed left/right rails to a static,
centered, wrapping row at viewport widths of 600 pixels or less. The static CSS
contract requires that rule in every pack. The browser matrix also locates each
left/right rail at the 390×844 mobile viewport, asserts computed `position:
static`, and fails if its box intersects the page heading.

The corrected tree passed all eight projects in a newly provisioned strict
WordPress 7.0.3 Sandbox worker:

```sh
HSSB_BROWSER_ARTIFACT_DIR=docs/evidence/browser-validation/2026-08-12-mobile-fix \
sb e2e --local --workers 1 --strict-provision \
  --playwright-config tests/browser-matrix.playwright.config.js \
  --timeout 1200 --json
```

| Artifact | Dimensions | SHA-256 |
|---|---:|---|
| `chrome-desktop.png` | 1440×3550 | `b6d66e778062cda2fb3d566f3f473d947ab8ffe08a1141ce7ae4cd240341485f` |
| `chrome-mobile-viewport.png` | 390×3559 | `8ed4a7c95fbc1f48cac86ecaccdf7de178a710d7b0019647b13c96eb6cd2630b` |
| `firefox-desktop.png` | 1440×3580 | `cf308abe0d5f92eaf0fdb1834f40bba17cf3d4d2c1d93d1812392eb15e731a36` |
| `firefox-mobile-viewport.png` | 390×3547 | `2102e02eba01e8d521eabed9241a3858c4353d9be5be0517bab209623d6a1f1e` |
| `edge-desktop.png` | 1440×3589 | `f3fb691c7cb8b196b232acfb8ffaa10b42cf02619f412497bd9b9b14f4a8bbbb` |
| `edge-mobile-viewport.png` | 390×3559 | `5636ea03e2c261e2e8ba552d256c81e7f7578cdd2bea9bf4b3b2f59d10548db2` |
| `webkit-desktop.png` | 1440×3625 | `e5379c9c0c33cb284fdbd582c573a61beab4a25bbcec52ff2ca30b08b7d1c071` |
| `webkit-mobile-viewport.png` | 390×3547 | `b616b9230ec97385bac1bedd354fc1bd2ca26a90e7e1d58531812fbc15fe445a` |

Safari was tested separately through the macOS Safari application, not inferred
from Playwright WebKit. Safari 26.6 desktop and 390×844 Responsive Design Mode
captures were recorded. A second responsive capture selected Safari's iOS 26.4
iPhone user agent. Both responsive captures show the heading unobstructed and
the formerly fixed rail in document flow. The screenshots document the reviewed
viewport states; they do not independently certify the full matrix or an
accessibility tree. The capture PNG itself is the 1202×768 Safari window; the
390×844 emulated viewport is visibly recorded in its toolbar.

| Safari artifact | SHA-256 |
|---|---|
| `safari-desktop.png` | `8bf720c391c40fd19009e599f75550e196bf9c29218d65571f0e50d714187df6` |
| `safari-responsive-390x844.png` | `ac3bcc653e17a39331dee2805e757b8979bcf5b3636a9e97ae6f5db2ff166aaf` |
| `safari-iphone-ua-390x844.png` | `e00b046c53a668863295ba15b0027a19b97f8a40506cb70cf0bb0b67cb4ef69c` |

These files are stored in
`docs/evidence/browser-validation/2026-08-12-mobile-fix/`. Responsive Design
Mode and an iPhone user agent are not evidence from a physical iPhone. The
automated settings-dialog keyboard test remains the executable keyboard proof;
no unverified manual focus screenshot is claimed.

## Defect found and corrected

The first Chrome run found that the canonical wrapper uses lowercase `prajin`,
while the retained historical stylesheet targeted uppercase `.Prajin` only.
The wrapper consequently had no painted height in shortcode placement. The
stylesheet now retains every historical uppercase selector and adds lowercase
canonical aliases, including `.prajin.in_shortcode a`. The static
`tests/iconset-css-contract.php` contract protects those aliases; the complete
Chrome, Firefox, Edge, and WebKit matrix above passed after the correction.

## Remaining scope limits

- Physical iOS-device and high-contrast-mode review are not part of this local
  evidence set.
- The browser matrix establishes painted assets and collision-free geometry for
  its fixtures; it is not a universal theme/layout certification.
