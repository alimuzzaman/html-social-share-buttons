<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Frontend;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Network\Network;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\ButtonAppearance;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderResult;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\ResolvedButton;

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

		$appearanceClass = ButtonAppearance::modifier( $request->buttonAppearance() );
		$autoHideClass = '';
		if (
			$request->autoHideEnabled() &&
			in_array( $request->placement(), array( RenderPlacement::FLOATING_LEFT, RenderPlacement::FLOATING_RIGHT ), true )
		) {
			$autoHideClass = 'hssb-rail--auto-hide';
		}

		$output .= "<div class='zmshbt " .
			esc_attr(
				'' !== $wrapperClass
					? $wrapperClass
					: $this->wrapperClass( $request->placement() )
			) . ' ' .
			esc_attr( '' !== $iconSetClass ? $iconSetClass : $request->iconSetId() ) . ' ' .
			esc_attr( '' !== $shapeClass ? $shapeClass : $result->shape() );
		if ( '' !== $appearanceClass ) {
			$output .= ' ' . esc_attr( $appearanceClass );
		}
		if ( '' !== $autoHideClass ) {
			$output .= ' ' . esc_attr( $autoHideClass );
		}
		if ( $request->browserUrlEnabled() && $this->hasBrowserDescriptors( $result ) ) {
			$output .= "' data-hssb-browser-url='1";
		}
		$output .= "'>";

		foreach ( $result->buttons() as $button ) {
			$label = sprintf(
				/* translators: %s is the social network name. */
				__( 'Share on %s', 'html-social-share-buttons' ),
				$button->network()->label()
			);
			$output .= "<a class='" .
				esc_attr( $this->cssClass( $button->network() ) ) .
			"' target='_blank' href='" .
				$this->buttonUrl( $button ) .
				$this->browserDescriptorAttributes( $button ) .
				"' rel='" .
				esc_attr( implode( ' ', $result->relTokens() ) ) .
				"' aria-label='" .
				esc_attr( $label ) .
				"'></a>\n";
		}

		if ( ! empty( $result->buttons() ) && ! empty( $result->profileLinks() ) ) {
			$output .= "<span class='zmshbt-profile-separator' aria-hidden='true'></span>\n";
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

	/**
	 * Keep the Bluesky separator through WordPress URL escaping.
	 *
	 * The public template intentionally remains `%0A` for backwards
	 * compatibility, but WordPress strips encoded line breaks in esc_url().
	 * An encoded space is safe in the query and preserves a readable boundary
	 * between the title and permalink in the rendered share intent.
	 */
	private function buttonUrl( ResolvedButton $button ) {
		return ShareUrlPresentation::escape( $button->network()->id(), $button->url() );
	}

	private function browserDescriptorAttributes( ResolvedButton $button ) {
		$descriptor = $button->browserUrlDescriptor();
		if ( empty( $descriptor['template'] ) || '%%permalink%%' !== ( isset( $descriptor['permalink_slot'] ) ? $descriptor['permalink_slot'] : '' ) ) {
			return '';
		}

		$json = function_exists( 'wp_json_encode' )
			? wp_json_encode( $descriptor )
			: json_encode( $descriptor );
		if ( ! is_string( $json ) || '' === $json ) {
			return '';
		}

		return "' data-hssb-browser-descriptor='" . esc_attr( $json ) .
			"' data-hssb-server-href='" . esc_attr( $this->buttonUrl( $button ) );
	}

	private function hasBrowserDescriptors( RenderResult $result ) {
		foreach ( $result->buttons() as $button ) {
			if ( ! empty( $button->browserUrlDescriptor() ) ) {
				return true;
			}
		}

		return false;
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
