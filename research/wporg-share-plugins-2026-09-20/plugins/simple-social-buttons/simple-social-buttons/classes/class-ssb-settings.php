<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Settings class.
 *
 * Handles the settings page and configuration for Simple Social Buttons.
 *
 * @package SimpleSocialButtons
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings class.
 *
 * @since 1.0.0
 */
class Ssb_Settings {

	/**
	 * Settings API instance.
	 *
	 * @var Ssb_Settings_Structure
	 */
	private $settings_api;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		include_once SSB_PLUGIN_DIR . '/classes/class-ssb-settings-structure.php';
		$this->settings_api = new Ssb_Settings_Structure();
		add_action( 'admin_init', array( $this, 'ssb_admin_init' ) );
		add_action( 'admin_menu', array( $this, 'ssb_admin_menu' ) );
		add_action( 'wp_ajax_ssb_help', array( $this, 'ssb_download_help' ) );
		add_action( 'wp_ajax_ssb_export', array( $this, 'ssb_export' ) );
		add_action( 'wp_ajax_ssb_import', array( $this, 'ssb_import' ) );
	}

	/**
	 * Add Admin Menu.
	 *
	 * @version 7.0.1
	 *
	 * @return void
	 */
	public function ssb_admin_menu() {

		// Don't create menu if React admin is active.
		global $ssb_react_admin_active;
		$use_react    = apply_filters( 'ssb_use_react_admin', false );
		$react_active = isset( $ssb_react_admin_active ) && $ssb_react_admin_active;

		if ( $use_react || $react_active ) {
			return;
		}
		if ( current_user_can( 'activate_plugins' ) ) {
			add_menu_page(
				'Simple Social Buttons',
				'Social Buttons',
				'activate_plugins',
				'simple-social-buttons',
				array( $this, 'ssb_plugin_page' ),
				ssb_get_admin_menu_icon(),
				100
			);
			add_submenu_page(
				'simple-social-buttons',
				__( 'Settings', 'simple-social-buttons' ),
				__( 'Settings', 'simple-social-buttons' ),
				'manage_options',
				'simple-social-buttons'
			);
			do_action( 'ssb_add_pro_submenu' );

			add_submenu_page(
				'simple-social-buttons',
				__( 'Help', 'simple-social-buttons' ),
				__( 'Help', 'simple-social-buttons' ),
				'manage_options',
				'ssb-help',
				array( $this, 'ssb_help_page' )
			);

			add_submenu_page(
				'simple-social-buttons',
				__( 'Import and export settings', 'simple-social-buttons' ),
				__( 'Import / Export', 'simple-social-buttons' ),
				'manage_options',
				'ssb-import-export',
				array( $this, 'ssb_import_export_page' )
			);

		}
	}

	/**
	 * Get settings sections.
	 *
	 * @return array Settings sections array.
	 */
	public function get_settings_sections() {
			$sections = array(
				array(
					'id'       => 'ssb_networks',
					'title'    => __( 'Social Buttons', 'simple-social-buttons' ),
					'priority' => '10',
				),
				array(
					'id'       => 'ssb_themes',
					'title'    => __( 'Social Buttons Designs', 'simple-social-buttons' ),
					'priority' => '15',
				),
				array(
					'id'       => 'ssb_positions',
					'title'    => __( 'Social Buttons Positions', 'simple-social-buttons' ),
					'priority' => '20',
				),
				array(
					'id'       => 'ssb_sidebar',
					'title'    => __( 'Sidebar', 'simple-social-buttons' ),
					'priority' => '25',
				),
				array(
					'id'       => 'ssb_media',
					'title'    => __( 'On Media', 'simple-social-buttons' ),
					'priority' => '40',
				),
				array(
					'id'       => 'ssb_popup',
					'title'    => __( 'Popup', 'simple-social-buttons' ),
					'priority' => '45',
				),
				array(
					'id'       => 'ssb_flyin',
					'title'    => __( 'Fly In', 'simple-social-buttons' ),
					'priority' => '50',
				),
				array(
					'id'       => 'ssb_inline',
					'title'    => __( 'InLine', 'simple-social-buttons' ),
					'priority' => '30',
				),
				array(
					'id'       => 'ssb_advanced',
					'title'    => __( 'Additional features', 'simple-social-buttons' ),
					'priority' => '99',
				),
			);

			$setting_section = apply_filters( 'ssb_settings_panel', $sections );

			usort( $setting_section, array( $this, 'ssb_sort_array' ) );

			return $setting_section;
	}

	/**
	 * Sort array by priority.
	 *
	 * @param array $a First array.
	 * @param array $b Second array.
	 * @return int|false Comparison result or false.
	 */
	public function ssb_sort_array( $a, $b ) {
		$first_priority = isset( $a['priority'] ) && ! empty( $a['priority'] ) ? $a['priority'] : false;
		$sec_priority   = isset( $b['priority'] ) && ! empty( $b['priority'] ) ? $b['priority'] : false;

		if ( $first_priority && $sec_priority ) {
			return $first_priority - $sec_priority;
		}

		return false;
	}

	/**
	 * Get current post types.
	 *
	 * @return array Post types array.
	 */
	public function get_current_post_types() {

		$post_types_list = array(
			'home' => __( 'Home', 'simple-social-buttons' ),
		);

		$args = array(
			'public' => true,
		);

		$post_types = get_post_types( $args );

		foreach ( $post_types as $post_type ) {
			$post_types_list[ $post_type ] = ucfirst( $post_type );
		}

		return $post_types_list;
	}

	/**
	 * Returns all the settings fields.
	 *
	 * @return array Settings fields.
	 */
	public function get_settings_fields() {
			$post_types            = $this->get_current_post_types();
			$ssb_positions_options = apply_filters(
				'ssb_positions_options',
				array(
					'sidebar' => __( 'Sidebar', 'simple-social-buttons' ),
					'inline'  => __( 'Inline', 'simple-social-buttons' ),
					'media'   => __( 'Media', 'simple-social-buttons' ),
					'popup'   => __( 'Popup', 'simple-social-buttons' ),
					'flyin'   => __( 'Fly In', 'simple-social-buttons' ),
				)
			);

			$ssb_sidebar = array(
				array(
					'name'     => 'orientation',
					'label'    => __( 'Sidebar Orientation', 'simple-social-buttons' ),
					// phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
					'desc'     => __( '<h4>Display Settings</h4>', 'simple-social-buttons' ),
					'type'     => 'ssb_select',
					'default'  => 'left',
					'options'  => array(
						'left'  => __( 'Left', 'simple-social-buttons' ),
						'right' => __( 'Right', 'simple-social-buttons' ),
					),
					'priority' => '5',
				),
				array(
					'name'     => 'animation',
					'label'    => __( 'Intro Animation', 'simple-social-buttons' ),
					'type'     => 'ssb_select',
					'default'  => 'no-animation',
					'options'  => array(
						'no-animation' => __( 'No', 'simple-social-buttons' ),
						'right-in'     => __( 'From Right', 'simple-social-buttons' ),
						'top-in'       => __( 'From Top', 'simple-social-buttons' ),
						'bottom-in'    => __( 'From Bottom', 'simple-social-buttons' ),
						'left-in'      => __( 'From Left', 'simple-social-buttons' ),
						'fade-in'      => __( 'Fade In', 'simple-social-buttons' ),
					),
					'priority' => '10',
				),
				array(
					'name'     => 'share_counts',
					'help'     => __(
						'<p id="share-count-message" >For Facebook share count you need to add Facebook App id and secret in the Social tab.</p>',
						'simple-social-buttons'
					),
					'label'    => __( 'Display Share Counts', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '15',
				),
				array(
					'name'     => 'total_share',
					'label'    => __( 'Display Total Shares', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '20',
				),
				array(
					'name'     => 'display_hover_text',
					'label'    => __( 'Display Text on hover', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '26',
				),
				array(
					'name'     => 'flat_button_sidebar',
					'label'    => __( 'Flat Button Style', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '27',
				),
				array(
					'name'     => 'icon_space',
					'label'    => __( 'Add Icon Spacing', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '25',
				),
				array(
					'name'              => 'icon_space_value',
					'type'              => 'ssb_text',
					'label'             => __( 'Enter the Space in Pixel', 'simple-social-buttons' ),
					'placeholder'       => '10',
					'sanitize_callback' => 'sanitize_text_field',
					'priority'          => '30',
				),
				array(
					'name'     => 'sticky_mobile_bottom',
					'label'    => __( 'Sticky Bottom (Mobile)', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '25',
				),
				array(
					'name'     => 'hide_mobile',
					'label'    => __( 'Hide On Mobile Devices', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '35',
				),
				array(
					'name'     => 'icon_limit',
					'label'    => __( 'Limit Social Icons', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '36',
				),
				array(
					'name'              => 'icon_limit_value',
					'type'              => 'ssb_text',
					'label'             => __( 'Maximum Icons to Show', 'simple-social-buttons' ),
					'placeholder'       => '3',
					'sanitize_callback' => 'absint',
					'priority'          => '37',
				),
				array(
					'name'     => 'posts',
					'label'    => __( 'Post Type Settings', 'simple-social-buttons' ),
					'desc'     => __( 'Multi checkbox description', 'simple-social-buttons' ),
					'type'     => 'ssb_post_types',
					'default'  => array(
						'post' => 'post',
						'page' => 'page',
					),
					'options'  => $post_types,
					'priority' => '99',
				),
				array(
					'name'  => 'go_pro',
					'type'  => 'ssb_go_pro',
					'label' => __(
						'Want More Control Over Your Sidebar Social Buttons Styling?',
						'simple-social-buttons'
					),
					'desc'  => __(
						'Simple Social Buttons Pro lets you set custom background, hover, and icon colors for every button,
						so they blend naturally with your website\'s design instead of standing out like a default plugin.
						Buttons that fit your site tend to get clicked more, which means more shares and more traffic for you.
						Pro also adds features like buttons on images, popups on exit intent, fly in slide animations, and more.',
						'simple-social-buttons'
					),
					'link'  => 'https://www.WPBrigade.com/wordpress/plugins/simple-social-buttons-pro/?utm_source=simple-social-buttons-lite&utm_medium=settings-sidebar&utm_campaign=pro-upgrade', // phpcs:ignore
				),
			);

			$ssb_sidebar = apply_filters( 'ssb_sidebar_fields', $ssb_sidebar );

			$ssb_inline = array(
				array(
					'name'     => 'location',
					'label'    => __( 'Icon Position', 'simple-social-buttons' ),
					// phpcs:ignore WordPress.WP.I18n.NoHtmlWrappedStrings
					'desc'     => __( '<h4>Display Settings</h4>', 'simple-social-buttons' ),
					'type'     => 'ssb_select',
					'default'  => 'above',
					'options'  => array(
						'above'       => __( 'Above The Content', 'simple-social-buttons' ),
						'below'       => __( 'Below The Content', 'simple-social-buttons' ),
						'above_below' => __( 'Above + Below The Content', 'simple-social-buttons' ),
					),
					'priority' => '5',
				),
				array(
					'name'     => 'icon_alignment',
					'label'    => __( 'Icon Alignment', 'simple-social-buttons' ),
					'type'     => 'ssb_select',
					'default'  => 'left',
					'options'  => array(
						'left'     => __( 'Left', 'simple-social-buttons' ),
						'centered' => __( 'Centered', 'simple-social-buttons' ),
						'right'    => __( 'Right', 'simple-social-buttons' ),
					),
					'priority' => '10',
				),
				array(
					'name'     => 'animation',
					'label'    => __( 'Animation', 'simple-social-buttons' ),
					'type'     => 'ssb_select',
					'default'  => 'no-animation',
					'options'  => array(
						'no-animation' => __( 'No', 'simple-social-buttons' ),
						'bottom-in'    => __( 'From bottom', 'simple-social-buttons' ),
						'top-in'       => __( 'From top', 'simple-social-buttons' ),
						'left-in'      => __( 'From left', 'simple-social-buttons' ),
						'right-in'     => __( 'From right', 'simple-social-buttons' ),
						'fade-in'      => __( 'Fade In', 'simple-social-buttons' ),
					),
					'priority' => '15',
				),
				array(
					'name'     => 'share_counts',
					'help'     => __(
						'<p id="share-count-message" >For Facebook share count you need to add Facebook App id and secret in the Social tab.</p>',
						'simple-social-buttons'
					),
					'label'    => __( 'Display Share Counts', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '20',
				),
				array(
					'name'     => 'total_share',
					'label'    => __( 'Display Total Shares', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '25',
				),
				array(
					'name'     => 'icon_space',
					'label'    => __( 'Add Icon Spacing', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '30',
				),
				array(
					'name'              => 'icon_space_value',
					'type'              => 'ssb_text',
					'label'             => __( 'Enter the Space in Pixel', 'simple-social-buttons' ),
					'placeholder'       => '10',
					'sanitize_callback' => 'sanitize_text_field',
					'priority'          => '35',
				),
				array(
					'name'     => 'hide_mobile',
					'label'    => __( 'Hide On Mobile Devices', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '40',
				),
				array(
					'name'     => 'icon_limit',
					'label'    => __( 'Limit Social Icons', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '41',
				),
				array(
					'name'              => 'icon_limit_value',
					'type'              => 'ssb_text',
					'label'             => __( 'Maximum Icons to Show', 'simple-social-buttons' ),
					'placeholder'       => '3',
					'sanitize_callback' => 'absint',
					'priority'          => '42',
				),
				array(
					'name'     => 'show_on_category',
					'label'    => __( 'Show at Category pages', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '45',
				),
				array(
					'name'     => 'show_on_archive',
					'label'    => __( 'Show at Archive pages', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '50',

				),
				array(
					'name'     => 'show_on_tag',
					'label'    => __( 'Show at Tag pages', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '55',
				),
				array(
					'name'     => 'show_on_search',
					'label'    => __( 'Show at Search pages', 'simple-social-buttons' ),
					'type'     => 'ssb_checkbox',
					'priority' => '56',
				),
				array(
					'name'              => 'share_title',
					'label'             => __( 'Share Title', 'simple-social-buttons' ),
					'type'              => 'ssb_text',
					'priority'          => '57',
					'sanitize_callback' => 'sanitize_text_field',

				),
				array(
					'name'     => 'posts',
					'label'    => __( 'Post Type Settings', 'simple-social-buttons' ),
					'desc'     => __( 'Multi checkbox description', 'simple-social-buttons' ),
					'type'     => 'ssb_post_types',
					'default'  => array(
						'post' => 'post',
						'page' => 'page',
					),
					'options'  => $post_types,
					'priority' => '99',
				),
				array(
					'name'  => 'go_pro',
					'type'  => 'ssb_go_pro',
					'label' => __(
						'Want More Control Over Your Inline Social Buttons Styling?',
						'simple-social-buttons'
					),
					'desc'  => __(
						'Simple Social Buttons Pro lets you set custom background, hover, and icon colors for every button,
						so they blend naturally with your website\'s design instead of standing out like a default plugin.
						Buttons that fit your site tend to get clicked more, which means more shares and more traffic for you.
						Pro also adds features like buttons on images, popups on exit intent, fly in slide animations, and more.',
						'simple-social-buttons'
					),
					'link'  => 'https://www.WPBrigade.com/wordpress/plugins/simple-social-buttons-pro/?utm_source=simple-social-buttons-lite&utm_medium=settings-inline&utm_campaign=pro-upgrade', // phpcs:ignore
				),
			);

			$ssb_inline = apply_filters( 'ssb_inline_fields', $ssb_inline );

			$ssb_advanced = array(
				array(
					'name'              => 'twitter_handle',
					'type'              => 'ssb_text',
					'label'             => __( 'Twitter/X @username:', 'simple-social-buttons' ),
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'  => 'http_https_resolve',
					'type'  => 'ssb_checkbox',
					'label' => __( 'Http/Https counts resolve:', 'simple-social-buttons' ),
				),
				array(
					'name'              => 'ssb_internal_flush_interval',
					'type'              => 'ssb_select',
					'label'             => __( 'Internal Share Flush Interval', 'simple-social-buttons' ),
					'default'           => '2',
					'options'           => array(
						'1'  => __( '1 hour', 'simple-social-buttons' ),
						'2'  => __( '2 hours', 'simple-social-buttons' ),
						'3'  => __( '3 hours', 'simple-social-buttons' ),
						'4'  => __( '4 hours', 'simple-social-buttons' ),
						'5'  => __( '5 hours', 'simple-social-buttons' ),
						'6'  => __( '6 hours', 'simple-social-buttons' ),
						'7'  => __( '7 hours', 'simple-social-buttons' ),
						'8'  => __( '8 hours', 'simple-social-buttons' ),
						'9'  => __( '9 hours', 'simple-social-buttons' ),
						'10' => __( '10 hours', 'simple-social-buttons' ),
						'11' => __( '11 hours', 'simple-social-buttons' ),
						'12' => __( '12 hours', 'simple-social-buttons' ),
						'13' => __( '13 hours', 'simple-social-buttons' ),
						'14' => __( '14 hours', 'simple-social-buttons' ),
						'15' => __( '15 hours', 'simple-social-buttons' ),
						'16' => __( '16 hours', 'simple-social-buttons' ),
						'17' => __( '17 hours', 'simple-social-buttons' ),
						'18' => __( '18 hours', 'simple-social-buttons' ),
						'19' => __( '19 hours', 'simple-social-buttons' ),
						'20' => __( '20 hours', 'simple-social-buttons' ),
						'21' => __( '21 hours', 'simple-social-buttons' ),
						'22' => __( '22 hours', 'simple-social-buttons' ),
						'23' => __( '23 hours', 'simple-social-buttons' ),
						'24' => __( '24 hours', 'simple-social-buttons' ),
					),
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'name'              => 'ssb_og_tags',
					'type'              => 'ssb_checkbox',
					'label'             => __( 'Open Graph Tags', 'simple-social-buttons' ),
					'default'           => '1',
					'sanitize_callback' => 'sanitize_text_field',

				),
				array(
					'name'              => 'ssb_uninstall_data',
					'type'              => 'ssb_checkbox',
					'label'             => __( 'Remove Settings on Uninstall', 'simple-social-buttons' ),
					'sanitize_callback' => 'sanitize_text_field',
					'help'              => sprintf(
						// translators: %1$s is the opening span tag, %2$s is the closing span tag.
						__( '%1$s This tool will remove all Simple Social Button settings upon uninstall.%2$s', 'simple-social-buttons' ),
						'<span class="ssb_uninstall_data">',
						'</span>'
					),
				),
				array(
					'name'              => 'ssb_factory_reset',
					'type'              => 'ssb_checkbox',
					'label'             => __( 'Factory Reset', 'simple-social-buttons' ),
					'sanitize_callback' => 'sanitize_text_field',
					'help'              => sprintf(
						// translators: %1$s is the opening span tag, %2$s is the closing span tag.
						__( '%1$s Enable to reset all settings made by Simple Social Buttons upon saving.%2$s', 'simple-social-buttons' ),
						'<span class="ssb_uninstall_data">',
						'</span>'
					),
				),
				array(
					'name'              => 'facebook_app_id',
					'desc'              => sprintf(
						// translators: %1$s is the opening h4 tag, %2$s is the closing h4 tag, %3$s is the opening anchor tag, %4$s is the closing anchor tag.
						__( '%1$sFacebook App%2$s %3$show to make App%4$s', 'simple-social-buttons' ),
						'<h4>',
						'</h4>',
						'<a href="https://wpbrigade.com/how-to-create-facebook-app-and-get-app-id-and-secret/" target="_blank">',
						'</a>'
					),
					'type'              => 'ssb_text',
					'label'             => __( 'Facebook App ID:', 'simple-social-buttons' ),
					'sanitize_callback' => 'sanitize_text_field',

				),
				array(
					'name'              => 'facebook_app_secret',
					'type'              => 'ssb_text',
					'label'             => __( 'Facebook App Secret:', 'simple-social-buttons' ),
					'sanitize_callback' => 'sanitize_text_field',

				),
				array(
					'name'              => 'ssb_css',
					'label'             => __( 'Custom CSS', 'simple-social-buttons' ),
					'type'              => 'ssb_textarea',
					'sanitize_callback' => array( $this, 'ssb_sanitize_css_code' ),

				),
				array(
					'name'              => 'ssb_js',
					'label'             => __( 'Custom JS', 'simple-social-buttons' ),
					'type'              => 'ssb_textarea',
					'sanitize_callback' => array( $this, 'ssb_sanitize_code' ),
				),
			);

			// The advanced settings section and fields.
			$ssb_advanced = apply_filters( 'ssb_advance_fields', $ssb_advanced );

			$settings_fields = array(
				'ssb_networks'  => array(
					array(
						'name' => 'icon_selection',
						'type' => 'ssb_icon_selection',
					),
				),
				'ssb_themes'    => array(
					array(
						'name'    => 'icon_style',
						'label'   => __( 'Icon Style', 'simple-social-buttons' ),
						'type'    => 'icon_style',
						'options' => array(
							'sm-round'           => 'sm-round',
							'simple-round'       => 'simple-round',
							'round-txt'          => 'round-txt',
							'round-btm-border'   => 'round-btm-border',
							'flat-button-border' => 'flat-button-border',
							'round-icon'         => 'round-icon',
							'simple-icons'       => 'simple-icons',
						),
					),
				),
				'ssb_positions' => array(
					array(
						'name'    => 'position',
						'label'   => __( 'Positions', 'simple-social-buttons' ),
						'desc'    => __( 'Multi checkbox description', 'simple-social-buttons' ),
						'type'    => 'position',
						'default' => 'inline',
						'options' => $ssb_positions_options,
					),

				),
				'ssb_sidebar'   => $ssb_sidebar,
				'ssb_inline'    => $ssb_inline,
				'ssb_media'     => array(
					array(
						'name'  => 'go_pro',
						'type'  => 'ssb_go_pro',
						'label' => __(
							'Want to Show Social Sharing Buttons on Your Media?',
							'simple-social-buttons'
						),
						'desc'  => __(
							'Simple Social Buttons Pro lets you display share buttons directly on images and photos
							across your posts and pages, choose their position, add icon spacing, and set custom colors that match your site.
							You can also hide them on mobile and control exactly which post types they appear on.
							It\'s an easy way to turn every image into a shareable opportunity and drive more traffic.',
							'simple-social-buttons'
						),
						'link'  => 'https://www.WPBrigade.com/wordpress/plugins/simple-social-buttons-pro/?utm_source=simple-social-buttons-lite&utm_medium=settings-media&utm_campaign=pro-upgrade', // phpcs:ignore
					),
				),
				'ssb_popup'     => array(
					array(
						'name'  => 'go_pro',
						'type'  => 'ssb_go_pro',
						'label' => __(
							'Want Smart Popups That Trigger at the Right Moment?',
							'simple-social-buttons'
						),
						'desc'  => __(
							'Simple Social Buttons Pro lets you create a custom share popup with your own title and message,
							and trigger it exactly when it matters, like before visitors leave or after they scroll.
							You can add animation, display share counts or total shares to build social proof, and set custom colors to match your brand.
							It\'s a smart way to catch visitor attention right when they\'re most likely to share.',
							'simple-social-buttons'
						),
						'link'  => 'https://www.WPBrigade.com/wordpress/plugins/simple-social-buttons-pro/?utm_source=simple-social-buttons-lite&utm_medium=settings-popup&utm_campaign=pro-upgrade', // phpcs:ignore
					),
				),
				'ssb_flyin'     => array(
					array(
						'name'  => 'go_pro',
						'type'  => 'ssb_go_pro',
						'label' => __(
							'Want Advanced Fly In Animations That Drive More Shares?',
							'simple-social-buttons'
						),
						'desc'  => __(
							'Simple Social Buttons Pro lets you create a custom Fly In box with your own title and message,
							choose its position and animation, and even display share counts or total shares to build social proof.
							You can fine-tune timing, icon spacing, and colors to match your brand, and control which post types it appears on.
							It\'s a great way to prompt visitors to share right when they\'re most engaged.',
							'simple-social-buttons'
						),
						'link'  => 'https://www.WPBrigade.com/wordpress/plugins/simple-social-buttons-pro/?utm_source=simple-social-buttons-lite&utm_medium=settings-flyin&utm_campaign=pro-upgrade', // phpcs:ignore
					),
				),
				'ssb_advanced'  => $ssb_advanced,
			);

			$settings_fields = apply_filters( 'ssb_setting_fields', $settings_fields, $post_types );

			return $settings_fields;
	}

	/**
	 * Sanitize input sanitization function
	 *
	 * @param string $input The code input to sanitize.
	 * @since 4.0.0
	 * @version 7.0.1
	 *
	 * @return $input the sanitized value.
	 */
	public function ssb_sanitize_code( $input ) {
		// Only users allowed unfiltered_html may persist Custom JS; otherwise preserve what's stored.
		if ( ! function_exists( 'ssb_user_can_save_custom_js' ) || ! ssb_user_can_save_custom_js() ) {
			$existing_advanced = get_option( 'ssb_advanced' );
			return is_array( $existing_advanced ) && isset( $existing_advanced['ssb_js'] ) ? $existing_advanced['ssb_js'] : '';
		}

		// Store raw JS (no base64). Read paths use ssb_get_custom_js_for_output() for legacy base64.
		if ( ! is_string( $input ) ) {
			return '';
		}
		return preg_replace( '/[\x00]/', '', $input );
	}

	/**
	 * Sanitize input sanitization function
	 *
	 * @param string $input The code input to sanitize.
	 * @since 4.0.0
	 *
	 * @return $input the sanitized value.
	 */
	public function ssb_sanitize_css_code( $input ) {
		return ssb_sanitize_custom_css( $input );
	}
	/**
	 * Plugin page callback.
	 *
	 * @return void
	 */
	public function ssb_plugin_page() {
		echo '<div class="wrap">';
			$this->settings_api->settings_header();
			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();
			$this->settings_api->settings_sidebar();
		echo '</div>';
	}

	/**
	 * Get all the pages.
	 *
	 * @return array Page names with key value pairs.
	 */
	public function get_pages() {
			$pages         = get_pages();
			$pages_options = array();
		if ( $pages ) {
			foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
			}
		}

			return $pages_options;
	}

	/**
	 * Admin init callback.
	 *
	 * @return void
	 */
	public function ssb_admin_init() {

		// Set the settings.
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		// Initialize settings.
		$this->settings_api->admin_init();
	}

	/**
	 * Help page callback.
	 *
	 * @return void
	 */
	public function ssb_help_page() {

		include SSB_PLUGIN_DIR . 'classes/class-ssb-logs.php';

		$html  = '<div class="simple-social-buttons-help-page">';
		$html .= '<h2>Help & Troubleshooting</h2>';
		$html .= sprintf(
			// translators: %1$s is the opening anchor tag, %2$s is the closing anchor tag.
			__( 'Free support is available on the %1$s plugin support forums%2$s.', 'simple-social-buttons' ),
			'<a href="https://wordpress.org/support/plugin/simple-social-buttons" target="_blank">',
			'</a>'
		);
		$html .= '<br /><br />';
		if ( ! class_exists( 'Simple_Social_Buttons_Pro' ) ) {
			$html .= sprintf(
				// translators: %1$s is the opening anchor tag, %2$s is the closing anchor tag.
				__( 'For premium features, add-ons and priority email support, %1$s upgrade to pro%2$s.', 'simple-social-buttons' ),
				'<a href="https://simplesocialbuttons.com/pricing/?utm_source=simple-social-buttons-lite&utm_medium=help-page&utm_campaign=pro-upgrade" target="_blank">',
				'</a>'
			);
			$html .= '<br /><br />';
		}

		$html .= 'Found a bug or have a feature request? Please submit an issue <a href="https://wpbrigade.com/contact/" target="_blank">here</a>!';
		$html .= '<pre><textarea rows="25" cols="75" readonly="readonly">';
		$html .= esc_html( Ssb_Logs_Info::ssb_get_sysinfo() );
		$html .= '</textarea></pre>';
		$html .= '<input type="button" class="button simple-social-buttons-log-file" value="' . esc_attr( __( 'Download Log File', 'simple-social-buttons' ) ) . '"/>';
		$html .= '<span class="ssb-log-file-sniper"><img src="' . esc_url( admin_url( 'images/wpspin_light.gif' ) ) . '" /></span>';
		$html .= '<span class="ssb-log-file-text">Simple Social Buttons Log File Downloaded Successfully!</span>';
		$html .= '</div>';
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $html;
	}

	/**
	 * Download help callback.
	 *
	 * @return void
	 * @version 7.0.1
	 */
	public function ssb_download_help() {

		check_ajax_referer( 'ssb-export-security-check', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.' );
		}

		include SSB_PLUGIN_DIR . 'classes/class-ssb-logs.php';

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- plain-text log file, not HTML.
		echo Ssb_Logs_Info::ssb_get_sysinfo();
		wp_die();
	}

	/**
	 * Include Import/Export Page.
	 *
	 * @since 2.0.4
	 */
	public function ssb_import_export_page() {
		include_once SSB_PLUGIN_DIR . '/inc/ssb-import-export.php';
	}

	/**
	 * Export Settings
	 *
	 * @since 2.0.4
	 */
	public function ssb_export() {

		check_ajax_referer( 'ssb-export-security-check', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Not allowed.' );
		}

		$sections = $this->get_settings_sections();
		$settings = array();

		foreach ( $sections as $section ) {
			$option_id              = $section['id'];
			$result                 = get_option( $option_id );
			$settings[ $option_id ] = ssb_strip_sensitive_settings( $option_id, $result );
		}

		$settings_obj['ssb_settings_obj'] = $settings;
		// phpcs:ignore WordPress.WP.AlternativeFunctions.json_encode_json_encode
		echo wp_json_encode( $settings_obj );
		wp_die();
	}

	/**
	 * Import Settings.
	 *
	 * Returns structured JSON for React: wp_send_json_success() on success,
	 * wp_send_json_error( array( 'message', 'code' ) ) on failure.
	 *
	 * @since 2.0.4
	 * @version 7.0.1
	 */
	public function ssb_import() {
		$nonce = isset( $_POST['security'] ) ? sanitize_text_field( wp_unslash( $_POST['security'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'ssb-import-security-check' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security check failed. Please refresh the page and try again.', 'simple-social-buttons' ),
					'code'    => 'invalid_nonce',
				)
			);
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'You do not have permission to import settings.', 'simple-social-buttons' ),
					'code'    => 'forbidden',
				)
			);
			return;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotValidated -- File input validated below.
		$file = ( isset( $_FILES['file'] ) && is_array( $_FILES['file'] ) ) ? $_FILES['file'] : array(); // phpcs:ignore

		$ssb_imp_tmp_name = isset( $file['tmp_name'] ) ? (string) $file['tmp_name'] : '';
		if ( empty( $ssb_imp_tmp_name ) || ! is_uploaded_file( $ssb_imp_tmp_name ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'No file uploaded or upload failed.', 'simple-social-buttons' ),
					'code'    => 'no_file',
				)
			);
			return;
		}

		$file_size = isset( $file['size'] ) ? (int) $file['size'] : 0;
		if ( $file_size > 5 * 1024 * 1024 ) {
			wp_send_json_error(
				array(
					'message' => __( 'The uploaded file is too large.', 'simple-social-buttons' ),
					'code'    => 'file_too_large',
				)
			);
			return;
		}

		$file_name = isset( $file['name'] ) ? sanitize_file_name( (string) $file['name'] ) : '';
		$file_ext  = strtolower( pathinfo( $file_name, PATHINFO_EXTENSION ) );
		if ( 'json' !== $file_ext ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please upload a valid JSON export file.', 'simple-social-buttons' ),
					'code'    => 'invalid_type',
				)
			);
			return;
		}

		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$ssb_file_content = file_get_contents( $ssb_imp_tmp_name );
		$ssb_json         = json_decode( $ssb_file_content, true );

		if ( JSON_ERROR_NONE !== json_last_error() ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid JSON file. Please upload a valid SSB export file.', 'simple-social-buttons' ),
					'code'    => 'invalid_json',
				)
			);
			return;
		}

		$invalid_format_msg = __( 'Invalid file format. This does not appear to be a Simple Social Buttons export file.', 'simple-social-buttons' );
		if ( ! isset( $ssb_json['ssb_settings_obj'] ) || ! is_array( $ssb_json['ssb_settings_obj'] ) ) {
			wp_send_json_error(
				array(
					'message' => $invalid_format_msg,
					'code'    => 'invalid_data',
				)
			);
			return;
		}

		$ssb_settings_obj       = $ssb_json['ssb_settings_obj'];
		$importable_section_ids = ssb_get_importable_section_ids();

		foreach ( $ssb_settings_obj as $id => $array ) {
			if ( ! in_array( $id, $importable_section_ids, true ) ) {
				continue;
			}

			// Normalize ssb_js to raw for storage (decode if imported file had base64).
			if ( 'ssb_advanced' === $id && isset( $array['ssb_js'] ) && '' !== $array['ssb_js'] ) {
				$array['ssb_js'] = ssb_get_custom_js_for_output( $array['ssb_js'] );
			}

			$existing_ssb_js = null;
			if ( 'ssb_advanced' === $id && ! ssb_user_can_save_custom_js() && isset( $array['ssb_js'] ) ) {
				$existing_advanced = get_option( 'ssb_advanced' );
				if ( is_array( $existing_advanced ) && isset( $existing_advanced['ssb_js'] ) ) {
					$existing_ssb_js = $existing_advanced['ssb_js'];
				}
			}

			// Never restore API credentials from an export file.
			$array     = ssb_strip_sensitive_settings( $id, $array );
			$sanitized = ssb_sanitize_imported_settings( $array );

			if ( null !== $existing_ssb_js ) {
				$sanitized['ssb_js'] = $existing_ssb_js;
			}

			update_option( $id, $sanitized );
		}

		wp_send_json_success( array( 'message' => __( 'Settings imported successfully.', 'simple-social-buttons' ) ) );
	}
}

new Ssb_Settings();
