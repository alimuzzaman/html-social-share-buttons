<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering;

use InvalidArgumentException;

final class RenderPlacement {
	const FLOATING_LEFT = 'floating_left';
	const FLOATING_RIGHT = 'floating_right';
	const BEFORE_CONTENT = 'before_content';
	const AFTER_CONTENT = 'after_content';
	const SHORTCODE = 'shortcode';
	const WIDGET = 'widget';
	const BLOCK = 'block';
	const ELEMENTOR = 'elementor';
	const WPBAKERY = 'wpbakery';
	const PHP_API = 'php_api';

	private function __construct() {
	}

	public static function all() {
		return array(
			self::FLOATING_LEFT,
			self::FLOATING_RIGHT,
			self::BEFORE_CONTENT,
			self::AFTER_CONTENT,
			self::SHORTCODE,
			self::WIDGET,
			self::BLOCK,
			self::ELEMENTOR,
			self::WPBAKERY,
			self::PHP_API,
		);
	}

	public static function assertValid( $placement ) {
		if ( ! in_array( (string) $placement, self::all(), true ) ) {
			throw new InvalidArgumentException( 'Unknown render placement.' );
		}
	}
}
