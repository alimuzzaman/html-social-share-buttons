<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Network\LegacyNetworkMapper;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderResult;

final class LegacyHtmlRenderer {
	private $networks;

	public function __construct( LegacyNetworkMapper $networks ) {
		$this->networks = $networks;
	}

	public function render(
		RenderRequest $request,
		RenderResult $result,
		array $iconClasses = array(),
		$wrapperClass = '',
		$iconSetClass = '',
		$shapeClass = ''
	) {
		$output = '';
		if ( $this->showsHeading( $request->placement() ) ) {
			$output = '<h3>' . esc_html( $request->heading() ) . '</h3>';
		}

		$output .= "<div class='zmshbt " .
			esc_attr(
				'' !== $wrapperClass
					? $wrapperClass
					: $this->className( $request->placement() )
			) . ' ' .
			esc_attr( '' !== $iconSetClass ? $iconSetClass : $request->iconSetId() ) . ' ' .
			esc_attr( '' !== $shapeClass ? $shapeClass : $result->shape() ) . "'>";

		foreach ( $result->buttons() as $button ) {
			$networkId = $button->network()->id();
			$output .= "<a class='" .
				esc_attr(
					isset( $iconClasses[ $networkId ] )
						? $iconClasses[ $networkId ]
						: $this->networks->cssClass( $button->network() )
				) .
				"' target='_blank' href='" .
				esc_url( $button->url() ) .
				"' rel='" .
				esc_attr( implode( ' ', $result->relTokens() ) ) .
				"'></a>\n";
		}

		foreach ( $result->profileLinks() as $profileLink ) {
			$networkId = $profileLink->network()->id();
			$className = isset( $iconClasses[ $networkId ] )
				? $iconClasses[ $networkId ]
				: $this->networks->cssClass( $profileLink->network() );
			$label = 'mail' === $networkId
				? __( 'Contact us by email', 'html-social-share-buttons' )
				: sprintf(
					__( 'Visit our %s profile', 'html-social-share-buttons' ),
					$profileLink->network()->label()
				);
			$output .= "<a class='" .
				esc_attr( $className . ' zmshbt-profile-link' ) .
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

	private function className( $placement ) {
		$classes = array(
			RenderPlacement::FLOATING_LEFT => 'left',
			RenderPlacement::FLOATING_RIGHT => 'right',
			RenderPlacement::BEFORE_CONTENT => 'in_widget',
			RenderPlacement::AFTER_CONTENT => 'in_widget',
			RenderPlacement::SHORTCODE => 'in_shortcode',
			RenderPlacement::WIDGET => 'in_widget',
			RenderPlacement::BLOCK => 'in_block',
			RenderPlacement::ELEMENTOR => 'in_elementor',
			RenderPlacement::WPBAKERY => 'in_shortcode',
			RenderPlacement::PHP_API => 'in_php_function',
		);

		return $classes[ $placement ];
	}
}
