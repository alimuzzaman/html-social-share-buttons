<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\BuildShareButtons;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\IconSet\LegacyRegistryAdapter;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Network\LegacyNetworkMapper;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering\LegacyHtmlRenderer;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering\LegacyRenderFacade;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering\LegacyRenderRequestMapper;
use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering\LegacyShareUrlResolver;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;

require_once dirname( __DIR__ ) . '/support/frontend-output-contract.php';

final class LegacyRendererParityTest extends WP_UnitTestCase {
	private $builder;
	private $mapper;
	private $renderer;
	private $facade;
	private $networks;
	private $originalRequestUri;
	private $originalPost;

	protected function setUp(): void {
		parent::setUp();
		$this->originalRequestUri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;
		$this->originalPost = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		hssb_test_prepare_frontend_context();

		global $zm_sh;
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$bundle = ( new LegacyRegistryAdapter( $networks ) )->adapt( $zm_sh->iconsets );
		$this->networks = $bundle->networks();
		$this->builder = new BuildShareButtons(
			$this->networks,
			$bundle->iconSets(),
			new LegacyShareUrlResolver( $bundle )
		);
		$this->mapper = new LegacyRenderRequestMapper();
		$this->renderer = new LegacyHtmlRenderer( new LegacyNetworkMapper() );
		$this->facade = new LegacyRenderFacade( $networks );
	}

	protected function tearDown(): void {
		remove_filter( 'home_url', 'hssb_test_frontend_home_url', PHP_INT_MAX );
		remove_filter( 'plugins_url', 'hssb_test_frontend_plugins_url', PHP_INT_MAX );
		remove_filter( 'zm_sh_title', 'hssb_test_frontend_title', PHP_INT_MAX );
		$_SERVER['REQUEST_URI'] = null === $this->originalRequestUri ? '' : $this->originalRequestUri;
		$GLOBALS['post'] = $this->originalPost;
		parent::tearDown();
	}

	public function testNewPipelineMatchesEveryDirectLegacyRendererScenario(): void {
		$scenarios = hssb_test_load_frontend_scenarios(
			dirname( __DIR__ ) . '/frontend-output-scenarios.json'
		);

		foreach ( $scenarios as $scenario ) {
			if ( isset( $scenario['entrypoint'] ) && 'renderer' !== $scenario['entrypoint'] ) {
				continue;
			}

			$legacy = hssb_test_render_frontend_scenario( $scenario );
			$request = $this->mapper->map( $legacy['options'], $this->networks );
			$result = $this->builder->build(
				$request,
				new ShareContext(
					'https://example.test/frontend-contract/?preview=true',
					'Frontend Contract Title'
				)
			);
			$rewritten = $this->renderer->render( $request, $result );

			$this->assertSame(
				$legacy['output'],
				hssb_test_normalize_frontend_output( $rewritten ),
				'New renderer parity failed for scenario: ' . $scenario['name']
			);
		}
	}

	public function testCompatibilityFacadeMatchesEveryDirectLegacyRendererScenario(): void {
		$scenarios = hssb_test_load_frontend_scenarios(
			dirname( __DIR__ ) . '/frontend-output-scenarios.json'
		);

		global $zm_sh;
		foreach ( $scenarios as $scenario ) {
			if ( isset( $scenario['entrypoint'] ) && 'renderer' !== $scenario['entrypoint'] ) {
				continue;
			}

			$legacy = hssb_test_render_frontend_scenario( $scenario );
			$outcome = $this->facade->render( $legacy['options'], $zm_sh->iconsets );

			$this->assertSame(
				$legacy['output'],
				hssb_test_normalize_frontend_output( $outcome->html() ),
				'Compatibility facade parity failed for scenario: ' . $scenario['name']
			);
		}
	}

	public function testCompatibilityFacadePreservesFooterStyleInputs(): void {
		global $zm_sh;
		$options = array(
			'iconset' => 'default',
			'iconset_type' => 'square',
			'class' => 'left',
			'show_on' => 'show_left',
			'icons' => array(
				'facebook' => 1,
				'x' => 1,
			),
		);

		$outcome = $this->facade->render( $options, $zm_sh->iconsets );
		$iconSet = $zm_sh->iconsets->get_iconset( 'default' );

		$this->assertSame(
			array( 'default' => $iconSet->url . $iconSet->stylesheet ),
			$outcome->stylesheets()
		);
		$this->assertSame(
			array( "default_square\0_facebook", "default_square\0_x" ),
			array_keys( $outcome->printedIcons() )
		);
		$this->assertSame(
			$iconSet->url,
			$outcome->printedIcons()[ "default_square\0_x" ]['iconset_url']
		);
		$this->assertSame(
			'square',
			$outcome->printedIcons()[ "default_square\0_x" ]['iconset_type']
		);
	}

	public function testCompatibilityFacadePreservesCustomWrapperAndBuiltInIconClass(): void {
		global $zm_sh;
		$custom = clone $zm_sh->iconsets->get_iconset( 'default' );
		$custom->id = 'community';
		$custom->name = 'Community';
		$custom->icons['facebook']['class'] = 'community-facebook';
		$registry = new LegacyRenderRegistryStub(
			array(
				'default' => $zm_sh->iconsets->get_iconset( 'default' ),
				'community' => $custom,
			)
		);

		$outcome = $this->facade->render(
			array(
				'iconset' => 'community',
				'iconset_type' => 'square',
				'class' => 'theme-placement',
				'show_on' => 'show_left',
				'icons' => array( 'facebook' => 1 ),
			),
			$registry
		);

		$this->assertStringContainsString(
			"class='zmshbt theme-placement community square'",
			$outcome->html()
		);
		$this->assertStringContainsString(
			"<a class='community-facebook'",
			$outcome->html()
		);
		$this->assertSame(
			'community-facebook',
			$outcome->printedIcons()[ "community_square\0_facebook" ]['class']
		);
	}

	public function testCompatibilityFacadePreservesUnderscoreAddOnIdentifiers(): void {
		global $zm_sh;
		$custom = clone $zm_sh->iconsets->get_iconset( 'default' );
		$custom->id = 'community_pack';
		$custom->name = 'Community pack';
		$custom->types = array( 'round_shape' );
		$custom->icons = array(
			'social_net' => array(
				'id' => 'social_net',
				'name' => 'Social net',
				'class' => 'social_net',
				'image' => 'social_net.svg',
				'url' => 'https://example.test/share?url=%%permalink%%',
			),
		);
		$registry = new LegacyRenderRegistryStub(
			array( 'community_pack' => $custom )
		);
		$platforms = array();
		$filter = static function ( $template, $platform ) use ( &$platforms ) {
			$platforms[] = $platform;

			return $template;
		};
		add_filter( 'zm_sh_share_template', $filter, 10, 2 );

		try {
			$outcome = $this->facade->render(
				array(
					'iconset' => 'community_pack',
					'iconset_type' => 'round_shape',
					'icons' => array( 'social_net' => '1' ),
				),
				$registry
			);
		} finally {
			remove_filter( 'zm_sh_share_template', $filter, 10 );
		}

		$this->assertStringContainsString(
			"class='zmshbt left community_pack round_shape'",
			$outcome->html()
		);
		$this->assertStringContainsString( "<a class='social_net'", $outcome->html() );
		$this->assertSame( array( 'social_net' ), $platforms );
		$this->assertArrayHasKey(
			"community_pack_round_shape\0_social_net",
			$outcome->printedIcons()
		);
	}

	public function testUnselectedInvalidIconSetCannotBreakRendering(): void {
		global $zm_sh;
		$registry = new LegacyRenderRegistryStub(
			array(
				'default' => $zm_sh->iconsets->get_iconset( 'default' ),
				'broken' => (object) array(
					'id' => 'broken',
					'name' => '',
					'types' => array(),
					'icons' => array(),
				),
			)
		);

		$outcome = $this->facade->render(
			array(
				'iconset' => 'default',
				'icons' => array( 'facebook' => 1 ),
			),
			$registry
		);

		$this->assertStringContainsString( "<a class='facebook'", $outcome->html() );
	}

	public function testShareUrlHooksPreservePerButtonOrderAndSkipMissingIcons(): void {
		global $zm_sh;
		$trace = array();
		$templateFilter = static function ( $template, $platform ) use ( &$trace ) {
			$trace[] = 'template:' . $platform;

			return $template;
		};
		$placeholderFilter = static function ( $url ) use ( &$trace ) {
			$trace[] = 'placeholder';

			return $url;
		};
		add_filter( 'zm_sh_share_template', $templateFilter, 10, 2 );
		add_filter( 'zm_sh_placeholder', $placeholderFilter, 20 );

		try {
			$this->facade->render(
				array(
					'iconset' => 'prajin',
					'icons' => array(
						'facebook' => 1,
						'x' => 1,
						'mail' => 1,
						'unsupported' => 1,
					),
				),
				$zm_sh->iconsets
			);
		} finally {
			remove_filter( 'zm_sh_share_template', $templateFilter, 10 );
			remove_filter( 'zm_sh_placeholder', $placeholderFilter, 20 );
		}

		$this->assertSame(
			array(
				'template:facebook',
				'placeholder',
				'template:x',
				'placeholder',
			),
			$trace
		);
	}

	public function testDirectRendererSkipsHistoricallyInvalidNetworkKeys(): void {
		global $zm_sh;
		$outcome = $this->facade->render(
			array(
				'iconset' => 'default',
				'icons' => array(
					'FACEBOOK' => 1,
					'face book' => 1,
				),
			),
			$zm_sh->iconsets
		);

		$this->assertStringNotContainsString( '<a ', $outcome->html() );
	}
}

final class LegacyRenderRegistryStub {
	private $iconSets;

	public function __construct( array $iconSets ) {
		$this->iconSets = $iconSets;
	}

	public function get_iconsets() {
		return $this->iconSets;
	}

	public function get_iconset( $id ) {
		return isset( $this->iconSets[ $id ] )
			? $this->iconSets[ $id ]
			: $this->iconSets['default'];
	}
}
