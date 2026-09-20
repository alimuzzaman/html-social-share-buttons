<?php

/**
 * Function that creates the sub-menu item and page for the content location of the share buttons.
 */
function dpsp_register_content_subpage() {
	// Only run if user Lite or user has valid license
	// Temporarily deprecated
	// if ( ! dpsp_should_show_settings() ) {
	// 	return;
	// }
	
	// Only run if share content is active
	if ( ! dpsp_is_tool_active( 'share_content' ) ) {
		return;
	}

	add_submenu_page( 'dpsp-social-pug', __( 'Inline Content', 'social-pug' ), __( 'Inline Content', 'social-pug' ), 'manage_options', 'dpsp-content', 'dpsp_content_subpage' );
}

/**
 * Outputs content to the content icons subpage.
 */
function dpsp_content_subpage() {
	include DPSP_PLUGIN_DIR . '/inc/tools/share-inline-content/views/view-submenu-page-content.php';
}


function dpsp_content_register_settings() {
	// Only run if share content is active
	if ( ! dpsp_is_tool_active( 'share_content' ) ) {
		return;
	}

	register_setting( 'dpsp_location_content', 'dpsp_location_content', 'dpsp_content_settings_sanitize' );
}

/**
 * Filter and sanitize settings.
 *
 * @param array $new_settings
 */
function dpsp_content_settings_sanitize( $new_settings ) {
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
