<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering;

use InvalidArgumentException;

final class RenderRequest {
	private $iconSetId;
	private $shape;
	private $placement;
	private $heading;
	private $networkIds;
	private $templateOverrides;
	private $permalinkOverride;
	private $noFollow;
	private $profileLinks;

	public function __construct(
		$iconSetId,
		$shape,
		$placement,
		$heading,
		array $networkIds,
		array $templateOverrides = array(),
		$permalinkOverride = '',
		$noFollow = false,
		array $profileLinks = array()
	) {
		RenderPlacement::assertValid( $placement );

		$normalizedNetworkIds = array();
		foreach ( $networkIds as $networkId ) {
			$networkId = (string) $networkId;
			if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $networkId ) ) {
				throw new InvalidArgumentException( 'Render network IDs must be lowercase slugs.' );
			}
			$normalizedNetworkIds[ $networkId ] = $networkId;
		}

		$normalizedOverrides = array();
		foreach ( $templateOverrides as $networkId => $template ) {
			if ( is_string( $template ) && '' !== trim( $template ) ) {
				$normalizedOverrides[ (string) $networkId ] = $template;
			}
		}

		$normalizedProfileLinks = array();
		foreach ( $profileLinks as $networkId => $url ) {
			$networkId = (string) $networkId;
			if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $networkId ) ) {
				throw new InvalidArgumentException( 'Profile-link network IDs must be lowercase slugs.' );
			}
			if ( is_string( $url ) && '' !== trim( $url ) ) {
				$normalizedProfileLinks[ $networkId ] = $this->normalizeProfileUrl(
					$networkId,
					trim( $url )
				);
			}
		}

		$this->iconSetId = (string) $iconSetId;
		$this->shape = (string) $shape;
		$this->placement = (string) $placement;
		$this->heading = (string) $heading;
		$this->networkIds = array_values( $normalizedNetworkIds );
		$this->templateOverrides = $normalizedOverrides;
		$this->permalinkOverride = (string) $permalinkOverride;
		$this->noFollow = (bool) $noFollow;
		$this->profileLinks = $normalizedProfileLinks;
	}

	public function iconSetId() {
		return $this->iconSetId;
	}

	public function shape() {
		return $this->shape;
	}

	public function placement() {
		return $this->placement;
	}

	public function heading() {
		return $this->heading;
	}

	public function networkIds() {
		return $this->networkIds;
	}

	public function templateOverrides() {
		return $this->templateOverrides;
	}

	public function permalinkOverride() {
		return $this->permalinkOverride;
	}

	public function noFollow() {
		return $this->noFollow;
	}

	public function profileLinks() {
		return $this->profileLinks;
	}

	private function normalizeProfileUrl( $networkId, $url ) {
		if ( preg_match( '/[\r\n]/', $url ) ) {
			throw new InvalidArgumentException( 'Profile-link URLs cannot contain line breaks.' );
		}

		if ( 'mail' === $networkId ) {
			if ( 0 !== stripos( $url, 'mailto:' ) ) {
				throw new InvalidArgumentException( 'Email profile links must use mailto.' );
			}
			$address = substr( $url, 7 );
			if (
				false !== strpos( $address, '?' ) ||
				false !== strpos( $address, '#' ) ||
				preg_match( '/%(?:0a|0d)/i', $address )
			) {
				throw new InvalidArgumentException( 'Email profile links cannot contain headers.' );
			}
			$address = rawurldecode( $address );
			if ( false === filter_var( $address, FILTER_VALIDATE_EMAIL ) ) {
				throw new InvalidArgumentException( 'Email profile links require a valid address.' );
			}

			return 'mailto:' . $address;
		}

		if (
			false === filter_var( $url, FILTER_VALIDATE_URL ) ||
			0 !== stripos( $url, 'https://' )
		) {
			throw new InvalidArgumentException( 'Social profile links must use an absolute HTTPS URL.' );
		}

		return $url;
	}
}
