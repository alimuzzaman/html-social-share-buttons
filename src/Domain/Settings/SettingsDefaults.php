<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Settings;

final class SettingsDefaults {
	private function __construct() {
	}

	public static function create() {
		return new Settings(
			'Share this with your friends',
			'default',
			'square',
			array(
				Placement::LEFT           => true,
				Placement::RIGHT          => false,
				Placement::BEFORE_CONTENT => false,
				Placement::AFTER_CONTENT  => true,
			),
			array(
				Placement::LEFT           => 'square',
				Placement::RIGHT          => 'square',
				Placement::BEFORE_CONTENT => 'square',
				Placement::AFTER_CONTENT  => 'square',
			),
			array(
				'facebook'  => true,
				'x'         => true,
				'linkedin'  => true,
				'pinterest' => true,
				'telegram'  => false,
				'bluesky'   => false,
				'mail'      => true,
			),
			array(),
			'',
			false,
			false,
			false,
			false,
			array()
		);
	}
}
