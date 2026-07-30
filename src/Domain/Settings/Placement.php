<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Settings;

final class Placement {
	const LEFT           = 'left';
	const RIGHT          = 'right';
	const BEFORE_CONTENT = 'before_content';
	const AFTER_CONTENT  = 'after_content';

	private function __construct() {
	}

	public static function all() {
		return array(
			self::LEFT,
			self::RIGHT,
			self::BEFORE_CONTENT,
			self::AFTER_CONTENT,
		);
	}
}
