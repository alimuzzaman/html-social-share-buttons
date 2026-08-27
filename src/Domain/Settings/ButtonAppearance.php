<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Settings;

final class ButtonAppearance {
	const LEGACY = 'legacy';
	const MINIMAL = 'minimal';
	const FRAMED = 'framed';
	const SOFT_SHADOW = 'soft-shadow';

	private function __construct() {
	}

	public static function all() {
		return array(
			self::LEGACY,
			self::MINIMAL,
			self::FRAMED,
			self::SOFT_SHADOW,
		);
	}

	public static function supports( $value ) {
		return is_string( $value ) && in_array( $value, self::all(), true );
	}

	public static function normalize( $value ) {
		return self::supports( $value ) ? $value : self::LEGACY;
	}

	public static function modifier( $value ) {
		$value = self::normalize( $value );

		return self::LEGACY === $value ? '' : 'hssb-appearance--' . $value;
	}
}
