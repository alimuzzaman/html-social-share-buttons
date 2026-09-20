<?php

/**
 * Add the share inline content tool to the toolkit array.
 *
 * @param array $tools
 * @return array
 */
function dpsp_tool_share_inline_content( $tools = [] ) {
	$tools['share_content'] = [
		'name'               => __( 'Inline Content', 'social-pug' ),
		'type'               => 'share_tool',
		'activation_setting' => 'dpsp_location_content[active]',
		'img'                => 'assets/dist/tool-content.png?' . DPSP_VERSION,
		'admin_page'         => 'admin.php?page=dpsp-content',
	];

	return $tools;
}

/**
 * Register the Inline Content hooks.
 */
function dpsp_register_inline_content() {
	add_filter( 'dpsp_get_tools', 'dpsp_tool_share_inline_content', 15 );
	add_action( 'admin_menu', 'dpsp_register_content_subpage', 30 );
	add_action( 'admin_init', 'dpsp_content_register_settings' );
	\Mediavine\Grow\Frontend_Content::get_instance();
}
