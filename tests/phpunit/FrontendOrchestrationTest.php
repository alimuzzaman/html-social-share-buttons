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
