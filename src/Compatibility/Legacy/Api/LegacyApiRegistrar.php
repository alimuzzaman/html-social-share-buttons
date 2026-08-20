<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\Plugin;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy;

/**
 * Installs public legacy names after the canonical kernel is already booted.
 */
final class LegacyApiRegistrar {
	private static $prepared = false;
	private static $adapter;

	private function __construct() {
	}

	/**
	 * Load compatibility symbols before factory construction without creating a
	 * legacy runtime. The returned importer is injected by the bootstrap, not
	 * referenced by the canonical factory.
	 */
	public static function prepare( $pluginFile ) {
		if ( self::$prepared ) {
			return;
		}

		LegacyConstants::define( $pluginFile );
		require_once __DIR__ . '/globals.php';
		self::prepareGlobals();
		self::$prepared = true;
	}

	public static function register( Plugin $plugin, $pluginFile ) {
		self::prepare( $pluginFile );
		LegacyApi::register( $plugin );
		self::registerRuntimeGlobals();
		LegacyHooks::register();
	}

	private static function prepareGlobals() {
		if ( ! isset( $GLOBALS['zm_sh_default_options'] ) || ! is_array( $GLOBALS['zm_sh_default_options'] ) ) {
			$GLOBALS['zm_sh_default_options'] = array(
				'title'                    => 'Share this with your friends',
				'iconset'                  => IconSetSelectionPolicy::NEW_DEFAULT_ID,
				'use_port'                 => false,
				'auto_hide_btn'            => false,
				'show_for_current_user'    => true,
				'show_for_logged_in_user'  => true,
				'show_for_logged_out_user' => true,
				'show_in'                  => array(
					'show_left'        => true,
					'show_right'       => false,
					'show_before_post' => false,
					'show_after_post'  => true,
				),
				'iconset_type'             => 'square',
				'icons'                    => array(
					'facebook'  => 1,
					'x'         => 1,
					'linkedin'  => 1,
					'pinterest' => 1,
					'telegram'  => 0,
					'bluesky'   => 0,
					'mail'      => 1,
				),
			);
		}
		if ( ! isset( $GLOBALS['zm_sh_iconset_classes'] ) || ! is_array( $GLOBALS['zm_sh_iconset_classes'] ) ) {
			$GLOBALS['zm_sh_iconset_classes'] = array();
		}
	}

	private static function registerRuntimeGlobals() {
		if ( ! isset( $GLOBALS['zm_sh'] ) || ! $GLOBALS['zm_sh'] instanceof \zm_social_share ) {
			$GLOBALS['zm_sh'] = new \zm_social_share();
		}
	}

	/**
	 * Preserve the old zm_sh_add_iconset extension point without letting legacy
	 * objects participate in request-time rendering. Add-ons are converted once
	 * into the canonical registries before the factory builds settings schema
	 * and presentation services.
	 */
	public static function importThirdPartyIconSets( $iconSets, $networks, $assets = null ) {
		if ( ! is_object( $iconSets ) || ! is_object( $networks ) ) {
			return;
		}

		LegacyHooks::registerIconSets();
		if ( ! self::$adapter instanceof LegacyIconSetAdapter ) {
			self::$adapter = new LegacyIconSetAdapter();
		}
		foreach ( $GLOBALS['zm_sh_iconset_classes'] as $class ) {
			if ( ! is_string( $class ) || ! class_exists( $class ) ) {
				continue;
			}
			try {
				self::$adapter->register( new $class(), $iconSets, $networks, $assets );
			} catch ( \Throwable $error ) {
				// A malformed third-party icon set must not prevent the plugin booting.
			}
		}
	}
}
