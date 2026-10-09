<?php

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderOutcome;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Frontend\FrontendFeatureRegistry;

final class CanonicalFrontendAssetCollectorTest extends WP_UnitTestCase {
	public function testItCollectsRenderedAssetsOnceAndPreservesHistoricalHandles(): void {
		$collector = new AssetCollector( 'https://example.test/iconset/default/style.css' );
		$outcome = new CanonicalFrontendAssetCollectorOutcome(
			'<div></div>',
			array(
				'default' => 'https://example.test/iconset/default/style.css',
				'flat' => 'https://example.test/iconset/flat/style.css',
			),
			array(
				'default_square_x' => array(
					'iconset_id' => 'default',
					'iconset_type' => 'square',
					'iconset_url' => 'https://example.test/iconset/default/',
					'class' => 'twitter',
					'image' => 'twitter.png',
				),
			)
		);

		$collector->collect( $outcome );
		$collector->collect( $outcome );
		$collector->enqueueStyles();

		$this->assertContains( 'social-share-default', wp_styles()->queue );
		$this->assertContains( 'social-share-flat', wp_styles()->queue );
		$this->assertSame(
			1,
			count( array_keys( wp_styles()->queue, 'social-share-flat', true ) )
		);
		$this->assertSame(
			'3.4.0',
			wp_styles()->registered['social-share-default']->ver
		);

		$css = $collector->inlineIconStyles( false );
		$this->assertSame( 1, substr_count( $css, '.zmshbt.default.square .twitter' ) );
		$this->assertStringContainsString(
			"background-image:url('https://example.test/iconset/default/square/twitter.png')",
			$css
		);
		$this->assertStringContainsString( '.zmshbt.left', $css );
		$this->assertStringContainsString( '.zmshbt.right', $css );
	}

	public function testItEnqueuesOneScriptPerRenderedFrontendFeature(): void {
		wp_dequeue_script( 'hssb-browser-url' );
		wp_dequeue_script( 'hssb-copy-link' );
		$collector = new AssetCollector(
			'fallback.css',
			'3.4.0',
			'hssb-button-appearance',
			'https://example.test/build/browser-url.js',
			'hssb-browser-url',
			array( FrontendFeatureRegistry::COPY_LINK => array( 'hssb-copy-link', 'https://example.test/build/copy-link.js' ) )
		);

		$collector->enqueueScripts();
		$this->assertNotContains( 'hssb-copy-link', wp_scripts()->queue );
		$this->assertNotContains( 'hssb-browser-url', wp_scripts()->queue );

		$collector->collect( new RenderOutcome( '', array(), array(), true, array( FrontendFeatureRegistry::COPY_LINK ) ) );
		$collector->enqueueScripts();
		$this->assertContains( 'hssb-copy-link', wp_scripts()->queue );
		$this->assertNotContains( 'hssb-browser-url', wp_scripts()->queue );
		$this->assertSame( 'https://example.test/build/copy-link.js', wp_scripts()->registered['hssb-copy-link']->src );

		$collector->collect( new RenderOutcome( '', array(), array(), true, array( FrontendFeatureRegistry::BROWSER_URL ) ) );
		$collector->enqueueScripts();
		$this->assertContains( 'hssb-browser-url', wp_scripts()->queue );
		$this->assertSame( 1, count( array_keys( wp_scripts()->queue, 'hssb-copy-link', true ) ) );

		$fresh = $collector->fresh();
		$fresh->collect( new RenderOutcome( '', array(), array(), true, array( FrontendFeatureRegistry::COPY_LINK ) ) );
		wp_dequeue_script( 'hssb-copy-link' );
		$fresh->enqueueScripts();
		$this->assertContains( 'hssb-copy-link', wp_scripts()->queue );
	}

	public function testCopyLinkRendersARealLinkAndReportsItsFeature(): void {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$renderer = new RenderFacade(
			$networks,
			( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks ),
			new IconSetAssetResolver(
				$root . '/assets/iconsets',
				plugins_url( 'assets/iconsets', $root . '/html-social-share.php' )
			),
			new ExtensionHooks()
		);

		$withCopy = $renderer->render(
			array( 'iconset' => 'bootstrap-solid', 'icons' => array( 'facebook', 'copy' ), 'url' => 'https://example.test/post/?a=1&b=2' )
		);
		$this->assertSame( array( FrontendFeatureRegistry::COPY_LINK ), $withCopy->frontendFeatures() );
		$this->assertStringContainsString(
			"<a class='copy' href='https://example.test/post/?a=1&#038;b=2' data-hssb-copy-link='1'",
			$withCopy->html()
		);
		$this->assertDoesNotMatchRegularExpression( "/<a class='copy'[^>]*target=/", $withCopy->html() );

		$withoutCopy = $renderer->render(
			array( 'iconset' => 'bootstrap-solid', 'icons' => array( 'facebook', 'whatsapp', 'reddit' ), 'url' => 'https://example.test/post/' )
		);
		$this->assertSame( array(), $withoutCopy->frontendFeatures() );
		$this->assertFalse( $withoutCopy->requiresFrontendJs() );
	}

	public function testItUsesTheDefaultStylesheetWhenNothingRendered(): void {
		$collector = new AssetCollector( 'https://example.test/iconset/default/style.css' );
		$collector->enqueueStyles();

		$this->assertContains( 'social-share-default', wp_styles()->queue );
		$this->assertSame(
			'https://example.test/iconset/default/style.css',
			wp_styles()->registered['social-share-default']->src
		);
	}

	public function testControllerHasNoCompatibilityRuntimeDependency(): void {
		$source = (string) file_get_contents(
			dirname( __DIR__, 2 ) . '/src/Presentation/Frontend/FrontendController.php'
		);

		$this->assertStringNotContainsString( 'Compatibility\\Legacy', $source );
		$this->assertStringNotContainsString( 'LegacyRuntime', $source );
		$this->assertStringNotContainsString( '$GLOBALS', $source );
		$this->assertStringNotContainsString( 'global $', $source );
	}

	public function testAutomaticPlacementPreservesStoredNetworkKeys(): void {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );
		$renderer = new RenderFacade(
			$networks,
			$iconSets,
			new IconSetAssetResolver(
				$root . '/assets/iconsets',
				plugins_url( 'assets/iconsets', $root . '/html-social-share.php' )
			),
			new ExtensionHooks()
		);
		$settings = new Settings(
			'Canonical placement',
			'default',
			'square',
			array(
				Placement::LEFT => false,
				Placement::RIGHT => false,
				Placement::BEFORE_CONTENT => false,
				Placement::AFTER_CONTENT => true,
			),
			array(
				Placement::LEFT => 'square',
				Placement::RIGHT => 'square',
				Placement::BEFORE_CONTENT => 'square',
				Placement::AFTER_CONTENT => 'square',
			),
			array( 'facebook' => true, 'x' => false, 'mail' => false ),
			array(),
			'',
			false,
			false,
			false,
			false
		);
		$controller = new FrontendController(
			new CanonicalFrontendSettingsRepository( $settings ),
			$renderer,
			new ContentPlacementComposer(),
			new FloatingPlacementPlanner(),
			new ExcludedContentPolicy(),
			new TranslationLoader( $root . '/html-social-share.php', 'html-social-share-buttons' ),
			new AssetCollector( plugins_url( 'iconset/default/style.css', $root . '/html-social-share.php' ) ),
			'_zm_sh_disable_share'
		);

		$html = $controller->renderPlacement( Placement::AFTER_CONTENT, 'in_widget' );

		$this->assertStringContainsString( '<h3>Canonical placement</h3>', $html );
		$this->assertStringContainsString( "class='facebook'", $html );
		$this->assertStringContainsString( "class='twitter'", $html );
		$this->assertStringContainsString( "class='mail'", $html );
		$this->assertStringContainsString( "class='zmshbt in_widget default square'", $html );
	}

	public function testAutomaticPlacementFromStoredOptionShapePreservesOrderAndNetworks(): void {
		$postId = self::factory()->post->create( array( 'post_title' => 'Automatic placement' ) );
		$this->go_to( get_permalink( $postId ) );
		$plugin = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin();

		$output = $plugin->frontend()->filterContentWithOptions(
			'<p>Original content.</p>',
			array(
				'title' => 'Stored placement',
				'iconset' => 'default',
				'icons' => array(
					'facebook' => '1',
				),
				'show_in' => array(
					'show_before_post' => '1',
					'show_after_post' => '1',
				),
			)
		);

		$before = strpos( $output, '<h3>Stored placement</h3>' );
		$content = strpos( $output, '<p>Original content.</p>' );
		$after = strrpos( $output, '<h3>Stored placement</h3>' );
		$this->assertNotFalse( $before );
		$this->assertNotFalse( $content );
		$this->assertNotFalse( $after );
		$this->assertLessThan( $content, $before );
		$this->assertGreaterThan( $content, $after );
		$this->assertSame( 1, substr_count( $output, '<p>Original content.</p>' ) );
		$this->assertSame( 2, substr_count( $output, "class='facebook'" ) );
		$this->assertStringNotContainsString( "class='twitter'", $output );
		$this->assertStringNotContainsString( "class='mail'", $output );
	}

	public function testBrowserUrlEnhancementIsOptInAndLimitedToEligibleAutomaticLinks(): void {
		$postId = self::factory()->post->create( array( 'post_title' => 'Browser URL' ) );
		$this->go_to( get_permalink( $postId ) );
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );
		$settings = new Settings(
			'Browser URL',
			'default',
			'square',
			array(),
			array(),
			array( 'facebook' => true ),
			array(),
			'',
			false,
			false,
			false,
			false,
			array(),
			array(),
			true,
			true,
			true,
			\Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\ButtonAppearance::LEGACY,
			true
		);
		$facade = new RenderFacade(
			$networks,
			$iconSets,
			new IconSetAssetResolver( $root . '/assets/iconsets', plugins_url( 'assets/iconsets', $root . '/html-social-share-buttons.php' ) ),
			new ExtensionHooks(),
			null,
			null,
			null,
			new CanonicalFrontendSettingsRepository( $settings )
		);

		$automatic = $facade->render(
			array( 'icons' => array( 'facebook' => true ), 'browser_url_eligible' => true, 'browser_url_source' => 'automatic_singular' ),
			$postId
		)->html();
		$custom = $facade->render(
			array( 'icons' => array( 'facebook' => true ), 'url' => 'https://example.test/custom', 'browser_url_eligible' => true, 'browser_url_source' => 'automatic_singular' ),
			$postId
		)->html();

		$this->assertStringContainsString( "data-hssb-browser-url='1'", $automatic );
		$this->assertStringContainsString( 'data-hssb-browser-descriptor=', $automatic );
		$this->assertStringNotContainsString( 'data-hssb-browser-url', $custom );
		$this->assertStringNotContainsString( 'data-hssb-browser-template', $custom );
	}
}

final class CanonicalFrontendAssetCollectorOutcome {
	private $html;
	private $stylesheets;
	private $printedIcons;

	public function __construct( $html, array $stylesheets, array $printedIcons ) {
		$this->html = (string) $html;
		$this->stylesheets = $stylesheets;
		$this->printedIcons = $printedIcons;
	}

	public function html() {
		return $this->html;
	}

	public function stylesheets() {
		return $this->stylesheets;
	}

	public function printedIcons() {
		return $this->printedIcons;
	}
}

final class CanonicalFrontendSettingsRepository implements SettingsRepository {
	private $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function load() {
		return $this->settings;
	}

	public function save( Settings $settings ) {
		$this->settings = $settings;
	}
}
