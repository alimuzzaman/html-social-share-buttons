# Release 2.2.4 and Settings Revamp Plan

## Plan 1: Minor Compatibility + Polish Release

### Summary
Prepare a small `2.2.4` release from the current `release` branch. Keep the published plugin backward compatible while confirming forward compatibility with current WordPress/PHP expectations.

### Key Changes
- Keep `Requires PHP: 7.0` and `Requires at least: 5.0` to preserve backward compatibility.
- Add/update release notes to state compatibility tested through WordPress `7.0` and PHP `8.5`.
- Fix the front-end analytics typo `conlole.log(action)` so enabling Google Social analytics does not produce a JavaScript error.
- Improve link safety for share buttons opened with `target="_blank"` by adding `rel="noopener noreferrer"` while preserving existing `nofollow` behavior.
- Make one small admin visual improvement: scope admin button styling to the plugin settings UI so the plugin page does not globally override WordPress admin `.button` styles.

### Test Plan
- Run `php -l` across all top-level plugin PHP files.
- Manually inspect generated share button HTML for correct `rel` output with and without the `nofollow` option.
- Verify the settings page still loads and the "Get PHP Code" / "Get Shortcode" buttons remain usable.
- Confirm plugin metadata/readme show the intended `2.2.4` compatibility release notes.

### Assumptions
- This stays on the released code line, not a rewrite branch.
- PHP minimum remains `7.0`; do not raise it to `7.4` or `8.x`.
- WordPress minimum remains `5.0`.

## Plan 2: Settings Page Presentation-Only Revamp

### Summary
Create a small branch from `release` after the compatibility release. Refresh only the settings page presentation. Keep the released plugin internals, option key `zm_shbt_fld`, form submission through `options.php`, iconset loading, shortcode generation behavior, widget behavior, and front-end rendering unchanged.

### Key Changes
- Base branch: `release`, because it matches the WordPress.org `2.2.3` release package.
- Touch only the legacy settings presentation files: `settings_page.php`, `form.php`, `assets/admin.css`, and optionally `assets/admin.js`.
- Keep all existing input names, IDs, option array structure, nonce/settings registration, and sanitize method unchanged.
- Reorganize the settings page visually into clearer sections: header, icon style, display placement, social networks, advanced options, and code generator.
- Replace broad/global admin CSS selectors with settings-page-scoped selectors so WordPress admin buttons and inputs outside this plugin are not affected.
- Borrow layout ideas from `alim-dev` only as visual reference, not code architecture.

### Test Plan
- Run PHP syntax checks on top-level plugin PHP files.
- Compare before/after form field names to confirm `zm_shbt_fld[...]` keys are unchanged.
- Manually verify the settings page loads, saves through `options.php`, and preserves existing saved options.
- Verify shortcode/PHP code generator still produces the legacy `zm_sh_btn` shortcode/function output.
- Confirm front-end share buttons render unchanged after saving settings.

### Assumptions
- Do not use the React admin work because all existing React branches are part of larger rewrites.
- Do not introduce a new build step, REST settings API, Composer/autoload structure, or renamed options.
- The settings revamp should happen after the `2.2.4` compatibility/polish release.
