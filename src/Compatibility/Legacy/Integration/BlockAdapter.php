<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration;

final class BlockAdapter {
	private $pluginRoot;

	public function __construct( $pluginRoot ) {
		$this->pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
	}

	public function register() {
		add_action( 'init', 'zm_sh_register_block' );
	}

	public function registerBlock() {
		if ( ! function_exists( 'register_block_type' ) || ! function_exists( 'zm_sh_shortcode_cb' ) ) {
			return;
		}

		$assetPath = $this->pluginRoot . '/build/social-share.asset.php';
		$asset = file_exists( $assetPath ) ? require $assetPath : array();
		$dependencies = array_values(
			array_unique(
				array_merge(
					array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components' ),
					isset( $asset['dependencies'] ) && is_array( $asset['dependencies'] )
						? $asset['dependencies']
						: array()
				)
			)
		);

		wp_register_script(
			'zm-sh-social-share-block',
			plugins_url( 'build/social-share.js', $this->pluginRoot . '/html-social-share.php' ),
			$dependencies,
			isset( $asset['version'] ) ? $asset['version'] : '2.2.4',
			true
		);
		wp_localize_script(
			'zm-sh-social-share-block',
			'zmShBlock',
			array(
				'iconsets' => zm_sh_get_builder_iconset_options(),
				'iconsetAssets' => $this->builderIconSetAssets(),
				'inheritedIconset' => zm_sh_get_builder_iconset( 'inherit' ),
			)
		);

		register_block_type(
			'html-social-share/social-share',
			array(
				'editor_script' => 'zm-sh-social-share-block',
				'attributes' => array(
					'title' => array( 'type' => 'string', 'default' => 'Share this page' ),
					'iconset' => array( 'type' => 'string', 'default' => 'inherit' ),
					'iconset_type' => array( 'type' => 'string', 'default' => 'square' ),
					'icons' => array(
						'type' => 'array',
						'default' => array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' ),
					),
				),
				'render_callback' => 'zm_sh_render_block',
			)
		);
	}

	public function builderIconSetAssets() {
		global $zm_sh;

		$assets = array();
		if ( ! is_object( $zm_sh ) || ! isset( $zm_sh->iconsets ) ) {
			return $assets;
		}

		foreach ( $zm_sh->iconsets->get_iconsets() as $iconSet ) {
			$assets[ $iconSet->id ] = array(
				'types' => array_values( (array) $iconSet->types ),
				'icons' => array(),
			);
			foreach ( (array) $iconSet->icons as $networkId => $icon ) {
				if ( empty( $icon['image'] ) ) {
					continue;
				}
				foreach ( $assets[ $iconSet->id ]['types'] as $type ) {
					$assets[ $iconSet->id ]['icons'][ $networkId ][ $type ] =
						trailingslashit( $iconSet->url . $type ) . rawurlencode( $icon['image'] );
				}
			}
		}

		return $assets;
	}

	public function render( $attributes ) {
		$attributes = is_array( $attributes ) ? $attributes : array();
		if ( isset( $attributes['icons'] ) && is_array( $attributes['icons'] ) && empty( $attributes['icons'] ) ) {
			return '';
		}
		$icons = isset( $attributes['icons'] ) && is_array( $attributes['icons'] )
			? array_filter( $attributes['icons'], 'is_scalar' )
			: array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' );

		return zm_sh_shortcode_cb(
			array(
				'title' => isset( $attributes['title'] ) ? $attributes['title'] : '',
				'iconset' => zm_sh_get_builder_iconset(
					isset( $attributes['iconset'] ) ? $attributes['iconset'] : 'inherit'
				),
				'iconset_type' => isset( $attributes['iconset_type'] )
					? $attributes['iconset_type']
					: 'square',
				'icons' => implode( ',', array_map( 'sanitize_key', $icons ) ),
				'class' => 'in_block',
			)
		);
	}
}
