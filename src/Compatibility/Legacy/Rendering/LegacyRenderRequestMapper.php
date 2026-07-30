<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;

final class LegacyRenderRequestMapper {
	public function map( array $options, $networks = null ) {
		$options = wp_parse_args(
			$options,
			array(
				'iconset' => 'default',
				'icons' => array(),
				'class' => 'left',
				'show_on' => 'show_left',
				'iconset_type' => '',
				'title' => '',
			)
		);
		$icons = is_array( $options['icons'] ) ? $options['icons'] : array();

		if ( isset( $icons['twitter'] ) ) {
			$icons['x'] = $icons['twitter'];
			unset( $icons['twitter'] );
		}

		$isList = ! empty( $icons ) && array_keys( $icons ) === range( 0, count( $icons ) - 1 );
		$networkIds = $isList ? array() : array_keys( $icons );
		$networkIds = array_values(
			array_filter(
				$networkIds,
				static function ( $networkId ) {
					$networkId = (string) $networkId;

					return '' !== $networkId && sanitize_key( $networkId ) === $networkId;
				}
			)
		);
		$showOn = in_array(
			$options['show_on'],
			array( 'show_left', 'show_right', 'show_before_post', 'show_after_post' ),
			true
		) ? $options['show_on'] : 'show_left';
		$type = $options['iconset_type']
			? $options['iconset_type']
			: ( isset( $options[ $showOn ] ) ? $options[ $showOn ] : '' );

		return new RenderRequest(
			sanitize_key( is_scalar( $options['iconset'] ) ? $options['iconset'] : 'default' ),
			sanitize_key( is_scalar( $type ) ? $type : '' ),
			$this->placement( $options['class'], $showOn ),
			is_scalar( $options['title'] ) ? (string) $options['title'] : '',
			$networkIds,
			array(),
			isset( $options['url'] ) && is_scalar( $options['url'] ) ? (string) $options['url'] : '',
			! empty( $options['nofollow'] )
		);
	}

	private function placement( $className, $showOn ) {
		$className = sanitize_html_class( is_scalar( $className ) ? $className : '' );

		if ( 'show_before_post' === $showOn ) {
			return RenderPlacement::BEFORE_CONTENT;
		}
		if ( 'show_after_post' === $showOn ) {
			return RenderPlacement::AFTER_CONTENT;
		}

		$placements = array(
			'left' => RenderPlacement::FLOATING_LEFT,
			'right' => RenderPlacement::FLOATING_RIGHT,
			'in_shortcode' => RenderPlacement::SHORTCODE,
			'in_widget' => RenderPlacement::WIDGET,
			'in_block' => RenderPlacement::BLOCK,
			'in_elementor' => RenderPlacement::ELEMENTOR,
			'in_php_function' => RenderPlacement::PHP_API,
		);

		return isset( $placements[ $className ] )
			? $placements[ $className ]
			: RenderPlacement::FLOATING_LEFT;
	}
}
