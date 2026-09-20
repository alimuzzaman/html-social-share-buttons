<?php

/**
 * Add the share floating sidebar tool to the toolkit array.
 *
 * @param array $tools
 * @return array
 */
function dpsp_tool_share_sidebar( $tools = [] ) {
	//@TODO: Use Toolkit class
	$tools['share_sidebar'] = [
		'name'               => __( 'Floating Sidebar', 'social-pug' ),
		'type'               => 'share_tool',
		'activation_setting' => 'dpsp_location_sidebar[active]',
		'img'                => 'assets/dist/tool-sidebar.png?' . DPSP_VERSION,
		'admin_page'         => 'admin.php?page=dpsp-sidebar',
	];

	return $tools;
}

/**
 * Register the Floating Sidebar hooks.
 */
function dpsp_register_floating_sidebar() {
	add_action( 'wp_footer', 'dpsp_output_front_end_floating_sidebar' );
	add_filter( 'dpsp_get_tools', 'dpsp_tool_share_sidebar', 10 );
	add_action( 'admin_menu', 'dpsp_register_floating_sidebar_subpage', 20 );
	add_action( 'admin_init', 'dpsp_sidebar_register_settings' );
}
