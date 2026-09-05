# Plugin Check warning disposition — 2026-09-06

Static review found no confirmed security vulnerability or release regression in the supplied warnings. The prefix diagnostic is a scanner-inferred hook prefix applied to PHP identifier rules; three icon-set shape warnings merit a bounded robustness follow-up. This is not a blanket baseline waiver or a clean security scan.

## Evidence and scope

- Candidate: HTML Social Share Buttons 3.2.0; Plugin Check 2.1.0; supplied result **0 errors, 63 warnings**. The sanitized result contains 63 WARNING entries; the zero-error runtime result is supplied by the parent task, not rerun here.
- Source inspected: `1fd860d4ef8acde3e19aabf603a60a8aca7362aa` in `/Users/alim/Sites/git/html-social-share-buttons`.
- Tested archive SHA-256 supplied by parent: `7249267b6bd98b331347b437a2f6f9058a879f58374f82a0b1643a7a261f74bf`. Parent reports PHP/source parity with this checkout; VC module identifiers differ. This review did not independently hash or extract that archive.
- Input: `/tmp/hssb-release-local-20260906-01/plugin-check-sanitized.json`; only type, code and location are available. Initial scanner prose was absent. A later bounded installed-scanner inspection supplied the exact inferred prefix `hssb/share` and its WPCS message.
- No applicable SECURITY.md was found by repository inventory and the policy resolver for `src`. Boundary evidence comes from endpoint registration, capability/nonce checks, the plugin header and compatibility decisions.
- Static reads only. No runtime, HTTP requests, tests, builds, credentials or production edits. Only this report was written.

## Category disposition

| Warning code | Count | Disposition and evidence |
| --- | ---: | --- |
| `missing_composer_json_file` | 1 | Intentional packaging choice: `.distignore:43-44` excludes Composer manifests; `html-social-share.php` requires the packaged autoloader and fails activation if it is absent. Missing development/build metadata is real, but is not itself a runtime dependency failure. Keep the reproducible source/manifests available to reviewers. |
| `DiscouragedFunctions.load_plugin_textdomainFound` | 1 | Retain for the declared WP 5.3 floor and local/legacy catalogs. `TranslationLoader.php:25` resolves the bundled language directory; no remote catalog fetch or user-selected path. Modern WordPress guidance alone does not establish a regression in this compatibility loader. |
| `NonceVerification.Recommended` | 3 | `CurrentPostPermalink.php:26,30`: scalar request IDs are unslashed and reduced with `absint`, then used only for `get_permalink` and URL escaping. This read-only AJAX-context fallback does not authorize a write. No missing-nonce state-changing path was established. This does not establish authorization safety for arbitrary third-party callers or confidentiality of all private permalink metadata. |
| `NonceVerification.Missing` | 12 | Eight occurrences in `SettingsAjaxController.php:48,61,79,89,99` call `verifySettingsRequest` or `verifyIconSetRequest` first; both check the settings nonce and `manage_options` and terminate on failure. Four in `SettingsPageController.php:60-62` select a sanitation policy using exact form identifiers; they do not grant write permission. The real form uses WordPress `options.php`, whose nonce/capability checks precede `update_option`. Programmatic callers retain responsibility for their own write authority. |
| `ValidatedSanitizedInput.InputNotSanitized` | 3 | One serialized settings input is deliberately unslashed before `parse_str`, then mapped and sanitized field by field by `persist`; sanitizing the serialized envelope as plain text would damage form data. Preview's two inputs are string-checked first; the service rejects unknown network IDs and templates over 8192 bytes, then uses shared token-preserving textarea sanitation. Output is JSON text with final URL escaping. No unsanitized HTML or fetched URL sink was found in these paths. |
| `ValidatedSanitizedInput.InputNotValidated` | 3 | Presence checks exist in `verifyIconSetRequest`, followed by `sanitize_key` and registry lookup. This is not absent validation or an auth bypass. However the helper does not explicitly require a string: follow up with array-input rejection tests across the WP/PHP support floor before asserting uniform malformed-input behavior. No security boundary crossing established; administrator plus valid nonce required. |
| `PrefixAllGlobals.InvalidPrefixPassed` | 1 | Scanner configuration mismatch, not a PHP declaration defect. The parent runtime task's bounded `Prefix_Scanner` inspection identified `hssb/share`. Installed `Prefix_Utils.php:41-47` collects inferred prefixes; `Prefixing_Check.php:56-61` passes them as PHPCS `runtime-set prefixes`. `PrefixAllGlobalsSniff.php:1268-1275` rejects `/` under PHP identifier rules and emits at token offset 0, explaining `html-social-share.php:1:1`. This plugin intentionally defines `hssb/share_*` WordPress hooks in `ExtensionHooks.php:12-15`; slash is not being used in a PHP declaration. Retain those public hooks. |
| `PrefixAllGlobals.NonPrefixedVariableFound` | 3 | `LegacyApiRegistrar.php:43,70,76` writes the deliberate historical `zm_sh_default_options`, `zm_sh_iconset_classes`, and `zm_sh` globals. Names are fixed in code, not request-selected. Retained compatibility surface, not untrusted global-name injection. |
| `PrefixAllGlobals.VariableConstantNameFound` | 1 | `LegacyConstants.php:46` defines an alias name passed only by the fixed `zm_sh_*` calls in the same class. The dynamic expression is real; attacker-selected constant names are not established. |
| `PrefixAllGlobals.NonPrefixedFunctionFound` | 22 | `globals.php:22-171` contains the guarded `zm_sh_*` compatibility delegates and `wp_ajax_get_iconset_details`. The latter delegates to the guarded canonical admin handler. Existing integration names are intentionally retained; changing them to silence naming checks would break callers. |
| `PrefixAllGlobals.NonPrefixedHooknameFound` | 6 | `LegacyHooks.php:102-142` invokes fixed historical `zm_sh_*` hooks. These are code-level extension interfaces; incoming request text does not select hook names. Retain the compatibility bridge. |
| `PrefixAllGlobals.DynamicHooknameFound` | 7 | `ExtensionHooks.php:19-54` uses constants declared in the same class, all with fixed `hssb/` names. This is static indirection, not request-controlled hook dispatch. |

Confidence is high for directly observed helper checks, sanitation and fixed naming; medium for compatibility/package dispositions and the read-only permalink scope. No confirmed or unresolved exploitability queue remains. Icon-set scalar rejection is a separate low-risk robustness recommendation, not an invented security finding.

## Exact warning ledger

Input-order IDs below preserve every supplied occurrence, including repeated locations. `N` means not actionable as the supplied security/regression claim, with category rationale above. No source warning was dropped.

| Input | Code suffix | Location | Verdict |
| --- | --- | --- | --- |
| 01 | `missing_composer_json_file` | `composer.json:0:0` | N |
| 02 | `load_plugin_textdomainFound` | `src/Infrastructure/WordPress/Translation/TranslationLoader.php:25:10` | N |
| 03 | `Recommended` | `src/Infrastructure/WordPress/Rendering/CurrentPostPermalink.php:26:19` | N |
| 04 | `Recommended` | `src/Infrastructure/WordPress/Rendering/CurrentPostPermalink.php:26:62` | N |
| 05 | `Recommended` | `src/Infrastructure/WordPress/Rendering/CurrentPostPermalink.php:30:35` | N |
| 06 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:48:19` | N |
| 07 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:48:72` | N |
| 08 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:61:24` | N |
| 09 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:61:59` | N |
| 10 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:61:94` | N |
| 11 | `InputNotSanitized` | `src/Presentation/Admin/SettingsAjaxController.php:61:94` | N |
| 12 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:79:35` | N |
| 13 | `InputNotValidated` | `src/Presentation/Admin/SettingsAjaxController.php:79:35` | N |
| 14 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:89:35` | N |
| 15 | `InputNotValidated` | `src/Presentation/Admin/SettingsAjaxController.php:89:35` | N |
| 16 | `Missing` | `src/Presentation/Admin/SettingsAjaxController.php:99:35` | N |
| 17 | `InputNotValidated` | `src/Presentation/Admin/SettingsAjaxController.php:99:35` | N |
| 18 | `InputNotSanitized` | `src/Presentation/Admin/ShareTemplatePreviewController.php:30:51` | N |
| 19 | `InputNotSanitized` | `src/Presentation/Admin/ShareTemplatePreviewController.php:30:84` | N |
| 20 | `Missing` | `src/Presentation/Admin/SettingsPageController.php:60:11` | N |
| 21 | `Missing` | `src/Presentation/Admin/SettingsPageController.php:60:34` | N |
| 22 | `Missing` | `src/Presentation/Admin/SettingsPageController.php:61:39` | N |
| 23 | `Missing` | `src/Presentation/Admin/SettingsPageController.php:62:17` | N |
| 24 | `InvalidPrefixPassed` | `html-social-share.php:1:1` | N |
| 25 | `NonPrefixedVariableFound` | `src/Compatibility/Legacy/Api/LegacyApiRegistrar.php:43:22` | N |
| 26 | `NonPrefixedVariableFound` | `src/Compatibility/Legacy/Api/LegacyApiRegistrar.php:70:22` | N |
| 27 | `NonPrefixedVariableFound` | `src/Compatibility/Legacy/Api/LegacyApiRegistrar.php:76:22` | N |
| 28 | `VariableConstantNameFound` | `src/Compatibility/Legacy/Api/LegacyConstants.php:46:21` | N |
| 29 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:22:5` | N |
| 30 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:37:5` | N |
| 31 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:43:5` | N |
| 32 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:49:5` | N |
| 33 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:55:5` | N |
| 34 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:61:5` | N |
| 35 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:67:5` | N |
| 36 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:73:5` | N |
| 37 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:79:5` | N |
| 38 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:85:5` | N |
| 39 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:91:5` | N |
| 40 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:97:5` | N |
| 41 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:105:5` | N |
| 42 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:113:5` | N |
| 43 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:126:5` | N |
| 44 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:132:5` | N |
| 45 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:138:5` | N |
| 46 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:147:5` | N |
| 47 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:153:5` | N |
| 48 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:159:5` | N |
| 49 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:165:5` | N |
| 50 | `NonPrefixedFunctionFound` | `src/Compatibility/Legacy/Api/globals.php:171:5` | N |
| 51 | `NonPrefixedHooknameFound` | `src/Compatibility/Legacy/Api/LegacyHooks.php:102:39` | N |
| 52 | `NonPrefixedHooknameFound` | `src/Compatibility/Legacy/Api/LegacyHooks.php:112:39` | N |
| 53 | `NonPrefixedHooknameFound` | `src/Compatibility/Legacy/Api/LegacyHooks.php:122:39` | N |
| 54 | `NonPrefixedHooknameFound` | `src/Compatibility/Legacy/Api/LegacyHooks.php:132:39` | N |
| 55 | `NonPrefixedHooknameFound` | `src/Compatibility/Legacy/Api/LegacyHooks.php:138:20` | N |
| 56 | `NonPrefixedHooknameFound` | `src/Compatibility/Legacy/Api/LegacyHooks.php:142:20` | N |
| 57 | `DynamicHooknameFound` | `src/Infrastructure/WordPress/Extension/ExtensionHooks.php:19:36` | N |
| 58 | `DynamicHooknameFound` | `src/Infrastructure/WordPress/Extension/ExtensionHooks.php:25:36` | N |
| 59 | `DynamicHooknameFound` | `src/Infrastructure/WordPress/Extension/ExtensionHooks.php:31:36` | N |
| 60 | `DynamicHooknameFound` | `src/Infrastructure/WordPress/Extension/ExtensionHooks.php:38:13` | N |
| 61 | `DynamicHooknameFound` | `src/Infrastructure/WordPress/Extension/ExtensionHooks.php:46:31` | N |
| 62 | `DynamicHooknameFound` | `src/Infrastructure/WordPress/Extension/ExtensionHooks.php:50:31` | N |
| 63 | `DynamicHooknameFound` | `src/Infrastructure/WordPress/Extension/ExtensionHooks.php:54:36` | N |

## Paths examined

All 12 paths named by the supplied warning list were inspected (the absent packaged `composer.json` was assessed against checkout `composer.json` and `.distignore`). Supporting reads: `src/Presentation/Admin/IconSetPayloadBuilder.php`, `src/Presentation/Admin/ShareTemplatePreview.php`, `src/Infrastructure/WordPress/Settings/ShareTemplateSanitizer.php`, `SettingsRequestSanitizer.php`, `OptionSettingsRequestMapper.php`, `src/Infrastructure/WordPress/Rendering/ShareContextFactory.php`, and `docs/REWRITE-COMPATIBILITY-DECISIONS.md`. The local WordPress file `/tmp/hssb-runtime-20260905-01/sandbox-home/runtime/wp-project/wp-admin/options.php` was read at its capability, nonce and update sites; it was not executed. Archive identity and other runtime acceptance remain the parent task's separate evidence.

Additional scanner-source reads were confined to `/tmp/hssb-runtime-20260905-01/sandbox-home/runtime/wp-project/wp-content/plugins/plugin-check/`: `includes/Traits/Prefix_Utils.php`, `includes/Checker/Checks/Plugin_Repo/Prefixing_Check.php`, `includes/Scanner/Prefix_Scanner.php`, and `vendor/wp-coding-standards/wpcs/WordPress/Sniffs/NamingConventions/PrefixAllGlobalsSniff.php`. The exact inferred prefix comes from the parent runtime task; the source review independently corroborates the diagnostic mechanism. No complete scanner rerun was performed for this follow-up.
