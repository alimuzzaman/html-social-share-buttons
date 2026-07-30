<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\WordPressMigrationStateStore;

final class UpgradeRollbackContractTest extends WP_UnitTestCase {
	private $schema;

	protected function setUp(): void {
		parent::setUp();
		$this->schema = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/settings-schema-baseline.json' ),
			true
		);
		delete_option( WordPressMigrationStateStore::OPTION_NAME );
	}

	public function testRepresentativeLastReleaseOptionIsReadWithoutBeingRewritten(): void {
		$optionName = $this->schema['option_name'];
		$stored = $this->schema['upgrade_rollback_case']['stored_2_2_6'];
		update_option( $optionName, $stored );

		$runtime = LegacyRuntime::settings()->runtime();

		$this->assertSame(
			$this->schema['upgrade_rollback_case']['runtime_icons'],
			$runtime['icons']
		);
		$this->assertSame( $stored, get_option( $optionName ) );
		$this->assertFalse( get_option( WordPressMigrationStateStore::OPTION_NAME, false ) );
	}

	public function testCanonicalRepositoryRoundTripKeepsTheLastReleaseOptionAndExtensionShape(): void {
		$optionName = $this->schema['option_name'];
		$stored = $this->schema['upgrade_rollback_case']['stored_2_2_6'];
		update_option( $optionName, $stored );

		$repository = LegacyRuntime::plugin()->settings();
		$saved = $repository->save( $repository->load() );

		$this->assertSame( $stored, $saved );
		$this->assertSame( $stored, get_option( $optionName ) );
		$this->assertArrayHasKey( 'twitter', $saved['icons'] );
		$this->assertArrayNotHasKey( 'x', $saved['icons'] );
		$this->assertSame( 'custom-enabled-value', $saved['icons']['mastodon'] );
		$this->assertTrue( $saved['third_party_flag'] );
		$this->assertFalse( get_option( WordPressMigrationStateStore::OPTION_NAME, false ) );
	}

	public function testRewriteRegistersNoActivationDeactivationOrUninstallMutation(): void {
		$plugin = 'html-social-share-buttons/html-social-share.php';

		$this->assertFalse( has_action( 'activate_' . $plugin ) );
		$this->assertFalse( has_action( 'deactivate_' . $plugin ) );
		$this->assertFalse( has_action( 'uninstall_' . $plugin ) );
		$this->assertFalse( get_option( WordPressMigrationStateStore::OPTION_NAME, false ) );
	}
}
