# 3.2.0 release acceptance

Status: **local candidate validated; release blocked by hosted CI**. No tag, merge, deployment or publication is authorized by this record.

Candidate source: `1fd860d4ef8acde3e19aabf603a60a8aca7362aa` on `codex/plugin-update-preview`. The source commit is local. GitHub rejected its push because the current OAuth login lacks `workflow` scope; hosted validation and the hosted failure probe have not run. The owner has been asked to refresh that scope.

## Observed local evidence

- Node 22.18.0 / pnpm 11.5.2: final frozen-lock build, settings contracts, JavaScript lint, icon determinism, appearance and release contracts passed. CI is configured for Node 24.
- PHP 8.5.8: Composer strict validation, PHPStan on 84 source files, PHPCS and syntax for 121 tracked non-PHPUnit PHP files passed. This does not substitute for the configured older PHP matrix.
- Isolated WordPress 7.1 / PHP 8.3 / PHPUnit 9.6.36: regular suite passed 210 tests / 3,222 assertions with one expected multisite-only skip; AJAX passed 14 / 65; focused multisite passed 1 / 7. These initial runs used the current source copy, before exact-archive installation.
- The registered Settings API test includes actual update_option/add_option double sanitation, unchanged effective settings, extension data, and exact retired-value/absence preservation. The strict frontend golden comparison passed without further fixture changes.
- Release contracts passed 18 static failure/cancel/skip paths, nine candidate archive cases, Plugin Check output validation and dependency remediation checks. Hosted failure injection remains pending.
- The release browser config discovers 48 tests across Chromium, Firefox and WebKit at desktop and 390px viewports. Discovery is not execution.

The [dependency disposition](2026-09-05-dependency-disposition.md) recommends accepting six retained advisories with specific unused-tool/unaffected-API restrictions. Both locks resolve those same six advisory IDs. This is not a vulnerability-free dependency tree.

## Exact local archive

`dist/release-candidate-3.2.0-1fd860d/candidate.zip` contains 242 files / 723,929 bytes. SHA-256: `7249267b6bd98b331347b437a2f6f9058a879f58374f82a0b1643a7a261f74bf`. The adjacent manifest records the full source SHA and each file hash.

Built from git archive of the stated commit, with a production authoritative Composer loader and verified matching frozen dependency graph. Two isolated builds were byte-identical; distribution, manifest, verified extraction and extracted-file parity checks passed. Root independently verified the retained copy. The isolated build changed only VC bundle numeric webpack module IDs and its matching asset hash versus committed generated files; the precise webpack context cause was not traced. This is local reproducibility, not cross-environment or hosted-artifact proof.

The exact ZIP installed on the isolated WordPress site as 3.2.0. A fresh per-file check found 242 installed files, 242 manifest entries and zero mismatches; stored settings and the detached source copy were unchanged. Sandbox's original read-only source symlink prevented normal overwrite. The installer checked its exact disposable target, detached only that symlink, then let WordPress's Plugin_Upgrader create a physical plugin installation. No original Git checkout, source permissions or source files were changed.

Plugin Check 2.1.0 with its early runtime loader and strict JSON completed with **zero errors and 63 warnings** on the exact ZIP. All warnings have a [source disposition](2026-09-06-plugin-check-disposition.md); no baseline was created. No confirmed security/regression issue was found. The regular suite also passed against the installed archive (210 / 3,222 / one expected skip), AJAX passed 14 / 65, and focused multisite passed 1 / 7.

Rollback/reinstall passed from exact candidate 3.2.0 to official WordPress.org 3.1.0 and back to the same candidate. Published archive SHA-256 was `1435c8a24b35877097ca71b65376f1f7ac97c36fe5ee02122ce0ea52dbdb8841`. The synthetic fixture's settings, post content and metadata hashes were identical across all three states. Final activation was 3.2.0 with 242 files and zero manifest mismatches. No uninstall or data deletion occurred. The rollback content fixture used an unsupported literal shortcode, so these hashes prove data preservation only; a separate correct-shortcode fixture is used for public rendering checks.

## Public browser acceptance

A separate public matrix passed **48 cases** on the exact installed ZIP: Chromium, Firefox and WebKit × 1440px/390px × four appearances × auto-hide on/off. Both rails had the expected classes, 12px desktop concealment, full mobile/disabled visibility and focus reveal. Actual assets returned successfully; the correct shortcode rendered Facebook/X anchors with the canonical page URL; no horizontal overflow or inline `_gaq` script was found.

The actual installed matrix used the retained Default pack. Earlier six-pack coverage is a static stylesheet fixture and is not presented as six-pack WordPress runtime evidence. These 48 public cases are separate from the 48 configured release-browser tests, whose authenticated settings and editor acceptance is recorded below. No real-device/Safari application certification is claimed.

Root visually reviewed representative current-fixture desktop/mobile screenshots across Legacy, Minimal, Framed and Soft shadow. Buttons, content and rails were contained without clipping or overlap. Screenshots show fully revealed rails with reduced motion. Evidence and screenshots are retained under `dist/release-candidate-3.2.0-1fd860d/public-browser/`; root independently checked all eight JSON files, 48 cases, zero failed checks and the correct fixture URL.

FPM initially retained the old source-symlink path after installation, causing public asset 404s while a new CLI process saw the canonical path. A supported restart of the isolated services cleared the stale paths; the final public checks used canonical URLs returning 200. No plugin source change was required for that environment issue.

## Browser broker diagnosis and repair

Sandbox refused its generated credential source as unsafe. It then failed supported broker creation of a fresh disposable credential with `operation_failed`. No refused secret source was read or permission-changed, and no credential bypass was attempted. Follow-up source review identified a concrete configuration error: the generated dotenv source was registered as `browser-generated.env`, while Sandbox requires a basename beginning with `.env` (`sandbox/config/secrets.py:79-95`). The temporary alias was corrected to a fresh `.env.release-browser`; broker source-info then reported safe/empty/readable and supported create-only generation succeeded. The refused YAML and previous dotenv source stayed untouched. The wrapper now explicitly isolates `SANDBOX_SECRETS_FILE` as well as `SANDBOX_HOME`; the built-in personal source is not used. A supported disposable WP-CLI password reset generated a credential in memory; a trusted producer piped it into the safe broker source. The broker supplied only that selected credential to the bounded browser recipient. No password, login screenshot, trace, storage state or raw failure stream was retained in test evidence. The wrapper used Python 3.14 after a harmless probe exposed an incompatible macOS Python 3.9 interpreter.

## Authenticated browser acceptance

The first configured release-browser attempt completed 47 of 48 cases before its 900-second worker bound: 42 passed, three failed and two timed out; the final WebKit mobile preview case did not complete. This failed attempt is retained, not presented as a green full-suite run.

Diagnosis produced three test-only corrections. No distributed plugin file changed:

- `8b9f5ec`: the static rail test now measures total and visible width in one layout snapshot. Firefox previously compared a transient Prajin width of 49px with its settled 36px width. All six corrected static projects passed.
- `8ea0e2c`: the preview test dismisses token autocomplete with Escape and asserts it closed. A mobile screenshot and hit-test proved the suggestion menu covered Preview sample; no preview request had been made. All six corrected preview projects passed.
- `c700b54`: the editor test waits, with a ten-second bound, for same-origin REST requests to finish before navigation. WebKit reported cancelled WordPress comments requests as Fetch API access-control errors. All 12 final editor cases passed with both zero-console-error and zero-page-error assertions unchanged. The exact tested file SHA-256 is `96dbc6afb081ad07717511b25a482b7a5302547b8e9269299260ea4dbd96cca4`.

The final evidence covers all 48 configured cases across the initial successful cases and corrected targeted reruns. This is split-run local acceptance on WordPress 7.1/PHP 8.3, not one uninterrupted green run or hosted CI. The independent tracker review found no actionable issue; its 500ms quiet period is bounded test stabilization, not proof of all network activity. Public installed-plugin paths use the exact ZIP; the static CSS test uses copied shipped styles.

Root independently consolidated the latest result for each project/test pair: 48 unique cases, all passed. The retained initial attempt, targeted results, summary and six screenshots are under `dist/release-candidate-3.2.0-1fd860d/authenticated-browser/`. Static-case durations are agent-observed terminal evidence, with rounded Firefox durations labelled explicitly.

Root reviewed desktop/mobile settings, preview and actual persisted-block editor screenshots: controls were contained, resolved URLs wrapped within their cards, and both block previews rendered. Browser artifacts contain only sanitized result metadata and authenticated UI screenshots.

## Analytics scope

The 3.2 candidate adds no analytics tracking. The obsolete `_gaq` integration and active control were removed; stored legacy preferences remain inert for compatibility and rollback. There is no GA4/GTM integration or built-in click dashboard. The public FAQ and Advanced settings describe this retirement. Optional, consent-aware click events would be a separate feature, disabled by default; a click must not be reported as proof that a user completed a share. The current release plan excludes adding tracking.

## Remaining acceptance

The hosted support matrix and failure probe, and review of the eventual hosted artifact remain pending. Historical 3.0/3.1 evidence does not certify this candidate. The elapsed staging-soak waiver remains in effect.

## Final local state and handoff

After authenticated checks, the original settings hash, rollback fixture content/meta hashes and the final 242-file installed manifest were reverified unchanged. The candidate ZIP and sanitized local runtime evidence remain in `dist/release-candidate-3.2.0-1fd860d/`. This ZIP is not a hosted CI artifact and must not be substituted silently for a later hosted candidate.

GitHub rejected `git push -u origin codex/plugin-update-preview` because its OAuth credential lacks `workflow` scope. Operator action: `gh auth refresh -h github.com -s workflow`, then retry the branch push through the authorized GitHub login. No remote branch update, hosted workflow, failure probe, tag, merge or publication succeeded in this task. The broker configuration has since been repaired and authenticated browser acceptance completed in split runs.

Sanitized Sandbox feedback: `b9e1da6c97dac6461e4ac114caac21d7` (broker creation), `2d93523470bceb6befe1ab2c78cbf33d` (ZIP staging), `d8819ca4e480822ec7564a2db5bd8927` (upgrader diagnostics). The latter install issues were resolved for this disposable test site with the scoped procedure above; the original broker error was resolved by correcting the dotenv registration, with causal feedback `d9eb85788fa55072ef3c1f689a85b5fd`. Feedback `d85ea47a7f172f9e7d6ffaf49f657960` records the WP-CLI preflight consuming stdin; the final supported producer/broker path avoids that transport.

Cleanup completed through scoped `sb down` with exit 0: all four verified `sandbox-hssb-release-local-20260-{db,mailpit,nginx,wp}-1` containers were removed. The post-status contained no task-owned containers. Database volume, source copies and evidence files were preserved.

Follow-up review corrected a test-only CI mismatch in commit `25fa890`: the editor fixture no longer requires the exact generator string WordPress 7.1 while CI installs latest WordPress. It accepts a stable WordPress version and retains the real iframe, block API, save/reload and frontend assertions. Syntax, whitespace and 48-test discovery passed. Distributed bytes did not change.
