<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Application\Frontend;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;

final class ContentPlacementComposer {
	public function compose(
		$content,
		Settings $settings,
		callable $renderPlacement,
		$isSingular
	) {
		$content = (string) $content;
		if ( ! $isSingular ) {
			return $content;
		}

		$placements = $settings->placements();
		if ( ! empty( $placements[ Placement::BEFORE_CONTENT ] ) ) {
			$content = (string) $renderPlacement( Placement::BEFORE_CONTENT ) . $content;
		}
		if ( ! empty( $placements[ Placement::AFTER_CONTENT ] ) ) {
			$content .= (string) $renderPlacement( Placement::AFTER_CONTENT );
		}

		return $content;
	}
}
