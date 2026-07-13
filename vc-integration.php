<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'vc_before_init', 'zm_sh_integrateWithVC' );

function zm_sh_integrateWithVC() {
	global $zm_sh;
	if(isset($zm_sh->excluded) and $zm_sh->excluded == true) return;
	if ( ! function_exists( 'vc_map' ) ) return;
	$iconsets = $zm_sh->iconsets->get_iconset_list();
	$iconsets = array_flip($iconsets);

	$iconset	= $zm_sh->iconsets->get_current_iconset();
	$types		= $iconset->types;
	$icons		= $iconset->get_icons_id_name();
	$icons		= array_flip($icons);
	//print_r($iconsets);
	/*
	Add your Visual Composer logic here.
	Lets call vc_map function to "register" our custom shortcode within Visual Composer interface.

	More info: http://kb.wpbakery.com/index.php?title=Vc_map
	*/
	vc_map( array(
					"name" => __("Html Social Share", 'html-social-share-buttons'),
					"description" => __("Html Social Share", 'html-social-share-buttons'),
					"base" => "zm_sh_btn",
					"class" => "zm_sh_btn",
					"controls" => "full",
					"category" => __('Content', 'html-social-share-buttons'),
					'admin_enqueue_js' => array( plugins_url( '/assets', __FILE__) .'/vc_scripts.js'),
					//'admin_enqueue_css' => array(plugins_url( '/assets', __FILE__) .'/admin-widget.css'),
					"params" => array(
						array(
						  "type"		=> "textfield",
						  "holder"		=> "div",
						  "class"		=> "",
						  "heading"		=> __("Title", 'html-social-share-buttons'),
						  "param_name"	=> "title",
						  "value"		=> __("Share this page", 'html-social-share-buttons'),
						  "description"	=> __("Add social share button", 'html-social-share-buttons')
					  ),
						array(
						  "type"		=> "dropdown",
						  "holder"		=> "div",
						  "class"		=> "",
						  "heading"		=> __("Iconset", 'html-social-share-buttons'),
						  "param_name"	=> "iconset",
						  "value"		=> $iconsets,
						  "description"	=> __("Select iconset to use", 'html-social-share-buttons'),
					  ),
						array(
						  "type"		=> "dropdown",
						  "holder"		=> "div",
						  "class"		=> "",
						  "heading"		=> __("Iconset type", 'html-social-share-buttons'),
						  "param_name"	=> "iconset_type",
						  "value"		=> $types,
						  "description"	=> __("Select iconset type", 'html-social-share-buttons'),
					  ),
						array(
						  "type"		=> "checkbox",
						  "holder"		=> "div",
						  "class"		=> "",
						  "heading"		=> __("Icons", 'html-social-share-buttons'),
						  "param_name"	=> "icons",
						  "value"		=> $icons,
						  "description"	=> __("Select icons", 'html-social-share-buttons'),
						  //"dependency"	=> array("element"=>"iconset", "callback" => "$this->iconsets->get_icons"),

					  ),
				)
	) );
}
