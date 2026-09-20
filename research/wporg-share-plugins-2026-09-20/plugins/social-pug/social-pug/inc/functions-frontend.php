<?php

use Mediavine\Grow\Share_Counts;

/**
 * Returns the social network buttons.
 *
 * @param array $settings - the current section settings for the social networks
 * @param string $action - the action being taken
 * @param string $location - the location where the social networks will be displayed
 * @param array $data - data passed to class
 * @return string
 */
function dpsp_get_output_network_buttons( $settings, $action = 'share', $location = '', $data = [] ) {
	$output = DPSP_Network_Buttons_Outputter::get_render( $settings, $action, $location, $data );
	return $output;
}

/**
 * Returns the HTML for the total share counts of the networks passed.
 *
 * If no networks are passed, the total count for all active networks will be outputed
 *
 * @param string $location - the location of the share buttons
 * @param array $networks - list with all networks we wish to output total for
 * @return int
 */
function dpsp_get_output_total_share_count( $location = '', $networks = [] ) {
	$post_obj = dpsp_get_current_post();
	if ( ! $post_obj ) {
		return null;
	}

	$total_shares = Share_Counts::post_total_share_counts( $post_obj->ID, $location );

	if ( 0 == intval( $total_shares ) ) {
		return '';
	}

	if ( is_null( $total_shares ) ) {
		return '';
	}

	$args = [
		'icon'     => dpsp_get_svg_icon_output( 'share' ),
		'count'    => apply_filters( 'dpsp_get_output_total_share_count', $total_shares, $location ),
		'text'     => apply_filters( 'dpsp_total_share_count_text', __( 'shares', 'social-pug' ) ),
		'location' => $location,
	];

	return \Mediavine\Grow\View_Loader::get_view( '/inc/views/total-share-count.php', $args );
}

/**
 * Returns the HTML string for the social share buttons
 *
 * @param array $args Arguments array elements:
 * 'id'                     - string
 * 'networks'               - array
 * 'networks_labels'        - array
 * 'button_style'           - int (from 1 to 8)
 * 'shape'                  - string (rectangle/rounded/circle)
 * 'size'                   - string (small/medium/large)
 * 'columns'                - string (auto) / int (from 1 to 6),
 * 'show_labels'            - bool
 * 'show_labels_mobile'     - bool
 * 'button_spacing'         - bool
 * 'show_count'             - bool
 * 'show_total_count'       - bool
 * 'total_count_position'   - string (before/after)
 * 'count_round'            - bool
 * 'minimum_count'          - int
 * 'minimum_individual_count'           - int
 * 'show_mobile'            - bool
 * 'overwrite'              - bool
 * @return string
 */
function dpsp_get_share_buttons( $args = [] ) {
	// Modify settings based on the attributes
	$settings = [];

	// Set networks and network labels
	if ( ! empty( $args['networks'] ) ) {
		$networks        = array_map( 'trim', $args['networks'] );
		$networks_labels = ( ! empty( $args['networks_labels'] ) ? $args['networks_labels'] : [] );

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

	// Set buttons shape
	if ( ! empty( $args['shape'] ) ) {
		$settings['display']['shape'] = $args['shape'];
	}

	// Set buttons size
	if ( ! empty( $args['size'] ) ) {
		$settings['display']['size'] = $args['size'];
	}

	// Set columns
	if ( ! empty( $args['columns'] ) ) {
		$settings['display']['column_count'] = sanitize_html_class( $args['columns'] );
	}

	// Show labels
	if ( isset( $args['show_labels'] ) ) {
		$settings['display']['show_labels'] = ( ! empty( $args['show_labels'] ) ? 'yes' : 'no' );
	}

	// Show labels mobile
	if ( isset( $args['show_labels_mobile'] ) ) {
		$settings['display']['show_labels_mobile'] = ( ! empty( $args['show_labels_mobile'] ) ? 'yes' : 'no' );
	}

	// Button spacing
	if ( isset( $args['button_spacing'] ) ) {
		$settings['display']['spacing'] = ( ! empty( $args['button_spacing'] ) ? 'yes' : 'no' );
	}

	// Show count
	if ( isset( $args['show_count'] ) ) {
		$settings['display']['show_count'] = ( ! empty( $args['show_count'] ) ? 'yes' : 'no' );
	}

	// Show count total
	if ( isset( $args['show_total_count'] ) ) {
		$settings['display']['show_count_total'] = ( ! empty( $args['show_total_count'] ) ? 'yes' : 'no' );
	}

	// Total count position
	if ( ! empty( $args['total_count_position'] ) ) {
		$settings['display']['total_count_position'] = $args['total_count_position'];
	}

	// Share counts round
	if ( isset( $args['count_round'] ) ) {
		$settings['display']['count_round'] = ( ! empty( $args['count_round'] ) ? 'yes' : 'no' );
	}

	// Share minimum global count
	if ( ! empty( $args['minimum_count'] ) ) {
		$settings['display']['minimum_count'] = (int) $args['minimum_count'];
	}

	// Share minimum individual count
	if ( ! empty( $args['minimum_individual_count'] ) ) {
		$settings['display']['minimum_individual_count'] = (int) $args['minimum_individual_count'];
	}

	// Show on mobile
	if ( isset( $args['show_mobile'] ) ) {
		$settings['display']['show_mobile'] = ( ! empty( $args['show_mobile'] ) ? 'yes' : 'no' );
	}

	// If Overwrite is set to "yes" strip everything
	if ( empty( $args['overwrite'] ) ) {

		// Location settings for the Content location
		$saved_settings = dpsp_get_location_settings( 'content' );

		// Social networks
		$settings['networks'] = ( ! empty( $settings['networks'] ) ? $settings['networks'] : $saved_settings['networks'] );

		// Display settings
		$settings['display'] = array_merge( $saved_settings['display'], $settings['display'] );
	}

	// Remove all display settings that have "no" as a value
	foreach ( $settings['display'] as $key => $value ) {
		if ( 'no' === $value ) {
			unset( $settings['display'][ $key ] );
		}
	}

	// Round counts cannot be changed direcly because they are too dependent on the location settings saved in the database.
	// For the moment removing the filters and adding them again is the only solution.
	if ( ! isset( $settings['display']['count_round'] ) ) {
		remove_filter( 'dpsp_get_output_post_shares_counts', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
		remove_filter( 'dpsp_get_output_total_share_count', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
	}

	// Start outputing
	$output = '';

	// Classes for the wrapper
	$wrapper_classes   = [ 'dpsp-share-buttons-wrapper' ];
	$wrapper_classes[] = ( isset( $settings['display']['shape'] ) ? 'dpsp-shape-' . $settings['display']['shape'] : '' );
	$wrapper_classes[] = ( isset( $settings['display']['size'] ) ? 'dpsp-size-' . $settings['display']['size'] : 'dpsp-size-medium' );
	$wrapper_classes[] = ( isset( $settings['display']['column_count'] ) ? 'dpsp-column-' . $settings['display']['column_count'] : '' );
	$wrapper_classes[] = ( isset( $settings['display']['spacing'] ) ? 'dpsp-has-spacing' : '' );
	$wrapper_classes[] = ( isset( $settings['display']['show_labels'] ) || isset( $settings['display']['show_count'] ) ? '' : 'dpsp-no-labels' );
	$wrapper_classes[] = ( isset( $settings['display']['show_labels_mobile'] ) || isset( $settings['display']['show_count'] ) ? '' : 'dpsp-no-labels-mobile' );
	$wrapper_classes[] = ( isset( $settings['display']['show_count'] ) ? 'dpsp-has-buttons-count' : '' );
	$wrapper_classes[] = ( isset( $settings['display']['show_mobile'] ) ? 'dpsp-show-on-mobile' : 'dpsp-hide-on-mobile' );

	// Button total share counts
	$minimum_count    = ( ! empty( $settings['display']['minimum_count'] ) ? (int) $settings['display']['minimum_count'] : 0 );
	$show_total_count = ( $minimum_count <= (int) Share_Counts::post_total_share_counts() && ! empty( $settings['display']['show_count_total'] ) ? true : false );

	$wrapper_classes[] = ( $show_total_count ? 'dpsp-show-total-share-count' : '' );
	$wrapper_classes[] = ( $show_total_count ? ( ! empty( $settings['display']['total_count_position'] ) ? 'dpsp-show-total-share-count-' . $settings['display']['total_count_position'] : 'dpsp-show-total-share-count-before' ) : '' );

	// Button styles
	$wrapper_classes[] = ( isset( $settings['button_style'] ) ? 'dpsp-button-style-' . $settings['button_style'] : '' );

	$wrapper_classes = implode( ' ', array_filter( $wrapper_classes ) );

	// Output total share counts
	if ( $show_total_count ) {
		$output .= dpsp_get_output_total_share_count( 'content' );
	}

	// Gets the social network buttons
	if ( isset( $settings['networks'] ) ) {
		$output .= dpsp_get_output_network_buttons( $settings, 'share', 'content' );
	}

	$output = '<div ' . ( ! empty( $args['id'] ) ? 'id="' . esc_attr( $args['id'] ) . '"' : '' ) . ' class="' . $wrapper_classes . '">' . $output . '</div>';

	// Add back the filters
	if ( ! isset( $settings['display']['count_round'] ) ) {
		add_filter( 'dpsp_get_output_post_shares_counts', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
		add_filter( 'dpsp_get_output_total_share_count', 'Mediavine\Grow\Share_Counts::round_counts', 10, 2 );
	}

	return $output;
}
