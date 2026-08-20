<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings;

use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsCodec;
use Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet\IconSetSelectionPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;

/**
 * The storage codec for the durable WordPress option shape.
 *
 * Its field names intentionally mirror the pre-rewrite option. This is an
 * infrastructure concern, not a legacy runtime dependency.
 */
final class OptionSettingsCodec implements SettingsCodec {
	public function decode( array $stored ) {
		return $this->fromArray( $stored );
	}

	public function encode( Settings $settings, array $original ) {
		return $this->toArray( $settings, $original );
	}

	public function fromArray( array $stored ) {
		$defaults = SettingsDefaults::create();
		$showIn = isset( $stored['show_in'] ) && is_array( $stored['show_in'] )
			? $stored['show_in']
			: array();
		$icons = isset( $stored['icons'] ) && is_array( $stored['icons'] )
			? $stored['icons']
			: array();
		$profileLinks = isset( $stored['profile_links'] ) && is_array( $stored['profile_links'] )
			? $stored['profile_links']
			: array();
		$profileLinkPlacements = isset( $stored['profile_link_placements'] ) && is_array( $stored['profile_link_placements'] )
			? $stored['profile_link_placements']
			: array();

		if ( array_key_exists( 'twitter', $icons ) && ! array_key_exists( 'x', $icons ) ) {
			$icons['x'] = $icons['twitter'];
		}
		if ( array_key_exists( 'twitter', $profileLinks ) && ! array_key_exists( 'x', $profileLinks ) ) {
			$profileLinks['x'] = $profileLinks['twitter'];
			unset( $profileLinks['twitter'] );
		}

		return new Settings(
			isset( $stored['title'] ) ? $stored['title'] : $defaults->title(),
			isset( $stored['iconset'] ) ? $stored['iconset'] : IconSetSelectionPolicy::LEGACY_DEFAULT_ID,
			isset( $stored['iconset_type'] ) ? $stored['iconset_type'] : $defaults->defaultIconShape(),
			array(
				Placement::LEFT           => $this->toBoolean( isset( $showIn['show_left'] ) ? $showIn['show_left'] : false ),
				Placement::RIGHT          => $this->toBoolean( isset( $showIn['show_right'] ) ? $showIn['show_right'] : false ),
				Placement::BEFORE_CONTENT => $this->toBoolean( isset( $showIn['show_before_post'] ) ? $showIn['show_before_post'] : false ),
				Placement::AFTER_CONTENT  => $this->toBoolean( isset( $showIn['show_after_post'] ) ? $showIn['show_after_post'] : false ),
			),
			array(
				Placement::LEFT           => isset( $stored['show_left'] ) ? $stored['show_left'] : 'square',
				Placement::RIGHT          => isset( $stored['show_right'] ) ? $stored['show_right'] : 'square',
				Placement::BEFORE_CONTENT => isset( $stored['show_before_post'] ) ? $stored['show_before_post'] : 'square',
				Placement::AFTER_CONTENT  => isset( $stored['show_after_post'] ) ? $stored['show_after_post'] : 'square',
			),
			$this->networkStates( $icons ),
			isset( $stored['share_templates'] ) && is_array( $stored['share_templates'] ) ? $stored['share_templates'] : array(),
			isset( $stored['excludes'] ) ? $stored['excludes'] : '',
			$this->toBoolean( isset( $stored['g_analytics'] ) ? $stored['g_analytics'] : false ),
			$this->toBoolean( isset( $stored['auto_hide_btn'] ) ? $stored['auto_hide_btn'] : false ),
			$this->toBoolean( isset( $stored['use_port'] ) ? $stored['use_port'] : false ),
			$this->toBoolean( isset( $stored['nofollow'] ) ? $stored['nofollow'] : false ),
			$this->profileLinks( $profileLinks ),
			$this->profileLinkPlacements( $profileLinkPlacements ),
			$this->audienceValue( $stored, 'show_for_current_user' ),
			$this->audienceValue( $stored, 'show_for_logged_in_user' ),
			$this->audienceValue( $stored, 'show_for_logged_out_user' )
		);
	}

	public function toArray( Settings $settings, array $original = array() ) {
		$placements = $settings->placements();
		$shapes = $settings->placementShapes();

		$original['title'] = $settings->title();
		$original['iconset'] = $settings->iconSetId();
		$original['excludes'] = $settings->excludedContent();
		if ( array_key_exists( 'iconset_type', $original ) ) {
			$original['iconset_type'] = $settings->defaultIconShape();
		}
		$original['show_in'] = array_filter(
			array(
				'show_left'        => ! empty( $placements[ Placement::LEFT ] ) ? '1' : null,
				'show_right'       => ! empty( $placements[ Placement::RIGHT ] ) ? '1' : null,
				'show_before_post' => ! empty( $placements[ Placement::BEFORE_CONTENT ] ) ? '1' : null,
				'show_after_post'  => ! empty( $placements[ Placement::AFTER_CONTENT ] ) ? '1' : null,
			)
		);
		$original['show_left'] = isset( $shapes[ Placement::LEFT ] ) ? $shapes[ Placement::LEFT ] : 'square';
		$original['show_right'] = isset( $shapes[ Placement::RIGHT ] ) ? $shapes[ Placement::RIGHT ] : 'square';
		$original['show_before_post'] = isset( $shapes[ Placement::BEFORE_CONTENT ] ) ? $shapes[ Placement::BEFORE_CONTENT ] : 'square';
		$original['show_after_post'] = isset( $shapes[ Placement::AFTER_CONTENT ] ) ? $shapes[ Placement::AFTER_CONTENT ] : 'square';
		$original['icons'] = $this->storedNetworkStates(
			$settings->networkStates(),
			isset( $original['icons'] ) && is_array( $original['icons'] ) ? $original['icons'] : array()
		);
		$original['share_templates'] = $settings->shareTemplates();
		$this->writeProfileLinks( $original, $settings->profileLinks() );
		$this->writeProfileLinkPlacements( $original, $settings->profileLinkPlacements() );
		$this->writeBoolean( $original, 'g_analytics', $settings->analyticsEnabled() );
		$this->writeBoolean( $original, 'auto_hide_btn', $settings->autoHideEnabled() );
		$this->writeBoolean( $original, 'use_port', $settings->preserveUrlPort() );
		$this->writeBoolean( $original, 'nofollow', $settings->noFollow() );
		$this->writeAudience( $original, 'show_for_current_user', $settings->showForCurrentUser() );
		$this->writeAudience( $original, 'show_for_logged_in_user', $settings->showForLoggedInUser() );
		$this->writeAudience( $original, 'show_for_logged_out_user', $settings->showForLoggedOutUser() );

		return $original;
	}

	private function networkStates( array $stored ) {
		$states = array();
		foreach ( $stored as $networkId => $value ) {
			$states[ (string) $networkId ] = $this->toBoolean( $value );
		}

		return $states;
	}

	private function profileLinks( array $stored ) {
		$links = array();
		foreach ( $stored as $networkId => $url ) {
			if ( is_string( $url ) && '' !== trim( $url ) ) {
				$links[ (string) $networkId ] = trim( $url );
			}
		}

		return $links;
	}

	private function writeProfileLinks( array &$target, array $profileLinks ) {
		if ( empty( $profileLinks ) ) {
			unset( $target['profile_links'] );

			return;
		}

		$original = isset( $target['profile_links'] ) && is_array( $target['profile_links'] )
			? $target['profile_links']
			: array();
		if ( isset( $profileLinks['x'] ) && array_key_exists( 'twitter', $original ) && ! array_key_exists( 'x', $original ) ) {
			$profileLinks['twitter'] = $profileLinks['x'];
			unset( $profileLinks['x'] );
		}
		$target['profile_links'] = $profileLinks;
	}

	private function profileLinkPlacements( array $stored ) {
		$placements = array();
		$keys = array(
			'show_left'        => Placement::LEFT,
			'show_right'       => Placement::RIGHT,
			'show_before_post' => Placement::BEFORE_CONTENT,
			'show_after_post'  => Placement::AFTER_CONTENT,
		);
		foreach ( $keys as $storedKey => $placement ) {
			if ( isset( $stored[ $storedKey ] ) && 'none' === (string) $stored[ $storedKey ] ) {
				$placements[ $placement ] = 'none';
			}
		}

		return $placements;
	}

	private function writeProfileLinkPlacements( array &$target, array $placements ) {
		$keys = array(
			Placement::LEFT           => 'show_left',
			Placement::RIGHT          => 'show_right',
			Placement::BEFORE_CONTENT => 'show_before_post',
			Placement::AFTER_CONTENT  => 'show_after_post',
		);
		/* Keep extension-owned nested keys intact when canonical settings save. */
		$stored = isset( $target['profile_link_placements'] ) && is_array( $target['profile_link_placements'] )
			? $target['profile_link_placements']
			: array();
		foreach ( $keys as $placement => $storedKey ) {
			unset( $stored[ $storedKey ] );
			if ( isset( $placements[ $placement ] ) && 'none' === $placements[ $placement ] ) {
				$stored[ $storedKey ] = 'none';
			}
		}
		if ( empty( $stored ) ) {
			unset( $target['profile_link_placements'] );

			return;
		}
		$target['profile_link_placements'] = $stored;
	}

	private function storedNetworkStates( array $states, array $original ) {
		$stored = $original;
		foreach ( $states as $networkId => $enabled ) {
			if ( 'x' === $networkId && array_key_exists( 'twitter', $original ) && ! array_key_exists( 'x', $original ) ) {
				if ( $enabled ) {
					$stored['twitter'] = $this->toBoolean( $original['twitter'] ) ? $original['twitter'] : '1';
				} else {
					unset( $stored['twitter'] );
				}

				continue;
			}

			if ( $enabled ) {
				$stored[ $networkId ] = array_key_exists( $networkId, $original ) && $this->toBoolean( $original[ $networkId ] ) ? $original[ $networkId ] : '1';
			} elseif ( ! array_key_exists( $networkId, $original ) || $this->toBoolean( $original[ $networkId ] ) ) {
				unset( $stored[ $networkId ] );
			}
		}

		return $stored;
	}

	private function toBoolean( $value ) {
		return OptionSettingsTruthiness::isTruthy( $value );
	}

	private function writeBoolean( array &$target, $key, $enabled ) {
		if ( $enabled ) {
			$target[ $key ] = true;

			return;
		}
		unset( $target[ $key ] );
	}

	private function audienceValue( array $stored, $key ) {
		return array_key_exists( $key, $stored )
			? $this->toBoolean( $stored[ $key ] )
			: true;
	}

	private function writeAudience( array &$target, $key, $enabled ) {
		if ( array_key_exists( $key, $target ) || ! $enabled ) {
			$target[ $key ] = (bool) $enabled;
		}
	}
}
