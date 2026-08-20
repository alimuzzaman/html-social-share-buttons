<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade;

/**
 * Characterizes the renderer through its canonical facade. The JSON fixture
 * records the approved 3.0.0 HTML contract, including additive accessible
 * names; neither a global runtime nor a legacy renderer produces the output.
 */
final class CanonicalRendererParityTest extends WP_UnitTestCase {
	private $facade;
	private $originalRequestUri;
	private $originalPost;

	protected function setUp(): void {
		parent::setUp();
		$this->facade = LegacyApi::plugin()->renderer();
		$this->assertInstanceOf( RenderFacade::class, $this->facade );
		$this->originalRequestUri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;
		$this->originalPost = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		hssb_test_prepare_frontend_context();
	}

	protected function tearDown(): void {
		remove_filter( 'home_url', 'hssb_test_frontend_home_url', PHP_INT_MAX );
		remove_filter( 'plugins_url', 'hssb_test_frontend_plugins_url', PHP_INT_MAX );
		remove_filter( 'zm_sh_title', 'hssb_test_frontend_title', PHP_INT_MAX );
		$_SERVER['REQUEST_URI'] = null === $this->originalRequestUri ? '' : $this->originalRequestUri;
		$GLOBALS['post'] = $this->originalPost;
		parent::tearDown();
	}

	public function testCanonicalFacadeMatchesTheHistoricalRendererGoldenMaster(): void {
		$scenarios = hssb_test_load_frontend_scenarios(
			dirname( __DIR__ ) . '/frontend-output-scenarios.json'
		);
		$baseline = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/frontend-output-baseline.json' ),
			true
		);

		foreach ( $scenarios as $scenario ) {
			if ( isset( $scenario['entrypoint'] ) && 'renderer' !== $scenario['entrypoint'] ) {
				continue;
			}

			$options = $this->optionsForScenario( $scenario );
			$outcome = $this->facade->render(
				$options,
				0,
				new ShareContext(
					'https://example.test/frontend-contract/?preview=true',
					'Frontend Contract Title'
				)
			);

			$this->assertSame(
				$baseline['scenarios'][ $scenario['name'] ]['output'],
				hssb_test_normalize_frontend_output( $outcome->html() ),
				'Canonical renderer parity failed for scenario: ' . $scenario['name']
			);
		}
	}

	public function testCanonicalFacadeCollectsStylesAndPrintedIconsForFrontendOwnership(): void {
		$outcome = $this->facade->render(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'class' => 'left',
				'icons' => array( 'facebook' => 1, 'x' => 1 ),
			),
			0,
			new ShareContext( 'https://example.test/', 'Example' )
		);

		$this->assertArrayHasKey( 'default', $outcome->stylesheets() );
		$this->assertSame(
			array( "default_square\0_facebook", "default_square\0_x" ),
			array_keys( $outcome->printedIcons() )
		);
		$this->assertSame( 'twitter', $outcome->printedIcons()[ "default_square\0_x" ]['class'] );
	}

	private function optionsForScenario( array $scenario ): array {
		$schema = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/settings-schema-baseline.json' ),
			true
		);
		$options = isset( $schema['default_options'] ) && is_array( $schema['default_options'] )
			? $schema['default_options'] : array();
		$replace = isset( $scenario['replace'] ) && is_array( $scenario['replace'] ) ? $scenario['replace'] : array();
		$overrides = isset( $scenario['options'] ) && is_array( $scenario['options'] ) ? $scenario['options'] : array();

		foreach ( $overrides as $key => $value ) {
			if ( in_array( $key, $replace, true ) ) {
				$options[ $key ] = $value;
			} elseif ( is_array( $value ) && isset( $options[ $key ] ) && is_array( $options[ $key ] ) ) {
				$options[ $key ] = array_replace_recursive( $options[ $key ], $value );
			} else {
				$options[ $key ] = $value;
			}
		}

		return $options;
	}
}
