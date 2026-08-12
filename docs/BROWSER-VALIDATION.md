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

An isolated `sb e2e --local --workers 1` attempt was also made first. Sandbox
could not create the fresh worker network because Docker reported that all
predefined address pools were fully subnetted. The visual run therefore used
the already healthy local global-Sandbox instance and must be repeated in an
isolated worker after Docker network capacity is recovered.

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
- Repeat the complete matrix in a fresh isolated Sandbox worker after the
  Docker network-pool issue is fixed.
- Conduct manual interaction/accessibility review (keyboard focus, hover,
  high-contrast and real-device layout) if it is a release gate; the automated
  checks establish painted asset presence and basic layout only.
