<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;

final class ShareUrlResolutionContractTest extends WP_UnitTestCase {
	private $originalPost;

	protected function setUp(): void {
		parent::setUp();
		$this->originalPost = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
	}

	protected function tearDown(): void {
		$GLOBALS['post'] = $this->originalPost;
		parent::tearDown();
	}

	/**
	 * @dataProvider postTypeProvider
	 */
	public function testEveryFrontendAdapterUsesTheCanonicalPostPermalink( $postType ): void {
		$postId = self::factory()->post->create(
			array(
				'post_type' => $postType,
				'post_status' => 'publish',
				'post_title' => 'Canonical URL contract',
			)
		);
		$this->go_to( get_permalink( $postId ) );
		$GLOBALS['post'] = get_post( $postId );
		$encodedPermalink = rawurlencode( get_permalink( $postId ) );

		foreach ( $this->frontendAdapterOutputs( $postId ) as $adapter => $output ) {
			$this->assertStringContainsString( $encodedPermalink, $output, $adapter );
			$this->assertStringNotContainsString(
				rawurlencode( $encodedPermalink ),
				$output,
				$adapter
			);
			$this->assertNoPlaceholderOrDoubleEncoding( $output, $adapter );
		}
	}

	public function postTypeProvider(): array {
		return array(
			'singular post' => array( 'post' ),
			'singular page' => array( 'page' ),
		);
	}

	public function testArchiveLoopUsesTheCurrentLoopPostPermalink(): void {
		$postId = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$this->go_to( home_url( '/?s=share' ) );
		$GLOBALS['post'] = get_post( $postId );
		$encodedPermalink = rawurlencode( get_permalink( $postId ) );

		foreach ( $this->frontendAdapterOutputs( $postId ) as $adapter => $output ) {
			$this->assertStringContainsString( $encodedPermalink, $output, $adapter );
			$this->assertStringNotContainsString(
				rawurlencode( $encodedPermalink ),
				$output,
				$adapter
			);
			$this->assertNoPlaceholderOrDoubleEncoding( $output, $adapter );
		}
	}

	public function testExplicitCustomUrlIsEscapedAndEncodedExactlyOnce(): void {
		$url = 'https://destination.example/article?id=42&source=contract';
		$output = zm_sh_shortcode_cb(
			array(
				'icons' => 'facebook',
				'url' => $url,
			)
		);

		$this->assertStringContainsString( rawurlencode( $url ), $output );
		$this->assertStringNotContainsString( rawurlencode( rawurlencode( $url ) ), $output );
		$this->assertNoPlaceholderOrDoubleEncoding( $output, 'explicit shortcode URL' );
	}

	public function testPersistedDynamicBlockUsesTheActualCurrentPostPermalink(): void {
		$postId = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title' => 'Persisted block permalink contract',
			)
		);
		$this->go_to( get_permalink( $postId ) );
		$GLOBALS['post'] = get_post( $postId );

		$output = do_blocks(
			'<!-- wp:html-social-share/social-share {"icons":["facebook"],"profile_links_mode":"none"} /-->'
		);

		$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $output );
		$this->assertNoPlaceholderOrDoubleEncoding( $output, 'persisted dynamic block' );
	}

	public function testAutomaticPlacementUsesTheActualCurrentPostPermalink(): void {
		$postId = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title' => 'Automatic placement permalink contract',
			)
		);
		$this->go_to( get_permalink( $postId ) );
		$GLOBALS['post'] = get_post( $postId );

		$output = LegacyApi::plugin()->frontend()->filterContentWithOptions(
			'<p>Original content.</p>',
			array(
				'iconset' => 'default',
				'icons' => array( 'facebook' => '1' ),
				'show_in' => array( 'show_after_post' => '1' ),
			)
		);

		$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $output );
		$this->assertNoPlaceholderOrDoubleEncoding( $output, 'automatic placement' );
	}

	/**
	 * Older shortcode attributes could reach the renderer after the token had
	 * already been URL-escaped. Treat that stored representation as the same
	 * current-post request rather than sharing the token itself.
	 */
	public function testPercentEscapedHistoricalPlaceholderUsesTheCurrentPermalink(): void {
		$postId = self::factory()->post->create( array( 'post_status' => 'publish' ) );
		$this->go_to( get_permalink( $postId ) );
		$GLOBALS['post'] = get_post( $postId );

		foreach ( array( '%25%25permalink%25%25', '%2525%2525permalink%2525%2525' ) as $url ) {
			$output = zm_sh_shortcode_cb(
				array(
					'icons' => 'facebook',
					'url' => $url,
				)
			);

			$this->assertStringContainsString( rawurlencode( get_permalink( $postId ) ), $output );
			$this->assertNoPlaceholderOrDoubleEncoding( $output, 'escaped historical shortcode URL' );
		}
	}

	private function frontendAdapterOutputs( $postId ) {
		$attributes = array(
			'title' => 'Stored title',
			'iconset' => 'default',
			'iconset_type' => 'square',
			'icons' => array( 'facebook' ),
		);

		return array(
			'shortcode' => zm_sh_shortcode_cb( array( 'icons' => 'facebook' ) ),
			'Gutenberg block' => LegacyApi::plugin()->block()->render( $attributes, $postId ),
			'Elementor-compatible adapter input' => zm_sh_shortcode_cb(
				array(
					'icons' => 'facebook',
					'class' => 'in_elementor',
				)
			),
			'WPBakery stored shortcode' => do_shortcode(
				'[zm_sh_btn iconset="default" iconset_type="square" icons="facebook"]'
			),
			'direct PHP API' => zm_sh_btn(
				array(
					'iconset' => 'default',
					'iconset_type' => 'square',
					'icons' => array( 'facebook' ),
					'class' => 'in_php_function',
				)
			),
		);
	}

	private function assertNoPlaceholderOrDoubleEncoding( $output, $adapter ) {
		$this->assertStringNotContainsString( '%%permalink%%', $output, $adapter );
		$this->assertStringNotContainsString( '%25%25permalink%25%25', $output, $adapter );
		$this->assertStringNotContainsString( 'http%3A%2F%2F%25%25permalink%25%25', $output, $adapter );
	}
}
