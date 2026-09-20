<?php

/**
 * Function that creates the sub-menu item and page for the debugger.
 *
 * @return void
 */
function dpsp_register_debugger_subpage() {
	$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );

	if ( ! empty( $settings['debugger_enabled'] ) ) {
		add_submenu_page( 'dpsp-social-pug', __( 'Debugger', 'social-pug' ), __( 'Debugger', 'social-pug' ), 'manage_options', 'dpsp-debugger', 'dpsp_debugger_subpage' );
	}
}

/**
 * Outputs content to the debugger subpage.
 */
function dpsp_debugger_subpage() {
	include DPSP_PLUGIN_DIR . '/inc/admin/views/view-submenu-page-debugger.php';
}

/**
 * Register hooks for submenu-page-debugger.php
 */
function dpsp_register_admin_debugger() {
	add_action( 'admin_menu', 'dpsp_register_debugger_subpage', 101 );
}
