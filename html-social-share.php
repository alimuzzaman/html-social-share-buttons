<?php
/*
Plugin Name: Html Social share buttons
Plugin URI: http://wordpress.org/plugins/html-social-share-buttons/
Description: Lightweight HTML and CSS social share buttons. Settings and block editing use WordPress JavaScript.
Author: Alimuzzaman Alim
Version: 2.2.6
Author URI: https://alim.dev
Text Domain: html-social-share-buttons
Domain Path: /languages
Requires at least: 5.3
Requires PHP: 7.0
License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Iconset dir where to search for iconsets.
define("zm_sh_dir", plugin_dir_path(__FILE__));
//define("zm_sh_url_iconset", zm_sh_dir . "iconset");

define("zm_sh_url", plugin_dir_url(__FILE__));
define("zm_sh_url_iconset", zm_sh_url . "iconset/");
define("zm_sh_url_assets", zm_sh_url . "assets/");
define("zm_sh_url_assets_img", zm_sh_url_assets . "image/");

$dir_iconset = plugin_dir_path(__FILE__) . "iconset";

$zm_sh_default_options = array(
	"title"				=> "Share this with your friends",
	"iconset"			=> "default",
	"use_port"			=> false,
	"auto_hide_btn"		=> false,
	"show_in" 			=> array(
		"show_left"			=> true,
		"show_right"		=> false,
		"show_before_post"	=> false,
		"show_after_post"	=> true,
	),
	'iconset_type'	=> "square",
	"icons" => array(
		"facebook"		=> 1,
		"x"				=> 1,
		"linkedin"		=> 1,
		"pinterest"		=> 1,
		"telegram"		=> 0,
		"bluesky"		=> 0,
		"mail"			=> 1,
	)

);

//include interfaces.php
//it's contains all interfaces
include("interfaces.php");

// Canonical, filterable share URL templates for built-in platforms.
include("share-templates.php");

//include iconsets.php
//it's contains all function to add, remove, get iconsets
include("iconsets.php");

//include actions.php
//it's contain actions
include("actions.php");

//include filters.php
//it's contain filters
include("filters.php");

require_once("function.php");
//include widget.php
//it's register widget
include("widget.php");

include('vc-integration.php');
include('shortcode.php');
include('elementor-integration.php');
include('block-integration.php');

require_once("form.php");

include("metabox.php");
include("settings_page.php");

// Add settings link to plugins page
add_filter('plugin_action_links_' . plugin_basename(__FILE__), function($links) {
	$settings_link = '<a href="options-general.php?page=zm_shbt_opt">' . __('Settings', 'html-social-share-buttons') . '</a>';
	array_unshift($links, $settings_link);
	return $links;
});

// make variable globaly accessable
global $zm_sh_iconset_classes;

//new instance of class zm_social_share
add_action('init', function () {
	global $zm_sh;
	$zm_sh = new zm_social_share;
	//echo 'xxxxx init done';
}, 1);

function zm_sh_btn($options) {
	global $zm_sh;
	//print_r($zm_sh);
	//wp_die();
	if (!is_object($zm_sh) || !is_array($options))
		return '';
	$icons = isset( $options['icons'] ) && is_array( $options['icons'] ) ? $options['icons'] : array();
	$is_list = ! empty( $icons ) && array_keys( $icons ) === range( 0, count( $icons ) - 1 );
	$icons = $is_list ? $icons : array_keys( $icons );
	$icons = array_map( 'sanitize_key', $icons );
	$options['icons'] = array_fill_keys( array_filter( $icons ), 'on' );
	return $zm_sh->zm_sh_btn($options);
}

class zm_social_share {
	public	$iconset;
	public	$iconsets;
	public  $excluded = false;

	public	$options;
	private	$schemas;
	private	$icons;
	private	$printed_icons;
	private	$stylesheets;
	/*
	public static function getInstance() {
		static $instance;
		if ($instance === null){
			$instance = new self;
			do_action( "zm_sh_add_iconset");
		}
		return $instance;
	}*/

	function __construct() {
		global $zm_sh_default_options;

		$this->options = get_option("zm_shbt_fld", $zm_sh_default_options);

		// Runtime migration: Convert 'twitter' to 'x' for backward compatibility
		if (isset($this->options['icons']['twitter'])) {
			$this->options['icons']['x'] = $this->options['icons']['twitter'];
			unset($this->options['icons']['twitter']);
		}

		$this->iconsets	= new zm_sh_iconset;
		//print_r($this->iconsets);
		// getting options form database
		// getting the current iconset
		$this->iconset = $this->iconsets->get_current_iconset();

		//print styles and floating buttons
		add_action('wp_footer',  array($this, 'footer'));
		//register stylesheets from theme
		//add_action( 'wp_enqueue_scripts', array($this,'register_styles') );



		add_action( 'init', array( $this, 'plugins_loaded' ), 2 );

		if (isset($this->options['show_after_post']) and $this->options['show_after_post'] or isset($this->options['show_before_post']) and $this->options['show_before_post'])
			add_filter('the_content', array($this, 'filter_the_content'));

		add_action('wp', array($this, 'wp'));
	}
	/*



	*/
	function wp() {
		global $post;
		//echo $post->ID;
		//print_r($post);
		if (!is_object($post) || empty($post->ID)) return;

		$excludes = ! empty( $this->options['excludes'] ) ? $this->options['excludes'] : '';
		if ( zm_sh_post_is_excluded( $post, $excludes ) ) {
			$this->excluded	= true;
			return;
		}

		$disable_share = get_post_meta($post->ID, '_zm_sh_disable_share', true);
		if ($disable_share == 'on') {
			$this->excluded	= true;
			return;
		}
	}

	function plugins_loaded() {
		// Localization
		$language_path = dirname( plugin_basename( __FILE__ ) ) . '/languages';

		// Keep loading the legacy domain so existing site-local translations remain available during migration.
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Required for the legacy zm-sh catalog during the text-domain migration.
		load_plugin_textdomain( 'zm-sh', false, $language_path );
		// phpcs:ignore PluginCheck.CodeAnalysis.DiscouragedFunctions.load_plugin_textdomainFound -- Required for the bundled catalog on WordPress versions without automatic plugin translation loading.
		load_plugin_textdomain( 'html-social-share-buttons', false, $language_path );

		add_filter( 'gettext', array( $this, 'translate_legacy_domain' ), 10, 3 );
	}

	function translate_legacy_domain( $translation, $text, $domain ) {
		if ( 'html-social-share-buttons' !== $domain || $translation !== $text ) {
			return $translation;
		}

		$legacy_translation = get_translations_for_domain( 'zm-sh' )->translate( $text );

		return $legacy_translation !== $text ? $legacy_translation : $translation;
	}

	function filter_the_content($content) {
		//if(isset($this->excluded) and $this->excluded == true) return;
		//print_r($this->options);
		$options = $this->options;
		$options['class'] = "in_widget";
		if (is_singular() && isset($options['show_in']['show_before_post']) and  $options['show_in']['show_before_post']) {
			$options['show_on'] = 'show_before_post';
			$content = $this->zm_sh_btn($options) . $content;
		}
		if (is_singular() && isset($options['show_in']['show_after_post']) and $options['show_in']['show_after_post']) {
			$options['show_on'] = 'show_after_post';
			$content = $content . $this->zm_sh_btn($options);
		}
		return $content;
	}



	//print styles and floating buttons
	function footer() {
		if (is_admin()) return;
		if (isset($this->excluded) and $this->excluded == true) return;
		$options = $this->options;

		if (isset($options['g_analytics']) and $options['g_analytics']) {
			echo "
				<script>
				jQuery(document).ready(function($){
					var _gaq = _gaq || [];
					jQuery('.zmshbt a').on('click', function(event){
						var _gaq = _gaq || [];
						switch(this.className){
							case 'googlepluse':
								action = '+1';
							case 'twitter':
								action = 'Tweet';
							case 'mail':
								action = 'Mail';
							default :
								action = 'Share';
						}
						_gaq.push(['_trackSocial', this.className, action]);
						console.log(action);
					});
				});
				</script>
			";
		}
		if (isset($options['show_in']['show_left']) and $options['show_in']['show_left']) {
			$options['class'] = 'left';
			$options['show_on'] = 'show_left';
			echo wp_kses_post($this->zm_sh_btn($options));
		}
		if (isset($options['show_in']['show_right']) and $options['show_in']['show_right']) {
			$options['class'] = 'right';
			$options['show_on'] = 'show_right';
			echo wp_kses_post($this->zm_sh_btn($options));
		}

		$this->register_styles();
		$this->icon_styles();
	}
	//register stylesheets from theme
	function register_styles() {
		if (is_array($this->stylesheets)) {
			foreach ($this->stylesheets as $id => $stylesheet) {
				wp_enqueue_style("social-share-" . sanitize_key($id), $stylesheet, array(), '2.2.4');
			}
		} else
			wp_enqueue_style("social-share-default", plugins_url('iconset/default/', __FILE__) . 'style.css', array(), '2.2.4');
	}

	//print styles for each icons in footer
	function icon_styles() {
		if (!is_array($this->printed_icons))
			return;
		$options = $this->options;
		echo "<style>";
		//print_r($this->printed_icons);
		foreach ($this->printed_icons as $id => $iconset) {
			// Explicit variable extraction for PHP 8.x compatibility
			$iconset_id = $iconset['iconset_id'] ?? '';
			$iconset_type = $iconset['iconset_type'] ?? '';
			$iconset_url = $iconset['iconset_url'] ?? '';
			$class = $iconset['class'] ?? '';
			$image = $iconset['image'] ?? '';
			echo "
			.zmshbt." . esc_attr($iconset_id) . "." . esc_attr($iconset_type) . " ." . esc_attr($class) . " {
					background-image:url('" . esc_url($iconset_url . $iconset_type . '/' . $image) . "');
			}
			";
		}
		if (!$options['auto_hide_btn']) {
			echo "
				.zmshbt.left{
					left: 0 !important;
				}
				.zmshbt.right {
					right: 0 !important;
				}
			";
		}
		echo "</style>";
	}

	//the button generator function
	function zm_sh_btn($instance = "") {
		$output			= '';
		if (isset($this->excluded) and $this->excluded == true) return;
		$options		= is_array( $instance ) && ! empty( $instance ) ? $instance : ( is_array( $this->options ) ? $this->options : array() );
		$options		= wp_parse_args( $options, array(
			'iconset' => 'default',
			'icons' => array(),
			'class' => 'left',
			'show_on' => 'show_left',
			'iconset_type' => '',
			'title' => '',
		) );
		if ( ! is_array( $options['icons'] ) ) {
			$options['icons'] = array();
		}

		// Runtime migration: Convert 'twitter' to 'x' for widgets/instances with old data
		if (isset($options['icons']['twitter'])) {
			$options['icons']['x'] = $options['icons']['twitter'];
			unset($options['icons']['twitter']);
		}
		// Sanitize inputs to prevent XSS vulnerabilities
		$__class		= sanitize_html_class( is_scalar( $options['class'] ) && $options['class'] ? $options['class'] : 'left' );
		$iconset_id		= sanitize_key( is_scalar( $options['iconset'] ) ? $options['iconset'] : 'default' );
		$selected_icons = $options['icons'];
		$rel			= ! empty( $options['nofollow'] ) ? 'nofollow noopener noreferrer' : 'noopener noreferrer';

		$iconset		= $this->iconsets->get_iconset($iconset_id);
		if ( ! is_object( $iconset ) ) {
			return '';
		}
		$this->stylesheets[$iconset->id]	= $iconset->url . $iconset->stylesheet;
		$icons			= $iconset->icons;
		$show_on = in_array( $options['show_on'], array( 'show_left', 'show_right', 'show_before_post', 'show_after_post' ), true ) ? $options['show_on'] : 'show_left';
		$iconset_types = array_values( array_filter( (array) $iconset->types, 'is_scalar' ) );
		$type_value = $options['iconset_type'] ? $options['iconset_type'] : ( isset( $options[ $show_on ] ) ? $options[ $show_on ] : '' );
		$iconset_type = sanitize_key( is_scalar( $type_value ) ? $type_value : '' );
		if ( ! $iconset_type || ! in_array( $iconset_type, $iconset_types, true ) ) {
			$iconset_type = sanitize_key( isset( $iconset_types[0] ) ? $iconset_types[0] : 'square' );
		}
		//print_r($options);
		if (
			(
				isset($options['show_on']) and
				($options['show_on'] == 'show_after_post' or $options['show_on'] == 'show_before_post')
			)
			or
			(isset($options['title']) and $options['class'] == 'in_shortcode')
		)
			$output = "<h3>" . esc_html($options['title']) . "</h3>";
		//print_r($options);
		//echo "\n\n\n\n\n\n\n\n\n\n\n\n";
		$output .= "<div class='zmshbt " . esc_attr($__class) . " " . esc_attr($iconset_id) . " " . esc_attr($iconset_type) . "'>";
		//print_r($icons);
		if (is_array($selected_icons))
			foreach ($selected_icons as $id => $ico) {
				if (!isset($icons[$id])) continue;
				$icon = $icons[$id];
				if (!$icon) continue;
				// Explicit variable extraction for PHP 8.x compatibility
				$class = $icon['class'] ?? '';
				$url = zm_sh_get_share_template( $id, $icon['url'] ?? '' );
				$icon['iconset_id']		= $iconset->id;
				$icon['iconset_url']	= $iconset->url;
				$icon['iconset_type']	= $iconset_type;
				if (!array_key_exists($id, (array)$selected_icons) and !in_array($id, (array)$selected_icons)) continue;
				$this->printed_icons[$iconset->id . "_$iconset_type\0_" . $id] = $icon;
				if (isset($options['url']) and !empty($options['url']))
					$url = str_replace("%%permalink%%", rawurlencode( esc_url_raw( (string) $options['url'] ) ), $url);
				$url = apply_filters("zm_sh_placeholder", $url);
				$output .= "<a class='" . esc_attr($class) . "' target='_blank' href='" . esc_url($url) . "' rel='" . esc_attr($rel) . "'></a>\n";
			}
		$output .= "</div>";
		return $output;
	}
}
