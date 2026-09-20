<?php
use Mediavine\Grow\Tools\Toolkit;
//@TODO: This whole file is ripe for refactoring to use a single source of truth in the network/tool classes to determine these values

/**
 * Returns an array with the positions where the social networks can be placed.
 *
 * @param string $for
 * @param boolean $only_slugs
 * @deprecated Use Toolkit methods instead
 * @return array
 */
function dpsp_get_network_locations( $for = 'all', $only_slugs = true ) {
	$tools         = Toolkit::get_instance();
	$all_locations = $tools->get_all();
	$all_locations = array_filter(
		$all_locations, function( $tool ) {
		return $tool->get_type() !== 'misc_tool';
		}
	);

	switch ( $for ) {
		case 'share':
			$locations = array_filter(
				$all_locations, function( $tool ) {
				return $tool->get_type() === 'share_tool';
				}
			);
			break;
		case 'follow':
			$locations = array_filter(
				$all_locations, function( $tool ) {
				return $tool->get_type() === 'follow_tool';
				}
			);
			break;
		default:
			$locations = $all_locations;
			break;
	}

	$locations = $tools->make_simple_array( $locations );

	$locations = apply_filters( 'dpsp_get_network_locations', $locations, $for );
	if ( $only_slugs ) {
		$locations = array_keys( $locations );
	}
	return $locations;
}

/**
 * Returns the name of a location.
 *
 * @param string $location_slug
 * @return string
 */
function dpsp_get_network_location_name( $location_slug ) {
	$locations = dpsp_get_network_locations( 'all', false );

	if ( isset( $locations[ $location_slug ] ) ) {
		return $locations[ $location_slug ];
	} else {
		return '';
	}
}

/**
 * Checks to see if the location is active or not.
 *
 * @param $location_slug
 * @return bool
 */
function dpsp_is_location_active( $location_slug ) {
	$settings = dpsp_get_location_settings( $location_slug );

	if ( isset( $settings['active'] ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Determines whether the location should be displayed or not
 *
 * @param string $location_slug
 * @return bool
 */
function dpsp_is_location_displayable( $location_slug ) {
	$return = true;

	// Get saved settings for the location
	// TODO: Make this more robust so that as we add tools, we don't need "location" in the slug
	// This was modified for save this
	$settings = ( $location_slug == 'email_save_this' ) ? get_option( 'dpsp_' . $location_slug ) : dpsp_get_location_settings( $location_slug );

	if ( empty( $settings ) ) {
		$return = false;
	}

	if ( ! isset( $settings['post_type_display'] ) || ( isset( $settings['post_type_display'] ) && ! is_singular( $settings['post_type_display'] ) ) ) {
		$return = false;
	}

	return apply_filters( 'dpsp_is_location_displayable', $return, $location_slug, $settings );
}

/**
 * Get settings for a particular location.
 *
 * @param string $location
 * @return mixed
 */
function dpsp_get_location_settings( $location = '' ) {
	// Return null if no location is provided
	if ( empty( $location ) ) {
		return null;
	}

	$location_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_location_' . $location, [] );

	return apply_filters( 'dpsp_get_location_settings', $location_settings, $location );
}

/**
 * Function that returns all networks.
 *
 * @param string $for - buttons for share(ing) or follow(ing)
 * @return array
 */
function dpsp_get_networks( $for = 'share' ) {
	$networks = \Mediavine\Grow\Networks::get_instance();
	// @TODO: This should probably be split up into independent functions
	if ( 'all' === $for ) {
		$output = $networks->make_simple_array( $networks->get_all() );
	} elseif ( 'follow' === $for ) {
		$output = apply_filters( 'dpsp_follow_networks', $networks->make_simple_array( $networks->get_followable() ) );
	} else {
		$output = apply_filters( 'dpsp_share_networks', $networks->make_simple_array( $networks->get_shareable() ) );

	}

	/**
	 * Filter the networks before returning them.
	 * @param array $networks
	 * @param string $for
	 */
	return apply_filters( 'dpsp_get_networks', $output, $for );
}

/**
 * Function that returns the name of a social network given its slug.
 *
 * @param string $slug
 * @return string
 */
function dpsp_get_network_name( $slug ) {
	$nerworks = dpsp_get_networks( 'all' );

	if ( isset( $nerworks[ $slug ] ) ) {
		return $nerworks[ $slug ];
	} else {
		return '';
	}
}

/**
 * Returns all networks that are set in every location panel.
 *
 * @param string $for
 * @return array
 */
function dpsp_get_active_networks( $for = 'share' ) {
	$locations = dpsp_get_network_locations( $for );
	$networks  = [];

	foreach ( $locations as $location ) {

		$location_settings = dpsp_get_location_settings( $location );
		if ( ! $location_settings || empty( $location_settings['active'] ) ) {
			continue;
		}

		if ( isset( $location_settings['networks'] ) && ! empty( $location_settings['networks'] ) ) {
			foreach ( $location_settings['networks'] as $network_slug => $network ) {

				if ( ! in_array( $network_slug, $networks, true ) ) {
					$networks[] = $network_slug;
				}
			}
		}
	}

	return apply_filters( 'dpsp_get_active_networks', $networks, $for );
}

/**
 * Return an array of registered post types slugs and names
 *
 * @return array
 */
function dpsp_get_post_types() {
	// Get default and custom post types
	$default_post_types = [ 'post', 'page' ];
	$custom_post_types  = get_post_types(
		[
			'public'   => true,
			'_builtin' => false,
		]
	);
	$post_types         = array_merge( $default_post_types, $custom_post_types );

	// The array we wish to return
	$return_post_types = [];

	foreach ( $post_types as $post_type ) {
		$post_type_object = get_post_type_object( $post_type );

		$return_post_types[ $post_type ] = $post_type_object->labels->singular_name;
	}

	return apply_filters( 'dpsp_get_post_types', $return_post_types );
}

/**
 * Returns the post types that are active for all locations.
 */
function dpsp_get_active_post_types() {

	$locations  = dpsp_get_network_locations();
	$post_types = [];

	foreach ( $locations as $location ) {
		$location_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_location_' . $location, [] );
		if ( isset( $location_settings['active'] ) && ! empty( $location_settings['post_type_display'] ) ) {
			$post_types = array_merge( $post_types, $location_settings['post_type_display'] );
		}
	}

	$post_types = array_unique( $post_types );

	return $post_types;
}

/**
 * Returns the saved option, but replaces the saved social network
 * data with simple data to display in the back-end
 *
 * @param string $option_name Name of the option to get
 * @return array $settings
 */
function dpsp_get_back_end_display_option( string $option_name ) : array {
	$settings = Mediavine\Grow\Settings::get_setting( $option_name );
	$networks = dpsp_get_networks( 'all' );

	$settings_networks_count = ( ! empty( $settings['networks'] ) ? count( $settings['networks'] ) : 0 );

	if ( $settings_networks_count > 2 ) {

		$current_network = 0;
		foreach ( $settings['networks'] as $network_slug => $network ) {

			if ( $current_network > 2 ) {
				unset( $settings['networks'][ $network_slug ] );
			} else {
				if ( ! empty( $networks[ $network_slug ] ) ) {
					$settings['networks'][ $network_slug ] = [ 'label' => $networks[ $network_slug ] ];
				}
			}

			$current_network ++;
		}
	} else {
		$settings['networks'] = [
			'facebook'  => [ 'label' => 'Facebook' ],
			'twitter'   => [ 'label' => 'Twitter' ],
			'pinterest' => [ 'label' => 'Pinterest' ],
		];
	}

	//Unset certain options
	unset( $settings['display']['show_count'] );

	return $settings;
}

/**
 * Returns the share link for a social network given the network slug.
 *
 * @param string $network_slug Slug for Network
 * @param string $post_url Post URL to get share link for
 * @param string $post_title Post Title to be included in share link
 * @param string $post_description Post description to be included in share link
 * @param string $post_image Post image to be included in share link
 *
 * @return string
 *
 */
function dpsp_get_network_share_link( 
	string $network_slug = '',
	?string $post_url = null, 
	?string $post_title = null, 
	?string $post_description = null,
	?string $post_image = null ) : string {
	
	if ( empty( $network_slug ) ) {
		return '';
	}

	if ( is_null( $post_url ) ) {
		$post_obj = dpsp_get_current_post();
		$post_url = dpsp_get_post_url( $post_obj->ID );
	}

	if ( is_null( $post_title ) ) {
		$post_obj   = dpsp_get_current_post();
		$post_title = dpsp_get_post_title( $post_obj->ID );
	}

	if ( is_null( $post_description ) ) {
		$post_obj         = dpsp_get_current_post();
		$post_description = dpsp_get_post_description( $post_obj->ID );
	}

	// Late filtering
	$post_url         = rawurlencode( apply_filters( 'dpsp_get_network_share_link_post_url', $post_url, $network_slug ) );
	$post_title       = rawurlencode( apply_filters( 'dpsp_get_network_share_link_post_title', $post_title, $network_slug ) );
	$post_image       = apply_filters( 'dpsp_get_network_share_link_post_image', $post_image, $network_slug );
	$post_description = rawurlencode( apply_filters( 'dpsp_get_network_share_link_post_description', $post_description, $network_slug ) );
	$networks         = \Mediavine\Grow\Networks::get_instance();
	$network          = $networks->get( $network_slug );
	if ( ! $network ) {
		return '';
	}
	return $network->get_share_link( $post_url, $post_title, $post_image, $post_description );
}

/**
 * Returns the network follow link.
 *
 * @param string $network_slug
 * @return string
 */
function dpsp_get_network_follow_link( $network_slug ) {
	$settings = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );

	if ( ! empty( $settings[ $network_slug . '_username' ] ) ) {
		$network_handle = $settings[ $network_slug . '_username' ];
	} else {
		return '';
	}

	$networks = \Mediavine\Grow\Networks::get_instance();
	$network  = $networks->get( $network_slug );
	if ( ! $network ) {
		return '';
	}
	return $network->get_follow_link( $network_handle );
}

/**
 * Return Facebook, Pinterest and Pinterest networks if no active networks are present
 * on first ever activation of the plugin in order for the first ever cron job to pull
 * the share counts for these three social networks.
 *
 * Without this, the cron job will be executed later and at first no share counts will be
 * available for the last posts.
 *
 * @param array $networks
 * @param string $for
 *
 * @return array
 *
 */
function dpsp_first_activation_active_networks( $networks = [], $for = 'share' ) {
	if ( ! empty( $networks ) ) {
		return $networks;
	}

	if ( 'share' !== $for ) {
		return $networks;
	}

	$first_activation = Mediavine\Grow\Settings::get_setting( 'dpsp_first_activation', '' );

	if ( ! empty( $first_activation ) ) {
		return $networks;
	}

	$networks = [ 'facebook', 'twitter', 'pinterest' ];

	return $networks;
}

/**
 * Adds the initial options and settings.
 */
function dpsp_default_settings() {
	// Add general settings
	$dpsp_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );

	// Click to Tweet
	if ( ! isset( $dpsp_settings['shortening_service'] ) ) {
		$dpsp_settings['shortening_service'] = 'bitly';
	}

	if ( ! isset( $dpsp_settings['ctt_style'] ) ) {
		$dpsp_settings['ctt_style'] = 1;
	}

	if ( ! isset( $dpsp_settings['ctt_link_text'] ) ) {
		$dpsp_settings['ctt_link_text'] = 'Click to Tweet';
	}

	// Google Analytics UTM tracking
	if ( ! isset( $dpsp_settings['utm_source'] ) ) {
		$dpsp_settings['utm_source'] = '{{network_name}}';
	}

	if ( ! isset( $dpsp_settings['utm_medium'] ) ) {
		$dpsp_settings['utm_medium'] = 'social';
	}

	if ( ! isset( $dpsp_settings['utm_campaign'] ) ) {
		$dpsp_settings['utm_campaign'] = 'hubbub-pro';
	}

	// Update settings
	update_option( 'dpsp_settings', $dpsp_settings );

	// Add default settings for each share buttons location
	$locations = dpsp_get_network_locations();

	foreach ( $locations as $location ) {
		$location_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_location_' . $location, [] );
		if ( ! empty( $location_settings ) ) {
			continue;
		}

		// General settings for all locations
		$location_settings = [
			'networks'          => [
				'facebook' 		=> ['label' => 'Facebook'],
				'pinterest'  	=> ['label' => 'Pinterest'],
				'email'  		=> ['label' => 'Email']
			],
			'button_style'      => 1,
			'display'           => [
				'shape' => 'rectangular',
				'size'  => 'medium',
				'spacing' => 'yes',
			],
			'post_type_display' => [
				'post',
			],
		];

		// Individual settings per location
		switch ( $location ) {
			case 'sidebar':
				$location_settings['display']['position']       = 'left';
				$location_settings['display']['icon_animation'] = 'yes';
				break;

			case 'content':
				$location_settings['display']['position']       = 'top';
				$location_settings['display']['column_count']   = 'auto';
				$location_settings['display']['icon_animation'] = 'yes';
				$location_settings['display']['show_mobile']    = 'yes';
				$location_settings['display']['show_labels']    = 'yes';
				$location_settings['display']['show_labels_mobile']    = 'no';
				break;

			case 'sticky_bar':
				$location_settings['display']['screen_size']      = '720';
				$location_settings['display']['column_count']     = '3';
				$location_settings['display']['icon_animation']   = 'yes';
				$location_settings['display']['show_on_device']   = 'mobile';
				$location_settings['display']['position_desktop'] = 'bottom';
				$location_settings['display']['position_mobile']  = 'bottom';
				break;

			case 'pop_up':
				$location_settings['display']['icon_animation'] = 'yes';
				$location_settings['display']['show_labels']    = 'yes';
				$location_settings['display']['show_labels_mobile']    = 'yes';
				$location_settings['display']['title']          = __( 'Sharing is Caring', 'social-pug' );
				$location_settings['display']['message']        = __( 'Help spread the word. Thank you!', 'social-pug' );
				break;

			case 'follow_widget':
				$location_settings['display']['show_labels'] = 'yes';
				$location_settings['display']['show_labels_mobile']    = 'yes';
				$location_settings['display']['show_mobile'] = 'yes';
				break;

		}

		// Update option with values
		update_option( 'dpsp_location_' . $location, $location_settings );
	}
}

/**
 * Determines whether to display the buttons for a location.
 *
 * Checks if post has overwrite display option selected.
 *
 * @param $return
 * @param $location_slug
 * @param $settings
 * @return bool
 */
function dpsp_post_location_overwrite_option( $return, $location_slug, $settings ) {
	$post_obj = dpsp_get_current_post();
	if ( ! $post_obj ) {
		return $return;
	}

	// Pull share options meta data
	$share_options = dpsp_maybe_convert_post_meta_to_json( $post_obj->ID, 'dpsp_share_options', true );

	if ( ! empty( $share_options['locations_overwrite'] ) && is_array( $share_options['locations_overwrite'] ) && in_array( $location_slug, $share_options['locations_overwrite'], true ) ) {
		return false;
	}
	if ( ! empty( $share_options['locations_overwrite_show'] ) && is_array( $share_options['locations_overwrite_show'] ) && in_array( $location_slug, $share_options['locations_overwrite_show'], true ) ) {
		return true;
	}

	return $return;
}

/**
 * Darkens a given color.
 *
 * @param $rgb
 * @param $darker
 * @return string
 */
function dpsp_darken_color( $rgb, $darker ) {
	$hash = ( strpos( $rgb, '#' ) !== false ) ? '#' : '';
	$rgb  = ( strlen( $rgb ) === 7 ) ? str_replace( '#', '', $rgb ) : ( ( strlen( $rgb ) === 6 ) ? $rgb : false );
	if ( strlen( $rgb ) !== 6 ) {
		return $hash . '000000';
	}
	$darker = ( $darker > 1 ) ? $darker : 1;

	list( $R16, $G16, $B16 ) = str_split( $rgb, 2 );

	$R = sprintf( '%02X', floor( hexdec( $R16 ) / $darker ) );
	$G = sprintf( '%02X', floor( hexdec( $G16 ) / $darker ) );
	$B = sprintf( '%02X', floor( hexdec( $B16 ) / $darker ) );

	return $hash . $R . $G . $B;
}

/**
 * Removes the script tags from the values of an array recursivelly.
 *
 * @param array $array
 * @return array
 */
function dpsp_array_strip_script_tags( $array = [] ) {
	if ( empty( $array ) || ! is_array( $array ) ) {
		return [];
	}

	foreach ( $array as $key => $value ) {
		if ( is_array( $value ) ) {
			$array[ $key ] = dpsp_array_strip_script_tags( $value );
		} else {
			$array[ $key ] = preg_replace( '@<(script)[^>]*?>.*?</\\1>@si', '', $value );
		}
	}

	return $array;
}

/**
 * Wrapper to WP's "attachment_url_to_postid" function, which also handles URLs for image sizes.
 *
 * @param string $url
 * @return int
 */
function dpsp_attachment_url_to_postid( $url ) {
	// Try to get post ID with given URL
	$post_id = attachment_url_to_postid( $url );

	// Try to get post ID with URL image sizes stripped down
	if ( empty( $post_id ) ) {
		$dir  = wp_upload_dir();
		$path = $url;

		if ( 0 === strpos( $path, $dir['baseurl'] . '/' ) ) {
			$path = substr( $path, strlen( $dir['baseurl'] . '/' ) );
		}

		if ( preg_match( '/^(.*)(\-\d*x\d*)(\.\w{1,})/i', $path, $matches ) ) {
			$url     = $dir['baseurl'] . '/' . $matches[1] . $matches[3];
			$post_id = attachment_url_to_postid( $url );
		}
	}

	// Try to get post ID with scaled image URL
	if ( empty( $post_id ) ) {
		$extension_pos = strrpos( $url, '.' );

		$url     = substr( $url, 0, $extension_pos ) . '-scaled' . substr( $url, $extension_pos );
		$post_id = attachment_url_to_postid( $url );

	}

	return absint( $post_id );
}

/**
 * Returns the SVG data for the provided icon slug.
 *
 * @param string $slug
 * @return array
 */
function dpsp_get_svg_icon_data( $slug = '' ) {
	$icons = \Mediavine\Grow\Icons::get_instance();
	$icon  = $icons->get( $slug );
	if ( ! $icon ) {
		error_log( 'MV Grow: Icon Not Found ' . $slug ); // @codingStandardsIgnoreLine
		return [];
	}

	return $icon->get_data();
}

/**
 * Outputs the <svg> element corresponding to the provided icon.
 *
 * @param string $slug
 * @return string
 */
function dpsp_get_svg_icon_output( $slug ) {
	$icons = \Mediavine\Grow\Icons::get_instance();
	$icon  = $icons->get( $slug );
	if ( ! $icon ) {
		error_log( 'MV Grow: Icon Not Found ' . $slug ); // @codingStandardsIgnoreLine
		return '';
	}

	return $icon->compose_svg();
}

/**
 * 
 * Deprecated March 2024
 * Old description: Attempts to recursively unserialize the given value.
 *
 * @param mixed $value
 * @return mixed
 */
function dpsp_maybe_unserialize( $value ) {
	$index = 1;
	$type  = gettype( $value );

	while ( 'string' === $type ) {
		if ( $index >= 5 ) {
			break;
		}

		$value = maybe_unserialize( $value );
		$type  = gettype( $value );
		$index++;
	}

	return $value;
}

/**
 * 
 * Helper function to convert previously PHP serialized data 
 * into JSON encoded strings and add a new post meta key
 * with _json appended, leaving the old PHP serialized data
 * in place.
 * - If the value is already JSON encoded return the decoded obj.
 * - If the value isn't serialized, return string
 * json_decode returns associative arrays and goes 5 levels
 * deep per dpsp_maybe_unserialize being recursive previously
 * 
 * @param mixed $value
 * @return mixed
 * 
 */
function dpsp_maybe_convert_post_meta_to_json( $id, $key ) {
	if ( empty( $id ) || empty( $key ) ) {
		return false;
	}

	// Check for JSON encoded key first,
	// if not present, assume the old PHP serialized key exists
	$value = get_post_meta( $id, $key . '_json', true );
	$value = ( empty( $value ) || !$value ) ? get_post_meta( $id, $key, true ) : $value;

	// If the value is an array it means it was serialized and has not yet
	// been converted to JSON. Any other data format can be left as-is.
	if ( is_array( $value ) || is_object( $value ) ) {
		// Convert the data to JSON, store it, and return.
		$json_encoded_value = json_encode( $value, JSON_UNESCAPED_UNICODE );

		update_post_meta( $id, $key . '_json', $json_encoded_value ); // Append _json to create new key

		$value = json_decode( $json_encoded_value, true, 5 );
	} else {
		// If this is JSON, decode it before returning.
		$decoded = json_decode( $value, true, 5 );

		if ( json_last_error() === JSON_ERROR_NONE ) {
			$value = $decoded;
		}
	}

	return $value;
}

/**
 * Get Icon Data from an array of networks
 * @param array $networks Array of networks, an associative array with network slugs as the keys
 *
 * @return array Array of icon data keyed to network slugs
 */
function dpsp_get_svg_data_for_networks( $networks ) {
	$icons = \Mediavine\Grow\Icons::get_instance();
	$slugs = array_keys( $networks );
	$data  = [];

	$share_icon = $icons->get( 'share' );

	$data['share'] = $share_icon ? $share_icon->get_data() : [];

	foreach ( $slugs as $slug ) {
		$icon = $icons->get( $slug );
		if ( empty( $icon ) ) {
			continue;
		}
		$data[ $slug ] = $icon->get_data();
	}
	return $data;
}

/**
 * Determines if the string provided is valid JSON
 * Used primarily to determine if a stored custom field value
 * is already encoded JSON.
 * 
 * @param string $string A string, likely returned from the database, also likely a serialized PHP Object
 * @return boolean
 */
function dpsp_is_json( $string ) {
	try {
		$result = json_decode( $string );
	} catch (\Throwable $th) {
		print_r($th);
	}
	return json_last_error() === JSON_ERROR_NONE;
}

/**
 * Register hooks for functions.php
 */
function dpsp_register_functions() {
	add_filter( 'dpsp_get_active_networks', 'dpsp_first_activation_active_networks', 10, 2 );
	add_filter( 'dpsp_is_location_displayable', 'dpsp_post_location_overwrite_option', 10, 3 );
}

/** 
 * Confirm Save This Tool email verification test
 * Runs very early to enable redirect and remove query argument
 */
function dpsp_save_this_verify(){
	// Confirm Verification request
	if ( isset( $_GET['verify'] ) ) {
		$dpsp_email_save_this = Mediavine\Grow\Settings::get_setting( 'dpsp_email_save_this', 'not_set' );
		$dpsp_email_save_this = ( $dpsp_email_save_this == 'not_set' ) ? array() : $dpsp_email_save_this; // First run experience may not have set up defaults yet
		$dpsp_email_save_this['verify_email_send_capability'] = ( $_GET['verify'] == 0 ) ? '' : 'yes';
		update_option( 'dpsp_email_save_this', $dpsp_email_save_this );
		wp_redirect( admin_url( 'admin.php?page=dpsp-email-save-this') );
		exit;
	}
}
