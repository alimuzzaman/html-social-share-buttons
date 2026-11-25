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
