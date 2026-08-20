# Superseded fourteen-day staging soak

## Status

**Waived by the release owner on 2026-08-13; this is not a current release
gate.** The owner chose a manual exact-archive review and a 3–4 day release
schedule before WordPress 7.1 instead of waiting fourteen elapsed days. The
recurring soak automation was deleted, and no active baseline or daily record
may be created for this superseded procedure.

The 2026-08-12 attempt was stopped after the release-diff audit found
packaged-runtime defects and a version collision with the already published
2.2.6 release. Its original evidence remains under
`docs/evidence/staging-soak-superseded/attempt-01-2026-08-12/`; nothing was
backfilled or reused for the corrected candidate.

## Current replacement gate

The superseded working-tree 3.0.0 ZIP received a manual review on 2026-08-13 in a
disposable WordPress 7.1-RC3 / PHP 8.3.33 instance. The settings UI was
reviewed at desktop and 390×844 widths; both API-v3 blocks were inserted and
saved; the Chromium forced-iframe test edited Inspector controls, reloaded the
stored attributes, and rendered the frontend; desktop and mobile frontend
layouts were reviewed; audience-setting persistence passed; rendered URLs
contained the real permalink and no raw or encoded placeholder; Plugin Check
reported zero errors and the same 57 reviewed warnings; and the bounded nginx
and PHP-FPM log window contained no 5xx, fatal, uncaught, or error entry.
Two editor deprecation warnings referenced legacy `core/edit-post` panel and
custom-toolbar APIs that do not occur in the plugin source or generated
bundles; the focused run recorded no console error or page error.

This manual gate does not make an uncommitted working tree immutable. Before a
release is authorized, the reviewed source must be committed intentionally,
the production ZIP rebuilt from that revision, and its exact SHA-256 and
archive contract reconfirmed. Any packaged-byte change requires rerunning the
focused exact-archive checks.

## Historical soak candidate baseline

| Field | Value |
|---|---|
| Candidate version | 3.0.0 |
| Candidate Git revision | Pending final validated commit |
| Candidate archive path | `/Users/alim/Sites/git/html-social-share-buttons.3.0.0.zip` |
| Candidate SHA-256 | `0900bc11b58b5e866bd4d359071cb26bc8216245e136426de1e5e3aa30ecee92` (working-tree validation build) |
| Candidate size / entries | 682,334 bytes / 234 files |
| Installed tree manifest SHA-256 | Pending exact staging installation |
| Rollback release | Published WordPress.org 2.2.6 |
| Rollback SHA-256 | `f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941` |
| Staging environment | Sandbox `scaleway-sandbox`, instance `html-social-share-button`, WordPress 7.0.3 / PHP 8.3.33 |
| Fixture URL | `https://default-html-social-share-buttons.sandbox.asb.bd/hssb-staging-soak-fixture/` (page ID 12) |
| Started at | Not started |
| Earliest valid completion | Start time plus fourteen real elapsed days |

The active `docs/evidence/staging-soak/` directory intentionally contains no
daily record until the corrected exact archive is installed and observed.
The superseded recorded archive was reproduced twice and passed exact-install checks on a
disposable WordPress 7.1-RC3 runtime. Its installed 234-file manifest matched
the local extraction at
`474f5bbf3e5e5f855378f53c219db47829718aff97219f07cc5aa9d225f3ee0c`.
That disposable proof is not the pending staging-tree identity. The candidate
is not yet an approved Git-revision baseline and has not been deployed to the
declared staging environment.

## Historical baseline initialization

If the release owner reactivates this optional procedure, the validator is
fail-closed until the exact staging installation and Day 1 observation have
produced `docs/evidence/staging-soak/baseline.json`. Never copy values from the
superseded attempt or from the disposable WordPress 7.1 runtime. The active
baseline must be one JSON object with this shape:

```json
{
  "schema_version": 1,
  "status": "active",
  "started_at": "DAY-1-OBSERVED-UTC",
  "candidate_sha256": "0900bc11b58b5e866bd4d359071cb26bc8216245e136426de1e5e3aa30ecee92",
  "installed_manifest_sha256": "STAGING-INSTALLED-TREE-SHA256",
  "candidate_git_revision": "APPROVED-40-CHARACTER-GIT-REVISION",
  "fixture_url": "https://default-html-social-share-buttons.sandbox.asb.bd/hssb-staging-soak-fixture/",
  "runtime": {
    "wordpress": "OBSERVED-WORDPRESS-VERSION",
    "php": "OBSERVED-PHP-VERSION",
    "active_plugin_version": "3.0.0"
  },
  "persisted_hashes": {
    "settings": "SHA256",
    "disabled_share": "SHA256",
    "elementor": "SHA256",
    "wpbakery": "SHA256",
    "content": "SHA256",
    "schema_version": "absent"
  }
}
```

Create it from the same bounded observation that creates Day 1, then keep it
immutable for that attempt. A byte change, environment replacement, or reset
requires moving the whole attempt to the superseded evidence area and creating
a new observed baseline; editing the old baseline is not a continuation.

## Historical pass criteria

- Fourteen consecutive elapsed 24-hour periods on the same exact candidate
  bytes and declared staging environment.
- One dated record per day with HTTP result, fixture probe JSON, relevant
  WordPress/PHP error snapshot, active plugin/version evidence, and any anomaly.
- Browser fixture review on days 1, 7, and 13.
- Candidate to published 2.2.6 to candidate dry rollback on day 7, without
  uninstalling or changing stored data.
- Final staging rollback after day 14 with option, disabled-meta, block,
  Elementor, and WPBakery fixture hashes unchanged and restored candidate
  output matching the pre-rollback baseline.
- No unexplained plugin error, missing required observation, candidate-byte
  change, environment replacement, placeholder leak, or corrupted share URL.

Any such event pauses or resets the clock. A missed day cannot be backfilled.

## Daily public probe

```sh
node scripts/staging-soak-probe.js \
  --url "https://STAGING-HOST/hssb-staging-soak-fixture/" \
  --marker "HSSB staging soak fixture"
```

The bounded probe requires a successful HTTP response, the fixture marker, a
rendered `zmshbt` wrapper and share link, and absence of raw or double-encoded
permalink placeholders. Redirects are rejected so another page cannot satisfy
the staging check. It emits timestamped JSON and a response-body hash.

## Daily record template

Create `docs/evidence/staging-soak/YYYY-MM-DD/day-NN.md` without changing past
records:

```text
# Day NN - YYYY-MM-DD

- Observed at (UTC):
- Candidate SHA-256:
- Git revision:
- WordPress / PHP:
- Active plugin version and archive evidence:
- Fixture probe JSON:
- Relevant error-log result:
- Browser review (days 1, 7, 13 only):
- Rollback result (day 7 and final only):
- Anomalies: none / details
- Disposition: pass / pause / reset
```

Each Markdown record must have an adjacent authoritative JSON sidecar with the
same base name, matching the schema used by `day-01.json`. The validator reads
the JSON directly; it never interprets Markdown as machine evidence. Validate
existing records against the active baseline with:

```sh
node scripts/validate-staging-soak-evidence.js
```

Use `--baseline /absolute/path/to/baseline.json` only for an isolated rehearsal
or validator test. The default always reads the active evidence directory's
`baseline.json`; it contains no built-in candidate, runtime, timestamp, or
persistence identity that could accidentally accept a superseded attempt.

After writing Day N, pass `--require-through N` so a missing daily record fails.
The validator enforces the recorded candidate/tree identities, 24-hour UTC
windows, contiguous records, runtime and persistence baselines, probe checks,
zero relevant errors/5xx responses, browser days, and the Day-7 rollback.
Response body hashes are recorded but may vary when otherwise valid markup or
cache state changes.

Day 14 remains a normal daily observation inside its elapsed window. Record the
required post-threshold rollback separately at
`docs/evidence/staging-soak/YYYY-MM-DD/final-rollback.md` plus an adjacent
`final-rollback.json`, with `kind: "final_rollback"` in the JSON. Only after
that operation succeeds, run:

```sh
node scripts/validate-staging-soak-evidence.js --require-through 14 --completion
```

Completion mode requires exactly fourteen valid daily records plus one valid
final rollback observed no earlier than the active Day 1 timestamp plus
fourteen elapsed days. It never treats Day 14 alone as completion.

Day-7 and final JSON evidence must identify the intermediate published
archive SHA-256 (`f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941`),
its activation/version, full observed probe JSON, and persistence hashes. It
must then contain the exact restored candidate archive/tree identities, full
passing restored-probe JSON, and restored persistence hashes equal to the Day 1
baseline. Rollback completion booleans alone are insufficient.

## Rollback sequence

1. Snapshot the database/uploads and record option/meta/builder fixture hashes.
2. Replace the active candidate with the exact published 2.2.6 archive without
   uninstalling it or deleting data.
3. Activate 2.2.6 and record public output plus persisted hashes.
4. Replace it with the exact candidate archive, activate, and repeat the same
   checks.
5. Require restored candidate output and all persisted hashes to equal their
   pre-rollback values. Record expected historical output differences while
   2.2.6 is active; do not misclassify them as data loss.

The successful isolated WP 5.3/PHP 7.0 rehearsal in
`RELEASE-CANDIDATE-VALIDATION.md` validates the mechanism but does not replace
these staging operations or the elapsed-time requirement.
