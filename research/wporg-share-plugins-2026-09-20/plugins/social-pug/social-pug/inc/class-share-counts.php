<?php
namespace Mediavine\Grow;

use Exception;
use Mediavine\Grow\Block\Share;
use Mediavine\Grow\Share_Count_Url_Counts;
use Mediavine\Grow\Tools\Toolkit;
use Mediavine\Grow\Settings;
use Mediavine\Grow\Share_Count_URL;
use Social_Pug;

/**
 * Handles display and manipulating share counts for posts
 */
class Share_Counts extends \Social_Pug {

	/** @var string WordPress post meta key for the last updated timestamp. */
	public const LAST_UPDATED_META_KEY = 'dpsp_networks_shares_last_updated';

	/** @var string Key for Timeout Transient */
	private const REFRESH_TIMEOUT_KEY = 'mv_grow_refresh_timeout_';

	/** @var string Key for External Lockout Transient */
	private const EXTERNAL_LOCKOUT_KEY = 'mv_grow_external_lockout_';

	/** @var float|int Duration of timeout time in seconds */
	private const REFRESH_TIMEOUT_DURATION = HOUR_IN_SECONDS;

	/** @var float|int Duration of lockout time in seconds */
	private const EXTERNAL_LOCKOUT_DURATION = 60;

	/** @var null|Share_Counts */
	private static $instance = null;

	/**
	 * @return Share_Counts|null
	 */
	public static function get_instance() : ?Share_Counts {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Initialize class and set up hooks
	 */
	public function init() : void {
		add_action( 'wp_head', [ $this, 'check_share_counts' ] );
		add_action( 'dpsp_update_post_share_counts', __CLASS__ . '::update_top_shared_posts', 10, 2 );
		add_filter( 'dpsp_get_post_total_share_count', __CLASS__ . '::post_total_shares_minimum', 20, 3 );
		add_filter( 'dpsp_get_output_post_shares_counts', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
		add_filter( 'dpsp_get_output_total_share_count', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
	}

	/**
	 * Set the last updated timestamp to a value very far in the past so that the value will be updated when it is next checked.
	 */
	public static function invalidate_all() {
		$posts = self::get_all_posts_with_counts();
		foreach ( $posts as $post ) {
			\update_post_meta( $post->ID, self::LAST_UPDATED_META_KEY, 1 );
		}
	}

	/**
	 * Return an array with all posts that have share counts.
	 *
	 * @return \WP_Post[]
	 */
	public static function get_all_posts_with_counts() {
		$args = [
			'meta_query' => [ // @codingStandardsIgnoreLine
				[
					'key'     => self::LAST_UPDATED_META_KEY,
					'compare' => 'EXISTS',
				],
			],
			// WordPress.VIP.PostsPerPage.posts_per_page_nopaging —
			// Disabling pagination is prohibited in VIP context, do not set `nopaging` to `true` ever.
			// @todo Discussion: would a custom SQL call be faster than using WP_Query?
			'nopaging'   => true, // @codingStandardsIgnoreLine
		];

		return \get_posts( $args );
	}

	/**
	 * Determine if counts should be retrieved
	 *
	 * @param \WP_Post|null $current_post The current post to check counts for.
	 * @return bool;
	 */
	public static function should_get_count( ?\WP_Post $current_post ) {
		if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|facebookexternalhit|slurp|spider/i', wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ) ) { // @codingStandardsIgnoreLine
			return false;
		}

		if ( ! is_singular() ) {
			return false;
		}

		if ( is_null( $current_post ) ) {
			return false;
		}

		//Make sure this post has an assigned permalink before fetching share counts by checking the path of the url
		$permalink        = get_permalink( $current_post->ID );
		$parsed_permalink = parse_url( $permalink );

		//Pull the path from the permalink
		$permalink_path = $parsed_permalink['path'];

		// Check if the permalink has a query to indicate a post
		$has_post_query = ! empty( $parsed_permalink['query'] ) && preg_match( '/p=[0-9]+/', $parsed_permalink['query'] );

		if ( '/' === $permalink_path && ! $has_post_query ) {
			// If there is no path, Grow thinks this is the front page we are working with
			$front_page = get_option( 'page_on_front' );
			if ( $front_page !== $current_post->ID ) {
				// If we aren't actually on the front page, return early because we don't want to get the wrong counts
				return false;
			}
		}

		if ( is_attachment( $current_post->ID ) ) {
			return false;
		}

		if ( in_array( $current_post->post_status, [ 'future', 'draft', 'pending', 'trash', 'auto-draft' ], true ) ) {
			return false;
		}

		if ( self::has_external_lockout( $current_post ) ) {
			return false;
		}

		$expired = self::is_post_count_expired( $current_post );
		if ( ! $expired ) {
			return false;
		}
		self::set_external_lockout( $current_post );

		return true;
	}

	/**
	 * Get the refresh rate for a post that was created at a given time
	 *
	 * @param int $post_time Unix timestamp of post time
	 * @return int
	 */
	public static function get_refresh_rate( $post_time ) {
		// Set the refresh rate, depending on how many days
		// have pased since it was created
		if ( time() - $post_time <= 10 * DAY_IN_SECONDS ) {
			$refresh_rate = 2;
		} elseif ( time() - $post_time <= 20 * DAY_IN_SECONDS ) {
			$refresh_rate = 6;
		} else {
			$refresh_rate = 12;
		}

		/**
		 * Filter the share counts cache refresh rate.
		 *
		 * @param int $refresh_rate
		 * @param int $post_time
		 */
		return apply_filters( 'dpsp_post_share_counts_cache_refresh_rate', $refresh_rate, $post_time );
	}

	/**
	 * Determine if the share counts are expired for a given post
	 *
	 * @param \WP_Post $post_obj Post object to check if count is exipired for/
	 * @return bool
	 */
	public static function is_post_count_expired( \WP_Post $post_obj ) : bool {
		// Get the post's time
		$post_time = mysql2date( 'U', $post_obj->post_date, false );

		$refresh_rate = (int) self::get_refresh_rate( $post_time );

		// Get the last updated time for the share counts
		$shares_last_updated = (int) get_post_meta( $post_obj->ID, self::LAST_UPDATED_META_KEY, true );

		return $shares_last_updated < time() - $refresh_rate * HOUR_IN_SECONDS;
	}

	/**
	 * Turn the Passed Counts into rounded count strings
	 *
	 * @param int|array $counts Counts to round
	 * @param string    $location Location where these counts will be shown
	 * @param int       $precision Decimal points to round to
	 * @return array|string Rounded counts
	 */
	public static function round_counts( $counts, string $location = '', int $precision = 1 ) {

		if ( empty( $location ) ) {
			return $counts;
		}

		if ( empty( $counts ) ) {
			return $counts;
		}

		$location_settings = Settings::get_setting( 'dpsp_location_' . $location, [] );

		if ( ! isset( $location_settings['display']['count_round'] ) ) {
			return $counts;
		}

		/**
		 * Filter the precision at which the number should be rounded.
		 *
		 * @param int $round_precision
		 */
		$precision = apply_filters( 'dpsp_share_counts_round_precision', $precision );

		if ( is_array( $counts ) ) {
			return array_map(
				function( $count ) use ( $precision ) {
					return self::round_count_single( $count, $precision );
				}, $counts
			);
		}
		return self::round_count_single( $counts, $precision );
	}

	/**
	 * Round a single count to the given precision
	 *
	 * @param int|string $count Count that will be rounded
	 * @param int        $precision number of decimal points to round to.
	 * @return string|int Rounded count string if applicable
	 */
	private static function round_count_single( $count, int $precision ) {
		if ( ! is_int( $count ) ) {
			$count = (int) $count;
		}
		if ( $count / 1000000 >= 1 ) {
			$count = number_format( $count / 1000000, $precision ) . 'M';
		} elseif ( $count / 1000 >= 1 ) {
			$count = number_format( $count / 1000, $precision ) . 'K';
		}
		return $count;
	}


	/**
	 * Return whether or not counts are enabled for any active tool
	 *
	 * @since 2.15.0
	 * @return bool
	 */
	public static function are_counts_enabled() : bool {
		// Get all Tools
		$tools = Toolkit::get_instance()->get_all();

		foreach ( $tools as $tool ) {

			// Skip if tool not active
			if ( ! $tool->is_active() ) {
				continue;
			}

			$settings = $tool->get_settings();

			// Skip if no settings or no display section in the settings
			if ( ! ( $settings && isset( $settings['display'] ) ) ) {
				continue;
			}

			$has_count       = isset( $settings['display']['show_count'] ) && 'yes' === $settings['display']['show_count'];
			$has_count_total = isset( $settings['display']['show_count_total'] ) && 'yes' === $settings['display']['show_count_total'];

			// If we find one with counts the whole function returns true
			if ( $has_count || $has_count_total ) {
				return true;
			}
		}

		// If we haven't returned true yet we know it's false
		return false;
	}

	/**
	 * Check and perhaps update the share counts for the current post
	 *
	 * @return bool Whether the update was successful
	 */
	public function check_share_counts() : bool {
		$current_post = dpsp_get_current_post();

		if ( ! $current_post || ! self::should_get_count( $current_post ) ) {
			return false;
		}

		$share_counts = $this->pull_post_share_counts( $current_post );

		return $this->update_post_share_counts( $current_post, $share_counts );
	}

	/**
	 * Fetch social share counts for a post using local querying.
	 *
	 * @param \WP_Post $post Post to be used for retrieving counts.
	 * @return Share_Count_Url_Counts
	 */
	private function fetch_counts_local( \WP_Post $post ) : Share_Count_Url_Counts {
		if ( ! is_numeric( $post->ID ) || $post->ID < 0 ) {
			return new Share_Count_Url_Counts( [] );
		}

		try {
			$counts = dpsp_pull_post_share_counts( $post->ID );
		} catch ( Exception $e ) {
			$counts = [];
		}

		$result = new Share_Count_Url_Counts( $counts );
		return $result;
	}

	/**
	 * Load the share counts
	 *
	 * @param int $post_id Id to get counts for
	 *
	 * @return array
	 */
	public static function post_share_counts( int $post_id ) : array {
		$meta_shares = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_networks_shares', true );
		// The below is likely no longer needed.
		// if ( is_string( $meta_shares ) && strpos( $meta_shares, '1:' ) === 0 ) {
		// 	$meta_shares[0] = 's';
		// 	$meta_shares = dpsp_maybe_unserialize( $meta_shares );
		// }

		$shares      = is_array( $meta_shares ) ? $meta_shares : [];
		return apply_filters( 'dpsp_get_post_share_counts', $shares, $post_id );
	}

	/**
	 * Load the share counts
	 *
	 * @param int    $post_id Id to get counts for
	 * @param string $location Location where total shares are being gotten
	 *
	 * @return int Total Share Count
	 */
	public static function post_total_share_counts( int $post_id = 0, string $location = '' ) : int {
		if ( 0 === $post_id ) {
			$post_obj = dpsp_get_current_post();
			if ( ! $post_obj ) {
				return 0;
			}
			$post_id = $post_obj->ID;
		}

		$total_share_meta = dpsp_maybe_convert_post_meta_to_json( $post_id, 'dpsp_networks_shares_total', true );
		$total_shares     = $total_share_meta ? $total_share_meta : 0;

		/**
		 * Filter total shares before returning them.
		 *
		 * @param int $total_shares
		 * @param int $post_id
		 * @param string $location
		 */
		return apply_filters( 'dpsp_get_post_total_share_count', (int) $total_shares, $post_id, $location );
	}

	/**
	 * Retrieve share counts for a given post
	 *
	 * @param \WP_Post $current_post The post to get share for
	 *
	 * @return Share_Count_Url_Counts
	 */
	public function pull_post_share_counts( \WP_Post $current_post ) : Share_Count_Url_Counts {
		$combined_shares = $this->fetch_counts_local( $current_post );

		return $combined_shares;
	}

	/**
	 * Refresh share counts for a given post
	 *
	 * @param \WP_Post $current_post The post to get share for
	 *
	 * @return Share_Count_Url_Counts|null
	 */
	public function refresh_post_share_counts( \WP_Post $current_post ) : ?Share_Count_Url_Counts {
		if ( self::has_refresh_timeout( $current_post ) ) {
			return null;
		}

		$share_counts = $this->pull_post_share_counts( $current_post );

		self::set_refresh_timeout( $current_post );

		$result = $share_counts;
		return $result;
	}

	/**
	 * Check to see if the given post has a refresh timeout
	 *
	 * @param \WP_Post $post Post to check timeout for
	 *
	 * @return bool If the post has a timeout set
	 */
	public static function has_refresh_timeout( \WP_Post $post ) : bool {
		$timeout = self::get_refresh_timeout( $post );
		return ( null !== $timeout && 0 < $timeout );
	}

	/**
	 * Check to see if the given post is locked out from external requests
	 *
	 * @param \WP_Post $post Post to check lockout for
	 *
	 * @return bool If the post is locked out
	 */
	public static function has_external_lockout( \WP_Post $post ) : bool {
		return ! ( ( time() - self::get_external_lockout( $post ) ) > 60 );
	}

	/**
	 * Was this post published recently?
	 *
	 * @param \WP_Post $post Target post used to determine recently-posted status.
	 * @return bool
	 */
	private function is_recently_published( \WP_Post $post ) : bool {
		$result = mysql2date( 'U', $post->post_date, false ) <= 10 * DAY_IN_SECONDS;
		return $result;
	}

	/**
	 * Get the expiration of the refresh timeout for the given post
	 *
	 * @param \WP_Post $post Post to check timeout for
	 *
	 * @return int|null If the post has a timeout set
	 */
	public static function get_refresh_timeout( \WP_Post $post ) : ?int {
		$transient = get_transient( self::REFRESH_TIMEOUT_KEY . $post->ID );

		return false !== $transient ? $transient : null;
	}

	/**
	 * Get the lockout value
	 *
	 * @param \WP_Post $post Post to check lockout for
	 *
	 * @return int The value of the lockout
	 */
	public static function get_external_lockout( \WP_Post $post ) : int {
		 $transient = get_transient( self::EXTERNAL_LOCKOUT_KEY . $post->ID );
		 return $transient ?? 0;
	}

	/**
	 * Set the refresh timeout transient
	 * @param \WP_Post $post Post to set the timeout for
	 *
	 * @return bool Success of timeout transient being set
	 */
	public static function set_refresh_timeout( \WP_Post $post ) : bool {
		return set_transient( self::REFRESH_TIMEOUT_KEY . $post->ID, time() + self::REFRESH_TIMEOUT_DURATION, self::REFRESH_TIMEOUT_DURATION );
	}

	/**
	 * Set the external lockout time for the post
	 * @param \WP_Post $post Post to set the lockout for
	 *
	 * @return bool Success of lockout transient being set
	 */
	public static function set_external_lockout( \WP_Post $post ) : bool {
		return set_transient( self::EXTERNAL_LOCKOUT_KEY . $post->ID, time(), self::EXTERNAL_LOCKOUT_DURATION );
	}


	/**
	 * Build the Share Count URL objects and chunk into groups of 10
	 *
	 * @param string[] $urls URLs to split into groups
	 * @param bool     $is_recently_published Whether the post was recently published
	 *
	 * @return array
	 */
	private function build_url_groups( array $urls, bool $is_recently_published ) : array {
		$url_objects = array_map(function( $url ) use ( $is_recently_published ) {
			return new Share_Count_URL( $url, $is_recently_published );
		}, $urls);
		return array_chunk( $url_objects, 10 );
	}

	/**
	 * Update the share counts in the database with new counts
	 *
	 * @param \WP_Post                               $current_post The post we are updating counts for
	 * @param \Mediavine\Grow\Share_Count_Url_Counts $share_counts the new total share counts for the post
	 *
	 * @return bool Whether the update was successful
	 */
	public function update_post_share_counts( \WP_Post $current_post, Share_Count_Url_Counts $share_counts ) : bool {
		$current_shares = self::post_share_counts( $current_post->ID );
		$updated_shares = $share_counts->get_counts();

		$updated_shares = $this->compare_counts( $updated_shares, $current_shares );

		$share_count_total = $share_counts->get_count_total();

		// Update post meta with all shares
		// For backwards compatibilty pre 2.21.0 / 1.33.2
		$shares_updated = update_post_meta( $current_post->ID, 'dpsp_networks_shares', $updated_shares );

		// Update JSON (which is what is used post 2.21.0 / 1.33.2 )
		$shares_updated_json = update_post_meta( $current_post->ID, 'dpsp_networks_shares_json', json_encode( $updated_shares, JSON_UNESCAPED_UNICODE ) );

		// Update post meta with total share counts
		$totals_updated = update_post_meta( $current_post->ID, 'dpsp_networks_shares_total', $share_count_total );

		// Update post meta with last updated timestamp
		$updated_time_updated = update_post_meta( $current_post->ID, 'dpsp_networks_shares_last_updated', time() );

		/**
		 * Do extra actions after updating the post's share counts
		 *
		 * @param int $post_id - the id of the post to save the shares
		 * @param array $shares - an array with the network shares and total shares
		 *
		 */
		do_action( 'dpsp_update_post_share_counts', $current_post->ID, $updated_shares );

		return $shares_updated && $totals_updated && $updated_time_updated;
	}

	/**
	 * Compare counts from two collections, returning the higher counts from each.
	 *
	 * @param array<string, int> $a The first count to check against
	 * @param array<string, int> $b The second count to be added to the first
	 *
	 * @return array<string, int>
	 */
	private function compare_counts( array $a, array $b ) : array {
		$total = [];
		$a     = $a + $b;
		foreach ( $a as $key => $value ) {
			$b_value       = $b[ $key ] ?? 0;
			$total[ $key ] = $value > $b_value ? $value : $b_value;
		}
		return $total;
	}

	/**
	 * Check to see if the passed post with the passed share counts belonds in the top shared posts list
	 *
	 * @param int   $post_id Id for the post to update shares for
	 * @param array $updated_shares New Share counts
	 *
	 * @return bool Whether the update was successful
	 */
	public static function update_top_shared_posts( int $post_id, array $updated_shares ) : bool {
		// Get the post's post type
		$post_type = get_post_type( $post_id );

		if ( ! $post_type ) {
			return false;
		}
		// Get current saved top shared posts
		$top_posts = Settings::get_setting( 'dpsp_top_shared_posts', [] );

		// Decode the top posts into an array
		if ( ! empty( $top_posts ) && ! is_array( $top_posts ) ) {
			$top_posts = json_decode( $top_posts, ARRAY_A );
		}

		$top_posts[ $post_type ][ $post_id ] = array_sum( $updated_shares );

		/**
		 * Filter top shared posts before saving in the db.
		 *
		 * @param array $top_posts
		 * @param int $post_id
		 *
		 * @return bool Whether the meta was updated or not
		 */
		$top_posts = apply_filters( 'dpsp_top_shared_posts_raw', $top_posts, $post_id );

		// Filter top posts array
		if ( ! empty( $top_posts ) ) {
			foreach ( $top_posts as $post_type => $post_list ) {
				if ( ! empty( $top_posts[ $post_type ] ) ) {
					// Sort descending
					arsort( $top_posts[ $post_type ] );

					// Get only first ten
					$top_posts[ $post_type ] = array_slice( $top_posts[ $post_type ], 0, 10, true );
				}
			}
		}

		// Update top posts
		return update_option( 'dpsp_top_shared_posts', json_encode( $top_posts ) );
	}

	/**
	 * Checks to see if total shares are at least as high as the minimum count
	 * needed. Return null if the minimum shares is greater than the total.
	 *
	 * @param int|string $total_shares - the total shares of the post for all active networks
	 * @param int        $post_id - the ID of the post
	 * @param string     $location - the location where the buttons are displayed
	 * @return string|int|null
	 */
	public static function post_total_shares_minimum( $total_shares, int $post_id, string $location = '' ) {
		if ( filter_var( $total_shares, FILTER_VALIDATE_INT ) === false ) {
			return $total_shares;
		}

		if ( empty( $location ) ) {
			return $total_shares;
		}

		$location_settings = Settings::get_setting( 'dpsp_location_' . $location );

		if ( empty( $location_settings['display']['minimum_count'] ) ) {
			return $total_shares;
		}

		if ( ctype_digit( (string) $location_settings['display']['minimum_count'] ) && intval( $location_settings['display']['minimum_count'] ) > intval( $total_shares ) ) {
			$total_shares = 0;
		}

		return $total_shares;
	}
}
