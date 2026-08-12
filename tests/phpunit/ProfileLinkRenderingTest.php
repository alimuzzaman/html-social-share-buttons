<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\HtmlRenderer;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;

final class ProfileLinkRenderingTest extends WP_UnitTestCase {
	private $builder;
	private $renderer;
	private $facade;
	private $originalOptions;

	protected function setUp(): void {
		parent::setUp();
		$this->originalOptions = get_option( 'zm_shbt_fld', null );
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );
		$this->builder = new BuildShareButtons( $networks, $iconSets, new ResolveShareUrl() );
		$this->renderer = new HtmlRenderer();
		$this->facade = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->renderer();
	}

	protected function tearDown(): void {
		if ( null === $this->originalOptions ) {
			delete_option( 'zm_shbt_fld' );
		} else {
			update_option( 'zm_shbt_fld', $this->originalOptions );
		}
		parent::tearDown();
	}

	public function testCanonicalBuilderUsesRegistryOrderAndDoesNotRequireShareToggle(): void {
		$request = new RenderRequest(
			'default', 'square', RenderPlacement::PHP_API, '', array( 'x' ), array(), '', true,
			array(
				'mail' => 'mailto:hello@example.com',
				'facebook' => 'https://facebook.com/example',
				'unknown' => 'https://example.com/profile',
			)
		);
		$result = $this->builder->build( $request, new ShareContext( 'https://example.test/post', 'Example' ) );

		$this->assertSame( array( 'x' ), $this->networkIds( $result->buttons() ) );
		$this->assertSame( array( 'facebook', 'mail' ), $this->networkIds( $result->profileLinks() ) );
		$this->assertSame( 'facebook.png', $result->profileLinks()[0]->iconFile() );
	}

	public function testCanonicalRequestRejectsUnsafeProfileDestinations(): void {
		$this->expectException( InvalidArgumentException::class );
		new RenderRequest( 'default', 'square', RenderPlacement::PHP_API, '', array(), array(), '', false, array( 'facebook' => 'http://facebook.com/example' ) );
	}

	public function testCanonicalRendererAppendsAccessibleProfileLinksWithoutChangingShareMarkup(): void {
		$request = new RenderRequest(
			'default', 'square', RenderPlacement::PHP_API, '', array( 'facebook' ), array(), '', true,
			array( 'facebook' => 'https://facebook.com/example', 'mail' => 'mailto:hello@example.com' )
		);
		$result = $this->builder->build( $request, new ShareContext( 'https://example.test/post', 'Example' ) );
		$html = $this->renderer->render( $request, $result );

		$this->assertStringContainsString( "<a class='facebook' target='_blank' href='https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fexample.test%2Fpost' rel='nofollow noopener noreferrer'></a>", $html );
		$this->assertStringContainsString( "class='facebook zmshbt-profile-link' data-zmshbt-kind='profile' target='_blank' rel='nofollow noopener noreferrer' href='https://facebook.com/example'", $html );
		$this->assertStringContainsString( "class='mail zmshbt-profile-link' data-zmshbt-kind='profile' href='mailto:hello@example.com'", $html );
	}

	public function testCanonicalFacadeRejectsUnsafeProfilesAndCollectsProfileAssets(): void {
		$outcome = $this->facade->render(
			array(
				'iconset' => 'default', 'iconset_type' => 'square', 'icons' => array(),
				'profile_links' => array( 'facebook' => 'https://facebook.com/example', 'x' => 'javascript:alert(1)' ),
			)
		);

		$this->assertStringContainsString( 'https://facebook.com/example', $outcome->html() );
		$this->assertStringNotContainsString( 'javascript:', $outcome->html() );
		$this->assertSame( array( "default_square\0_facebook" ), array_keys( $outcome->printedIcons() ) );
	}

	public function testCanonicalFrontendAnalyticsExcludesProfilesOnlyWhenConfigured(): void {
		$withProfiles = $this->footerFor( array( 'facebook' => 'https://facebook.com/example' ) );
		$this->assertStringContainsString( "jQuery('.zmshbt a:not(.zmshbt-profile-link)').on('click'", $withProfiles );

		$withoutProfiles = $this->footerFor( array() );
		$this->assertStringContainsString( "jQuery('.zmshbt a').on('click'", $withoutProfiles );
		$this->assertStringNotContainsString( 'a:not(.zmshbt-profile-link)', $withoutProfiles );
	}

	public function testGlobalProfilesAreInheritedByCanonicalShortcodeAndBlockControllers(): void {
		update_option( 'zm_shbt_fld', array( 'profile_links' => array( 'facebook' => 'https://facebook.com/example' ) ) );
		$plugin = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin();
		$shortcodeHtml = $plugin->shortcode()->render( array( 'icons' => 'x' ) );
		$blockHtml = $plugin->block()->render( array( 'icons' => array( 'x' ) ) );

		$this->assertStringContainsString( 'https://facebook.com/example', $shortcodeHtml );
		$this->assertStringContainsString( 'https://facebook.com/example', $blockHtml );
	}

	public function testAutomaticPlacementCanHideProfilesWithoutChangingTheGlobalProfileMap(): void {
		$profiles = array( 'facebook' => 'https://facebook.com/example' );
		$hidden = $this->automaticPlacementFor(
			$profiles,
			array( Placement::AFTER_CONTENT => 'none' )
		);
		$inherited = $this->automaticPlacementFor( $profiles, array() );

		$this->assertStringNotContainsString( 'zmshbt-profile-link', $hidden );
		$this->assertStringNotContainsString( 'https://facebook.com/example', $hidden );
		$this->assertStringContainsString( 'zmshbt-profile-link', $inherited );
		$this->assertStringContainsString( 'https://facebook.com/example', $inherited );
	}

	public function testExplicitNoneModeSuppressesProfilesForExistingShortcodeAndBlockAttributes(): void {
		update_option( 'zm_shbt_fld', array( 'profile_links' => array( 'facebook' => 'https://facebook.com/example' ) ) );
		$plugin = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin();

		$shortcodeHtml = $plugin->shortcode()->render(
			array( 'icons' => 'x', 'profile_links_mode' => 'none' )
		);
		$blockHtml = $plugin->block()->render(
			array( 'icons' => array( 'x' ), 'profile_links_mode' => 'none' )
		);

		$this->assertStringNotContainsString( 'zmshbt-profile-link', $shortcodeHtml );
		$this->assertStringNotContainsString( 'zmshbt-profile-link', $blockHtml );
	}

	private function footerFor( array $profiles ): string {
		$controller = $this->controllerFor( $profiles, array() );

		ob_start();
		$controller->footer();
		return (string) ob_get_clean();
	}

	private function automaticPlacementFor( array $profiles, array $placementModes ): string {
		return $this->controllerFor( $profiles, $placementModes )->renderPlacement(
			Placement::AFTER_CONTENT,
			'after_content'
		);
	}

	private function controllerFor( array $profiles, array $placementModes ) {
		$plugin = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin();
		$settings = $plugin->settings()->load();
		return new FrontendController(
			new ProfileLinkSettingsRepository( new Settings(
				$settings->title(), $settings->iconSetId(), $settings->defaultIconShape(), array(),
				$settings->placementShapes(), $settings->networkStates(), $settings->shareTemplates(),
				$settings->excludedContent(), true, $settings->autoHideEnabled(), $settings->preserveUrlPort(),
				$settings->noFollow(), $profiles, $placementModes
			) ),
			$plugin->renderer(), new ContentPlacementComposer(), new FloatingPlacementPlanner(),
			new ExcludedContentPolicy(), new TranslationLoader( HSSB_PLUGIN_FILE, 'html-social-share-buttons' ),
			new AssetCollector( HSSB_PLUGIN_URL . 'iconset/default/style.css' ),
			'_zm_sh_disable_share'
		);
	}

	private function networkIds( array $links ): array {
		return array_map( static function ( $link ) { return $link->network()->id(); }, $links );
	}
}

final class ProfileLinkSettingsRepository implements SettingsRepository {
	private $settings;
	public function __construct( Settings $settings ) { $this->settings = $settings; }
	public function load() { return $this->settings; }
	public function save( Settings $settings ) { $this->settings = $settings; return $settings; }
}
