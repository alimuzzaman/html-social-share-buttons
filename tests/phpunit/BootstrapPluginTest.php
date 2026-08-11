<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;

final class BootstrapPluginTest extends WP_UnitTestCase {
	public function testCanonicalKernelBootsWithoutWritingAMigration(): void {
		$runtime = LegacyApi::plugin();

		$this->assertTrue( $runtime->isBooted() );
		$this->assertSame(
			array( 'facebook', 'x', 'linkedin', 'pinterest', 'telegram', 'bluesky', 'mail' ),
			$runtime->networks()->ids()
		);
		$this->assertSame(
			array( 'default', 'flat', 'long-shadows', 'prajin', 'bootstrap-solid', 'tabler-outline' ),
			$runtime->iconSets()->ids()
		);
		$this->assertInstanceOf(
			\Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy::class,
			$runtime->excludedContent()
		);
		$this->assertInstanceOf(
			\Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer::class,
			$runtime->contentPlacement()
		);
		$this->assertInstanceOf(
			\Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner::class,
			$runtime->floatingPlacement()
		);
		$this->assertStringEndsWith(
			'html-social-share-buttons/languages',
			$runtime->translations()->relativeLanguagePath()
		);
		$this->assertStringEndsWith(
			'/iconset/default/style.css',
			$runtime->assets()->stylesheetPath( $runtime->iconSets()->get( 'default' ) )
		);
		$this->assertInstanceOf( ExtensionHooks::class, $runtime->extensions() );
		$this->assertFalse( get_option( 'hssb_schema_version', false ) );
	}
}
