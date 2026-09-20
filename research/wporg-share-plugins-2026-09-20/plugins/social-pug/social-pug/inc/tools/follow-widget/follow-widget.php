<?php

/**
 * Add the follow widget tool to the toolkit array.
 *
 * @param array $tools
 * @return array
 */
function dpsp_tool_follow_widget( $tools = [] ) {
	//@TODO: Use Toolkit class
	$tools['follow_widget'] = [
		'name'               => __( 'Follow Widget', 'social-pug' ),
		'type'               => 'follow_tool',
		'activation_setting' => 'dpsp_location_follow_widget[active]',
		'img'                => 'assets/dist/tool-follow-widget.png?' . DPSP_VERSION,
		'admin_page'         => 'admin.php?page=dpsp-follow-widget',
	];

	return $tools;
}

/**
 * Register the Follow Widgets hooks.
 */
function dpsp_register_follow_widget() {
	add_action( 'widgets_init', 'dpsp_social_media_follow_buttons' );
	add_filter( 'dpsp_get_tools', 'dpsp_tool_follow_widget', 30 );
	add_action( 'admin_menu', 'dpsp_register_follow_widget_subpage', 50 );
	add_action( 'admin_init', 'dpsp_follow_widget_register_settings' );
}
