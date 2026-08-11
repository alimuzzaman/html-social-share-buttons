# Icon asset provenance

The historical `iconset/` tree retains the released 2.2.6 assets and public
URLs, including their casing, `long_shadow` directory name, and `twitter`
filenames. `assets/iconsets/` contains only the reproducibly generated SVG
sets and their license notices. Canonical manifests record each pack's actual
asset path; no duplicate historical tree or legacy asset map is shipped.

## Recorded sources

| Assets | Source | License and attribution | Status |
|---|---|---|---|
| All `bluesky.svg` glyphs | [Simple Icons](https://github.com/simple-icons/simple-icons) Bluesky path, adapted into the four bundled backgrounds | Repository is CC0-1.0, subject to the project’s [brand-icon disclaimer](https://github.com/simple-icons/simple-icons/blob/develop/DISCLAIMER.md). Each file retains an embedded source comment. | Recorded; recheck the individual brand license before release |
| All `telegram.svg` glyphs | Font Awesome Free 6 `paper-plane`, adapted into the four bundled backgrounds | [Font Awesome Free icons are CC BY 4.0](https://fontawesome.com/license/free). Each file retains an embedded attribution comment. | Recorded |
| `bootstrap-solid` SVG set | [Bootstrap Icons v1.13.1](https://github.com/twbs/icons/tree/v1.13.1/icons), using the Facebook, Twitter X, LinkedIn, Pinterest, Telegram, Bluesky, and Envelope glyphs | MIT. Pinned upstream sources and SHA-256 checksums are retained under `scripts/iconsets/upstream`; the distributed license is `assets/iconsets/licenses/bootstrap-icons-MIT.txt`. | **Cleared and reproducibly generated** |
| `tabler-outline` SVG set | [Tabler Icons v3.46.0](https://github.com/tabler/tabler-icons/tree/v3.46.0/icons/outline), using the matching six brand glyphs and Mail | MIT. Pinned upstream sources and SHA-256 checksums are retained under `scripts/iconsets/upstream`; the distributed license is `assets/iconsets/licenses/tabler-icons-MIT.txt`. | **Cleared and reproducibly generated** |
| Flat brand PNGs | Probable adaptations of Hakan Ertan’s [Flat Social Media Icons](https://www.softicons.com/social-media-icons/flat-social-media-icons-by-hakan-ertan); the [WordPress plugin page](https://wordpress.org/plugins/html-social-share-buttons/) also credits Hakan Ertan / Tonicons | Downloaded candidates are visually related but not byte-identical. The source page says commercial use is allowed but provides no explicit redistribution, modification, sublicensing, or GPL-compatible grant. | **Probable source; license unresolved — release blocker** |
| Long Shadows brand PNGs | Probable adaptations of Hakan Ertan’s [Round Shadow](https://www.softicons.com/social-media-icons/round-shadow-social-icons-by-hakan-ertan) and [Square Shadow](https://www.softicons.com/social-media-icons/square-shadow-social-icons-by-hakan-ertan) sets | Downloaded candidates are visually related but not byte-identical. Their pages do not provide the redistribution and modification grant required for distribution with this plugin. | **Probable source; license unresolved — release blocker** |
| Prajin brand PNGs | Probable adaptations of Prajin’s ShareIcon [Social Media pack](https://www.shareicon.net/author/prajin) | Individual pages identify Prajin and say “Free for commercial use,” but downloaded candidates are not byte-identical and the [site terms](https://www.shareicon.net/tos) provide no explicit redistribution, modification, sublicensing, or GPL-compatible grant. | **Probable source; license unresolved — release blocker** |
| Default pack PNGs, utility icons, previews, and style sheets | Inherited from release 2.2.6 | No verifiable upstream files, version identifiers, or license grant are recorded. | **Source and license unresolved — release blocker** |

The compatibility test suite verifies byte parity for historical canonical
files, validates every manifest asset, checks that PNG metadata is valid and
bounded, and checks that SVG files are well-formed, locally referenced, and
free of active-content constructs. `pnpm run icons:check` separately verifies
that every generated SVG and stylesheet matches its pinned source input.

The unresolved PNG rows must gain a verifiable upstream source, a license that
permits modification and redistribution with this GPL plugin, and required
attribution—or be replaced under an explicitly approved visual change—before
3.0 is published. Probable historical attribution is not release clearance and
is therefore not listed as a cleared asset in `THIRD-PARTY-NOTICES.txt`.
Copyright clearance for the generated sets does not grant trademark rights in
the represented platform names or logos; downstream use must still follow the
relevant platform brand guidelines.
