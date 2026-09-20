<?php

/**
 * Add the share sticky bar tool to the toolkit array.
 *
 * @param array $tools
 * @return array
 */
function dpsp_tool_share_sticky_bar( $tools = [] ) {
	//@TODO: Use Toolkit class
	$tools['share_sticky_bar'] = [
		'name'               => __( 'Sticky Bar', 'social-pug' ),
		'type'               => 'share_tool',
		'activation_setting' => 'dpsp_location_sticky_bar[active]',
		'img'                => 'assets/dist/tool-mobile.png?' . DPSP_VERSION,
		'admin_page'         => 'admin.php?page=dpsp-sticky-bar',
	];

	return $tools;
}

/**
 * Register the Sticky Bar hooks.
 */
function dpsp_register_sticky_bar() {
	add_filter( 'dpsp_get_tools', 'dpsp_tool_share_sticky_bar', 20 );
	add_action( 'wp_footer', 'dpsp_output_front_end_sticky_bar' );
	add_filter( 'the_content', 'dpsp_output_front_end_sticky_bar_content_markup' );
	add_filter( 'mv_grow_frontend_data', 'dpsp_sticky_bar_content_data' );
	add_action( 'admin_menu', 'dpsp_register_sticky_bar_subpage', 40 );
	add_action( 'admin_init', 'dpsp_sticky_bar_register_settings' );
}
