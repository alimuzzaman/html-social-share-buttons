<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration;

/**
 * Localized labels for bundled builder choices.
 *
 * Third-party extensions can retain their registered labels; only identifiers
 * shipped by this plugin are translated here.
 */
final class BuilderLabels {
	private function __construct() {
	}

	public static function network( $networkId, $fallback ) {
		$labels = array(
			'facebook'  => __( 'Facebook', 'html-social-share-buttons' ),
			'x'         => __( 'X', 'html-social-share-buttons' ),
			'linkedin'  => __( 'LinkedIn', 'html-social-share-buttons' ),
			'pinterest' => __( 'Pinterest', 'html-social-share-buttons' ),
			'telegram'  => __( 'Telegram', 'html-social-share-buttons' ),
			'bluesky'   => __( 'Bluesky', 'html-social-share-buttons' ),
			'mail'      => __( 'Email', 'html-social-share-buttons' ),
		);

		$networkId = (string) $networkId;

		return isset( $labels[ $networkId ] ) ? $labels[ $networkId ] : (string) $fallback;
	}

	public static function wpBakeryNetwork( $networkId, $fallback ) {
		if ( 'x' === (string) $networkId ) {
			return __( 'X (formerly Twitter)', 'html-social-share-buttons' );
		}

		return self::network( $networkId, $fallback );
	}

	public static function iconSet( $iconSetId, $fallback ) {
		$labels = array(
			'default'         => __( 'Default (legacy)', 'html-social-share-buttons' ),
			'flat'            => __( 'Flat', 'html-social-share-buttons' ),
			'long-shadows'    => __( 'Long Shadows', 'html-social-share-buttons' ),
			'prajin'          => __( 'Prajin', 'html-social-share-buttons' ),
			'bootstrap-solid' => __( 'Bootstrap Solid', 'html-social-share-buttons' ),
			'tabler-outline'  => __( 'Tabler Outline', 'html-social-share-buttons' ),
		);

		$iconSetId = (string) $iconSetId;

		return isset( $labels[ $iconSetId ] ) ? $labels[ $iconSetId ] : (string) $fallback;
	}

	public static function shape( $shape, $fallback ) {
		$labels = array(
			'square' => __( 'Square', 'html-social-share-buttons' ),
			'circle' => __( 'Circle', 'html-social-share-buttons' ),
		);

		$shape = (string) $shape;

		return isset( $labels[ $shape ] ) ? $labels[ $shape ] : (string) $fallback;
	}
}
