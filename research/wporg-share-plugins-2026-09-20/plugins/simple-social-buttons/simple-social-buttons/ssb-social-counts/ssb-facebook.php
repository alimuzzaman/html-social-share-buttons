<?php // phpcs:ignore
/**
 * Facebook share count functions.
 *
 * @package Simple_Social_Buttons
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format the facebook response to share count only (engagement.share_count).
 *
 * @param string $response Response of the count call.
 * @return int Share count from Graph API engagement.share_count.
 * @since 1.0.0
 * @version 7.0.0
 */
function ssb_format_fbshare_response( $response ) {
	$formatted_response = json_decode( $response, true );

	// Check for JSON decode errors.
	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return 0;
	}

	// Check for Facebook API errors.
	if ( isset( $formatted_response['error'] ) ) {
		return 0;
	}

	if ( ! isset( $formatted_response['engagement'] ) || ! is_array( $formatted_response['engagement'] ) ) {
		return 0;
	}

	$engagement = $formatted_response['engagement'];
	$shares     = isset( $engagement['share_count'] ) ? (int) $engagement['share_count'] : 0;

	/**
	 * Filter the Facebook number used for share counters (default: share_count only).
	 *
	 * @param int   $shares     Parsed share_count.
	 * @param array $engagement Engagement object from the Graph API response.
	 */
	return (int) apply_filters( 'ssb_facebook_share_count', $shares, $engagement );
}


/**
 * Generate link for facebook get count API.
 *
 * @param string $url URL to get share count for.
 * @return string|false Ready link to call for API, or false if credentials missing.
 * @since 1.0.0
 * @version 7.0.0
 */
function ssb_fbshare_generate_link( $url ) {
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	global $_ssb_pr;
	$advance_settings    = $_ssb_pr->extra_option;
	$facebook_app_id     = isset( $advance_settings['facebook_app_id'] ) ? $advance_settings['facebook_app_id'] : '';
	$facebook_secret_key = isset( $advance_settings['facebook_app_secret'] ) ? $advance_settings['facebook_app_secret'] : '';

	// Validate the Facebook App ID and Secret.
	if ( empty( $facebook_app_id ) || empty( $facebook_secret_key ) ) {
		return false; // Return false if credentials are missing.
	}

	// Use the Facebook Graph API version 24.0.
	$api_version = 'v25.0';

	// Request only share_count under engagement (smaller payload than full engagement).
	$fields = rawurlencode( 'engagement{share_count}' );
	$link   = 'https://graph.facebook.com/' . $api_version . '/?id=' . rawurlencode( $url )
		. '&fields=' . $fields
		. '&access_token=' . $facebook_app_id . '|' . $facebook_secret_key;

	return $link;
}
