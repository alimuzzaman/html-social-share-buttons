<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderResult;

/**
 * Canonical frontend markup renderer.
 *
 * The deliberately old-looking class names, quote style and newlines are
 * public HTML compatibility, rather than a dependency on old PHP classes.
 */
final class HtmlRenderer {
	public function render(
		RenderRequest $request,
		RenderResult $result,
		$wrapperClass = '',
		$iconSetClass = '',
		$shapeClass = ''
	) {
		$output = '';
		if ( $request->showHeading() || $this->showsHeading( $request->placement() ) ) {
			$output = '<h3>' . esc_html( $request->heading() ) . '</h3>';
		}

		$output .= "<div class='zmshbt " .
			esc_attr(
				'' !== $wrapperClass
					? $wrapperClass
					: $this->wrapperClass( $request->placement() )
			) . ' ' .
			esc_attr( '' !== $iconSetClass ? $iconSetClass : $request->iconSetId() ) . ' ' .
			esc_attr( '' !== $shapeClass ? $shapeClass : $result->shape() ) . "'>";

		foreach ( $result->buttons() as $button ) {
			$output .= "<a class='" .
				esc_attr( $this->cssClass( $button->network() ) ) .
				"' target='_blank' href='" .
				esc_url( $button->url() ) .
				"' rel='" .
				esc_attr( implode( ' ', $result->relTokens() ) ) .
				"'></a>\n";
		}

		foreach ( $result->profileLinks() as $profileLink ) {
			$network = $profileLink->network();
			$networkId = $network->id();
			$label = 'mail' === $networkId
				? __( 'Contact us by email', 'html-social-share-buttons' )
				: sprintf(
					/* translators: %s is the social network name. */
					__( 'Visit our %s profile', 'html-social-share-buttons' ),
					$network->label()
				);
			$output .= "<a class='" .
				esc_attr( $this->cssClass( $network ) . ' zmshbt-profile-link' ) .
				"' data-zmshbt-kind='profile'";
			if ( 'mail' !== $networkId ) {
				$output .= " target='_blank' rel='" .
					esc_attr( implode( ' ', $result->relTokens() ) ) . "'";
			}
			$output .= " href='" . esc_url( $profileLink->url() ) . "' aria-label='" .
				esc_attr( $label ) . "'></a>\n";
		}

		return $output . '</div>';
	}

	public function cssClass( Network $network ) {
		/* X retained the historical twitter CSS class in shipped markup. */
		return 'x' === $network->id() ? 'twitter' : $network->cssClass();
	}

	private function showsHeading( $placement ) {
		return in_array(
			$placement,
			array(
				RenderPlacement::BEFORE_CONTENT,
				RenderPlacement::AFTER_CONTENT,
				RenderPlacement::SHORTCODE,
				RenderPlacement::WPBAKERY,
			),
			true
		);
	}

	private function wrapperClass( $placement ) {
		$classes = array(
			RenderPlacement::FLOATING_LEFT  => 'left',
			RenderPlacement::FLOATING_RIGHT => 'right',
			RenderPlacement::BEFORE_CONTENT => 'in_widget',
			RenderPlacement::AFTER_CONTENT  => 'in_widget',
			RenderPlacement::SHORTCODE      => 'in_shortcode',
			RenderPlacement::WIDGET         => 'in_widget',
			RenderPlacement::BLOCK          => 'in_block',
			RenderPlacement::ELEMENTOR      => 'in_elementor',
			RenderPlacement::WPBAKERY       => 'in_shortcode',
			RenderPlacement::PHP_API        => 'in_php_function',
		);

		return $classes[ $placement ];
	}
}
