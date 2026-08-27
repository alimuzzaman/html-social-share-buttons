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
			'title'                    => isset( $input['title'] ) ? $input['title'] : '',
			'icon_set'                 => isset( $input['iconset'] ) ? $input['iconset'] : '',
			'button_appearance'        => isset( $input['button_appearance'] ) ? $input['button_appearance'] : '',
			'icon_shape'               => isset( $input['iconset_type'] ) ? $input['iconset_type'] : '',
			'placements'               => $placements,
			'placement_shapes'         => $placementShapes,
			'networks'                 => $networks,
			'share_templates'          => isset( $input['share_templates'] ) && is_array( $input['share_templates'] )
				? $input['share_templates']
				: array(),
			'profile_links'            => $profileLinks,
			'profile_link_placements'  => $profileLinkPlacements,
			'excluded_content'         => isset( $input['excludes'] ) ? $input['excludes'] : '',
			'analytics_enabled'        => isset( $input['g_analytics'] )
				? OptionSettingsTruthiness::isTruthy( $input['g_analytics'] )
				: false,
			'auto_hide_enabled'        => isset( $input['auto_hide_btn'] )
				? OptionSettingsTruthiness::isTruthy( $input['auto_hide_btn'] )
				: false,
			'preserve_url_port'        => isset( $input['use_port'] )
				? OptionSettingsTruthiness::isTruthy( $input['use_port'] )
				: false,
			'no_follow'                => isset( $input['nofollow'] )
			? OptionSettingsTruthiness::isTruthy( $input['nofollow'] )
			: false,
			'show_for_current_user'    => $this->audienceInput( $input, 'show_for_current_user' ),
			'show_for_logged_in_user'  => $this->audienceInput( $input, 'show_for_logged_in_user' ),
			'show_for_logged_out_user' => $this->audienceInput( $input, 'show_for_logged_out_user' ),
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
		$audienceFields = array(
			'show_for_current_user',
			'show_for_logged_in_user',
			'show_for_logged_out_user',
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
			} elseif ( in_array( $key, $audienceFields, true ) ) {
				$sanitized[ $key ] = $this->audienceInput( $input, $key );
			} elseif ( 'title' === $key ) {
				$sanitized['title'] = $settings->title();
			} elseif ( 'excludes' === $key ) {
				$sanitized['excludes'] = $settings->excludedContent();
			} elseif ( 'iconset' === $key ) {
				$sanitized['iconset'] = sanitize_key( $value );
			} elseif ( 'button_appearance' === $key ) {
				$sanitized['button_appearance'] = $settings->buttonAppearance();
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

	/**
	 * Replace core-owned form fields while retaining opaque extension data.
	 */
	public function toStoredReplacement( Settings $settings, array $input, array $stored ) {
		$replacement = $stored;
		$scalarFields = array(
			'title',
			'iconset',
			'button_appearance',
			'iconset_type',
			'excludes',
			'show_left',
			'show_right',
			'show_before_post',
			'show_after_post',
			'g_analytics',
			'auto_hide_btn',
			'use_port',
			'nofollow',
			'show_for_current_user',
			'show_for_logged_in_user',
			'show_for_logged_out_user',
		);
		foreach ( $scalarFields as $field ) {
			unset( $replacement[ $field ] );
		}

		$placementKeys = array_keys( $this->placements );
		$networkKeys = array( 'facebook', 'x', 'twitter', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' );
		$this->removeOwnedNestedKeys( $replacement, 'show_in', $placementKeys );
		$this->removeOwnedNestedKeys( $replacement, 'profile_link_placements', $placementKeys );
		$this->removeOwnedNestedKeys( $replacement, 'icons', $networkKeys );
		$this->removeOwnedNestedKeys( $replacement, 'share_templates', $networkKeys );
		$this->removeOwnedNestedKeys( $replacement, 'profile_links', $networkKeys );

		foreach ( $this->toStoredSubmission( $settings, $input ) as $key => $value ) {
			if ( is_array( $value ) && isset( $replacement[ $key ] ) && is_array( $replacement[ $key ] ) ) {
				$replacement[ $key ] = array_replace( $replacement[ $key ], $value );
			} else {
				$replacement[ $key ] = $value;
			}
		}

		return $replacement;
	}

	private function removeOwnedNestedKeys( array &$stored, $field, array $ownedKeys ) {
		if ( ! isset( $stored[ $field ] ) || ! is_array( $stored[ $field ] ) ) {
			unset( $stored[ $field ] );

			return;
		}
		foreach ( $ownedKeys as $key ) {
			unset( $stored[ $field ][ $key ] );
		}
		if ( empty( $stored[ $field ] ) ) {
			unset( $stored[ $field ] );
		}
	}

	private function audienceInput( array $input, $key ) {
		return array_key_exists( $key, $input )
			? OptionSettingsTruthiness::isTruthy( $input[ $key ] )
			: true;
	}
}
