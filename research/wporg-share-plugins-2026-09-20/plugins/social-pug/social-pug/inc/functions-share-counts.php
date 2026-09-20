<?php

use Mediavine\Grow\Networks;
use Mediavine\Grow\Share_Counts;

/**
 * Listens for the Facebook response with the access code from the Hubbub Facebook app.
 */
// function dpsp_capture_authorize_facebook_access_token() {
// 	$token = filter_input( INPUT_GET, 'tkn' );
// 	if ( empty( $token ) || ! wp_verify_nonce( $token, 'dpsp_authorize_facebook_app' ) ) {
// 		return false;
// 	}

// 	if ( empty( $_GET['facebook_access_token'] ) ) {
// 		return false;
// 	}

// 	if ( empty( $_GET['expires_in'] ) ) {
// 		return false;
// 	}

// 	$facebook_access_token = [
// 		'access_token' => sanitize_text_field( filter_input( INPUT_GET, 'facebook_access_token' ) ),
// 		'expires_in'   => time() + absint( filter_input( INPUT_GET, 'expires_in' ) ),
// 	];

// 	update_option( 'dpsp_facebook_access_token', $facebook_access_token );
// 	wp_redirect(
// 		add_query_arg(
// 			[
// 				'page'             => 'dpsp-settings',
// 				'dpsp_message_id'  => 4,
// 				'settings-updated' => '',
// 			],
// 			admin_url( 'admin.php' )
// 		)
// 	);
// 	exit;
// }

/**
 * Not all social networks support social count.
 *
 * This function returns an array of network slugs for the networks that do support it.
 *
 * @return array
 */
function dpsp_get_networks_with_social_count() {
	$networks           = Networks::get_instance();
	$countable_networks = $networks->get_countable();

	$countable_networks = $networks->make_slug_array( $countable_networks );

	/**
	 * Filter the networks that support share counts before returning
	 *
	 * @param array
	 *
	 */
	return apply_filters( 'dpsp_get_networks_with_social_count', $countable_networks );
}

/**
 * Returns the share count for a post and a social network from the social network through an API.
 * This function determines if the requested network differentiates between https and http, and if
 * so creates urls to check for each and pings the network, otherwise it will just pass through to
 * dpsp_get_url_network_share_count, then allow the result of that function to be filtered
 *
 * @param int    $post_id ID of the post.
 * @param string $network_slug Slug of the social network.
 * @return bool|int False if something went wrong, and int if everything went well
 */
function dpsp_get_post_network_share_count( $post_id, $network_slug ) {
	if ( empty( $post_id ) || empty( $network_slug ) ) {
		return false;
	}

	// The return value
	$share_count = false;

	// Get page url for the post
	$page_url = get_permalink( $post_id );

	// Get plugin settings
	$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );

	/**
	 * These are the networks that consider http and https versions of the
	 * same page URL as being different.
	 *
	 * Example: http://mediavine.com/  - returns a share count of 134
	 *            https://mediavine.com/ - returns a share count of 208
	 * Given that it is basically the same page, we may want to return the sum of the two.
	 */
	$networks = [ 'facebook', 'pinterest' ];

	// Return the share counts only for the current protocol
	if ( ! in_array( $network_slug, $networks, true ) || empty( $settings['http_and_https_share_counts'] ) ) {
		$share_count = dpsp_get_url_network_share_count( $page_url, $network_slug );
	}

	// Return the share counts for both HTTP and HTTPS protocols
	if ( in_array( $network_slug, $networks, true ) && ! empty( $settings['http_and_https_share_counts'] ) ) {
		// Check if the post's permalink has HTTP or HTTPS
		if ( 0 === strpos( strtolower( $page_url ), 'https' ) ) {
			$https_page_url = $page_url;
			$http_page_url  = substr_replace( $page_url, 'http', 0, 5 );
		} else {
			$https_page_url = substr_replace( $page_url, 'https', 0, 4 );
			$http_page_url  = $page_url;
		}

		$http_share_counts  = dpsp_get_url_network_share_count( $http_page_url, $network_slug );
		$https_share_counts = dpsp_get_url_network_share_count( $https_page_url, $network_slug );

		// If both share counts are good return the sum of them
		if ( false !== $http_share_counts && false !== $https_share_counts ) {
			$share_count = $http_share_counts + $https_share_counts;
		}

		if ( false === $http_share_counts ) {
			$share_count = $https_share_counts;
		}

		if ( false === $https_share_counts ) {
			$share_count = $http_share_counts;
		}
	}

	/**
	 * Filter the share count just before returning.
	 * @param int|false $share_count
	 * @param int $post_id
	 * @param string $network_slug
	 */
	$share_count = apply_filters( 'dpsp_get_post_network_share_count', $share_count, $post_id, $network_slug );

	return $share_count;
}

/**
 * Returns the share count for a given url and a social network from the social network through an API.
 *
 * @param string $url            - the URL for which we want the share counts
 * @param string $network_slug - slug of the social network
 * @return mixed                - bool false if something went wrong, and int if everything went well
 */
function dpsp_get_url_network_share_count( $url = '', $network_slug = '' ) {
	if ( empty( $url ) || empty( $network_slug ) ) {
		return false;
	}

	// Plugin settings
	$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );

	// Encode URL
	$page_url = rawurlencode( $url );

	// Default post arguments
	// TODO Does this need to be so high?
	$args = [ 'timeout' => 10 ]; // phpcs:ignore WordPressVIPMinimum.Performance.RemoteRequestTimeout

	// Prepare urls to get remote request
	switch ( $network_slug ) {
		case 'facebook':
			$access_token = '';
			if ( ! empty( $settings['facebook_share_counts_provider'] ) ) {

				// Grab the token from the authorized app
				if ( 'authorized_app' === $settings['facebook_share_counts_provider'] ) {
					$facebook_access_token = Mediavine\Grow\Settings::get_setting( 'dpsp_facebook_access_token' );

					$access_token = ( ! empty( $facebook_access_token['access_token'] ) ? $facebook_access_token['access_token'] : '' );
				}

				// Grab the token from the user's own app
				if ( 'own_app' === $settings['facebook_share_counts_provider'] ) {
					$access_token = ( ! empty( $settings['facebook_app_access_token'] ) ? $settings['facebook_app_access_token'] : '' );
				}
			}

			// Facebook requires an access token to use their API
			if ( ! empty( $access_token ) ) {
				$remote_url = 'https://graph.facebook.com/v9.0/?id=' . $page_url . '&access_token=' . $access_token . '&fields=engagement';
			}
			break;

		case 'pinterest':
			$remote_url = 'https://widgets.pinterest.com/v1/urls/count.json?source=6&url=' . $page_url;
			break;
		case 'grow':
			$remote_url = 'https://api.grow.me/graphql?query=%0A++query+GetPageBookmarkCount%28%24where%3A+PageWhereUniqueInput%21%29+%7B%0A++++page%28where%3A+%24where%29+%7B%0A++++++__typename%0A++++++id%0A++++++bookmarkCount%0A++++%7D%0A++%7D%0A&variables=%7B%22where%22%3A%7B%22url%22%3A%22' . $page_url . '%22%7D%7D&operationName=GetPageBookmarkCount';
			break;
	}

	// If we have no remote URL, then return false early
	if ( empty( $remote_url ) ) {
		return false;
	}

	// Get response from the api call
	$response = wp_remote_get( $remote_url, $args );

	// Continue only if response code is 200
	if ( 200 === wp_remote_retrieve_response_code( $response ) ) {
		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		// Get share value from response body
		switch ( $network_slug ) {

			case 'facebook':
				$reaction_count = isset( $body['engagement']['reaction_count'] ) ? $body['engagement']['reaction_count'] : false;
				$comment_count  = isset( $body['engagement']['comment_count'] ) ? $body['engagement']['comment_count'] : false;
				$share_count    = isset( $body['engagement']['share_count'] ) ? $body['engagement']['share_count'] : false;

				$share_count = ( false !== $reaction_count && false !== $comment_count && false !== $share_count ? (int) $reaction_count + (int) $comment_count + (int) $share_count : false );
				break;

			case 'pinterest':
				$body   = wp_remote_retrieve_body( $response );
				$start  = strpos( $body, '(' );
				$end    = strpos( $body, ')', $start + 1 );
				$length = $end - $start;
				$body   = json_decode( substr( $body, $start + 1, $length - 1 ), true );

				$share_count = ( isset( $body['count'] ) ? $body['count'] : false );
				break;
			case 'grow':
				$share_count = ( isset( $body['data'] ) && isset( $body['data']['page'] ) && isset( $body['data']['page']['bookmarkCount'] ) ? $body['data']['page']['bookmarkCount'] : false );
				break;
			default:
				if ( function_exists( 'dpsp_get_pro_url_network_share_count_response' ) ) {
					$share_count = dpsp_get_pro_url_network_share_count_response( $network_slug, $body, $response );
				} else {
					$share_count = ( isset( $body['count'] ) ? $body['count'] : false );
				}
				break;
		}

		return ( false !== $share_count ? (int) $share_count : $share_count );
	}

	// If we have a Facebook error, we need to possibly adjust the expires date
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( isset( $body['error']['code'] ) && 190 === $body['error']['code'] ) {
		$facebook_access_token = Mediavine\Grow\Settings::get_setting( 'dpsp_facebook_access_token' );

		// Adjust Facebook access token expiration if the token is marked as invalid by Facebook
		if ( ! empty( $facebook_access_token ) ) {
			$facebook_access_token['expires_in'] = time();

			update_option( 'dpsp_facebook_access_token', $facebook_access_token );
		}
	}

	return false;
}

/**
 * Pulls the share counts for all active networks for a certain post
 *
 * @param int $post_id The unique ID of the post.
 * @return array
 */
function dpsp_pull_post_share_counts( $post_id = 0 ) {
	if ( 0 === $post_id ) {
		return [];
	}
	$networks_class = Networks::get_instance();

	// Get active social networks
	$social_networks = dpsp_get_active_networks();

	// Get saved shares
	$networks_shares = Share_Counts::post_share_counts( $post_id );

	if ( empty( $networks_shares ) ) {
		$networks_shares = [];
	}

	// Set temporary variable
	$_networks_shares = [];

	// Pass through each active social networks and grab the share counts for the post
	foreach ( $social_networks as $network_slug ) {
		$network = $networks_class->get( $network_slug );
		if ( ! $network || ! $network->has_count() ) {
			continue;
		}

		$share_count = dpsp_get_post_network_share_count( $post_id, $network_slug );
		if ( false === $share_count ) {
			continue;
		}

		// Take into account Twitter old counts from NewShareCounts and OpenShareCount
		// The post meta "dpsp_cache_shares_twitter" was used for NewShareCounts
		// The post meta "dpsp_cache_shares_twitter_2" was used for OpenShareCount
		if ( 'twitter' === $network->get_slug() && isset( $networks_shares[ $network->get_slug() ] ) ) {
			$cached_old_twitter_shares = get_post_meta( $post_id, 'dpsp_cache_shares_twitter_2', true );

			// Add the Twitter shares to the cache if they do not exist
			if ( '' === $cached_old_twitter_shares ) {
				$cached_old_twitter_shares = absint( $networks_shares[ $network->get_slug() ] );
				update_post_meta( $post_id, 'dpsp_cache_shares_twitter_2', $cached_old_twitter_shares );

				// Delete the post meta for NewShareCounts
				delete_post_meta( $post_id, 'dpsp_cache_shares_twitter' );
			}

			// Add the current shares to the cached ones
			$share_count += $cached_old_twitter_shares;
		}

		// Add the share counts
		$_networks_shares[ $network->get_slug() ] = $share_count;
	} // End of social_networks loop

	/**
	 * Filter the social share counts as they are retrieved from the social networks.
	 *
	 * @param array $_networks_shares
	 * @param int $post_id
	 */
	$_networks_shares = apply_filters( 'dpsp_pull_post_share_counts_raw', $_networks_shares, $post_id );

	// Update the share counts only if they are bigger
	foreach ( $_networks_shares as $network_slug => $share_count ) {
		if ( isset( $networks_shares[ $network_slug ] ) ) {
			$networks_shares[ $network_slug ] = ( absint( $share_count ) > absint( $networks_shares[ $network_slug ] ) ? absint( $share_count ) : absint( $networks_shares[ $network_slug ] ) );
		} else {
			// If the share counts don't exist for the network, add them
			$networks_shares[ $network_slug ] = absint( $_networks_shares[ $network_slug ] );
		}
	}

	// Remove social counts for networks that are not required
	if ( ! empty( $networks_shares ) ) {
		foreach ( $networks_shares as $network_slug => $share_count ) {
			if ( ! in_array( $network_slug, $social_networks, true ) ) {
				unset( $networks_shares[ $network_slug ] );
			}
		}
	}

	return $networks_shares;
}
