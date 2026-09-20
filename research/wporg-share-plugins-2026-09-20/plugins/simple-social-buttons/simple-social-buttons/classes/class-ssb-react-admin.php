<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName

/**
 * Simple Social Buttons React Admin Interface
 *
 * Handles the React-based settings UI when both free and (if present) Pro
 * meet the minimum version. Enqueues assets only on the main SSB settings screen.
 *
 * @package SimpleSocialButtons
 * @since 7.0.0
 */
class Ssb_React_Admin {

	/**
	 * Minimum required version for React admin.
	 *
	 * @var string
	 */
	private const MIN_VERSION = '7.0.0';

	/**
	 * Required WordPress React dependencies.
	 *
	 * @var array<string>
	 */
	private const WP_DEPENDENCIES = array(
		'wp-element',
		'wp-components',
		'wp-i18n',
		'wp-api-fetch',
	);

	/**
	 * Additional script dependencies.
	 *
	 * @var array<string>
	 */
	private const SCRIPT_DEPENDENCIES = array(
		'jquery',
		'wp-color-picker',
	);

	/**
	 * Core settings sections (option names).
	 *
	 * @var array<string>
	 */
	const CORE_SECTIONS = array(
		'ssb_networks',
		'ssb_themes',
		'ssb_positions',
		'ssb_sidebar',
		'ssb_inline',
		'ssb_media',
		'ssb_popup',
		'ssb_flyin',
		'ssb_advanced',
		'ssb_snapchat',
	);

	/**
	 * Pro-specific sections (option names).
	 *
	 * @var array<string>
	 */
	const PRO_SECTIONS = array(
		'ssb_click_to_tweet',
		'ssb_ngg_gallery',
	);

	/**
	 * Whether to use React admin
	 *
	 * @var bool
	 */
	private $use_react_admin = false;

	/**
	 * Constructor.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function __construct() {
		// Defer version compatibility check to init hook to avoid premature translation loading.
		add_action( 'init', array( $this, 'ssb_check_version_compatibility' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'ssb_enqueue_react_scripts' ) );
		add_action( 'wp_ajax_ssb_get_settings', array( $this, 'ssb_get_settings' ) );
		add_action( 'wp_ajax_ssb_save_settings', array( $this, 'ssb_save_settings' ) );
		add_action( 'admin_menu', array( $this, 'ssb_add_react_menu' ), 5 );

		// Keep the cached plugin version data (PERF-2) fresh across updates/activations.
		add_action( 'upgrader_process_complete', array( $this, 'ssb_clear_plugin_version_cache' ) );
		add_action( 'activated_plugin', array( $this, 'ssb_clear_plugin_version_cache' ) );
	}

	/**
	 * Check version compatibility for React admin interface.
	 *
	 * Only relevant to the admin menu/React gate, so skip the plugin-data
	 * lookups entirely on front-end requests.
	 *
	 * @since 7.0.0
	 * @version 7.0.1
	 * @return void
	 */
	public function ssb_check_version_compatibility() {
		if ( ! is_admin() ) {
			return;
		}

		$free_version = $this->ssb_get_plugin_version( 'simple-social-buttons/simple-social-buttons.php' );
		$pro_version  = $this->ssb_get_plugin_version( 'simple-social-buttons-pro/simple-social-buttons-pro.php' );

		// Determine if React interface should be enabled.
		$this->use_react_admin = $this->ssb_should_enable_react_admin( $free_version, $pro_version );

		// Set filter for backward compatibility.
		$filter_value = $this->use_react_admin ? '__return_true' : '__return_false';
		add_filter( 'ssb_use_react_admin', $filter_value );
	}

	/**
	 * Determine if React admin should be enabled based on versions
	 *
	 * @param string|false $free_version Free plugin version.
	 * @param string|false $pro_version Pro plugin version.
	 * @return bool
	 *
	 * @since 7.0.0
	 */
	private function ssb_should_enable_react_admin( $free_version, $pro_version ) {
		if ( $pro_version ) {
			// Both plugins exist - check if both are 7.0.0+.
			return version_compare( $free_version, self::MIN_VERSION, '>=' )
				&& version_compare( $pro_version, self::MIN_VERSION, '>=' );
		} else {
			// Only free version exists - check if it's 7.0.0+.
			return version_compare( $free_version, self::MIN_VERSION, '>=' );
		}
	}

	/**
	 * Get plugin version safely, cached in a transient keyed by plugin file (PERF-2).
	 *
	 * Avoids loading wp-admin/includes/plugin.php and calling get_plugin_data() on
	 * every request; only done on a cache miss. Cache is invalidated on plugin
	 * updates/activations via ssb_clear_plugin_version_cache().
	 *
	 * @param string $plugin_file Plugin file path.
	 * @return string|false Plugin version or false on failure
	 *
	 * @since 7.0.0
	 * @version 7.0.1
	 */
	private function ssb_get_plugin_version( $plugin_file ) {
		$cache_key = $this->ssb_get_plugin_version_cache_key( $plugin_file );
		$cached    = get_transient( $cache_key );

		if ( is_array( $cached ) && array_key_exists( 'version', $cached ) ) {
			return $cached['version'];
		}

		if ( ! function_exists( 'get_plugin_data' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
		$version     = false;

		if ( file_exists( $plugin_path ) && is_plugin_active( $plugin_file ) ) {
			$plugin_data = get_plugin_data( $plugin_path );
			$version     = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : false;
		}

		set_transient( $cache_key, array( 'version' => $version ), DAY_IN_SECONDS );

		return $version;
	}

	/**
	 * Build the transient key used to cache a plugin's version (PERF-2).
	 *
	 * @param string $plugin_file Plugin file path.
	 * @return string
	 * @since 7.0.1
	 */
	private function ssb_get_plugin_version_cache_key( $plugin_file ) {
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_md5 -- Not used for security, just a short/stable cache key.
		return 'ssb_pv_' . md5( $plugin_file );
	}

	/**
	 * Clear cached plugin version data (PERF-2).
	 *
	 * Hooked to upgrader_process_complete and activated_plugin so a version
	 * change is reflected immediately instead of waiting out the transient TTL.
	 *
	 * @since 7.0.1
	 * @return void
	 */
	public function ssb_clear_plugin_version_cache() {
		delete_transient( $this->ssb_get_plugin_version_cache_key( 'simple-social-buttons/simple-social-buttons.php' ) );
		delete_transient( $this->ssb_get_plugin_version_cache_key( 'simple-social-buttons-pro/simple-social-buttons-pro.php' ) );
	}

	/**
	 * Add React-based admin menu.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_add_react_menu() {
		if ( ! $this->use_react_admin ) {
			return;
		}

		// Set global flag to prevent PHP admin from creating menu.
		global $ssb_react_admin_active;
		$ssb_react_admin_active = true;

		$this->ssb_add_main_menu_page();
		$this->ssb_add_submenu_pages();
	}

	/**
	 * Add the main menu page
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 * @version 7.0.1
	 */
	private function ssb_add_main_menu_page() {
		add_menu_page(
			'Simple Social Buttons',
			'Social Buttons',
			'activate_plugins',
			'simple-social-buttons',
			array( $this, 'ssb_render_react_admin' ),
			ssb_get_admin_menu_icon(),
			100
		);
	}

	/**
	 * Add all submenu pages
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 */
	private function ssb_add_submenu_pages() {
		// Settings submenu.
		add_submenu_page(
			'simple-social-buttons',
			'Settings',
			'Settings',
			'manage_options',
			'simple-social-buttons',
			array( $this, 'ssb_render_react_admin' )
		);

		// Help submenu.
		add_submenu_page(
			'simple-social-buttons',
			__( 'Help', 'simple-social-buttons' ),
			__( 'Help', 'simple-social-buttons' ),
			'manage_options',
			'ssb-help',
			array( $this, 'ssb_help_page_react' )
		);

		// Import/Export submenu.
		add_submenu_page(
			'simple-social-buttons',
			__( 'Import and export settings', 'simple-social-buttons' ),
			__( 'Import / Export', 'simple-social-buttons' ),
			'manage_options',
			'ssb-import-export',
			array( $this, 'ssb_import_export_page' )
		);

		// Pro submenu if Pro is active.
		if ( class_exists( 'Simple_Social_Buttons_Pro' ) ) {
			do_action( 'ssb_add_pro_submenu' );
		} else {
			$this->ssb_add_upgrade_submenu();
		}
	}

	/**
	 * Add the Upgrade to Pro submenu for Lite users.
	 *
	 * @since 7.1.0
	 * @return void
	 */
	private function ssb_add_upgrade_submenu() {
		$upgrade_url = add_query_arg(
			array(
				'utm_source'   => 'simple-social-buttons-lite',
				'utm_medium'   => 'admin-menu',
				'utm_campaign' => 'pro-upgrade',
			),
			'https://simplesocialbuttons.com/pricing/'
		);

		add_submenu_page(
			'simple-social-buttons',
			__( 'Upgrade to Pro', 'simple-social-buttons' ),
			__( 'Upgrade to Pro', 'simple-social-buttons' ),
			'manage_options',
			$upgrade_url
		);

		global $submenu;
		if ( ! isset( $submenu['simple-social-buttons'] ) ) {
			return;
		}

		foreach ( $submenu['simple-social-buttons'] as $position => $menu_item ) {
			if ( isset( $menu_item[2] ) && false !== strpos( $menu_item[2], 'simplesocialbuttons.com/pricing' ) ) {
				if ( isset( $submenu['simple-social-buttons'][ $position ][4] ) ) {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- add CSS class to Upgrade submenu.
					$submenu['simple-social-buttons'][ $position ][4] .= ' ssb-sidebar-upgrade-pro';
				} else {
					// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- add CSS class to Upgrade submenu.
					$submenu['simple-social-buttons'][ $position ][] = 'ssb-sidebar-upgrade-pro';
				}
				break;
			}
		}
	}

	/**
	 * Enqueue React scripts and styles.
	 *
	 * @param string $hook Current admin page hook.
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_enqueue_react_scripts( $hook ) {
		$this->ssb_enqueue_outside_plugin_admin_assets();

		if ( ! $this->ssb_should_load_scripts( $hook ) ) {
			return;
		}

		$this->ssb_enqueue_wp_dependencies();
		$this->ssb_enqueue_main_script();
		$this->ssb_enqueue_styles();
		wp_enqueue_media();
		$this->ssb_localize_script();
	}

	/**
	 * Enqueue Lite upgrade-menu assets across the WordPress admin.
	 *
	 * @since 7.1.0
	 * @return void
	 */
	private function ssb_enqueue_outside_plugin_admin_assets() {
		if ( class_exists( 'Simple_Social_Buttons_Pro' ) ) {
			return;
		}

		wp_enqueue_style(
			'ssb-outside-plugin-admin',
			SSB_PLUGIN_URL . 'assets/css/outside-plugin-admin.css',
			array(),
			SSB_VERSION
		);
		wp_enqueue_script(
			'ssb-outside-plugin-admin',
			SSB_PLUGIN_URL . 'assets/js/outside-plugin-admin.js',
			array( 'jquery' ),
			SSB_VERSION,
			false
		);
	}

	/**
	 * Check if scripts should be loaded
	 *
	 * Only loads React assets on the main SSB settings screen, not on Help or Import/Export.
	 *
	 * @param string $hook Current admin page hook.
	 * @return bool
	 *
	 * @since 7.0.0
	 */
	private function ssb_should_load_scripts( $hook ) {
		if ( ! $this->use_react_admin || strpos( $hook, 'simple-social-buttons' ) === false ) {
			return false;
		}
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen instanceof \WP_Screen && 'toplevel_page_simple-social-buttons' !== $screen->id ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Enqueue WordPress React dependencies.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	private function ssb_enqueue_wp_dependencies() {
		foreach ( self::WP_DEPENDENCIES as $dependency ) {
			wp_enqueue_script( $dependency );
		}
	}

	/**
	 * Enqueue the main React admin script
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 */
	private function ssb_enqueue_main_script() {
		$asset_data = $this->ssb_get_asset_data();

		wp_enqueue_script(
			'ssb-react-admin',
			SSB_PLUGIN_URL . 'build/index.js',
			array_merge( $asset_data['dependencies'], self::SCRIPT_DEPENDENCIES ),
			$asset_data['version'],
			true
		);
	}

	/**
	 * Enqueue required styles
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 */
	private function ssb_enqueue_styles() {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_style(
			'ssb-admin-react',
			SSB_PLUGIN_URL . 'assets/css/admin.css',
			array( 'wp-color-picker' ),
			SSB_VERSION
		);
	}

	/**
	 * Localize script with data
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 * @version 7.0.1
	 */
	private function ssb_localize_script() {
		wp_localize_script(
			'ssb-react-admin',
			'ssbReact',
			array(
				'nonce'         => wp_create_nonce( 'ssb_react_nonce' ),
				'activateNonce' => wp_create_nonce( 'activate-plugin_simple-social-buttons-pro/simple-social-buttons-pro.php' ),
				'apiUrl'        => rest_url( 'wp/v2/' ),
				'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
				'pluginUrl'     => SSB_PLUGIN_URL,
				'adminUrl'      => admin_url(),
				'isPro'         => class_exists( 'Simple_Social_Buttons_Pro' ),
				'proInstalled'  => file_exists( WP_PLUGIN_DIR . '/simple-social-buttons-pro/simple-social-buttons-pro.php' ),
				'hasNextGen'    => class_exists( 'C_Photocrati_Installer' ),
				'adminEmail'    => get_option( 'admin_email' ),
				'currentUser'   => wp_get_current_user()->display_name,
				'postTypes'     => $this->ssb_get_post_types(),
				'version'       => SSB_VERSION,
				'knownButtons'  => function_exists( 'ssb_get_known_buttons' ) ? ssb_get_known_buttons() : array(),
			)
		);
	}

	/**
	 * Get asset file data with fallback.
	 *
	 * @since 7.0.0
	 * @return array{dependencies: array, version: string} Asset dependencies and version.
	 */
	private function ssb_get_asset_data() {
		$asset_file_path = SSB_PLUGIN_DIR . 'build/index.asset.php';

		if ( ! file_exists( $asset_file_path ) ) {
			return array(
				'dependencies' => array(),
				'version'      => SSB_VERSION,
			);
		}

		$asset_data = include $asset_file_path;

		return array_merge(
			array(
				'dependencies' => array(),
				'version'      => SSB_VERSION,
			),
			array( $asset_data )
		);
	}

	/**
	 * Get available post types for the React UI.
	 *
	 * @since 7.0.0
	 * @return array<string, string> Post type slug => label.
	 */
	private function ssb_get_post_types() {
		$post_types_list   = array( 'home' => 'Home' );
		$public_post_types = get_post_types( array( 'public' => true ) );

		foreach ( $public_post_types as $post_type ) {
			$post_types_list[ $post_type ] = ucfirst( $post_type );
		}

		return $post_types_list;
	}

	/**
	 * Render React admin page.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_render_react_admin() {
		$this->ssb_add_help_tab();
		$this->ssb_render_admin_html();
		$this->ssb_render_initialization_script();
	}

	/**
	 * Add help tab to current screen
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 */
	private function ssb_add_help_tab() {
		if ( function_exists( 'get_current_screen' ) ) {
			$screen = get_current_screen();
			if ( $screen ) {
				$screen->add_help_tab(
					array(
						'id'      => 'ssb-help-overview',
						'title'   => __( 'Overview', 'simple-social-buttons' ),
						'content' => '<p>' . __( 'This is the React-powered admin interface for Simple Social Buttons.', 'simple-social-buttons' ) . '</p>',
					)
				);
			}
		}
	}

	/**
	 * Render the admin HTML structure
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 */
	private function ssb_render_admin_html() {
		?>
		<div class="wrap ssb-admin-page">

			<?php if ( class_exists( 'Ssb_Pro_React_Admin' ) || ! class_exists( 'Simple_Social_Buttons_Pro' ) ) : ?>
				<?php Ssb_Settings_Structure::ssb_banner_content(); ?>
				<div id="ssb-react-admin-root"></div>
			<?php endif; ?>

		</div>
		<?php
	}

	/**
	 * Render the React initialization script
	 *
	 * @return void
	 *
	 * @since 7.0.0
	 */
	private function ssb_render_initialization_script() {
		?>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				if (typeof window.SSBAdminInit === 'function') {
					window.SSBAdminInit();
				} else {
					setTimeout(function() {
						if (typeof window.SSBAdminInit === 'function') {
							window.SSBAdminInit();
						}
					}, 100);
				}
			});
		</script>
		<?php
	}

	/**
	 * AJAX handler to get settings.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_get_settings() {
		$this->ssb_verify_ajax_request();

		$settings = $this->ssb_get_all_settings();
		$settings = $this->ssb_process_settings_for_display( $settings );

		wp_send_json_success( $settings );
	}

	/**
	 * Verify AJAX request security and permissions.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	private function ssb_verify_ajax_request() {
		check_ajax_referer( 'ssb_react_nonce', '_wpnonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( 'Permission denied' );
		}
	}

	/**
	 * Get all plugin settings.
	 *
	 * @since 7.0.0
	 * @return array<string, array> Option name => option value.
	 */
	private function ssb_get_all_settings() {
		$settings = array();
		$sections = $this->ssb_get_all_sections();

		foreach ( $sections as $section ) {
			$settings[ $section ] = get_option( $section, array() );
		}

		return $settings;
	}

	/**
	 * Get all settings sections (core + pro if available).
	 *
	 * @since 7.0.0
	 * @return array<int, string> List of section option names.
	 */
	private function ssb_get_all_sections() {
		$sections = self::CORE_SECTIONS;

		if ( class_exists( 'Simple_Social_Buttons_Pro' ) ) {
			$pro_sections = self::PRO_SECTIONS;

			// Only include NextGEN Gallery section if NextGen plugin is installed.
			if ( ! class_exists( 'C_Photocrati_Installer' ) ) {
				$pro_sections = array_filter(
					$pro_sections,
					function ( $section ) {
						return 'ssb_ngg_gallery' !== $section;
					}
				);
			}

			$sections = array_merge( $sections, $pro_sections );
		}

		return $sections;
	}

	/**
	 * Check whether a section is a valid plugin settings section.
	 *
	 * @param string $section_name Section name.
	 * @return bool
	 * @since 7.0.0
	 */
	private function ssb_is_valid_section( $section_name ) {
		return in_array( $section_name, $this->ssb_get_all_sections(), true );
	}

	/**
	 * Keys that store boolean-like values ('0'/'1'). Normalized to true/false for React.
	 *
	 * @var array<string>
	 */
	private const BOOLEAN_KEYS = array(
		'share_counts',
		'total_share',
		'hide_mobile',
		'display_hover_text',
		'flat_button_sidebar',
		'icon_space',
		'icon_limit',
		'sticky_mobile_bottom',
		'trigger_after_scrolling',
		'trigger_before_leaving',
		'ssb_og_tags',
		'ssb_uninstall_data',
		'ssb_factory_reset',
		'show_on_category',
		'show_on_archive',
		'show_on_tag',
		'show_on_search',
		'ssb_enable_ngg_setting',
		'ssb_enable_ngg_on_list',
	);

	/**
	 * Keys that store numeric values. Normalized to integers for React.
	 *
	 * @var array<string>
	 * @version 7.0.0
	 */
	private const INTEGER_KEYS = array(
		'icon_space_value',
		'icon_limit_value',
		'time_interval',
		'trigger_after_scrolling_value',
		'ssb_internal_flush_interval',
	);

	/**
	 * Process settings for display (decode base64, normalize types for React).
	 *
	 * @since 7.0.0
	 * @param array<string, array> $settings Raw settings array.
	 * @return array<string, array> Processed settings array.
	 */
	private function ssb_process_settings_for_display( $settings ) {
		if ( isset( $settings['ssb_advanced']['ssb_js'] ) && '' !== $settings['ssb_advanced']['ssb_js'] ) {
			$settings['ssb_advanced']['ssb_js'] = ssb_get_custom_js_for_output( $settings['ssb_advanced']['ssb_js'] );
		}
		return $this->ssb_normalize_settings_for_react( $settings );
	}

	/**
	 * Normalize scalar values (booleans, integers) so React receives proper types.
	 *
	 * @since 7.0.0
	 * @param array $data Settings array (may be nested).
	 * @return array Normalized copy.
	 */
	private function ssb_normalize_settings_for_react( $data ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		$out = array();
		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) && ! $this->ssb_is_posts_array( $value ) ) {
				$out[ $key ] = $this->ssb_normalize_settings_for_react( $value );
			} elseif ( in_array( $key, self::BOOLEAN_KEYS, true ) ) {
				$out[ $key ] = rest_sanitize_boolean( $value );
			} elseif ( in_array( $key, self::INTEGER_KEYS, true ) ) {
				$out[ $key ] = is_numeric( $value ) ? (int) $value : $value;
			} else {
				$out[ $key ] = $value;
			}
		}
		return $out;
	}

	/**
	 * Check if array is a post-types map (e.g. ['post' => 'post', 'page' => 'page']).
	 *
	 * @param array $arr Array to check.
	 * @return bool
	 */
	private function ssb_is_posts_array( $arr ) {
		if ( empty( $arr ) || ! is_array( $arr ) ) {
			return false;
		}
		foreach ( array_keys( $arr ) as $k ) {
			if ( ! is_string( $k ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * AJAX handler to save settings.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_save_settings() {
		$this->ssb_verify_ajax_request();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotValidated,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$section = isset( $_POST['section'] ) ? sanitize_text_field( wp_unslash( $_POST['section'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized,WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		$data = isset( $_POST['data'] ) ? wp_unslash( $_POST['data'] ) : '';

		if ( 'all' === $section ) {
			$this->ssb_save_all_sections( $data );
		} else {
			$this->ssb_save_single_section( $section, $data );
		}
	}

	/**
	 * Save all sections at once
	 *
	 * @param string $data JSON encoded data.
	 * @return void
	 * @since 7.0.0
	 */
	private function ssb_save_all_sections( $data ) {
		// Do not use stripslashes() on JSON — it corrupts escape sequences (e.g. \n becomes literal "n").
		$all_data = json_decode( $data, true );

		if ( ! $all_data ) {
			wp_send_json_error( array( 'message' => __( 'Invalid data', 'simple-social-buttons' ) ) );
			return;
		}

		// Check if factory reset is triggered.
		$factory_reset_triggered = false;
		if ( isset( $all_data['ssb_advanced']['ssb_factory_reset'] ) && '1' === $all_data['ssb_advanced']['ssb_factory_reset'] ) {
			$factory_reset_triggered = true;
			$this->ssb_factory_reset();
		}

		foreach ( $all_data as $section_name => $section_data ) {
			if ( ! $this->ssb_is_valid_section( $section_name ) ) {
				wp_send_json_error(
					array(
						/* translators: %s: invalid section name. */
						'message' => sprintf( __( 'Invalid settings section: %s', 'simple-social-buttons' ), $section_name ),
					)
				);
				return;
			}

			if ( $factory_reset_triggered ) {
				// Use restored defaults instead of incoming data.
				$sanitized_data = $this->ssb_get_restored_defaults( $section_name );
				if ( empty( $sanitized_data ) ) {
					// For sections without specific defaults, get from option (which was just restored).
					$sanitized_data = get_option( $section_name, array() );
				}
			} else {
				$sanitized_data = $this->ssb_sanitize_settings_data( $section_data );
				$sanitized_data = $this->ssb_apply_section_defaults( $section_name, $sanitized_data );
				$sanitized_data = $this->ssb_handle_special_cases( $section_name, $sanitized_data );
			}

			update_option( $section_name, $sanitized_data );
		}

		wp_send_json_success(
			array(
				'message'       => $factory_reset_triggered ? __( 'Factory reset completed! All settings restored to defaults.', 'simple-social-buttons' )
				: __( 'All settings saved successfully!', 'simple-social-buttons' ),
				'sections'      => count( $all_data ),
				'factory_reset' => $factory_reset_triggered,
			)
		);
	}

	/**
	 * Save a single section.
	 *
	 * @since 7.0.0
	 * @param string       $section Section name.
	 * @param string|array $data    Section data (JSON string or array).
	 * @return void
	 */
	private function ssb_save_single_section( $section, $data ) {
		if ( ! $this->ssb_is_valid_section( $section ) ) {
			wp_send_json_error(
				array(
					/* translators: %s: invalid section name. */
					'message' => sprintf( __( 'Invalid settings section: %s', 'simple-social-buttons' ), $section ),
				)
			);
			return;
		}

		$sanitized_data = $this->ssb_sanitize_settings_data( $data );
		$sanitized_data = $this->ssb_apply_section_defaults( $section, $sanitized_data );
		$sanitized_data = $this->ssb_handle_special_cases( $section, $sanitized_data );

		update_option( $section, $sanitized_data );

		wp_send_json_success(
			array(
				'message' => 'Settings saved successfully!',
				'section' => $section,
				'data'    => $sanitized_data,
			)
		);
	}

	/**
	 * Apply section-specific defaults
	 *
	 * @param string $section_name Section name.
	 * @param array  $data Section data.
	 * @return array Data with defaults applied.
	 * @since 7.0.0
	 */
	private function ssb_apply_section_defaults( $section_name, $data ) {
		$defaults = $this->ssb_get_section_defaults( $section_name );
		$merged   = array_merge( $defaults, $data );

		// Special handling for ngg_gallery - preserve empty strings for description.
		if ( 'ssb_ngg_gallery' === $section_name && isset( $data['ssb_enable_ngg_description'] ) ) {
			// If user explicitly set it to empty string, preserve it.
			if ( '' === $data['ssb_enable_ngg_description'] ) {
				$merged['ssb_enable_ngg_description'] = '';
			}
		}

		return $merged;
	}

	/**
	 * Get defaults for a specific section
	 *
	 * @param string $section_name Section name.
	 * @return array Section defaults.
	 * @since 7.0.0
	 */
	private function ssb_get_section_defaults( $section_name ) {
		$defaults_map = array(
			'ssb_networks'    => array(
				'icon_selection' => 'fbshare,twitter,linkedin',
				'custom_buttons' => array(),
			),
			'ssb_advanced'    => $this->ssb_get_advanced_defaults(),
			'ssb_popup'       => $this->ssb_get_popup_defaults(),
			'ssb_flyin'       => $this->ssb_get_flyin_defaults(),
			'ssb_media'       => $this->ssb_get_media_defaults(),
			'ssb_sidebar'     => $this->ssb_get_sidebar_defaults(),
			'ssb_inline'      => $this->ssb_get_inline_defaults(),
			'ssb_ngg_gallery' => $this->ssb_get_ngg_gallery_defaults(),
			'ssb_snapchat'    => $this->ssb_get_snapchat_defaults(),
		);

		return isset( $defaults_map[ $section_name ] ) ? $defaults_map[ $section_name ] : array();
	}

	/**
	 * Snapchat (Creative Kit) section defaults.
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_snapchat_defaults() {
		return array(
			'snapchat_client_id' => '',
		);
	}

	/**
	 * Get advanced section defaults.
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_advanced_defaults() {
		return array(
			'twitter_handle'              => '',
			'http_https_resolve'          => '0',
			'ssb_internal_flush_interval' => '2',
			'ssb_og_tags'                 => '1',
			'ssb_uninstall_data'          => '0',
			'ssb_factory_reset'           => '0',
			'facebook_app_id'             => '',
			'facebook_app_secret'         => '',
			'ssb_facebook_page_url'       => '',
			'ssb_css'                     => '',
			'ssb_js'                      => '',
		);
	}

	/**
	 * Get popup section defaults
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_popup_defaults() {
		return array(
			'time_interval'                 => '1440',
			'share_counts'                  => '0',
			'hide_mobile'                   => '0',
			'animation'                     => 'no-animation',
			'trigger_after_scrolling_value' => '',
			'trigger_before_leaving'        => '0',
			'trigger_after_scrolling'       => '0',
			'icon_alignment'                => 'left',
			'total_share'                   => '0',
			'posts'                         => array(
				'home' => 'home',
				'post' => 'post',
			),
		);
	}

	/**
	 * Get flyin section defaults
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_flyin_defaults() {
		return array(
			'time_interval'  => '1440',
			'share_counts'   => '0',
			'hide_mobile'    => '0',
			'animation'      => 'no-animation',
			'postion'        => 'bottom-right',
			'icon_alignment' => 'left',
			'total_share'    => '0',
			'posts'          => array( 'post' => 'post' ),
		);
	}

	/**
	 * Get media section defaults
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_media_defaults() {
		return array(
			'display_position' => 'top-left',
			'share_counts'     => '0',
			'hide_mobile'      => '0',
			'icon_alignment'   => 'left',
			'total_share'      => '0',
			'posts'            => array( 'post' => 'post' ),
		);
	}

	/**
	 * Get sidebar section defaults
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_sidebar_defaults() {
		return array(
			'orientation'          => 'left',
			'animation'            => 'no-animation',
			'share_counts'         => '0',
			'total_share'          => '0',
			'display_hover_text'   => '0',
			'flat_button_sidebar'  => '0',
			'icon_space'           => '0',
			'icon_space_value'     => '10',
			'sticky_mobile_bottom' => '0',
			'hide_mobile'          => '0',
			'icon_limit'           => '0',
			'icon_limit_value'     => '3',
			'posts'                => array( 'post' => 'post' ),
		);
	}

	/**
	 * Get inline section defaults
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_inline_defaults() {
		return array(
			'location'         => 'below',
			'icon_alignment'   => 'left',
			'animation'        => 'no-animation',
			'share_counts'     => '0',
			'total_share'      => '0',
			'icon_space'       => '0',
			'icon_space_value' => '10',
			'hide_mobile'      => '0',
			'icon_limit'       => '0',
			'icon_limit_value' => '3',
			'show_on_category' => '0',
			'show_on_archive'  => '0',
			'show_on_tag'      => '0',
			'show_on_search'   => '0',
			'share_title'      => '',
			'posts'            => array( 'post' => 'post' ),
		);
	}

	/**
	 * Get NextGEN Gallery section defaults
	 *
	 * @return array
	 * @since 7.0.0
	 */
	private function ssb_get_ngg_gallery_defaults() {
		return array(
			'ssb_enable_ngg_setting'     => '0',
			'ssb_enable_ngg_on_list'     => '0',
			'ssb_enable_ngg_description' => '%image_url%',
		);
	}

	/**
	 * Parse icon_selection string into an array of trimmed, non-empty IDs.
	 *
	 * @param string|null $string_value Comma-separated icon_selection value.
	 * @return array<int,string> List of icon IDs.
	 * @since 7.0.0
	 */
	public static function ssb_parse_icon_selection( $string_value ) {
		$s     = ( isset( $string_value ) && is_string( $string_value ) ) ? $string_value : '';
		$order = array_map( 'trim', explode( ',', $s ) );
		$order = array_filter( $order );
		$order = array_values(
			array_filter(
				$order,
				function ( $id ) {
					return 'fblike' !== $id;
				}
			)
		);
		return array_values( $order );
	}

	/**
	 * Build icon_selection string from an array of icon IDs.
	 *
	 * @param array $array_value List of icon IDs (strings).
	 * @return string Comma-separated icon_selection value.
	 * @since 7.0.0
	 */
	public static function ssb_implode_icon_selection( $array_value ) {
		if ( ! is_array( $array_value ) ) {
			return '';
		}
		$order = array_map(
			function ( $v ) {
				return is_string( $v ) ? trim( $v ) : '';
			},
			$array_value
		);
		$order = array_values( array_filter( $order ) );
		return implode( ',', $order );
	}

	/**
	 * Handle special cases like factory reset
	 *
	 * @param string $section_name Section name.
	 * @param array  $data Section data.
	 * @return array Processed data
	 * @since 7.0.0
	 */
	private function ssb_handle_special_cases( $section_name, $data ) {
		if ( in_array( $section_name, array( 'ssb_inline', 'ssb_sidebar' ), true ) ) {
			if (
				isset( $data['icon_limit'] )
				&& rest_sanitize_boolean( $data['icon_limit'] )
				&& ( ! isset( $data['icon_limit_value'] ) || absint( $data['icon_limit_value'] ) < 3 )
			) {
				$data['icon_limit_value'] = 3;
			}
		}

		// Handle factory reset.
		if ( 'ssb_advanced' === $section_name && isset( $data['ssb_factory_reset'] ) && '1' === $data['ssb_factory_reset'] ) {
			$this->ssb_factory_reset();
			// Return the restored default values instead of the incoming data.
			return $this->ssb_get_restored_defaults( $section_name );
		}

		if ( 'ssb_advanced' === $section_name ) {
			$this->ssb_reschedule_internal_share_flush();
		}

		// Preserve custom buttons in the database when Pro is inactive (front-end hides them).
		if ( 'ssb_networks' === $section_name && ! ssb_is_pro_active() ) {
			$existing_networks = get_option( 'ssb_networks', array() );
			if ( ! is_array( $existing_networks ) ) {
				$existing_networks = array();
			}

			if ( ! empty( $existing_networks['custom_buttons'] ) && is_array( $existing_networks['custom_buttons'] ) ) {
				$data['custom_buttons'] = $existing_networks['custom_buttons'];
			}

			$existing_order   = self::ssb_parse_icon_selection( $existing_networks['icon_selection'] ?? '' );
			$incoming_order   = self::ssb_parse_icon_selection( $data['icon_selection'] ?? '' );
			$preserved_custom = array_values(
				array_filter(
					$existing_order,
					function ( $id ) {
						return ssb_is_custom_button_id( (string) $id );
					}
				)
			);
			$incoming_builtin = array_values(
				array_filter(
					$incoming_order,
					function ( $id ) {
						return ! ssb_is_custom_button_id( (string) $id );
					}
				)
			);

			$data['icon_selection'] = self::ssb_implode_icon_selection( array_merge( $incoming_builtin, $preserved_custom ) );
		}

		// Sanitize custom_buttons and sync icon_selection for ssb_networks (full form save only).
		if ( 'ssb_networks' === $section_name && isset( $data['custom_buttons'] ) && is_array( $data['custom_buttons'] ) ) {
			$allowed_keys = array( 'label', 'url', 'icon_url', 'bg_color', 'hover_bg_color', 'text_color' );
			$defaults     = array(
				'label'          => '',
				'url'            => '',
				'icon_url'       => '',
				'bg_color'       => '#0865ff',
				'hover_bg_color' => '#1557b0',
				'text_color'     => '#ffffff',
			);
			$sanitized    = array();
			foreach ( $data['custom_buttons'] as $cid => $entry ) {
				if ( ! preg_match( '/^custom_\d+$/', (string) $cid ) ) {
					continue;
				}
				$row = is_array( $entry ) ? $entry : array();
				$out = $defaults;
				foreach ( $allowed_keys as $key ) {
					if ( ! isset( $row[ $key ] ) ) {
						continue;
					}
					$v = $row[ $key ];
					if ( 'url' === $key || 'icon_url' === $key ) {
						$out[ $key ] = is_string( $v ) ? esc_url_raw( $v ) : '';
					} elseif ( 'bg_color' === $key || 'hover_bg_color' === $key || 'text_color' === $key ) {
						$out[ $key ] = is_string( $v ) ? sanitize_hex_color( $v ) : $out[ $key ];
						if ( empty( $out[ $key ] ) ) {
							$out[ $key ] = ( 'text_color' === $key ) ? '#ffffff' : ( ( 'hover_bg_color' === $key ) ? '#1557b0' : '#0865ff' );
						}
					} else {
						$out[ $key ] = sanitize_text_field( $v );
					}
				}
				$sanitized[ $cid ] = $out;
			}
			$data['custom_buttons'] = $sanitized;
			$order                  = self::ssb_parse_icon_selection( isset( $data['icon_selection'] ) ? $data['icon_selection'] : null );
			$valid_custom           = array_keys( $data['custom_buttons'] );
			$order                  = array_values(
				array_filter(
					$order,
					function ( $id ) use ( $valid_custom ) {
						if ( preg_match( '/^custom_\d+$/', $id ) ) {
							return in_array( $id, $valid_custom, true );
						}
						return true;
					}
				)
			);
			$data['icon_selection'] = self::ssb_implode_icon_selection( $order );
		}

		return $data;
	}

	/**
	 * Reschedule internal share flush event after settings update.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	private function ssb_reschedule_internal_share_flush() {
		wp_clear_scheduled_hook( 'ssb_flush_internal_share_queue' );
		wp_schedule_event( time() + 5 * MINUTE_IN_SECONDS, 'ssb_internal_share_flush', 'ssb_flush_internal_share_queue' );
	}

	/**
	 * Sanitize settings data
	 *
	 * @param mixed $data Data to sanitize.
	 * @return array Sanitized data.
	 * @since 7.0.0
	 */
	private function ssb_sanitize_settings_data( $data ) {
		if ( ! is_array( $data ) ) {
			return array();
		}

		$sanitized = array();

		foreach ( $data as $key => $value ) {
			if ( is_array( $value ) ) {
				$sanitized[ $key ] = $this->ssb_sanitize_settings_data( $value );
			} else {
				$sanitized[ $key ] = $this->ssb_sanitize_field_value( $key, $value );
			}
		}

		return $sanitized;
	}

	/**
	 * Sanitize individual field value based on field type
	 *
	 * @param string $key Field key.
	 * @param mixed  $value Field value.
	 * @return mixed Sanitized value.
	 * @since 7.0.0
	 * @version 7.0.1
	 */
	private function ssb_sanitize_field_value( $key, $value ) {
		switch ( $key ) {
			case 'ssb_css':
				return ssb_sanitize_custom_css( $value );
			case 'ssb_js':
				// Only users allowed unfiltered_html may persist Custom JS; otherwise preserve what's stored.
				if ( ! function_exists( 'ssb_user_can_save_custom_js' ) || ! ssb_user_can_save_custom_js() ) {
					$existing_advanced = get_option( 'ssb_advanced' );
					return is_array( $existing_advanced ) && isset( $existing_advanced['ssb_js'] ) ? $existing_advanced['ssb_js'] : '';
				}
				// Incoming base64 from client (transport only). Decode once and store raw.
				if ( is_string( $value ) && preg_match( '/^[A-Za-z0-9+\/=]+\s*$/', trim( $value ) ) ) {
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
					$decoded = base64_decode( trim( $value ), true );
					if ( false !== $decoded ) {
						return preg_replace( '/[\x00]/', '', $decoded );
					}
				}
				return $value;
			case 'ssb_enable_ngg_description':
				// Allow HTML tags and variables for custom message.
				return wp_kses_post( $value );
			case 'ssb_facebook_page_url':
				return is_string( $value ) ? esc_url_raw( $value ) : '';
			case 'icon_space_value':
			case 'time_interval':
			case 'trigger_after_scrolling_value':
				return absint( $value );
			case 'icon_limit_value':
				return max( 3, absint( $value ) );
			case 'share_counts':
			case 'total_share':
			case 'hide_mobile':
			case 'icon_space':
			case 'icon_limit':
			case 'display_hover_text':
			case 'flat_button_sidebar':
			case 'sticky_mobile_bottom':
			case 'trigger_after_scrolling':
			case 'trigger_before_leaving':
			case 'show_on_category':
			case 'show_on_archive':
			case 'show_on_tag':
			case 'show_on_search':
			case 'ssb_og_tags':
			case 'ssb_uninstall_data':
			case 'ssb_factory_reset':
			case 'ssb_enable_ngg_setting':
			case 'ssb_enable_ngg_on_list':
				return rest_sanitize_boolean( $value ) ? '1' : '0';
			case 'location':
			case 'animation':
			case 'icon_alignment':
			case 'orientation':
			case 'display_position':
			case 'postion':
				return sanitize_key( $value );
			case 'facebook_app_id':
			case 'facebook_app_secret':
			case 'twitter_handle':
			case 'share_title':
			case 'snapchat_client_id':
				return sanitize_text_field( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Factory reset all settings.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	private function ssb_factory_reset() {
		$sections = $this->ssb_get_all_sections();

		// Delete all options.
		foreach ( $sections as $section ) {
			delete_option( $section );
		}

		// Restore default values.
		$this->ssb_restore_default_settings();
	}

	/**
	 * Restore default settings after factory reset.
	 *
	 * @since 7.0.0
	 * @return void
	 */
	private function ssb_restore_default_settings() {
		// Restore default networks.
		update_option(
			'ssb_networks',
			array(
				'icon_selection' => 'fbshare,twitter,linkedin',
				'custom_buttons' => array(),
			)
		);

		// Restore default themes.
		update_option(
			'ssb_themes',
			array(
				'icon_style' => 'simple-icons',
			)
		);

		// Restore default positions.
		update_option(
			'ssb_positions',
			array(
				'position' => array(
					'inline' => 'inline',
				),
			)
		);

		// Restore default inline settings.
		update_option(
			'ssb_inline',
			array(
				'location' => 'below',
				'posts'    => array(
					'post' => 'post',
				),
			)
		);

		// Restore default advanced settings.
		update_option(
			'ssb_advanced',
			$this->ssb_get_advanced_defaults()
		);

		update_option(
			'ssb_snapchat',
			$this->ssb_get_snapchat_defaults()
		);
	}

	/**
	 * Get restored default values for a section after factory reset
	 *
	 * @param string $section_name Section name.
	 * @return array Default values for the section.
	 * @since 7.0.0
	 */
	private function ssb_get_restored_defaults( $section_name ) {
		switch ( $section_name ) {
			case 'ssb_advanced':
				return $this->ssb_get_advanced_defaults();
			case 'ssb_networks':
				return array(
					'icon_selection' => 'fbshare,twitter,linkedin',
					'custom_buttons' => array(),
				);
			case 'ssb_themes':
				return array(
					'icon_style' => 'simple-icons',
				);
			case 'ssb_positions':
				return array(
					'position' => array(
						'inline' => 'inline',
					),
				);
			case 'ssb_inline':
				return array(
					'location' => 'below',
					'posts'    => array(
						'post' => 'post',
					),
				);
			case 'ssb_snapchat':
				return $this->ssb_get_snapchat_defaults();
			default:
				return array();
		}
	}

	/**
	 * Help page (React version).
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_help_page_react() {
		include_once SSB_PLUGIN_DIR . '/inc/ssb-help-react.php';
	}

	/**
	 * Import/Export page (reuse existing functionality).
	 *
	 * @since 7.0.0
	 * @return void
	 */
	public function ssb_import_export_page() {
		include_once SSB_PLUGIN_DIR . '/inc/ssb-import-export-react.php';
	}
}

// Initialize React Admin if version compatibility is met.
new Ssb_React_Admin();
