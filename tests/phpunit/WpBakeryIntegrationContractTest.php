<?php

if ( ! function_exists( 'vc_map' ) ) {
	function vc_map( array $definition ) {
		$GLOBALS['hssb_wpbakery_definition'] = $definition;
	}
}

final class WpBakeryIntegrationContractTest extends WP_UnitTestCase {
	private $surface;

	protected function setUp(): void {
		parent::setUp();
		unset( $GLOBALS['hssb_wpbakery_definition'] );
		$this->surface = json_decode(
			(string) file_get_contents( dirname( __DIR__ ) . '/fixtures/wordpress-surface-baseline.json' ),
			true
		);
	}

	public function testWpbakeryMapPreservesIdentityParametersAndStoredValues(): void {
		\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()
			->wpBakery()
			->registerElement();

		$this->assertArrayHasKey( 'hssb_wpbakery_definition', $GLOBALS );
		$definition = $GLOBALS['hssb_wpbakery_definition'];
		$this->assertSame( $this->surface['wpbakery']['base'], $definition['base'] );
		$this->assertArrayNotHasKey(
			'admin_enqueue_js',
			$definition,
			'The editor bundle is enqueued by WordPress so it is not loaded twice by WPBakery.'
		);

		$params = array();
		foreach ( $definition['params'] as $param ) {
			$params[ $param['param_name'] ] = $param;
		}

		$this->assertSame(
			array( 'title', 'iconset', 'iconset_type', 'icons', 'profile_links_mode' ),
			array_keys( $params )
		);
		$this->assertSame( 'textfield', $params['title']['type'] );
		$this->assertSame( 'dropdown', $params['iconset']['type'] );
		$this->assertSame( 'dropdown', $params['iconset_type']['type'] );
		$this->assertSame( 'checkbox', $params['icons']['type'] );
		$this->assertContains( 'bootstrap-solid', $params['iconset']['value'] );
		$this->assertContains( 'default', $params['iconset']['value'] );
		$this->assertContains( 'facebook', $params['icons']['value'] );
		$this->assertContains( 'x', $params['icons']['value'] );
		$this->assertSame( 'inherit', $params['profile_links_mode']['std'] );
		$this->assertContains( 'none', $params['profile_links_mode']['value'] );
	}

	public function testStoredLegacyDefaultStillRendersThroughWpbakery(): void {
		$output = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()
			->wpBakery()
			->render(
				array(
					'iconset' => 'default',
					'iconset_type' => 'square',
					'icons' => 'facebook',
				)
			);

		$this->assertMatchesRegularExpression( '/class=[\'\"]zmshbt in_shortcode default square[\'\"]/', $output );
		$this->assertStringContainsString( 'facebook', $output );
	}

	public function testWpbakeryProfileLinkModeIsAdditiveAndSuppressesOnlyWhenRequested(): void {
		update_option( 'zm_shbt_fld', array( 'profile_links' => array( 'facebook' => 'https://facebook.com/example' ) ) );
		$integration = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->wpBakery();

		$inherited = $integration->render( array( 'icons' => 'facebook' ) );
		$hidden = $integration->render( array( 'icons' => 'facebook', 'profile_links_mode' => 'none' ) );

		$this->assertStringContainsString( 'https://facebook.com/example', $inherited );
		$this->assertStringNotContainsString( 'zmshbt-profile-link', $hidden );
	}

	public function testWpbakeryEditorBundleUsesOneHandleAndReceivesItsNonce(): void {
		$settings = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->admin();
		$settings->enqueueAssets( 'post.php' );

		$contract = $this->surface['wpbakery'];
		$script = wp_scripts()->registered[ $contract['script_handle'] ];

		$this->assertSame( $contract['script_dependencies'], $script->deps );
		$this->assertStringEndsWith( '/' . $contract['script'], $script->src );
		$this->assertContains( $contract['script_handle'], wp_scripts()->queue );
		$this->assertStringContainsString(
			'var ' . $contract['localized_object'] . ' =',
			$script->extra['data']
		);
		$this->assertStringContainsString( '"nonce"', $script->extra['data'] );
		$this->assertStringContainsString( '"legacyIconsets":{"default":', $script->extra['data'] );
	}
}
