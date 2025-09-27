### Overview

This is a complete, actionable documentation and community readiness package for the Html Social Share Buttons WordPress plugin. It includes ready-to-drop file templates, WordPress.org asset specifications, a clear changelog strategy, contributor-facing policies and templates, an i18n implementation and extraction plan, and a phased beta test matrix with rollout and acceptance criteria.

---

### README and readme.txt

#### README.md Content
- **Purpose**: landing README for GitHub repository with quick start, usage, contribution pointers, license, and links to WordPress.org listing.
- **Key sections**: Title and badges, Short description, Features, Screenshots link, Installation, Usage examples, Developer notes, Testing and contributing links, Changelog pointer, License.

Sample README.md (replace bracketed values)
```markdown
# Html Social Share Buttons

[![WordPress Version](https://img.shields.io/badge/WordPress-6.x+-blue)](https://wordpress.org)
[![PHP Version](https://img.shields.io/badge/PHP-8.0+-8892BF)](#)
[![License](https://img.shields.io/badge/License-GPLv2%2B-green)](#)

A lightweight, accessible set of HTML social share buttons for WordPress that works without third-party scripts.

## Features
- **Zero third-party scripts**; pure HTML + CSS share buttons
- **ARIA accessible** and keyboard friendly
- Customizable icon style and button placement
- Works with popular page builders and themes

## Installation
1. Upload plugin folder to `/wp-content/plugins/`
2. Activate via Plugins page in WordPress
3. Configure under Settings > Social Share or use shortcode `[html_social_share]`

## Usage
- Shortcode: `[html_social_share style="rounded" networks="facebook,twitter,linkedin"]`
- PHP: `echo do_shortcode( '[html_social_share]' );`

## Developer Notes
- Text domain: `html-social-share-buttons`
- Main plugin file: `html-social-share-buttons.php`
- Translations live in `languages/`

## Contributing
See `CONTRIBUTING.md` and `CODE_OF_CONDUCT.md`.

## Changelog
See `readme.txt` changelog section and `CHANGELOG.md` for detailed history.

## License
GPLv2+.
```

#### readme.txt Content for WordPress.org
- **Purpose**: WordPress.org plugin directory expects readme.txt using the plugin readme standard (stable tag, short/long description, installation, screenshots, changelog).
- **Include**: `== Description ==`, `== Installation ==`, `== Screenshots ==`, `== Changelog ==`, `== Upgrade Notice ==`, `== Frequently Asked Questions ==`.

Sample readme.txt skeleton
```txt
=== Html Social Share Buttons ===
Contributors: yourname
Donate link: https://your.site/donate
Tags: social,share,buttons,share-buttons,html,accessibility
Requires at least: 6.2
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
A lightweight, accessible set of HTML social share buttons for WordPress without external scripts.

== Installation ==
1. Upload plugin to `/wp-content/plugins/`
2. Activate plugin through the 'Plugins' screen in WordPress
3. Configure under Settings > Social Share

== Screenshots ==
1. Overview of settings page
2. Example buttons in block editor
3. Example on frontend with theme X

== Changelog ==
= 1.0.0 =
* Initial release.

== Upgrade Notice ==
= 1.0.0 =
Initial stable release.

== Frequently Asked Questions ==
= How do I change icon style? =
See Settings > Social Share.

```

#### Asset Specifications
- **Icons**
  - **icon-256x256.png** (required): PNG, square, 256×256, transparent background recommended, used in plugin screens.
  - **icon-128x128.png** (optional): PNG, 128×128.
- **Banners**
  - **banner-772x250.png**: PNG or JPG, used as plugin header on WordPress.org.
  - **banner-1544x500.png**: Double-size retina recommended.
  - Use high-contrast text and a simple composition: product name, one-liner, small screenshot or SVG mark.
- **Screenshots**
  - Use PNG or JPG, **minimum 772×250** and maintain consistent width for all screenshots. Export at 72–150 DPI.
  - Provide 4–6 images showing settings, shortcode, block editor integration, frontend on a theme, and a mobile view.
- **Filenames**
  - Use lowercase, hyphen-separated filenames, e.g., `screenshot-1-settings.png`, `icon-256x256.png`, `banner-772x250.png`.

---

### Contributing Guidelines

#### CODE_OF_CONDUCT.md
- **Policy**: Adopt a concise code of conduct that enforces respectful interaction and reporting procedure. Reference Contributor Covenant or a short in-repo version with reporting email and enforcement escalation.
- **Minimal template**
```markdown
# Contributor Code of Conduct

Be respectful, inclusive, and professional. Harassment, hateful language, and discriminatory behavior are not allowed.

Report incidents to: security@your.domain with subject "Code of Conduct Report".

Enforcement: Maintainers will investigate and may remove content or contributors who violate this policy.
```

#### CONTRIBUTING.md
- **Contents**: how to file issues, PR conventions, branching strategy, commit message style, testing expectations, release notes process, how to run local dev environment.
- **Key points**
  - **Branching**: `main` for stable, `develop` for next release, feature branches `feature/name`.
  - **Commits**: Conventional Commits preferred (feat:, fix:, docs:, chore:, test:).
  - **PR checklist**: linted, translatable strings added to POT, unit or integration tests included for new logic.
  - **Code style**: PSR-12 for PHP, ESLint rules for JS, stylelint for CSS.

Sample CONTRIBUTING.md skeleton
```markdown
# Contributing

- Fork and create a branch `feature/short-description`
- Open PR against `develop` with a clear description and screenshots when UI changes
- Include tests for logic changes
- Use Conventional Commits for commit messages
- Ensure all strings are wrapped with translation functions and run `npm run i18n` to update POT

## Testing
- Use the included Docker Compose or Local WP environment
- Run `composer install` and `npm ci` then `npm test`
```

#### ISSUE_TEMPLATEs
- **Bug report**
```markdown
---
name: Bug report
about: Create a report to help us improve
---

**Steps to reproduce**
1.
2.
3.

**Expected behavior**

**Actual behavior**

**Environment**
- WordPress version:
- PHP version:
- Plugin version:
- Theme:
- Page builder:
- Console errors or logs:

**Screenshots or video**
```
- **Feature request**
```markdown
---
name: Feature request
about: Suggest an improvement
---

**Summary**

**Use case**

**Suggested UI or API**
```

#### PULL_REQUEST_TEMPLATE.md
- **Checklist**
```markdown
## Proposed changes
Describe what this PR does.

## Checklist
- [ ] Tests added or updated
- [ ] Linted and formatted
- [ ] POT file updated (`npm run i18n` or `wp i18n make-pot`)
- [ ] Screenshots added for UI changes
- [ ] Documentation updated (README / readme.txt)
```

---

### Internationalization Plan

#### Text Domain Setup
- **Text domain**: `html-social-share-buttons`
- **File pattern**: load translations from `languages/` using `load_plugin_textdomain()` in main plugin file.
- **Use translation functions**:
  - Strings in PHP: `__( 'Text', 'html-social-share-buttons' )`, `_e()`, `esc_html__()`
  - In JS: use wp.i18n `__()` and `wp_localize_script` for dynamic strings

Sample PHP bootstrap
```php
function hssb_load_textdomain() {
    load_plugin_textdomain( 'html-social-share-buttons', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'hssb_load_textdomain' );
```

#### POT File Extraction Configuration
- **Preferred tool**: WP-CLI `i18n` command or the `wp i18n make-pot` from the wp-cli/i18n package.
- **Command example**
```bash
# from project root
wp i18n make-pot . languages/html-social-share-buttons.pot --exclude=node_modules,vendor --domain=html-social-share-buttons
```
- **npm script** (package.json)
```json
"scripts": {
  "i18n": "wp i18n make-pot . languages/html-social-share-buttons.pot --exclude=node_modules,vendor --domain=html-social-share-buttons"
}
```
- **Extraction rules**
  - Ensure all PHP files and JS files using `__()` and `wp.i18n.__()` are scanned.
  - Configure exclusions for build artifacts.

#### POT Maintenance and CI
- Add CI job to fail if POT is out of date. Example step:
  - Extract POT to `languages/` in CI and compare with repo file; fail if differences exist.
- Add `npm run i18n` as pre-commit hook or part of `pre-push`.

#### Initial Translations
- **Baseline languages**: English (source), **Bengali (bn_BD)** and **Spanish (es_ES)** as initial community-friendly translations.
- **PO sample for bn_BD** (languages/bn_BD.po)
```po
msgid ""
msgstr ""
"Project-Id-Version: Html Social Share Buttons 1.0.0\n"
"Language: bn_BD\n"

#: src/settings.php:12
msgid "Save Settings"
msgstr "সেটিং সংরক্ষণ করুন"
```
- Store compiled `.mo` files in `languages/` or rely on WordPress language packs for deployment.

#### Translation Workflow
- Use GlotPress on translate.wordpress.org for official translations after plugin is published.
- For Git-based contributions, accept `.po` updates via PR; CI must compile `.mo` or instruct maintainers to compile before releases.
- Document how contributors should add translations in `CONTRIBUTING.md`.

---

### Beta Testing Plan

#### Test Matrix
- **WordPress versions**
  - Latest stable release
  - Previous major release
  - Latest release candidate during development windows
- **PHP versions**
  - 8.2 (recommended)
  - 8.1
  - 8.0
  - 7.4 (optional legacy)
- **Themes**
  - Twenty Twenty Five or the current default theme
  - GeneratePress
  - Astra
  - OceanWP
  - A heavyweight theme with many hooks (e.g., Neve)
- **Page Builders**
  - Elementor
  - Divi
  - Beaver Builder
  - WPBakery
  - Bricks Builder

Test matrix template (example rows)
- Columns: WP Version; PHP Version; Theme; Page Builder; Priority; Test Cases
- Example entry:
  - **WP 6.x | PHP 8.2 | Twenty theme | None | High**
    - Install, activate, shortcode rendering, accessibility audit, frontend render on desktop and mobile.
  - **WP 6.x | PHP 8.1 | GeneratePress | Elementor | High**
    - Test block insertion, shortcode/native widget behavior, editor preview, dynamic CSS collisions.

#### Test Cases and Acceptance Criteria
- **Install and Activation**
  - Plugin installs, activates, and shows settings page without PHP warnings.
- **Shortcode and Block**
  - Shortcode renders expected markup, buttons include correct share URLs, no inline external scripts.
- **Accessibility**
  - Buttons reachable by keyboard; aria-label attributes present; color contrast passes WCAG AA.
- **Compatibility**
  - No JS errors in console for each builder/theme.
  - Buttons style gracefully degrade with common theme CSS overrides.
- **Performance**
  - No blocking network requests; page load impact measured with Lighthouse (score not degraded by more than 2%).
- **i18n**
  - Strings translate when site locale is changed, POT includes new strings.
- **Upgrade and Rollback**
  - Upgrade from prior version preserves settings; rollback possible without fatal errors.

#### Beta Rollout Strategy for Opt-in Users
- **Opt-in recruitment**
  - Add a sign-up form in plugin README, GitHub, and a lightweight form on your website; collect WP.org username, email, WP and PHP versions, theme, and builder.
  - Invite existing users and contributors and publicize in developer channels (WordPress Slack, Twitter, GitHub).
- **Distribution**
  - Use GitHub Releases and a "beta" tag with downloadable ZIPs for closed phases.
  - Provide installation instructions for manual install or recommend the GitHub Updater plugin for automatic updates from repo.
- **Phased rollout**
  1. **Internal Alpha**: 3–5 maintainers and trusted contributors test core flows and regression tests.
  2. **Closed Beta**: ~20 opt-ins representing diverse environments (theme, builder, PHP). Duration 1 week per cycle.
  3. **Open Beta**: 200+ opt-ins, announced on GitHub and plugin page, duration 2–4 weeks.
  4. **Public Stable**: Gradual WordPress.org release after critical bugs resolved.
- **Feedback loop**
  - Provide a dedicated Slack channel or GitHub Discussions category for beta feedback.
  - Use a simple bug template for beta testers to report environment, reproduction steps, and attach debug logs.
- **Monitoring and Telemetry**
  - Encourage opt-in debug logging for hard-to-reproduce issues by exposing a debug toggle in plugin settings that writes a scoped log file.
  - Track support threads and GitHub labels: `beta`, `regression`, `critical`, `wontfix`.
- **Rollback Plan**
  - Maintain a signed stable release on `main` branch and tag. If a critical issue appears, immediately notify beta users and publish a hotfix release.
  - Document database-safe rollback steps in `UPGRADE.md`.

#### Timeline and Responsibilities
- **Week 0**: Prepare beta build, update README/readme.txt, package banners and screenshots.
- **Week 1**: Internal alpha testing and fix critical issues.
- **Week 2–3**: Closed beta cycle, triage, patching.
- **Week 4–5**: Open beta, finalize translations, create final changelog entries.
- **Week 6**: Publish stable release.

#### Acceptance Criteria to Promote to Stable
- No open `critical` or `blocker` bugs for 72 hours.
- Accessibility checks passed for core flows.
- POT file updated and primary translations present.
- Automated tests passing in CI for supported PHP versions.
- Changelog and upgrade notice prepared in readme.txt.

---

### File Templates and Quick Paste Snippets

- Include the following files in the repository root:
  - README.md (example above)
  - readme.txt (WordPress.org)
  - CHANGELOG.md (human-readable release history)
  - CODE_OF_CONDUCT.md
  - CONTRIBUTING.md
  - .github/ISSUE_TEMPLATE/bug_report.md
  - .github/ISSUE_TEMPLATE/feature_request.md
  - .github/PULL_REQUEST_TEMPLATE.md
  - languages/html-social-share-buttons.pot (generated)
  - languages/*.po and *.mo as needed

Provide these templates verbatim in the repo so contributors can fork, edit, and submit PRs.

---

If you want, I will generate ready-to-commit files for each template (README.md, readme.txt, CODE_OF_CONDUCT.md, CONTRIBUTING.md, ISSUE and PR templates, and a starter POT and two sample .po files for Bengali and Spanish) organized in the repository layout you prefer.