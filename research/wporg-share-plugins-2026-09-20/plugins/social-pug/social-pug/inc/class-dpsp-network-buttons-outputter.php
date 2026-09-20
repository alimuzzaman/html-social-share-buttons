<?php

use Mediavine\Grow\Network;
use Mediavine\Grow\Networks;
use Mediavine\Grow\Critical_Styles;
use Mediavine\Grow\Share_Counts;
/**
 * Class that handles the output of the social button list items and unordered list wrapper.
 */
class DPSP_Network_Buttons_Outputter {

	private static $button_defaults = [
		'tag'               => 'a',
		'rel'               => '',
		'network_slug'      => '',
		'href_attribute'    => '',
		'button_classes'    => '',
		'icon_svg'          => '',
		'title_attribute'   => '',
		'location'          => '',
		'action'            => 'share',
		'network_label'     => '',
		'network_shares'    => 0,
		'show_labels'       => true,
		'show_labels_mobile' => true,
		'show_share_counts' => true,
	];

	/**
	 * Return the output for a button location.
	 *
	 * @param array $settings Array of settings for this location
	 * @param string $action The action these buttons will be used for, either share or follow
	 * @param string $location The location slug where these buttons will be output
	 * @param array $data Other data about how these buttons should be output
	 * @return string
	 */
	public static function get_render( $settings = [], $action = '', $location = '', $data = [] ) {
		$data['settings'] = apply_filters( 'dpsp_network_buttons_outputter_settings', $settings, $action, $location );
		$data['action']   = $action;
		$data['location'] = $location;
		if ( empty( $data['settings']['networks'] ) ) {
			return '';
		}
		$data['post_details'] = self::get_post_details( $data );
		// Start concatenating the output
		$output = '<ul class="' . esc_attr( self::make_wrapper_classes( $data['settings'], $data['action'], $data['location'] ) ) . '" ' . Critical_Styles::get( 'button-list-wrapper', $location ) . '>';

		// Array position for css classes, start off with first
		$array_position = [ 'first' ];
		// Loop through each network and create the button
		end( $data['settings']['networks'] );
		$last_key = key( $data['settings']['networks'] );
		$networks = Networks::get_instance();
		
		foreach ( $data['settings']['networks'] as $network_slug => $network_data ) {
			// Check if we are at the last position to for the css class
			if ( $last_key === $network_slug ) {
				$array_position[] = 'last';
			}
			$network = $networks->get( $network_slug );
			if ( ! $network ) {
				continue;
			}
			$args   = array_merge( self::$button_defaults, self::single_button_data( $data, $network, $network_data['label'], $array_position ) );
			$button = \Mediavine\Grow\View_Loader::get_view( '/inc/views/single-button.php', $args );

			$output .= apply_filters( 'dpsp_get_button_output', $button, $args['network_slug'], $args['action'], $args['location'] );
			// Set position to false after first iteration
			$array_position = [];
		}

		$output .= '</ul>';

		return $output;
	}

	/**
	 * Collects general data about the current post.
	 *
	 * @param array $data Data about the current call for buttons
	 * @return array $post_details
	 */
	public static function get_post_details( $data ) {
		if ( is_admin() ) {
			return [];
		}
		// Set networks shares and post details
		$post_obj = dpsp_get_current_post();
		if ( ! is_object( $post_obj ) ) {
			return false;
		}
		$post_details = [];
		// Get post url and title

		if ( empty( $data['shortcode_url'] ) ) {
			$post_details['post_id']                 = $post_obj->ID;
			$post_details['post_url']                = dpsp_get_post_url( $post_obj->ID );
			$post_details['post_title']              = dpsp_get_post_title( $post_obj->ID );
			$post_details['post_description']        = dpsp_get_post_description( $post_obj->ID );
			$post_details['post_featured_image_url'] = get_the_post_thumbnail_url( $post_obj, 'full' );

			// If a shortcode URL is provided we don't use the post's data
		} else {
			$post_details['post_url']   = $data['shortcode_url'];
			$post_details['post_title'] = ( ! empty( $data['shortcode_desc'] ) ? $data['shortcode_desc'] : '' );
		}

		// Get custom sharable content ( custom tweet, pinterest image and pinterest description )
		$share_options = dpsp_maybe_convert_post_meta_to_json( $post_obj->ID, 'dpsp_share_options', true );

		if ( ! empty( $share_options['custom_tweet'] ) ) {
			$post_details['custom_tweet'] = $share_options['custom_tweet'];
		}

		if ( ! empty( $share_options['custom_image_pinterest']['src'] ) ) {
			$post_details['post_custom_image_pinterest'] = rawurlencode( esc_url( $share_options['custom_image_pinterest']['src'] ) );
		}

		$share_counts = Share_Counts::post_share_counts( $post_obj->ID );
		// Get networks share count for this post
		if ( $post_obj ) {
			$networks_shares = apply_filters( 'dpsp_get_output_post_shares_counts', $share_counts, $data['location'] );
		}

		$post_details['networks_shares'] = ( ! empty( $networks_shares ) ? $networks_shares : [] );
		$post_details['networks_shares_unfiltered'] = ( ! empty( $share_counts ) ? $share_counts : [] );

		return $post_details;
	}

	/**
	 * Generate the wrapper class string.
	 *
	 * @param array  $settings settings for this location
	 * @param string $action string representing the action, either share or follow
	 * @param string $location string representing the location
	 *
	 * @return string of classes for wrapper
	 */
	private static function make_wrapper_classes( $settings, $action, $location ) {
		$wrapper_classes   = [];
		$wrapper_classes[] = 'dpsp-networks-btns-wrapper';
		$wrapper_classes[] = 'dpsp-networks-btns-' . esc_attr( $action );
		if ( ! empty( $location ) ) {
			$wrapper_classes[] = 'dpsp-networks-btns-' . str_replace( '_', '-', $location );
		}
		$wrapper_classes[] = ( isset( $settings['display']['column_count'] ) ? 'dpsp-column-' . sanitize_html_class( $settings['display']['column_count'] ) : '' );
		$wrapper_classes[] = ( isset( $settings['display']['icon_animation'] ) ? 'dpsp-has-button-icon-animation' : '' );

		return implode( ' ', $wrapper_classes );
	}

	/**
	 * Returns an array of data that contains all information for button output.
	 *
	 * @param array   $data Data about how button should be output
	 * @param Network $network  Associative array of network data
	 * @param string  $network_label  Network Label for Display
	 * @param array   $array_position An array of string identifiers for css classes to indicate the position
	 * @return array
	 */
	private static function single_button_data( $data, $network, $network_label, $array_position ) {
		$slug        = $network->get_slug();
		$icon_slug   = empty( $network->get_icon_override() ) ? $slug : $network->get_icon_override()->get_slug();
		$button_data = [
			'network_slug'    => $slug,
			'icon_svg'        => dpsp_get_svg_icon_output( $icon_slug ),
			'title_attribute' => esc_attr( 'share' === $data['action'] ? self::get_share_link_title_attribute( $slug, $network->get_label() ) : self::get_follow_link_title_attribute( $slug, $network->get_label() ) ),
			'network_label'   => esc_attr( ( ( $network_label === $network->get_name() ) && ( $network_label !== $network->get_label() ) ) ? $network->get_label() : $network_label ),
			'network_shares'  => ! empty( $data['post_details']['networks_shares'] ) ? self::get_network_shares( $slug, $data['post_details']['networks_shares'], $data['location'] ) : 0,
			'location'        => $data['location'],
			'action'          => $data['action'],
		];

		$button_data['show_share_counts'] = self::should_count_show( $data['settings'], $slug, ! empty( $data['post_details']['networks_shares_unfiltered'][ $slug ] ) && is_int($data['post_details']['networks_shares_unfiltered'][ $slug ]) ? $data['post_details']['networks_shares_unfiltered'][ $slug ] : 0 );
		$button_data['show_labels']       = self::should_label_show( $data['settings'] );
		$button_data['show_labels_mobile']       = self::should_label_show_mobile( $data['settings'] );

		// Get the link of the button
		$network_share_link = 'share' === $data['action'] ? self::get_button_share_link( $slug, $data ) : self::get_button_follow_link( $slug );

		$networks_with_count = array_keys( Networks::get_instance()->get_countable() );

		// Set button classes
		$button_data['button_classes']   = [ 'dpsp-network-btn' ];
		$button_data['button_classes'][] = ( ! empty( $slug ) ? 'dpsp-' . $slug : '' );
		$button_data['button_classes'][] = ( ( empty( $network->get_label() ) || ! isset( $data['settings']['display']['show_labels'] ) ) && ( ! in_array( $slug, $networks_with_count, true ) || ! $button_data['show_share_counts'] ) ? 'dpsp-no-label' : '' );
		$button_data['button_classes'][] = ( $button_data['show_share_counts'] ? 'dpsp-has-count' : '' );
		foreach ( $array_position as $position ) {
			$button_data['button_classes'][] = 'dpsp-' . $position;
		}
		$button_data['button_classes'][] = $button_data['show_labels'] ? 'dpsp-has-label' : '';

		$button_data['button_classes'][] = ! $button_data['show_labels_mobile'] ? 'dpsp-has-label-mobile' : '';

		// Filter the button classes
		$button_data['button_classes'] = apply_filters( 'dpsp_button_classes', $button_data['button_classes'], $data['location'], $button_data['network_shares'] );
		$button_data['button_classes'] = esc_attr( implode( ' ', array_filter( $button_data['button_classes'] ) ) );

		// Load pinterest and grow as a button when no href value is used
		if ( ( 'pinterest' === $slug || 'grow' === $slug || 'mastodon' === $slug || 'messenger' === $slug ) && 'share' === $data['action'] ) {
			$button_data['tag']            = 'button';
			$button_data['href_attribute'] = 'data-href="' . esc_url( $network_share_link ) . '"';
		} else {

			if ( 'twitter' === $slug || 'x' === $slug ) { // See GH #1844
				$network_share_link = 'mailto:'.$network_share_link;
				$network_share_link = esc_url( $network_share_link );
				$network_share_link = str_replace( 'mailto:', '', $network_share_link );
			} else {
				$network_share_link = esc_url( $network_share_link );
			}

			$button_data['tag']            = 'a';
			$button_data['href_attribute'] = 'href="' . $network_share_link . '"';
		}

		// Filter the "rel" attribute before adding it.
		$button_data['rel'] = esc_attr(
			implode(
				' ', apply_filters(
					'dpsp_network_button_attribute_rel', [
						'nofollow',
						'noopener',
					]
				)
			)
		);

		return $button_data;
	}

	/**
	 * Returns the value that should be populated in the link's "title" attribute, based on the provided network.
	 *
	 * @param string $network_slug
	 * @param string $network_label
	 * @return string
	 */
	private static function get_share_link_title_attribute( $network_slug, $network_label ) {
		// translators: %s
		$title = sprintf( __( 'Share on %s', 'social-pug' ), $network_label );

		if ( 'pinterest' === $network_slug ) {
			$title = __( 'Save to Pinterest', 'social-pug' );
		}

		if ( 'email' === $network_slug ) {
			$title = __( 'Send over email', 'social-pug' );
		}

		if ( 'print' === $network_slug ) {
			$title = __( 'Print this webpage', 'social-pug' );
		}

		if ( 'grow' === $network_slug ) {
			$title = __( 'Save on Grow.me', 'social-pug' );
		}

		/**
		 * Filter the title before returning it
		 *
		 * @param string $title
		 * @param string $network_slug
		 *
		 */
		$title = apply_filters( 'dpsp_link_title_attribute', $title, $network_slug );

		return $title;
	}

	/**
	 * Returns the value that should be populated in the link's "title" attribute, based on the provided network.
	 *
	 * @param string $network_slug
	 * @param string $network_label
	 * @return string
	 */
	private static function get_follow_link_title_attribute( $network_slug, $network_label ) {
		// translators: %s
		$title = sprintf( __( 'Follow on %s', 'social-pug' ), $network_label );
		$title = apply_filters( 'dpsp_link_title_attribute', $title, $network_slug );
		return $title;
	}

	/**
	 * Get the network shares for a given network.
	 *
	 * @param string $network_slug Machine readable name of network
	 * @param int    $networks_shares Array representing share counts from various networks
	 * @param string $location Location where these buttons are being output
	 *
	 * @return int|string Represents the count that will be displayed on the front end
	 */
	private static function get_network_shares( $network_slug, $networks_shares, $location ) {
		$network_shares = ( isset( $networks_shares[ $network_slug ] ) ? $networks_shares[ $network_slug ] : 0 );
		return apply_filters( 'dpsp_get_output_post_network_share_count', $network_shares, $location );
	}

	/**
	 * Determine if the share count should be output for a given button.
	 *
	 * @param array  $settings Array of settings for this location
	 * @param string $network_slug Slug of the network we are checking for
	 * @param int    $network_shares Shares for this network
	 * @return bool
	 */
	private static function should_count_show( array $settings, string $network_slug, int $network_shares ) : bool {
		if ( ! isset( $settings['display']['show_count'] ) ) {
			// Settings are not set for showing count
			return false;
		}
		$networks_with_count = array_keys( Networks::get_instance()->get_countable() );
		if ( ! in_array( $network_slug, $networks_with_count, true ) ) {
			// Network doesn't support share counts
			return false;
		}
		if ( 0 == intval( $network_shares ) ) {
			//Shares are not more than zero
			return false;
		}
		if ( ! empty( $settings['display']['minimum_count'] ) && $settings['display']['minimum_count'] > Share_Counts::post_total_share_counts() ) {
			// Minimum global count is set, and there are not enough shares
			return false;
		}
		if ( ! empty( $settings['display']['minimum_individual_count'] ) && $settings['display']['minimum_individual_count'] > $network_shares ) {
			// Minimum individual count is set, and there are not enough shares
			return false;
		}

		return true;
	}

	/**
	 * Determine if the label for a button should show.
	 *
	 * @param array $settings Array of settings for this location
	 * @return bool
	 */
	private static function should_label_show( $settings ) {
		return isset( $settings['display']['show_labels'] );
	}

	/**
	 * Determine if the label for a button should show on mobile
	 *
	 * @param array $settings Array of settings for this location
	 * @return bool
	 */
	private static function should_label_show_mobile( $settings ) {
		return isset( $settings['display']['show_labels_mobile'] );
	}

	/**
	 * Get the url string that a button or link will point to.
	 *
	 * @param string $network_slug Machine readable name of the network
	 * @param array $data Data bout the post and button output
	 * @return string
	 */
	private static function get_button_share_link( $network_slug, $data ) {
		if ( is_admin() ) {
			// Don't generate real link for admin pages
			return dpsp_get_network_share_link( $network_slug, '#', '', '' );
		}

		$post_image       = null;
		$post_title       = sanitize_text_field( $data['post_details']['post_title'] );
		$post_description = sanitize_text_field( $data['post_details']['post_description'] );

		// Replace post title with custom tweet for Twitter
		if ( 'twitter' === $network_slug && ! empty( $data['post_details']['custom_tweet'] ) ) {
			$post_title = $data['post_details']['custom_tweet'];
		} elseif ( 'pinterest' === $network_slug ) {
			// Replace post title with custom pinterest description
			// and post image with custom image for Pinterest
			$pinterest_custom_description = self::get_post_custom_description_pinterest();
			if ( ! empty( $pinterest_custom_description ) ) {
				$post_title = $pinterest_custom_description;
			}

			if ( ! empty( $data['post_details']['post_custom_image_pinterest'] ) ) {
				$post_image = $data['post_details']['post_custom_image_pinterest'];
			}
		}

		// Filter values before getting the share links
		$post_url         = apply_filters( 'dpsp_get_button_share_link_url', $data['post_details']['post_url'], $data['post_details']['post_id'], $network_slug, $data['location'] );
		$post_title       = apply_filters( 'dpsp_get_button_share_link_title', $post_title, $data['post_details']['post_id'], $network_slug, $data['location'] );
		$post_description = apply_filters( 'dpsp_get_button_share_link_description', $post_description, $data['post_details']['post_id'], $network_slug, $data['location'] );
		$post_image       = apply_filters( 'dpsp_get_button_share_link_image', $post_image, $data['post_details']['post_id'], $network_slug, $data['location'] );

		return dpsp_get_network_share_link( $network_slug, $post_url, $post_title, $post_description, $post_image );
	}

	/**
	 * Returns the Pinterest description for the post.
	 *
	 * @return string
	 */
	private static function get_post_custom_description_pinterest() {
		$pinterest_description = '';

		$post_obj      = dpsp_get_current_post();
		$share_options = dpsp_maybe_convert_post_meta_to_json( $post_obj->ID, 'dpsp_share_options', true );

		// Set the custom Pinterest description of the post if it exists
		if ( ! empty( $share_options['custom_description_pinterest'] ) ) {
			$pinterest_description = $share_options['custom_description_pinterest'];
		} elseif ( ! empty( $share_options['custom_image_pinterest']['id'] ) ) {
			// If it doesn't, check to see if a Pinterest image is set for the post,
			// if it is grab the Pinterest description from the image

			$image_id = absint( $share_options['custom_image_pinterest']['id'] );

			$image_pin_description = get_post_meta( $image_id, 'pin_description', true );

			// If the Pin description is set for the image, set it
			if ( ! empty( $image_pin_description ) ) {
				$pinterest_description = $image_pin_description;
			} else {
				// If not, check for the value from the Pin Description Source settings

				$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_pinterest_share_images_setting', [] );

				$pin_source = ( ! empty( $settings['share_image_pin_description_source'] ) ? $settings['share_image_pin_description_source'] : 'image_alt_tag' );

				// Get the alt text
				if ( 'image_alt_tag' === $pin_source ) {
					$pinterest_description = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
				}

				// Get the title text
				if ( 'image_title' === $pin_source ) {
					$pinterest_description = get_the_title( $image_id );
				}
			}
		}

		return $pinterest_description;
	}

	/**
	 * Get the url string that a follow link will point to.
	 *
	 * @param {string} $network_slug Machine readable name of the network
	 * @return string
	 */
	private static function get_button_follow_link( $network_slug ) {
		// @TODO: Bring this function into this class?
		return dpsp_get_network_follow_link( $network_slug );
	}
}
