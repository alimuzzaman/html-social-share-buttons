<?php // phpcs:disable Universal.Files.SeparateFunctionsFromOO.Mixed

/**
 * Widget class for social share follower widget.
 *
 * @package Simple_Social_Buttons
 * @since   2.0.5
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Social follow buttons widget.
 *
 * @since 2.0.5
 */
class Ssb_Follower_Widget extends WP_Widget {

	/**
	 * Transient Time.
	 *
	 * 43200 = 12 Hours.
	 *
	 * @var int
	 */
	private $cache_time = 43200;

	/**
	 * Register ssb widget with WordPress.
	 *
	 * @since 2.0.5
	 */
	public function __construct() {
		$widget_ops = array(
			'description' => __( 'Display Follow Button For your site', 'simple-social-buttons' ),
		);
		parent::__construct( 'ssb_widget', __( 'Social Follow Widget', 'simple-social-buttons' ), $widget_ops );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param array $args Widget arguments.
	 * @param array $instance Saved values from database.

	 * @see WP_Widget::widget()
	 * @access public
	 * @since 2.0.5
	 * @version 7.0.1
	 */
	public function widget( $args, $instance ) {
		$before_widget = isset( $args['before_widget'] ) ? $args['before_widget'] : '';
		$after_widget  = isset( $args['after_widget'] ) ? $args['after_widget'] : '';
		$before_title  = isset( $args['before_title'] ) ? $args['before_title'] : '';
		$after_title   = isset( $args['after_title'] ) ? $args['after_title'] : '';
		$display       = '1';

		$widget_title = apply_filters( 'ssb_follow_widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance );

		$show_facebook    = ! empty( $instance['show_facebook'] ) ? $instance['show_facebook'] : '';
		$show_twitter     = ! empty( $instance['show_twitter'] ) ? $instance['show_twitter'] : '';
		$show_google_plus = ! empty( $instance['show_google_plus'] ) ? $instance['show_google_plus'] : '';
		$show_youtube     = ! empty( $instance['show_youtube'] ) ? $instance['show_youtube'] : '';
		$show_pinterest   = ! empty( $instance['show_pinterest'] ) ? $instance['show_pinterest'] : '';
		$show_instagram   = ! empty( $instance['show_instagram'] ) ? $instance['show_instagram'] : '';
		$show_whatsapp    = ! empty( $instance['show_whatsapp'] ) ? $instance['show_whatsapp'] : '';

		$facebook_id            = ! empty( $instance['facebook_id'] ) ? $instance['facebook_id'] : '';
		$facebook_show_counter  = ! empty( $instance['facebook_show_counter'] ) ? $instance['facebook_show_counter'] : '';
		$facebook_text          = ! empty( $instance['facebook_text'] ) ? $instance['facebook_text'] : '';
		$facebook_access_token  = ! empty( $instance['facebook_access_token'] ) ? $instance['facebook_access_token'] : '';
		$facebook_default_count = ! empty( $instance['facebook_default_count'] ) ? $instance['facebook_default_count'] : '';

		$twitter_id            = ! empty( $instance['twitter'] ) ? $instance['twitter'] : '';
		$twitter_show_counter  = ! empty( $instance['twitter_show_counter'] ) ? $instance['twitter_show_counter'] : '';
		$twitter_text          = ! empty( $instance['twitter_text'] ) ? $instance['twitter_text'] : '';
		$twitter_api_key       = ! empty( $instance['twitter_api_key'] ) ? $instance['twitter_api_key'] : '';
		$twitter_secret_key    = ! empty( $instance['twitter_secret_key'] ) ? $instance['twitter_secret_key'] : '';
		$twitter_default_count = ! empty( $instance['twitter_default_count'] ) ? $instance['twitter_default_count'] : '';

		$youtube_id           = ! empty( $instance['youtube'] ) ? $instance['youtube'] : '';
		$youtube_show_counter = ! empty( $instance['youtube_show_counter'] ) ? $instance['youtube_show_counter'] : '';
		$youtube_text         = ! empty( $instance['youtube_text'] ) ? $instance['youtube_text'] : '';
		$youtube_type         = ! empty( $instance['youtube_type'] ) ? $instance['youtube_type'] : '';
		$youtube_api_key      = ! empty( $instance['youtube_api_key'] ) ? $instance['youtube_api_key'] : '';

		$pinterest_id           = ! empty( $instance['pinterest'] ) ? $instance['pinterest'] : '';
		$pinterest_show_counter = ! empty( $instance['pinterest_show_counter'] ) ? $instance['pinterest_show_counter'] : '';
		$pinterest_api_key      = ! empty( $instance['pinterest_api_key'] ) ? $instance['pinterest_api_key'] : '';
		$pinterest_text         = ! empty( $instance['pinterest_text'] ) ? $instance['pinterest_text'] : '';

		$instagram_id           = ! empty( $instance['instagram'] ) ? $instance['instagram'] : '';
		$instagram_show_counter = ! empty( $instance['instagram_show_counter'] ) ? $instance['instagram_show_counter'] : '';
		$instagram_text         = ! empty( $instance['instagram_text'] ) ? $instance['instagram_text'] : '';
		$instagram_access_token = ! empty( $instance['instagram_access_token'] ) ? $instance['instagram_access_token'] : '';

		$whatsapp      = ! empty( $instance['whatsapp'] ) ? $instance['whatsapp'] : '';
		$whatsapp_text = ! empty( $instance['whatsapp_text'] ) ? $instance['whatsapp_text'] : '';

		$fb_likes           = $this->get_facebook_likes_count( $facebook_id, $facebook_access_token, $facebook_show_counter );
		$twitter_follower   = $this->get_twitter_followers( $twitter_id, $twitter_api_key, $twitter_secret_key, $twitter_show_counter );
		$youtube_subscriber = $this->get_youtube_subscriber( $youtube_id, $youtube_api_key, $youtube_show_counter, $youtube_type );
		$pinterest_follower = $this->get_pinterest_followers( $pinterest_id, $pinterest_show_counter );
		$instagram_follower = $this->get_instagram_id_followers( $instagram_access_token, $instagram_show_counter );

		if ( ! empty( $facebook_default_count ) && ( '0' === $fb_likes || 0 === $fb_likes ) ) {
			$fb_likes = $facebook_default_count;
		}

		if ( ! empty( $twitter_default_count ) && ( '0' === $twitter_follower || 0 === $twitter_follower ) ) {
			$twitter_follower = $twitter_default_count;
		}
		include SSB_PLUGIN_DIR . '/inc/ssb-widget-front.php';
	}

	/**
	 * Back-end widget form.
	 *
	 * @param array $instance Previously saved values from database.
	 * @see WP_Widget::form()
	 *
	 * @access public
	 * @since 2.0.5
	 * @return mixed
	 */
	public function form( $instance ) {
		$display = '1';

		// First time run when instance create.
		if ( 0 === count( $instance ) ) {
			$instance['facebook_text']  = __( 'Follow us on Facebook', 'simple-social-buttons' );
			$instance['youtube_text']   = __( 'Subscribe us on Youtube', 'simple-social-buttons' );
			$instance['twitter_text']   = __( 'Follow us on Twitter', 'simple-social-buttons' );
			$instance['pinterest_text'] = __( 'Follow us on Pinterest', 'simple-social-buttons' );
			$instance['instagram_text'] = __( 'Follow us on Instagram', 'simple-social-buttons' );
			$instance['whatsapp_text']  = __( 'Contact us on WhatsApp', 'simple-social-buttons' );

		}

		$title = isset( $instance['title'] ) ? $instance['title'] : esc_html__( 'Follow Us', 'simple-social-buttons' );

		$show_facebook    = ! empty( $instance['show_facebook'] ) ? $instance['show_facebook'] : '';
		$show_twitter     = ! empty( $instance['show_twitter'] ) ? $instance['show_twitter'] : '';
		$show_google_plus = ! empty( $instance['show_google_plus'] ) ? $instance['show_google_plus'] : '';
		$show_youtube     = ! empty( $instance['show_youtube'] ) ? $instance['show_youtube'] : '';
		$show_pinterest   = ! empty( $instance['show_pinterest'] ) ? $instance['show_pinterest'] : '';
		$show_instagram   = ! empty( $instance['show_instagram'] ) ? $instance['show_instagram'] : '';
		$show_whatsapp    = ! empty( $instance['show_whatsapp'] ) ? $instance['show_whatsapp'] : '';

		$facebook_id            = ! empty( $instance['facebook_id'] ) ? $instance['facebook_id'] : '';
		$facebook_show_counter  = ! empty( $instance['facebook_show_counter'] ) ? $instance['facebook_show_counter'] : '';
		$facebook_text          = ! empty( $instance['facebook_text'] ) ? $instance['facebook_text'] : '';
		$facebook_app_id        = ! empty( $instance['facebook_app_id'] ) ? $instance['facebook_app_id'] : '';
		$facebook_security_key  = ! empty( $instance['facebook_security_key'] ) ? $instance['facebook_security_key'] : '';
		$facebook_access_token  = ! empty( $instance['facebook_access_token'] ) ? $instance['facebook_access_token'] : '';
		$facebook_default_count = ! empty( $instance['facebook_default_count'] ) ? $instance['facebook_default_count'] : '';

		$twitter               = ! empty( $instance['twitter'] ) ? $instance['twitter'] : '';
		$twitter_api_key       = ! empty( $instance['twitter_api_key'] ) ? $instance['twitter_api_key'] : '';
		$twitter_show_counter  = ! empty( $instance['twitter_show_counter'] ) ? $instance['twitter_show_counter'] : '';
		$twitter_text          = ! empty( $instance['twitter_text'] ) ? $instance['twitter_text'] : '';
		$twitter_secret_key    = ! empty( $instance['twitter_secret_key'] ) ? $instance['twitter_secret_key'] : '';
		$twitter_default_count = ! empty( $instance['twitter_default_count'] ) ? $instance['twitter_default_count'] : '';

		$youtube              = ! empty( $instance['youtube'] ) ? $instance['youtube'] : '';
		$youtube_text         = ! empty( $instance['youtube_text'] ) ? $instance['youtube_text'] : '';
		$youtube_type         = ! empty( $instance['youtube_type'] ) ? $instance['youtube_type'] : '';
		$youtube_show_counter = ! empty( $instance['youtube_show_counter'] ) ? $instance['youtube_show_counter'] : '';
		$youtube_api_key      = ! empty( $instance['youtube_api_key'] ) ? $instance['youtube_api_key'] : '';

		$pinterest              = ! empty( $instance['pinterest'] ) ? $instance['pinterest'] : '';
		$pinterest_text         = ! empty( $instance['pinterest_text'] ) ? $instance['pinterest_text'] : '';
		$pinterest_show_counter = ! empty( $instance['pinterest_show_counter'] ) ? $instance['pinterest_show_counter'] : '';
		$pinterest_api_key      = ! empty( $instance['pinterest_api_key'] ) ? $instance['pinterest_api_key'] : '';

		$instagram              = ! empty( $instance['instagram'] ) ? $instance['instagram'] : '';
		$instagram_user_id      = ! empty( $instance['instagram_user_id'] ) ? $instance['instagram_user_id'] : '';
		$instagram_text         = ! empty( $instance['instagram_text'] ) ? $instance['instagram_text'] : '';
		$instagram_show_counter = ! empty( $instance['instagram_show_counter'] ) ? $instance['instagram_show_counter'] : '';
		$instagram_access_token = ! empty( $instance['instagram_access_token'] ) ? $instance['instagram_access_token'] : '';

		// WhatsApp mobile number will store in $whatsapp.
		$whatsapp      = ! empty( $instance['whatsapp'] ) ? $instance['whatsapp'] : '';
		$whatsapp_text = ! empty( $instance['whatsapp_text'] ) ? $instance['whatsapp_text'] : '';

		include SSB_PLUGIN_DIR . '/inc/ssb-widget-fields.php';
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 * @see WP_Widget::update()
	 * @access public
	 * @since 2.0.5
	 * @version 7.0.1
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {

		// Delete scoped transients when user updates widget settings.
		$scope_keys = array(
			array( 'facebook_id', 'ssb_follow_facebook_counter_' ),
			array( 'twitter', 'ssb_follow_twitter_counter_' ),
			array( 'youtube', 'ssb_follow_youtube_counter_' ),
			array( 'pinterest', 'ssb_follow_pinterest_counter_' ),
			array( 'instagram_access_token', 'ssb_follow_instagram_counter_' ),
		);
		foreach ( $scope_keys as $scope ) {
			foreach ( array( $old_instance, $new_instance ) as $source ) {
				if ( isset( $source[ $scope[0] ] ) && '' !== $source[ $scope[0] ] ) {
					delete_transient( $scope[1] . md5( (string) wp_unslash( $source[ $scope[0] ] ) ) ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5
				}
			}
		}

		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? wp_strip_all_tags( $new_instance['title'] ) : '';

		$instance['show_facebook']    = ! empty( $new_instance['show_facebook'] ) ? wp_strip_all_tags( $new_instance['show_facebook'] ) : '0';
		$instance['show_twitter']     = ! empty( $new_instance['show_twitter'] ) ? wp_strip_all_tags( $new_instance['show_twitter'] ) : '0';
		$instance['show_google_plus'] = ! empty( $new_instance['show_google_plus'] ) ? wp_strip_all_tags( $new_instance['show_google_plus'] ) : '0';
		$instance['show_youtube']     = ! empty( $new_instance['show_youtube'] ) ? wp_strip_all_tags( $new_instance['show_youtube'] ) : '0';
		$instance['show_pinterest']   = ! empty( $new_instance['show_pinterest'] ) ? wp_strip_all_tags( $new_instance['show_pinterest'] ) : '0';
		$instance['show_instagram']   = ! empty( $new_instance['show_instagram'] ) ? wp_strip_all_tags( $new_instance['show_instagram'] ) : '0';
		$instance['show_whatsapp']    = ! empty( $new_instance['show_whatsapp'] ) ? wp_strip_all_tags( $new_instance['show_whatsapp'] ) : '0';

		$instance['facebook_id']            = isset( $new_instance['facebook_id'] ) ? sanitize_text_field( wp_unslash( $new_instance['facebook_id'] ) ) : '';
		$instance['facebook_app_id']        = isset( $new_instance['facebook_app_id'] ) ? sanitize_text_field( wp_unslash( $new_instance['facebook_app_id'] ) ) : '';
		$instance['facebook_security_key']  = isset( $new_instance['facebook_security_key'] ) ? sanitize_text_field( wp_unslash( $new_instance['facebook_security_key'] ) ) : '';
		$instance['facebook_access_token']  = isset( $new_instance['facebook_access_token'] ) ? sanitize_text_field( wp_unslash( $new_instance['facebook_access_token'] ) ) : '';
		$instance['facebook_show_counter']  = ( ! empty( $new_instance['facebook_show_counter'] ) ) ? wp_strip_all_tags( $new_instance['facebook_show_counter'] ) : '0';
		$instance['facebook_text']          = isset( $new_instance['facebook_text'] ) ? sanitize_text_field( wp_unslash( $new_instance['facebook_text'] ) ) : '';
		$instance['facebook_default_count'] = isset( $new_instance['facebook_default_count'] ) ? sanitize_text_field( wp_unslash( $new_instance['facebook_default_count'] ) ) : '';

		$instance['twitter']               = isset( $new_instance['twitter'] ) ? sanitize_text_field( wp_unslash( $new_instance['twitter'] ) ) : '';
		$instance['twitter_api_key']       = isset( $new_instance['twitter_api_key'] ) ? sanitize_text_field( wp_unslash( $new_instance['twitter_api_key'] ) ) : '';
		$instance['twitter_secret_key']    = isset( $new_instance['twitter_secret_key'] ) ? sanitize_text_field( wp_unslash( $new_instance['twitter_secret_key'] ) ) : '';
		$instance['twitter_show_counter']  = ( ! empty( $new_instance['twitter_show_counter'] ) ) ? wp_strip_all_tags( $new_instance['twitter_show_counter'] ) : '0';
		$instance['twitter_text']          = isset( $new_instance['twitter_text'] ) ? sanitize_text_field( wp_unslash( $new_instance['twitter_text'] ) ) : '';
		$instance['twitter_default_count'] = isset( $new_instance['twitter_default_count'] ) ? sanitize_text_field( wp_unslash( $new_instance['twitter_default_count'] ) ) : '';

		$instance['youtube']              = isset( $new_instance['youtube'] ) ? sanitize_text_field( wp_unslash( $new_instance['youtube'] ) ) : '';
		$instance['youtube_show_counter'] = ( ! empty( $new_instance['youtube_show_counter'] ) ) ? wp_strip_all_tags( $new_instance['youtube_show_counter'] ) : '0';
		$instance['youtube_text']         = isset( $new_instance['youtube_text'] ) ? sanitize_text_field( wp_unslash( $new_instance['youtube_text'] ) ) : '';
		$instance['youtube_type']         = isset( $new_instance['youtube_type'] ) ? sanitize_text_field( wp_unslash( $new_instance['youtube_type'] ) ) : '';
		$instance['youtube_api_key']      = isset( $new_instance['youtube_api_key'] ) ? sanitize_text_field( wp_unslash( $new_instance['youtube_api_key'] ) ) : '';

		$instance['pinterest']              = isset( $new_instance['pinterest'] ) ? sanitize_text_field( wp_unslash( $new_instance['pinterest'] ) ) : '';
		$instance['pinterest_show_counter'] = ( ! empty( $new_instance['pinterest_show_counter'] ) ) ? wp_strip_all_tags( $new_instance['pinterest_show_counter'] ) : '0';
		$instance['pinterest_text']         = isset( $new_instance['pinterest_text'] ) ? sanitize_text_field( wp_unslash( $new_instance['pinterest_text'] ) ) : '';
		$instance['pinterest_api_key']      = isset( $new_instance['pinterest_api_key'] ) ? sanitize_text_field( wp_unslash( $new_instance['pinterest_api_key'] ) ) : '';

		$instance['instagram']              = isset( $new_instance['instagram'] ) ? sanitize_text_field( wp_unslash( $new_instance['instagram'] ) ) : '';
		$instance['instagram_show_counter'] = ( ! empty( $new_instance['instagram_show_counter'] ) ) ? wp_strip_all_tags( $new_instance['instagram_show_counter'] ) : '0';
		$instance['instagram_text']         = isset( $new_instance['instagram_text'] ) ? sanitize_text_field( wp_unslash( $new_instance['instagram_text'] ) ) : '';
		$instance['instagram_access_token'] = isset( $new_instance['instagram_access_token'] ) ? sanitize_text_field( wp_unslash( $new_instance['instagram_access_token'] ) ) : '';

		$instance['whatsapp']      = isset( $new_instance['whatsapp'] ) ? sanitize_text_field( wp_unslash( $new_instance['whatsapp'] ) ) : '';
		$instance['whatsapp_text'] = isset( $new_instance['whatsapp_text'] ) ? sanitize_text_field( wp_unslash( $new_instance['whatsapp_text'] ) ) : '';

		return $instance;
	}

	/**
	 * Passing facebook and access token return facebook like counter.
	 *
	 * @param string $facebook_id   Facebook page ID.
	 * @param string $access_token  Facebook access token.
	 * @param bool   $show_counter  Whether to show counter.
	 *
	 * @access public
	 * @since 2.0.5
	 * @version 7.0.1
	 * @return int
	 */
	public function get_facebook_likes_count( $facebook_id, $access_token, $show_counter ) {

		if ( $show_counter ) {
			if ( '' === $facebook_id ) {
				return 0;
			}

			$transient_key = 'ssb_follow_facebook_counter_' . md5( (string) $facebook_id ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5

			if ( false === get_transient( $transient_key ) ) {
				$json_feed_url = "https://graph.facebook.com/$facebook_id/?fields=likes,fan_count&access_token=$access_token";

				$args      = array( 'httpversion' => '1.1' );
				$json_feed = wp_remote_get( $json_feed_url, $args );

				if ( is_wp_error( $json_feed ) || 200 !== wp_remote_retrieve_response_code( $json_feed ) ) {
					return 0;
				}
				$result  = json_decode( wp_remote_retrieve_body( $json_feed ) );
				$counter = ( isset( $result->fan_count ) ? $result->fan_count : 0 );
				$counter = $this->format_number( $counter );

				if ( ! empty( $counter ) ) {
					set_transient( $transient_key, $counter, $this->cache_time );
				}

				return $counter;
			} else {
				return get_transient( $transient_key );
			}
		}
	}

	/**
	 * Pass twitter user name and api key return twitter follower.
	 *
	 * @param string $twitter_handle Twitter handle.
	 * @param string $api_key       Twitter API key.
	 * @param string $secret_key    Twitter secret key.
	 * @param bool   $show_count    Whether to show count.
	 *
	 * @access public
	 * @since 2.0.5
	 * @version 7.0.1
	 * @return mixed|void
	 */
	public function get_twitter_followers( $twitter_handle, $api_key, $secret_key, $show_count ) {
		// Some variables.
		$consumer_key    = $api_key;
		$consumer_secret = $secret_key;
		$token_option    = 'ssb_follow_twitter_token_' . md5( (string) $api_key ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5
		$token           = get_option( $token_option );
		$transient_key   = 'ssb_follow_twitter_counter_' . md5( (string) $twitter_handle ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5

		// Get follower count from cache.
		$number_of_followers = get_transient( $transient_key );

		if ( $show_count ) {

			if ( '' === $twitter_handle ) {
				return 0;
			}
			// Cache version does not exist or expired.
			if ( false === get_transient( $transient_key ) ) {

				// Getting new auth bearer only if we don't have one.
				if ( ! $token ) {
					// Preparing credentials.
					$credentials = $consumer_key . ':' . $consumer_secret;
					$to_send     = base64_encode( $credentials ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode

					$args = array(
						'method'      => 'POST',
						'httpversion' => '1.1',
						'blocking'    => true,
						'headers'     => array(
							'Authorization' => 'Basic ' . $to_send,
							'Content-Type'  => 'application/x-www-form-urlencoded;charset=UTF-8',
						),
						'body'        => array( 'grant_type' => 'client_credentials' ),
					);

					$response = wp_remote_post( 'https://api.twitter.com/oauth2/token', $args );

					$keys = json_decode( wp_remote_retrieve_body( $response ) );

					if ( $keys && isset( $keys->access_token ) ) {
						// Saving token to wp_options table.
						update_option( $token_option, $keys->access_token );
						$token = $keys->access_token;
					}
				}

				// We have bearer token whether we obtained it from API or from options.
				$args = array(
					'httpversion' => '1.1',
					'blocking'    => true,
					'headers'     => array(
						'Authorization' => "Bearer $token",
					),
				);

				$api_url  = "https://api.twitter.com/1.1/users/show.json?screen_name=$twitter_handle";
				$response = wp_remote_get( $api_url, $args );
				if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
					return 0;
				}

				$followers = json_decode( wp_remote_retrieve_body( $response ) );
				$counter   = isset( $followers->followers_count ) ? $followers->followers_count : 0;
				$counter   = $this->format_number( $counter );
				// Cache for an hour.
				if ( ! empty( $counter ) ) {
					set_transient( $transient_key, $counter, $this->cache_time );
				}

				return $counter;
			}

			return get_transient( $transient_key );

		}
	}

	/**
	 * Passing youtube channel id and access token return the channel subscriber counter.
	 *
	 * @param string $channel_id   YouTube channel ID.
	 * @param string $api_key      YouTube API key.
	 * @param bool   $show_counter Whether to show counter.
	 * @param string $youtube_type YouTube type (user or channel).
	 *
	 * @access public
	 * @since 2.0.5
	 * @version 7.0.1
	 * @return int
	 */
	public function get_youtube_subscriber( $channel_id, $api_key, $show_counter, $youtube_type ) {

		if ( $show_counter ) {

			if ( '' === $channel_id ) {
				return 0;
			}

			$transient_key = 'ssb_follow_youtube_counter_' . md5( (string) $channel_id ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5

			if ( false === get_transient( $transient_key ) ) {

				// Check if username of channel id.
				$_type = 'user' === $youtube_type ? 'forUsername' : 'id';
				// Youtube Data API V3.
				$json_feed_url = 'https://www.googleapis.com/youtube/v3/channels?key=' . $api_key . '&part=contentDetails,statistics&' . $_type . '=' . $channel_id;
				$args          = array(
					'httpversion' => '1.1',
					'timeout'     => 15,
				);
				$json_feed     = wp_remote_get( $json_feed_url, $args );
				if ( is_wp_error( $json_feed ) || 200 !== wp_remote_retrieve_response_code( $json_feed ) ) {
					return 0;
				}
				$result  = json_decode( wp_remote_retrieve_body( $json_feed ) );
				$counter = isset( $result->items[0]->statistics->subscriberCount ) ? $result->items[0]->statistics->subscriberCount : 0;
				$counter = $this->format_number( $counter );

				if ( ! empty( $counter ) ) {

					set_transient( $transient_key, $counter, $this->cache_time );
				}

				return $counter;
			} else {

				return get_transient( $transient_key );
			}
		}
	}

	/**
	 * Passing pinterest access_token for getting pinterest follower counter.
	 *
	 * @param string $pinterest_id Pinterest ID.
	 * @param bool   $show_counter Whether to show counter.
	 *
	 * @access public
	 * @since 2.0.5
	 * @version 7.0.1
	 * @return int|string
	 */
	public function get_pinterest_followers( $pinterest_id, $show_counter ) {

		if ( $show_counter ) {
			if ( '' === $pinterest_id ) {
				return 0;
			}

			$transient_key = 'ssb_follow_pinterest_counter_' . md5( (string) $pinterest_id ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5

			if ( false === get_transient( $transient_key ) ) {

				$metas = get_meta_tags( "https://pinterest.com/$pinterest_id/" );

				$counter = isset( $metas['pinterestapp:followers'] ) ? intval( $metas['pinterestapp:followers'] ) : 0;
				$counter = $this->format_number( $counter );
				if ( ! empty( $counter ) ) {

					set_transient( $transient_key, $counter, $this->cache_time );
				}

				return $counter;
			} else {

				return get_transient( $transient_key );
			}
		}
	}

	/**
	 * Passing instagram access token for getting instagram follower.
	 *
	 * @param string $access_token Instagram access token.
	 * @param bool   $show_counter  Whether to show counter.
	 *
	 * @access public
	 * @since 2.0.10
	 * @version 7.0.1
	 * @return int|string
	 */
	public function get_instagram_id_followers( $access_token, $show_counter ) {

		if ( $show_counter ) {
			if ( '' === $access_token ) {
				return 0;
			}

			$transient_key = 'ssb_follow_instagram_counter_' . md5( (string) $access_token ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5

			if ( false === get_transient( $transient_key ) ) {
				$json_feed_url = 'https://api.instagram.com/v1/users/self/?access_token=' . $access_token;

				$args      = array(
					'httpversion' => '1.1',
					'timeout'     => 60,
				);
				$json_feed = wp_remote_get( $json_feed_url, $args );

				if ( is_wp_error( $json_feed ) || 200 !== wp_remote_retrieve_response_code( $json_feed ) ) {
					return 0;
				}

				$result  = json_decode( wp_remote_retrieve_body( $json_feed ), true );
				$counter = isset( $result['data']['counts']['followed_by'] ) ? $result['data']['counts']['followed_by'] : 0;
				$counter = $this->format_number( $counter );

				if ( ! empty( $counter ) ) {
					set_transient( $transient_key, $counter, $this->cache_time );
				}

				return $counter;
			} else {
				return get_transient( $transient_key );
			}
		}
	}

	/**
	 * Format the (int)number into easy readable format like 1K, 1M.
	 *
	 * @param int $value The number to format.
	 *
	 * @access public
	 * @since 2.0.5
	 * @return string
	 */
	public function format_number( $value ) {
		if ( $value > 999 && $value <= 999999 ) {
			$result = floor( $value / 1000 ) . 'k+';
			return $result;
		} elseif ( $value > 999999 ) {
			$result = floor( $value / 1000000 ) . 'm+';
			return $result;
		} else {
			$result = $value;
			return $result;
		}
	}
} // end class Ssb_Follower_Widget

/**
 * Register plugin widget.
 */
function ssb_register_widget() {
	register_widget( 'Ssb_Follower_Widget' );
}

add_action( 'widgets_init', 'ssb_register_widget' );
