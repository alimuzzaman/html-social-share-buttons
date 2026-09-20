<?php

/**
 * Register the Top Shared Posts widget.
 */
function dpsp_register_widget_top_shared_posts() {
	if ( class_exists( 'DPSP_Top_Shared_Posts' ) ) {
		register_widget( 'DPSP_Top_Shared_Posts' );
	}
}

/**
 * Register hooks for admin-widgets.php.
 */
function dpsp_register_admin_widgets() {
	add_action( 'widgets_init', 'dpsp_register_widget_top_shared_posts' );
}
