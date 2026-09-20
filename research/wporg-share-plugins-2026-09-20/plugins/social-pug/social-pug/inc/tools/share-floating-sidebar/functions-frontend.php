<?php

// Check that the sidebar has been added only once
global $dpsp_output_front_end_floating_sidebar;

use Mediavine\Grow\View_Loader;

/**
 * Displays the floating sidebar sharing buttons.
 */
function dpsp_output_front_end_floating_sidebar() {
	// Only run if share sidebar is active
	if ( ! dpsp_is_tool_active( 'share_sidebar' ) ) {
		return;
	}

	if ( ! dpsp_is_tool_active( 'share_sidebar' ) ) {
		return;
	}

	if ( ! dpsp_is_location_displayable( 'sidebar' ) ) {
		return;
	}

	if ( doing_action( 'before_feast_layout' ) || doing_action( 'genesis_header' ) || doing_action( 'genesis_after_header' ) || doing_action( 'genesis_footer' ) || doing_action( 'genesis_sidebar' ) || doing_action( 'kadence_before_sidebar' ) || doing_action( 'kadence_before_footer' ) || doing_action('kadence_dynamic_sidebar_content') || doing_action('kadence_after_header') ) {
        return $content;
    }

	// Check that the sidebar has been added only once
	$tool_container = \Mediavine\Grow\Tools\Toolkit::get_instance();
	$tool_instance  = $tool_container->get( 'sidebar' );
	if ( $tool_instance->has_rendered() ) {
		return;
	}
	$tool_instance->render();

	// Get saved settings
	$settings = \Mediavine\Grow\Tools\Floating_Sidebar::get_prepared_settings();

	// Set Scroll trigger value
	$scroll_trigger = ( isset( $settings['display']['show_after_scrolling'] ) ? ( ! empty( $settings['display']['scroll_distance'] ) ? (int) str_replace( '%', '', trim( $settings['display']['scroll_distance'] ) ) : 0 ) : 'false' );

	// Echo the final output
	echo wp_kses(
		apply_filters(
			'dpsp_output_front_end_floating_sidebar', \Mediavine\Grow\View_Loader::get_view(
				'/inc/tools/share-floating-sidebar/views/frontend.php', [
					'settings'        => $settings,
					'scroll_trigger'  => $scroll_trigger,
					'wrapper_classes' => \Mediavine\Grow\Tools\Floating_Sidebar::get_wrapper_classes( $settings ),
				]
			)
		), View_Loader::get_allowed_tags()
	);
}
