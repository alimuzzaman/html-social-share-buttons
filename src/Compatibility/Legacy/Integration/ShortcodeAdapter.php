<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration;

final class ShortcodeAdapter {
	public function register() {
		add_shortcode( 'zm_sh_btn', 'zm_sh_shortcode_cb' );
	}

	public function render( $attributes ) {
		global $zm_sh;

		if ( ! is_object( $zm_sh ) || ! method_exists( $zm_sh, 'zm_sh_btn' ) ) {
			return '';
		}
		if ( isset( $zm_sh->excluded ) && true === $zm_sh->excluded ) {
			return null;
		}
		$attributes = is_array( $attributes ) ? $attributes : array();

		$attributes = shortcode_atts(
			array(
				'title' => '',
				'iconset' => 'default',
				'url' => '%%permalink%%',
				'icons' => array(
					'facebook' => 'on',
					'x' => 'on',
					'linkedin' => 'on',
					'pinterest' => 'on',
					'mail' => 'on',
				),
				'iconset_type' => 'square',
				'class' => 'in_shortcode',
			),
			$attributes,
			'zm_sh_btn'
		);

		$attributes['title'] = sanitize_text_field( $this->scalar( $attributes['title'], '' ) );
		$attributes['iconset'] = sanitize_key( $this->scalar( $attributes['iconset'], 'default' ) );
		$attributes['url'] = esc_url_raw( $this->scalar( $attributes['url'], '%%permalink%%' ) );
		$attributes['iconset_type'] = sanitize_key(
			$this->scalar( $attributes['iconset_type'], 'square' )
		);
		$attributes['class'] = sanitize_html_class(
			$this->scalar( $attributes['class'], 'in_shortcode' )
		);

		if ( is_array( $attributes['icons'] ) ) {
			$isList = ! empty( $attributes['icons'] ) &&
				array_keys( $attributes['icons'] ) === range( 0, count( $attributes['icons'] ) - 1 );
			$icons = $isList ? $attributes['icons'] : array_keys( $attributes['icons'] );
		} else {
			$icons = explode( ',', (string) $attributes['icons'] );
		}

		$icons = array_filter( $icons, 'is_scalar' );
		$icons = array_map( 'sanitize_key', $icons );
		$icons = array_map(
			static function ( $icon ) {
				return 'twitter' === $icon ? 'x' : $icon;
			},
			$icons
		);
		$attributes['icons'] = array_fill_keys( array_filter( $icons ), 'on' );

		return $zm_sh->zm_sh_btn( $attributes );
	}

	public function resolveBuilderIconSet( $iconSet = 'inherit' ) {
		global $zm_sh;

		$iconSet = sanitize_key( $this->scalar( $iconSet, 'inherit' ) );
		if ( 'inherit' !== $iconSet ) {
			return $iconSet;
		}
		if ( is_object( $zm_sh ) && ! empty( $zm_sh->options['iconset'] ) ) {
			return sanitize_key( $zm_sh->options['iconset'] );
		}

		return 'default';
	}

	public function builderIconSetOptions() {
		global $zm_sh;

		$options = array(
			'inherit' => __( 'Inherit from plugin settings', 'html-social-share-buttons' ),
		);
		if ( is_object( $zm_sh ) && isset( $zm_sh->iconsets ) ) {
			foreach ( $zm_sh->iconsets->get_iconsets() as $iconSet ) {
				$options[ $iconSet->id ] = $iconSet->name;
			}
		}

		return $options;
	}

	private function scalar( $value, $fallback ) {
		return is_scalar( $value ) ? (string) $value : (string) $fallback;
	}
}
