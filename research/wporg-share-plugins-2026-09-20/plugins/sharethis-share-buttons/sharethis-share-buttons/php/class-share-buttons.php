<?php
/**
 * Share Buttons.
 *
 * @package ShareThisShareButtons
 */

namespace ShareThisShareButtons;

/**
 * Share Buttons Class
 *
 * @package ShareThisShareButtons
 */
class Share_Buttons {


	/**
	 * Plugin instance.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Button Widget instance.
	 *
	 * @var object
	 */
	public $button_widget;

	/**
	 * Menu slug.
	 *
	 * @var string
	 */
	public $menu_slug;

	/**
	 * Menu hook suffix.
	 *
	 * @var string
	 */
	private $hook_suffix;

	/**
	 * Sub Menu hook suffix.
	 *
	 * @var string
	 */
	private $general_hook_suffix;

	/**
	 * Holds the settings sections.
	 *
	 * @var array
	 */
	public $setting_sections;

	/**
	 * Holds the settings fields.
	 *
	 * @var array
	 */
	public $setting_fields;

	/**
	 * Networks available for sharing.
	 *
	 * @var array
	 */
	public $networks;

	/**
	 * Languages available for sharing in.
	 *
	 * @var array
	 */
	public $languages;

	/**
	 * Class constructor.
	 *
	 * @param object $plugin Plugin class.
	 * @param object $button_widget Button Widget class.
	 */
	public function __construct( $plugin, $button_widget ) {

		$this->plugin        = $plugin;
		$this->button_widget = $button_widget;
		$this->menu_slug     = 'sharethis-inline-sticky';
		$this->set_settings();
		$this->set_networks();
		$this->set_languages();

		// Configure your buttons notice on activation.
		register_activation_hook(
			DIR_PATH . 'sharethis-share-buttons.php',
			array( $this, 'st_activation_hook' )
		);

		// Clean up plugin information on deactivation.
		register_deactivation_hook(
			DIR_PATH . 'sharethis-share-buttons.php',
			array( $this, 'st_deactivation_hook' )
		);
	}

	/**
	 * Set the settings sections and fields.
	 *
	 * @access private
	 */
	private function set_settings() {
		// Sections config.
		$this->setting_sections = array(
			'<span id="Inline" class="st-arrow">&#9658;</span>' .
			esc_html( 'Inline Share Buttons' ),
			'<span id="Sticky" class="st-arrow">&#9658;</span>' .
			esc_html( 'Sticky Share Buttons' ),
			'<span id="GDPR" class="st-arrow">&#9658;</span>' .
			esc_html( 'GDPR Compliance Tool' ),
		);

		// Setting configs.
		$this->setting_fields = array(
			array(
				'id_suffix'         => 'inline_settings',
				'description'       => $this->get_descriptions( 'Inline' ),
				'callback'          => 'config_settings',
				'section'           => 'share_button_section_1',
				'arg'               => 'inline',
				'sanitize_callback' => 'sanitize_array_setting',
				'default'           => array(),
			),
			array(
				'id_suffix'         => 'sticky_settings',
				'description'       => $this->get_descriptions( 'Sticky' ),
				'callback'          => 'config_settings',
				'section'           => 'share_button_section_2',
				'arg'               => 'sticky',
				'sanitize_callback' => 'sanitize_array_setting',
				'default'           => array(),
			),
			array(
				'id_suffix'         => 'shortcode',
				'description'       => $this->get_descriptions( '', 'shortcode' ),
				'callback'          => 'shortcode_template',
				'section'           => 'share_button_section_1',
				'arg'               => array(
					'type'  => 'shortcode',
					'value' => '[sharethis-inline-buttons]',
				),
				'sanitize_callback' => 'sanitize_text_field_setting',
				'default'           => '',
			),
			array(
				'id_suffix'         => 'template',
				'description'       => $this->get_descriptions( '', 'template' ),
				'callback'          => 'shortcode_template',
				'section'           => 'share_button_section_1',
				'arg'               => array(
					'type'  => 'template',
					'value' => '<?php echo sharethis_inline_buttons(); ?>',
				),
				'sanitize_callback' => 'sanitize_text_field_setting',
				'default'           => '',
			),
		);
	}

	/**
	 * Set inline settings fields.
	 */
	public function inline_setting_fields() {
		return array(
			array(
				'id_suffix'         => 'inline_post_top',
				'title'             => esc_html__( 'Top of post body', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'   => 'checked="checked"',
					'false'  => '',
					'margin' => true,
				),
			),
			array(
				'id_suffix'         => 'inline_post_bottom',
				'title'             => esc_html__( 'Bottom of post body', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix'         => 'inline_page_top',
				'title'             => esc_html__( 'Top of page body', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix'         => 'inline_page_bottom',
				'title'             => esc_html__( 'Bottom of page body', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
			array(
				'id_suffix'         => 'excerpt',
				'title'             => esc_html__( 'Include in excerpts', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'   => '',
					'false'  => 'checked="checked"',
					'margin' => true,
				),
			),
		);
	}

	/**
	 * Settings fields.
	 */
	public function sticky_setting_fields() {
		return array(
			array(
				'id_suffix'         => 'sticky_home',
				'title'             => esc_html__( 'Home Page', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix'         => 'sticky_post',
				'title'             => esc_html__( 'Posts', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix'         => 'sticky_custom_posts',
				'title'             => esc_html__( 'Custom Post Types', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix'         => 'sticky_page',
				'title'             => esc_html__( 'Pages', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix'         => 'sticky_page_off',
				'title'             => esc_html__( 'Exclude specific pages:', 'sharethis-share-buttons' ),
				'callback'          => 'list_cb',
				'type'              => array(
					'single' => 'page',
					'multi'  => 'pages',
				),
				'sanitize_callback' => 'sanitize_array_setting',
				'default'           => array(),
			),
			array(
				'id_suffix'         => 'sticky_category',
				'title'             => esc_html__( 'Category archive pages', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix'         => 'sticky_category_off',
				'title'             => esc_html__( 'Exclude specific category archives:', 'sharethis-share-buttons' ),
				'callback'          => 'list_cb',
				'type'              => array(
					'single' => 'category',
					'multi'  => 'categories',
				),
				'sanitize_callback' => 'sanitize_array_setting',
				'default'           => array(),
			),
			array(
				'id_suffix'         => 'sticky_tags',
				'title'             => esc_html__( 'Tags Archives', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
			array(
				'id_suffix'         => 'sticky_author',
				'title'             => esc_html__( 'Author pages', 'sharethis-share-buttons' ),
				'callback'          => 'onoff_cb',
				'type'              => '',
				'sanitize_callback' => 'sanitize_checkbox_setting',
				'default'           => array(
					'true'  => 'checked="checked"',
					'false' => '',
				),
			),
		);
	}

	/**
	 * Add in ShareThis menu option.
	 *
	 * @action admin_menu
	 */
	public function define_sharethis_menus() {
		$propertyid = get_option( 'sharethis_property_id' );

		// Menu base64 Encoded icon.
		$icon = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+Cjxzdmcgd2lkdGg9IjE2cHgiIGhlaWdodD0iMTZweCIgdmlld0JveD0iMCAwIDE2IDE2IiB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiPgogICAgPCEtLSBHZW5lcmF0b3I6IFNrZXRjaCA0NC4xICg0MTQ1NSkgLSBodHRwOi8vd3d3LmJvaGVtaWFuY29kaW5nLmNvbS9za2V0Y2ggLS0+CiAgICA8dGl0bGU+RmlsbCAzPC90aXRsZT4KICAgIDxkZXNjPkNyZWF0ZWQgd2l0aCBTa2V0Y2guPC9kZXNjPgogICAgPGRlZnM+PC9kZWZzPgogICAgPGcgaWQ9IlBhZ2UtMSIgc3Ryb2tlPSJub25lIiBzdHJva2Utd2lkdGg9IjEiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+CiAgICAgICAgPGcgaWQ9IkRlc2t0b3AtSEQiIHRyYW5zZm9ybT0idHJhbnNsYXRlKC0xMC4wMDAwMDAsIC00MzguMDAwMDAwKSIgZmlsbD0iI0ZFRkVGRSI+CiAgICAgICAgICAgIDxwYXRoIGQ9Ik0yMy4xNTE2NDMyLDQ0OS4xMDMwMTEgQzIyLjcyNjg4NzcsNDQ5LjEwMzAxMSAyMi4zMzM1MDYyLDQ0OS4yMjg5OSAyMS45OTcwODA2LDQ0OS40Mzc5ODkgQzIxLjk5NTE0OTksNDQ5LjQzNTA5MyAyMS45OTcwODA2LDQ0OS40Mzc5ODkgMjEuOTk3MDgwNiw0NDkuNDM3OTg5IEMyMS44ODA3NTU1LDQ0OS41MDg5NDMgMjEuNzM1NDY5OCw0NDkuNTQ1NjI2IDIxLjU4OTIxODgsNDQ5LjU0NTYyNiBDMjEuNDUzMTA0LDQ0OS41NDU2MjYgMjEuMzE5ODg1Miw0NDkuNTA3NDk0IDIxLjIwODg2OTYsNDQ5LjQ0NTIyOSBMMTQuODczNzM4Myw0NDYuMDM4OTggQzE0Ljc2NDE3MDcsNDQ1Ljk5MDIzIDE0LjY4NzkwNzgsNDQ1Ljg3ODczMSAxNC42ODc5MDc4LDQ0NS43NTEzMDUgQzE0LjY4NzkwNzgsNDQ1LjYyMzM5NSAxNC43NjUxMzYsNDQ1LjUxMTg5NyAxNC44NzQ3MDM2LDQ0NS40NjI2NjQgTDIxLjIwODg2OTYsNDQyLjA1Njg5NyBDMjEuMzE5ODg1Miw0NDEuOTk1MTE1IDIxLjQ1MzEwNCw0NDEuOTU2NTAxIDIxLjU4OTIxODgsNDQxLjk1NjUwMSBDMjEuNzM1NDY5OCw0NDEuOTU2NTAxIDIxLjg4MDc1NTUsNDQxLjk5MzY2NyAyMS45OTcwODA2LDQ0Mi4wNjQ2MiBDMjEuOTk3MDgwNiw0NDIuMDY0NjIgMjEuOTk1MTQ5OSw0NDIuMDY3MDM0IDIxLjk5NzA4MDYsNDQyLjA2NDYyIEMyMi4zMzM1MDYyLDQ0Mi4yNzMxMzcgMjIuNzI2ODg3Nyw0NDIuMzk5MTE1IDIzLjE1MTY0MzIsNDQyLjM5OTExNSBDMjQuMzY2NTQwMyw0NDIuMzk5MTE1IDI1LjM1MTY4MzQsNDQxLjQxNDQ1NSAyNS4zNTE2ODM0LDQ0MC4xOTk1NTggQzI1LjM1MTY4MzQsNDM4Ljk4NDY2IDI0LjM2NjU0MDMsNDM4IDIzLjE1MTY0MzIsNDM4IEMyMi4wMTYzODc2LDQzOCAyMS4wOTMwMjcyLDQzOC44NjMwMjYgMjAuOTc1MjU0MSw0MzkuOTY3MzkgQzIwLjk3MTM5MjYsNDM5Ljk2MzA0NiAyMC45NzUyNTQxLDQzOS45NjczOSAyMC45NzUyNTQxLDQzOS45NjczOSBDMjAuOTUwNjM3NSw0NDAuMjM5MTM3IDIwLjc2OTE1MTEsNDQwLjQ2NzkyNiAyMC41MzYwMTgzLDQ0MC41ODQyNTEgTDE0LjI3OTU2MzMsNDQzLjk0NzU0MiBDMTQuMTY0MjAzNiw0NDQuMDE3MDQ3IDE0LjAyNDIyNzMsNDQ0LjA1NjE0NCAxMy44Nzk0MjQzLDQ0NC4wNTYxNDQgQzEzLjcwODU1NjgsNDQ0LjA1NjE0NCAxMy41NDgzMDgxLDQ0NC4wMDQ0OTggMTMuNDIwODgxNSw0NDMuOTEwMzc2IEMxMy4wNzUyODUsNDQzLjY4NDk2NiAxMi42NjUwMDk4LDQ0My41NTEyNjQgMTIuMjIxOTEyNiw0NDMuNTUxMjY0IEMxMS4wMDcwMTU1LDQ0My41NTEyNjQgMTAuMDIyMzU1MSw0NDQuNTM2NDA3IDEwLjAyMjM1NTEsNDQ1Ljc1MTMwNSBDMTAuMDIyMzU1MSw0NDYuOTY2MjAyIDExLjAwNzAxNTUsNDQ3Ljk1MDg2MiAxMi4yMjE5MTI2LDQ0Ny45NTA4NjIgQzEyLjY2NTAwOTgsNDQ3Ljk1MDg2MiAxMy4wNzUyODUsNDQ3LjgxNzY0MyAxMy40MjA4ODE1LDQ0Ny41OTIyMzMgQzEzLjU0ODMwODEsNDQ3LjQ5NzYyOSAxMy43MDg1NTY4LDQ0Ny40NDY0NjUgMTMuODc5NDI0Myw0NDcuNDQ2NDY1IEMxNC4wMjQyMjczLDQ0Ny40NDY0NjUgMTQuMTY0MjAzNiw0NDcuNDg1MDc5IDE0LjI3OTU2MzMsNDQ3LjU1NDU4NSBMMjAuNTM2MDE4Myw0NTAuOTE4MzU4IEMyMC43Njg2Njg0LDQ1MS4wMzQyMDEgMjAuOTUwNjM3NSw0NTEuMjYzNDcyIDIwLjk3NTI1NDEsNDUxLjUzNTIxOSBDMjAuOTc1MjU0MSw0NTEuNTM1MjE5IDIwLjk3MTM5MjYsNDUxLjUzOTU2MyAyMC45NzUyNTQxLDQ1MS41MzUyMTkgQzIxLjA5MzAyNzIsNDUyLjYzOTEwMSAyMi4wMTYzODc2LDQ1My41MDI2MDkgMjMuMTUxNjQzMiw0NTMuNTAyNjA5IEMyNC4zNjY1NDAzLDQ1My41MDI2MDkgMjUuMzUxNjgzNCw0NTIuNTE3NDY2IDI1LjM1MTY4MzQsNDUxLjMwMjU2OSBDMjUuMzUxNjgzNCw0NTAuMDg3NjcyIDI0LjM2NjU0MDMsNDQ5LjEwMzAxMSAyMy4xNTE2NDMyLDQ0OS4xMDMwMTEiIGlkPSJGaWxsLTMiPjwvcGF0aD4KICAgICAgICA8L2c+CiAgICA8L2c+Cjwvc3ZnPg==';

		// Main sharethis menu.
		add_menu_page(
			$this->get_descriptions( '', 'share_buttons' ),
			__( 'ShareThis Share Buttons', 'sharethis-share-buttons' ),
			'manage_options',
			$this->menu_slug . '-share-buttons',
			array( $this, 'share_button_display' ),
			$icon,
			26
		);
	}

	/**
	 * Enqueue main MU script.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function enqueue_mu() {
		wp_enqueue_script( ASSET_PREFIX . '-mu' );
	}

	/**
	 * Enqueue admin assets.
	 *
	 * @action admin_enqueue_scripts
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Are sticky and inline buttons enabled.
		$inline        = 'true' === get_option( 'sharethis_inline' ) || true === get_option( 'sharethis_inline' ) ?
			true :
			false;
		$sticky        = 'true' === get_option( 'sharethis_sticky' ) || true === get_option( 'sharethis_sticky' ) ?
			true :
			false;
		$gdpr          = 'true' === get_option( 'sharethis_gdpr' ) || true === get_option( 'sharethis_gdpr' ) ?
			true :
			false;
		$first_exists  = get_option( 'sharethis_first_product' );
		$first_button  = false !== $first_exists && null !== $first_exists ? $first_exists : '';
		$first_exists  = false === $first_exists || null === $first_exists || '' === $first_exists ? true : false;
		$propertyid    = explode( '-', get_option( 'sharethis_property_id' ), 2 );
		$token         = get_option( 'sharethis_token' );
		$property_id   = isset( $propertyid[0] ) ? $propertyid[0] : '';
		$secret        = isset( $propertyid[1] ) ? $propertyid[1] : '';
		$button_config = get_option( 'sharethis_button_config', true );
		$button_config = false !== $button_config && null !== $button_config ? $button_config : '';

		if ( '' === $property_id ) {
			wp_register_script(
				ASSET_PREFIX . '-credentials',
				DIR_URL . 'js/set-credentials.js',
				array( 'jquery', 'wp-util' ),
				filemtime( DIR_PATH . 'js/set-credentials.js' ),
				false
			);

			// Only enqueue this script on the general settings page for credentials.
			wp_enqueue_script( ASSET_PREFIX . '-credentials' );
			wp_add_inline_script(
				ASSET_PREFIX . '-credentials',
				sprintf(
					'Credentials.boot( %s );',
					wp_json_encode(
						array(
							'nonce'        => wp_create_nonce( META_PREFIX ),
							'email'        => get_bloginfo( 'admin_email' ),
							'url'          => str_replace( 'http://', '', str_replace( 'https://', '', site_url() ) ),
							'buttonConfig' => $button_config,
						)
					)
				)
			);
		}

		// Only enqueue assets on this plugin admin menu.
		if ( 'toplevel_page_sharethis-inline-sticky-share-buttons' !== $hook_suffix ) {
			return;
		}

		// Enqueue the styles globally throughout the ShareThis menus.
		wp_enqueue_style( ASSET_PREFIX . '-admin' );
		wp_enqueue_script( ASSET_PREFIX . '-mua' );

		if ( $first_exists && ( $inline || $sticky ) ) {
			$first = $inline ? 'inline' : 'sticky';

			update_option( 'sharethis_first_product', $first );
		}

		wp_enqueue_script( ASSET_PREFIX . '-admin' );
		wp_add_inline_script(
			ASSET_PREFIX . '-admin',
			sprintf(
				'ShareButtons.boot( %s );',
				wp_json_encode(
					array(
						'inlineEnabled' => $inline,
						'stickyEnabled' => $sticky,
						'gdprEnabled'   => $gdpr,
						'propertyid'    => $property_id,
						'token'         => $token,
						'secret'        => $secret,
						'buttonConfig'  => $button_config,
						'nonce'         => wp_create_nonce( META_PREFIX ),
						'fresh'         => get_option( 'sharethis_fract' ),
						'first'         => get_option( 'sharethis_first_product', false ),
					)
				)
			)
		);
	}

	/**
	 * Call back for displaying Share Buttons settings page.
	 */
	public function share_button_display() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$description = $this->get_descriptions( '', 'share_buttons' );
		$enabled     = array(
			'inline' => 'true' === get_option( 'sharethis_inline' ) ? 'Enabled' : 'Disabled',
			'sticky' => 'true' === get_option( 'sharethis_sticky' ) ? 'Enabled' : 'Disabled',
			'gdpr'   => 'true' === get_option( 'sharethis_gdpr' ) ? 'Enabled' : 'Disabled',
		);

		include_once DIR_PATH . '/templates/share-buttons/share-button-settings.php';
	}

	/**
	 * Define share button setting sections and fields.
	 *
	 * @action admin_init
	 */
	public function settings_api_init() {
		// Register sections.
		foreach ( $this->setting_sections as $index => $title ) {
			$i       = $index + 1;
			$section = "share_button_section_{$i}";
			$arg     = '';

			switch ( $i ) {
				case 1:
					$arg = 'inline';
					break;
				case 2:
					$arg = 'sticky';
					break;
				case 3:
					$arg = 'gdpr';
					break;
			}

			add_settings_section(
				$section,
				'',
				array( $this, 'social_button_link' ),
				$this->menu_slug . '-share-buttons',
				array( $arg )
			);
		}

		// Register setting fields with sanitization.
		foreach ( $this->setting_fields as $setting_field ) {
			$option_name = $this->menu_slug . '_' . $setting_field['id_suffix'];

			register_setting(
				$this->menu_slug . '-share-buttons',
				$option_name,
				array(
					'sanitize_callback' => isset( $setting_field['sanitize_callback'] )
						? array( $this, $setting_field['sanitize_callback'] )
						: array( $this, 'sanitize_text_field_setting' ),
					'default' => isset( $setting_field['default'] ) ? $setting_field['default'] : '',
				)
			);

			add_settings_field(
				$option_name,
				$setting_field['description'],
				array( $this, $setting_field['callback'] ),
				$this->menu_slug . '-share-buttons',
				$setting_field['section'],
				$setting_field['arg']
			);
		}

		// Register omit settings with sanitization.
		register_setting(
			$this->menu_slug . '-share-buttons',
			$this->menu_slug . '_sticky_page_off',
			array(
				'sanitize_callback' => array( $this, 'sanitize_checkbox_setting' ),
				'default'           => 0,
			)
		);

		register_setting(
			$this->menu_slug . '-share-buttons',
			$this->menu_slug . '_sticky_category_off',
			array(
				'sanitize_callback' => array( $this, 'sanitize_checkbox_setting' ),
				'default'           => 0,
			)
		);
	}

	/**
	 * Sanitize plain text settings.
	 *
	 * @param mixed $value Raw setting value.
	 * @return string
	 */
	public function sanitize_text_field_setting( $value ) {
		return sanitize_text_field( wp_unslash( $value ) );
	}

	/**
	 * Sanitize checkbox settings.
	 *
	 * @param mixed $value Raw setting value.
	 * @return int
	 */
	public function sanitize_checkbox_setting( $value ) {
		return ! empty( $value ) ? 1 : 0;
	}

	/**
	 * Sanitize textarea settings.
	 *
	 * @param mixed $value Raw setting value.
	 * @return string
	 */
	public function sanitize_textarea_setting( $value ) {
		return sanitize_textarea_field( wp_unslash( $value ) );
	}

	/**
	 * Sanitize URL settings.
	 *
	 * @param mixed $value Raw setting value.
	 * @return string
	 */
	public function sanitize_url_setting( $value ) {
		return esc_url_raw( wp_unslash( $value ) );
	}

	/**
	 * Sanitize array settings.
	 *
	 * @param mixed $value Raw setting value.
	 * @return array
	 */
	public function sanitize_array_setting( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}

		return array_map( 'sanitize_text_field', wp_unslash( $value ) );
	}

	/**
	 * Call back function for on / off buttons.
	 *
	 * @param string $type The setting type.
	 */
	public function config_settings( $type ) {
		$config_array = 'inline' === $type ? $this->inline_setting_fields() : $this->sticky_setting_fields();

		// Display on off template for inline settings.
		foreach ( $config_array as $setting ) {
			$option       = 'sharethis_' . $setting['id_suffix'];
			$title        = isset( $setting['title'] ) ? $setting['title'] : '';
			$option_value = get_option( 'sharethis_' . $type . '_settings' );
			$default      = isset( $setting['default'] ) ? $setting['default'] : '';
			$allowed      = array(
				'li'    => array(
					'class' => array(),
				),
				'span'  => array(
					'id'    => array(),
					'class' => array(),
				),
				'input' => array(
					'id'    => array(),
					'name'  => array(),
					'type'  => array(),
					'value' => array(),
				),
			);

			// Margin control variables.
			$margin = isset( $setting['default']['margin'] ) ? $setting['default']['margin'] : false;
			$mclass = isset( $option_value[ $option . '_margin_top' ] ) &&
					0 !== (int) $option_value[ $option . '_margin_top' ] ||
					isset( $option_value[ $option . '_margin_bottom' ] ) &&
					0 !== (int) $option_value[ $option . '_margin_bottom' ] ?
				'active-margin' : '';
			$onoff  = '' !== $mclass ? __( 'On', 'sharethis-share-buttons' ) : __( 'Off', 'sharethis-share-buttons' );
			$active = array(
				'class' => $mclass,
				'onoff' => esc_html( $onoff ),
			);

			if ( isset( $option_value[ $option ] ) && false !== $option_value[ $option ] && null !== $option_value[ $option ] ) {
				$default = array(
					'true'  => '',
					'false' => '',
				);
			}

			// Display the list call back if specified.
			if ( 'onoff_cb' === $setting['callback'] ) {
				include DIR_PATH . '/templates/share-buttons/onoff-buttons.php';
			} else {
				$current_omit = $this->get_omit( $setting['type'] );

				$this->list_cb( $setting['type'], $current_omit, $allowed );
			}
		}
	}

	/**
	 * Helper function to build the omit list html
	 *
	 * @access private
	 *
	 * @param array $setting the omit type.
	 *
	 * @return string The html for omit list.
	 */
	private function get_omit( $setting ) {
		$current_omit = get_option( 'sharethis_sticky_' . $setting['single'] . '_off' );
		$current_omit = isset( $current_omit ) ? $current_omit : '';
		$html         = '';

		if ( is_array( $current_omit ) ) {
			foreach ( $current_omit as $title => $id ) {
				$html .= '<li class="omit-item">';
				$html .= $title;
				$html .= '<span id="' . $id . '" class="remove-omit">X</span>';
				$html .= "<input
							type='hidden'
							name='sharethis_sticky_{$setting['single']}
							_off[{$title}]'
							value='{$id}'
							id='sharethis_sticky_{$setting['single']}_off[{$title}]'
						>";
				$html .= '</li>';
			}
		}

		// Add ommit ids to meta box option.
		$this->update_metabox_list( $current_omit );

		return $html;
	}

	/**
	 * Helper function to update metabox list to sync with omit.
	 *
	 * @param array $current_omit The omit list.
	 */
	private function update_metabox_list( $current_omit ) {
		$current_on = get_option( 'sharethis_sticky_page_on' );

		if ( true === isset( $current_on, $current_omit )
			&& true === is_array( $current_on )
			&& true === is_array( $current_omit )
		) {
			$new_on = array_diff( $current_on, $current_omit );

			if ( true === is_array( $new_on ) ) {
				delete_option( 'sharethis_sticky_page_on' );
				delete_option( 'sharethis_sticky_page_off' );

				update_option( 'sharethis_sticky_page_off', $current_omit );
				update_option( 'sharethis_sticky_page_on', $new_on );
			}
		}
	}

	/**
	 * Callback function for on/off buttons
	 *
	 * @param array $id The setting type.
	 */
	public function enable_cb( $id ) {
		include DIR_PATH . '/templates/share-buttons/enable-buttons.php';
	}

	/**
	 * Callback function for omitting fields.
	 *
	 * @param array $type The type of list to return for exlusion.
	 * @param array $current_omit The currently omited items.
	 * @param array $allowed The allowed html that an omit item can echo.
	 */
	public function list_cb( $type, $current_omit, $allowed ) {
		include DIR_PATH . '/templates/share-buttons/list.php';
	}

	/**
	 * Callback function for the shortcode and template code fields.
	 *
	 * @param string $type The type of template to pull.
	 */
	public function shortcode_template( $type ) {
		include DIR_PATH . '/templates/share-buttons/shortcode-templatecode.php';
	}

	/**
	 * Callback function for the login buttons.
	 *
	 * @param string $button The specific product to link to.
	 */
	public function social_button_link( $button ) {
		$networks  = $this->networks;
		$languages = $this->languages;

		if ( isset( $button['id'] ) && 'share_button_section_3' === $button['id'] ) {
			// User type options.
			$user_types = array(
				'eu'     => esc_html__( 'Only visitors in the EU', 'sharethis-share-buttons' ),
				'always' => esc_html__( 'All visitors globally', 'sharethis-share-buttons' ),
			);

			$vendor_data = $this->get_vendors();

			if ( $vendor_data ) {
				$vendors  = $vendor_data['vendors'];
				$purposes = array_column( $vendor_data['purposes'], 'name', 'id' );
			}

			$enabled = array(
				'gdpr' => 'true' === get_option( 'sharethis_gdpr' ) ? 'Enabled' : 'Disabled',
			);

			// Template vars.
			$colors = array(
				'#e31010',
				'#000000',
				'#ffffff',
				'#09cd18',
				'#ff6900',
				'#fcb900',
				'#7bdcb5',
				'#00d084',
				'#8ed1fc',
				'#0693e3',
				'#abb8c3',
				'#eb144c',
				'#f78da7',
				'#9900ef',
				'#b80000',
				'#db3e00',
				'#fccb00',
				'#008b02',
				'#006b76',
				'#1273de',
				'#004dcf',
				'#5300eb',
				'#eb9694',
				'#fad0c3',
				'#fef3bd',
				'#c1e1c5',
				'#bedadc',
				'#c4def6',
				'#bed3f3',
				'#d4c4fb',
			);

			include DIR_PATH . '/templates/general/gdpr/gdpr-config.php';
		} else {
			$enabled = array(
				'inline' => 'true' === get_option( 'sharethis_inline' ) ? 'Enabled' : 'Disabled',
				'sticky' => 'true' === get_option( 'sharethis_sticky' ) ? 'Enabled' : 'Disabled',
				'gdpr'   => 'true' === get_option( 'sharethis_gdpr' ) ? 'Enabled' : 'Disabled',
			);

			include DIR_PATH . '/templates/share-buttons/button-config.php';
		}
	}

	/**
	 * Define setting descriptions.
	 *
	 * @param string $type Type of button.
	 * @param string $subtype Setting type.
	 *
	 * @access private
	 * @return string|void
	 */
	private function get_descriptions( $type = '', $subtype = '' ) {
		global $current_user;

		switch ( $subtype ) {
			case '':
				$description  = esc_html( 'WordPress Display Settings' );
				$description .= '<span>';
				$description .= esc_html(
					'Use these settings to automatically include or restrict the display of '
				) . esc_html( $type ) . esc_html(
					' Share Buttons on specific pages of your site.'
				);
				$description .= '</span>';
				break;
			case 'shortcode':
				$description  = esc_html( 'Shortcode' );
				$description .= '<span>';
				$description .= esc_html(
					'Use this shortcode to deploy your inline share buttons in a widget, or WYSIWYG editor.'
				);
				$description .= '</span>';
				break;
			case 'template':
				$description  = esc_html( 'PHP' );
				$description .= '<span>';
				$description .= esc_html(
					'Use this PHP snippet to include your inline share buttons anywhere else in your template.'
				);
				$description .= '</span>';
				break;
			case 'social':
				$description  = esc_html( 'Social networks and button styles' );
				$description .= '<span>';
				$description .= esc_html(
					'Login to ShareThis Platform to add, remove or re-order social networks in your '
				) . esc_html( $type ) . esc_html(
					' Share buttons.  You may also update the alignment, size, labels and count settings.'
				);
				$description .= '</span>';
				break;
			case 'property':
				$description  = esc_html( 'Property ID' );
				$description .= '<span>';
				$description .= esc_html(
					'We use this unique ID to identify your property. Copy it from your '
				);
				$description .= '<a class="st-support" href="https://platform.sharethis.com/settings?utm_source=sharethis-plugin&utm_medium=sharethis-plugin-page&utm_campaign=property-settings" target="_blank">';
				$description .= esc_html( 'ShareThis platform settings' );
				$description .= '</a></span>';
				break;
			case 'share_buttons':
				$description = '<h3>';
				break;
		}

		return wp_kses_post( $description );
	}

	/**
	 * Set the property id and secret key for the user's platform account if query params are present.
	 *
	 * @action wp_ajax_set_credentials
	 */
	public function set_credentials() {
		check_ajax_referer( META_PREFIX, 'nonce' );

		if ( ! isset( $_POST['data'], $_POST['token'] ) || '' === $_POST['data'] ) { // WPCS: input var ok.
			wp_send_json_error( 'Set credentials failed.' );
		}

		$data  = sanitize_text_field( wp_unslash( $_POST['data'] ) ); // WPCS: input var ok.
		$token = sanitize_text_field( wp_unslash( $_POST['token'] ) ); // WPCS: input var ok.

		// If both variables exist add them to a database option.
		if ( false === get_option( 'sharethis_property_id' ) ) {
			update_option( 'sharethis_property_id', $data );
			update_option( 'sharethis_token', $token );
		}
	}

	/**
	 * Helper function to determine if property ID is set.
	 *
	 * @param string $type Should empty count as false.
	 *
	 * @access private
	 * @return bool
	 */
	private function is_property_id_set( $type = '' ) {
		$property_id = get_option( 'sharethis_property_id' );

		// If the property id is set then show the general settings template.
		if ( false !== $property_id && null !== $property_id ) {
			if ( 'empty' === $type && '' === $property_id ) {
				return false;
			}

			return true;
		}

		return false;
	}

	/**
	 * AJAX Call back to update status of buttons
	 *
	 * @action wp_ajax_update_buttons
	 */
	public function update_buttons() {
		check_ajax_referer( META_PREFIX, 'nonce' );

		if ( ! isset( $_POST['type'], $_POST['onoff'] ) ) { // phpcs:ignore input var ok.
			wp_send_json_error( 'Update buttons failed.' );
		}

		// Set option type and button value.
		$type  = 'sharethis_' . sanitize_text_field( wp_unslash( $_POST['type'] ) ); // WPCS: input var ok.
		$onoff = sanitize_text_field( wp_unslash( $_POST['onoff'] ) ); // WPCS: input var ok.

		if ( 'On' === $onoff ) {
			update_option( $type, 'true' );
		} elseif ( 'Off' === $onoff ) {
			update_option( $type, 'false' );
		}
	}

	/**
	 * AJAX Call back to update buttons show/hide settings.
	 *
	 * @action wp_ajax_update_st_settings
	 */
	public function update_st_settings() {
		check_ajax_referer( META_PREFIX, 'nonce' );

		if ( ! isset( $_POST['formData'], $_POST['button'] ) ) { // phpcs:ignore input var ok.
			wp_send_json_error( 'Update settings failed.' );
		}

		$button        = sanitize_text_field( wp_unslash( filter_input( INPUT_POST, 'button', FILTER_UNSAFE_RAW ) ) );
		$form_data     = sanitize_text_field( wp_unslash( filter_input( INPUT_POST, 'formData', FILTER_UNSAFE_RAW ) ) );
		$new_form_data = array();

		if ( false === empty( $form_data ) ) {
			foreach ( json_decode( $form_data, true ) as $form_data_item_name => $form_data_value ) {
				$new_name                   = str_replace( array( 'sharethis_' . $button . '_settings[', ']' ), array( '', '' ), $form_data_item_name );
				$new_form_data[ $new_name ] = $form_data_value;
			}

			update_option( 'sharethis_' . $button . '_settings', $new_form_data );
			wp_send_json_success( 'Settings saved' );
		}

		wp_send_json_error( 'Update settings failed.' );
	}

	/**
	 * AJAX Call back to set defaults when reset button is clicked.
	 *
	 * @action wp_ajax_set_default_settings
	 */
	public function set_default_settings() {
		check_ajax_referer( META_PREFIX, 'nonce' );

		if ( ! isset( $_POST['type'] ) ) { // WPCS: CRSF ok. input var ok.
			wp_send_json_error( 'Update buttons failed.' );
		}

		// Set option type and button value.
		$type = strtolower( sanitize_text_field( wp_unslash( $_POST['type'] ) ) ); // WPCS: input var ok.

		// Note the new install.
		update_option( 'sharethis_fract', 'true' );

		$this->set_the_defaults( $type );
	}

	/**
	 * Helper function to set the default button options.
	 *
	 * @param string $type The type of default to set.
	 */
	private function set_the_defaults( $type ) {
		$default = array(
			'inline_settings'     => array(
				'sharethis_inline_post_top'               => 'true',
				'sharethis_inline_post_bottom'            => 'false',
				'sharethis_inline_page_top'               => 'false',
				'sharethis_inline_page_bottom'            => 'false',
				'sharethis_excerpt'                       => 'false',
				'sharethis_inline_post_top_margin_top'    => 0,
				'sharethis_inline_post_top_margin_bottom' => 0,
				'sharethis_inline_post_bottom_margin_top' => 0,
				'sharethis_inline_post_bottom_margin_bottom' => 0,
				'sharethis_inline_page_top_margin_top'    => 0,
				'sharethis_inline_page_top_margin_bottom' => 0,
				'sharethis_inline_page_bottom_margin_top' => 0,
				'sharethis_inline_page_bottom_margin_bottom' => 0,
				'sharethis_excerpt_margin_top'            => 0,
				'sharethis_excerpt_margin_bottom'         => 0,
			),
			'sticky_settings'     => array(
				'sharethis_sticky_home'         => 'true',
				'sharethis_sticky_post'         => 'true',
				'sharethis_sticky_custom_posts' => 'true',
				'sharethis_sticky_page'         => 'true',
				'sharethis_sticky_category'     => 'true',
				'sharethis_sticky_tags'         => 'true',
				'sharethis_sticky_author'       => 'true',
			),
			'sticky_page_off'     => '',
			'sticky_category_off' => '',
		);

		if ( 'both' !== $type ) {
			update_option( 'sharethis_' . $type . '_settings', $default[ $type . '_settings' ] );

			if ( 'sticky' === $type ) {
				update_option( 'sharethis_sticky_page_off', '' );
				update_option( 'sharethis_sticky_category_off', '' );
			}
		} else {
			foreach ( $default as $types => $settings ) {
				update_option( 'sharethis_' . $types, $settings );
			}
		}
	}

	/**
	 * Ajax Call back to return categories or pages based on input.
	 *
	 * @action wp_ajax_return_omit
	 */
	public function return_omit() {
		check_ajax_referer( META_PREFIX, 'nonce' );

		$post = filter_input_array(
			INPUT_POST,
			array(
				'key'  => FILTER_UNSAFE_RAW,
				'type' => FILTER_UNSAFE_RAW,
			)
		);

		if ( true === empty( $post['key'] ) || true === empty( $post['type'] ) ) {
			wp_send_json_error( '' );
		}

		$sharethis_sticky_category_off = get_option( 'sharethis_sticky_category_off', array() );

		if ( false === is_array( $sharethis_sticky_category_off ) ) {
			$sharethis_sticky_category_off = array();
		}

		$current_cat = array_values( $sharethis_sticky_category_off );

		if ( 'category' === $post['type'] ) {
			// Search category names LIKE $key_input.
			$categories = get_categories(
				array(
					'name__like' => htmlspecialchars( $post['key'] ),
					'exclude'    => $current_cat,
					'hide_empty' => false,
				)
			);

			foreach ( $categories as $cats ) {
				$related[] = array(
					'id'    => $cats->term_id,
					'title' => $cats->name,
				);
			}
		} else {
			global $wpdb;

			// @codingStandardsIgnoreStart
			$pages = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, post_title
					FROM $wpdb->posts
					WHERE post_title LIKE '%%s%'
					AND post_type = 'page'",
					htmlspecialchars( $post['key'] )
				)
			);
			// @codingStandardsIgnoreEnd

			foreach ( $pages as $page ) {
				if ( true === $this->not_in_list( $page->ID ) ) {
					$related[] = array(
						'id'    => $page->ID,
						'title' => $page->post_title,
					);
				}
			}
		}

		// Create output list if any results exist.
		if ( count( $related ) > 0 ) {
			foreach ( $related as $items ) {
				$item_option[] = sprintf(
					'<li class="ta-' . htmlspecialchars( $post['type'] ) . '-item" data-id="%1$d">%2$s</li>',
					(int) $items['id'],
					esc_html( $items['title'] )
				);
			}

			wp_send_json_success( $item_option );
		} else {
			wp_send_json_error( 'no results' );
		}
	}

	/**
	 * Helper function to determine if page is in the list already.
	 *
	 * @param integer $id The page id.
	 *
	 * @return bool
	 */
	private function not_in_list( $id ) {
		$sharethis_sticky_page_off = get_option( 'sharethis_sticky_page_off', array() );

		if ( false === is_array( $sharethis_sticky_page_off ) ) {
			$sharethis_sticky_page_off = array();
		}

		$current_pages = array_values( $sharethis_sticky_page_off );

		return true === empty( $current_pages ) || false === in_array( $id, $current_pages, true );
	}

	/**
	 * Display custom admin notice.
	 *
	 * @action admin_notices
	 */
	public function connection_made_admin_notice() {
		$screen = get_current_screen();
		if ( 'sharethis_page_sharethis-share-buttons' === $screen->base ) {
			settings_errors();

			$reset = filter_input( INPUT_GET, 'reset', FILTER_UNSAFE_RAW );

			if ( false === empty( $reset ) ) {
				?>
				<div class="notice notice-success is-dismissible">
					<p>
						<?php
						printf(
							/* translators: %1$s refers to type of reset button. */
							esc_html__(
								'Successfully reset your %1$s share button options!',
								'sharethis-share-buttons'
							),
							esc_html( sanitize_text_field( wp_unslash( $reset ) ) )
						);
						?>
					</p>
				</div>
				<?php
			};
		}
	}

	/**
	 * Runs only when the plugin is activated.
	 */
	public function st_activation_hook() {
		// Create transient data.
		set_transient( 'st-activation', true, 5 );
		set_transient( 'st-connection', true, 360 );

		// Set the default options.
		$this->set_the_defaults( 'both' );
	}

	/**
	 * Admin Notice on Activation.
	 *
	 * @action admin_notices
	 */
	public function activation_inform_notice() {
		$screen  = get_current_screen();
		$product = get_option( 'sharethis_first_product' );
		$product = null !== $product && false !== $product ? ucfirst( $product ) : 'your';
		$gen_url = '<a href="' . esc_url( admin_url( 'admin.php?page=sharethis-inline-sticky-share-buttons&nft' ) ) . '">
						configuration
					</a>';

		if ( ! $this->is_property_id_set() ) {
			$gen_url = '<a href="' . esc_url( admin_url( 'admin.php?page=sharethis-inline-sticky-share-buttons' ) ) . '">configuration</a>';
		}

		// Check transient, if available display notice.
		if ( get_transient( 'st-activation' ) ) {
			?>
			<div class="updated notice is-dismissible">
				<p>
					<?php

					printf(
						/* translators: %1$s is the general settings url. */
						esc_html__(
							'Your ShareThis Share Button plugin requires %1$s',
							'sharethis-share-buttons'
						),
						wp_kses_post( $gen_url )
					);
					?>
					.
				</p>
			</div>
			<?php
			// Delete transient, only display this notice once.
			delete_transient( 'st-activation' );
		}

		$nft = filter_input( INPUT_GET, 'nft', FILTER_UNSAFE_RAW );

		if ( 'sharethis_page_sharethis-share-buttons' === $screen->base &&
			get_transient( 'st-connection' ) &&
			true === empty( $nft )
		) {
			?>
			<div class="notice notice-success is-dismissible">
				<p>
					<?php
					printf(
						/* translators: %1$s is the product type. */
						esc_html__(
							'Congrats! You’ve activated %1$s Share Buttons. Sit tight, they’ll appear on your site in just a few minutes!',
							'sharethis-share-buttons'
						),
						esc_html( $product )
					);
					?>
				</p>
			</div>
			<?php
			delete_transient( 'st-connection' );
		}
	}

	/**
	 * Remove all database information when plugin is deactivated.
	 */
	public function st_deactivation_hook() {
		foreach ( wp_load_alloptions() as $option => $value ) {
			if ( strpos( $option, 'sharethis_' ) === 0 ) {
				delete_option( $option );
			}
		}
	}

	/**
	 * Register the button widget.
	 *
	 * @action widgets_init
	 */
	public function register_widgets() {
		register_widget( $this->button_widget );
	}

	/**
	 * Set the languages array.
	 */
	private function set_languages() {
		$this->languages = array(
			'English'    => 'en',
			'German'     => 'de',
			'Spanish'    => 'es',
			'French'     => 'fr',
			'Italian'    => 'it',
			'Japanese'   => 'ja',
			'Korean'     => 'ko',
			'Portuguese' => 'pt',
			'Russian'    => 'ru',
			'Chinese'    => 'zh',
		);
	}


	/**
	 * Set network array with info.
	 */
	private function set_networks() {
		$this->networks = array(
			'facebook'        => array(
				'color'    => '#3B5998',
				'selected' => 'true',
			),
			'twitter'         => array(
				'color'    => '#000000',
				'selected' => 'true',
			),
			'pinterest'       => array(
				'color'    => '#CB2027',
				'selected' => 'true',
			),
			'chatgpt'         => array(
				'color'    => '#000000',
				'selected' => 'false',
			),
			'claude'          => array(
				'color'    => '#D97757',
				'selected' => 'false',
			),
			'copilot'         => array(
				'color'    => '#0078D4',
				'selected' => 'false',
			),
			'gemini'          => array(
				'color'    => '#8E75B2',
				'selected' => 'false',
			),
			'grok'            => array(
				'color'    => '#000000',
				'selected' => 'false',
			),
			'perplexity'      => array(
				'color'    => '#1FB8CD',
				'selected' => 'false',
			),
			'email'           => array(
				'color'    => '#7d7d7d',
				'selected' => 'true',
			),
			'sms'             => array(
				'color'    => '#ffbd00',
				'selected' => 'true',
			),
			'messenger'       => array(
				'color'    => '#448AFF',
				'selected' => 'false',
				'url'      => 'messenger.com/',
			),
			'sharethis'       => array(
				'color'    => '#95D03A',
				'selected' => 'true',
			),
			'linkedin'        => array(
				'color'    => '#0077b5',
				'selected' => 'false',
			),
			'reddit'          => array(
				'color'    => '#ff4500',
				'selected' => 'false',
			),
			'tumblr'          => array(
				'color'    => '#32506d',
				'selected' => 'false',
			),
			'bluesky'         => array(
				'color'    => '#097AFE',
				'selected' => 'false',
			),
			'digg'            => array(
				'color'    => '#262626',
				'selected' => 'false',
			),
			'kakao'           => array(
				'color'    => '#F9DD4A',
				'selected' => 'false',
			),
			'kindleit'        => array(
				'color'    => '#363C3D',
				'selected' => 'false',
			),
			'outlook'         => array(
				'color'    => '#3070CB',
				'selected' => 'false',
			),
			'tencentqq'       => array(
				'color'    => '#5790F7',
				'selected' => 'false',
			),
			'trello'          => array(
				'color'    => '#0D63DE',
				'selected' => 'false',
			),
			'threads'         => array(
				'color'    => '#000000',
				'selected' => 'false',
			),
			'viber'           => array(
				'color'    => '#645EA4',
				'selected' => 'false',
			),
			'yummly'          => array(
				'color'    => '#E16120',
				'selected' => 'false',
			),
			'stumbleupon'     => array(
				'color'    => '#eb4924',
				'selected' => 'false',
			),
			'whatsapp'        => array(
				'color'    => '#25d366',
				'selected' => 'false',
			),
			'vk'              => array(
				'color'    => '#4c6c91',
				'selected' => 'false',
			),
			'weibo'           => array(
				'color'    => '#ff9933',
				'selected' => 'false',
			),
			'odnoklassniki'   => array(
				'color'    => '#d7772d',
				'selected' => 'false',
			),
			'xing'            => array(
				'color'    => '#1a7576',
				'selected' => 'false',
			),
			'print'           => array(
				'color'    => '#222222',
				'selected' => 'false',
			),
			'blogger'         => array(
				'color'    => '#ff8000',
				'selected' => 'false',
			),
			'flipboard'       => array(
				'color'    => '#e12828',
				'selected' => 'false',
			),
			'meneame'         => array(
				'color'    => '#ff6400',
				'selected' => 'false',
			),
			'mailru'          => array(
				'color'    => '#168de2',
				'selected' => 'false',
			),
			'delicious'       => array(
				'color'    => '#205cc0',
				'selected' => 'false',
			),
			'buffer'          => array(
				'color'    => '#323b43',
				'selected' => 'false',
			),
			'diigo'           => array(
				'color'    => '#5285c4',
				'selected' => 'false',
			),
			'diaspora'        => array(
				'color'    => '#000000',
				'selected' => 'false',
			),
			'douban'          => array(
				'color'    => '#2e963d',
				'selected' => 'false',
			),
			'evernote'        => array(
				'color'    => '#5ba525',
				'selected' => 'false',
			),
			'fark'            => array(
				'color'    => '#6a6a9c',
				'selected' => 'false',
			),
			'googlebookmarks' => array(
				'color'    => '#4285F4',
				'selected' => 'false',
			),
			'gmail'           => array(
				'color'    => '#D44638',
				'selected' => 'false',
			),
			'hackernews'      => array(
				'color'    => '#ff4000',
				'selected' => 'false',
			),
			'houzz'           => array(
				'color'    => '#4DBC15',
				'selected' => 'false',
			),
			'instapaper'      => array(
				'color'    => '#000000',
				'selected' => 'false',
			),
			'line'            => array(
				'color'    => '#00c300',
				'selected' => 'false',
			),
			'mastodon'        => array(
				'color'    => '#3088d4',
				'selected' => 'false',
			),
			'microsoftteams'  => array(
				'color'    => '#515bc1',
				'selected' => 'false',
			),
			'naver'           => array(
				'color'    => '#07bc5e',
				'selected' => 'false',
			),
			'nextdoor'        => array(
				'color'    => '#8ed500',
				'selected' => 'false',
			),
			'pinboard'        => array(
				'color'    => '#1f36f2',
				'selected' => 'false',
			),
			'plurk'           => array(
				'color'    => '#FF574D',
				'selected' => 'false',
			),
			'pocket'          => array(
				'color'    => '#ef4056',
				'selected' => 'false',
			),
			'qzone'           => array(
				'color'    => '#F1C40F',
				'selected' => 'false',
			),
			'refind'          => array(
				'color'    => '#4286f4',
				'selected' => 'false',
			),
			'renren'          => array(
				'color'    => '#005baa',
				'selected' => 'false',
			),
			'telegram'        => array(
				'color'    => '#37AEE2',
				'selected' => 'false',
			),
			'threema'         => array(
				'color'    => '#000000',
				'selected' => 'false',
			),
			'yahoomail'       => array(
				'color'    => '#720e9e',
				'selected' => 'false',
			),
			'wordpress'       => array(
				'color'    => '#21759b',
				'selected' => 'false',
			),
			'wechat'          => array(
				'color'    => '#4EC034',
				'selected' => 'false',
				'url'      => 'wechat.com/',
			),
			'livejournal'     => array(
				'color'    => '#00b0ea',
				'selected' => 'false',
			),
			'snapchat'        => array(
				'color'    => '#FFFC00',
				'selected' => 'false',
			),
		);
	}

	/**
	 * AJAX Call back to save the set up button config for setup.
	 *
	 * @action wp_ajax_set_button_config
	 */
	public function set_button_config() {
		check_ajax_referer( META_PREFIX, 'nonce' );

		$post = filter_input_array(
			INPUT_POST,
			array(
				'button' => FILTER_UNSAFE_RAW,
				'config' => array(
					'filter' => FILTER_DEFAULT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				),
				'first'  => FILTER_UNSAFE_RAW,
				'nonce'  => FILTER_UNSAFE_RAW,
				'fresh'  => FILTER_UNSAFE_RAW,
				'type'   => FILTER_UNSAFE_RAW,
			)
		);

		if ( 'true' === htmlspecialchars( $post['fresh'] ) ) {
			update_option( 'sharethis_fract', 'false' );
		}

		if ( true === empty( $post['button'] ) || true === empty( $post['config'] ) ) {
			wp_send_json_error( 'Button Config Set Failed' );
		}

		$networks = true === isset( $post['config']['networks'] ) ?
			array_map( 'sanitize_text_field', wp_unslash( $post['config']['networks'] ) ) :
			'';

		// Set Purposes.
		$purposes = isset( $post['config']['publisher_purposes'] ) ? $post['config']['publisher_purposes'] : '';

		$first  = ( true === isset( $post['first'] ) && 'upgrade' !== $post['first'] );
		$type   = ( false === empty( $post['type'] ) );
		$button = sanitize_text_field( wp_unslash( $post['button'] ) );
		$config = $post['config'];

		// If user doesn't have a sharethis account already.
		if ( false === $type ) {
			$newconfig[ strtolower( $button ) ] = $config;
			$config                             = $newconfig;
		} else {
			$config = 'platform' !== $button ? json_decode( str_replace( '\\', '', $config ), true ) : $config;
		}

		$restrictions = isset( $config[ $button ]['publisher_restrictions'] ) ? $config[ $button ]['publisher_restrictions'] : '';

		if ( false === $first ) {
			$current_config                        = get_option( 'sharethis_button_config', array() );
			$current_config                        = true === is_array( $current_config ) ? $current_config : array();
			$current_config[ $button ]             = $post['config'];
			$current_config[ $button ]['networks'] = $networks;

			if ( 'gdpr' === $button ) {
				$current_config[ $button ]['publisher_purposes'] = $purposes;
			}

			$config = $current_config;

			if ( 'gdpr' === $button ) {
				$config['gdpr']['publisher_restrictions'] = $restrictions;
			}
		}
		// Make sure bool is "true" or "false".
		if ( isset( $config['inline'] ) ) {
			$config['inline']['enabled'] = ( true === $config['inline']['enabled'] ||
										'1' === $config['inline']['enabled'] ||
										'true' === $config['inline']['enabled'] );
		}

		if ( isset( $config['sticky'] ) ) {
			$config['sticky']['enabled'] = ( true === $config['sticky']['enabled'] ||
										'1' === $config['sticky']['enabled'] ||
										'true' === $config['sticky']['enabled'] );
		}

		if ( isset( $config['gdpr'] ) ) {
			$config['gdpr']['enabled'] = ( true === $config['gdpr']['enabled'] ||
										'1' === $config['gdpr']['enabled'] ||
										'true' === $config['gdpr']['enabled'] );

			// Remove network.
			unset( $config['gdpr']['networks'] );
		}

		$enable_tool = true === $config[ strtolower( $button ) ]['enabled'] ? 'true' : 'false';

		update_option( 'sharethis_button_config', $config );

		if ( 'upgrade' === $first && 'platform' !== $button ) {
			update_option( 'sharethis_first_product', strtolower( $button ) );
			update_option( 'sharethis_' . strtolower( $button ), 'false' );
		}

		if ( 'platform' !== $button ) {
			update_option( 'sharethis_' . strtolower( $button ), $enable_tool );
		}
	}

	/**
	 * AJAX Call back to save the set up gdpr config for setup.
	 *
	 * @action wp_ajax_set_gdpr_config
	 */
	public function set_gdpr_config() {
		check_ajax_referer( META_PREFIX, 'nonce' );

		$post = filter_input_array(
			INPUT_POST,
			array(
				'button' => FILTER_UNSAFE_RAW,
				'config' => array(
					'filter' => FILTER_DEFAULT,
					'flags'  => FILTER_REQUIRE_ARRAY,
				),
				'first'  => FILTER_UNSAFE_RAW,
				'nonce'  => FILTER_UNSAFE_RAW,
				'type'   => FILTER_UNSAFE_RAW,
			)
		);

		if ( true === empty( $post['config'] ) ) {
			wp_send_json_error( 'GDPR Config Set Failed' );
		}

		$first          = false === empty( $post['first'] );
		$current_config = get_option( 'sharethis_button_config', true );
		$config         = false !== $current_config && null !== $current_config ? $current_config : array();
		$config['gdpr'] = $post['config'];

		// Make sure bool is "true" or "false".
		$config['gdpr']['enabled'] = ( true === $config['gdpr']['enabled'] ||
									'1' === $config['gdpr']['enabled'] ||
									'true' === $config['gdpr']['enabled'] );

		// Add purposes back.
		$config['gdpr']['publisher_purposes'] = $purposes;

		update_option( 'sharethis_button_config', $config );

		if ( $first ) {
			update_option( 'sharethis_gdpr', 'true' );
		}
	}

	/**
	 * Helper function get vendors.
	 *
	 * @return array
	 */
	private function get_vendors() {
		$response = wp_remote_get( 'https://vendorlist.consensu.org/v2/vendor-list.json' );

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
