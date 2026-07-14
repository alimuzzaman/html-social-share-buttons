<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', 'zm_sh_register_block' );

function zm_sh_register_block() {
	if ( ! function_exists( 'register_block_type' ) || ! function_exists( 'zm_sh_shortcode_cb' ) ) {
		return;
	}

	$asset = file_exists( __DIR__ . '/build/social-share.asset.php' ) ? require __DIR__ . '/build/social-share.asset.php' : array();
	wp_register_script(
		'zm-sh-social-share-block',
		plugins_url( 'build/social-share.js', __FILE__ ),
		isset( $asset['dependencies'] ) ? $asset['dependencies'] : array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components' ),
		isset( $asset['version'] ) ? $asset['version'] : '2.2.4',
		true
	);
	wp_localize_script(
		'zm-sh-social-share-block',
		'zmShBlock',
		array(
			'iconsets'          => zm_sh_get_builder_iconset_options(),
			'iconsetAssets'     => zm_sh_get_builder_iconset_assets(),
			'inheritedIconset'  => zm_sh_get_builder_iconset( 'inherit' ),
		)
	);

	register_block_type(
		'html-social-share/social-share',
		array(
			'editor_script'   => 'zm-sh-social-share-block',
				'attributes'      => array(
					'title'        => array( 'type' => 'string', 'default' => 'Share this page' ),
					'iconset'      => array( 'type' => 'string', 'default' => 'inherit' ),
					'iconset_type' => array( 'type' => 'string', 'default' => 'square' ),
				'icons'        => array( 'type' => 'array', 'default' => array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' ) ),
			),
			'render_callback' => 'zm_sh_render_block',
		)
	);
}

function zm_sh_get_builder_iconset_assets() {
	global $zm_sh;

	$assets = array();
	if ( ! is_object( $zm_sh ) || ! isset( $zm_sh->iconsets ) ) {
		return $assets;
	}

	foreach ( $zm_sh->iconsets->get_iconsets() as $iconset ) {
		$assets[ $iconset->id ] = array(
			'types' => array_values( (array) $iconset->types ),
			'icons' => array(),
		);

		foreach ( (array) $iconset->icons as $id => $icon ) {
			if ( empty( $icon['image'] ) ) {
				continue;
			}

			foreach ( $assets[ $iconset->id ]['types'] as $type ) {
				$assets[ $iconset->id ]['icons'][ $id ][ $type ] = trailingslashit( $iconset->url . $type ) . rawurlencode( $icon['image'] );
			}
		}
	}

	return $assets;
}

function zm_sh_render_block( $attributes ) {
	if ( isset( $attributes['icons'] ) && is_array( $attributes['icons'] ) && empty( $attributes['icons'] ) ) {
		return '';
	}

	return zm_sh_shortcode_cb(
		array(
			'title'        => isset( $attributes['title'] ) ? $attributes['title'] : '',
			'iconset'      => zm_sh_get_builder_iconset( isset( $attributes['iconset'] ) ? $attributes['iconset'] : 'inherit' ),
			'iconset_type' => isset( $attributes['iconset_type'] ) ? $attributes['iconset_type'] : 'square',
			'icons'        => isset( $attributes['icons'] ) && is_array( $attributes['icons'] ) ? implode( ',', array_map( 'sanitize_key', $attributes['icons'] ) ) : 'facebook,x,linkedin,pinterest,mail',
			'class'        => 'in_block',
		)
	);
}
