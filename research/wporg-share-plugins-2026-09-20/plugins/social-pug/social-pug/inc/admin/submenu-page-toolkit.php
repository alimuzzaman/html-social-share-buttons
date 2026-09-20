<?php

/**
 * Function that creates the sub-menu item and page for the tools page.
 */
function dpsp_register_toolkit_subpage() {
	// Only run if user Lite or user has valid license
	// Temporarily deprecated
	// if ( ! dpsp_should_show_settings() ) {
	// 	return;
	// }
	
	add_submenu_page( 'dpsp-social-pug', __( 'Toolkit', 'social-pug' ), __( 'Toolkit', 'social-pug' ), 'manage_options', 'dpsp-toolkit', 'dpsp_toolkit_subpage' );
}

/**
 * Outputs content to the toolkit subpage.
 */
function dpsp_toolkit_subpage() {
	include DPSP_PLUGIN_DIR . '/inc/admin/views/view-submenu-page-toolkit.php';
}

/**
 * Register hooks for submenu-page-toolkit.php.
 */
function dpsp_register_admin_toolkit() {
	add_action( 'admin_menu', 'dpsp_register_toolkit_subpage', 15 );
}
