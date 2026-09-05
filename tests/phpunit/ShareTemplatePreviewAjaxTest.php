<?php

/** @group ajax */
final class ShareTemplatePreviewAjaxTest extends WP_Ajax_UnitTestCase {
	protected function tearDown(): void {
		$_POST = array();
		$_REQUEST = array();
		parent::tearDown();
	}

	public function testPreviewDoesNotWriteSettingsOrFetchUrls(): void {
		$this->_setRole( 'administrator' );
		$before = get_option( 'zm_shbt_fld' );
		$http = function () { $this->fail( 'Preview attempted an HTTP request.' ); };
		add_filter( 'pre_http_request', $http );
		add_filter( 'hssb/share_template', $http );
		add_filter( 'hssb/share_url', $http );
		try {
			$result = $this->request( 'facebook', 'https://example.com/?url=%%permalink%%' );
			$this->assertTrue( $result['success'] );
			$this->assertSame( $before, get_option( 'zm_shbt_fld' ) );
			$this->assertStringContainsString( 'sample-post', $result['data']['resolved_url'] );
		} finally {
			remove_filter( 'pre_http_request', $http );
			remove_filter( 'hssb/share_template', $http );
			remove_filter( 'hssb/share_url', $http );
		}
	}

	public function testCapabilityAndInputShapeAreRequired(): void {
		$this->_setRole( 'subscriber' );
		$this->assertFalse( $this->request( 'facebook', '' )['success'] );
		$this->_setRole( 'administrator' );
		$this->assertFalse( $this->request( array(), '' )['success'] );
		$this->assertFalse( $this->request( 'facebook', array() )['success'] );
		$this->assertFalse( $this->request( 'unknown', '' )['success'] );
		$this->assertFalse( $this->request( 'facebook', str_repeat( 'x', 8193 ) )['success'] );
		$this->assertFalse( has_action( 'wp_ajax_nopriv_hssb_preview_share_template' ) );
	}

	public function testBadNonceIsRejected(): void {
		$this->_setRole( 'administrator' );
		$this->expectException( WPAjaxDieStopException::class );
		$this->request( 'facebook', '', 'invalid' );
	}

	private function request( $network, $template, $nonce = null ) {
		$_POST = array( 'network' => $network, 'template' => $template, 'nonce' => null === $nonce ? wp_create_nonce( 'zm_sh_admin' ) : $nonce );
		$_REQUEST = $_POST;
		$this->_last_response = '';
		try {
			$this->_handleAjax( 'hssb_preview_share_template' );
		} catch ( WPAjaxDieContinueException $exception ) {
			unset( $exception );
		}
		return json_decode( $this->_last_response, true );
	}
}
