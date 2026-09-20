<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Frontend;

/**
 * Shared identity for opt-in public frontend JavaScript features.
 *
 * This is deliberately data-only. It gives the settings UI and the runtime
 * asset collector one stable feature ID without creating a broad extension
 * API for third parties.
 */
final class FrontendFeatureRegistry {
	const BROWSER_URL = 'browser_url';

	private function __construct() {
	}

	public static function all() {
		return array(
			self::BROWSER_URL => array(
				'id'                   => self::BROWSER_URL,
				'setting'              => 'use_browser_url',
				'requires_frontend_js' => true,
			),
		);
	}

	public static function get( $id ) {
		$features = self::all();
		$id = (string) $id;

		return isset( $features[ $id ] ) ? $features[ $id ] : null;
	}

	public static function requiresFrontendJs( $id ) {
		$feature = self::get( $id );

		return is_array( $feature ) && ! empty( $feature['requires_frontend_js'] );
	}
}
