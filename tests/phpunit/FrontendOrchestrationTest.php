<?php

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\ContentPlacementComposer;
use Alimuzzaman\HtmlSocialShareButtons\Application\Frontend\FloatingPlacementPlanner;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\SettingsDefaults;

final class FrontendOrchestrationTest extends WP_UnitTestCase {
	public function testExcludedContentPolicyNormalizesAndMatchesAllSupportedIdentifiers(): void {
		$policy = new ExcludedContentPolicy();

		$this->assertSame(
			array( '42', 'sample-page', 'Exact title' ),
			$policy->identifiers( ' 42, sample-page, Exact title, ' )
		);
		$this->assertTrue( $policy->matches( 42, 'other', 'Other', '42' ) );
		$this->assertTrue( $policy->matches( 10, 'sample-page', 'Other', 'SAMPLE-PAGE' ) );
		$this->assertTrue( $policy->matches( 10, 'other', 'Exact title', 'exact TITLE' ) );
		$this->assertFalse( $policy->matches( 10, 'other', 'Other', 'missing' ) );
	}

	public function testContentPlacementComposerKeepsBeforeAndAfterOrder(): void {
		$composer = new ContentPlacementComposer();
		$settings = $this->settings(
			array(
				Placement::BEFORE_CONTENT => true,
				Placement::AFTER_CONTENT  => true,
			)
		);

		$result = $composer->compose(
			'content',
			$settings,
			function ( $placement ) {
				return '[' . $placement . ']';
			},
			true
		);

		$this->assertSame( '[before_content]content[after_content]', $result );
		$this->assertSame(
			'content',
			$composer->compose( 'content', $settings, function () {
				return 'unexpected';
			}, false )
		);
	}

	public function testFloatingPlacementPlannerReturnsEnabledSidesInRenderOrder(): void {
		$planner = new FloatingPlacementPlanner();

		$this->assertSame(
			array( Placement::LEFT, Placement::RIGHT ),
			$planner->enabled(
				$this->settings(
					array(
						Placement::LEFT  => true,
						Placement::RIGHT => true,
					)
				)
			)
		);
	}

	public function testFilterContentSkipsNonMainLoopFeedsAndEmbeds(): void {
		$postId = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Frontend loop guard post',
			)
		);
		$previousOptions = get_option( 'zm_shbt_fld', null );
		$previousQuery   = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
		$previousMain    = isset( $GLOBALS['wp_the_query'] ) ? $GLOBALS['wp_the_query'] : null;
		$previousPost    = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		try {
			update_option(
				'zm_shbt_fld',
				array(
					'iconset' => 'default',
					'icons'   => array( 'facebook' => 'on' ),
					'show_in' => array( 'show_after_post' => '1' ),
					'show_for_current_user'    => '1',
					'show_for_logged_in_user'  => '1',
					'show_for_logged_out_user' => '1',
				)
			);
			$this->go_to( get_permalink( $postId ) );
			$GLOBALS['post'] = get_post( $postId );
			$frontend        = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->frontend();

			$GLOBALS['wp_query']->in_the_loop = true;
			$insideLoop = $frontend->filterContent( '<p>Body</p>' );
			$this->assertStringContainsString( 'zmshbt', $insideLoop );

			$GLOBALS['wp_query']->in_the_loop = false;
			$this->assertSame( '<p>Body</p>', $frontend->filterContent( '<p>Body</p>' ) );

			$GLOBALS['wp_query']->in_the_loop = true;
			$GLOBALS['wp_the_query']          = new WP_Query();
			$this->assertSame( '<p>Body</p>', $frontend->filterContent( '<p>Body</p>' ) );

			$GLOBALS['wp_the_query']       = $GLOBALS['wp_query'];
			$GLOBALS['wp_query']->is_feed  = true;
			$this->assertSame( '<p>Body</p>', $frontend->filterContent( '<p>Body</p>' ) );
		} finally {
			if ( null === $previousOptions ) {
				delete_option( 'zm_shbt_fld' );
			} else {
				update_option( 'zm_shbt_fld', $previousOptions );
			}
			$GLOBALS['wp_query']     = $previousQuery;
			$GLOBALS['wp_the_query'] = $previousMain;
			$GLOBALS['post']         = $previousPost;
		}
	}

	public function testFilterContentSupportsCorePostContentBlockOutsideLoopAndSkipsFeedsForLegacyOptions(): void {
		$postId = self::factory()->post->create(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Block template post',
				'post_content' => '<!-- wp:paragraph --><p>Block template body</p><!-- /wp:paragraph -->',
			)
		);
		$otherPostId = self::factory()->post->create(
			array(
				'post_status'  => 'publish',
				'post_title'   => 'Secondary block post',
				'post_content' => '<!-- wp:paragraph --><p>Secondary block body</p><!-- /wp:paragraph -->',
			)
		);
		$previousOptions = get_option( 'zm_shbt_fld', null );
		$previousQuery   = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
		$previousMain    = isset( $GLOBALS['wp_the_query'] ) ? $GLOBALS['wp_the_query'] : null;
		$previousPost    = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;
		$frontend        = \Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->frontend();
		$wasHooked       = has_filter( 'the_content', array( $frontend, 'filterContent' ) );
		if ( false === $wasHooked ) {
			add_filter( 'the_content', array( $frontend, 'filterContent' ) );
		}

		try {
			$options = array(
				'iconset'                  => 'default',
				'icons'                    => array( 'facebook' => 'on' ),
				'show_in'                  => array(
					'show_before_post' => '1',
					'show_after_post'  => '1',
				),
				'show_for_current_user'    => '1',
				'show_for_logged_in_user'  => '1',
				'show_for_logged_out_user' => '1',
			);
			update_option( 'zm_shbt_fld', $options );

			$this->go_to( get_permalink( $postId ) );
			$GLOBALS['post']                  = get_post( $postId );
			$GLOBALS['wp_query']->in_the_loop = false;

			$mainBlock = new WP_Block(
				array( 'blockName' => 'core/post-content', 'attrs' => array() ),
				array( 'postId' => $postId, 'postType' => 'post' )
			);
			$renderedMain = $mainBlock->render();
			$this->assertStringContainsString( 'zmshbt', $renderedMain );
			$this->assertStringContainsString( 'Block template body', $renderedMain );

			$GLOBALS['post'] = get_post( $otherPostId );
			$otherBlock = new WP_Block(
				array( 'blockName' => 'core/post-content', 'attrs' => array() ),
				array( 'postId' => $otherPostId, 'postType' => 'post' )
			);
			$renderedOther = $otherBlock->render();
			$this->assertStringNotContainsString( 'zmshbt', $renderedOther );
			$this->assertStringContainsString( 'Secondary block body', $renderedOther );

			$GLOBALS['post']               = get_post( $postId );
			$GLOBALS['wp_query']->is_feed  = true;
			$this->assertSame( '<p>Feed</p>', $frontend->filterContentWithOptions( '<p>Feed</p>', $options ) );

			$GLOBALS['wp_query']->is_feed  = false;
			$GLOBALS['wp_query']->is_embed = true;
			$this->assertSame( '<p>Embed</p>', $frontend->filterContentWithOptions( '<p>Embed</p>', $options ) );
		} finally {
			if ( false === $wasHooked ) {
				remove_filter( 'the_content', array( $frontend, 'filterContent' ) );
			}
			if ( null === $previousOptions ) {
				delete_option( 'zm_shbt_fld' );
			} else {
				update_option( 'zm_shbt_fld', $previousOptions );
			}
			$GLOBALS['wp_query']     = $previousQuery;
			$GLOBALS['wp_the_query'] = $previousMain;
			$GLOBALS['post']         = $previousPost;
			$frontend->detectExclusion();
		}
	}

	public function testFooterFlushesExplicitAssetsOnExcludedPageWhileSuppressingFloatingBars(): void {
		$postId = self::factory()->post->create(
			array(
				'post_status' => 'publish',
				'post_title'  => 'Excluded explicit block post',
			)
		);
		update_post_meta( $postId, '_zm_sh_disable_share', 'on' );

		$root            = dirname( __DIR__, 2 );
		$networks        = ( new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider() )->createRegistry();
		$iconSets        = ( new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\ManifestIconSetProvider( $root . '/resources/iconsets' ) )->createRegistry( $networks );
		$renderer        = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering\RenderFacade(
			$networks,
			$iconSets,
			new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Asset\IconSetAssetResolver(
				$root . '/assets/iconsets',
				plugins_url( 'assets/iconsets', $root . '/html-social-share.php' )
			),
			new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Extension\ExtensionHooks()
		);
		$settings = $this->settings(
			array(
				Placement::LEFT  => true,
				Placement::RIGHT => true,
			)
		);
		$repo = new class( $settings ) implements \Alimuzzaman\HtmlSocialShareButtons\Application\Settings\SettingsRepository {
			private $settings;

			public function __construct( Settings $settings ) {
				$this->settings = $settings;
			}

			public function load() {
				return $this->settings;
			}

			public function save( Settings $settings ) {
				$this->settings = $settings;
			}
		};

		$previousQuery = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query'] : null;
		$previousMain  = isset( $GLOBALS['wp_the_query'] ) ? $GLOBALS['wp_the_query'] : null;
		$previousPost  = isset( $GLOBALS['post'] ) ? $GLOBALS['post'] : null;

		try {
			$this->go_to( get_permalink( $postId ) );
			$GLOBALS['post'] = get_post( $postId );

			$emptyAssets = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\AssetCollector(
				'https://example.test/fallback.css',
				'3.5.1',
				'hssb-button-appearance',
				'https://example.test/browser-url.js',
				'hssb-browser-url',
				array(
					\Alimuzzaman\HtmlSocialShareButtons\Domain\Frontend\FrontendFeatureRegistry::COPY_LINK => array( 'hssb-copy-link', 'https://example.test/copy-link.js' ),
				)
			);
			$emptyController = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController(
				$repo,
				$renderer,
				new ContentPlacementComposer(),
				new FloatingPlacementPlanner(),
				new ExcludedContentPolicy(),
				new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader( $root . '/html-social-share.php', 'html-social-share-buttons' ),
				$emptyAssets,
				'_zm_sh_disable_share'
			);
			$emptyController->detectExclusion();
			$this->assertTrue( $emptyController->isExcluded() );

			wp_dequeue_style( 'social-share-default' );
			wp_deregister_style( 'social-share-default' );
			wp_dequeue_script( 'hssb-copy-link' );
			wp_deregister_script( 'hssb-copy-link' );

			ob_start();
			$emptyController->footer();
			$emptyFooter = (string) ob_get_clean();

			$this->assertSame( '', $emptyFooter );
			$this->assertNotContains( 'social-share-default', wp_styles()->queue );
			$this->assertNotContains( 'hssb-copy-link', wp_scripts()->queue );

			$explicitAssets     = $emptyAssets->fresh();
			$explicitController = new \Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend\FrontendController(
				$repo,
				$renderer,
				new ContentPlacementComposer(),
				new FloatingPlacementPlanner(),
				new ExcludedContentPolicy(),
				new \Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Translation\TranslationLoader( $root . '/html-social-share.php', 'html-social-share-buttons' ),
				$explicitAssets,
				'_zm_sh_disable_share'
			);
			$explicitController->detectExclusion();
			$this->assertTrue( $explicitController->isExcluded() );

			$outcome = $renderer->render(
				array(
					'iconset' => 'default',
					'icons'   => array( 'facebook', 'copy' ),
					'url'     => get_permalink( $postId ),
				),
				$postId
			);
			$explicitAssets->collect( $outcome );

			ob_start();
			$explicitController->footer();
			$explicitFooter = (string) ob_get_clean();

			$this->assertStringNotContainsString( 'zmshbt left', $explicitFooter );
			$this->assertStringNotContainsString( 'zmshbt right', $explicitFooter );
			$this->assertStringContainsString( '.zmshbt.default.square .facebook', $explicitFooter );
			$this->assertContains( 'social-share-default', wp_styles()->queue );
			$this->assertContains( 'hssb-copy-link', wp_scripts()->queue );
		} finally {
			wp_dequeue_style( 'social-share-default' );
			wp_deregister_style( 'social-share-default' );
			wp_dequeue_script( 'hssb-copy-link' );
			wp_deregister_script( 'hssb-copy-link' );
			$this->go_to( home_url( '/' ) );
			\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Api\LegacyApi::plugin()->frontend()->detectExclusion();
			$GLOBALS['wp_query']     = $previousQuery;
			$GLOBALS['wp_the_query'] = $previousMain;
			$GLOBALS['post']         = $previousPost;
		}
	}

	private function settings( array $placementOverrides ) {
		$defaults = SettingsDefaults::create();
		$placements = array(
			Placement::LEFT           => false,
			Placement::RIGHT          => false,
			Placement::BEFORE_CONTENT => false,
			Placement::AFTER_CONTENT  => false,
		);

		return new Settings(
			$defaults->title(),
			$defaults->iconSetId(),
			$defaults->defaultIconShape(),
			array_merge( $placements, $placementOverrides ),
			$defaults->placementShapes(),
			$defaults->networkStates(),
			$defaults->shareTemplates(),
			$defaults->excludedContent(),
			$defaults->analyticsEnabled(),
			$defaults->autoHideEnabled(),
			$defaults->preserveUrlPort(),
			$defaults->noFollow()
		);
	}
}
