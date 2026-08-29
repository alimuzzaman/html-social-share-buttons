<?php

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi;
use Alimuzzaman\HtmlSocialShareButtons\Application\Rendering\ResolveShareUrl;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ShareContext;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;

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

	public function testBlueskyRenderedTextKeepsASeparatorAfterWordPressUrlEscaping(): void {
		$permalink = 'https://example.test/frontend-contract/?preview=true';
		$output = LegacyApi::plugin()->renderer()->render(
			array(
				'iconset' => 'default',
				'iconset_type' => 'square',
				'icons' => array( 'bluesky' ),
				'url' => $permalink,
			),
			0,
			new ShareContext( $permalink, 'Frontend Contract Title' )
		)->html();

		$this->assertSame(
			1,
			preg_match( "/class='bluesky'[^>]+href='([^']+)'/", $output, $matches ),
			'Expected one rendered Bluesky share link.'
		);
		$href = html_entity_decode( $matches[1], ENT_QUOTES, 'UTF-8' );
		$parts = wp_parse_url( $href );
		$query = array();
		parse_str( isset( $parts['query'] ) ? $parts['query'] : '', $query );

		$this->assertSame(
			'Frontend Contract Title ' . $permalink,
			isset( $query['text'] ) ? $query['text'] : null
		);
	}

	public function testEveryBuiltInNetworkRendersItsDecodedShareParameters(): void {
		$permalink = 'https://example.test/share-contract/?source=all';
		$title = 'Share contract title';
		$image = 'https://example.test/share-contract.jpg';
		$renderer = LegacyApi::plugin()->renderer();
		$output = $renderer->render(
			array(
				'iconset'      => 'default',
				'iconset_type' => 'square',
				'icons'        => ( new BuiltInNetworkProvider() )->createRegistry()->ids(),
				'url'          => $permalink,
			),
			0,
			new ShareContext( $permalink, $title, 'Share contract description', $image )
		)->html();

		$this->assertSame(
			7,
			preg_match_all( "/<a class='([^']+)'[^>]+href='([^']+)'/", $output, $matches ),
			'Every built-in network should produce one share anchor.'
		);
		$decoded = array();
		foreach ( $matches[1] as $index => $class ) {
			$href = html_entity_decode( $matches[2][ $index ], ENT_QUOTES, 'UTF-8' );
			$parts = wp_parse_url( $href );
			$query = array();
			parse_str( isset( $parts['query'] ) ? $parts['query'] : '', $query );
			$decoded[ $class ] = $query;
		}

		$this->assertSame( $permalink, $decoded['facebook']['u'] );
		$this->assertSame( $permalink, $decoded['twitter']['url'] );
		$this->assertSame( $title, $decoded['twitter']['text'] );
		$this->assertSame( $permalink, $decoded['linkedin']['url'] );
		$this->assertSame( $permalink, $decoded['pinterest']['url'] );
		$this->assertSame( $image, $decoded['pinterest']['media'] );
		$this->assertSame( $title, $decoded['pinterest']['description'] );
		$this->assertSame( $permalink, $decoded['telegram']['url'] );
		$this->assertSame( $title, $decoded['telegram']['text'] );
		$this->assertSame( $title . ' ' . $permalink, $decoded['bluesky']['text'] );
		$this->assertSame( $title, $decoded['mail']['subject'] );
		$this->assertSame( $permalink, $decoded['mail']['body'] );
	}

	public function testBuiltInResolverTemplatesDecodeWithoutUnresolvedPlaceholders(): void {
		$context = new ShareContext(
			'https://example.test/share-contract/?source=resolver',
			'Resolver title',
			'Resolver description',
			'https://example.test/resolver.jpg'
		);
		$networks = ( new BuiltInNetworkProvider() )->createRegistry();
		$resolver = new ResolveShareUrl();

		foreach ( $networks->all() as $network ) {
			$url = $resolver->resolve( $network, $context );
			$this->assertStringNotContainsString( '%%', $url, $network->id() );
			$this->assertStringNotContainsString( '%25', $url, $network->id() );
			$this->assertSame( $network->placeholders(), $this->placeholdersIn( $network->defaultShareTemplate() ), $network->id() . ' declarations' );
		}
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

	private function placeholdersIn( $template ): array {
		preg_match_all( '/%%[a-z]+%%/', (string) $template, $matches );

		return array_values( array_unique( $matches[0] ) );
	}
}
