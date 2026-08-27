<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Presentation\Rendering;

use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderPlacement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Rendering\RenderRequest;
use InvalidArgumentException;

/**
 * Maps the long-standing public option array to the small canonical render
 * request. Keeping this at the presentation boundary lets public adapters
 * preserve their stored shapes without making the renderer legacy-owned.
 */
final class RenderRequestMapper {
	public function map( array $options ) {
		$options = array_merge(
			array(
				'iconset'           => 'default',
				'icons'             => array(),
				'class'             => 'left',
				'show_on'           => 'show_left',
				'iconset_type'      => '',
				'button_appearance' => 'legacy',
				'auto_hide_enabled' => false,
				'title'             => '',
			),
			$options
		);

		$showOn = $this->showOn( $options['show_on'] );
		$shape = $this->shape( $options, $showOn );

		return new RenderRequest(
			$this->key( $options['iconset'], 'default' ),
			$shape,
			$this->placement( $options['class'], $showOn ),
			is_scalar( $options['title'] ) ? (string) $options['title'] : '',
			$this->networkIds( $options['icons'] ),
			$this->templates( $options ),
			isset( $options['url'] ) && is_scalar( $options['url'] ) ? (string) $options['url'] : '',
			! empty( $options['nofollow'] ),
			$this->profileLinks( $options ),
			! empty( $options['profiles_only'] ) && ! empty( $options['title'] ),
			is_string( $options['button_appearance'] ) ? $options['button_appearance'] : 'legacy',
			! empty( $options['auto_hide_enabled'] )
		);
	}

	private function networkIds( $icons ) {
		if ( ! is_array( $icons ) ) {
			return array();
		}
		if ( $this->isList( $icons ) ) {
			/* Existing serialized blocks store a list. Its values are selection IDs. */
			return $this->normalizeNetworkIds( $icons );
		}

		return $this->normalizeNetworkIds( array_keys( $icons ) );
	}

	private function normalizeNetworkIds( array $networkIds ) {
		$normalized = array();
		foreach ( $networkIds as $networkId ) {
			$networkId = $this->networkId( $networkId );
			if ( '' !== $networkId ) {
				$normalized[ $networkId ] = $networkId;
			}
		}

		return array_values( $normalized );
	}

	private function templates( array $options ) {
		$templates = isset( $options['share_templates'] ) && is_array( $options['share_templates'] )
			? $options['share_templates']
			: array();
		$normalized = array();
		foreach ( $templates as $networkId => $template ) {
			$networkId = $this->networkId( $networkId );
			if ( '' !== $networkId && is_string( $template ) && '' !== trim( $template ) ) {
				$normalized[ $networkId ] = $template;
			}
		}

		return $normalized;
	}

	private function profileLinks( array $options ) {
		if ( 'none' === $this->profileLinksMode( $options ) ) {
			return array();
		}

		$submitted = isset( $options['profile_links'] ) && is_array( $options['profile_links'] )
			? $options['profile_links']
			: array();
		$profileLinks = array();
		foreach ( $submitted as $networkId => $url ) {
			$networkId = $this->networkId( $networkId );
			if ( '' === $networkId || ! is_string( $url ) ) {
				continue;
			}

			try {
				/* RenderRequest owns the canonical URL validation rules. */
				new RenderRequest(
					'default',
					'square',
					RenderPlacement::SHORTCODE,
					'',
					array(),
					array(),
					'',
					false,
					array( $networkId => $url )
				);
				$profileLinks[ $networkId ] = $url;
			} catch ( InvalidArgumentException $error ) {
				/* Invalid saved profile data must not make frontend rendering fail. */
			}
		}

		return $profileLinks;
	}

	/**
	 * `profile_links_mode` is additive at every integration edge. Missing or
	 * malformed modes deliberately retain historical inheritance behavior.
	 */
	private function profileLinksMode( array $options ) {
		$mode = isset( $options['profile_links_mode'] ) && is_scalar( $options['profile_links_mode'] )
			? strtolower( trim( (string) $options['profile_links_mode'] ) )
			: 'inherit';

		return in_array( $mode, array( 'inherit', 'none', 'custom' ), true ) ? $mode : 'inherit';
	}

	private function showOn( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		return in_array(
			$value,
			array( 'show_left', 'show_right', 'show_before_post', 'show_after_post' ),
			true
		) ? $value : 'show_left';
	}

	private function shape( array $options, $showOn ) {
		$value = ! empty( $options['iconset_type'] )
			? $options['iconset_type']
			: ( isset( $options[ $showOn ] ) ? $options[ $showOn ] : '' );

		return $this->key( $value, 'square' );
	}

	private function placement( $className, $showOn ) {
		if ( 'show_before_post' === $showOn ) {
			return RenderPlacement::BEFORE_CONTENT;
		}
		if ( 'show_after_post' === $showOn ) {
			return RenderPlacement::AFTER_CONTENT;
		}

		$placements = array(
			'left'            => RenderPlacement::FLOATING_LEFT,
			'right'           => RenderPlacement::FLOATING_RIGHT,
			'in_shortcode'    => RenderPlacement::SHORTCODE,
			'in_widget'       => RenderPlacement::WIDGET,
			'in_block'        => RenderPlacement::BLOCK,
			'in_elementor'    => RenderPlacement::ELEMENTOR,
			'in_php_function' => RenderPlacement::PHP_API,
		);
		$className = $this->htmlClass( $className );

		return isset( $placements[ $className ] )
			? $placements[ $className ]
			: RenderPlacement::FLOATING_LEFT;
	}

	private function networkId( $networkId ) {
		$networkId = $this->key( $networkId, '' );
		return 'twitter' === $networkId ? 'x' : $networkId;
	}

	private function key( $value, $fallback ) {
		$value = is_scalar( $value ) ? strtolower( (string) $value ) : '';
		if ( function_exists( 'sanitize_key' ) ) {
			$value = sanitize_key( $value );
		} elseif ( ! preg_match( '/^[a-z][a-z0-9-]*$/', $value ) ) {
			$value = '';
		}

		return '' !== $value ? $value : $fallback;
	}

	private function htmlClass( $value ) {
		$value = is_scalar( $value ) ? (string) $value : '';
		if ( function_exists( 'sanitize_html_class' ) ) {
			return sanitize_html_class( $value );
		}

		return preg_replace( '/[^A-Za-z0-9_-]/', '', $value );
	}

	private function isList( array $values ) {
		return ! empty( $values ) && array_keys( $values ) === range( 0, count( $values ) - 1 );
	}
}
