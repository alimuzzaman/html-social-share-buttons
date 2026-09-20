<?php //phpcs:ignore
/**
 * Tumblr share count functions.
 *
 * @package Simple_Social_Buttons
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format the tumblr response to beautiful count.
 *
 * @param string $response Response of the count call.
 * @return int Count of tumblr share.
 * @since 1.0.0
 */
function ssb_format_tumblr_response( $response ) {
	$response = json_decode( $response, true );

	if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $response ) ) {
		return 0;
	}

	if ( ! isset( $response['meta']['status'] ) || 200 !== (int) $response['meta']['status'] ) {
		return 0;
	}

	if ( ! isset( $response['response']['note_count'] ) ) {
		return 0;
	}

	return max( 0, (int) $response['response']['note_count'] );
}

/**
 * Generate link for tumblr get count API.
 *
 * @param string $url URL to get share count for.
 * @return string Ready link to call for API.
 * @since 1.0.0
 */
function ssb_tumblr_generate_link( $url ) {
	return 'https://api.tumblr.com/v2/share/stats?url=' . rawurlencode( $url );
}
