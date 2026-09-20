<?php

namespace Mediavine\Grow;

use ArrayAccess;
use ArrayIterator;
use InvalidArgumentException;
use IteratorAggregate;
use RuntimeException;
use Serializable;
use Traversable;

/**
 * Data object for communicating individual Share Count URL service counts.
 */
class Share_Count_Url_Counts implements ArrayAccess, IteratorAggregate, Serializable {

	/** The API may return keys with this suffix. */
	private const API_SUFFIX = '_share_count';

	/** @var int */
	private $count_total;

	/** @var array<string, int> */
	private $network_counts = [
		'facebook'  => 0,
		'pinterest' => 0,
		'reddit'    => 0,
		'twitter'   => 0,
	];

	/**
	 * Set up the instance.
	 *
	 * @param array $counts An array containing individual site counts.
	 */
	public function __construct( array $counts ) {
		$this->load_counts( $counts );
	}

	/**
	 * Create a new instance with counts from another instance added to the current.
	 *
	 * @param Share_Count_Url_Counts $share_counts
	 *
	 * @return Share_Count_Url_Counts
	 */
	public function with_sum( Share_Count_Url_Counts $share_counts ) : Share_Count_Url_Counts {
		$counts            = $this->get_counts();
		$additional_counts = $share_counts->get_counts();

		foreach ( $counts as $network => &$network_count ) {
			$add_total      = $additional_counts[ $network ] ?? 0;
			$network_count += $add_total;
		}

		return new Share_Count_Url_Counts( $counts );
	}

	/**
	 * Get all available share counts.
	 *
	 * @return array{facebook: int, pinterest: int, reddit: int, twitter: int}
	 */
	public function get_counts() : array {
		return $this->network_counts;
	}

	/**
	 * Get the total number of network shares API.
	 *
	 * @return int
	 */
	public function get_count_total() : int {
		if ( null === $this->count_total ) {
			$this->count_total = array_sum( $this->network_counts );
		}

		return $this->count_total;
	}

	/**
	 * Given an array of network counts, load them into the object state.
	 *
	 * @param array $counts Individual network counts.
	 */
	private function load_counts( array $counts ) : void {
		foreach ( $this->network_counts as $network => $network_count_current ) {
			$network_count = $counts[ $network ] ?? $counts[ $network . self::API_SUFFIX ] ?? null;
			if ( null !== $network_count ) {
				$this->set_count( $network, $network_count );
			}
		}
	}

	/**
	 * Whether an offset exists.
	 *
	 * @param mixed $offset An offset to check for.
	 * @return bool Returns true on success or false on failure.
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return array_key_exists( $offset, $this->network_counts );
	}

	/**
	 * Returns the value at specified offset.
	 *
	 * @param mixed $offset The offset to retrieve.
	 * @return mixed Can return all value types.
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->network_counts[ $offset ] ?? null;
	}

	/**
	 * Assigns a value to the specified offset.
	 *
	 * @param mixed $offset The offset to assign the value to.
	 * @param mixed $value The value to set.
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		throw new RuntimeException( 'Failed to mutate immutable object.' );
	}

	/**
	 * Unsets an offset.
	 *
	 * @param mixed $offset The offset to unset.
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		throw new RuntimeException( 'Failed to mutate immutable object.' );
	}

	/**
	 * Should return the string representation of the object.
	 *
	 * @return string
	 */
	public function serialize() {
		$result = serialize( $this->network_counts );
		return $result;
	}

	/**
	 * Set the count for a specific network.
	 *
	 * @param string $network Network to update.
	 * @param int    $network_count Count to use when updating the network.
	 * @return int
	 * @throws InvalidArgumentException When the network is invalid.
	 */
	private function set_count( string $network, int $network_count ) : int {
		if ( ! array_key_exists( $network, $this->network_counts ) ) {
			// @codeCoverageIgnoreStart
			throw new InvalidArgumentException( "Invalid network specified: {$network}" );
			// @codeCoverageIgnoreEnd
		}
		$this->network_counts[ $network ] = $network_count;
		return $network_count;
	}

	/**
	 * Called during unserialization of the object.
	 *
	 * @param string $data
	 */
	public function unserialize( $data ) {
		$counts = unserialize( $data );
		if ( ! is_array( $counts ) ) {
			// @codeCoverageIgnoreStart
			throw new InvalidArgumentException( 'Failed to unserialize ' . self::class . '.' );
			// @codeCoverageIgnoreEnd
		}
		$this->load_counts( $counts );
	}

	/**
	 * Returns an external iterator.
	 *
	 * @return Traversable
	 */
	#[\ReturnTypeWillChange]
	public function getIterator() {
		return new ArrayIterator( $this->network_counts );
	}

	// For PHP 8.1+ compat
	public function __serialize() {
		return $this->serialize();
	}
	public function __unserialize( $data ) {
		return $this->unserialize( $data );
	}
}
