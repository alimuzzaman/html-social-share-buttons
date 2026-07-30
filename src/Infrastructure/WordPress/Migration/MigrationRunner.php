<?php

declare( strict_types=1 );

namespace Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration;

use InvalidArgumentException;

final class MigrationRunner {
	private $state;
	private $migrations;

	public function __construct( MigrationStateStore $state, array $migrations ) {
		$this->state      = $state;
		$this->migrations = $this->validateAndSort( $migrations );
	}

	public function run() {
		$current = (int) $this->state->currentVersion();

		foreach ( $this->migrations as $migration ) {
			$version = (int) $migration->version();
			if ( $version <= $current ) {
				continue;
			}

			$migration->up();
			$this->state->saveVersion( $version );
			$current = $version;
		}

		return $current;
	}

	private function validateAndSort( array $migrations ) {
		$versions = array();

		foreach ( $migrations as $migration ) {
			if ( ! $migration instanceof Migration ) {
				throw new InvalidArgumentException( 'Every database migration must implement Migration.' );
			}

			$version = (int) $migration->version();
			if ( $version < 1 ) {
				throw new InvalidArgumentException( 'Database migration versions must be positive integers.' );
			}
			if ( isset( $versions[ $version ] ) ) {
				throw new InvalidArgumentException( 'Database migration versions must be unique.' );
			}
			$versions[ $version ] = true;
		}

		usort(
			$migrations,
			static function ( Migration $left, Migration $right ) {
				return (int) $left->version() - (int) $right->version();
			}
		);

		return $migrations;
	}
}
