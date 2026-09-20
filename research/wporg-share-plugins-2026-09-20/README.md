# WordPress.org share-plugin research

Snapshot date: 2026-09-20 (Asia/Dhaka)

This directory contains ten plugins downloaded from the WordPress.org plugin API
and the corresponding `downloads.wordpress.org` ZIP endpoint. Each archive was
unpacked under `plugins/<slug>/<slug>/`; the API response used to select the
version is under `metadata/<slug>.json`. Archive hashes are in
`archives/SHA256SUMS`. `manifest.tsv` records the selected versions, source
URLs, install counts, and the corresponding archive hash.

## Selection

The set is a source-level comparison of widely installed or strategically
relevant share-button implementations, including server-rendered, block,
scriptless, native Web Share, and hosted-service approaches:

`add-to-any`, `sassy-social-share`, `simple-share-buttons-adder`, `social-pug`,
`kiwi-social-share`, `social-sharing-block`, `scriptless-social-sharing`,
`super-web-share`, `sharethis-share-buttons`, and `simple-social-buttons`.

The list is not a claim that these are the only or objectively best plugins.
It is a reproducible comparison sample. Source findings are static observations
of these downloaded versions; endpoint behavior, remote service policy, and
WordPress.org metadata can change after this snapshot.

## Review outputs

- `reviews/set-a.md`: AddToAny, Sassy Social Share, Simple Share Buttons Adder,
  Hubbub Lite/Social Pug, and Kiwi Social Share.
- `reviews/set-b.md`: Social Sharing Block, Scriptless Social Sharing, Super Web
  Share, ShareThis Share Buttons, and Simple Social Media Share Buttons.
- `findings.md`: consolidated competitor findings and review of the proposed
  next-release plan against the current html-social-share-buttons source.
