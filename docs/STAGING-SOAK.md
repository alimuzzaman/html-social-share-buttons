# Fourteen-day staging soak

## Status

**In progress.** The exact candidate archive was installed and verified on the
declared staging environment before the clock started at
`2026-08-12T09:04:40Z`. The soak has not passed. It cannot pass before fourteen
real elapsed days and the final staging rollback.

## Candidate baseline

| Field | Value |
|---|---|
| Candidate Git revision | `78c7f2344f01620441528b00707bb77152de476c` |
| Candidate archive path | `/Users/alim/Sites/git/html-social-share-buttons.2.2.6.zip` |
| Candidate SHA-256 | `d6575a33ff120ec768b6f71a4ea29f51a083760d016cd5f9a599aa0982945b05` |
| Candidate size / entries | 668,318 bytes / 231 files |
| Installed tree manifest SHA-256 | `8ede5b6e6789c218a10c8efe5f395a4db0d6b928167a4050334da2f78432c42b` (matches local archive extraction) |
| Rollback release | Published WordPress.org 2.2.6 |
| Rollback SHA-256 | `f056820bf7377ca4e228fe28792f23a3e6bf226db4d1a98c85bb26be9d23f941` |
| Staging environment | Sandbox `scaleway-sandbox`, instance `html-social-share-button`, WordPress 7.0.3 / PHP 8.3.33 |
| Fixture URL | `https://default-html-social-share-buttons.sandbox.asb.bd/hssb-staging-soak-fixture/` (page ID 12) |
| Started at | `2026-08-12T09:04:40Z` |
| Earliest valid completion | `2026-08-26T09:04:40Z` |

Day 1 evidence is recorded at
`docs/evidence/staging-soak/2026-08-12/day-01.md`. The initial staging
bootstrap/path-cache failures occurred and were corrected before the start
timestamp; they are recorded there rather than omitted from the history.

## Pass criteria

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
existing records offline with:

```sh
node scripts/validate-staging-soak-evidence.js
```

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
final rollback observed no earlier than `2026-08-26T09:04:40Z`. It never treats
Day 14 alone as completion.

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
