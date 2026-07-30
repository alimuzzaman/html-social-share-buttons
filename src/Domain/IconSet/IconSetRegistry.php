<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\IconSet;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\NetworkRegistry;
use InvalidArgumentException;
use LogicException;

final class IconSetRegistry {
	private $networks;
	private $iconSets = array();

	public function __construct( NetworkRegistry $networks ) {
		$this->networks = $networks;
	}

	public function register( IconSet $iconSet ) {
		$id = $iconSet->id();
		if ( isset( $this->iconSets[ $id ] ) ) {
			throw new LogicException( 'An icon set with this ID is already registered.' );
		}

		foreach ( array_keys( $iconSet->iconFiles() ) as $networkId ) {
			if ( ! $this->networks->has( $networkId ) ) {
				throw new InvalidArgumentException( 'The icon set references an unknown network.' );
			}
		}

		$this->iconSets[ $id ] = $iconSet;
	}

	public function has( $id ) {
		return isset( $this->iconSets[ (string) $id ] );
	}

	public function get( $id ) {
		$id = (string) $id;
		if ( ! isset( $this->iconSets[ $id ] ) ) {
			throw new InvalidArgumentException( 'Unknown icon-set ID.' );
		}

		return $this->iconSets[ $id ];
	}

	public function all() {
		return array_values( $this->iconSets );
	}

	public function ids() {
		return array_keys( $this->iconSets );
	}
}
