<?php

use Mediavine\Grow\View_Loader;
use Mediavine\Grow\Networks;
use Mediavine\Grow\Share_Counts;

/**
 * Displays the HTML of the plugin admin header.
 */
function dpsp_admin_header() {
	if ( empty( filter_input( INPUT_GET, 'page' ) ) ) {
		return;
	}

	if ( strpos( filter_input( INPUT_GET, 'page' ), 'dpsp' ) === false ) {
		return;
	}

	$page = esc_attr( wp_unslash( trim( filter_input( INPUT_GET, 'page' ) ) ) );

	echo wp_kses( dpsp_get_admin_header( $page ), View_Loader::get_allowed_tags() );
}

/** 
 * Saves and activates Hubbub Lite license
 */
function dpsp_ajax_lite_save_and_activate_license() {
	$dpsp_nonce_lite_save_and_activate_license = filter_input( INPUT_POST, 'dpsp_nonce_lite_save_and_activate_license' );
	if ( empty( $dpsp_nonce_lite_save_and_activate_license ) || ! wp_verify_nonce( $dpsp_nonce_lite_save_and_activate_license, 'dpsp_nonce_lite_save_and_activate_license' ) ) {
		error_log('Nonce failed');
		echo 0;
		wp_die();
	}

	$license_key = filter_input( INPUT_POST, 'license_key' );

	if ( empty( $license_key ) ) {
		error_log('empty license');
		echo 0;
		wp_die();
	}

	require_once 'class-activation-lite.php';

	$dpsp_settings   = Mediavine\Grow\Settings::get_setting( 'dpsp_settings', 'not_set' );
	$mv_grow_license = Mediavine\Grow\Settings::get_setting( 'mv_grow_license', null );

	$dpsp_settings['mv_grow_license'] = $license_key;
	$mv_grow_license = $license_key;

	update_option( 'dpsp_settings', $dpsp_settings );
	update_option( 'mv_grow_license', $mv_grow_license );

	$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
	$hubbub_activation_lite->check_license();

	echo 1;
	wp_die();
}

/**
 * Returns the HTML of the plugin admin header.
 *
 * @param string $page The current page that the header will appear on
 */
function dpsp_get_admin_header( string $page ) : string {

	$is_lite_registered = false;

	if ( ! Social_Pug::is_free() ) {
		$hubbub_activation 	= new \Mediavine\Grow\Activation;
		$license_tier 		= $hubbub_activation->get_license_tier();
	} else {
		$license_tier 		= 'Lite';

		$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
		if ( $hubbub_activation_lite->is_lite_registered() ) {
			$is_lite_registered = true;
		}
	}
	
	$logo_base_url 		= DPSP_PLUGIN_DIR_URL . 'assets/dist/hubbub-logo-white.svg?' . MV_GROW_VERSION;

	// translators: %1$s is replaced by the type of logo (e.g. Hubbub).
	$logo_alt           = esc_attr( sprintf( __( '%1$s logo', 'mediavine' ), __( 'Hubbub', 'mediavine' ) ) );
	$logo_src           = esc_attr( $logo_base_url );
	$html_version       = esc_html( MV_GROW_VERSION );
	$html_version		.= ( $is_lite_registered ) ? ' Unlocked' : '';
	$documentation_href = esc_attr( dpsp_get_documentation_link( $page ) );
	$html_tier 			= esc_html( ( ! $license_tier ) ? 'Pro' : ucfirst( $license_tier ) );
	$unlock_class		= ( ! Social_Pug::is_free() || $is_lite_registered ) ? ' hidden' : '';

	$result = /** @lang HTML */ <<<HTML
<div class="dpsp-page-header">
	<span class="dpsp-logo">
	<img alt="{$logo_alt}" class="mv-grow-logo" src="{$logo_src}">

	<span class="dpsp-logo-inner">Hubbub {$html_tier}</span>
	<small class="dpsp-version">v.{$html_version}</small>
	</span>

	<nav>
	<a href="#" id="dpsp-button-unlock-features" class="{$unlock_class}" title="Register Hubbub Lite to unlock additional features free" target="_blank">🔓 Get More Features Free</a>
	<a href="{$documentation_href}" title="Read our Support Doc for help with this Hubbub settings page" target="_blank"><i class="dashicons dashicons-book"></i>Need help?</a>
	</nav>
	</div>
HTML;
	return $result;
}

/**
 * Returns the link to the docs depending on the page the user is on.
 *
 * @param string $page Page slug used to determine external documentation URL.
 *
 * @return string
 */
function dpsp_get_documentation_link( string $page ) : string {
	$page = str_replace( 'dpsp-', '', $page );

	switch ( $page ) {
		case 'sidebar':
			$url = 'https://morehubbub.com/docs/how-to-add-social-sharing-buttons-as-a-floating-sidebar/';
			break;
		case 'content':
			$url = 'https://morehubbub.com/docs/how-to-add-social-share-buttons-before-and-after-your-posts-content/';
			break;
		case 'sticky-bar':
		case 'mobile':
			$url = 'https://morehubbub.com/docs/sticky-bar-sharing-buttons/';
			break;
		case 'pinterest-images':
			$url = 'https://morehubbub.com/docs/how-to-add-a-pin-it-button-to-your-posts-images/';
			break;
		case 'dashboard':
			$url = 'https://morehubbub.com/docs/how-to-use-the-hubbub-dashboard/';
			break;
		case 'follow-widget':
			$url = 'https://morehubbub.com/docs/using-the-follow-widget-in-social-pro/';
			break;
		case 'pop-up':
			$url = 'https://morehubbub.com/docs/pop-up-sharing-buttons/';
			break;
		case 'settings':
			$url = 'https://morehubbub.com/docs/social-pro-general-settings/';
			break;
		case 'toolkit':
			$url = 'https://morehubbub.com/docs/getting-started-with-hubbub-pro/';
			break;
		case 'email-save-this':
			$url = 'https://morehubbub.com/docs/how-to-use-save-this/';
			break;
		case 'import-export':
			$url = 'https://morehubbub.com/docs/import-export/';
			break;
		default:
			$url = 'https://morehubbub.com/docs/';
			break;
	}

	return $url . '?utm_source=hubbub_plugin&utm_content=settings_page_header_button';
}

/**
 * Displays the HTML for a given tool.
 *
 * @param string $tool_slug Slug for the tool we are outputting
 * @param array  $tool An array of data about the tool
 */
function dpsp_output_tool_box( string $tool_slug, array $tool ) : void {
	$grow_url     = 'https://morehubbub.com';
	$is_extension = empty( $tool['admin_page'] );
	$box_class    = Social_Pug::is_free() && ! $is_extension ? 'dpsp-col-3-8' : 'dpsp-col-1-4';
	echo '<div class="' . esc_attr( $box_class ) . '">';
	echo '<div class="dpsp-tool-wrapper dpsp-card ' . ( $is_extension ? 'dpsp-unavailable' : '' ) . '">';

	if ( $is_extension ) {
		if ( empty( $tool['url'] ) ) {
			$tool['url'] = $grow_url;
		}

		echo '<a href="' . esc_url( $tool['url'] ) . '">';
	}

	// Tool image
	echo '<img src="' . esc_url( strpos( $tool['img'], 'http' ) === false ? DPSP_PLUGIN_DIR_URL . $tool['img'] : $tool['img'] ) . '" />';

	if ( $is_extension ) {
		echo '</a>';
	}

	// Tool name
	echo '<h4 class="dpsp-tool-name">' . esc_html( $tool['name'] ) . '</h4>';

	if ( ! empty( $tool['desc'] ) ) {
		echo '<p class="dpsp-description">' . esc_html( $tool['desc'] ) . '</p>';
	}

	$tool_active = dpsp_is_tool_active( $tool_slug );

	// Tool actions
	echo '<div class="dpsp-tool-actions dpsp-card-footer dpsp-' . ( (bool) $tool_active ? 'active' : 'inactive' ) . '">';

	if ( ! $is_extension ) {
		// Tool admin page
		echo '<a class="dpsp-tool-settings" href="' . esc_url( admin_url( $tool['admin_page'] ) ) . '"><i class="dashicons dashicons-admin-generic"></i>' . esc_html__( 'Settings', 'social-pug' ) . '</a>';

		if ( ! Social_Pug::is_free() ) {
			$hubbub_activation 	= new \Mediavine\Grow\Activation;
			$license_tier 		= $hubbub_activation->get_license_tier();
		} else {
			$license_tier 		= 'Lite';

			$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
			if ( $hubbub_activation_lite->is_lite_registered() ) {
				$license_tier .= '+';
			}
		}

		$show_switch = true;

		if ( $tool_slug == 'follow_widget' && $license_tier == 'Lite' ) {
			echo '<p style="text-align: center; font-size: 15px; font-weight: bold; margin: 0 0 10px 0;">Add Follow Buttons to your website</p>';
			echo '<div style="text-align: center"><a style="margin-bottom: 10px;" class="dpsp-button-secondary dpsp-button-unlock-hubbub-lite" id="dpsp-button-unlock-features-follow-widget-tool" target="_blank" href="" title="Register Hubbub Lite to unlock additional features free">🔓 Get More Features Free</a></div>';

			$show_switch = false;
		}

		if ( $tool_slug == 'email_save_this' && ( $license_tier == 'pro' || ! $license_tier ) ) {
			echo '<p style="text-align: center; font-size: 15px; font-weight: bold; margin: 0 0 10px 0;">Upgrade to Plus or Priority to get Save This</p>';
			echo '<div style="text-align: center"><a style="margin-bottom: 10px;" class="dpsp-button-primary" target="_blank" href="https://morehubbub.com/save-this/?utm_source=hubbub_plugin&utm_content=save_this_announce_learn_more_button" title="Learn more about the Save This tool and our Pro+ and Priority licenses">Learn More</a><br/>';
			$check_license_url = esc_attr( add_query_arg( [ '_wpnonce' => wp_create_nonce( 'dpsp_check_license' ), 'dpsp_check_license' => 'dpsp_check_license' ] ), remove_query_arg( ['_wpnonce', 'dpsp_check_license' ], $_SERVER['REQUEST_URI'] ) );
			echo 'Already upgraded? <a style="text-decoration: underline; color: #2271b1;" class="dpsp-get-license" href="' . $check_license_url . '">Refresh your license.</a></div>';

			$show_switch = false;
		}
		
		if ( $show_switch ) {
			// Tool activation switch
			echo '<div class="dpsp-switch small">';

			echo( (bool) $tool_active ? '<span>' . esc_html__( 'Active', 'social-pug' ) . '</span>' : '<span>' . esc_html__( 'Inactive', 'social-pug' ) . '</span>' );
			echo '<input id="dpsp-' . esc_attr( $tool_slug ) . '-active" data-tool="' . esc_attr( $tool_slug ) . '" data-tool-activation="' . esc_attr( ! empty( $tool['activation_setting'] ) ? $tool['activation_setting'] : '' ) . '" class="cmn-toggle cmn-toggle-round" type="checkbox" value="1"' . ( $tool_active ? 'checked' : '' ) . ' />';
			echo '<label for="dpsp-' . esc_attr( $tool_slug ) . '-active"></label>';

			echo '</div>';
		}

		
	} else {
		if ( empty( $tool['url'] ) ) {
			$tool['url'] = $grow_url;
		}

		echo '<a href="' . esc_url( $tool['url'] ) . '" class="dpsp-button-primary">' . esc_html__( 'Learn More', 'social-pug' ) . '</a>';

	}
	echo '</div>';

	echo '</div>';
	echo '</div>';
}

/**
 * Function that displays the HTML for a settings field.
 *
 * @param string $type The type of settings field
 * @param string $name Name for the settings field
 * @param mixed  $saved_value The current value saved in the database for this setting
 * @param string $label The label for the setting
 * @param array  $options Options for setting types that feature different choices
 * @param string $tooltip Tooltip to display for the setting
 * @param array  $editor_settings Settings for the tinyMCE editor that will be passed to the editor rendered when setting type is 'editor'
 * @param string $disabled This string will be rendered in the input attribute if the type is select or text, used to pass a disabled attribute
 */
function dpsp_settings_field( string $type, string $name, $saved_value = '', string $label = '', array $options = [], string $tooltip = '', array $editor_settings = [], string $disabled = '' ) : void {

	$settings_field_slug = ( ! empty( $label ) ? strtolower( str_replace( ' ', '-', $label ) ) : '' );

	echo '<div class="dpsp-setting-field-wrapper dpsp-setting-field-' . esc_attr( $type ) . ( is_array( $options ) && count( $options ) === 1 ? ' dpsp-single' : ( is_array( $options ) && count( $options ) > 1 ? ' dpsp-multiple' : '' ) ) . ' ' . ( ! empty( $label ) ? 'dpsp-has-field-label dpsp-setting-field-' . esc_attr( $settings_field_slug ) : '' ) . '">';

	switch ( $type ) {
		// Display input type text
		case 'text':
			echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
			echo '<input type="text" ' . ( ! empty( $label ) ? 'id="' . esc_attr( $name ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" value="' . esc_attr( $saved_value ) . '" ' . esc_attr( $disabled ) . ' />'; // @todo WordPress' disabled() function should be used instead
			break;

		// Display textareas
		case 'textarea':
			echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
			echo '<textarea ' . ( ! empty( $label ) ? 'id="' . esc_attr( $name ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '">' . esc_textarea( $saved_value ) . '</textarea>';
			break;

		// Display wp_editors
		case 'editor':
			echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
			wp_editor( $saved_value, $name, $editor_settings );
			break;

		// Display input type radio
		case 'radio':
			echo ! empty( $label ) ? '<label class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
			if ( ! empty( $options ) ) {
				foreach ( $options as $option_value => $option_name ) {
					echo '<input type="radio" id="' . esc_attr( $name ) . '[' . esc_attr( $option_value ) . ']" name="' . esc_attr( $name ) . '" value="' . esc_attr( $option_value ) . '" ' . checked( $option_value, $saved_value, false ) . ' />';
					echo '<label for="' . esc_attr( $name ) . '[' . esc_attr( $option_value ) . ']" class="dpsp-settings-field-radio">' . ( isset( $option_name ) ? esc_attr( $option_name ) : esc_attr( $option_value ) ) . '<span></span></label>';
				}
			}
			break;

		// Display input type checkbox
		case 'checkbox':
			// If no options are passed make the main label as the label for the checkbox
			if ( count( $options ) === 1 ) {
				if ( is_array( $saved_value ) ) {
					$saved_value = $saved_value[0];
				}
				echo '<input type="checkbox" ' . ( ! empty( $label ) ? 'id="' . esc_attr( $name ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" value="' . esc_attr( $options[0] ) . '" ' . checked( $options[0], $saved_value, false ) . ' />';
				echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '<span></span></label>' : '';

				// Else display checkboxes just like radios
			} else {
				echo ! empty( $label ) ? '<label class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
				if ( ! empty( $options ) ) {
					foreach ( $options as $option_value => $option_name ) {
						echo '<input type="checkbox" id="' . esc_attr( $name ) . '[' . esc_attr( $option_value ) . ']" name="' . esc_attr( $name ) . '" value="' . esc_attr( $option_value ) . '" ' . ( in_array( $option_value, $saved_value, true ) ? 'checked' : '' ) . ' />';
						echo '<label for="' . esc_attr( $name ) . '[' . esc_attr( $option_value ) . ']" class="dpsp-settings-field-checkbox">' . ( isset( $option_name ) ? esc_attr( $option_name ) : esc_attr( $option_value ) ) . '<span></span></label>';
					}
				}
			}
			break;

		// Display switch
		case 'switch':
			if ( count( $options ) === 1 ) {
				if ( is_array( $saved_value ) ) {
					$saved_value = $saved_value[0];
				}
				echo '<div class="dpsp-switch">';
				echo '<input type="checkbox" ' . ( ! empty( $label ) ? 'id="' . esc_attr( $name ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" class="cmn-toggle cmn-toggle-round" value="' . esc_attr( $options[0] ) . '" ' . checked( $options[0], $saved_value, false ) . ' />';
				echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '"></label>' : '';
				echo '</div>';
				echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '<span></span></label>' : '';
			}
			break;

		case 'select':
			echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
			echo '<select id="' . esc_attr( $name ) . '" name="' . esc_attr( $name ) . '"' . esc_attr( $disabled ) . '>';
			foreach ( $options as $option_value => $option_name ) {
				echo '<option value="' . esc_attr( $option_value ) . '" ' . selected( $saved_value, $option_value, false ) . '>' . esc_html( $option_name ) . '</option>';
			}
			echo '</select>';
			break;

		case 'color-picker':
			echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
			echo '<input class="dpsp-color-picker" type="text" ' . ( ! empty( $label ) ? 'id="' . esc_attr( $name ) . '"' : '' ) . ' name="' . esc_attr( $name ) . '" value="' . esc_attr( $saved_value ) . '" />';
			break;

		case 'image':
			echo ! empty( $label ) ? '<label for="' . esc_attr( $name ) . '" class="dpsp-setting-field-label">' . esc_html( $label ) . '</label>' : '';
			echo '<div>';
			if ( ! empty( $saved_value['id'] ) ) {
				$thumb_details = wp_get_attachment_image_src( $saved_value['id'], 'medium' );
				$image_details = wp_get_attachment_image_src( $saved_value['id'], 'full' );
			}
			if ( ! empty( $thumb_details[0] ) && ! empty( $image_details[0] ) ) {
				$thumb_src = $thumb_details[0];
				$image_src = $image_details[0];
			} else {
				$thumb_src         = '';
				$image_src         = '';
				$saved_value['id'] = '';
			}
			echo '<div>';
			echo '<img src="' . esc_url( $thumb_src ) . '">';
			echo '</div>';
			echo '<a class="dpsp-image-select button button-primary ' . ( ! empty( $saved_value['id'] ) ? 'hidden' : '' ) . '" href="#">' . esc_html__( 'Select Image', 'social-pug' ) . '</a>';
			echo '<a class="dpsp-image-remove button button-secondary ' . ( empty( $saved_value['id'] ) ? 'hidden' : '' ) . '" href="#">' . esc_html__( 'Remove Image', 'social-pug' ) . '</a>';
			echo '<input class="dpsp-image-id" type="hidden" name="' . esc_attr( $name ) . '[id]" value="' . esc_attr( $saved_value['id'] ) . '" />';
			echo '<input class="dpsp-image-src" type="hidden" name="' . esc_attr( $name ) . '[src]" value="' . esc_attr( $image_src ) . '" />';
			echo '</div>';
			break;
	} // end of switch

	// Tooltip
	if ( ! empty( $tooltip ) ) {
		dpsp_output_backend_tooltip( $tooltip );
	}

	do_action( 'dpsp_inner_after_settings_field', $settings_field_slug, $type, $name );
	echo '</div>';
}


/**
 * Set the column_count option to 1 when displaying the buttons inside the WP dashboard admin
 *
 * @param array  $settings - the settings array for the current location
 * @param string $action - the current type of action ( share/follow )
 * @param string $location - the display location for the buttons
 *
 * @return mixed
 */
function dpsp_admin_buttons_display_column_count_to_one( $settings, $action, $location ) {
	if ( empty( $settings['display']['column_count'] ) ) {
		return $settings;
	}
	if ( ! is_admin() ) {
		return $settings;
	}

	$settings['display']['column_count'] = 1;

	return $settings;
}

/**
 * Returns the HTML output with the selectable networks.
 *
 * @param array $networks - the networks available to be sorted
 * @param array $settings_networks - the networks saved for the location
 *
 * @return string
 */
function dpsp_output_selectable_networks( array $networks = [], array $settings_networks = [] ) : string {
	$networks_container = Networks::get_instance();
	$output             = '<div id="dpsp-networks-selector-wrapper">';
	$output            .= '<ul id="dpsp-networks-selector">';

	if ( ! empty( $networks ) ) {
		foreach ( $networks as $network_slug => $network_name ) {
			$network = $networks_container->get( $network_slug );
			if ( ! $network ) {
				continue;
			}
			$tooltip = $network->get_tooltip();
			$output .= '<li>';
			$output .= '<div class="dpsp-network-item" data-network="' . $network_slug . '" data-network-name="' . $network->get_name() . '" ' . ( isset( $settings_networks[ $network_slug ] ) ? 'data-checked="true"' : '' ) . '>';
			$output .= '<div class="dpsp-network-item-checkbox dpsp-icon-ok">' . dpsp_get_svg_icon_output( 'ok' ) . '</div>';
			
			/** 
			 * Get settings to determine Twitter or X
			 */
			$network_slug_background_color = $network_slug;
			if ( $network_slug == 'twitter' ):
				$settings = \Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );
				if ( empty($settings['twitter_4ever']) ) :
					$network_slug_background_color = 'x';
				endif;
			endif;

			$output .= '<div class="dpsp-network-item-name-wrapper dpsp-network-' . $network_slug . ' dpsp-background-color-network-' . $network_slug_background_color . '">';
			$output .= '<span class="dpsp-list-icon dpsp-list-icon-social dpsp-icon-' . $network_slug_background_color . ' dpsp-background-color-network-' . $network_slug_background_color . '">' . dpsp_get_svg_icon_output( $network_slug ) . '</span>';
			$output .= '<h4>' . $network->get_name() . '</h4>';
			$output .= '</div>';
			if ( ! empty( $tooltip ) ) {
				$output .= dpsp_output_backend_tooltip( $tooltip, true );
			}
			$output .= '</li>';

		}
	}

	$output .= '</ul>';
	$output .= '<div id="dpsp-networks-selector-footer" class="dpsp-card-footer">';
	$output .= '<a href="#" class="dpsp-button-primary">' . esc_html__( 'Apply Selection', 'social-pug' ) . '</a>';
	if ( \Social_Pug::is_free() ) {
		$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
		if ( ! $hubbub_activation_lite->is_lite_registered() ) {
			$output .= '<a href="#" class="dpsp-button-secondary dpsp-button-unlock-hubbub-lite" id="dpsp-button-unlock-features-network-selector">' . esc_html__( '🔓 Unlock More Networks Free!', 'social-pug' ) . '</a>';
		}
	}
	$output .= '</div>';
	$output .= '</div>';

	return $output;
}


/**
 * Returns the HTML output with the sortable networks.
 *
 * @param array  $networks The networks to sort
 * @param string $settings_name The name for the setting that the sorted networks will be saved to
 *
 * @return string
 */
function dpsp_output_sortable_networks( array $networks, string $settings_name = '', array $missing_accounts = [] ) : string {
	$networks_container = Networks::get_instance();
	$output             = '<ul class="dpsp-social-platforms-sort-list sortable">';
	$current_network    = 1;
	if ( ! empty( $networks ) ) {

		foreach ( $networks as $network_slug => $network_name ) {
			$network = $networks_container->get( $network_slug );
			if ( ! $network ) {
				continue;
			}
			$output .= '<li data-network="' . esc_attr( $network_slug ) . '" ' . ( count( $networks ) === $current_network ? 'class="dpsp-last"' : '' ) . '>';

			// The sort handle
			$output .= '<div class="dpsp-sort-handle"><!-- --></div>';

			/** 
			 * Get settings to determine Twitter or X
			 */
			$network_slug_background_color = $network_slug;
			$network_label = $networks[ $network_slug ]['label'] ? $networks[ $network_slug ]['label'] : $network->get_label(); // This will load the default label, unless they've customized it
			if ( $network_slug == 'twitter' ):
				$settings = \Mediavine\Grow\Settings::get_setting( 'dpsp_settings', [] );
				if ( empty($settings['twitter_4ever']) ) :
					$network_slug_background_color = 'x';
					if ( $network_label == 'Twitter' ) : // This means the person didn't customize the label, make it X
						$network_label = 'X';
					endif;
				else :
					if ( $network_label == 'X' ) : // The person has switched back to Twitter from X, and didn't customize the label
						$network_label = 'Twitter';
					endif;
				endif;
			endif;

			// The social network icon
			$output .= '<div class="dpsp-list-icon dpsp-list-icon-social dpsp-icon-' . esc_attr( $network_slug_background_color ) . ' dpsp-background-color-network-' . esc_attr( $network_slug_background_color ) . '">' . dpsp_get_svg_icon_output( $network_slug ) . '</div>';

			if ( $settings_name == 'dpsp_location_follow_widget' && in_array( $network_slug, $missing_accounts ) ) {
				$output .= '<div class="dpsp-list-icon-social-missing-account" title="Network information missing in settings.">⚠️</div>';
			}

			// The label edit field
			$output .= '<div class="dpsp-list-input-wrapper">';
			$output .= '<input type="text" placeholder="' . __( 'This button has no label text.', 'social-pug' ) . '" name="' . esc_attr( $settings_name ) . '[networks][' . $network_slug . '][label]" value="' . ( esc_attr( $network_label ) ) . '" />';
			$output .= '</div>';

			// List item actions
			$output .= '<div class="dpsp-list-actions">';
			$output .= '<a class="dpsp-list-edit-label" href="#"><span class="dashicons dashicons-edit"></span>' . esc_html__( 'Edit Label', 'social-pug' ) . '</a>';
			$output .= '<a class="dpsp-list-remove" href="#"><span class="dashicons dashicons-no-alt"></span>' . esc_html__( 'Remove', 'social-pug' ) . '</a>';
			$output .= '</div>';
			$output .= '</li>';

			$current_network ++;

		}
	}

	$output .= '</ul>';

	return $output;
}


/**
 * Outputs the HTML of the tooltip
 *
 * @param string $tooltip - the text of the tooltip
 * @param bool   $return - whether to return or to output the HTML
 *
 * @return string
 */
function dpsp_output_backend_tooltip( string $tooltip = '', bool $return = false ) : ?string {
	$output  = '<div class="dpsp-setting-field-tooltip-wrapper ' . ( ( strpos( $tooltip, '</a>' ) !== false ) ? 'dpsp-has-link' : '' ) . '">';
	$output .= '<span class="dpsp-setting-field-tooltip-icon"></span>';
	$output .= '<div class="dpsp-setting-field-tooltip dpsp-transition">' . $tooltip . '</div>';
	$output .= '</div>';

	if ( $return ) {
		return $output;
	} else {
		echo wp_kses( $output, View_Loader::get_allowed_tags() );
	}

	return null;
}

/**
 * Registers an extra column for the shares with all active custom post types.
 */
function dpsp_register_custom_post_type_columns() {
	$active_post_types = dpsp_get_active_post_types();

	if ( ! empty( $active_post_types ) ) {
		foreach ( $active_post_types as $post_type ) {
			add_filter( 'manage_' . $post_type . '_posts_columns', 'dpsp_set_shares_column' );
			add_filter( 'manage_edit-' . $post_type . '_sortable_columns', 'dpsp_set_shares_column_sortable' );
			add_action( 'manage_' . $post_type . '_posts_custom_column', 'dpsp_output_shares_column', 10, 2 );
		}
	}
}

/**
 * Adds the Shares column to all active post types.
 *
 * @param array $columns The current columns in the post editor screen
 *
 * @return array
 */
function dpsp_set_shares_column( $columns ) {
	$column_output = '<span class="dpsp-list-table-shares"><i class="dashicons dashicons-share"></i><span>' . __( 'Shares', 'social-pug' ) . '</span></span>';

	if ( isset( $columns['date'] ) ) {
		$array = array_slice( $columns, 0, array_search( 'date', array_keys( $columns ), true ) );

		$array['dpsp_shares'] = $column_output;

		$columns = array_merge( $array, $columns );
	} else {
		$columns['dpsp_shares'] = $column_output;
	}

	return $columns;
}

/**
 * Defines the total shares column as sortable.
 *
 * @param array $columns The current sortable columns
 *
 * @return array
 */
function dpsp_set_shares_column_sortable( $columns ) {
	$columns['dpsp_shares'] = 'dpsp_shares';

	return $columns;
}

/**
 * Outputs the share counts in the Shares columns.
 *
 * @param string $column_name The column name, this function will not do anything if it's not the share count column
 * @param int    $post_id The post id for this row
 */
function dpsp_output_shares_column( string $column_name, int $post_id ) : void {
	if ( 'dpsp_shares' === $column_name ) {
		echo '<span class="dpsp-list-table-post-share-count">' . esc_html( Share_Counts::post_total_share_counts( $post_id ) ) . '</span>';
	}
}


/**
 * Check to see if the user selected to order the posts by share counts and changes the query accordingly.
 *
 * @param WP_Query $query The current Query
 */
function dpsp_pre_get_posts_shares_query( $query ) {
	if ( ! is_admin() ) {
		return;
	}

	$orderby = $query->get( 'orderby' );
	if ( 'dpsp_shares' === $orderby ) {
		$query->set( 'meta_key', 'dpsp_networks_shares_total' );
		$query->set( 'orderby', 'meta_value_num' );
	}
}

/**
 * Makes a call to Facebook to scrape the post's Open Graph data after the post has been saved.
 *
 * @param int     $post_id the post id to scrape
 * @param WP_Post $post The post itself
 */
function dpsp_save_post_facebook_scrape_url( $post_id, $post ) : void {
	if ( ! is_admin() ) {
		return;
	}

	/**
	 * Allow 3rd party plugins to add their CPTs to the excluded list
	 *
	 * @param array Array of excluded post types
	 *
	 * @return array
	 */
	$not_allowed_post_types = apply_filters( 'mv_grow_not_allowed_post_types', [ 'nav_menu_item', 'wprm_recipe' ] );
	if ( in_array( $post->post_type, $not_allowed_post_types, true ) ) {
		return;
	}

	$not_allowed_post_statuses = [ 'draft', 'auto-draft', 'future', 'pending', 'trash' ];
	if ( in_array( $post->post_status, $not_allowed_post_statuses, true ) ) {
		return;
	}

	$post_url = get_permalink( $post );
	$post_url = rawurlencode( $post_url );

	$url = add_query_arg(
		[
			'id'     => $post_url,
			'scrape' => 'true',
		],
		'https://graph.facebook.com/'
	);

	$response = wp_remote_post( $url );
}

/**
 * Output settings sidebar — CTA to upgrade to Pro.
 */
function dpsp_add_submenu_page_sidebar() {
	$icon = '<span class="dpsp-dashicons"><span class="dashicons dashicons-yes"></span></span>';
	$url  = 'https://morehubbub.com/?utm_source=plugin&utm_medium=sidebar&utm_campaign=social-pug';

	echo '<div class="dpsp-settings-sidebar">';
	echo '<div id="dpsp-settings-sidebar-social-pug-pro" class="dpsp-card">';
	echo '<div class="dpsp-card-inner">';

	echo '<img data-pin-nopin="true" alt="A creator makes the heart symbol with his two fingers." src="' . esc_url( DPSP_PLUGIN_DIR_URL . 'assets/dist/hubbub-upgrade.png?' . DPSP_VERSION ) . '" />';

	echo '<h3>' . esc_html__( 'Upgrade to Pro, Pro+, or Priority', 'social-pug' ) . '</h3>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Add a "Pin It" button that appears when visitors hover your in-post images.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Force a custom image to be shared on Pinterest when using the Pinterest button.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Add unlimited hidden Pinterest images to your posts and pages.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . __( 'New: <a href="https://morehubbub.com/save-this/?utm_source=hubbub_plugin&utm_content=save_this_announce_sidebar_button_lite" title="Learn more about the Save This tool" target="_blank">Save This</a>! Add a form users can save the current page via email and add them to your mailing list.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Share to Threads, Mastodon, and 17+ other social networks!', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Attract users to your social media profiles with our Follow Buttons Widget and follow shortcode. You can place it in your sidebar, template files, or anywhere on your site. Buttons include Facebook, X, Pinterest, Threads, Mastodon, LinkedIn, Reddit, Instagram, YouTube, Flipboard, Vimeo, SoundCloud, Twitch, and Behance.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Make your website mobile-friendly with sticky footer social share buttons.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Trigger a pop-up with the social sharing buttons when a user starts to scroll, arrives at the bottom of a post or begins to leave your site.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Add custom pin descriptions and repin IDs to your in-post images.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( "Recover your lost social share counts if you've ever changed your permalink structure.", 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Add unlimited "Click to Tweet" boxes so that your users can share your content on Twitter with just one click.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'Optionally revert X icons to old-school Twitter icons.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . __( 'Get help with support provided by <a href="https://www.nerdpress.net/" title="NerdPress - WordPress support that feels like family">NerdPress</a>.', 'social-pug' ) . '</p>';
	echo '<p>' . wp_kses_post( $icon ) . esc_html__( 'And more...', 'social-pug' ) . '</p>';

	echo '</div>';
	echo '<div class="dpsp-card-footer"><a class="dpsp-button-primary" href="' . esc_url( $url ) . '" target="_blank">' . esc_html__( 'Upgrade', 'social-pug' ) . '</a></div>';
	echo '</div>';
}

/**
 * Register hooks for functions-admin.php.
 */
function dpsp_register_functions_admin() {
	add_action( 'admin_notices', 'dpsp_admin_header', 1 );
	add_filter( 'dpsp_network_buttons_outputter_settings', 'dpsp_admin_buttons_display_column_count_to_one', 10, 3 );
	add_action( 'admin_init', 'dpsp_register_custom_post_type_columns' );
	add_action( 'pre_get_posts', 'dpsp_pre_get_posts_shares_query' );
	add_action( 'save_post', 'dpsp_save_post_facebook_scrape_url', 99, 2 );
}
