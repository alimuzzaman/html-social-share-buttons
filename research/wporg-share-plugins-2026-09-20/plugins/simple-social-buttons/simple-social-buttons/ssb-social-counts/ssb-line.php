<?php // phpcs:ignore

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Line share counts use ssb_fetch_shares_via_http_api() (5s timeout, cached with other networks).
 *
 * Format the line response to beautiful count.
 *
 * @param array $response response of the count call.
 * @since 7.0.0
 * @return  int count of line share.
 */
function ssb_format_line_response( $response ) {
	// Parse the response to get the actual number.
	$response = json_decode( $response, true ); // phpcs:ignore
	return isset( $response['share'] ) ? intval( $response['share'] ) : 0;
}

/**
 * Generate link for line get count API.
 *
 * @param string $url URL to get share count for.
 * @since 7.0.0
 * @return string  ready link to call for API.
 */
function ssb_line_generate_link( $url ) {
	// Line Social Plugin Metrics API.
	$request_url = 'https://api.line.me/social-plugin/metrics?url=' . rawurlencode( $url );
	return $request_url;
}
