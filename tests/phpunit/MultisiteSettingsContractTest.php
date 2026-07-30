<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Migration\WordPressMigrationStateStore;

/**
 * @group multisite
 */
final class MultisiteSettingsContractTest extends WP_UnitTestCase {
	public function testSettingsRemainSiteLocalAndEmptyMigrationsWriteNoVersion(): void {
		if ( ! is_multisite() ) {
			$this->markTestSkipped( 'This contract requires phpunit.multisite.xml.dist.' );
		}

		$primarySiteId = get_current_blog_id();
		$secondarySiteId = self::factory()->blog->create();
		$primarySettings = array(
			'title' => 'Primary site',
			'icons' => array( 'facebook' => '1' ),
		);
		update_option( 'zm_shbt_fld', $primarySettings );

		switch_to_blog( $secondarySiteId );
		try {
			$this->assertFalse( get_option( 'zm_shbt_fld', false ) );
			$this->assertSame(
				array( 'fallback' => true ),
				LegacyRuntime::settings()->stored( array( 'fallback' => true ) )
			);
			$secondarySettings = LegacyRuntime::settings()->save(
				array(
					'title' => 'Secondary site',
					'icons' => array( 'x' => '1' ),
				)
			);
			$this->assertSame( $secondarySettings, get_option( 'zm_shbt_fld' ) );
			$this->assertFalse(
				get_option( WordPressMigrationStateStore::OPTION_NAME, false )
			);
		} finally {
			restore_current_blog();
		}

		$this->assertSame( $primarySiteId, get_current_blog_id() );
		$this->assertSame( $primarySettings, get_option( 'zm_shbt_fld' ) );
		$this->assertFalse(
			get_option( WordPressMigrationStateStore::OPTION_NAME, false )
		);
	}
}
