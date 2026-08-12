<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

/**
 * Translates the established settings form keys to the canonical request.
 */
final class OptionSettingsRequestMapper {
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
		foreach ( $this->placements as $storedKey => $canonicalKey ) {
			$placements[ $canonicalKey ] = isset( $submittedPlacements[ $storedKey ] )
				? OptionSettingsTruthiness::isTruthy( $submittedPlacements[ $storedKey ] )
				: false;
			if ( isset( $input[ $storedKey ] ) ) {
				$placementShapes[ $canonicalKey ] = $input[ $storedKey ];
			}
		}

		$networks = isset( $input['icons'] ) && is_array( $input['icons'] ) ? $input['icons'] : array();
		if ( isset( $networks['twitter'] ) && ! isset( $networks['x'] ) ) {
			$networks['x'] = $networks['twitter'];
		}
		$profileLinks = isset( $input['profile_links'] ) && is_array( $input['profile_links'] )
			? $input['profile_links']
			: array();
		if ( isset( $profileLinks['twitter'] ) && ! isset( $profileLinks['x'] ) ) {
			$profileLinks['x'] = $profileLinks['twitter'];
			unset( $profileLinks['twitter'] );
		}
		$profileLinkPlacements = array();
		$submittedProfileLinkPlacements = isset( $input['profile_link_placements'] ) && is_array( $input['profile_link_placements'] )
			? $input['profile_link_placements']
			: array();
		foreach ( $this->placements as $storedKey => $canonicalPlacement ) {
			if ( isset( $submittedProfileLinkPlacements[ $storedKey ] ) ) {
				$profileLinkPlacements[ $canonicalPlacement ] = $submittedProfileLinkPlacements[ $storedKey ];
			}
		}

		return array(
			'title'                   => isset( $input['title'] ) ? $input['title'] : '',
			'icon_set'                => isset( $input['iconset'] ) ? $input['iconset'] : '',
			'icon_shape'              => isset( $input['iconset_type'] ) ? $input['iconset_type'] : '',
			'placements'              => $placements,
			'placement_shapes'        => $placementShapes,
			'networks'                => $networks,
			'share_templates'         => isset( $input['share_templates'] ) && is_array( $input['share_templates'] )
				? $input['share_templates']
				: array(),
			'profile_links'           => $profileLinks,
			'profile_link_placements' => $profileLinkPlacements,
			'excluded_content'        => isset( $input['excludes'] ) ? $input['excludes'] : '',
			'analytics_enabled'       => isset( $input['g_analytics'] )
				? OptionSettingsTruthiness::isTruthy( $input['g_analytics'] )
				: false,
			'auto_hide_enabled'       => isset( $input['auto_hide_btn'] )
				? OptionSettingsTruthiness::isTruthy( $input['auto_hide_btn'] )
				: false,
			'preserve_url_port'       => isset( $input['use_port'] )
				? OptionSettingsTruthiness::isTruthy( $input['use_port'] )
				: false,
			'no_follow'               => isset( $input['nofollow'] )
				? OptionSettingsTruthiness::isTruthy( $input['nofollow'] )
				: false,
		);
	}

	public function toStoredSubmission( Settings $settings, array $input ) {
		$sanitized = array();
		$literalFields = array(
			'icons',
			'show_left',
			'show_right',
			'show_before_post',
			'show_after_post',
		);
		$booleanFields = array(
			'g_analytics'   => $settings->analyticsEnabled(),
			'auto_hide_btn' => $settings->autoHideEnabled(),
			'use_port'      => $settings->preserveUrlPort(),
			'nofollow'      => $settings->noFollow(),
		);
		$placementStates = $settings->placements();

		foreach ( $input as $key => $value ) {
			if ( 'show_in' === $key && is_array( $value ) ) {
				foreach ( $value as $storedPlacement => $enabled ) {
					$canonicalPlacement = isset( $this->placements[ $storedPlacement ] )
						? $this->placements[ $storedPlacement ]
						: null;
					if ( $enabled && ( null === $canonicalPlacement || ! empty( $placementStates[ $canonicalPlacement ] ) ) ) {
						$sanitized['show_in'][ $storedPlacement ] = '1';
					}
				}
			} elseif ( 'share_templates' === $key && is_array( $value ) ) {
				$sanitized['share_templates'] = $settings->shareTemplates();
			} elseif ( 'profile_links' === $key && is_array( $value ) ) {
				$profileLinks = $settings->profileLinks();
				if ( isset( $profileLinks['x'] ) && array_key_exists( 'twitter', $value ) && ! array_key_exists( 'x', $value ) ) {
					$profileLinks['twitter'] = $profileLinks['x'];
					unset( $profileLinks['x'] );
				}
				if ( ! empty( $profileLinks ) ) {
					$sanitized['profile_links'] = $profileLinks;
				}
			} elseif ( 'profile_link_placements' === $key && is_array( $value ) ) {
				$placementModes = array();
				foreach ( $this->placements as $storedPlacement => $canonicalPlacement ) {
					if ( 'none' === $settings->profileLinkMode( $canonicalPlacement ) ) {
						$placementModes[ $storedPlacement ] = 'none';
					}
				}
				if ( ! empty( $placementModes ) ) {
					$sanitized['profile_link_placements'] = $placementModes;
				}
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
