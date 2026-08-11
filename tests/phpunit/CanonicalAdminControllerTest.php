<?php

use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginConfig;
use Alimuzzaman\HtmlSocialShareButtons\Bootstrap\PluginPaths;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\MetaboxController;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Admin\IconSetPayloadBuilder;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper;

final class CanonicalAdminControllerTest extends WP_UnitTestCase {
	private $originalPost;

	protected function setUp(): void {
		parent::setUp();
		$this->originalPost = $_POST;
		wp_set_current_user( self::factory()->user->create( array( 'role' => 'administrator' ) ) );
	}

	protected function tearDown(): void {
		$_POST = $this->originalPost;
		parent::tearDown();
	}

	public function testSettingsFormMappingKeepsThePublicFieldsAtThePresentationBoundary(): void {
		$mapper = new OptionSettingsRequestMapper();
		$input = array(
			'title' => ' <b>Settings title</b> ',
			'iconset' => 'flat',
			'show_in' => array( 'show_left' => '1' ),
			'icons' => array( 'facebook' => '1', 'twitter' => '1' ),
			'use_port' => '1',
		);
		$canonical = $mapper->toCanonical( $input );

		$this->assertSame( 'flat', $canonical['icon_set'] );
		$this->assertTrue( $canonical['placements'][ Placement::LEFT ] );
		$this->assertSame( '1', $canonical['networks']['x'] );

		$settings = new Settings(
			'Settings title',
			'flat',
			'square',
			array(
				Placement::LEFT => true,
				Placement::RIGHT => false,
				Placement::BEFORE_CONTENT => false,
				Placement::AFTER_CONTENT => false,
			),
			array(
				Placement::LEFT => 'square',
				Placement::RIGHT => 'square',
				Placement::BEFORE_CONTENT => 'square',
				Placement::AFTER_CONTENT => 'square',
			),
			array(),
			array(),
			'',
			false,
			false,
			true,
			false
		);

		$this->assertSame(
			array(
				'title' => 'Settings title',
				'iconset' => 'flat',
				'show_in' => array( 'show_left' => '1' ),
				'icons' => array( 'facebook' => '1', 'twitter' => '1' ),
				'use_port' => true,
			),
			$mapper->toStoredSubmission( $settings, $input )
		);
	}

	public function testMetaboxControllerRetainsTheStoredMetaAndNonceContract(): void {
		$config = new PluginConfig( new PluginPaths( dirname( __DIR__, 2 ) . '/html-social-share.php' ) );
		$controller = new MetaboxController( $config );
		$postId = self::factory()->post->create( array( 'post_type' => 'page' ) );

		ob_start();
		$controller->render( get_post( $postId ) );
		$output = (string) ob_get_clean();
		$this->assertStringContainsString( 'name="' . $config->metaboxNonceField() . '"', $output );
		$this->assertStringContainsString( 'name="' . $config->disabledMetaKey() . '"', $output );

		$_POST = array(
			$config->metaboxNonceField() => wp_create_nonce( $config->metaboxNonceAction() ),
			'post_type' => 'page',
			$config->disabledMetaKey() => 'on',
		);
		$controller->save( $postId );
		$this->assertSame( 'on', get_post_meta( $postId, $config->disabledMetaKey(), true ) );
	}

	public function testIconSetPayloadUsesExplicitlyLocalizedBuiltInLabels(): void {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );
		$payload = ( new IconSetPayloadBuilder(
			$iconSets,
			$networks,
			new IconSetAssetResolver( $root, 'https://example.test/plugin' )
		) )->settingsPayload();
		$labels = array();
		foreach ( $payload as $iconSet ) {
			$labels[ $iconSet['id'] ] = $iconSet['name'];
		}

		$this->assertSame(
			array(
				'default' => 'Default',
				'flat' => 'Flat',
				'long-shadows' => 'Long Shadows',
				'prajin' => 'Prajin',
				'bootstrap-solid' => 'Bootstrap Solid',
				'tabler-outline' => 'Tabler Outline',
			),
			$labels
		);
		$this->assertSame( 'Facebook', $payload[0]['icons'][0]['name'] );
		$this->assertSame( 'X (formerly Twitter)', $payload[0]['icons'][1]['name'] );
	}
}
