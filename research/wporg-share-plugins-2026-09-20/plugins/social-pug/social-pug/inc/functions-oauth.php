<?php
/** Functions related to OAuth capture */

/**
 * Listens for our API's Constant Contact response with the access code from the Hubbub app.
 */
function dpsp_capture_oauth_access_token() {

    $connection = ( ! empty( filter_input( INPUT_GET, 'connection' ) ) ) ? filter_input( INPUT_GET, 'connection' ) : 'facebook';
    $redirect_page = ( ! empty( filter_input( INPUT_GET, 'redirect_page' ) ) ) ? filter_input( INPUT_GET, 'redirect_page' ) : 'dpsp-settings';

	$token = filter_input( INPUT_GET, 'tkn' );
	if ( empty( $token ) ) {
		return false;
	}

    if ( empty( $_GET['access_token'] ) && empty( $_GET['facebook_access_token'] ) ) {
		return false;
	}

    switch( $connection ) {
        case 'facebook':
            $nonce_id       = 'dpsp_authorize_facebook_app';
            $settings_id    = 'dpsp_facebook_access_token';

            $settings       = [
                'access_token' => sanitize_text_field( filter_input( INPUT_GET, 'facebook_access_token' ) ),
		        'expires_in'   => time() + absint( filter_input( INPUT_GET, 'expires_in' ) ),
            ];
        break;
        case 'constant-contact':

            if ( empty( $_GET['refresh_token'] ) ) {
                return false;
            }

            $nonce_id       = 'dpsp_oauth_constant_contact';
            $settings_id    = 'dpsp_oauth_constant_contact';
            $redirect_page  = 'dpsp-email-save-this';

            $settings       = [
                'access_token'  => sanitize_text_field( filter_input( INPUT_GET, 'access_token' ) ),
                'refresh_token' => sanitize_text_field( filter_input( INPUT_GET, 'refresh_token' ) ),
		        'expires_at'    => strtotime( '+20 hours' ),
            ];
            $dpsp_email_save_this = Mediavine\Grow\Settings::get_setting( 'dpsp_email_save_this', 'not_set' );
            $dpsp_email_save_this['connection']['service'] = 'constant-contact';
            update_option( 'dpsp_email_save_this', $dpsp_email_save_this );
            wp_schedule_event( strtotime( '+12 hours' ), 'twicedaily', 'dpsp_cron_refresh_constant_contact_token' );
        break;
        case 'drip':
            $nonce_id       = 'dpsp_oauth_drip';
            $settings_id    = 'dpsp_oauth_drip';
            $redirect_page  = 'dpsp-email-save-this';

            $settings       = [
                'access_token'  => sanitize_text_field( filter_input( INPUT_GET, 'access_token' ) ),
                'account_id' => sanitize_text_field( filter_input( INPUT_GET, 'account_id' ) ),
            ];

            $dpsp_email_save_this = Mediavine\Grow\Settings::get_setting( 'dpsp_email_save_this', 'not_set' );
            $dpsp_email_save_this['connection']['service'] = 'drip';
            update_option( 'dpsp_email_save_this', $dpsp_email_save_this );
        break;
    }

    if ( ! wp_verify_nonce( $token, $nonce_id ) ) {
		return false;
	}

	update_option( $settings_id, $settings );
	wp_redirect(
		add_query_arg(
			[
				'page'             => $redirect_page,
				'dpsp_message_id'  => 4,
				'settings-updated' => '',
			],
			admin_url( 'admin.php' )
		)
	);
	exit;
}