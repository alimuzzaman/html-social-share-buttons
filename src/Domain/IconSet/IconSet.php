<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet;

use InvalidArgumentException;

final class IconSet {
	private $id;
	private $label;
	private $stylesheet;
	private $preview;
	private $shapes;
	private $iconFiles;

	public function __construct(
		$id,
		$label,
		$stylesheet,
		$preview,
		array $shapes,
		array $iconFiles
	) {
		$id = (string) $id;
		$label = (string) $label;
		$stylesheet = (string) $stylesheet;
		$preview = (string) $preview;

		if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $id ) ) {
			throw new InvalidArgumentException( 'Icon-set IDs must be lowercase slugs.' );
		}
		if ( '' === trim( $label ) ) {
			throw new InvalidArgumentException( 'Icon-set labels cannot be empty.' );
		}
		$this->assertFileName( $stylesheet );
		$this->assertFileName( $preview );

		$normalizedShapes = array();
		foreach ( $shapes as $shape ) {
			$shape = (string) $shape;
			if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $shape ) ) {
				throw new InvalidArgumentException( 'Icon-set shapes must be lowercase slugs.' );
			}
			$normalizedShapes[ $shape ] = $shape;
		}
		if ( empty( $normalizedShapes ) ) {
			throw new InvalidArgumentException( 'An icon set must support at least one shape.' );
		}

		$normalizedIcons = array();
		foreach ( $iconFiles as $networkId => $fileName ) {
			$networkId = (string) $networkId;
			if ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $networkId ) ) {
				throw new InvalidArgumentException( 'Icon network IDs must be lowercase slugs.' );
			}
			$this->assertFileName( (string) $fileName );
			$normalizedIcons[ $networkId ] = (string) $fileName;
		}
		if ( empty( $normalizedIcons ) ) {
			throw new InvalidArgumentException( 'An icon set must define at least one icon.' );
		}

		$this->id = $id;
		$this->label = $label;
		$this->stylesheet = $stylesheet;
		$this->preview = $preview;
		$this->shapes = array_values( $normalizedShapes );
		$this->iconFiles = $normalizedIcons;
	}

	public function id() {
		return $this->id;
	}

	public function label() {
		return $this->label;
	}

	public function stylesheet() {
		return $this->stylesheet;
	}

	public function preview() {
		return $this->preview;
	}

	public function shapes() {
		return $this->shapes;
	}

	public function iconFiles() {
		return $this->iconFiles;
	}

	public function hasIcon( $networkId ) {
		return isset( $this->iconFiles[ (string) $networkId ] );
	}

	public function iconFile( $networkId ) {
		$networkId = (string) $networkId;
		if ( ! isset( $this->iconFiles[ $networkId ] ) ) {
			throw new InvalidArgumentException( 'The icon set does not contain this network.' );
		}

		return $this->iconFiles[ $networkId ];
	}

	private function assertFileName( $fileName ) {
		if (
			'' === $fileName ||
			basename( $fileName ) !== $fileName ||
			! preg_match( '/^[A-Za-z0-9][A-Za-z0-9._-]*$/', $fileName )
		) {
			throw new InvalidArgumentException( 'Icon-set asset values must be plain file names.' );
		}
	}
}
