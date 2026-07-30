<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Hook;

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;

final class LegacyExtensionHookBridge {
	private static $active = array();

	public static function shareTemplates( array $templates ) {
		return self::filter(
			'share_templates',
			$templates,
			static function ( $value ) {
				$extensions = self::extensions();
				if ( null !== $extensions ) {
					$value = $extensions->shareTemplates( $value );
				}

				return apply_filters( 'zm_sh_share_templates', $value );
			}
		);
	}

	public static function shareTemplate( $template, $platform, $fallback ) {
		return self::filter(
			'share_template',
			$template,
			static function ( $value ) use ( $platform, $fallback ) {
				$extensions = self::extensions();
				if ( null !== $extensions ) {
					$value = $extensions->shareTemplate(
						$value,
						$platform,
						$fallback
					);
				}

				return apply_filters(
					'zm_sh_share_template',
					$value,
					$platform,
					$fallback
				);
			}
		);
	}

	public static function shareTitle( $title ) {
		return self::filter(
			'share_title',
			$title,
			static function ( $value ) {
				$extensions = self::extensions();
				if ( null !== $extensions ) {
					$value = $extensions->shareTitle( $value );
				}

				return apply_filters( 'zm_sh_title', $value );
			}
		);
	}

	public static function shareUrl( $url ) {
		return self::filter(
			'share_url',
			$url,
			static function ( $value ) {
				$extensions = self::extensions();
				if ( null !== $extensions ) {
					$value = $extensions->shareUrl( $value );
				}

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

	private static function filter( $name, $value, $callback ) {
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

	private static function extensions() {
		try {
			return LegacyRuntime::plugin()->extensions();
		} catch ( \LogicException $error ) {
			return null;
		}
	}
}
