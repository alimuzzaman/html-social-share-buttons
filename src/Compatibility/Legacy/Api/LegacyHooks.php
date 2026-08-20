<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;

/**
 * Maps canonical extension filters to their historic names exactly once.
 *
 * Canonical code applies hssb/* filters. This bridge is installed after the
 * canonical kernel exists and only invokes the matching zm_sh_* extension
 * hook; it must never call an ExtensionHooks method from inside a callback or
 * the canonical chain would run twice.
 */
final class LegacyHooks {
	private static $active = array();
	private static $bridging = array();
	private static $registered = false;

	private function __construct() {
	}

	public static function register() {
		if ( self::$registered || ! function_exists( 'add_filter' ) ) {
			return;
		}

		add_filter( ExtensionHooks::SHARE_TEMPLATES, array( __CLASS__, 'bridgeShareTemplates' ), 999 );
		add_filter( ExtensionHooks::SHARE_TEMPLATE, array( __CLASS__, 'bridgeShareTemplate' ), 999, 3 );
		add_filter( ExtensionHooks::SHARE_TITLE, array( __CLASS__, 'bridgeTitle' ), 999 );
		add_filter( ExtensionHooks::SHARE_URL, array( __CLASS__, 'bridgeUrl' ), 999 );
		self::$registered = true;
	}

	/**
	 * Public entry points used by historical globals.  A legacy callback may
	 * call one of those globals again; in that case return its current value
	 * instead of re-entering the canonical chain.  This preserves the old
	 * extension point while allowing hssb/* filters to run first.
	 */
	public static function shareTemplates( array $templates ) {
		return self::canonical(
			'share_templates',
			$templates,
			function ( $value ) {
				$hooks = LegacyApi::extensions();

				return is_object( $hooks ) && method_exists( $hooks, 'shareTemplates' )
				? $hooks->shareTemplates( $value )
				: $value;
			}
		);
	}

	public static function shareTemplate( $template, $platform, $fallback = '' ) {
		return self::canonical(
			'share_template',
			$template,
			function ( $value ) use ( $platform, $fallback ) {
				$hooks = LegacyApi::extensions();

				return is_object( $hooks ) && method_exists( $hooks, 'shareTemplate' )
				? $hooks->shareTemplate( $value, $platform, $fallback )
				: $value;
			}
		);
	}

	public static function title( $title ) {
		return self::canonical(
			'share_title',
			$title,
			function ( $value ) {
				$hooks = LegacyApi::extensions();

				return is_object( $hooks ) && method_exists( $hooks, 'shareTitle' )
				? $hooks->shareTitle( $value )
				: $value;
			}
		);
	}

	public static function url( $url ) {
		return self::canonical(
			'share_url',
			$url,
			function ( $value ) {
				$hooks = LegacyApi::extensions();

				return is_object( $hooks ) && method_exists( $hooks, 'shareUrl' )
				? $hooks->shareUrl( $value )
				: $value;
			}
		);
	}

	public static function bridgeShareTemplates( array $templates ) {
		return self::legacy(
			'share_templates',
			$templates,
			function ( $value ) {
				return apply_filters( 'zm_sh_share_templates', $value );
			}
		);
	}

	public static function bridgeShareTemplate( $template, $platform, $fallback = '' ) {
		return self::legacy(
			'share_template',
			$template,
			function ( $value ) use ( $platform, $fallback ) {
				return apply_filters( 'zm_sh_share_template', $value, $platform, $fallback );
			}
		);
	}

	public static function bridgeTitle( $title ) {
		return self::legacy(
			'share_title',
			$title,
			function ( $value ) {
				return apply_filters( 'zm_sh_title', $value );
			}
		);
	}

	public static function bridgeUrl( $url ) {
		return self::legacy(
			'share_url',
			$url,
			function ( $value ) {
				return apply_filters( 'zm_sh_placeholder', $value );
			}
		);
	}

	public static function registerIconSets() {
		do_action( 'zm_sh_add_iconset' );
	}

	public static function registerSchemas() {
		do_action( 'zm_sh_add_schema' );
	}

	private static function canonical( $name, $value, $callback ) {
		if ( ! empty( self::$active[ $name ] ) ) {
			return $value;
		}

		self::$active[ $name ] = true;
		try {
			return call_user_func( $callback, $value );
		} finally {
			unset( self::$active[ $name ] );
		}
	}

	private static function legacy( $name, $value, $callback ) {
		if ( ! empty( self::$bridging[ $name ] ) ) {
			return $value;
		}

		self::$bridging[ $name ] = true;
		try {
			return call_user_func( $callback, $value );
		} finally {
			unset( self::$bridging[ $name ] );
		}
	}
}
