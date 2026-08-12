# Legacy compatibility inventory

This inventory records the retained 2.2.6 public surface after the canonical
rewrite. Every legacy definition and bridge belongs under
`src/Compatibility/Legacy`. Release evidence and remaining scope limits are
tracked separately in `RELEASE-CANDIDATE-VALIDATION.md`.

## Constants and globals

| Legacy surface | New owner | Compatibility behavior |
|---|---|---|
| `zm_sh_dir` | bootstrap paths | Define as an alias of the new plugin directory constant |
| `zm_sh_url` | bootstrap URLs | Define as an alias of the new plugin URL constant |
| `zm_sh_url_iconset` | icon-set asset resolver | Preserve trailing slash |
| `zm_sh_url_assets` | asset manager | Preserve trailing slash |
| `zm_sh_url_assets_img` | asset manager | Preserve trailing slash |
| `$zm_sh` | plugin/render facade | Expose a legacy facade after the new container boots |
| `$zm_sh_default_options` | settings schema | Expose an exact legacy-shaped default array |
| `$zm_sh_iconset_classes` | legacy icon-set adapter | Preserve add-on registration input; do not use in new registry |
| `$dir_iconset` | built-in manifest loader | Expose only if external usage is confirmed |

## Functions

| Legacy function | New service or adapter |
|---|---|
| `zm_sh_btn()` | legacy render facade → `RenderShareButtons` |
| `zm_sh_shortcode_cb()` | shortcode adapter |
| `zm_sh_get_builder_iconset()` | integration icon-set resolver |
| `zm_sh_get_builder_iconset_options()` | icon-set registry presenter |
| `zm_sh_get_builder_iconset_assets()` | icon-set asset presenter |
| `zm_sh_render_block()` | block adapter |
| `zm_sh_register_block()` | block registrar |
| `zm_sh_register_widgets()` | widget registrar |
| `zm_sh_register_elementor_widget()` | Elementor registrar |
| `zm_sh_integrateWithVC()` | WPBakery registrar |
| `zm_sh_metabox_new()` | metabox registrar |
| `zm_sh_curentPageURL()` | legacy alias, including misspelling, → URL context |
| `zm_sh_get_excluded_post_identifiers()` | exclusion parser |
| `zm_sh_post_is_excluded()` | exclusion policy |
| `zm_sh_get_default_share_templates()` | network registry |
| `zm_sh_get_share_templates()` | template repository/resolver |
| `zm_sh_get_share_template()` | template resolver |
| `zm_sh_get_schema()` | legacy schema registry facade |
| `zm_sh_get_schemas()` | legacy schema registry facade |
| `zm_sh_add_schema()` | legacy schema registry facade |
| `zm_sh_remove_schema()` | legacy schema registry facade |
| `wp_ajax_get_iconset_details()` | legacy AJAX adapter |

## Classes and interfaces

| Legacy symbol | New owner or wrapper |
|---|---|
| `interface_iconset` | legacy icon-set contract |
| `__iconset_parent_class` | legacy icon-set adapter base |
| `zm_sh_iconset` | legacy facade over `IconSetRegistry` |
| `zm_sh_iconset_default` | built-in manifest compatibility wrapper |
| `zm_sh_iconset_flat` | built-in manifest compatibility wrapper |
| `zm_sh_iconset_long_shadows` | built-in manifest compatibility wrapper |
| `zm_sh_iconset_prajin` | built-in manifest compatibility wrapper |
| `zm_social_share` | legacy facade over rendering, assets, and placement |
| `zm_sh_filters` | legacy hook bridge |
| `zm_sh_settings` | legacy settings/controller facade |
| `zm_sh_metabox` | legacy metabox facade |
| `zm_html_share_widget` | widget compatibility class; class name and widget ID remain stable |
| `zm_form` | compatibility-only admin form helper if external use is confirmed |
| `zm_sh_schema` | legacy schema registry facade |
| `ZM_SH_Elementor_Share_Widget` | Elementor compatibility class |

Public methods, properties, and magic properties are frozen in
`tests/fixtures/legacy-public-api-baseline.json` and enforced by
`LegacyPublicApiContractTest`. High-risk members include:

- `zm_social_share::$options`, `$iconset`, `$iconsets`, and `$excluded`;
- `zm_social_share::zm_sh_btn()`, `footer()`, `filter_the_content()`,
  `register_styles()`, and `icon_styles()`;
- `zm_sh_iconset::get_iconset()`, `get_current_iconset()`,
  `get_iconsets()`, `get_iconset_list()`, `add_iconset()`, and
  `remove_iconset()`;
- all public icon-set definition properties such as `id`, `name`, `types`,
  `icons`, `url`, `stylesheet`, and preview paths;
- public `zm_form` methods used by custom widget/admin extensions.

## Storage and identifiers

| Surface | Current value |
|---|---|
| Main option | `zm_shbt_fld` |
| Settings group | `zm_shbt_opt` |
| Settings page slug | `zm_shbt_opt` |
| Settings parent | `options-general.php` |
| Disable-share post meta | `_zm_sh_disable_share` |
| Metabox ID | `zm_sh_metabox` |
| Metabox nonce action | `zm_sh_metabox` |
| Metabox nonce field | `zm_sh_mtbox` |
| Shortcode | `zm_sh_btn` |
| Widget class | `zm_html_share_widget` |
| Widget ID | `html_share_button_widget` |
| Block name | `html-social-share/social-share` |
| Elementor widget name | `zm_social_share` |
| WPBakery base | `zm_sh_btn` |
| Canonical text domain | `html-social-share-buttons` |
| Legacy text domain | `zm-sh` |

Other persisted surfaces requiring upgrade fixtures:

- widget option records created by WordPress;
- serialized block attributes in post content;
- Elementor document settings;
- WPBakery shortcode strings;
- site-local translation files for both text domains.

## Custom hooks

| Legacy hook | Type | Arguments/behavior |
|---|---|---|
| `zm_sh_add_iconset` | action | Add-on registration; currently fired during registry construction |
| `zm_sh_add_schema` | action | Schema registration |
| `zm_sh_placeholder` | filter | Complete share URL after template selection |
| `zm_sh_ico_link` | filter | Registered but currently has no meaningful core call site |
| `zm_sh_title` | filter | Resolved share title |
| `zm_sh_share_templates` | filter | Complete network-template map |
| `zm_sh_share_template` | filter | One resolved template; receives template, platform, fallback |

Hook migration preserves timing, priority, accepted argument count, and return
shape. Canonical share hooks bridge to their legacy equivalents exactly once
through `LegacyExtensionHookBridge`, which has a recursion guard.

## WordPress hooks and AJAX actions

Compatibility-sensitive registrations include:

- `init` priorities 1 and 2 plus default-priority settings/block registration;
- `wp`, `wp_footer`, and `the_content`;
- `widgets_init`;
- `vc_before_init`;
- `elementor/widgets/register`;
- `load-post.php`, `load-post-new.php`, `add_meta_boxes`, and `save_post`;
- `admin_menu`, `admin_init`, and `admin_enqueue_scripts` at priority 20;
- `gettext` with three arguments;
- `plugin_action_links_{plugin-basename}`;
- `wp_ajax_get_iconset`;
- `wp_ajax_get_iconset_preview`;
- `wp_ajax_get_iconset_details`;
- `wp_ajax_zm_sh_save_settings`;
- `wp_ajax_zm_sh_search_content`.

All current AJAX actions use nonce action `zm_sh_admin`; administrative
mutations and icon-set reads require `manage_options`. Response payloads,
status codes, nonce/capability failures, persistence, and historical plain-text
versus JSON behavior are covered by the isolated AJAX suite.

## Asset and JavaScript identifiers

| Surface | Current value |
|---|---|
| Block script | `zm-sh-social-share-block` |
| Admin script | `zm_sh_admin_scripts` |
| Admin stylesheet | `zm_sh_admin_styles` |
| Widget admin stylesheet | `zm_sh_admin_styles_scripts` |
| WPBakery admin script | `zm_sh_vc_admin_scripts` |
| Block localized object | `zmShBlock` |
| Settings localized object | `zm_sh_react_settings` |
| WPBakery localized object | `zm_sh` |
| Frontend icon styles | `social-share-{iconset}` |
| Default frontend style | `social-share-default` |

Frontend DOM/API identifiers that must remain covered include:

- wrapper class `zmshbt`;
- placement classes `left`, `right`, `in_widget`, `in_shortcode`,
  `in_block`, and `in_elementor`;
- network classes including historical `twitter` for X;
- settings form ID `zm-social-share-settings`;
- React root `zmsh-react-settings-root`;
- code dialog ID `zm-sh-thick-box`.

## Canonical hook mapping

| New hook | Legacy bridge |
|---|---|
| `hssb/networks` | no direct old equivalent |
| `hssb/icon_sets` | adapt registrations from `zm_sh_add_iconset` |
| `hssb/share_templates` | bridge through `zm_sh_share_templates` |
| `hssb/share_template` | bridge through `zm_sh_share_template` |
| `hssb/share_title` | bridge through `zm_sh_title` |
| `hssb/share_url` | bridge through `zm_sh_placeholder` |
| `hssb/settings_schema` | typed canonical schema filter; `zm_sh_add_schema` remains compatibility-only because its arbitrary admin-schema arrays do not map safely to the typed runtime schema |

## External compatibility review and evidence limits

- Search WordPress.org support, GitHub, Packagist/code search, and known client
  sites for external calls to legacy symbols.
- Confirm whether `zm_sh_ico_link`, schema APIs, `$dir_iconset`, and `zm_form`
  are used by third parties.
- Keep the real Elementor editor/public fixture current after integration
  changes. When the paid WPBakery editor is unavailable, the release owner has
  accepted its official `vc_map()`/shortcode documentation plus exact mapping,
  persistence, compiled-bundle, and public-render contracts. Do not convert
  that accepted scope into a claim that the paid editor ran.

Completed repository evidence includes hook registration/render order, all
five AJAX success/failure surfaces, widget/block/Elementor/WPBakery storage,
translations, frontend assets, multisite settings, and the machine-readable
legacy API disappearance test.
