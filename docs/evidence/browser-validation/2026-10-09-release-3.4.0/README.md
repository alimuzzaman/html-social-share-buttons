# Release 3.4.0 browser validation (2026-10-09)

Run against a local Sandbox site (WordPress 7.1, PHP container) with the
production archive installed from the zip. The remote preview could not be
created (Sandbox `preview create` failed on both remote hosts), so this run
used the local instance.

Starting state: a saved 3.3.0-shaped `zm_shbt_fld` option. The script then
enabled WhatsApp, Reddit, and Copy link through the settings form, saved, and
checked the post "Café & Co: share test" in Chromium (Playwright 1.61).
`results.json` holds all 22 checks; all passed.

| File | Shows |
| --- | --- |
| `01-admin-networks.png` | Settings > Social Networks with the three new networks, the Copy link panel without a share template, and its frontend-JavaScript badge. |
| `02-frontend-buttons.png` | The rendered share bar after saving, with JavaScript. |
| `03-frontend-post.png` | The same bar in the post. |
| `04-nojs-buttons.png` | The bar with JavaScript disabled; Copy link is a plain link to the page. |
| `05-icon-sets-new-glyphs.png` | Bluesky, WhatsApp, Reddit, and Copy link glyphs across the legacy icon sets and shapes. |

Checked behaviour:

- The new toggles are present and start disabled, then persist once saved.
- WhatsApp opens `wa.me` with the title and permalink. Reddit opens
  `/submit` with the permalink and title. Both titles match the X button's
  text exactly.
- Copy link has no `target`, enqueues `copy-link.js`, writes the permalink
  to the clipboard, announces "Link copied" in a polite live region, and does
  not navigate.
- With JavaScript disabled, Copy link is a plain link to the page.

Compatibility probes on the same site:

- Never-saved install (option deleted): renders Facebook, X, LinkedIn,
  Pinterest, Telegram, Bluesky, and Email, the same set as 3.3.0, with no
  frontend scripts.
- Saved 3.3.0 option: renders the same set as above with no frontend scripts.

Pre-existing behaviour seen during the run (unchanged from 3.3.0, not
addressed by this release):

- `%%title%%` carries the texturized title, so an ampersand reaches every
  title-bearing share URL (X, Pinterest, Email, and now WhatsApp and Reddit)
  as `&#038;`.
- Saving the form stores network keys in the form's two-column order, so
  the automatic bar follows that order after a save.
