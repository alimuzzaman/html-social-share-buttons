<?php

use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginPaths;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Block\BlockRegistrar;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Shortcode\ShortcodeController;

final class CanonicalShortcodeBlockIntegrationTest extends WP_UnitTestCase {
	private $settings;
	private $shortcode;
	private $blocks;
	private $assets;

	protected function setUp(): void {
		parent::setUp();

		$root = dirname( __DIR__, 2 );
		$paths = new PluginPaths(
			$root . '/html-social-share.php',
			$root,
			'https://example.test/wp-content/plugins/html-social-share-buttons'
		);
		$config = new PluginConfig( $paths );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );
		$assetResolver = new IconSetAssetResolver(
			$root . '/assets/iconsets',
			'https://example.test/wp-content/plugins/html-social-share-buttons/assets/iconsets'
		);
		$renderer = new RenderFacade( $networks, $iconSets, $assetResolver );
		$this->settings = new CanonicalPresentationSettingsRepository( SettingsDefaults::create() );
		$this->assets = new AssetCollector(
			'https://example.test/wp-content/plugins/html-social-share-buttons/iconset/default/style.css'
		);
		$this->shortcode = new ShortcodeController(
			$renderer,
			$this->settings,
			$iconSets,
			$this->assets,
			$config
		);
		$this->blocks = new BlockRegistrar(
			$root,
			$renderer,
			$this->settings,
			$iconSets,
			$assetResolver,
			$networks,
			$this->assets,
			$config
		);
	}

	public function testShortcodeResolvesTheCurrentPermalinkAndCollectsAssets(): void {
		$postId = self::factory()->post->create( array( 'post_title' => 'Shortcode URL' ) );
		$GLOBALS['post'] = get_post( $postId );

		$html = $this->shortcode->render(
			array(
				'url' => '%%permalink%%',
				'icons' => 'facebook',
			)
		);

		$this->assertStringContainsString( 'in_shortcode', $html );
		$this->assertStringNotContainsString( '%%permalink%%', $html );
		$this->assertStringNotContainsString( '%25%25permalink%25%25', $html );
		$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $html );
		$this->assertArrayHasKey( 'default', $this->assets->stylesheets() );
	}

	public function testBothShortcodeNamesUseTheCanonicalRendererAndResolveThePermalinkOnce(): void {
		$postId = self::factory()->post->create( array( 'post_title' => 'Shortcode alias URL' ) );
		$this->go_to( get_permalink( $postId ) );
		$GLOBALS['post'] = get_post( $postId );
		$this->shortcode->registerHooks();

		$expected = rawurlencode( get_permalink( $postId ) );
		foreach ( $this->shortcodeNames() as $shortcode ) {
			$html = do_shortcode( '[' . $shortcode . ' icons="facebook" url="%%permalink%%"]' );

			$this->assertStringContainsString( $expected, $html, $shortcode );
			$this->assertStringNotContainsString( rawurlencode( $expected ), $html, $shortcode );
			$this->assertStringNotContainsString( '%%permalink%%', $html, $shortcode );
			$this->assertStringNotContainsString( '%25%25permalink%25%25', $html, $shortcode );
		}

		$source = (string) file_get_contents(
			dirname( __DIR__, 2 ) . '/src/Presentation/Integration/Shortcode/ShortcodeController.php'
		);
		$this->assertStringContainsString( '$this->renderer->render( $options )', $source );
		$this->assertStringNotContainsString( 'zm_sh_shortcode_cb', $source );
	}

	public function testShortcodeInheritsConfiguredProfileLinksWithoutChangingLegacyMarkup(): void {
		$this->settings->save( $this->settingsWithProfiles(
			array( 'facebook' => 'https://facebook.com/example' )
		) );

		$html = $this->shortcode->render(
			array(
				'title' => 'Share this page',
				'icons' => 'facebook',
			)
		);

		$this->assertStringContainsString( '<h3>Share this page</h3>', $html );
		$this->assertStringContainsString( "class='zmshbt in_shortcode default square'", $html );
		$this->assertStringContainsString( "class='facebook zmshbt-profile-link'", $html );
		$this->assertStringContainsString( 'https://facebook.com/example', $html );
	}

	public function testShortcodePreservesEmptySelectionAndEncodesExplicitUrlsOnce(): void {
		$empty = $this->shortcode->render( array( 'icons' => '' ) );
		$url = 'https://example.test/custom path/?query=one two';
		$html = $this->shortcode->render(
			array(
				'icons' => 'facebook',
				'url' => $url,
			)
		);

		$this->assertStringContainsString( "class='zmshbt in_shortcode default square'", $empty );
		$this->assertStringNotContainsString( '<a ', $empty );
		$this->assertStringContainsString( rawurlencode( $url ), $html );
		$this->assertStringNotContainsString( rawurlencode( rawurlencode( $url ) ), $html );
	}

	public function testShareBlockPreservesEmptySelectionAndUsesTheCanonicalRenderer(): void {
		$postId = self::factory()->post->create( array( 'post_title' => 'Block URL' ) );
		$block = (object) array( 'context' => array( 'postId' => $postId ) );

		$this->assertSame( '', $this->blocks->renderShareBlock( array( 'icons' => array() ), '', $block ) );

		$html = $this->blocks->renderShareBlock(
			array(
				'icons' => array( 'facebook' ),
				'iconset' => 'inherit',
			),
			'',
			$block
		);

		$this->assertStringContainsString( "class='zmshbt in_block default square'", $html );
		$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $html );
		$this->assertStringNotContainsString( '%%permalink%%', $html );
		$this->assertStringNotContainsString( 'zm_sh_shortcode_cb', (string) file_get_contents(
			dirname( __DIR__, 2 ) . '/src/Presentation/Integration/Block/BlockRegistrar.php'
		) );
	}

	public function testSocialLinksBlockUsesInheritedProfilesWithoutShareAnchors(): void {
		$this->settings->save( $this->settingsWithProfiles(
			array( 'facebook' => 'https://facebook.com/example' )
		) );

		$html = $this->blocks->renderSocialLinksBlock(
			array(
				'title' => 'Follow us',
				'profile_links_mode' => 'inherit',
			)
		);

		$this->assertStringContainsString( '<h3>Follow us</h3>', $html );
		$this->assertStringContainsString( 'zmshbt-profile-link', $html );
		$this->assertStringContainsString( 'https://facebook.com/example', $html );
		$this->assertStringNotContainsString( 'sharer.php', $html );
		$this->assertStringNotContainsString( 'intent/tweet', $html );
	}

	public function testSocialLinksBlockHonoursCustomAndNoneProfileModes(): void {
		$this->settings->save( $this->settingsWithProfiles(
			array( 'facebook' => 'https://facebook.com/inherited' )
		) );

		$custom = $this->blocks->renderSocialLinksBlock(
			array(
				'profile_links_mode' => 'custom',
				'profile_links' => array( 'x' => 'https://x.com/custom' ),
			)
		);
		$none = $this->blocks->renderSocialLinksBlock(
			array( 'profile_links_mode' => 'none' )
		);

		$this->assertStringContainsString( 'https://x.com/custom', $custom );
		$this->assertStringNotContainsString( 'facebook.com/inherited', $custom );
		$this->assertStringContainsString( "class='twitter zmshbt-profile-link'", $custom );
		$this->assertSame( '', $none );
	}

	private function settingsWithProfiles( array $profileLinks ) {
		return new Settings(
			'Share this with your friends',
			'default',
			'square',
			array(),
			array(),
			array(),
			array(),
			'',
			false,
			false,
			false,
			false,
			$profileLinks
		);
	}

	private function shortcodeNames() {
		return array( 'zm_sh_btn', 'html-social-share-buttons' );
	}
}

final class CanonicalPresentationSettingsRepository implements SettingsRepository {
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
