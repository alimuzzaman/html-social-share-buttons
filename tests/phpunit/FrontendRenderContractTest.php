<?php

require_once dirname( __DIR__ ) . '/support/frontend-output-contract.php';

final class FrontendRenderContractTest extends WP_UnitTestCase {
	private $original_request_uri;
	private $original_post;

	protected function setUp(): void {
		parent::setUp();

		$this->original_request_uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : null;
		$this->original_post        = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		hssb_test_prepare_frontend_context();
	}

	protected function tearDown(): void {
		remove_filter( 'home_url', 'hssb_test_frontend_home_url', PHP_INT_MAX );
		remove_filter( 'plugins_url', 'hssb_test_frontend_plugins_url', PHP_INT_MAX );
		remove_filter( 'zm_sh_title', 'hssb_test_frontend_title', PHP_INT_MAX );

		if ( null === $this->original_request_uri ) {
			$_SERVER['REQUEST_URI'] = '';
		} else {
			$_SERVER['REQUEST_URI'] = $this->original_request_uri;
		}
		$GLOBALS['post'] = $this->original_post;

		parent::tearDown();
	}

	public function testFrontendRendererMatchesTheGoldenMaster(): void {
		$scenario_file = dirname( __DIR__ ) . '/frontend-output-scenarios.json';
		$baseline_file = dirname( __DIR__ ) . '/fixtures/frontend-output-baseline.json';
		$scenarios     = hssb_test_load_frontend_scenarios( $scenario_file );
		$baseline      = json_decode( (string) file_get_contents( $baseline_file ), true );

		$this->assertIsArray( $baseline );
		$this->assertArrayHasKey( 'scenarios', $baseline );
		$this->assertNotEmpty( $baseline['scenarios'], 'The frontend golden master must not be empty.' );

		$current = hssb_test_capture_frontend_scenarios( $scenarios );
		$entrypoints = array();
		foreach ( $scenarios as $scenario ) {
			$entrypoints[ $scenario['name'] ] = isset( $scenario['entrypoint'] )
				? $scenario['entrypoint']
				: 'renderer';
		}
		$this->assertSame(
			array_keys( $baseline['scenarios'] ),
			array_keys( $current ),
			'The baseline and scenario catalog must contain the same ordered names.'
		);

		foreach ( $current as $name => $result ) {
			$expected = $baseline['scenarios'][ $name ]['output'];
			$actual   = $result['output'];
			if ( 'footer' === $entrypoints[ $name ] ) {
				$expected = hssb_test_normalize_kses_serialization( $expected );
				$actual   = hssb_test_normalize_kses_serialization( $actual );
			}
			$this->assertSame(
				$expected,
				$actual,
				sprintf( 'Frontend output changed for scenario "%s".', $name )
			);
		}
	}
}
