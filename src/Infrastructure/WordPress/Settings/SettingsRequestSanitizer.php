<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsSchema;

final class SettingsRequestSanitizer {
	private $schema;

	public function __construct( SettingsSchema $schema ) {
		$this->schema = $schema;
	}

	public function sanitize( array $input ) {
		$defaults = SettingsDefaults::create();
		$iconSetId = isset( $input['icon_set'] ) ? sanitize_key( $input['icon_set'] ) : '';
		if ( ! $this->schema->supportsIconSet( $iconSetId ) ) {
			$iconSetId = $defaults->iconSetId();
		}
		$iconShape = isset( $input['icon_shape'] ) ? sanitize_key( $input['icon_shape'] ) : '';
		if ( ! $this->schema->supportsIconShape( $iconShape ) ) {
			$iconShape = $defaults->defaultIconShape();
		}

		$placements = array();
		$placementShapes = array();
		$submittedPlacements = isset( $input['placements'] ) && is_array( $input['placements'] )
			? $input['placements']
			: array();
		$submittedShapes = isset( $input['placement_shapes'] ) && is_array( $input['placement_shapes'] )
			? $input['placement_shapes']
			: array();
		foreach ( $this->schema->placementIds() as $placement ) {
			$placements[ $placement ] = $this->toBoolean(
				isset( $submittedPlacements[ $placement ] )
					? $submittedPlacements[ $placement ]
					: false
			);
			$shape = isset( $submittedShapes[ $placement ] )
				? sanitize_key( $submittedShapes[ $placement ] )
				: $iconShape;
			$placementShapes[ $placement ] = $this->schema->supportsIconShape( $shape )
				? $shape
				: $iconShape;
		}

		$submittedNetworks = isset( $input['networks'] ) && is_array( $input['networks'] )
			? $input['networks']
			: array();
		$networkStates = array();
		$shareTemplates = array();
		$profileLinks = array();
		$submittedTemplates = isset( $input['share_templates'] ) && is_array( $input['share_templates'] )
			? $input['share_templates']
			: array();
		$submittedProfileLinks = isset( $input['profile_links'] ) && is_array( $input['profile_links'] )
			? $input['profile_links']
			: array();
		foreach ( $this->schema->networkIds() as $networkId ) {
			$networkStates[ $networkId ] = $this->toBoolean(
				isset( $submittedNetworks[ $networkId ] )
					? $submittedNetworks[ $networkId ]
					: false
			);
			if ( isset( $submittedTemplates[ $networkId ] ) && is_string( $submittedTemplates[ $networkId ] ) ) {
				$shareTemplates[ $networkId ] = sanitize_textarea_field(
					$submittedTemplates[ $networkId ]
				);
			}
			if ( isset( $submittedProfileLinks[ $networkId ] ) ) {
				$profileLink = $this->sanitizeProfileLink(
					$networkId,
					$submittedProfileLinks[ $networkId ]
				);
				if ( '' !== $profileLink ) {
					$profileLinks[ $networkId ] = $profileLink;
				}
			}
		}

		return new Settings(
			isset( $input['title'] ) ? sanitize_text_field( $input['title'] ) : $defaults->title(),
			$iconSetId,
			$iconShape,
			$placements,
			$placementShapes,
			$networkStates,
			$shareTemplates,
			isset( $input['excluded_content'] )
				? sanitize_textarea_field( $input['excluded_content'] )
				: '',
			$this->toBoolean( isset( $input['analytics_enabled'] ) ? $input['analytics_enabled'] : false ),
			$this->toBoolean( isset( $input['auto_hide_enabled'] ) ? $input['auto_hide_enabled'] : false ),
			$this->toBoolean( isset( $input['preserve_url_port'] ) ? $input['preserve_url_port'] : false ),
			$this->toBoolean( isset( $input['no_follow'] ) ? $input['no_follow'] : false ),
			$profileLinks
		);
	}

	private function sanitizeProfileLink( $networkId, $value ) {
		if ( ! is_string( $value ) ) {
			return '';
		}

		$value = trim( $value );
		if ( '' === $value || preg_match( '/[\r\n]/', $value ) ) {
			return '';
		}

		if ( 'mail' === $networkId ) {
			if ( 0 !== stripos( $value, 'mailto:' ) ) {
				return '';
			}

			$address = substr( $value, 7 );
			if (
				false !== strpos( $address, '?' ) ||
				false !== strpos( $address, '#' ) ||
				preg_match( '/%(?:0a|0d)/i', $address )
			) {
				return '';
			}

			$address = rawurldecode( $address );
			$validated = is_email( $address );

			return false !== $validated ? 'mailto:' . $validated : '';
		}

		$url = esc_url_raw( $value, array( 'https' ) );
		$parts = $url ? wp_parse_url( $url ) : false;
		if (
			! is_array( $parts ) ||
			empty( $parts['host'] ) ||
			empty( $parts['scheme'] ) ||
			'https' !== strtolower( $parts['scheme'] )
		) {
			return '';
		}

		return $url;
	}

	private function toBoolean( $value ) {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_int( $value ) || is_float( $value ) ) {
			return 0.0 !== (float) $value;
		}
		if ( is_string( $value ) ) {
			return ! in_array(
				strtolower( trim( $value ) ),
				array( '', '0', 'false', 'off', 'no' ),
				true
			);
		}

		return ! empty( $value );
	}
}
