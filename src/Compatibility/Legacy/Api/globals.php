<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Historical global symbols.
 *
 * This is the sole file a canonical bootstrap needs to require for old public
 * names. Every callable below delegates into a canonical Plugin injected by
 * LegacyApi; it must never create services, register operational hooks, or
 * generate frontend markup.
 */

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyElementorWidgetFactory;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyHooks;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacySchemaRegistry;

if ( ! function_exists( 'zm_sh_btn' ) ) {
	function zm_sh_btn( $options ) {
		if ( ! is_array( $options ) ) {
			return '';
		}
		if ( isset( $options['icons'] ) && is_array( $options['icons'] ) ) {
			$icons = $options['icons'];
			$isList = ! empty( $icons ) && array_keys( $icons ) === range( 0, count( $icons ) - 1 );
			$options['icons'] = $isList ? array_fill_keys( $icons, 'on' ) : $icons;
		}

		return LegacyApi::render( $options );
	}
}

if ( ! function_exists( 'zm_sh_shortcode_cb' ) ) {
	function zm_sh_shortcode_cb( $attributes ) {
		return LegacyApi::delegate( 'shortcode', 'render', array( $attributes ), '' );
	}
}

if ( ! function_exists( 'zm_sh_get_builder_iconset' ) ) {
	function zm_sh_get_builder_iconset( $iconSet = 'inherit' ) {
		return LegacyApi::delegate( 'shortcode', 'resolveBuilderIconSet', array( $iconSet ), 'default' );
	}
}

if ( ! function_exists( 'zm_sh_get_builder_iconset_options' ) ) {
	function zm_sh_get_builder_iconset_options() {
		return LegacyApi::delegate( 'shortcode', 'builderIconSetOptions', array(), array() );
	}
}

if ( ! function_exists( 'zm_sh_get_builder_iconset_assets' ) ) {
	function zm_sh_get_builder_iconset_assets() {
		return LegacyApi::delegate( 'block', 'builderIconSetAssets', array(), array() );
	}
}

if ( ! function_exists( 'zm_sh_render_block' ) ) {
	function zm_sh_render_block( $attributes ) {
		return LegacyApi::delegate( 'block', 'render', array( $attributes ), '' );
	}
}

if ( ! function_exists( 'zm_sh_register_block' ) ) {
	function zm_sh_register_block() {
		return LegacyApi::delegate( 'block', 'registerBlocks' );
	}
}

if ( ! function_exists( 'zm_sh_register_widgets' ) ) {
	function zm_sh_register_widgets() {
		return LegacyApi::delegate( 'widgets', 'registerWidget' );
	}
}

if ( ! function_exists( 'zm_sh_register_elementor_widget' ) ) {
	function zm_sh_register_elementor_widget( $widgets_manager ) {
		return LegacyElementorWidgetFactory::register( $widgets_manager );
	}
}

if ( ! function_exists( 'zm_sh_integrateWithVC' ) ) {
	function zm_sh_integrateWithVC() {
		return LegacyApi::delegate( 'wpBakery', 'registerElement' );
	}
}

if ( ! function_exists( 'zm_sh_metabox_new' ) ) {
	function zm_sh_metabox_new() {
		return new zm_sh_metabox();
	}
}

if ( ! function_exists( 'zm_sh_curentPageURL' ) ) {
	function zm_sh_curentPageURL() {
		$request = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : '/';

		return esc_url_raw( home_url( $request ) );
	}
}

if ( ! function_exists( 'zm_sh_get_excluded_post_identifiers' ) ) {
	function zm_sh_get_excluded_post_identifiers( $excludes ) {
		$policy = LegacyApi::delegate( 'excludedContent', 'identifiers', array( $excludes ) );

		return is_array( $policy ) ? $policy : array();
	}
}

if ( ! function_exists( 'zm_sh_post_is_excluded' ) ) {
	function zm_sh_post_is_excluded( $post, $excludes ) {
		if ( ! is_object( $post ) || empty( $post->ID ) ) {
			return false;
		}
		$policy = LegacyApi::service( 'excludedContent' );

		return is_object( $policy ) && method_exists( $policy, 'matches' )
			? $policy->matches( $post->ID, isset( $post->post_name ) ? $post->post_name : '', isset( $post->post_title ) ? $post->post_title : '', $excludes )
			: false;
	}
}

if ( ! function_exists( 'zm_sh_get_default_share_templates' ) ) {
	function zm_sh_get_default_share_templates() {
		return LegacyApi::defaultShareTemplates();
	}
}

if ( ! function_exists( 'zm_sh_get_share_templates' ) ) {
	function zm_sh_get_share_templates() {
		return LegacyHooks::shareTemplates( LegacyApi::shareTemplates() );
	}
}

if ( ! function_exists( 'zm_sh_get_share_template' ) ) {
	function zm_sh_get_share_template( $platform, $fallback = '' ) {
		$templates = zm_sh_get_share_templates();
		$template = isset( $templates[ $platform ] ) ? $templates[ $platform ] : $fallback;

		return LegacyHooks::shareTemplate( $template, $platform, $fallback );
	}
}

if ( ! function_exists( 'zm_sh_get_schema' ) ) {
	function zm_sh_get_schema( $id ) {
		return LegacySchemaRegistry::instance()->get( $id );
	}
}

if ( ! function_exists( 'zm_sh_get_schemas' ) ) {
	function zm_sh_get_schemas() {
		return LegacySchemaRegistry::instance()->all();
	}
}

if ( ! function_exists( 'zm_sh_add_schema' ) ) {
	function zm_sh_add_schema( $schema ) {
		return LegacySchemaRegistry::instance()->add( $schema );
	}
}

if ( ! function_exists( 'zm_sh_remove_schema' ) ) {
	function zm_sh_remove_schema( $id ) {
		return LegacySchemaRegistry::instance()->remove( $id );
	}
}

if ( ! function_exists( 'wp_ajax_get_iconset_details' ) ) {
	function wp_ajax_get_iconset_details() {
		return LegacyApi::delegate( 'admin', 'iconSetDetails' );
	}
}

if ( ! interface_exists( 'interface_iconset' ) ) {
	interface interface_iconset {
		public function set_dir_and_url( $__FILE__ );
		public function push_icon( $icon );
		public function get_iconset_preview();
	}
}

if ( ! class_exists( '__iconset_parent_class' ) ) {
	class __iconset_parent_class implements interface_iconset {
		public $__FILE__;
		public $id = '';
		public $name = '';
		public $types = array();
		public $icons = array();
		public $inTheme = false;
		public $inChildTheme = false;
		public $dir = '';
		public $url = '';
		public $stylesheet_url = '';
		public $preview_img_url = '';
		public $preview_img_dir = '';
		public $stylesheet = '';
		public $preview_img = '';

		public function __construct() { if ( '' !== (string) $this->__FILE__ ) { $this->set_dir_and_url( $this->__FILE__ ); } }
		public function set_dir_and_url( $__FILE__ ) {
			$this->__FILE__ = $__FILE__;
			if ( ! empty( $this->inTheme ) ) {
				$this->dir = get_template_directory() . $this->inTheme;
				$this->url = get_template_directory_uri() . $this->inTheme;
			} elseif ( ! empty( $this->inChildTheme ) ) {
				$this->dir = get_stylesheet_directory() . $this->inChildTheme;
				$this->url = get_stylesheet_directory_uri() . $this->inChildTheme;
			} else {
				$this->dir = plugin_dir_path( $__FILE__ );
				$this->url = plugins_url( '/', $__FILE__ );
			}
			$this->stylesheet_url = $this->url . $this->stylesheet;
			$this->preview_img_url = $this->url . $this->preview_img;
			$this->preview_img_dir = $this->dir . $this->preview_img;
		}
		public function get_icons() { return $this->icons; }
		public function get_icons_id_name() { $icons = array(); foreach ( $this->icons as $id => $icon ) { $icons[ $id ] = isset( $icon['name'] ) ? $icon['name'] : $id; } return $icons; }
		public function push_icon( $icon ) { $this->icons[] = $icon; }
		public function get_iconset_preview() { return $this->preview_img_url; }
	}
}

if ( ! class_exists( 'zm_sh_iconset_default' ) ) {
	class zm_sh_iconset_default extends __iconset_parent_class {
		public $id = 'default';
		public function __construct() { LegacyApi::populateLegacyIconSet( $this, $this->id ); parent::__construct(); }
	}
}

if ( ! class_exists( 'zm_sh_iconset_flat' ) ) {
	class zm_sh_iconset_flat extends __iconset_parent_class {
		public $id = 'flat';
		public function __construct() { LegacyApi::populateLegacyIconSet( $this, $this->id ); parent::__construct(); }
	}
}

if ( ! class_exists( 'zm_sh_iconset_long_shadows' ) ) {
	class zm_sh_iconset_long_shadows extends __iconset_parent_class {
		public $id = 'long-shadows';
		public function __construct() { LegacyApi::populateLegacyIconSet( $this, $this->id ); parent::__construct(); }
	}
}

if ( ! class_exists( 'zm_sh_iconset_prajin' ) ) {
	class zm_sh_iconset_prajin extends __iconset_parent_class {
		public $id = 'prajin';
		public function __construct() { LegacyApi::populateLegacyIconSet( $this, $this->id ); parent::__construct(); }
	}
}

if ( ! class_exists( 'zm_sh_iconset_bootstrap_solid' ) ) {
	class zm_sh_iconset_bootstrap_solid extends __iconset_parent_class {
		public $id = 'bootstrap-solid';
		public function __construct() { LegacyApi::populateLegacyIconSet( $this, $this->id ); parent::__construct(); }
	}
}

if ( ! class_exists( 'zm_sh_iconset_tabler_outline' ) ) {
	class zm_sh_iconset_tabler_outline extends __iconset_parent_class {
		public $id = 'tabler-outline';
		public function __construct() { LegacyApi::populateLegacyIconSet( $this, $this->id ); parent::__construct(); }
	}
}

if ( ! class_exists( 'zm_social_share' ) ) {
	class zm_social_share {
		public $iconset;
		public $iconsets;
		public $excluded = false;
		public $options = array();
		private $assets;

		public function __construct() { $this->options = LegacyApi::legacyOptions( isset( $GLOBALS['zm_sh_default_options'] ) ? $GLOBALS['zm_sh_default_options'] : array() ); $this->iconsets = new zm_sh_iconset(); $this->iconset = $this->iconsets->get_current_iconset(); $this->assets = LegacyApi::delegate( 'frontend', 'createAssetCollector', array(), null ); }
		public function wp() { LegacyApi::delegate( 'frontend', 'detectExclusion' ); $this->excluded = LegacyApi::delegate( 'frontend', 'isExcluded', array(), false ); return $this->excluded; }
		public function plugins_loaded() { return LegacyApi::delegate( 'frontend', 'loadTranslations' ); }
		public function translate_legacy_domain( $translation, $text, $domain ) { return LegacyApi::delegate( 'frontend', 'translateLegacyDomain', array( $translation, $text, $domain ), $translation ); }
		public function filter_the_content( $content ) { return is_object( $this->assets ) ? LegacyApi::delegate( 'frontend', 'filterContentWithOptionsAndAssets', array( $content, $this->options, $this->assets ), $content ) : LegacyApi::delegate( 'frontend', 'filterContentWithOptions', array( $content, $this->options ), $content ); }
		public function footer() { $html = is_object( $this->assets ) ? LegacyApi::delegate( 'frontend', 'footerWithOptions', array( $this->options, $this->assets ), '' ) : ''; /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Canonical renderer escapes the complete HTML fragment. */ echo $html; }
		public function register_styles() { if ( is_object( $this->assets ) ) { return LegacyApi::delegate( 'frontend', 'enqueueCollectedAssets', array( $this->assets ) ); } }
		public function icon_styles() { if ( is_object( $this->assets ) ) { /* phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Canonical asset collector returns sanitized CSS markup. */ echo LegacyApi::delegate( 'frontend', 'historicalCollectedIconStyles', array( ! empty( $this->options['auto_hide_btn'] ), $this->assets ), '' ); } }
		public function zm_sh_btn( $instance = '' ) { $options = is_array( $instance ) && ! empty( $instance ) ? $instance : $this->options; return is_object( $this->assets ) ? LegacyApi::delegate( 'frontend', 'renderWithOptions', array( $options, 0, $this->assets, $this->options ), '' ) : LegacyApi::render( $options ); }
	}
}

if ( ! class_exists( 'zm_sh_iconset' ) ) {
	class zm_sh_iconset {
		public $options = array();
		private $current = 'default';
		private $iconsets;
		public function __get( $var ) { return in_array( $var, array( 'curr_iconset', 'private' ), true ) ? $this->get_current_iconset() : ( isset( $this->iconsets->$var ) ? $this->iconsets->$var : null ); }
		public function __construct() {
			$this->options = LegacyApi::legacyOptions( isset( $GLOBALS['zm_sh_default_options'] ) ? $GLOBALS['zm_sh_default_options'] : array() );
			$this->iconsets = new stdClass();
			$settings = LegacyApi::settings();
			if ( is_object( $settings ) && method_exists( $settings, 'load' ) ) {
				$current = $settings->load();
				if ( is_object( $current ) && method_exists( $current, 'iconSetId' ) ) { $this->current = $current->iconSetId(); }
			}
			foreach ( array( 'default', 'flat', 'long-shadows', 'prajin', 'bootstrap-solid', 'tabler-outline' ) as $id ) {
				$set = $this->create( $id );
				if ( $set ) { $this->iconsets->$id = $set; }
			}
			foreach ( isset( $GLOBALS['zm_sh_iconset_classes'] ) ? (array) $GLOBALS['zm_sh_iconset_classes'] : array() as $class ) {
				if ( ! is_string( $class ) || ! class_exists( $class ) ) { continue; }
				$set = new $class();
				if ( empty( $set->id ) ) { continue; }
				$id = str_replace( '_', '-', sanitize_key( $set->id ) );
				if ( '' !== $id ) { $this->iconsets->$id = $set; }
			}
		}
		public function add_iconset( $iconset ) {
			$registered = LegacyApi::registerLegacyIconSet( $iconset );
			if ( $registered && is_object( $iconset ) && ! empty( $iconset->id ) ) {
				$id = str_replace( '_', '-', sanitize_key( $iconset->id ) );
				$this->iconsets->$id = $iconset;
			}
			return $registered ? $iconset : false;
		}
		public function get_current_iconset() { return $this->get_iconset( $this->current ); }
		public function set_current_iconset( $iconset_name ) { $this->current = (string) $iconset_name; return true; }
		public function get_iconset( $iconset = 'default', $setAsCurrent = false ) { if ( $setAsCurrent && isset( $this->iconsets->$iconset ) ) { $this->current = (string) $iconset; } return isset( $this->iconsets->$iconset ) ? $this->iconsets->$iconset : ( isset( $this->iconsets->default ) ? $this->iconsets->default : false ); }
		public function get_iconsets() { return $this->iconsets; }
		public function get_iconset_list() { $list = array(); foreach ( $this->iconsets as $id => $iconset ) { $list[ $id ] = $iconset->name; } return $list; }
		public function remove_iconset( $id ) { unset( $this->iconsets->$id ); return $id; }
		public function wp_ajax_get_iconset_preview() { return LegacyApi::delegate( 'admin', 'iconSetPreview' ); }
		public function wp_ajax_get_iconset() { return LegacyApi::delegate( 'admin', 'iconSet' ); }
		private function create( $id ) { $class = 'zm_sh_iconset_' . str_replace( '-', '_', $id ); return class_exists( $class ) ? new $class() : null; }
	}
}

/**
 * The remaining historical classes retain their names and signatures as thin
 * façades. Canonical admin, form, schema, metabox and widget controllers own
 * all work; these classes are only callable aliases for old extensions.
 */
if ( ! class_exists( 'zm_sh_settings' ) ) {
	class zm_sh_settings {
		public function __construct() {}
		public function reg_admn_menu() { return LegacyApi::delegate( 'admin', 'registerMenu' ); }
		public function admin_scripts( $hook ) { return LegacyApi::delegate( 'admin', 'enqueueAssets', array( $hook ) ); }
		public function ajax_search_content() { return LegacyApi::delegate( 'admin', 'searchContent' ); }
		public function ajax_save_settings() { return LegacyApi::delegate( 'admin', 'saveSettings' ); }
		public function add_new() { return LegacyApi::delegate( 'admin', 'registerMenu' ); }
		public function show_all() { return LegacyApi::delegate( 'admin', 'renderPage' ); }
		public function zm_sh_opt() { return LegacyApi::delegate( 'admin', 'renderPage' ); }
		public function zm_reg_sett() { return LegacyApi::delegate( 'admin', 'registerSettings' ); }
		public function sanitize( $input ) { return LegacyApi::delegate( 'admin', 'sanitize', array( $input ), $input ); }
	}
}

if ( ! class_exists( 'zm_form' ) ) {
	class zm_form {
		public $options = array();
		public $zm_sh;
		public $iconsets;
		public function __construct( $options = '' ) { $this->options = is_array( $options ) && ! empty( $options ) ? $options : LegacyApi::legacyOptions( isset( $GLOBALS['zm_sh_default_options'] ) ? $GLOBALS['zm_sh_default_options'] : array() ); $this->zm_sh = isset( $GLOBALS['zm_sh'] ) ? $GLOBALS['zm_sh'] : null; $this->iconsets = new zm_sh_iconset(); }
		public function text( $id, $label, $name = false, $value = false ) { return $this->present( 'text', func_get_args() ); }
		public function textArea( $id, $label, $name = false, $value = false ) { return $this->present( 'textArea', func_get_args() ); }
		public function checkbox( $id, $label, $name = '', $selected = null, $class = '', $yes = '', $no = '', $id_prefix = '', $description = '' ) { return $this->present( 'checkbox', func_get_args() ); }
		public function show_on( $id, $label, $selected = false, $class = 'toggle-check', $yes = '', $no = '' ) { return $this->present( 'showOn', func_get_args() ); }
		public function icon_fields( $label, $label_prefix, $class = 'toggle-check', $yes = '', $no = '' ) { return $this->present( 'iconFields', func_get_args() ); }
		public function icon_fields_widget( $id, $name, $selected_icons, $label, $label_prefix, $iconset ) { return $this->present( 'iconFieldsWidget', func_get_args() ); }
		public function dropdown( $id, $label, $items, $name = false, $selected = false ) { return $this->present( 'dropdown', func_get_args() ); }
		public function _select_iconset( $id, $label, $items, $name = false, $selected = 'default' ) { return $this->present( 'selectIconset', func_get_args() ); }
		public function select_iconset( $id, $label, $name = false, $selected = false ) { return $this->present( 'selectIconset', array( $id, $label, null, $name, $selected ) ); }
		private function present( $method, array $arguments ) { return LegacyApi::delegate( 'forms', $method, array_merge( array( $this->options ), $arguments ) ); }
	}
}

if ( ! class_exists( 'zm_sh_filters' ) ) {
	class zm_sh_filters {
		public function __construct() {}
		public function zm_sh_placeholder( $item ) {
			$url = zm_sh_curentPageURL();
			return str_replace( array( '%%permalink%%', '%%title%%', '%%description%%', '%%imageurl%%' ), array( rawurlencode( $url ), rawurlencode( $this->make_title( $url ) ), rawurlencode( get_bloginfo( 'description' ) ), rawurlencode( $this->image_url( $url ) ) ), $item );
		}
		public function ico_link( $ico_link ) { return $ico_link; }
		public function make_title( $url ) { $home = get_home_url(); $title = ( $home === $url || $home . '/' === $url ) ? get_bloginfo( 'name' ) : ( url_to_postid( $url ) ? get_the_title( url_to_postid( $url ) ) : get_the_title() ); return LegacyHooks::title( $title ); }
		public function image_url( $url ) { global $post; if ( ! is_object( $post ) || empty( $post->ID ) ) { return ''; } $image = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) ); if ( $image ) { return $image; } $linked = url_to_postid( $url ) ? get_post( url_to_postid( $url ), 'OBJECT' ) : null; if ( ! is_object( $linked ) ) { return ''; } preg_match_all( '/<img.+src=[\'\"]([^\'\"]+)[\'\"].*>/i', str_replace( 'zm_sh_btn', '', do_shortcode( $linked->post_content ) ), $matches ); return isset( $matches[1][0] ) ? $matches[1][0] : ''; }
	}
}

if ( ! class_exists( 'zm_sh_schema' ) ) {
	class zm_sh_schema {
		public static function getInstance() { return LegacySchemaRegistry::instance(); }
		public function get_schema( $id ) { return zm_sh_get_schema( $id ); }
		public function get_schemas() { return zm_sh_get_schemas(); }
		public function add_schema( $schema ) { return zm_sh_add_schema( $schema ); }
		public function remove_schema( $id ) { return zm_sh_remove_schema( $id ); }
	}
}

if ( ! class_exists( 'zm_sh_metabox' ) ) {
	class zm_sh_metabox {
		public function __construct() {}
		public function add_meta_box( $postType ) { return LegacyApi::delegate( 'metabox', 'addMetaBox', array( $postType ) ); }
		public function save( $postId ) { return LegacyApi::delegate( 'metabox', 'save', array( $postId ) ); }
		public function render_meta_box_content( $post ) { return LegacyApi::delegate( 'metabox', 'render', array( $post ) ); }
	}
}

if ( ! class_exists( 'zm_html_share_widget' ) && class_exists( 'WP_Widget' ) ) {
	class zm_html_share_widget extends WP_Widget {
		public function __construct() { parent::__construct( 'html_share_button_widget', 'HTML Social Share Buttons' ); }
		public function widget( $arguments, $instance ) { return LegacyApi::delegate( 'widgets', 'render', array( $arguments, $instance ) ); }
		public function update( $newInstance, $oldInstance ) { return LegacyApi::delegate( 'widgets', 'update', array( $newInstance, $oldInstance ), $oldInstance ); }
		public function form( $instance ) { return LegacyApi::delegate( 'widgets', 'form', array( $instance ) ); }
	}
}
