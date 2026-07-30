<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Domain\Network;

use InvalidArgumentException;
use LogicException;

final class NetworkRegistry {
	private $networks = array();

	public function register( Network $network ) {
		$id = $network->id();
		if ( isset( $this->networks[ $id ] ) ) {
			throw new LogicException( 'A network with this ID is already registered.' );
		}

		$this->networks[ $id ] = $network;
	}

	public function has( $id ) {
		return isset( $this->networks[ (string) $id ] );
	}

	public function get( $id ) {
		$id = (string) $id;
		if ( ! isset( $this->networks[ $id ] ) ) {
			throw new InvalidArgumentException( 'Unknown network ID.' );
		}

		return $this->networks[ $id ];
	}

	public function all() {
		return array_values( $this->networks );
	}

	public function ids() {
		return array_keys( $this->networks );
	}
}
