<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

/**
 * Defines old path constants as aliases of canonical constants.
 *
 * These aliases intentionally retain historical iconset/ paths.  Built-in PNG
 * packs remain there for saved add-ons and external integrations.
 */
final class LegacyConstants {
	private function __construct() {
	}

	public static function define( $pluginFile ) {
		$pluginFile = (string) $pluginFile;
		self::canonical( $pluginFile );
		$pluginDir = (string) constant( 'HSSB_PLUGIN_DIR' );
		$pluginUrl = (string) constant( 'HSSB_PLUGIN_URL' );
		$assetsUrl = (string) constant( 'HSSB_ASSETS_URL' );

		self::alias( 'zm_sh_dir', $pluginDir );
		self::alias( 'zm_sh_url', $pluginUrl );
		self::alias( 'zm_sh_url_iconset', $pluginUrl . 'iconset/' );
		self::alias( 'zm_sh_url_assets', $assetsUrl );
		self::alias( 'zm_sh_url_assets_img', $assetsUrl . 'image/' );
	}

	private static function canonical( $pluginFile ) {
		if ( ! defined( 'HSSB_PLUGIN_FILE' ) ) {
			define( 'HSSB_PLUGIN_FILE', $pluginFile );
		}
		if ( ! defined( 'HSSB_PLUGIN_DIR' ) ) {
			define( 'HSSB_PLUGIN_DIR', plugin_dir_path( HSSB_PLUGIN_FILE ) );
		}
		if ( ! defined( 'HSSB_PLUGIN_URL' ) ) {
			define( 'HSSB_PLUGIN_URL', plugin_dir_url( HSSB_PLUGIN_FILE ) );
		}
		if ( ! defined( 'HSSB_ASSETS_URL' ) ) {
			define( 'HSSB_ASSETS_URL', HSSB_PLUGIN_URL . 'assets/' );
		}
	}

	private static function alias( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}
