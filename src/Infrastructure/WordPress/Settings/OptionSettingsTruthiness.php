<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings;

/**
 * Preserves the PHP truthiness semantics of the established option values.
 *
 * Existing installations can contain values such as the non-empty string
 * "false". Changing their meaning during a rewrite would alter live output.
 */
final class OptionSettingsTruthiness {
	public static function isTruthy( $value ) {
		return (bool) $value;
	}

	/**
	 * Strict parsing for newly introduced boolean settings. Historical fields
	 * keep isTruthy() semantics; new fields reject arrays, objects, and unknown
	 * strings instead of silently enabling a feature.
	 */
	public static function isStrictBoolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_int( $value ) || is_float( $value ) ) {
			return 1.0 === (float) $value;
		}
		if ( ! is_string( $value ) ) {
			return false;
		}

		$value = strtolower( trim( $value ) );
		if ( in_array( $value, array( '1', 'true', 'on', 'yes' ), true ) ) {
			return true;
		}
		if ( in_array( $value, array( '', '0', 'false', 'off', 'no' ), true ) ) {
			return false;
		}

		return false;
	}
}
