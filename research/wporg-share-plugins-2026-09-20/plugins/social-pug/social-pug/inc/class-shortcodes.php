<?php
namespace Mediavine\Grow;

use Mediavine\Grow\Share_Counts;

/**
 * Class file that wraps up all shortcodes.
 *
 * The use of the class was not necessary, but I find it cleaner this way,
 * instead of using functions and/or separating functions in multiple files.
 */
class Shortcodes {

	private const CLICK_TO_TWEET_DEFAULT_STYLE = 1;

	/** @var string[] Shortcodes to be registered. */
	private static $shortcodes = [
		'socialpug_share'  => __CLASS__ . '::share_buttons',
		'socialpug_follow' => __CLASS__ . '::follow_buttons',
		'socialpug_tweet'  => __CLASS__ . '::click_to_tweet',
		'mv_grow_share'    => __CLASS__ . '::share_buttons',
		'mv_grow_follow'   => __CLASS__ . '::follow_buttons',
		'mv_grow_tweet'    => __CLASS__ . '::click_to_tweet',
		'hubbub_share'    => __CLASS__ . '::share_buttons',
		'hubbub_follow'   => __CLASS__ . '::follow_buttons',
		'hubbub_tweet'    => __CLASS__ . '::click_to_tweet',
		'hubbub_save_this' => __CLASS__ . '::email_save_this',
	];

	/**
	 * Call WordPress built-in to register shortcodes.
	 */
	public static function register_shortcodes() {
		foreach ( self::$shortcodes as $shortcode_slug => $shortcode_callback ) {
			add_shortcode( $shortcode_slug, $shortcode_callback );
		}
	}

	/**
	 * Display the share buttons.
	 *
	 * The buttons default to the settings for the Content location,
	 * but they can be overwritten by the custom attributes.
	 *
	 * @param array $atts
	 * @param null $content
	 * @return string
	 */
	public static function share_buttons( $atts = [], $content = null ) {
		// Define supported attributes
		// If "overwrite" is "yes" no settings from the Content location will be taken into account
		$args = shortcode_atts(
			[
				'id'                       => '',
				'networks'                 => '',
				'networks_labels'          => '',
				'button_style'             => '',
				'shape'                    => '',
				'size'                     => '',
				'columns'                  => '',
				'show_labels'              => '',
				'show_labels_mobile'	   => '',
				'button_spacing'           => '',
				'show_count'               => '',
				'show_total_count'         => '',
				'total_count_position'     => '',
				'count_round'              => '',
				'minimum_count'            => '',
				'minimum_individual_count' => '',
				'show_mobile'              => '',
				'overwrite'                => 'no',
				'url'                      => '',
				'description'              => '',
			],
			$atts
		);

		add_filter( 'mv_grow_scripts_should_enqueue', '__return_true' );

		// Modify settings based on the attributes
		// Location settings for the Content location
		$settings = dpsp_get_location_settings( 'content' );

		// If Overwrite is set to "yes" strip everything
		if ( 'yes' === $args['overwrite'] ) {
			$settings = [];
		}

		// Set networks and network labels
		if ( ! empty( $args['networks'] ) ) {
			$networks        = array_map( 'trim', explode( ',', $args['networks'] ) );
			$networks_labels = ( ! empty( $args['networks_labels'] ) ? array_map( 'trim', explode( ',', $args['networks_labels'] ) ) : [] );

			// Set the array with the networks slug and labels
			foreach ( $networks as $key => $network_slug ) {
				$networks[ $network_slug ]['label'] = ( isset( $networks_labels[ $key ] ) ? $networks_labels[ $key ] : dpsp_get_network_name( $network_slug ) );
				unset( $networks[ $key ] );
			}

			$settings['networks'] = $networks;
		}

		// Set button style
		if ( ! empty( $args['button_style'] ) ) {
			$settings['button_style'] = $args['button_style'];
		}
		// If no style is set, set the default to the first style
		if ( ! isset( $settings['button_style'] ) ) {
			$settings['button_style'] = 1;
		}

		// Set display option
		if ( ! empty( $args['shape'] ) ) {
			$settings['display']['shape'] = $args['shape'];
		}

		if ( ! empty( $args['size'] ) ) {
			$settings['display']['size'] = $args['size'];
		}

		if ( ! empty( $args['columns'] ) ) {
			$settings['display']['column_count'] = sanitize_html_class( $args['columns'] );
		}

		if ( ! empty( $args['show_labels'] ) ) {
			$settings['display']['show_labels'] = $args['show_labels'];
		}

		if ( ! empty( $args['show_labels_mobile'] ) ) {
			$settings['display']['show_labels_mobile'] = $args['show_labels_mobile'];
		}

		if ( ! empty( $args['button_spacing'] ) ) {
			$settings['display']['spacing'] = $args['button_spacing'];
		}

		if ( ! empty( $args['show_count'] ) ) {
			$settings['display']['show_count'] = $args['show_count'];
		}

		if ( ! empty( $args['show_total_count'] ) ) {
			$settings['display']['show_count_total'] = $args['show_total_count'];
		}

		if ( ! empty( $args['total_count_position'] ) ) {
			$settings['display']['total_count_position'] = $args['total_count_position'];
		}

		if ( ! empty( $args['count_round'] ) ) {
			$settings['display']['count_round'] = $args['count_round'];
		}

		if ( ! empty( $args['minimum_count'] ) ) {
			$settings['display']['minimum_count'] = $args['minimum_count'];
		}

		if ( ! empty( $args['minimum_individual_count'] ) ) {
			$settings['display']['minimum_individual_count'] = $args['minimum_individual_count'];
		}

		if ( ! empty( $args['show_mobile'] ) ) {
			$settings['display']['show_mobile'] = $args['show_mobile'];
		}

		$data = [];

		if ( ! empty( $args['url'] ) ) {
			$data['shortcode_url'] = $args['url'];
		}

		if ( ! empty( $args['description'] ) ) {
			$data['shortcode_desc'] = $args['description'];
		}

		// Remove all display settings that have "no" as a value
		foreach ( $settings['display'] as $key => $value ) {
			if ( 'no' === $value ) {
				unset( $settings['display'][ $key ] );
			}
		}

		// Round counts cannot be changed direcly because they are too dependend
		// on the location settings saved in the database.
		// For the moment removing the filters and adding them again is the only solution
		if ( ! isset( $settings['display']['count_round'] ) ) {
			remove_filter( 'dpsp_get_output_post_shares_counts', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
			remove_filter( 'dpsp_get_output_total_share_count', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
		}

		/*
		 * Start outputing
		 *
		 */
		$output = '';

		// Classes for the wrapper
		$wrapper_classes   = [ 'dpsp-shortcode-wrapper' ];
		$wrapper_classes[] = ( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . sanitize_html_class( $settings['display']['shape'] ) : '' );
		$wrapper_classes[] = ( isset( $settings['display']['size'] ) ? 'dpsp-size-' . sanitize_html_class( $settings['display']['size'] ) : 'dpsp-size-medium' );
		$wrapper_classes[] = ( isset( $settings['display']['column_count'] ) ? 'dpsp-column-' . sanitize_html_class( $settings['display']['column_count'] ) : '' );
		$wrapper_classes[] = ( isset( $settings['display']['spacing'] ) ? 'dpsp-has-spacing' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_labels'] ) || isset( $settings['display']['show_count'] ) ? '' : 'dpsp-no-labels' );
		$wrapper_classes[] = ( isset( $settings['display']['show_labels_mobile'] ) ? '' : 'dpsp-no-labels-mobile' );
		$wrapper_classes[] = ( isset( $settings['display']['show_count'] ) ? 'dpsp-has-buttons-count' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_mobile'] ) ? 'dpsp-show-on-mobile' : 'dpsp-hide-on-mobile' );

		// Button total share counts
		$minimum_count    = ( ! empty( $settings['display']['minimum_count'] ) ? (int) $settings['display']['minimum_count'] : 0 );
		$show_total_count = ( $minimum_count <= (int) Share_Counts::post_total_share_counts() && ! empty( $settings['display']['show_count_total'] ) ? true : false );

		$wrapper_classes[] = ( $show_total_count ? 'dpsp-show-total-share-count' : '' );
		$wrapper_classes[] = ( $show_total_count ? ( ! empty( $settings['display']['total_count_position'] ) ? 'dpsp-show-total-share-count-' . sanitize_html_class( $settings['display']['total_count_position'] ) : 'dpsp-show-total-share-count-before' ) : '' );

		// Button styles
		$wrapper_classes[] = ( isset( $settings['button_style'] ) ? 'dpsp-button-style-' . sanitize_html_class( $settings['button_style'] ) : '' );

		$wrapper_classes = implode( ' ', array_filter( $wrapper_classes ) );

		// Output total share counts
		if ( $show_total_count ) {
			$output .= dpsp_get_output_total_share_count( 'content' );
		}

		// Gets the social network buttons
		if ( isset( $settings['networks'] ) ) {
			self::output_svg_data( $settings['networks'] );
		}
		$output .= dpsp_get_output_network_buttons( $settings, 'share', 'content', $data );

		// The `id` attribute is author-supplied, so restrict it to characters that are
		// valid in an HTML id and cannot terminate the attribute. See #2323.
		$wrapper_id = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $args['id'] );

		$output = '<div ' . ( '' !== $wrapper_id ? 'id="' . esc_attr( $wrapper_id ) . '"' : '' ) . ' class="' . esc_attr( $wrapper_classes ) . '">' . $output . '</div>';

		// Add back the filters
		if ( ! isset( $settings['display']['count_round'] ) ) {
			add_filter( 'dpsp_get_output_post_shares_counts', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
			add_filter( 'dpsp_get_output_total_share_count', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
		}

		return $output;

	}


	/*
	 * Display the follow buttons
	 * The buttons default to the settings for the Follow Widget location,
	 * but they can be overwritten by the custom attributes
	 *
	 */

	public static function output_svg_data( $networks ) {
		add_filter(
			'mv_grow_frontend_data',
			function ( $data ) use ( $networks ) {
				$svg_arr           = isset( $data['buttonSVG'] ) ? $data['buttonSVG'] : [];
				$data['buttonSVG'] = array_merge( $svg_arr, dpsp_get_svg_data_for_networks( $networks ) );

				return $data;
			}
		);
	}


	/*
	 * Display the Click to Tweet box
	 *
	 */

	public static function follow_buttons( $atts = [], $content = null ) {

		/*
		 * Define supported attributes
		 *
		 * If "overwrite" is "yes" no settings from the Follow Widget location will be taken into account
		 *
		 */
		$args = shortcode_atts(
			[
				'id'              => '',
				'networks'        => '',
				'networks_labels' => '',
				'button_style'    => '',
				'shape'           => '',
				'size'            => '',
				'alignment'       => '',
				'columns'         => '',
				'show_labels'     => '',
				'show_labels_mobile' => '',
				'button_spacing'  => '',
				'overwrite'       => 'no',
			],
			$atts
		);

		add_filter( 'mv_grow_scripts_should_enqueue', '__return_true' );

		/*
		 * Modify settings based on the attributes
		 *
		 */

		// Location settings for the Content location
		$settings = dpsp_get_location_settings( 'follow_widget' );

		// If Overwrite is set to "yes" strip everything
		if ( 'yes' === $args['overwrite'] ) {
			$settings = [];
		}

		// Set networks and network labels
		if ( ! empty( $args['networks'] ) ) {

			$networks        = array_map( 'trim', explode( ',', $args['networks'] ) );
			$networks_labels = ( ! empty( $args['networks_labels'] ) ? array_map( 'trim', explode( ',', $args['networks_labels'] ) ) : [] );

			// Set the array with the networks slug and labels
			foreach ( $networks as $key => $network_slug ) {
				$networks[ $network_slug ]['label'] = ( isset( $networks_labels[ $key ] ) ? $networks_labels[ $key ] : dpsp_get_network_name( $network_slug ) );
				unset( $networks[ $key ] );
			}

			$settings['networks'] = $networks;

		}

		// Set button style
		if ( ! empty( $args['button_style'] ) ) {
			$settings['button_style'] = $args['button_style'];
		}
		// If no style is set, set the default to the first style
		if ( ! isset( $settings['button_style'] ) ) {
			$settings['button_style'] = 1;
		}

		// Set display option
		if ( ! empty( $args['shape'] ) ) {
			$settings['display']['shape'] = $args['shape'];
		}

		if ( ! empty( $args['size'] ) ) {
			$settings['display']['size'] = $args['size'];
		}

		if ( ! empty( $args['alignment'] ) ) {
			$settings['display']['alignment'] = $args['alignment'];
		}

		if ( ! empty( $args['columns'] ) ) {
			$settings['display']['column_count'] = sanitize_html_class( $args['columns'] );
		}

		if ( ! empty( $args['show_labels'] ) ) {
			$settings['display']['show_labels'] = $args['show_labels'];
		}

		if ( ! empty( $args['show_labels_mobile'] ) ) {
			$settings['display']['show_labels_mobile'] = $args['show_labels_mobile'];
		}

		if ( ! empty( $args['button_spacing'] ) ) {
			$settings['display']['spacing'] = $args['button_spacing'];
		}

		// Remove all display settings that have "no" as a value
		foreach ( $settings['display'] as $key => $value ) {
			if ( 'no' === $value ) {
				unset( $settings['display'][ $key ] );
			}
		}

		/*
		 * Start outputing
		 *
		 */
		$output = '';

		// Classes for the wrapper
		$wrapper_classes   = [ 'dpsp-shortcode-follow-wrapper' ];
		$wrapper_classes[] = ( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . $settings['display']['shape'] : '' );
		$wrapper_classes[] = ( isset( $settings['display']['size'] ) ? 'dpsp-size-' . $settings['display']['size'] : 'dpsp-size-medium' );
		$wrapper_classes[] = ( isset( $settings['display']['alignment'] ) ? 'dpsp-follow-align-' . $settings['display']['alignment'] : 'dpsp-follow-align-left' );
		$wrapper_classes[] = ( isset( $settings['display']['column_count'] ) ? 'dpsp-column-' . sanitize_html_class( $settings['display']['column_count'] ) : '' );
		$wrapper_classes[] = ( isset( $settings['display']['spacing'] ) ? 'dpsp-has-spacing' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_labels'] ) || isset( $settings['display']['show_count'] ) ? '' : 'dpsp-no-labels' );
		$wrapper_classes[] = ( isset( $settings['display']['show_labels_mobile'] ) ? '' : 'dpsp-no-labels-mobile' );
		$wrapper_classes[] = ( isset( $settings['display']['show_count'] ) ? 'dpsp-has-buttons-count' : '' );
		$wrapper_classes[] = ( isset( $settings['display']['show_mobile'] ) ? 'dpsp-show-on-mobile' : 'dpsp-hide-on-mobile' );

		// Button styles
		$wrapper_classes[] = ( isset( $settings['button_style'] ) ? 'dpsp-button-style-' . $settings['button_style'] : '' );

		$wrapper_classes = implode( ' ', array_filter( $wrapper_classes ) );

		// Gets the social network buttons
		if ( isset( $settings['networks'] ) ) {
			self::output_svg_data( $settings['networks'] );
		}

		$data = [
			'args'            => $args,
			'settings'        => $settings,
			'wrapper_classes' => $wrapper_classes,
		];

		return \Mediavine\Grow\View_Loader::get_view( '/inc/views/follow-shortcode.php', $data );

	}

	/**
	 * Handle rendering of the Click-to-Tweet widget.
	 *
	 * @param array $atts Shortcode attributes.
	 * @return false|string|void
	 */
	public static function click_to_tweet( $atts = [] ) {

		// Supported attributes
		$args = shortcode_atts(
			[
				'tweet'           => '',
				'display_tweet'   => '',
				'style'           => '',
				'remove_url'      => null,
				'remove_username' => null,
				'author_avatar'   => '',
			],
			$atts
		);

		add_filter( 'mv_grow_scripts_should_enqueue', '__return_true' );

		// Exit if there is no tweet
		if ( empty( $args['tweet'] ) ) {
			return;
		}

		// Get settings
		$settings = \Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );

		// Set tweet
		$tweet = $args['tweet'];

		// Set display tweet
		$display_tweet = ( ! empty( $args['display_tweet'] ) ? $args['display_tweet'] : $args['tweet'] );

		// Check tweet length and slice it if it's too long
		if ( strlen( $tweet ) > apply_filters( 'dpsp_tweet_maximum_count', 280 ) ) {
			$tweet = substr( $tweet, 0, ( apply_filters( 'dpsp_tweet_maximum_count', 280 ) - strlen( $tweet ) - 10 ) ) . '... ';
		}

		// Get the share link
		$network_share_link = dpsp_get_network_share_link( 'twitter', ( 'yes' === $args['remove_url'] ? '' : null ), $tweet, '' );

		// Handle @via
		if ( ! empty( $settings['twitter_username'] ) ) {

			$twitter_username = str_replace( '@', '', trim( $settings['twitter_username'] ) );

			if ( 'yes' === $args['remove_username'] ) {
				$network_share_link = remove_query_arg( 'via', $network_share_link );

			} else {

				if ( strpos( $network_share_link, 'via=' ) === false ) {
					$network_share_link = add_query_arg( [ 'via' => $twitter_username ], $network_share_link );
				}
			}
		}

		// Get Click to Tweet style class
		$style_class = self::click_to_tweet_style( $args, $settings );

		// Check to see if there's a style with author details
		$avatar = false;

		if ( ! empty( $args['author_avatar'] ) && 'yes' === $args['author_avatar'] ) {

			// Get post
			global $dpsp_cache_wp_post;

			// Get the post author avatar
			if ( $dpsp_cache_wp_post && ! empty( $dpsp_cache_wp_post->post_author ) ) {

				$avatar = get_avatar( $dpsp_cache_wp_post->post_author, apply_filters( 'dpsp_click_to_tweet_avatar', 85 ) );

				if ( $avatar ) {
					$style_class .= ' dpsp-has-avatar';
				}
			}
		}

		// Add cta position to the style class
		$style_class .= ' dpsp-click-to-tweet-cta-' . ( ! empty( $settings['ctt_link_position'] ) ? esc_attr( $settings['ctt_link_position'] ) : 'right' );

		// Add cta icon animation to the styles class
		$style_class .= ( ! empty( $settings['ctt_link_icon_animation'] ) ? ' dpsp-click-to-tweet-cta-icon-animation' : '' );

		$args = [
			'network_share_link' => $network_share_link,
			'avatar'             => $avatar,
			'display_tweet'      => $display_tweet,
			'settings'           => $settings,
			'style_class'        => $style_class,
		];

		return \Mediavine\Grow\View_Loader::get_view( '/inc/views/click-to-tweet-shortcode.php', $args );

	}

	/**
	 * Given a click-to-tweet shortcode's arguments and an array of addon settings, return an appropriate CSS class.
	 *
	 * @param array $shortcode_args Arguments passed to a shortcode.
	 * @param array $addon_settings Additional settings used by the addon.
	 * @return string
	 */
	private static function click_to_tweet_style( array $shortcode_args, array $addon_settings ) : string {
		if ( ! empty( $shortcode_args['style'] ) ) {
			$configured_style = trim( $shortcode_args['style'] );
		} elseif ( ! empty( $addon_settings['ctt_style'] ) ) {
			$configured_style = trim( $addon_settings['ctt_style'] );
		} else {
			$configured_style = self::CLICK_TO_TWEET_DEFAULT_STYLE;
		}

		$configured_style = filter_var( $configured_style, FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => 1,
				'max_range' => 5,
			],
		] );
		$style_id         = $configured_style ? $configured_style : self::CLICK_TO_TWEET_DEFAULT_STYLE;
		$result           = "dpsp-style-{$style_id}";
		return $result;
	}

	/**
	 * 
	 * Replaces the shortcode with the Save This form
	 * @param array $attr Attributes passed to the shortcode (see Support Doc for full list)
	 * @return string html
	 * 
	 */
	public static function email_save_this( $attrs = [] ) {

		// Save This is only embedded if
		// Pro+ or Priority tier license key
		// License status is not invalid or disabled and is active on this domain
		$hubbub_activation = new \Mediavine\Grow\Activation;
		$license_tier = $hubbub_activation->get_license_tier();
		
		if ( !$license_tier || $license_tier == 'pro' ) { // Pro
			return;
		} else { // Pro+ or Priority
			$license_status = get_option( 'mv_grow_license_status' );

			if ( empty( $license_status ) || $license_status != 'valid' && $license_status != 'expired' ) {
				return;
			}
		}
		
		$attrs['is_shortcode'] = true; // Sending this attribute to alert the form builder that this is a shortcode
		
		return dpsp_email_save_this_get_form( $attrs );
	}
}
