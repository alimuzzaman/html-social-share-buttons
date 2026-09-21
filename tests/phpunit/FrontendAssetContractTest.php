<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;

final class FrontendAssetContractTest extends WP_UnitTestCase {
	private $styleHandles = array( 'social-share-default', 'social-share-flat', 'hssb-button-appearance' );

	protected function setUp(): void {
		parent::setUp();
		$this->resetHistoricalStyleHandles();
	}

	protected function tearDown(): void {
		$this->resetHistoricalStyleHandles();
		parent::tearDown();
	}

	public function testRenderedIconsetsAreCollectedAndEnqueuedOnceByHistoricalHandle(): void {
		$plugin = LegacyApi::plugin();
		$frontend = $plugin->frontend();

		$frontend->render(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'icons' => array( 'facebook' => 1 ),
			)
		);
		$frontend->render(
			array(
				'iconset' => 'flat',
				'iconset_type' => 'circle',
				'icons' => array( 'x' => 1 ),
			)
		);
		$frontend->render(
			array(
				'iconset' => 'flat',
				'iconset_type' => 'circle',
				'icons' => array( 'x' => 1 ),
			)
		);

		$frontend->assets()->enqueueStyles();
		$styles = wp_styles();

		$this->assertContains( 'social-share-default', $styles->queue );
		$this->assertContains( 'social-share-flat', $styles->queue );
		$this->assertSame(
			$plugin->assets()->stylesheetUrl( $plugin->iconSets()->get( 'default' ) ),
			$styles->registered['social-share-default']->src
		);
		$this->assertSame(
			$plugin->assets()->stylesheetUrl( $plugin->iconSets()->get( 'flat' ) ),
			$styles->registered['social-share-flat']->src
		);
		$this->assertSame( '3.3.0', $styles->registered['social-share-default']->ver );
		$this->assertSame( '3.3.0', $styles->registered['social-share-flat']->ver );
		$this->assertSame(
			1,
			count( array_keys( $styles->queue, 'social-share-flat', true ) )
		);
	}

	public function testCoreStylesheetFollowsPacksCollectedLater(): void {
		$collector = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector( 'fallback.css' );
		foreach ( array(
			array(
				'default'                => 'default.css',
				'hssb-button-appearance' => 'core.css',
			),
			array( 'flat' => 'flat.css' ),
		) as $sheets ) {
			$collector->collect(
				new class( $sheets ) {
					private $sheets;

					public function __construct( $sheets ) {
						$this->sheets = $sheets;
					}

					public function stylesheets() {
						return $this->sheets;
					}
				}
			);
		}
		$collector->enqueueStyles();
		$queue = wp_styles()->queue;
		$this->assertLessThan( array_search( 'hssb-button-appearance', $queue, true ), array_search( 'social-share-flat', $queue, true ) );
	}

	public function testInlineIconRulesAreDeduplicatedAndUseLegacyAssetUrls(): void {
		$plugin = LegacyApi::plugin();
		$frontend = $plugin->frontend();
		$options = array(
			'iconset' => 'default',
			'iconset_type' => 'square',
			'icons' => array( 'x' => 1 ),
		);

		$frontend->render( $options );
		$frontend->render( $options );

		$css = $frontend->assets()->inlineIconStyles( false );
		$iconSet = $plugin->iconSets()->get( 'default' );

		$this->assertSame( 1, substr_count( $css, '.zmshbt.default.square .twitter' ) );
		$this->assertStringContainsString(
			"background-image:url('" . $plugin->assets()->setUrl( $iconSet ) . 'square/twitter.png' . "')",
			$css
		);
		$this->assertStringContainsString( '.zmshbt.left', $css );
		$this->assertStringContainsString( '.zmshbt.right', $css );
	}

	public function testNoRenderedButtonsKeepsTheHistoricalDefaultStylesheetFallback(): void {
		$frontend = LegacyApi::plugin()->frontend();

		$frontend->assets()->enqueueStyles();

		$this->assertContains( 'social-share-default', wp_styles()->queue );
		$this->assertStringEndsWith(
			'/iconset/default/style.css',
			wp_styles()->registered['social-share-default']->src
		);
	}

	private function resetHistoricalStyleHandles(): void {
		foreach ( $this->styleHandles as $handle ) {
			wp_dequeue_style( $handle );
			wp_deregister_style( $handle );
		}
	}
}
