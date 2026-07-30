<?php

use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\Migration;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\MigrationRunner;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\MigrationStateStore;

final class MigrationRunnerTest extends WP_UnitTestCase {
	public function testNoRegisteredMigrationDoesNotWriteDatabaseState(): void {
		$state  = new TestMigrationStateStore();
		$runner = new MigrationRunner( $state, array() );

		$this->assertSame( 0, $runner->run() );
		$this->assertSame( array(), $state->savedVersions );
	}

	public function testMigrationsRunOnceInVersionOrder(): void {
		$state = new TestMigrationStateStore();
		$log   = array();
		$runner = new MigrationRunner(
			$state,
			array(
				new TestMigration( 2, $log ),
				new TestMigration( 1, $log ),
			)
		);

		$this->assertSame( 2, $runner->run() );
		$this->assertSame( array( 1, 2 ), $log );
		$this->assertSame( array( 1, 2 ), $state->savedVersions );

		$this->assertSame( 2, $runner->run() );
		$this->assertSame( array( 1, 2 ), $log );
	}

	public function testFailedMigrationDoesNotAdvanceTheStoredVersion(): void {
		$state = new TestMigrationStateStore();
		$log   = array();
		$runner = new MigrationRunner(
			$state,
			array(
				new TestMigration( 1, $log ),
				new TestMigration( 2, $log, true ),
			)
		);

		try {
			$runner->run();
			$this->fail( 'The failing migration should throw.' );
		} catch ( RuntimeException $error ) {
			$this->assertSame( 'Migration 2 failed.', $error->getMessage() );
		}

		$this->assertSame( 1, $state->currentVersion() );
		$this->assertSame( array( 1 ), $state->savedVersions );
	}
}

final class TestMigrationStateStore implements MigrationStateStore {
	public $savedVersions = array();
	private $version = 0;

	public function currentVersion() {
		return $this->version;
	}

	public function saveVersion( $version ) {
		$this->version         = (int) $version;
		$this->savedVersions[] = (int) $version;
	}
}

final class TestMigration implements Migration {
	private $migrationVersion;
	private $log;
	private $fails;

	public function __construct( $version, array &$log, $fails = false ) {
		$this->migrationVersion = (int) $version;
		$this->log              = &$log;
		$this->fails            = (bool) $fails;
	}

	public function version() {
		return $this->migrationVersion;
	}

	public function up() {
		if ( $this->fails ) {
			throw new RuntimeException( sprintf( 'Migration %d failed.', $this->migrationVersion ) );
		}

		$this->log[] = $this->migrationVersion;
	}
}
