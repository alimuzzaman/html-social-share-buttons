<?php // phpcs:ignore

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Format the twitter response to beautiful count.
 *
 * @param string $response Response of the count call.
 * @return int Count of twitter share.
 * @since 1.0.0
 */
function ssb_format_twitter_response( $response ) {
	// Parse the response to get the actual number.
	$response = json_decode( $response, true );
	// Check for JSON decode errors.
	if ( json_last_error() !== JSON_ERROR_NONE || ! isset( $response['count'] ) ) {
		return 0;
	}
	return isset( $response['count'] ) ? intval( $response['count'] ) : 0;
}

/**
 * Generate link for twitter get count API.
 *
 * @param string $url URL to get share count for.
 * @return string Ready link to call for API.
 * @since 1.0.0
 * @version 7.0.1
 */
function ssb_twitter_generate_link( $url ) {

	// Return the correct Twitter JSON endpoint URL.
	$request_url = 'https://counts.twitcount.com/counts.php?url=' . rawurlencode( $url );
	return $request_url;
}
