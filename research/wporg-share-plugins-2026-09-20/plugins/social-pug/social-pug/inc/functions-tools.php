<?php
//@TODO: This whole file could probably be removed in favor of the Toolkit class
/**
 * Returns all the tools available with all their data.
 *
 * @param string $type
 * @param bool $only_slugs
 *
 * @return array
 */
function dpsp_get_tools( $type = 'all', $only_slugs = false ) {
	//@TODO: Use Toolkit class
	// The tools array
	$tools = [];

	/**
	 * Possibility to add other tools into the tools array.
	 *
	 * @param array $tools
	 */
	$tools = apply_filters( 'dpsp_get_tools', $tools );

	// Return only the tools of a certain type
	if ( 'all' !== $type && ! empty( $tools ) ) {
		foreach ( $tools as $tool_slug => $tool ) {
			if ( $type !== $tool['type'] ) {
				unset( $tools[ $tool_slug ] );
			}
		}
	}

	// Return only the slugs
	if ( $only_slugs ) {
		$tools = array_keys( $tools );
	}

	return $tools;
}

/**
 * Returns all active tools.
 *
 * Does not take into account the custom activation settings of the tools.
 *
 * @return array List of active tools
 */
function dpsp_get_active_tools() {
	$active_tools = Mediavine\Grow\Settings::get_setting( 'dpsp_active_tools', [] );
	// Get legacy active tools
	$tools = dpsp_get_tools();

	// Get slugs of tools that aren't already in the active tools array
	$legacy_slugs = array_diff( array_keys( $tools ), $active_tools );

	// Check the tool settings to see if any of the tools not in the active tools array are in fact active, then merge in.
	$active_tools = array_merge(
		$active_tools, array_filter(
			$legacy_slugs, function ( $tool_slug ) use ( $tools ) {
				return dpsp_is_legacy_tool_active( $tools[ $tool_slug ] );
			}
		)
	);

	return $active_tools;
}

/** 
 * Returns all activate tools - with nicenames.
 * 
 * Similar to get_active_tools except it returns the names of the tools rather
 * than the slugs
 * 
 */

function dpsp_get_active_tools_nicenames( $format = 'array', $include_links = false ) {

	$active_tools = dpsp_get_active_tools();

	$nicenames_array 	= array();
	$nicenames_csv		= '';
	$index				= 1;

	if ( is_array( $active_tools ) ) {

		foreach ( $active_tools as $tool ) {

			$skip_comma = false;
			
			switch( $tool ) {
				case 'share_sidebar':
					$nicenames_array[] 	= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-sidebar') . '">Floating Sidebar</a>' : 'Floating Sidebar';
					$nicenames_csv		.= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-sidebar') . '">Floating Sidebar</a>' : 'Floating Sidebar';
					break;
				case 'share_content':
					$nicenames_array[] 	= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-content') . '">Inline Content</a>' : 'Inline Content';
					$nicenames_csv		.= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-content') . '">Inline Content</a>' : 'Inline Content';
					break;
				case 'share_sticky_bar':
					$nicenames_array[] 	= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-sticky-bar') . '">Sticky Bar</a>' : 'Sticky Bar';
					$nicenames_csv		.= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-sticky-bar') . '">Sticky Bar</a>' : 'Sticky Bar';
					break;
				case 'share_pop_up':
					$nicenames_array[] 	= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-pop-up') . '">Pop-up</a>' : 'Pop-up';
					$nicenames_csv		.= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-pop-up') . '">Pop-up</a>' : 'Pop-up';
					break;
				case 'follow_widget':
					$nicenames_array[] 	= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-follow-widget') . '">Follow Widget</a>' : 'Follow Widget';
					$nicenames_csv		.= ( $include_links ) ? '<a href="' . admin_url( 'admin.php?page=dpsp-follow-widget') . '">Follow Widget</a>' : 'Follow Widget';
					break;
				default:
					$skip_comma = true;
					break;
			}

			if ( !$skip_comma ) {
				$nicenames_csv .= ( $index < count( $active_tools ) ) ? ', ' : '';
			}

			$index++;
		}

		if (substr($nicenames_csv, -2) === ', ') {
			$nicenames_csv = substr($nicenames_csv, 0, -2);
		}

	}

	if ( $format == 'array' ) {
		return $nicenames_array;
	} else {
		return $nicenames_csv; // CSV
	}

}

/**
 * Checks to see if the tool settings is active or not.
 *
 * @param string $tool_slug
 *
 * @return bool
 */
function dpsp_is_tool_active( $tool_slug ) {
	$active_tools = dpsp_get_active_tools();

	return in_array( $tool_slug, $active_tools, true );
}

/**
 * If Pro, check the status of the user's license to determine if the settings should show
 * If Lite, always show settings
 * @return bool
 */
function dpsp_should_show_settings() {
	if ( \Social_Pug::is_free() ) return true;

	$license_key 		 = get_option( 'mv_grow_license' );
	$license_status      = get_option( 'mv_grow_license_status' );
	$show_settings		 = ( ! empty( $license_key) && ( 'invalid' !== $license_status && 'disabled' !== $license_status ) ) ? true : false;

	return $show_settings;
}

/**
 * Checks to see if the legacy (non-db check) tool setting is active or not.
 *
 * @param array $tool Full details of the tool
 *
 * @return bool
 */
function dpsp_is_legacy_tool_active( $tool ) {
	// Only proceed if we have an activation setting
	if ( empty( $tool['activation_setting'] ) ) {
		return false;
	}

	$setting     = $tool['activation_setting'];
	$option_name = explode( '[', $setting );
	$option_name = $option_name[0];
	$settings    = Mediavine\Grow\Settings::get_setting( $option_name );

	if ( isset( $settings[ str_replace( [ $option_name, '[', ']' ], '', $setting ) ] ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Activates a network location.
 */
function dpsp_activate_tool() {
	$tool = dpsp_validate_tool_ajax_action();

	dpsp_update_combined_tools_active_setting(
		$tool, function ( $active_tools, $tool ) {
		if ( ! in_array( $tool, $active_tools, true ) ) {
			array_push( $active_tools, $tool );
		}

		return $active_tools;
		}
	);

	dpsp_update_tool_activation_setting(
		$tool, function ( $settings, $active_option, $is_active ) {
		if ( ! $is_active ) {
			$settings[ $active_option ] = 1;
		}
		return $settings;
		}
	);

	echo 1;
	wp_die();
}


/**
 * Deactivates a network location.
 */
function dpsp_deactivate_tool() {
	$tool = dpsp_validate_tool_ajax_action();

	dpsp_update_combined_tools_active_setting(
		$tool, function ( $active_tools, $tool ) {
		$key = array_search( $tool, $active_tools, true );
		if ( false !== $key ) {
			unset( $active_tools[ $key ] );
			$active_tools = array_values( $active_tools );
		}

		return $active_tools;
		}
	);

	dpsp_update_tool_activation_setting(
		$tool, function ( $settings, $active_option, $is_active ) {
		if ( $is_active ) {
			unset( $settings[ $active_option ] );
		}
		return $settings;
		}
	);
	echo 1;
	wp_die();
}

/**
 * Update the dpsp_active_tools setting object with a de-duplicated list of active tools using the passed callback
 *
 * @param string $tool Tool slug that may be added or removed (or any other action) based on the callback
 * @param callable $callback Function to be called with the active tools and tool slug as parameters
 *
 * @return bool Whether or not the value was successfully updated
 */
function dpsp_update_combined_tools_active_setting( $tool, $callback ) {
	$active_tools = Mediavine\Grow\Settings::get_setting( 'dpsp_active_tools', [] );
	/**
	 * Callback to determine how the active tools array is modified
	 *
	 * @param string[] $active_tools Array of slugs of the currently active tools
	 * @param string $slug The tool in question to modify the tools array, typically removing or adding it.
	 *
	 * @var callable $callback
	 */
	$active_tools = $callback( $active_tools, $tool );
	$active_tools = array_unique( $active_tools );

	return update_option( 'dpsp_active_tools', $active_tools );
}


/**
 * Function ot validate an AJAX activation or deactivation action for a tool. Validates nonce and pulls tool slug from response
 *
 * @return mixed Tool slug if validation passes, process killed if it doesn't
 */
function dpsp_validate_tool_ajax_action() {
	$token = filter_input( INPUT_POST, 'dpsptkn' );
	if ( empty( $token ) || ! wp_verify_nonce( $token, 'dpsptkn' ) ) {
		echo 0;
		wp_die();
	}

	$tool = htmlspecialchars( $_POST['tool'] );

	if ( empty( $tool ) ) {
		echo 0;
		wp_die();
	}

	return $tool;
}

/**
 * Update the activation setting within a singular tool's setting
 * This function corresponds to what dpsp_is_legacy_tool_active checks for
 *
 * @param string $tool Slug for this tool
 * @param callable $update_callback function that will be called with the settings, the activation option, and whether or not the tool is currently active
 * @return boolean True if the update succeeds, false if the tool doesn't have an activation setting or the update fails.
 */
function dpsp_update_tool_activation_setting( $tool, $update_callback ) {
	$tools = dpsp_get_tools();
	// Update the activation setting if there is one
	$tool_setting = ( ! empty( $tools[ $tool ]['activation_setting'] ) ? $tools[ $tool ]['activation_setting'] : '' );
	if ( empty( $tool_setting ) ) {
		return false;
	}
	$option_name = explode( '[', $tool_setting );
	$option_name = $option_name[0];

	$settings      = Mediavine\Grow\Settings::get_setting( $option_name );
	$active_option = str_replace( [ $option_name, '[', ']' ], '', $tool_setting );

	$is_active = isset( $settings[ $active_option ] );

	/**
	 * @param array $settings Current Settings for the tool
	 * @param string $active_option The key for the activation option on the settings array
	 * @param bool $is_active Whether or not the tool is currently active
	 *
	 * @return array $updated_settings the Settings after being modified from the callback
	 */
	$updated_settings = $update_callback( $settings, $active_option, $is_active );

	return update_option( $option_name, $updated_settings );

}


/**
 * Register hooks for functions-tools.php.
 */
function dpsp_register_functions_tools() {
	add_action( 'wp_ajax_dpsp_activate_tool', 'dpsp_activate_tool' );
	add_action( 'wp_ajax_dpsp_deactivate_tool', 'dpsp_deactivate_tool' );
}
