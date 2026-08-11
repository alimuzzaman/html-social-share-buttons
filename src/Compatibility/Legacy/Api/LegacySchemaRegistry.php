<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api;

/**
 * Isolated state for the historical schema-extension API.
 *
 * Schemas are an extension payload, not application configuration, so this
 * boundary retains their historical mutable semantics without leaking them
 * into the canonical settings model.
 */
final class LegacySchemaRegistry {
	private static $instance;
	private $schemas = array();

	private function __construct() {
		LegacyHooks::registerSchemas();
	}

	public static function instance() {
		if ( ! self::$instance instanceof self ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get( $id ) {
		$id = (string) $id;

		return isset( $this->schemas[ $id ] ) ? $this->schemas[ $id ] : null;
	}

	public function all() {
		return $this->schemas;
	}

	public function add( $schema ) {
		$items = self::isList( $schema ) ? $schema : array( $schema );
		foreach ( $items as $item ) {
			if ( is_array( $item ) && isset( $item['id'] ) && is_scalar( $item['id'] ) ) {
				$this->schemas[ (string) $item['id'] ] = $item;
			}
		}

		return $this->schemas;
	}

	public function remove( $id ) {
		unset( $this->schemas[ (string) $id ] );

		return $id;
	}

	private static function isList( $value ) {
		return is_array( $value ) && array_keys( $value ) === range( 0, count( $value ) - 1 );
	}
}
