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

	public function __construct(
		$iconSetId,
		$shape,
		$placement,
		$heading,
		array $networkIds,
		array $templateOverrides = array(),
		$permalinkOverride = '',
		$noFollow = false
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

		$this->iconSetId = (string) $iconSetId;
		$this->shape = (string) $shape;
		$this->placement = (string) $placement;
		$this->heading = (string) $heading;
		$this->networkIds = array_values( $normalizedNetworkIds );
		$this->templateOverrides = $normalizedOverrides;
		$this->permalinkOverride = (string) $permalinkOverride;
		$this->noFollow = (bool) $noFollow;
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
}
