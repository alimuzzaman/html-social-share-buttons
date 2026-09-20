<?php

use Mediavine\Grow\Settings;
use Mediavine\Grow\Share_Counts;
use Mediavine\Grow\Status_API_Controller;

/**
 * Core plugin class.
 */
class Social_Pug {

	public const API_NAMESPACE = 'mv-grow-social/v1';

	/** @var string|null Build tool sets this. */
	const VERSION = '1.36.3.1';

	/** @var string|null Version number for this release. @deprecated Use MV_GROW_VERSION */
	public static $VERSION;

	private static $instance = null;

	/** @var Status_API_Controller */
	private $status_api_controller;

	/** @var \Mediavine\Grow\Asset_Loader  */
	public $asset_loader = null;

	/** @var \Mediavine\Grow\Frontend_Data */
	public $frontend_data = null;

	/** @var \Mediavine\Grow\Admin_Notices */
	public $admin_notices = null;

	/** @var \Mediavine\Grow\Settings_API */
	public $settings_api = null;

	/** @var \Mediavine\Grow\Networks */
	public $networks = null;

	/**  @var \Mediavine\Grow\Icons */
	public $icons = null;

	/**  @var \Mediavine\Grow\Subscribe_Widget */
	public $subscribe_widget = null;

	/** @var \Mediavine\Grow\Tools\Toolkit Container for all the tools. */
	public $tools = null;

	/** @var Share_Counts|null Count class. */
	public $share_counts = null;

	/** @var string $has_license Whether or not there is a license */
	public $has_license = false;

	/**
	 * Get the defined addon version, if available.
	 *
	 * @return string|null
	 */
	public function get_version() : ?string {
		$version = defined( 'MV_GROW_VERSION' ) ? MV_GROW_VERSION : null;
		return $version;
	}

	/**
	 * Determine our version number depending on whether plugin has been built or is in development.
	 */
	public function set_version() {
		if ( ! is_null( self::VERSION ) ) {
			// If the build tool has run, use its version.
			self::$VERSION = self::VERSION; // @codingStandardsIgnoreLine
			define( 'MV_GROW_VERSION', self::VERSION );
			define( 'HUBBUB_VERSION', self::VERSION );
			return;
		}
		// Pull version from the plugin bootstrap file
		$version = \get_file_data(DPSP_PLUGIN_DIR . '/index.php', [
			'version' => 'Version',
		])['version'];

		$version = ! empty( $version ) ? $version : '99';
		self::$VERSION = $version; // @codingStandardsIgnoreLine
		define( 'MV_GROW_VERSION', $version );
		define( 'HUBBUB_VERSION', $version );
	}

	/**
	 * Singleton factory.
	 *
	 * @return Social_Pug|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Plugin bootstrap.
	 */
	public function init() {
		$this->set_version();
		define( 'DPSP_VERSION', self::$VERSION );

		define( 'DPSP_TRANSLATION_TEXTDOMAIN', 'social-pug' );

		$this->has_license = (bool) Settings::get_setting( 'mv_grow_license', false );

		// Register feature flags early.
		add_action( 'after_setup_theme', '\Mediavine\Grow\register_flags' );

		// Setup compatibility hooks.
		add_action( 'wp_head', [ 'Mediavine\Grow\Compatibility', 'disable_known_meta_tags' ], 1 );
		add_action( 'wp', [ 'Mediavine\Grow\Compatibility', 'set_yoast_meta_data' ], 10 );
		add_action( 'mv_grow_meta_tag_hook', [ 'Mediavine\Grow\Compatibility', 'set_yoast_meta_tag_hook' ], 10 );

		$this->settings_api = \Mediavine\Grow\Settings_API::get_instance();

		/**
		 * Hook in when WordPress is preparing to serve an API request.
		 *
		 * @param WP_REST_Server $wp_rest_server Server object.
		 */
		add_action( 'rest_api_init', function ( $wp_rest_server ) {
			$this->status_api_controller = new Mediavine\Grow\Status_API_Controller( $wp_rest_server, $this );
			$this->status_api_controller->register_routes( $wp_rest_server );
		} );

		$this->setup_integrations();
		$this->setup_free_tools();

		$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
		if ( $hubbub_activation_lite->is_lite_registered() ) {
			require_once( __DIR__ . '/networks/class-pro-networks.php' );
		}

		$this->asset_loader     = \Mediavine\Grow\Asset_Loader::get_instance();
		$this->frontend_data    = \Mediavine\Grow\Frontend_Data::get_instance();
		$this->networks         = \Mediavine\Grow\Networks::get_instance();
		$this->icons            = \Mediavine\Grow\Icons::get_instance();
		$this->subscribe_widget = \Mediavine\Grow\Subscribe_Widget::get_instance();

		// Meta tags
		add_action( apply_filters( 'mv_grow_meta_tag_hook', 'wp_head' ), [ 'Mediavine\Grow\Meta_Tags', 'build_and_output' ], 1 );

		// Activation & deativation hooks.
		register_activation_hook( mv_grow_get_activation_path(), 'dpsp_default_settings' );
		register_activation_hook( mv_grow_get_activation_path(), 'dpsp_set_cron_jobs' );
		register_activation_hook( mv_grow_get_activation_path(), 'dpsp_check_serial_key_status' );
		register_deactivation_hook( mv_grow_get_activation_path(), 'dpsp_stop_cron_jobs' );

		add_action( 'init', [ $this, 'init_translation' ] );
		add_action( 'admin_menu', [ $this, 'add_main_menu_page' ], 10 );
		add_action( 'admin_init', [ $this, 'add_hubbub_admin_menu_item_badge' ] );
		add_action( 'admin_menu', [ $this, 'remove_main_menu_page' ], 9999 );
		add_action( 'admin_enqueue_scripts', [ $this, 'init_admin_scripts' ], 100 );
		add_action( 'wp_enqueue_scripts', [ $this->asset_loader, 'register_front_end_scripts' ] );
		add_action( 'wp_enqueue_scripts', [ $this->asset_loader, 'enqueue_scripts' ] );
		add_action( 'wp_footer', [ $this->asset_loader, 'maybe_dequeue' ] );
		add_action( 'admin_init', [ $this, 'update_database' ] );
		add_filter( 'body_class', [ $this, 'add_body_class' ] );

		// Save This Verification settings update
		add_action( 'admin_init', 'dpsp_save_this_verify', 10 );

		// Exclude Hubbub from WP Rocket's Delay JavaScript
		add_filter( 'rocket_delay_js_exclusions', [ $this, 'add_hubbub_wp_rocket_delay_js_exclusion' ] );

		// Exclude Hubbub from Perfmatters Delay JavaScript
		add_filter( 'perfmatters_delay_js_exclusions', [ $this, 'add_hubbub_perfmatters_delay_js_exclusion' ] );

		// Set up Facebook Authorization
		//add_action( 'admin_init', 'dpsp_capture_authorize_facebook_access_token' );

		// Set up OAuth Authorization capture
		add_action( 'admin_init', 'dpsp_capture_oauth_access_token' );

		// Add a class to the admin body to tell plugin pages apart
		add_filter( 'admin_body_class', [ $this, 'admin_body_class' ] );

		add_filter( 'plugin_action_links_' . MV_GROW_PLUGIN_BASENAME, [ $this, 'add_plugin_action_links' ] );

		// Add inline css properties for critical styles to allow list
		add_filter( 'safe_style_css', [ \Mediavine\Grow\Critical_Styles::class, 'allowed_properties' ] );

		// Not sure why this is in a hook, so I'm leaving it for now, but this should be looked into.
		// TODO: It's also in the regular `init` hook, so not sure why it's called `load_resources_admin` - STA
		add_action( 'init', [ $this, 'load_resources_admin' ] );

		// Hook registration in functions files.
		dpsp_register_functions();
		dpsp_register_functions_admin();
		dpsp_register_functions_cron();
		dpsp_register_functions_post();

		if ( Share_Counts::are_counts_enabled() ) {
			// Only Register Count functions if counts are enabled
			$this->share_counts = Share_Counts::get_instance();
		}

		dpsp_register_functions_tools();

		// Hook registration in tools files.
		dpsp_register_floating_sidebar();
		dpsp_register_inline_content();

		// Hook registration in admin files.
		dpsp_register_admin_metaboxes();
		dpsp_register_admin_widgets();
		dpsp_register_admin_debugger();
		dpsp_register_admin_settings();
		dpsp_register_admin_dashboard();
		dpsp_register_admin_toolkit();

		if ( file_exists( DPSP_PLUGIN_DIR . '/inc/tools/share-pop-up/class-pop-up.php' ) && ! self::is_free() ) {
			$this->register_pro_features();
		} else {
			$this->register_free_features();
		}

		add_action( 'wp_head', [ $this, 'add_hubbub_meta_tag' ] );

		// This must happen after register_free_features() otherwise pro notices will show up on free
		$this->admin_notices = \Mediavine\Grow\Admin_Notices::get_instance();
	}

	/**
	 * Register Pro-only features.
	 */
	public function register_pro_features() {
		dpsp_register_functions_version_update();

		\Mediavine\Grow\Shortcodes::register_shortcodes();
		\Mediavine\Grow\Activation::get_instance();
		\Mediavine\Grow\Data_Sync::get_instance();

		$this->setup_pro_tools();

		// If Pro, check if license is valid before allowing plugin update
		add_filter('upgrader_pre_install', [ $this, 'pre_upgrade_check_license' ], 10, 2);

		// Register Gutenberg editor assets
		add_action( 'enqueue_block_editor_assets', [ $this, 'init_gutenberg_scripts' ] );

		// Register Block editor assets for IFrame / WP 6.7
		add_action( 'enqueue_block_assets', [ $this, 'init_block_assets' ] );

		dpsp_register_follow_widget();
		dpsp_register_import_export();

		dpsp_register_link_shortening();
		dpsp_register_link_shortening_bitly();
		dpsp_register_link_shortening_branch();

		dpsp_register_social_shares_recovery();
		dpsp_register_utm_tracking();
		dpsp_register_click_tweet();
		
		dpsp_register_images_pinterest();
		dpsp_register_pop_up();
		dpsp_register_sticky_bar();

		// /** Required for "Save This" Editor Block */
		add_action( 'init', 'dpsp_register_save_this_block' );

		/* Save This tool (which is different from the Block) */
		dpsp_register_email_save_this();
		add_filter( 'the_content', 'dpsp_output_front_end_email_save_this', 10 );
		add_filter( 'hubbub_save_this_the_content', 'dpsp_filter_the_content_email_save_this' );
		add_filter( 'query_vars', 'dpsp_add_query_vars_email_save_this' );

	}

	/**
	 * Register Free-only features.
	 */
	public function register_free_features() {
		//add_action( 'dpsp_enqueue_admin_scripts', 'dpsp_enqueue_admin_scripts_feedback' );
		//add_action( 'admin_footer', 'dpsp_output_feedback_form' );
		//add_action( 'wp_ajax_dpsp_ajax_send_feedback', 'dpsp_ajax_send_feedback' );
		add_action( 'dpsp_submenu_page_bottom', 'dpsp_add_submenu_page_sidebar' );
		add_action( 'admin_menu', 'dpsp_register_extensions_subpage', 102 );
		add_filter( 'mv_grow_is_free', '__return_true' );

		$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
		if ( $hubbub_activation_lite->is_lite_registered() ) {
			\Mediavine\Grow\Shortcodes::register_shortcodes();
			dpsp_register_follow_widget();
		}

		// Unlock Features Modal
		add_action( 'wp_ajax_dpsp_ajax_lite_save_and_activate_license', 'dpsp_ajax_lite_save_and_activate_license' );
    	add_action( 'wp_ajax_nopriv_dpsp_ajax_lite_save_and_activate_license', 'dpsp_ajax_lite_save_and_activate_license' );
	}

	/**
	 * Integrations bootstrap.
	 */
	public function setup_integrations() {
		$integration_container = \Mediavine\Grow\Integrations\Container::get_instance();
		$integration_container->add_integrations(
			[
				\Mediavine\Grow\Integrations\MV_Trellis::get_instance(),
				\Mediavine\Grow\Integrations\MV_Create::get_instance(),
			]
		);
	}

	/**
	 *  Register all tool classes with the main class
	 */
	public function setup_pro_tools() {
		$tool_container = \Mediavine\Grow\Tools\Toolkit::get_instance();
		$tools          = [
			new \Mediavine\Grow\Tools\Pop_Up(),
			new \Mediavine\Grow\Tools\Pinterest(),
			new \Mediavine\Grow\Tools\Floating_Sidebar(),
			new \Mediavine\Grow\Tools\Import_Export(),
			new \Mediavine\Grow\Tools\Follow_Widget(),
			new \Mediavine\Grow\Tools\Sticky_Bar(),
			new \Mediavine\Grow\Tools\Email_Save_This(),
		];
		$tool_container->add( $tools );
		foreach ( $tools as $tool ) {
			$this->settings_api->register_setting( $tool );
		}
		$this->tools = $tool_container;
	}

	/**
	 * Register all tool classes available with free version
	 */
	public function setup_free_tools() {
		$tool_container = \Mediavine\Grow\Tools\Toolkit::get_instance();
		$tools          = [
			new \Mediavine\Grow\Tools\Inline_Content(),
			new \Mediavine\Grow\Tools\Floating_Sidebar(),
		];

		$hubbub_activation_lite = new \Mediavine\Grow\ActivationLite;
		if ( $hubbub_activation_lite->is_lite_registered() ) {
			$tools[] = new \Mediavine\Grow\Tools\Follow_Widget();
		}

		$tool_container->add( $tools );
		foreach ( $tools as $tool ) {
			$this->settings_api->register_setting( $tool );
		}
		$this->tools = $tool_container;
	}

	/**
	 * Add Hubbub to WP Rocket's Delay JavaScript Exclusion List
	 */
	public function add_hubbub_wp_rocket_delay_js_exclusion( $excluded = array() ) {
		$excluded[] = 'social-pug';
		return $excluded;
	}

	/**
	 * Add Hubbub to Perfmatters Delay JavaScript Exclusion List
	 */
	public function add_hubbub_perfmatters_delay_js_exclusion( $excluded = array() ) {
		$excluded[] = 'social-pug';
		return $excluded;
	}

	public static function assets_url() {
		return plugin_dir_url( __FILE__ );
	}

	public function add_body_class( $body_classes ) {
		$active_tools = Mediavine\Grow\Settings::get_setting( 'dpsp_active_tools' );
		if ( in_array( 'share_sidebar', $active_tools, true ) && ! in_array( 'has_grow_sidebar', $body_classes, true ) ) {
			$body_classes[]   = 'has-grow-sidebar';
			$sidebar_settings = Mediavine\Grow\Settings::get_setting( 'dpsp_location_sidebar', 'not_set' );
			if ( isset( $sidebar_settings['display']['show_mobile'] ) ) {
				$body_classes[] = 'has-grow-sidebar-mobile';
			}
		}

		return $body_classes;
	}

	/**
	 * Loads the translations files if they exist
	 *
	 */
	public function init_translation() {

		load_plugin_textdomain( DPSP_TRANSLATION_TEXTDOMAIN, false, dirname( plugin_basename( __FILE__ ) ) . '/translations' );

	}

	/**
	 * Add the main menu page
	 *
	 */
	public function add_main_menu_page() {

		add_menu_page( __( 'Hubbub', 'social-pug' ),
						__( 'Hubbub', 'social-pug' ),
						'manage_options',
						'dpsp-social-pug',
						'',
						'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0nMS4wJyBlbmNvZGluZz0nVVRGLTgnPz48c3ZnIGlkPSdMYXllcl8xJyB4bWxucz0naHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmcnIHZpZXdCb3g9JzAgMCAzMiAzMic+PGRlZnM+PHN0eWxlPi5jbHMtMSwuY2xzLTJ7c3Ryb2tlLXdpZHRoOjBweDt9LmNscy0ye2ZpbGw6I2E0YWFhZjt9PC9zdHlsZT48L2RlZnM+PHBhdGggY2xhc3M9J2Nscy0yJyBkPSdtMzAuNzUsMjkuMjRsLTYuMDctNS45OS0xOC4zNS0uMDVMMS4yNSw4LjIyLDMwLjc1LDIuNzZ2MjEuNWgtMS40MlY0LjQ5djE3LjI3aDEuNDJ2Ny40OFonLz48cGF0aCBjbGFzcz0nY2xzLTEnIGQ9J20xMy4zLDE5LjI2di05LjExaDIuMDd2My40MmgzLjc2di0zLjQyaDIuMDh2OS4xMWgtMi4wOHYtMy44NWgtMy43NnYzLjg1aC0yLjA3WicvPjwvc3ZnPg=='
					);

	}

	/**
	 * Pro only: Adds a badge to the Hubbub menu if the user has notices that require attention
	 * 
	 */
	public function add_hubbub_admin_menu_item_badge() {
		if ( \Social_Pug::is_free() || wp_doing_ajax() ) return;
		
		$numberOfNotices = \Mediavine\Grow\Admin_Notices::dpsp_count_hubbub_admin_notices();
		if ( $numberOfNotices == 0 ) return;
		
		global $menu;
	
    	foreach( $menu as $key => $value ) :
        	if( 'dpsp-social-pug' == $value[2] ) :
            	$menu[$key][0] .= ' <span class="update-plugins"><span class="plugin-count count-' . $numberOfNotices . '">' . $numberOfNotices . '</span></span>';
			endif;
		endforeach;
	}

	/**
	 * Remove the main menu page as we will rely only on submenu pages
	 *
	 */
	public function remove_main_menu_page() {
		remove_submenu_page( 'dpsp-social-pug', 'dpsp-social-pug' );
	}

	/**
	 * Enqueue scripts and styles for the admin dashboard
	 *
	 * @param string $hook_suffix The current admin page. this is being run on
	 */
	public function init_admin_scripts( string $hook_suffix ) {

		if ( strpos( $hook_suffix, 'dpsp' ) !== false ) {
			wp_register_script( 'select2-js', DPSP_PLUGIN_DIR_URL . 'assets/libs/select2/select2.min.js', [ 'jquery' ] );
			wp_enqueue_script( 'select2-js' );
			wp_register_style( 'select2-css', DPSP_PLUGIN_DIR_URL . 'assets/libs/select2/select2.min.css' );
			wp_enqueue_style( 'select2-css' );

			wp_register_script(
				'dpsp-touch-punch-js',
				DPSP_PLUGIN_DIR_URL . 'assets/dist/jquery.ui.touch-punch.min.js',
				[
					'jquery-ui-sortable',
					'jquery',
				]
			);
			wp_enqueue_script( 'dpsp-touch-punch-js' );
			wp_enqueue_script( 'wp-color-picker' );
			wp_enqueue_style( 'wp-color-picker' );
		}

		wp_register_style( 'dpsp-dashboard-style-pro', DPSP_PLUGIN_DIR_URL . 'assets/dist/style-dashboard-pro.css', [], self::$VERSION );
		wp_enqueue_style( 'dpsp-dashboard-style-pro' );

		wp_register_script(
			'dpsp-dashboard-js-pro',
			DPSP_PLUGIN_DIR_URL . 'assets/dist/dashboard-pro.js',
			[
				'jquery-ui-sortable',
				'jquery',
			],
			self::$VERSION
		);
		wp_localize_script(
			'dpsp-dashboard-js-pro',
			'dpsp_ajax',
			array(
				'ajax_url' 					=> admin_url( 'admin-ajax.php' ),
				'hubbub_save_this_verify'   => wp_create_nonce('hubbub_save_this_verify'),
				'hubbub_dashboard_quick_edit' => wp_create_nonce('hubbub_dashboard_quick_edit'),
				'hubbub_dashboard_quick_edit_save' => wp_create_nonce('hubbub_dashboard_quick_edit_save'),
			)
		);
		wp_enqueue_script( 'dpsp-dashboard-js-pro' );

		wp_register_style( 'dpsp-frontend-style-pro', DPSP_PLUGIN_DIR_URL . 'assets/dist/style-frontend-pro.css', [], self::$VERSION );
		wp_enqueue_style( 'dpsp-frontend-style-pro' );
		
		wp_enqueue_media();
		
	}

	/**
	 * Enqueue scripts that are Gutenberg specific
	 *
	 */
	public function init_gutenberg_scripts() {

		$screen = get_current_screen();
		// Don't load on the widgets screen because these scripts conflict with the widget editor scripts
		if ( $screen && 'widgets' === $screen->id ) {
			return false;
		}
		$IS_DEVELOPMENT = apply_filters( 'mv_grow_dev_mode', false );
		$script_url     = $IS_DEVELOPMENT ? DPSP_PLUGIN_DIR_URL . 'assets/dist/dev-entry.js' : DPSP_PLUGIN_DIR_URL . 'assets/dist/block-editor.js';
		wp_enqueue_script(
			'dpsp-block-editor',
			$script_url,
			[
				'wp-components',
				'wp-blocks',
				'wp-compose',
				'wp-editor',
				'wp-element',
				'wp-i18n',
				'lodash',
			],
			self::$VERSION
		);

	}

	/**
	 * Enqueue scripts that are specific to the Block Editor's new IFrame (WP 6.7+)
	 */
	public function init_block_assets() {
		if ( is_admin() ) { // just in case
			wp_register_style( 'dpsp-dashboard-style-pro', DPSP_PLUGIN_DIR_URL . 'assets/dist/style-dashboard-pro.css', [], self::$VERSION );
			wp_enqueue_style( 'dpsp-dashboard-style-pro' );

			wp_register_style( 'dpsp-frontend-style-pro', DPSP_PLUGIN_DIR_URL . 'assets/dist/style-frontend-pro.css', [], self::$VERSION );
			wp_enqueue_style( 'dpsp-frontend-style-pro' );
		}
	}

	/**
	 * Fallback for setting defaults when updating the plugin,
	 * as register_activation_hook does not fire for automatic updates
	 *
	 */
	public function update_database() {

		$dpsp_db_version = Mediavine\Grow\Settings::get_setting( 'dpsp_version', '' );

		if ( self::$VERSION !== $dpsp_db_version ) {

			dpsp_default_settings();
			update_option( 'dpsp_version', self::$VERSION );

			// Add first time activation
			if ( '' === Mediavine\Grow\Settings::get_setting( 'dpsp_first_activation', '' ) ) {

				update_option( 'dpsp_first_activation', time() );

				/**
				 * Do extra actions on plugin's first ever activation
				 *
				 */
				do_action( 'dpsp_first_activation' );

			}

			// Update Sidebar button style from 1,2,3 to 1,5,8
			$dpsp_location_sidebar = dpsp_get_location_settings( 'sidebar' );

			if ( '2' === $dpsp_location_sidebar['button_style'] ) {
				$dpsp_location_sidebar['button_style'] = 5;
			}

			if ( '3' === $dpsp_location_sidebar['button_style'] ) {
				$dpsp_location_sidebar['button_style'] = 8;
			}

			update_option( 'dpsp_location_sidebar', $dpsp_location_sidebar );

			/**
			 * Do extra database updates on plugin update
			 *
			 * @param string $dpsp_db_version - the previous version of the plugin
			 * @param string DPSP_VERSION     - the new (current) version of the plugin
			 *
			 */
			do_action( 'dpsp_update_database', $dpsp_db_version, self::$VERSION );

		}

	}

	/**
	 * Add custom plugin CSS classes to the admin body classes
	 *
	 */
	public function admin_body_class( $classes ) {
		$page = filter_input( INPUT_GET, 'page' );
		if ( empty( $page ) ) {
			return $classes;
		}

		if ( false === strpos( $page, 'dpsp-' ) ) {
			return $classes;
		}

		if ( $this->is_free() ) : 
			return $classes . ' dpsp-pagestyles hubbub-lite';
		else :
			return $classes . ' dpsp-pagestyles hubbub-pro';
		endif;

	}

	/**
	 * Adds meta tag to HEAD containing Hubbub Pro/Lite version information
	 */
	public function add_hubbub_meta_tag() {
		echo '<meta name="hubbub-info" description="' . self::get_branding_name() . ' ' . DPSP_VERSION . '">';
	}

	/**
	 * Add extra action links in the plugins page
	 *
	 */
	public function add_plugin_action_links( $links ) {

		$all_links = array(
			'<a href="' . esc_url( get_admin_url( null, 'admin.php?page=dpsp-toolkit' ) ) . '">' . __( 'Settings', 'social-pug' ) . '</a>',
			'<a target="_blank" href="https://morehubbub.com/docs/" title="Read Support Docs">' . __( 'Docs', 'social-pug' ) . '</a>',
			'<a target="_blank" href="https://morehubbub.com/docs/upgrading-your-hubbub-pro-license/" title="Upgrade your license">' . __( 'Upgrade', 'social-pug' ) . '</a>'
		);

		if ( isset( $links['deactivate'] ) ) {
			$all_links[] = $links['deactivate'];
		}

		return $all_links;

	}

	/**
	 * 
	 * If license is empty, invalid, or expired, do not allow plugin update
	 * 
	 * @return boolean or WP_Error
	 * 
	 * See docs: https://developer.wordpress.org/reference/hooks/upgrader_pre_install/
	 */
	public function pre_upgrade_check_license( $return, $plugin ) {
		if ( is_wp_error( $return ) ) :
			return $return;
		endif;
		
		if ( isset( $plugin['plugin'] ) && $plugin['plugin'] == 'social-pug/index.php' ) :

			$license_key 		= get_option( 'mv_grow_license' );

			if ( empty( $license_key) ) return new WP_Error( 'hubbub-pro-license-empty', __('A valid Hubbub Pro license key is required to update the plugin. Please add your license key in <a href="' . admin_url( 'admin.php?page=dpsp-settings' ) . '">Hubbub > Settings</a> and try again.') );

			$license_status      = get_option( 'mv_grow_license_status' );
			$license_status_date = get_option( 'mv_grow_license_status_date' );

			if ( ! $license_status || empty( $license_status ) ) return new WP_Error( 'hubbub-pro-expired', __('The status of your Hubbub Pro license key is unknown. Please update your license key in <a href="' . admin_url( 'admin.php?page=dpsp-settings' ) . '">Hubbub > Settings</a> and try again.') );
			
			switch ( $license_status ) {
				case 'disabled':
					return new WP_Error( 'hubbub-pro-license-disabled', __('The license key for Hubbub Pro disabled. Please <a href="' . admin_url( 'admin.php?page=dpsp-settings' ) . '">reinstate your license</a> and try updating the plugin again.') );
					break;
				case 'expired':
					return new WP_Error( 'hubbub-pro-license-expired', __('The license key for Hubbub Pro has expired. Please <a href="' . admin_url( 'admin.php?page=dpsp-settings' ) . '">renew your license</a> and try updating the plugin again.') );
					break;
				case 'valid':
					return $return; // Allow the plugin update to proceed
					break;
				case 'invalid':
					return new WP_Error( 'hubbub-pro-license-invalid', __('The license key for Hubbub Pro is invalid. Please <a href="' . admin_url( 'admin.php?page=dpsp-settings' ) . '">update the license key</a> and try updating the plugin again.') );
					break;
				default: // Allow the plugin update to proceed
					return $return;
			}
		endif;

		return $return;
	}

	/**
	 * Include plugin files for the admin area
	 */
	public function load_resources_admin() {
		$this->setup_integrations();
	}

	/**
	 * Whether or not this instance of the plugin is free
	 * @return bool
	 */
	public static function is_free() {
		return (bool) apply_filters( 'mv_grow_is_free', false );
	}

	/**
	 * Are pro-level features available?
	 *
	 * @return bool
	 */
	public function is_pro() : bool {
		return ! self::is_free();
	}

	/**
	 * Are plus-level features available?
	 *
	 * @return bool
	 */
	public static function is_pro_plus() : bool {

		if ( self::is_free() ) {
			return false;
		}

		$hubbub_activation 	= new \Mediavine\Grow\Activation;
		$license_tier 		= $hubbub_activation->get_license_tier();

		if ( ! empty( $license_tier ) && $license_tier != 'pro' && $license_tier != 'priority' ) { 
			return true;
		}

		return false;
	}

	/**
	 * Are plus-level or above features available?
	 *
	 * @return bool
	 */
	public static function is_pro_plus_or_above() : bool {

		if ( self::is_free() ) {
			return false;
		}

		$hubbub_activation 	= new \Mediavine\Grow\Activation;
		$license_tier 		= $hubbub_activation->get_license_tier();

		if ( ! empty( $license_tier ) && $license_tier != 'pro' ) { 
			return true;
		}

		return false;
	}

	/**
	 * Are priority-level features available?
	 *
	 * @return bool
	 */
	public static function is_priority() : bool {

		if ( self::is_free() ) {
			return false;
		}

		$hubbub_activation 	= new \Mediavine\Grow\Activation;
		$license_tier 		= $hubbub_activation->get_license_tier();

		if ( ! empty( $license_tier ) && $license_tier != 'pro' && $license_tier != 'pro+' ) { 
			return true;
		}

		return false;
	}

	/**
	 * Return the branding name based on free vs pro
	 *
	 * @return string
	 */
	public static function get_branding_name() {
		if ( Social_Pug::is_free() ) {
			return __( 'Hubbub', 'social-pug' );
		}

		return __( 'Hubbub Pro', 'social-pug' );
	}
}
