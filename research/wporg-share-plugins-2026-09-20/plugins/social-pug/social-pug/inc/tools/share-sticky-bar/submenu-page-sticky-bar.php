<?php

/**
 * Function that creates the sub-menu item and page for the sticky bar location of the share buttons.
 */
function dpsp_register_sticky_bar_subpage() {
	// Only run if user Lite or user has valid license
	// Temporarily deprecated
	// if ( ! dpsp_should_show_settings() ) {
	// 	return;
	// }
	
	// Only run if share sticky bar is active
	if ( ! dpsp_is_tool_active( 'share_sticky_bar' ) ) {
		return;
	}

	add_submenu_page( 'dpsp-social-pug', __( 'Sticky Bar', 'social-pug' ), __( 'Sticky Bar', 'social-pug' ), 'manage_options', 'dpsp-sticky-bar', 'dpsp_sticky_bar_subpage' );
}

/**
 * Outputs content to the sticky bar icons subpage.
 */
function dpsp_sticky_bar_subpage() {
	include DPSP_PLUGIN_DIR . '/inc/tools/share-sticky-bar/views/view-submenu-page-sticky-bar.php';
}

/**
 * Registers the settings for the sticky bar.
 *
 */
function dpsp_sticky_bar_register_settings() {
	// Only run if share sticky bar is active
	if ( ! dpsp_is_tool_active( 'share_sticky_bar' ) ) {
		return;
	}

	register_setting( 'dpsp_location_sticky_bar', 'dpsp_location_sticky_bar', 'dpsp_sticky_bar_settings_sanitize' );
}

/**
 * Filter and sanitize settings.
 *
 * @param array $new_settings
 * @return array
 *
 */
function dpsp_sticky_bar_settings_sanitize( $new_settings ) {
	// Save default values even if values do not exist
	if ( ! isset( $new_settings['networks'] ) ) {
		$new_settings['networks'] = [];
	}

	if ( ! isset( $new_settings['button_style'] ) ) {
		$new_settings['button_style'] = 1;
	}

	// Add default screen size to display the buttons
	if ( empty( $new_settings['display']['screen_size'] ) ) {
		$new_settings['display']['screen_size'] = '720';
	}

	// Remove "px", "pixels" or "pixel" if found
	$new_settings['display']['screen_size'] = str_replace( 'px', '', $new_settings['display']['screen_size'] );
	$new_settings['display']['screen_size'] = str_replace( 'pixels', '', $new_settings['display']['screen_size'] );
	$new_settings['display']['screen_size'] = str_replace( 'pixel', '', $new_settings['display']['screen_size'] );
	$new_settings['display']['screen_size'] = trim( $new_settings['display']['screen_size'] );

	$new_settings = dpsp_array_strip_script_tags( $new_settings );

	return $new_settings;
}
