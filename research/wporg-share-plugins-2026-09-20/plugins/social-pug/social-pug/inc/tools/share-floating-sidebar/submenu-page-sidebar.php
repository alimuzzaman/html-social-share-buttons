<?php

/**
 * Creates the sub-menu item and page for the floating sidebar location of the share buttons.
 */
function dpsp_register_floating_sidebar_subpage() {
	// Only run if user Lite or user has valid license
	// Temporarily deprecated
	// if ( ! dpsp_should_show_settings() ) {
	// 	return;
	// }
	
	// Only run if share sidebar is active
	if ( ! dpsp_is_tool_active( 'share_sidebar' ) ) {
		return;
	}

	add_submenu_page( 'dpsp-social-pug', __( 'Floating Sidebar', 'social-pug' ), __( 'Floating Sidebar', 'social-pug' ), 'manage_options', 'dpsp-sidebar', 'dpsp_sidebar_subpage' );
}

/**
 * Outputs content to the floating sidebar subpage.
 */
function dpsp_sidebar_subpage() {
	include DPSP_PLUGIN_DIR . '/inc/tools/share-floating-sidebar/views/view-submenu-page-sidebar.php';
}

/**
 *
 */
function dpsp_sidebar_register_settings() {
	// Only run if share sidebar is active
	if ( ! dpsp_is_tool_active( 'share_sidebar' ) ) {
		return;
	}

	register_setting( 'dpsp_location_sidebar', 'dpsp_location_sidebar', 'dpsp_sidebar_settings_sanitize' );
}

/**
 * Filter and sanitize settings.
 *
 * @param array $new_settings
 * @return array
 */
function dpsp_sidebar_settings_sanitize( $new_settings ) {
	// Save default values even if values do not exist
	if ( ! isset( $new_settings['networks'] ) ) {
		$new_settings['networks'] = [];
	}

	if ( ! isset( $new_settings['button_style'] ) ) {
		$new_settings['button_style'] = 1;
	}

	$new_settings = dpsp_array_strip_script_tags( $new_settings );

	return $new_settings;
}
