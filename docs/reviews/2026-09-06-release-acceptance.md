# 3.2.0 release acceptance

Status: **candidate built; release blocked by hosted CI and authenticated admin-browser gates**. No tag, merge, deployment or publication is authorized by this record.

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

The actual installed matrix used the retained Default pack. Earlier six-pack coverage is a static stylesheet fixture and is not presented as six-pack WordPress runtime evidence. These 48 public cases are separate from the 48 configured release-browser tests, which include authenticated settings and editor flows and have not executed. No real-device/Safari application certification is claimed.

Root visually reviewed representative current-fixture desktop/mobile screenshots across Legacy, Minimal, Framed and Soft shadow. Buttons, content and rails were contained without clipping or overlap. Screenshots show fully revealed rails with reduced motion. Evidence and screenshots are retained under `dist/release-candidate-3.2.0-1fd860d/public-browser/`; root independently checked all eight JSON files, 48 cases, zero failed checks and the correct fixture URL.

FPM initially retained the old source-symlink path after installation, causing public asset 404s while a new CLI process saw the canonical path. A supported restart of the isolated services cleared the stale paths; the final public checks used canonical URLs returning 200. No plugin source change was required for that environment issue.

## Browser broker blocker

Sandbox refused its generated credential source as unsafe. It then failed supported broker creation of a fresh disposable credential with `operation_failed`. No refused secret source was read or permission-changed, and no credential bypass was attempted. The browser login path requires operator repair or a separately authorized supported test authentication route before browser acceptance can complete.

## Remaining acceptance

Authenticated settings/preview/editor browser execution, the hosted support matrix and failure probe, and review of the eventual hosted artifact remain pending. Historical 3.0/3.1 evidence does not certify this candidate. The elapsed staging-soak waiver remains in effect.

## Final local state and handoff

After public checks, the original settings hash, rollback fixture content/meta hashes and the final 242-file installed manifest were reverified unchanged. The candidate ZIP and sanitized local runtime evidence remain in `dist/release-candidate-3.2.0-1fd860d/`. This ZIP is not a hosted CI artifact and must not be substituted silently for a later hosted candidate.

GitHub rejected `git push -u origin codex/plugin-update-preview` because its OAuth credential lacks `workflow` scope. Operator action: `gh auth refresh -h github.com -s workflow`, then retry the branch push through the authorized GitHub login. No remote branch update, hosted workflow, failure probe, tag, merge or publication succeeded in this task. The supported broker also needs repair before authenticated browser tests.

Sanitized Sandbox feedback: `b9e1da6c97dac6461e4ac114caac21d7` (broker creation), `2d93523470bceb6befe1ab2c78cbf33d` (ZIP staging), `d8819ca4e480822ec7564a2db5bd8927` (upgrader diagnostics). The latter install issues were resolved for this disposable test site with the scoped procedure above; the broker issue remains open.

Cleanup completed through scoped `sb down` with exit 0: all four verified `sandbox-hssb-release-local-20260-{db,mailpit,nginx,wp}-1` containers were removed. The post-status contained no task-owned containers. Database volume, source copies and evidence files were preserved.
