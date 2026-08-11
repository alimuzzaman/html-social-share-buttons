<?php

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;

final class CanonicalRenderFacadeTest extends WP_UnitTestCase {
	public function testBuiltInAssetsUseTheirReleasedDirectoriesAndFilenames(): void {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$sets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );
		$assets = new IconSetAssetResolver( $root, 'https://example.test/plugin' );

		$this->assertSame(
			'https://example.test/plugin/iconset/flat/circle/Twitter.png',
			$assets->iconUrl( $sets->get( 'flat' ), 'circle', 'x' )
		);
		$this->assertSame(
			'https://example.test/plugin/iconset/long_shadow/square/twitter.png',
			$assets->iconUrl( $sets->get( 'long-shadows' ), 'square', 'x' )
		);
		$this->assertSame(
			'https://example.test/plugin/assets/iconsets/tabler-outline/style.css',
			$assets->stylesheetUrl( $sets->get( 'tabler-outline' ) )
		);
		$this->assertFileExists( $assets->iconPath( $sets->get( 'flat' ), 'circle', 'x' ) );
	}

	public function testCanonicalFacadePreservesPublicMarkupAndEncodesAnExplicitUrlOnce(): void {
		$facade = $this->facade();
		$url = 'https://destination.example/article?id=42&source=canonical';
		$outcome = $facade->render(
			array(
				'iconset' => 'flat',
				'iconset_type' => 'circle',
				'class' => 'in_shortcode',
				'title' => 'Canonical title',
				'icons' => array( 'facebook' => 1, 'twitter' => 1 ),
				'url' => $url,
			),
			0,
			new ShareContext( 'https://example.test/fallback/', 'Canonical title' )
		);

		$this->assertStringContainsString(
			"<h3>Canonical title</h3><div class='zmshbt in_shortcode flat circle'>",
			$outcome->html()
		);
		$this->assertStringContainsString( "<a class='twitter'", $outcome->html() );
		$this->assertStringContainsString( rawurlencode( $url ), $outcome->html() );
		$this->assertStringNotContainsString( rawurlencode( rawurlencode( $url ) ), $outcome->html() );
		$this->assertSame(
			array( 'flat' => 'https://example.test/plugin/iconset/flat/style.css' ),
			$outcome->stylesheets()
		);
		$this->assertSame(
			'https://example.test/plugin/iconset/flat/',
			$outcome->printedIcons()[ "flat_circle\0_x" ]['iconset_url']
		);
		$this->assertSame(
			'Twitter.png',
			$outcome->printedIcons()[ "flat_circle\0_x" ]['image']
		);
	}

	public function testProfilesOnlyRequestsRenderProfilesWithoutShareAnchors(): void {
		$outcome = $this->facade()->render(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'class' => 'in_block',
				'title' => 'Follow us',
				'profiles_only' => true,
				'icons' => array(),
				'profile_links' => array( 'facebook' => 'https://www.facebook.com/example' ),
			),
			0,
			new ShareContext( 'https://example.test/', 'Example' )
		);

		$this->assertStringContainsString( '<h3>Follow us</h3>', $outcome->html() );
		$this->assertStringContainsString( "data-zmshbt-kind='profile'", $outcome->html() );
		$this->assertStringNotContainsString( "target='_blank' href='https://www.facebook.com/sharer", $outcome->html() );
		$this->assertCount( 1, $outcome->printedIcons() );
	}

	public function testUnknownIconSetUsesDefaultAssetsButKeepsRequestedWrapperClass(): void {
		$outcome = $this->facade()->render(
			array(
				'iconset' => 'missing-iconset',
				'iconset_type' => 'square',
				'icons' => array( 'facebook' => 1 ),
			),
			0,
			new ShareContext( 'https://example.test/', 'Example' )
		);

		$this->assertStringContainsString(
			"class='zmshbt left missing-iconset square'",
			$outcome->html()
		);
		$this->assertSame(
			array( 'default' => 'https://example.test/plugin/iconset/default/style.css' ),
			$outcome->stylesheets()
		);
		$this->assertArrayHasKey( "default_square\0_facebook", $outcome->printedIcons() );
	}

	public function testApplicationRenderingDoesNotReferenceLegacySymbolsOrGlobals(): void {
		$directory = dirname( __DIR__, 2 ) . '/src/Application/Rendering';
		foreach ( glob( $directory . '/*.php' ) as $file ) {
			$source = (string) file_get_contents( $file );
			$this->assertStringNotContainsString( 'Compatibility\\Legacy', $source, $file );
			$this->assertStringNotContainsString( 'zm_sh_', $source, $file );
			$this->assertStringNotContainsString( '$GLOBALS', $source, $file );
		}
	}

	private function facade(): RenderFacade {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$sets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );

		return new RenderFacade(
			$networks,
			$sets,
			new IconSetAssetResolver( $root, 'https://example.test/plugin' ),
			new ExtensionHooks()
		);
	}
}
