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
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\BuilderLabels;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Widget\ShareWidget;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\WpBakery\WpBakeryRegistrar;

final class CanonicalBuilderIntegrationTest extends WP_UnitTestCase {
	private $originalPost;

	protected function setUp(): void {
		parent::setUp();
		$this->originalPost = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
	}

	protected function tearDown(): void {
		$GLOBALS['post'] = $this->originalPost;
		parent::tearDown();
	}

	public function testWidgetRetainsStoredSelectionFormatsWithoutLegacyRuntime(): void {
		$services = $this->services();
		$widget = new ShareWidget(
			$services['renderer'],
			$services['settings'],
			$services['iconSets'],
			$services['networks'],
			$services['assets'],
			$services['config']
		);

		$this->assertSame(
			array(
				'title'        => 'Widget title',
				'icons'        => array( 'facebook' => '1', 'x' => '1' ),
				'iconset_type' => 'circle',
				'iconset'      => 'flat',
			),
			$widget->update(
				array(
					'title'        => ' <b>Widget title</b> ',
					'icons'        => array( 'facebook' => '1', 'twitter' => '1' ),
					'iconset_type' => 'CIRCLE',
					'iconset'      => 'Flat',
				),
				array()
			)
		);

		$postId = self::factory()->post->create();
		$GLOBALS['post'] = get_post( $postId );
		ob_start();
		$widget->widget(
			array( 'before_widget' => '<section>', 'after_widget' => '</section>' ),
			array(
				'title'        => '',
				'icons'        => array( 'facebook', 'x' ),
				'iconset_type' => 'square',
				'iconset'      => 'default',
			)
		);
		$output = (string) ob_get_clean();

		$this->assertStringContainsString( 'zmshbt in_widget default square', $output );
		$this->assertStringContainsString( 'facebook', $output );
		$this->assertStringContainsString( 'twitter', $output );
		$this->assertStringNotContainsString( '%%permalink%%', $output );
	}

	public function testWpBakeryCanRenderStoredShortcodeAttributesWithoutShortcodeCallback(): void {
		$services = $this->services(
			array(
				'facebook' => 'https://www.facebook.com/hssb',
				'mail'     => 'mailto:hello@example.com',
			)
		);
		$integration = new WpBakeryRegistrar(
			$services['renderer'],
			$services['settings'],
			$services['iconSets'],
			$services['networks'],
			$services['assets'],
			$services['config']
		);
		$postId = self::factory()->post->create( array( 'post_title' => 'Builder post' ) );
		$GLOBALS['post'] = get_post( $postId );

		$fixture = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/builder-storage-baseline.json' ),
			true
		);
		$attributes = $fixture['wpbakery']['attributes'];
		$attributes['url'] = '%%permalink%%';
		$output = $integration->render( $attributes );

		$this->assertStringContainsString( '<h3>Stored title</h3>', $output );
		$this->assertStringContainsString( 'zmshbt in_shortcode flat circle', $output );
		$this->assertStringContainsString( 'facebook', $output );
		$this->assertStringContainsString( 'twitter', $output );
		$this->assertStringNotContainsString( '%%permalink%%', $output );
		$this->assertStringNotContainsString( '%25%25permalink%25%25', $output );
		$this->assertStringContainsString( 'zmshbt-profile-link', $output );
		$this->assertStringContainsString( 'https://www.facebook.com/hssb', $output );
		$this->assertStringContainsString( 'mailto:hello@example.com', $output );
	}

	public function testWidgetInheritsProfilesForNumericStoredSelections(): void {
		$services = $this->services(
			array( 'facebook' => 'https://www.facebook.com/hssb' )
		);
		$widget = new ShareWidget(
			$services['renderer'],
			$services['settings'],
			$services['iconSets'],
			$services['networks'],
			$services['assets'],
			$services['config']
		);
		$postId = self::factory()->post->create();
		$GLOBALS['post'] = get_post( $postId );

		ob_start();
		$widget->widget(
			array(),
			array(
				'title'        => '',
				'icons'        => array( 'facebook', 'x' ),
				'iconset_type' => 'square',
				'iconset'      => 'default',
			)
		);
		$output = (string) ob_get_clean();

		$this->assertStringContainsString( 'zmshbt-profile-link', $output );
		$this->assertStringContainsString( 'https://www.facebook.com/hssb', $output );
		$this->assertStringContainsString( 'class="facebook"', $output );
		$this->assertStringContainsString( 'class="facebook zmshbt-profile-link"', $output );

		ob_start();
		$widget->widget(
			array(),
			array(
				'title'         => '',
				'icons'         => array( 'facebook' ),
				'iconset_type'  => 'square',
				'iconset'       => 'default',
				'profile_links' => array(),
			)
		);
		$withoutOverride = (string) ob_get_clean();
		$this->assertStringNotContainsString( 'zmshbt-profile-link', $withoutOverride );
	}

	public function testBuilderIntegrationsHaveNoCompatibilityOrShortcodeDependency(): void {
		$root = dirname( __DIR__, 2 ) . '/src/Presentation/Integration';
		foreach (
			array(
				$root . '/Widget/ShareWidget.php',
				$root . '/Elementor/ElementorShareWidget.php',
				$root . '/WpBakery/WpBakeryRegistrar.php',
			) as $file
		) {
			$source = (string) file_get_contents( $file );
			$this->assertStringNotContainsString( 'Compatibility\\Legacy', $source );
			$this->assertStringNotContainsString( 'LegacyRuntime', $source );
			$this->assertStringNotContainsString( 'zm_sh_shortcode_cb', $source );
			$this->assertStringNotContainsString( 'global $', $source );
		}
	}

	public function testBundledBuilderChoiceLabelsUseThePluginTextDomain(): void {
		$this->assertSame(
			__( 'Default', 'html-social-share-buttons' ),
			BuilderLabels::iconSet( 'default', 'not used' )
		);
		$this->assertSame(
			__( 'X (formerly Twitter)', 'html-social-share-buttons' ),
			BuilderLabels::wpBakeryNetwork( 'x', 'not used' )
		);
		$this->assertSame(
			__( 'Circle', 'html-social-share-buttons' ),
			BuilderLabels::shape( 'circle', 'not used' )
		);
		$this->assertSame(
			'Extension label',
			BuilderLabels::iconSet( 'extension-pack', 'Extension label' )
		);
	}

	private function services( array $profileLinks = array() ) {
		$root = dirname( __DIR__, 2 );
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$iconSets = ( new ManifestIconSetProvider( $root . '/resources/iconsets' ) )
			->createRegistry( $networks );
		$paths = PluginPaths::fromPluginFile( $root . '/html-social-share.php' );
		$settings = new class( $profileLinks ) implements SettingsRepository {
			private $profileLinks;

			public function __construct( array $profileLinks ) {
				$this->profileLinks = $profileLinks;
			}

			public function load() {
				$defaults = SettingsDefaults::create();

				return new Settings(
					$defaults->title(),
					$defaults->iconSetId(),
					$defaults->defaultIconShape(),
					$defaults->placements(),
					$defaults->placementShapes(),
					$defaults->networkStates(),
					$defaults->shareTemplates(),
					$defaults->excludedContent(),
					$defaults->analyticsEnabled(),
					$defaults->autoHideEnabled(),
					$defaults->preserveUrlPort(),
					$defaults->noFollow(),
					$this->profileLinks
				);
			}

			public function save( Settings $settings ) {
				return $settings;
			}
		};

		return array(
			'renderer' => new RenderFacade(
				$networks,
				$iconSets,
				new IconSetAssetResolver(
					$paths->assetsDirectory() . 'iconsets',
					$paths->assetsUrl() . 'iconsets'
				)
			),
			'settings' => $settings,
			'iconSets' => $iconSets,
			'networks' => $networks,
			'assets'   => new AssetCollector( $paths->url() . 'iconset/default/style.css' ),
			'config'   => new PluginConfig( $paths ),
		);
	}
}
