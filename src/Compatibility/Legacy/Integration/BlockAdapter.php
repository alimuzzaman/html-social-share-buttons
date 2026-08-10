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
		if ( ! function_exists( 'register_block_type' ) ) {
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
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations(
				'zm-sh-social-share-block',
				'html-social-share-buttons',
				$this->pluginRoot . '/languages'
			);
		}

		$metadataPath = $this->pluginRoot . '/block.json';
		$args = array( 'render_callback' => array( $this, 'renderRegisteredBlock' ) );
		if ( function_exists( 'register_block_type_from_metadata' ) ) {
			register_block_type( $metadataPath, $args );

			return;
		}

		$metadata = json_decode( (string) file_get_contents( $metadataPath ), true );
		if ( ! is_array( $metadata ) || empty( $metadata['name'] ) ) {
			return;
		}
		register_block_type(
			$metadata['name'],
			array_merge(
				$args,
				array(
					'attributes' => isset( $metadata['attributes'] ) ? $metadata['attributes'] : array(),
					'editor_script' => 'zm-sh-social-share-block',
				)
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

	public function renderRegisteredBlock( $attributes, $content = '', $block = null ) {
		$contextPostId = is_object( $block ) && isset( $block->context['postId'] )
			? absint( $block->context['postId'] )
			: 0;

		return $this->render( $attributes, $contextPostId );
	}

	public function render( $attributes, $contextPostId = 0 ) {
		global $zm_sh;

		if ( ! is_object( $zm_sh ) || ! method_exists( $zm_sh, 'renderCanonical' ) ) {
			return '';
		}
		$attributes = is_array( $attributes ) ? $attributes : array();
		if ( isset( $attributes['icons'] ) && is_array( $attributes['icons'] ) && empty( $attributes['icons'] ) ) {
			return '';
		}
		$icons = isset( $attributes['icons'] ) && is_array( $attributes['icons'] )
			? array_filter( $attributes['icons'], 'is_scalar' )
			: array( 'facebook', 'x', 'linkedin', 'pinterest', 'mail' );

		return $zm_sh->renderCanonical(
			array(
				'title' => isset( $attributes['title'] ) ? $attributes['title'] : '',
				'iconset' => zm_sh_get_builder_iconset(
					isset( $attributes['iconset'] ) ? $attributes['iconset'] : 'inherit'
				),
				'iconset_type' => isset( $attributes['iconset_type'] )
					? $attributes['iconset_type']
					: 'square',
				'icons' => array_fill_keys(
					array_filter( array_map( 'sanitize_key', $icons ) ),
					'on'
				),
				'class' => 'in_block',
			),
			$contextPostId
		);
	}
}
