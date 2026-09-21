<?php

use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\ButtonAppearance;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;

final class BrowserUrlEligibilityTest extends WP_UnitTestCase {
	public function testPageLevelFloatingControlsCanUseTheArchiveRequestUrl(): void {
		$postId = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$this->go_to( home_url( '/?s=share' ) );
		$GLOBALS['post'] = get_post( $postId );

		$outcome = $this->facade()->render(
			array(
				'class'                => 'left',
				'placement'            => RenderPlacement::FLOATING_LEFT,
				'icons'                => array( 'facebook' ),
				'browser_url_eligible' => true,
				'browser_url_source'   => 'automatic_page',
			),
			0
		);

		$this->assertStringContainsString( "data-hssb-browser-url='1'", $outcome->html() );
		$this->assertTrue( $outcome->requiresFrontendJs() );
		$this->assertStringNotContainsString( rawurlencode( get_permalink( $postId ) ), $outcome->html() );
	}

	public function testArchiveLoopPostRenderingRemainsServerOnly(): void {
		$postId = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$this->go_to( home_url( '/?s=share' ) );
		$GLOBALS['post'] = get_post( $postId );

		$outcome = $this->facade()->render(
			array(
				'class'                => 'in_shortcode',
				'placement'            => RenderPlacement::SHORTCODE,
				'icons'                => array( 'facebook' ),
				'browser_url_eligible' => true,
				'browser_url_source'   => 'automatic_singular',
			),
			$postId
		);

		$this->assertStringNotContainsString( 'data-hssb-browser-url', $outcome->html() );
		$this->assertFalse( $outcome->requiresFrontendJs() );
		$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $outcome->html() );
	}

	public function testSecondaryLoopPostOnASingularPageRemainsServerOnly(): void {
		$mainPostId = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$loopPostId = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$this->go_to( get_permalink( $mainPostId ) );
		$GLOBALS['post'] = get_post( $loopPostId );

		$outcome = $this->facade()->render(
			array(
				'class'                => 'in_shortcode',
				'placement'            => RenderPlacement::SHORTCODE,
				'icons'                => array( 'facebook' ),
				'browser_url_eligible' => true,
				'browser_url_source'   => 'automatic_singular',
			),
			$loopPostId
		);

		$this->assertStringNotContainsString( 'data-hssb-browser-url', $outcome->html() );
		$this->assertFalse( $outcome->requiresFrontendJs() );
		$this->assertStringContainsString( rawurlencode( get_permalink( $loopPostId ) ), $outcome->html() );
	}

	private function facade(): RenderFacade {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );
		$settings = new Settings(
			'Browser URL eligibility',
			'default',
			'square',
			array(
				Placement::LEFT           => true,
				Placement::RIGHT          => false,
				Placement::BEFORE_CONTENT => false,
				Placement::AFTER_CONTENT  => false,
			),
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
			ButtonAppearance::LEGACY,
			true
		);

		return new RenderFacade(
			$networks,
			$iconSets,
			new IconSetAssetResolver( $root . '/assets/iconsets', plugins_url( 'assets/iconsets', $root . '/html-social-share.php' ) ),
			new ExtensionHooks(),
			null,
			null,
			null,
			new BrowserUrlEligibilitySettingsRepository( $settings )
		);
	}
}

final class BrowserUrlEligibilitySettingsRepository implements SettingsRepository {
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
