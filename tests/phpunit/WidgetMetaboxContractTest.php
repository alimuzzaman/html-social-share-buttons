<?php

final class WidgetMetaboxContractTest extends WP_UnitTestCase {
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

	public function testWidgetSavesAndRendersSelectedNetworks(): void {
		$widget = $this->widget();
		$saved = $widget->update(
			array(
				'title' => ' <b>Widget title</b> ',
				'icons' => array(
					'facebook' => '1',
					'x' => '1',
				),
				'iconset_type' => 'CIRCLE',
				'iconset' => 'Flat',
			),
			array()
		);

		$this->assertSame(
			array(
				'title' => 'Widget title',
				'icons' => array( 'facebook' => '1', 'x' => '1' ),
				'iconset_type' => 'circle',
				'iconset' => 'flat',
			),
			$saved
		);

		ob_start();
		$widget->widget(
			array(
				'before_widget' => '<section>',
				'after_widget' => '</section>',
				'before_title' => '<h2>',
				'after_title' => '</h2>',
			),
			$saved
		);
		$output = (string) ob_get_clean();

		$this->assertStringStartsWith(
			'<section><h2>Widget title</h2><div class="zmshbt in_widget flat circle">',
			$output
		);
		$this->assertStringContainsString( 'class="facebook"', $output );
		$this->assertStringContainsString( 'class="twitter"', $output );
	}

	public function testWidgetRendersLegacyNumericNetworkStorage(): void {
		$widget = $this->widget();

		ob_start();
		$widget->widget(
			array(),
			array(
				'title' => '',
				'icons' => array( 'facebook', 'x' ),
				'iconset_type' => 'square',
				'iconset' => 'default',
			)
		);
		$output = (string) ob_get_clean();

		$this->assertStringContainsString( 'class="facebook"', $output );
		$this->assertStringContainsString( 'class="twitter"', $output );
	}

	public function testWidgetCanPersistAndApplyTheAdditiveProfileLinkMode(): void {
		update_option( 'zm_shbt_fld', array( 'profile_links' => array( 'facebook' => 'https://facebook.com/example' ) ) );
		$widget = $this->widget();
		$saved = $widget->update(
			array(
				'title'              => '',
				'icons'              => array( 'facebook' => '1' ),
				'iconset_type'       => 'square',
				'iconset'            => 'default',
				'profile_links_mode' => 'none',
			),
			array()
		);

		$this->assertSame( 'none', $saved['profile_links_mode'] );
		ob_start();
		$widget->widget( array(), $saved );
		$output = (string) ob_get_clean();
		$this->assertStringNotContainsString( 'zmshbt-profile-link', $output );
	}

	public function testMetaboxRenderAndAuthorizedSaveMatchThePersistedContract(): void {
		$postId = self::factory()->post->create( array( 'post_type' => 'page' ) );
		$metabox = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->metabox();
		$post = get_post( $postId );

		ob_start();
		$metabox->render( $post );
		$output = (string) ob_get_clean();

		$this->assertStringContainsString( 'name="zm_sh_mtbox"', $output );
		$this->assertStringContainsString( 'id="_zm_sh_disable_share"', $output );
		$this->assertStringContainsString( 'name="_zm_sh_disable_share"', $output );

		$_POST = array(
			'zm_sh_mtbox' => wp_create_nonce( 'zm_sh_metabox' ),
			'post_type' => 'page',
			'_zm_sh_disable_share' => 'on',
		);
		$metabox->save( $postId );
		$this->assertSame( 'on', get_post_meta( $postId, '_zm_sh_disable_share', true ) );

		unset( $_POST['_zm_sh_disable_share'] );
		$metabox->save( $postId );
		$this->assertSame( '', get_post_meta( $postId, '_zm_sh_disable_share', true ) );
		$this->assertSame( array( '' ), get_post_meta( $postId, '_zm_sh_disable_share', false ) );
	}

	public function testMetaboxRejectsAnInvalidNonce(): void {
		$postId = self::factory()->post->create( array( 'post_type' => 'post' ) );
		update_post_meta( $postId, '_zm_sh_disable_share', 'on' );
		$metabox = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->metabox();

		$_POST = array(
			'zm_sh_mtbox' => 'invalid',
			'post_type' => 'post',
		);
		$metabox->save( $postId );

		$this->assertSame( 'on', get_post_meta( $postId, '_zm_sh_disable_share', true ) );
	}

	private function widget() {
		$plugin = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin();

		return new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Integration\Widget\ShareWidget(
			$plugin->renderer(),
			$plugin->settings(),
			$plugin->iconSets(),
			$plugin->networks(),
			$plugin->frontend()->assets(),
			$plugin->config()
		);
	}
}
