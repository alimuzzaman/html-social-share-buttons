<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Frontend;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

final class FloatingPlacementPlanner {
	public function enabled( Settings $settings ) {
		$states = $settings->placements();
		$enabled = array();

		foreach ( array( Placement::LEFT, Placement::RIGHT ) as $placement ) {
			if ( ! empty( $states[ $placement ] ) ) {
				$enabled[] = $placement;
			}
		}

		return $enabled;
	}
}
