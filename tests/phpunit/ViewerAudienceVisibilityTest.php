<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;

final class ViewerAudienceVisibilityTest extends WP_UnitTestCase {
	private $originalPost;

	protected function setUp(): void {
		parent::setUp();
		$this->originalPost = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
	}

	protected function tearDown(): void {
		delete_option( 'zm_shbt_fld' );
		wp_set_current_user( 0 );
		$GLOBALS['post'] = $this->originalPost;
		parent::tearDown();
	}

	public function testLegacySettingsDefaultEveryAudienceToVisible(): void {
		$settings = LegacyApi::plugin()->settings()->load();

		$this->assertTrue( $settings->showForCurrentUser() );
		$this->assertTrue( $settings->showForLoggedInUser() );
		$this->assertTrue( $settings->showForLoggedOutUser() );
	}

	public function testCurrentAuthorUsesTheCurrentUserSetting(): void {
		$authorId = self::factory()->user->create();
		$postId = $this->publishedPost( $authorId );
		$this->storeAudience( false, true, true );
		wp_set_current_user( $authorId );

		$this->assertSame( '', $this->shortcodeOutput( $postId ) );
	}

	public function testAnotherAuthenticatedViewerUsesTheLoggedInSetting(): void {
		$authorId = self::factory()->user->create();
		$viewerId = self::factory()->user->create();
		$postId = $this->publishedPost( $authorId );
		$this->storeAudience( true, false, true );
		wp_set_current_user( $viewerId );

		$this->assertSame( '', $this->shortcodeOutput( $postId ) );
	}

	public function testGuestUsesTheLoggedOutSettingAcrossCanonicalAdapters(): void {
		$postId = $this->publishedPost( self::factory()->user->create() );
		$this->storeAudience( true, true, false );
		wp_set_current_user( 0 );
		$this->activatePost( $postId );

		$this->assertSame( '', zm_sh_shortcode_cb( array( 'icons' => 'facebook' ) ) );
		$this->assertSame(
			'',
			LegacyApi::plugin()->block()->render(
				array( 'icons' => array( 'facebook' ) ),
				$postId
			)
		);
		$this->assertSame(
			'',
			LegacyApi::plugin()->wpBakery()->render(
				array( 'icons' => 'facebook' )
			)
		);
		$this->assertSame(
			'',
			do_blocks(
				'<!-- wp:html-social-share/social-share {"icons":["facebook"]} /-->'
			)
		);
		$this->assertSame(
			'',
			zm_sh_btn(
				array(
					'iconset' => 'default',
					'icons' => array( 'facebook' ),
				)
			)
		);
	}

	public function testAllowedAudienceRetainsCanonicalPermalinkRendering(): void {
		$postId = $this->publishedPost( self::factory()->user->create() );
		$this->storeAudience( true, true, true );
		wp_set_current_user( 0 );

		$output = $this->shortcodeOutput( $postId );

		$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $output );
		$this->assertStringNotContainsString( '%%permalink%%', $output );
		$this->assertStringNotContainsString( '%25%25permalink%25%25', $output );
	}

	private function publishedPost( $authorId ) {
		return self::factory()->post->create(
			array(
				'post_author' => $authorId,
				'post_status' => 'publish',
				'post_title' => 'Audience visibility contract',
			)
		);
	}

	private function shortcodeOutput( $postId ) {
		$this->activatePost( $postId );

		return zm_sh_shortcode_cb( array( 'icons' => 'facebook' ) );
	}

	private function activatePost( $postId ) {
		$this->go_to( get_permalink( $postId ) );
		$GLOBALS['post'] = get_post( $postId );
	}

	private function storeAudience( $currentUser, $loggedInUser, $loggedOutUser ) {
		update_option(
			'zm_shbt_fld',
			array(
				'show_for_current_user' => (bool) $currentUser,
				'show_for_logged_in_user' => (bool) $loggedInUser,
				'show_for_logged_out_user' => (bool) $loggedOutUser,
			)
		);
	}
}
