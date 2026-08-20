<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;

final class LegacyApiRenderBridgeTest extends WP_UnitTestCase {
	private $originalOption;
	private $originalPost;

	protected function setUp(): void {
		parent::setUp();
		$this->originalOption = get_option( 'zm_shbt_fld', null );
		$this->originalPost = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
	}

	protected function tearDown(): void {
		if ( null === $this->originalOption ) {
			delete_option( 'zm_shbt_fld' );
		} else {
			update_option( 'zm_shbt_fld', $this->originalOption );
		}
		$GLOBALS['post'] = $this->originalPost;
		$this->go_to( home_url( '/' ) );
		LegacyApi::plugin()->frontend()->detectExclusion();
		parent::tearDown();
	}

	public function testLegacyPhpApiUsesCanonicalFrontendProfileInheritance(): void {
		update_option(
			'zm_shbt_fld',
			array(
				'iconset' => 'default',
				'profile_links' => array( 'facebook' => 'https://example.test/community' ),
			)
		);

		$output = zm_sh_btn(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'icons' => array( 'facebook' => 'on' ),
				'class' => 'in_php_function',
			)
		);

		$this->assertStringContainsString( 'in_php_function', $output );
		$this->assertStringContainsString( 'zmshbt-profile-link', $output );
		$this->assertStringContainsString( 'https://example.test/community', $output );
	}

	public function testLegacyPublicObjectsReceiveCurrentLegacyShapedOptions(): void {
		update_option(
			'zm_shbt_fld',
			array(
				'title' => 'Current title',
				'iconset' => 'flat',
				'icons' => array( 'twitter' => '1' ),
			)
		);

		$share = new zm_social_share();
		$icons = new zm_sh_iconset();
		$form = new zm_form();

		$this->assertSame( 'Current title', $share->options['title'] );
		$this->assertSame( 'flat', $icons->options['iconset'] );
		$this->assertSame( '1', $share->options['icons']['twitter'] );
		$this->assertSame( $share->options, $form->options );
	}

	public function testLegacyFormHidesDefaultForNewSettingsAndRetainsAnExplicitSelection(): void {
		delete_option( 'zm_shbt_fld' );
		$newForm = new zm_form();
		ob_start();
		$newForm->select_iconset( 'iconset', 'Icon set' );
		$newMarkup = (string) ob_get_clean();

		$this->assertStringContainsString( "value='bootstrap-solid'", $newMarkup );
		$this->assertStringNotContainsString( "value='default'", $newMarkup );

		update_option( 'zm_shbt_fld', array( 'iconset' => 'default' ) );
		$legacyForm = new zm_form();
		ob_start();
		$legacyForm->select_iconset( 'iconset', 'Icon set' );
		$legacyMarkup = (string) ob_get_clean();

		$this->assertMatchesRegularExpression(
			"/value='default'[^>]*selected='selected'/",
			$legacyMarkup
		);
	}

	public function testLegacyPhpApiHonorsCanonicalFrontendExclusionState(): void {
		$postId = self::factory()->post->create();
		update_post_meta( $postId, '_zm_sh_disable_share', 'on' );
		$this->go_to( get_permalink( $postId ) );
		LegacyApi::plugin()->frontend()->detectExclusion();

		$this->assertNull(
			LegacyApi::render(
				array(
					'iconset' => 'default',
					'iconset_type' => 'square',
					'icons' => array( 'facebook' => 'on' ),
				),
				$postId
			)
		);
	}
}
