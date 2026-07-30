<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;

final class BootstrapPluginTest extends WP_UnitTestCase {
	public function testNewRuntimeBootsWithoutRegisteringOrWritingAMigration(): void {
		$runtime = LegacyRuntime::plugin();

		$this->assertTrue( $runtime->isBooted() );
		$this->assertSame(
			array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			$runtime->networks()->ids()
		);
		$this->assertSame(
			array( 'default', 'flat', 'long-shadows', 'prajin' ),
			$runtime->iconSets()->ids()
		);
		$this->assertSame( $runtime->excludedContent(), LegacyRuntime::excludedContent() );
		$this->assertSame( $runtime->contentPlacement(), LegacyRuntime::contentPlacement() );
		$this->assertSame( $runtime->floatingPlacement(), LegacyRuntime::floatingPlacement() );
		$this->assertStringEndsWith(
			'html-social-share-buttons/languages',
			$runtime->translations()->relativeLanguagePath()
		);
		$this->assertStringEndsWith(
			'/assets/iconsets/default/style.css',
			$runtime->assets()->stylesheetPath( $runtime->iconSets()->get( 'default' ) )
		);
		$this->assertInstanceOf( ExtensionHooks::class, $runtime->extensions() );
		$this->assertFalse( get_option( 'hssb_schema_version', false ) );
	}
}
