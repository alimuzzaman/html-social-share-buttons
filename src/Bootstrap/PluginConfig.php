<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Bootstrap;

/**
 * Immutable canonical names for WordPress persistence and public surfaces.
 *
 * These identifiers intentionally retain their historical values.  New
 * controllers receive this object instead of duplicating literals or asking a
 * legacy bootstrap for plugin state.
 */
final class PluginConfig {
	const VERSION = '3.1.0';
	const OPTION_NAME = 'zm_shbt_fld';
	const SHORTCODE = 'zm_sh_btn';
	const SHORTCODE_ALIAS = 'html-social-share-buttons';
	const SHARE_BLOCK = 'html-social-share/social-share';
	const SOCIAL_LINKS_BLOCK = 'html-social-share/social-links';
	const DISABLE_META_KEY = '_zm_sh_disable_share';
	const SETTINGS_PAGE = 'zm_shbt_opt';
	const SETTINGS_GROUP = 'zm_shbt_opt';
	const TEXT_DOMAIN = 'html-social-share-buttons';
	const LEGACY_TEXT_DOMAIN = 'zm-sh';
	const FRONTEND_STYLE_HANDLE_PREFIX = 'social-share-';
	const DEFAULT_STYLE_HANDLE = 'social-share-default';
	const BUTTON_APPEARANCE_STYLE_HANDLE = 'hssb-button-appearance';
	const SHARE_BLOCK_EDITOR_HANDLE = 'zm-sh-social-share-block';
	const SOCIAL_LINKS_BLOCK_EDITOR_HANDLE = 'zm-sh-social-links-block';
	const ADMIN_NONCE_ACTION = 'zm_sh_admin';
	const SETTINGS_AJAX_SAVE = 'zm_sh_save_settings';
	const SETTINGS_AJAX_SEARCH = 'zm_sh_search_content';
	const ICON_SET_AJAX_GET = 'get_iconset';
	const ICON_SET_AJAX_PREVIEW = 'get_iconset_preview';
	const ICON_SET_AJAX_DETAILS = 'get_iconset_details';
	const METABOX_ID = 'zm_sh_metabox';
	const METABOX_NONCE_ACTION = 'zm_sh_metabox';
	const METABOX_NONCE_FIELD = 'zm_sh_mtbox';
	const ADMIN_SETTINGS_STYLE_HANDLE = 'zm_sh_admin_styles';
	const ADMIN_WIDGET_STYLE_HANDLE = 'zm_sh_admin_styles_scripts';
	const ADMIN_SETTINGS_SCRIPT_HANDLE = 'zm_sh_admin_scripts';
	const ADMIN_WPBAKERY_SCRIPT_HANDLE = 'zm_sh_vc_admin_scripts';
	const ADMIN_ELEMENTOR_SCRIPT_HANDLE = 'hssb-elementor-editor';
	const ADMIN_SETTINGS_OBJECT = 'zm_sh_react_settings';
	const ADMIN_WPBAKERY_OBJECT = 'zm_sh';
	const WIDGET_ID_BASE = 'html_share_button_widget';
	const ELEMENTOR_WIDGET_NAME = 'zm_social_share';
	const WPBAKERY_BASE = 'zm_sh_btn';
	const WPBAKERY_CLASS = 'zm_sh_btn';
	const WIDGET_HOOK = 'widgets_init';
	const ELEMENTOR_HOOK = 'elementor/widgets/register';
	const WPBAKERY_HOOK = 'vc_before_init';
	const WIDGET_WRAPPER_CLASS = 'in_widget';
	const ELEMENTOR_WRAPPER_CLASS = 'in_elementor';
	const WPBAKERY_WRAPPER_CLASS = 'in_shortcode';

	private $paths;

	public function __construct( PluginPaths $paths ) {
		$this->paths = $paths;
	}

	public function paths() {
		return $this->paths;
	}

	public function optionName() {
		return self::OPTION_NAME;
	}

	public function version() {
		return self::VERSION;
	}

	public function shortcode() {
		return self::SHORTCODE;
	}

	/**
	 * Both names are public content contracts. Keep the historical short form
	 * first so existing shortcode_atts filters retain their established tag.
	 */
	public function shortcodeAliases() {
		return array( self::SHORTCODE, self::SHORTCODE_ALIAS );
	}

	public function shareBlockName() {
		return self::SHARE_BLOCK;
	}

	public function socialLinksBlockName() {
		return self::SOCIAL_LINKS_BLOCK;
	}

	public function disabledMetaKey() {
		return self::DISABLE_META_KEY;
	}

	public function settingsPage() {
		return self::SETTINGS_PAGE;
	}

	public function settingsGroup() {
		return self::SETTINGS_GROUP;
	}

	public function settingsScreenHook() {
		return 'settings_page_' . $this->settingsPage();
	}

	public function textDomain() {
		return self::TEXT_DOMAIN;
	}

	public function legacyTextDomain() {
		return self::LEGACY_TEXT_DOMAIN;
	}

	public function frontendStyleHandle( $iconSetId ) {
		return self::FRONTEND_STYLE_HANDLE_PREFIX . sanitize_key( (string) $iconSetId );
	}

	public function defaultStyleHandle() {
		return self::DEFAULT_STYLE_HANDLE;
	}

	public function buttonAppearanceStyleHandle() {
		return self::BUTTON_APPEARANCE_STYLE_HANDLE;
	}

	public function buttonAppearanceStyleUrl() {
		return $this->paths->assetsUrl() . 'frontend/button-appearance.css';
	}

	public function shareBlockEditorHandle() {
		return self::SHARE_BLOCK_EDITOR_HANDLE;
	}

	public function socialLinksBlockEditorHandle() {
		return self::SOCIAL_LINKS_BLOCK_EDITOR_HANDLE;
	}

	public function adminNonceAction() {
		return self::ADMIN_NONCE_ACTION;
	}

	public function settingsAjaxSaveAction() {
		return self::SETTINGS_AJAX_SAVE;
	}

	public function settingsAjaxSearchAction() {
		return self::SETTINGS_AJAX_SEARCH;
	}

	public function iconSetAjaxGetAction() {
		return self::ICON_SET_AJAX_GET;
	}

	public function iconSetAjaxPreviewAction() {
		return self::ICON_SET_AJAX_PREVIEW;
	}

	public function iconSetAjaxDetailsAction() {
		return self::ICON_SET_AJAX_DETAILS;
	}

	public function metaboxId() {
		return self::METABOX_ID;
	}

	public function metaboxNonceAction() {
		return self::METABOX_NONCE_ACTION;
	}

	public function metaboxNonceField() {
		return self::METABOX_NONCE_FIELD;
	}

	public function adminSettingsStyleHandle() {
		return self::ADMIN_SETTINGS_STYLE_HANDLE;
	}

	public function adminWidgetStyleHandle() {
		return self::ADMIN_WIDGET_STYLE_HANDLE;
	}

	public function adminSettingsScriptHandle() {
		return self::ADMIN_SETTINGS_SCRIPT_HANDLE;
	}

	public function adminWpBakeryScriptHandle() {
		return self::ADMIN_WPBAKERY_SCRIPT_HANDLE;
	}

	public function adminElementorScriptHandle() {
		return self::ADMIN_ELEMENTOR_SCRIPT_HANDLE;
	}

	public function adminSettingsObject() {
		return self::ADMIN_SETTINGS_OBJECT;
	}

	public function adminWpBakeryObject() {
		return self::ADMIN_WPBAKERY_OBJECT;
	}

	public function widgetIdBase() {
		return self::WIDGET_ID_BASE;
	}

	public function elementorWidgetName() {
		return self::ELEMENTOR_WIDGET_NAME;
	}

	public function wpBakeryBase() {
		return self::WPBAKERY_BASE;
	}

	public function wpBakeryClass() {
		return self::WPBAKERY_CLASS;
	}

	public function widgetHook() {
		return self::WIDGET_HOOK;
	}

	public function elementorHook() {
		return self::ELEMENTOR_HOOK;
	}

	public function wpBakeryHook() {
		return self::WPBAKERY_HOOK;
	}

	public function widgetWrapperClass() {
		return self::WIDGET_WRAPPER_CLASS;
	}

	public function elementorWrapperClass() {
		return self::ELEMENTOR_WRAPPER_CLASS;
	}

	public function wpBakeryWrapperClass() {
		return self::WPBAKERY_WRAPPER_CLASS;
	}
}
