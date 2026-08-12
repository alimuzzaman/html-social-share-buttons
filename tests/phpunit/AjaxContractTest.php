<?php

/**
 * @group ajax
 */
final class AjaxContractTest extends WP_Ajax_UnitTestCase {
	protected function tearDown(): void {
		$_POST = array();
		$_GET = array();
		$_REQUEST = array();
		delete_option( 'zm_shbt_fld' );
		parent::tearDown();
	}

	public function testAjaxRenderingUsesTheRequestedPostCanonicalPermalink(): void {
		$postId = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$GLOBALS['post'] = null;
		$_REQUEST['post_id'] = (string) $postId;

		$output = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()
			->shortcode()
			->render( array( 'icons' => 'facebook' ) );

		$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $output );
		$this->assertStringNotContainsString( '%%permalink%%', $output );
		$this->assertStringNotContainsString( '%25%25permalink%25%25', $output );
	}

	public function testSettingsSearchReturnsPublishedContentForAdministrators(): void {
		$this->_setRole( 'administrator' );
		$postId = self::factory()->post->create(
			array(
				'post_type' => 'page',
				'post_status' => 'publish',
				'post_title' => 'Contract Search Target',
			)
		);
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'query' => 'Contract Search',
		);

		$response = $this->requestJson( 'zm_sh_search_content' );

		$this->assertTrue( $response['success'] );
		$this->assertSame( (string) $postId, $response['data'][0]['id'] );
		$this->assertSame(
			sprintf( '#%d - Contract Search Target (page)', $postId ),
			$response['data'][0]['token']
		);
	}

	public function testSettingsSearchRejectsUsersWithoutManageOptions(): void {
		$this->_setRole( 'subscriber' );
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'query' => 'Contract Search',
		);

		$response = $this->requestJson( 'zm_sh_search_content' );

		$this->assertFalse( $response['success'] );
		$this->assertSame(
			'You are not allowed to search content.',
			$response['data']['message']
		);
	}

	public function testSettingsSavePreservesTheLegacyResponseAndStoredShape(): void {
		$this->_setRole( 'administrator' );
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'settings' => http_build_query(
				array(
					'zm_shbt_fld' => array(
						'title' => 'AJAX contract title',
						'iconset' => 'flat',
						'show_in' => array( 'show_left' => '1' ),
						'icons' => array( 'facebook' => '1', 'x' => '1' ),
						'use_port' => '1',
					),
				)
			),
		);

		$response = $this->requestJson( 'zm_sh_save_settings' );
		$expected = array(
			'title' => 'AJAX contract title',
			'iconset' => 'flat',
			'show_in' => array( 'show_left' => '1' ),
			'icons' => array( 'facebook' => '1', 'x' => '1' ),
			'use_port' => true,
		);

		$this->assertTrue( $response['success'] );
		$this->assertSame( 'Settings saved.', $response['data']['message'] );
		$this->assertSame( $expected, $response['data']['options'] );
		$this->assertSame( $expected, get_option( 'zm_shbt_fld' ) );
	}

	public function testSettingsSavePersistsOnlyExplicitPlacementProfileOverrides(): void {
		$this->_setRole( 'administrator' );
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'settings' => http_build_query(
				array(
					'zm_shbt_fld' => array(
						'profile_link_placements' => array(
							'show_left'        => 'none',
							'show_right'       => 'inherit',
							'show_before_post' => 'invalid',
						),
					),
				)
			),
		);

		$response = $this->requestJson( 'zm_sh_save_settings' );
		$expected = array(
			'profile_link_placements' => array( 'show_left' => 'none' ),
		);

		$this->assertTrue( $response['success'] );
		$this->assertSame( $expected, $response['data']['options'] );
		$this->assertSame( $expected, get_option( 'zm_shbt_fld' ) );
	}

	public function testSettingsSaveRejectsMissingSettings(): void {
		$this->_setRole( 'administrator' );
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'settings' => '',
		);

		$response = $this->requestJson( 'zm_sh_save_settings' );

		$this->assertFalse( $response['success'] );
		$this->assertSame( 'No settings were received.', $response['data']['message'] );
	}

	public function testSettingsAjaxRejectsAnInvalidNonce(): void {
		$this->_setRole( 'administrator' );
		$_POST = array(
			'nonce' => 'invalid',
			'query' => 'Contract Search',
		);

		try {
			$this->_handleAjax( 'zm_sh_search_content' );
			$this->fail( 'Expected the invalid AJAX nonce to terminate the request.' );
		} catch ( WPAjaxDieStopException $exception ) {
			$this->assertSame( '-1', $exception->getMessage() );
		}
	}

	public function testIconsetDetailAndPreviewResponseShapesRemainStable(): void {
		$this->_setRole( 'administrator' );
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'iconset' => 'default',
		);

		$details = $this->requestJson( 'get_iconset_details' );
		$this->assertSame( 'Facebook', $details['facebook'] );
		$this->assertSame( 'X (formerly Twitter)', $details['x'] );

		$this->_last_response = '';
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'iconsetId' => 'default',
		);
		$preview = $this->requestRaw( 'get_iconset_preview' );

		$this->assertStringEndsWith( '/iconset/default/preview.png', $preview );
	}

	public function testIconsetObjectEndpointReturnsTheLegacyPublicProperties(): void {
		$this->_setRole( 'administrator' );
		$_POST = array(
			'nonce' => wp_create_nonce( 'zm_sh_admin' ),
			'iconsetId' => 'flat',
		);

		$iconSet = $this->requestJson( 'get_iconset' );

		$this->assertSame( 'flat', $iconSet['id'] );
		$this->assertSame( 'Flat', $iconSet['name'] );
		$this->assertSame( array( 'square', 'circle' ), $iconSet['types'] );
		$this->assertSame( 'Twitter.png', $iconSet['icons']['x']['image'] );
	}

	private function requestJson( $action ) {
		$response = $this->requestRaw( $action );
		$decoded = json_decode( $response, true );
		$this->assertIsArray( $decoded, 'AJAX response was not valid JSON: ' . $response );

		return $decoded;
	}

	private function requestRaw( $action ) {
		$this->_last_response = '';
		try {
			$this->_handleAjax( $action );
		} catch ( WPAjaxDieContinueException $exception ) {
			unset( $exception );
		} catch ( WPAjaxDieStopException $exception ) {
			if ( 'get_iconset_preview' !== $action ) {
				throw $exception;
			}

			return $exception->getMessage();
		}

		return $this->_last_response;
	}
}
