<?php

/**
 * Enqueue admin scripts for the feedback form.
 */
function dpsp_enqueue_admin_scripts_feedback() {
	// Plugin styles
	wp_register_style( 'dpsp-style-feedback', DPSP_PLUGIN_DIR_URL . 'inc/admin/feedback-form/assets/css/style-admin-feedback-form.css', [], DPSP_VERSION );
	wp_enqueue_style( 'dpsp-style-feedback' );

	// Plugin script
	wp_register_script( 'dpsp-script-feedback', DPSP_PLUGIN_DIR_URL . 'inc/admin/feedback-form/assets/js/script-admin-feedback-form.js', [ 'jquery' ], DPSP_VERSION );
	wp_enqueue_script( 'dpsp-script-feedback' );
}

/**
 * Outputs the feedback form in the admin footer.
 */
function dpsp_output_feedback_form() {
	$page = filter_input( INPUT_GET, 'page' );
	if ( empty( $page ) || false === strpos( $page, 'dpsp' ) ) {
		return;
	}
	include 'views/view-feedback-form.php';
}
