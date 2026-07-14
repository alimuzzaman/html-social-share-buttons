<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}



	add_shortcode("zm_sh_btn", 'zm_sh_shortcode_cb');

	function zm_sh_shortcode_cb($atts){
		global $zm_sh;
		if ( ! is_object( $zm_sh ) || ! method_exists( $zm_sh, 'zm_sh_btn' ) ) {
			return '';
		}
		if(isset($zm_sh->excluded) and $zm_sh->excluded == true) return;
		$atts = shortcode_atts(array(
									'title'			=> '',
									'iconset'		=> "default",
									'url'			=> "%%permalink%%",
									'icons'			=> array(
															"facebook"		=> "on",
															"x"				=> "on",
															"linkedin"		=> "on",
															"pinterest"		=> "on",
															"mail"			=> "on",
															),
									'iconset_type'	=> "square",
									'class'			=> "in_shortcode",
								),
								$atts,
								'zm_sh_btn'
							);

		// Sanitize all user inputs to prevent XSS
		$atts['title'] = sanitize_text_field($atts['title']);
		$atts['iconset'] = sanitize_key($atts['iconset']);
		$atts['url'] = esc_url_raw($atts['url']);
		$atts['iconset_type'] = sanitize_key($atts['iconset_type']);
		$atts['class'] = sanitize_html_class($atts['class']);

		if ( is_array( $atts['icons'] ) ) {
			$is_list = ! empty( $atts['icons'] ) && array_keys( $atts['icons'] ) === range( 0, count( $atts['icons'] ) - 1 );
			$icons   = $is_list ? $atts['icons'] : array_keys( $atts['icons'] );
		} else {
			$icons = explode( ',', (string) $atts['icons'] );
		}
		// Sanitize each icon name
		$icons = array_map('sanitize_key', $icons);

		// Runtime migration: Convert 'twitter' to 'x' for backward compatibility
		$icons = array_map(function($icon) {
			return $icon === 'twitter' ? 'x' : $icon;
		}, $icons);

		$atts['icons'] = array_fill_keys( array_filter( $icons ), 'on' );
		return $zm_sh->zm_sh_btn($atts);
	}

	function zm_sh_get_builder_iconset( $iconset = 'inherit' ) {
		global $zm_sh;

		$iconset = sanitize_key( $iconset );
		if ( 'inherit' !== $iconset ) {
			return $iconset;
		}

		if ( is_object( $zm_sh ) && ! empty( $zm_sh->options['iconset'] ) ) {
			return sanitize_key( $zm_sh->options['iconset'] );
		}

		return 'default';
	}

	function zm_sh_get_builder_iconset_options() {
		global $zm_sh;

		$options = array(
			'inherit' => __( 'Inherit from plugin settings', 'html-social-share-buttons' ),
		);

		if ( is_object( $zm_sh ) && isset( $zm_sh->iconsets ) ) {
			foreach ( $zm_sh->iconsets->get_iconsets() as $iconset ) {
				$options[ $iconset->id ] = $iconset->name;
			}
		}

		return $options;
	}
