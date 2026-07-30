<?php

namespace Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Integration;

final class WpBakeryAdapter {
	private $pluginRoot;

	public function __construct( $pluginRoot ) {
		$this->pluginRoot = rtrim( (string) $pluginRoot, '/\\' );
	}

	public function register() {
		add_action( 'vc_before_init', 'zm_sh_integrateWithVC' );
	}

	public function integrate() {
		global $zm_sh;

		if (
			! is_object( $zm_sh ) ||
			! isset( $zm_sh->iconsets ) ||
			! function_exists( 'vc_map' ) ||
			( isset( $zm_sh->excluded ) && $zm_sh->excluded == true )
		) {
			return;
		}

		$iconSets = array_flip( $zm_sh->iconsets->get_iconset_list() );
		$iconSet = $zm_sh->iconsets->get_current_iconset();
		if ( ! is_object( $iconSet ) ) {
			return;
		}

		vc_map(
			array(
				'name' => __( 'Html Social Share', 'html-social-share-buttons' ),
				'description' => __( 'Html Social Share', 'html-social-share-buttons' ),
				'base' => 'zm_sh_btn',
				'class' => 'zm_sh_btn',
				'controls' => 'full',
				'category' => __( 'Content', 'html-social-share-buttons' ),
				'params' => array(
					array(
						'type' => 'textfield',
						'holder' => 'div',
						'class' => '',
						'heading' => __( 'Title', 'html-social-share-buttons' ),
						'param_name' => 'title',
						'value' => __( 'Share this page', 'html-social-share-buttons' ),
						'description' => __( 'Add social share button', 'html-social-share-buttons' ),
					),
					array(
						'type' => 'dropdown',
						'holder' => 'div',
						'class' => '',
						'heading' => __( 'Iconset', 'html-social-share-buttons' ),
						'param_name' => 'iconset',
						'value' => $iconSets,
						'description' => __( 'Select iconset to use', 'html-social-share-buttons' ),
					),
					array(
						'type' => 'dropdown',
						'holder' => 'div',
						'class' => '',
						'heading' => __( 'Iconset type', 'html-social-share-buttons' ),
						'param_name' => 'iconset_type',
						'value' => $iconSet->types,
						'description' => __( 'Select iconset type', 'html-social-share-buttons' ),
					),
					array(
						'type' => 'checkbox',
						'holder' => 'div',
						'class' => '',
						'heading' => __( 'Icons', 'html-social-share-buttons' ),
						'param_name' => 'icons',
						'value' => array_flip( $iconSet->get_icons_id_name() ),
						'description' => __( 'Select icons', 'html-social-share-buttons' ),
					),
				),
			)
		);
	}
}
