# Icon asset provenance

The canonical `assets/iconsets/` tree is a path-normalized, byte-for-byte copy
of the icon assets bundled with release 2.2.6. Historical casing, the
`long_shadow` directory name, and `twitter` filenames are translated only by
the legacy asset map.

## Recorded sources

| Assets | Source | License and attribution | Status |
|---|---|---|---|
| All `bluesky.svg` glyphs | [Simple Icons](https://github.com/simple-icons/simple-icons) Bluesky path, adapted into the four bundled backgrounds | Repository is CC0-1.0, subject to the project’s [brand-icon disclaimer](https://github.com/simple-icons/simple-icons/blob/develop/DISCLAIMER.md). Each file retains an embedded source comment. | Recorded; recheck the individual brand license before release |
| All `telegram.svg` glyphs | Font Awesome Free 6 `paper-plane`, adapted into the four bundled backgrounds | [Font Awesome Free icons are CC BY 4.0](https://fontawesome.com/license/free). Each file retains an embedded attribution comment. | Recorded |
| Pre-existing PNG icons, previews, and style sheets | Inherited from the plugin’s historical Default, Flat, Long Shadows, and Prajin packs; the plugin UI credits Hakan Ertan / Tonicons | No upstream files, version identifiers, or license grant were present in the 2.2.6 repository | **Unresolved — release blocker** |

The compatibility test suite verifies that every canonical file matches the
2.2.6 byte, that PNG metadata is valid and bounded, and that SVG files are
well-formed, locally referenced, and free of active-content constructs.

The unresolved PNG rows must gain a verifiable upstream source, license, and
required attribution—or be replaced under an explicitly approved visual
change—before 3.0 is published.
