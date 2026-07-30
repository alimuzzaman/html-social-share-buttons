<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;

$legacyPluginRoot = LegacyRuntime::pluginRoot();
$legacyPluginFile = $legacyPluginRoot . '/html-social-share.php';

define( 'zm_sh_dir', plugin_dir_path( $legacyPluginFile ) );
define( 'zm_sh_url', plugin_dir_url( $legacyPluginFile ) );
define( 'zm_sh_url_iconset', zm_sh_url . 'iconset/' );
define( 'zm_sh_url_assets', zm_sh_url . 'assets/' );
define( 'zm_sh_url_assets_img', zm_sh_url_assets . 'image/' );

$zm_sh_default_options = array(
	'title' => 'Share this with your friends',
	'iconset' => 'default',
	'use_port' => false,
	'auto_hide_btn' => false,
	'show_in' => array(
		'show_left' => true,
		'show_right' => false,
		'show_before_post' => false,
		'show_after_post' => true,
	),
	'iconset_type' => 'square',
	'icons' => array(
		'facebook' => 1,
		'x' => 1,
		'linkedin' => 1,
		'pinterest' => 1,
		'telegram' => 0,
		'bluesky' => 0,
		'mail' => 1,
	),
);

require_once __DIR__ . '/interfaces.php';
require_once __DIR__ . '/share-templates.php';
require_once __DIR__ . '/iconsets.php';
require_once __DIR__ . '/actions.php';
require_once __DIR__ . '/filters.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/widget.php';
require_once __DIR__ . '/wpbakery.php';
require_once __DIR__ . '/shortcode.php';
require_once __DIR__ . '/elementor.php';
require_once __DIR__ . '/block.php';
require_once __DIR__ . '/form.php';
require_once __DIR__ . '/metabox.php';
require_once __DIR__ . '/settings-page.php';

add_filter(
	'plugin_action_links_' . plugin_basename( $legacyPluginFile ),
	static function ( $links ) {
		$settingsLink = '<a href="options-general.php?page=zm_shbt_opt">' .
			__( 'Settings', 'html-social-share-buttons' ) .
			'</a>';
		array_unshift( $links, $settingsLink );

		return $links;
	}
);

require_once __DIR__ . '/runtime.php';
