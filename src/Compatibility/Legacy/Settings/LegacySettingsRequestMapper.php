<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

final class LegacySettingsRequestMapper {
	private $placements = array(
		'show_left'        => Placement::LEFT,
		'show_right'       => Placement::RIGHT,
		'show_before_post' => Placement::BEFORE_CONTENT,
		'show_after_post'  => Placement::AFTER_CONTENT,
	);

	public function toCanonical( array $input ) {
		$placements = array();
		$placementShapes = array();
		$submittedPlacements = isset( $input['show_in'] ) && is_array( $input['show_in'] )
			? $input['show_in']
			: array();
		foreach ( $this->placements as $legacyKey => $canonicalKey ) {
			$placements[ $canonicalKey ] = isset( $submittedPlacements[ $legacyKey ] )
				? LegacyTruthiness::isTruthy( $submittedPlacements[ $legacyKey ] )
				: false;
			if ( isset( $input[ $legacyKey ] ) ) {
				$placementShapes[ $canonicalKey ] = $input[ $legacyKey ];
			}
		}

		$networks = isset( $input['icons'] ) && is_array( $input['icons'] )
			? $input['icons']
			: array();
		if ( isset( $networks['twitter'] ) && ! isset( $networks['x'] ) ) {
			$networks['x'] = $networks['twitter'];
		}

		return array(
			'title'              => isset( $input['title'] ) ? $input['title'] : '',
			'icon_set'           => isset( $input['iconset'] ) ? $input['iconset'] : '',
			'icon_shape'         => isset( $input['iconset_type'] ) ? $input['iconset_type'] : '',
			'placements'         => $placements,
			'placement_shapes'   => $placementShapes,
			'networks'           => $networks,
			'share_templates'    => isset( $input['share_templates'] ) && is_array( $input['share_templates'] )
				? $input['share_templates']
				: array(),
			'excluded_content'   => isset( $input['excludes'] ) ? $input['excludes'] : '',
			'analytics_enabled'  => isset( $input['g_analytics'] )
				? LegacyTruthiness::isTruthy( $input['g_analytics'] )
				: false,
			'auto_hide_enabled'  => isset( $input['auto_hide_btn'] )
				? LegacyTruthiness::isTruthy( $input['auto_hide_btn'] )
				: false,
			'preserve_url_port'  => isset( $input['use_port'] )
				? LegacyTruthiness::isTruthy( $input['use_port'] )
				: false,
			'no_follow'          => isset( $input['nofollow'] )
				? LegacyTruthiness::isTruthy( $input['nofollow'] )
				: false,
		);
	}

	public function toLegacySubmission( Settings $settings, array $input ) {
		$sanitized = array();
		$literalFields = array(
			'icons',
			'show_left',
			'show_right',
			'show_before_post',
			'show_after_post',
		);
		$booleanFields = array(
			'g_analytics'  => $settings->analyticsEnabled(),
			'auto_hide_btn' => $settings->autoHideEnabled(),
			'use_port'      => $settings->preserveUrlPort(),
			'nofollow'      => $settings->noFollow(),
		);
		$placementStates = $settings->placements();

		foreach ( $input as $key => $value ) {
			if ( 'show_in' === $key && is_array( $value ) ) {
				foreach ( $value as $legacyPlacement => $enabled ) {
					$canonicalPlacement = isset( $this->placements[ $legacyPlacement ] )
						? $this->placements[ $legacyPlacement ]
						: null;
					if (
						$enabled &&
						( null === $canonicalPlacement || ! empty( $placementStates[ $canonicalPlacement ] ) )
					) {
						$sanitized['show_in'][ $legacyPlacement ] = '1';
					}
				}
			} elseif ( 'share_templates' === $key && is_array( $value ) ) {
				$sanitized['share_templates'] = $settings->shareTemplates();
			} elseif ( 'title' === $key ) {
				$sanitized['title'] = $settings->title();
			} elseif ( 'excludes' === $key ) {
				$sanitized['excludes'] = $settings->excludedContent();
			} elseif ( 'iconset' === $key ) {
				$sanitized['iconset'] = sanitize_key( $value );
			} elseif ( in_array( $key, $literalFields, true ) ) {
				$sanitized[ $key ] = $value;
			} elseif ( isset( $booleanFields[ $key ] ) && $booleanFields[ $key ] ) {
				$sanitized[ $key ] = true;
			} elseif ( ! array_key_exists( $key, $booleanFields ) && isset( $input[ $key ] ) && $input[ $key ] ) {
				$sanitized[ $key ] = true;
			}
		}

		return $sanitized;
	}
}
