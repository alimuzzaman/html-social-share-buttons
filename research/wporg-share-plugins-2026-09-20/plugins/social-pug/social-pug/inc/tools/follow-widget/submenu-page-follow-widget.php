<?php

/**
 * Function that creates the sub-menu item and page for the follow widget location of the share buttons.
 *
 * @return void
 */
function dpsp_register_follow_widget_subpage() {
	// Only run if user Lite or user has valid license
	// Temporarily deprecated
	// if ( ! dpsp_should_show_settings() ) {
	// 	return;
	// }

	// Only run if follow widget is active
	if ( ! dpsp_is_tool_active( 'follow_widget' ) ) {
		return;
	}

	add_submenu_page( 'dpsp-social-pug', __( 'Follow Widget', 'social-pug' ), __( 'Follow Widget', 'social-pug' ), 'manage_options', 'dpsp-follow-widget', 'dpsp_follow_widget_subpage' );
}

/**
 * Outputs content to the follow widget icons subpage.
 */
function dpsp_follow_widget_subpage() {
	include DPSP_PLUGIN_DIR . '/inc/tools/follow-widget/views/view-submenu-page-follow-widget.php';
}

/**
 *
 */
function dpsp_follow_widget_register_settings() {
	// Only run if follow widget is active
	if ( ! dpsp_is_tool_active( 'follow_widget' ) ) {
		return;
	}

	register_setting( 'dpsp_location_follow_widget', 'dpsp_location_follow_widget', 'dpsp_follow_widget_settings_sanitize' );

}

/**
 * Filter and sanitize settings.
 *
 * @param array $new_settings
 * @return array
 */
function dpsp_follow_widget_settings_sanitize( $new_settings ) {
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
