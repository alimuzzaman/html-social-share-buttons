<?php

/**
 * Function that creates the sub-menu item and page for the settings page.
 */
function dpsp_register_settings_subpage() {
	add_submenu_page( 'dpsp-social-pug', __( 'Settings', 'social-pug' ), __( 'Settings', 'social-pug' ), 'manage_options', 'dpsp-settings', 'dpsp_settings_subpage' );
}

/**
 * Outputs content to the settings subpage.
 */
function dpsp_settings_subpage() {
	$tabs = [
		'general-settings' => __( 'General Settings', 'social-pug' ),
		'social-identity'  => __( 'Social Identity', 'social-pug' ),
	];

	$tabs = apply_filters( 'dpsp_submenu_page_settings_tabs', $tabs );

	$pro = ( \Social_Pug::is_free() ) ? '' : '-pro';
	include DPSP_PLUGIN_DIR . '/inc/admin/views/view-submenu-page-settings' . $pro . '.php';
}

/**
 * Register DPSP Settings
 */
function dpsp_settings_register_settings() {
	register_setting( 'dpsp_settings', 'dpsp_settings' );
}

/**
 * Hooks to generate a Facebook App access token that will be used for retrieving share counts.
 */
function dpsp_generate_facebook_app_access_token( $new_settings = [], $old_settings = [] ) {

	if ( empty( $new_settings['facebook_app_id'] ) || empty( $new_settings['facebook_app_secret'] ) ) {
		return $new_settings;
	}

	$response = wp_remote_post(
		add_query_arg(
			[
				'client_id'     => trim( $new_settings['facebook_app_id'] ),
				'client_secret' => trim( $new_settings['facebook_app_secret'] ),
				'grant_type'    => 'client_credentials',
			],
			'https://graph.facebook.com/oauth/access_token'
		)
	);

	if ( is_wp_error( $response ) ) {
		return $new_settings;
	}

	if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
		return $new_settings;
	}

	$body = wp_remote_retrieve_body( $response );
	$body = json_decode( $body, true );

	if ( ! empty( $body['access_token'] ) && strpos( $body['access_token'], '|' ) !== false ) {
		$new_settings['facebook_app_access_token'] = $body['access_token'];
	}

	return $new_settings;
}

/**
 * Hooks to update settings to check the serial status and update it.
 */
function dpsp_update_serial_key_status( $old_settings = [], $new_settings = [] ) {

	if ( \Social_Pug::is_free() ) :
		$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
		$hubbub_activation_lite->validate_license( $old_settings, $new_settings );

		if ( $new_settings['mv_grow_license'] == '' ) {
			delete_option( 'mv_grow_license' );
			delete_option( 'mv_grow_license_status' );
			delete_option( 'mv_grow_license_status_date' );
			delete_option( 'mv_grow_license_tier' );
		}
		return;
	endif;

	$hubbub_activation = new \Mediavine\Grow\Activation;
	$hubbub_activation->validate_license( $old_settings, $new_settings );

	if ( isset($new_settings) && isset($new_settings['mv_grow_license']) && $new_settings['mv_grow_license'] == '' ) {
		delete_option( 'mv_grow_license' );
		delete_option( 'mv_grow_license_status' );
		delete_option( 'mv_grow_license_status_date' );
		delete_option( 'mv_grow_license_tier' );
	}
}


/**
 * Checks the current license key in Settings
 * Likely run with cron. This is a helper function.
 */
function dpsp_check_serial_key_status() {
	if ( \Social_Pug::is_free() ) :
		return;
	endif;

	$hubbub_activation = new \Mediavine\Grow\Activation;
	$hubbub_activation->check_license();
}

/**
 * Santizes all settings for Hubbub prior to being saved to the database
 */
function dpsp_sanitize_all_settings( $settings ){
	return array_map( 'sanitize_text_field', $settings );
}

/**
 * Register hooks for submenu-page-settings.php.
 */
function dpsp_register_admin_settings() {
	add_action( 'admin_menu', 'dpsp_register_settings_subpage', 100 );
	add_action( 'admin_init', 'dpsp_settings_register_settings' );
	add_filter( 'pre_update_option_dpsp_settings', 'dpsp_generate_facebook_app_access_token', 10, 2 );
	add_action( 'update_option_dpsp_settings', 'dpsp_update_serial_key_status', 10, 2 );
	add_action( 'pre_update_option_dpsp_settings', 'dpsp_sanitize_all_settings', 10, 2 );
}
