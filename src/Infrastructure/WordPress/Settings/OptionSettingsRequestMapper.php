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
	private $coreNetworkKeys = array(
		'facebook',
		'x',
		'twitter',
		'linkedin',
		'pinterest',
		'telegram',
		'bluesky',
		'whatsapp',
		'reddit',
		'copy',
		'native',
		'mail',
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
			'use_browser_url'          => isset( $input['use_browser_url'] )
			? OptionSettingsTruthiness::isStrictBoolean( $input['use_browser_url'] )
			: false,
			'show_for_current_user'    => $this->audienceInput( $input, 'show_for_current_user' ),
			'show_for_logged_in_user'  => $this->audienceInput( $input, 'show_for_logged_in_user' ),
			'show_for_logged_out_user' => $this->audienceInput( $input, 'show_for_logged_out_user' ),
		);
	}

	public function toStoredSubmission( Settings $settings, array $input ) {
		return $this->mapStoredSubmission( $settings, $input, false );
	}

	/**
	 * Replace core-owned form fields while retaining opaque extension data.
	 */
	public function toStoredReplacement( Settings $settings, array $input, array $stored ) {
		// Retired controls are owned by stored data, never by admin input.
		unset( $input['g_analytics'], $input['use_port'] );
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
			'auto_hide_btn',
			'nofollow',
			'use_browser_url',
			'show_for_current_user',
			'show_for_logged_in_user',
			'show_for_logged_out_user',
		);
		foreach ( $scalarFields as $field ) {
			unset( $replacement[ $field ] );
		}

		$placementKeys = array_keys( $this->placements );
		$this->removeOwnedNestedKeys( $replacement, 'show_in', $placementKeys );
		$this->removeOwnedNestedKeys( $replacement, 'profile_link_placements', $placementKeys );
		$this->removeOwnedNestedKeys( $replacement, 'icons', $this->coreNetworkKeys );
		$this->removeOwnedNestedKeys( $replacement, 'share_templates', $this->coreNetworkKeys );
		$this->removeOwnedNestedKeys( $replacement, 'profile_links', $this->coreNetworkKeys );

		foreach ( $this->mapStoredSubmission( $settings, $input, true ) as $key => $value ) {
			if ( is_array( $value ) && isset( $replacement[ $key ] ) && is_array( $replacement[ $key ] ) ) {
				$replacement[ $key ] = array_replace( $replacement[ $key ], $value );
			} else {
				$replacement[ $key ] = $value;
			}
		}

		return $replacement;
	}

	private function mapStoredSubmission( Settings $settings, array $input, $strictAdmin ) {
		$sanitized = array();
		$booleanFields = array(
			'g_analytics'     => $settings->analyticsEnabled(),
			'auto_hide_btn'   => $settings->autoHideEnabled(),
			'use_port'        => $settings->preserveUrlPort(),
			'nofollow'        => $settings->noFollow(),
			'use_browser_url' => $settings->useBrowserUrl(),
		);
		$audienceFields = array(
			'show_for_current_user',
			'show_for_logged_in_user',
			'show_for_logged_out_user',
		);
		$placementStates = $settings->placements();
		$placementShapes = $settings->placementShapes();

		foreach ( $input as $key => $value ) {
			if ( 'show_in' === $key ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $storedPlacement => $enabled ) {
						if ( ! is_string( $storedPlacement ) || 1 !== preg_match( '/^[a-z0-9_-]+$/i', $storedPlacement ) ) {
							continue;
						}
						$canonicalPlacement = isset( $this->placements[ $storedPlacement ] )
							? $this->placements[ $storedPlacement ]
							: null;
						if ( $strictAdmin && null === $canonicalPlacement ) {
							continue;
						}
						if ( $enabled && ( null === $canonicalPlacement || ! empty( $placementStates[ $canonicalPlacement ] ) ) ) {
							$sanitized['show_in'][ $storedPlacement ] = '1';
						}
					}
				}
			} elseif ( 'icons' === $key ) {
				if ( is_array( $value ) ) {
					$icons = $this->sanitizeIcons( $value, $strictAdmin );
					if ( ! empty( $icons ) ) {
						$sanitized['icons'] = $icons;
					}
				}
			} elseif ( 'share_templates' === $key ) {
				if ( is_array( $value ) ) {
					$sanitized['share_templates'] = $settings->shareTemplates();
				}
			} elseif ( 'profile_links' === $key ) {
				if ( is_array( $value ) ) {
					$profileLinks = $settings->profileLinks();
					if ( isset( $profileLinks['x'] ) && array_key_exists( 'twitter', $value ) && ! array_key_exists( 'x', $value ) ) {
						$profileLinks['twitter'] = $profileLinks['x'];
						unset( $profileLinks['x'] );
					}
					if ( ! empty( $profileLinks ) ) {
						$sanitized['profile_links'] = $profileLinks;
					}
				}
			} elseif ( 'profile_link_placements' === $key ) {
				if ( is_array( $value ) ) {
					$placementModes = array();
					foreach ( $this->placements as $storedPlacement => $canonicalPlacement ) {
						if ( 'none' === $settings->profileLinkMode( $canonicalPlacement ) ) {
							$placementModes[ $storedPlacement ] = 'none';
						}
					}
					if ( ! empty( $placementModes ) ) {
						$sanitized['profile_link_placements'] = $placementModes;
					}
				}
			} elseif ( in_array( $key, $audienceFields, true ) ) {
				$sanitized[ $key ] = $this->audienceInput( $input, $key );
			} elseif ( 'title' === $key ) {
				$sanitized['title'] = $settings->title();
			} elseif ( 'excludes' === $key ) {
				$sanitized['excludes'] = $settings->excludedContent();
			} elseif ( 'iconset' === $key ) {
				if ( $strictAdmin ) {
					$sanitized['iconset'] = $settings->iconSetId();
				} else {
					$iconSet = is_scalar( $value ) ? sanitize_key( (string) $value ) : '';
					$sanitized['iconset'] = '' !== $iconSet ? $iconSet : $settings->iconSetId();
				}
			} elseif ( 'iconset_type' === $key ) {
				$sanitized['iconset_type'] = $strictAdmin
					? $settings->defaultIconShape()
					: $this->sanitizeShape( $value, $settings->defaultIconShape() );
			} elseif ( 'button_appearance' === $key ) {
				$sanitized['button_appearance'] = $settings->buttonAppearance();
			} elseif ( isset( $this->placements[ $key ] ) ) {
				$canonicalPlacement = $this->placements[ $key ];
				$fallbackShape = isset( $placementShapes[ $canonicalPlacement ] )
					? $placementShapes[ $canonicalPlacement ]
					: $settings->defaultIconShape();
				$sanitized[ $key ] = $strictAdmin
					? $fallbackShape
					: $this->sanitizeShape( $value, $fallbackShape );
			} elseif ( isset( $booleanFields[ $key ] ) ) {
				if ( $booleanFields[ $key ] ) {
					$sanitized[ $key ] = true;
				}
			} elseif ( ! $strictAdmin && is_string( $key ) && 1 === preg_match( '/^[a-z0-9_-]+$/i', $key ) && isset( $input[ $key ] ) && $input[ $key ] ) {
				$sanitized[ $key ] = true;
			}
		}

		return $sanitized;
	}

	private function sanitizeShape( $value, $fallback ) {
		if ( ! is_scalar( $value ) ) {
			return $fallback;
		}
		$shape = trim( (string) $value );
		if ( '' === $shape || 1 !== preg_match( '/^[a-z0-9_-]+$/i', $shape ) ) {
			return $fallback;
		}

		return strtolower( $shape );
	}

	private function sanitizeIcons( array $icons, $strictAdmin ) {
		$sanitized = array();
		foreach ( $icons as $networkKey => $networkValue ) {
			if ( ! is_string( $networkKey ) || 1 !== preg_match( '/^[a-z0-9_-]+$/i', $networkKey ) ) {
				continue;
			}
			$cleanKey = strtolower( $networkKey );
			if ( $strictAdmin ) {
				if ( ! in_array( $cleanKey, $this->coreNetworkKeys, true ) ) {
					continue;
				}
				if ( is_bool( $networkValue ) || 0 === $networkValue || 1 === $networkValue || in_array( $networkValue, array( '0', '1', 'on' ), true ) ) {
					$sanitized[ $cleanKey ] = $networkValue;
				}
				continue;
			}

			if ( is_bool( $networkValue ) || is_int( $networkValue ) ) {
				$sanitized[ $cleanKey ] = $networkValue;
			} elseif ( is_string( $networkValue ) ) {
				$cleaned = function_exists( 'sanitize_text_field' )
					? sanitize_text_field( $networkValue )
					: trim( (string) preg_replace( '/<(script|style)\b[^>]*>.*?<\/\1>|<[^>]*>/is', '', $networkValue ) );
				if ( '' !== $cleaned || '' === trim( $networkValue ) ) {
					$sanitized[ $cleanKey ] = $cleaned;
				}
			}
		}

		return $sanitized;
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
