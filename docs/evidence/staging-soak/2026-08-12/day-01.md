# Day 01 - 2026-08-12

- Disposition: **pass; clock started**
- Started at (UTC): `2026-08-12T09:04:40Z`
- Earliest valid completion: `2026-08-26T09:04:40Z`
- Candidate archive SHA-256: `d6575a33ff120ec768b6f71a4ea29f51a083760d016cd5f9a599aa0982945b05`
- Candidate Git revision: `78c7f2344f01620441528b00707bb77152de476c`
- Candidate archive size / entries: 668,318 bytes / 231 files
- Rollback archive SHA-256: `f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941`
- Staging: Sandbox remote `scaleway-sandbox`, instance
  `html-social-share-button`, public HTTPS host
  `default-html-social-share-buttons.sandbox.asb.bd`
- Runtime: WordPress 7.0.3, PHP 8.3.33, Twenty Twenty-Five 1.5
- Active relevant plugins: HTML Social Share Buttons 2.2.6, Elementor 4.2.2,
  Plugin Check 2.0.0
- Fixture: page ID 12,
  `https://default-html-social-share-buttons.sandbox.asb.bd/hssb-staging-soak-fixture/`

## Exact-byte evidence

The local archive was built twice consecutively and the SHA-256 output matched.
It was copied to the remote host, verified there, and installed through WP-CLI
with `--force --activate`. The installed plugin is a directory, not the source
deployment symlink, and contains 231 files.

The sorted per-file SHA-256 manifest was hashed locally from an archive
extraction and independently inside the installed staging plugin. Both results
were:

`8ede5b6e6789c218a10c8efe5f395a4db0d6b928167a4050334da2f78432c42b`

## Persisted baseline

| Surface | SHA-256 / value |
|---|---|
| `zm_shbt_fld` serialized option | `9c6286ffade97ba5926dafef4c328f697a4ff0e0df300e5d6c4fb41d31748aaf` |
| `_zm_sh_disable_share` | empty; serialized hash `36761a168eb691d20edf88ace3d06fb63ec9112f257ac2f20fce1afdc331b40b` |
| Elementor fixture meta | `2cb96b33cbd7c1b5080d4666f71740e5257c777e7322d0f455ad33d7359a6388` |
| WPBakery fixture meta | `d92ebdfe82b849bdc64686a4ea341e14bfa2fb668d6228bb7f34ac67059c713c` |
| Stored shortcode/block content | `a00f5e609c577e8cd87b34a79b04d71026a2e8efcd34dd0beda4140c2b910565` |
| `hssb_schema_version` | absent |

## Public and browser evidence

The bounded public probe returned HTTP 200 at `2026-08-12T09:04:40.169Z`.
The fixture marker, share wrapper, and share link were present; neither the raw
nor double-encoded permalink placeholder appeared. Cache-busted response-body
SHA-256 was
`fd195ec0f9613c1991dbfa2838c4421addf1a4e2771ced0272e6870e482f221e`.

The in-app browser independently loaded the final candidate:

- Desktop 1280×720: three wrappers, 27 links, 27 painted buttons, no raw or
  encoded placeholder, no browser warning/error log.
- Mobile 390×844: three wrappers, 27 links, 27 painted buttons. The left rail
  computed to `position: static`, 375×92 pixels, and did not intersect the page
  heading. No browser warning/error log was recorded.

## Pre-start bootstrap findings

These events occurred before `2026-08-12T09:04:40Z` and therefore do not count
as soak failures or elapsed evidence:

1. A source-only remote deploy lacked the ignored production Composer
   autoloader and failed before exposure.
2. A separate-instance attempt could not allocate another Docker network on
   the remote. The existing dedicated HSSB instance was reconciled in place;
   no unrelated resources were deleted.
3. The source bootstrap's realpath remained in PHP-FPM opcode state after the
   exact ZIP replaced the symlink, producing incorrect asset URLs. Restarting
   the dedicated WordPress PHP container cleared that state. The final exact
   archive then rendered all checked buttons, and the container log after the
   final restart contained only normal FPM start/ready notices. No nginx 5xx
   entry was present in the reviewed window.

The clock began only after the final archive, exact installed-tree manifest,
public probe, desktop/mobile rendering, persisted hashes, and error review all
passed.
