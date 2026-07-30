<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Settings;

use InvalidArgumentException;

final class SettingsSchema {
	private $networkIds;
	private $iconSetIds;
	private $iconShapes;

	public function __construct( array $networkIds, array $iconSetIds, array $iconShapes ) {
		$this->networkIds = $this->slugs( $networkIds, 'network' );
		$this->iconSetIds = $this->slugs( $iconSetIds, 'icon set' );
		$this->iconShapes = $this->slugs( $iconShapes, 'icon shape' );
	}

	public function networkIds() {
		return $this->networkIds;
	}

	public function iconSetIds() {
		return $this->iconSetIds;
	}

	public function iconShapes() {
		return $this->iconShapes;
	}

	public function placementIds() {
		return Placement::all();
	}

	public function supportsNetwork( $id ) {
		return in_array( (string) $id, $this->networkIds, true );
	}

	public function supportsIconSet( $id ) {
		return in_array( (string) $id, $this->iconSetIds, true );
	}

	public function supportsIconShape( $shape ) {
		return in_array( (string) $shape, $this->iconShapes, true );
	}

	private function slugs( array $values, $label ) {
		$normalized = array();
		foreach ( $values as $value ) {
			$value = (string) $value;
			if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $value ) ) {
				throw new InvalidArgumentException( 'Invalid settings ' . $label . ' identifier.' );
			}
			$normalized[ $value ] = $value;
		}
		if ( empty( $normalized ) ) {
			throw new InvalidArgumentException( 'Settings schemas require at least one ' . $label . '.' );
		}

		return array_values( $normalized );
	}
}
