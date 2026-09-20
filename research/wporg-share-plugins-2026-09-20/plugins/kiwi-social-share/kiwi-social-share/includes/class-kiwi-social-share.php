<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Kiwi_Social_Share {

	/**
	 * The single instance of Kiwi_Social_Share.
	 *
	 * @var    object
	 * @access   private
	 * @since    1.0.0
	 */
	private static $_instance = NULL;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = NULL;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Kiwi_Social_Share constructor.
	 *
	 * @param string $file
	 * @param string $version
	 */
	public function __construct( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token   = 'kiwi_social_sharing';

		// Load plugin environment variables
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';


		register_activation_hook( $this->file, array( $this, 'install' ) );

		new Kiwi_Social_Share_Click_To_Tweet();



		add_action( 'plugins_loaded', array( $this, 'load_frontend_kiwi' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load JS and CSS on the post/page edit pages only.
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_print_styles_post_only' ) );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_print_styles_post_only' ) );

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		$this->add_shortcodes();
		$this->add_metaboxes();
		$this->import_settings();

	} // End __construct ()


	/**
	 * Initiate shortcode
	 */
	public function add_shortcodes() {
		$advanced = Kiwi_Social_Share_Helper::get_setting_value( 'advanced_shortcode_manager', false, 'kiwi_advanced_settings' );
		new Kiwi_Social_Share_Shortcodes( $advanced );
	}

	/**
	 * Add metaboxes if the option is enabled
	 */
	public function add_metaboxes() {
		$custom_meta_boxes = Kiwi_Social_Share_Helper::get_setting_value( 'custom_meta_boxes', '' );
		if ( ! empty( $custom_meta_boxes ) ) {
			new Kiwi_Social_Share_Metaboxes();
		}
	}

	/**
	 * Render the frontend
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function load_frontend_kiwi() {
		$instance = Kiwi_Social_Share_Frontend::instance( __FILE__, $this->_version );
	}

	/**
	 * Start importer
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function import_settings() {
		$settings = get_option( 'kiwi_backup_option', false );
		if ( ! $settings ) {
			return;
		}

		$current  = get_option( 'kiwi_general_settings', false );
		$settings = get_option( 'kiwi_backup_option' );

		if ( ! $current ) {
			update_option( 'kiwi_general_settings', $settings );
		} else {
			$new = array_merge( $current, $settings );
			update_option( 'kiwi_general_settings', $new );
		}

		delete_option( 'kiwi_backup_option' );
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'kiwi-icomoon', esc_url( $this->assets_url ) . 'vendors/icomoon/style.css', array(), $this->_version );
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend' . $this->script_suffix . '.css', array( 'kiwi-icomoon' ), $this->_version );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-kiwi', esc_url( $this->assets_url ) . 'js/kiwi' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( $this->_token . '-kiwi' ), $this->_version );

	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		// Add the color picker css file
		if ( $hook == 'toplevel_page_kiwi_social_sharing_settings' || $hook == 'kiwi_page_kiwi-upgrade' ) {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_style( 'open-sans', '//fonts.googleapis.com/css?family=Open+Sans:300,400,700,800', array(), $this->_version );
			wp_enqueue_style( 'icomoon', esc_url( $this->assets_url ) . 'vendors/icomoon/style.css', array(), $this->_version );
			wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
			wp_enqueue_style( $this->_token . '-admin' );
		}else {
			wp_register_style('menu-link', esc_url( $this->assets_url ) . 'css/menu-link.css', array(), $this->_version );
			wp_enqueue_style( 'menu-link' );
		}

	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
		if ( $hook == 'toplevel_page_kiwi_social_sharing_settings' || $hook == 'plugins.php' ) {
			wp_register_script( $this->_token . '-kiwi', esc_url( $this->assets_url ) . 'js/kiwi' . $this->script_suffix . '.js', array(
				'jquery',
				'jquery-ui-sortable',
				'wp-color-picker'
			), $this->_version, true );
			wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin.js', array( $this->_token . '-kiwi' ), $this->_version, true );
			$kiwi_locale = array(
				'kiwi_step_one_title'    => esc_html__( 'Get your premium version now!', 'kiwi-social-share' ),
				'kiwi_step_two_title'    => esc_html__( 'Almost Done', 'kiwi-social-share' ),
				'kiwi_step_one_subtitle' => esc_html__( 'Take advantage of the large number of professional features anad take
 your business one step further!', 'kiwi-social-share' ),
				'kiwi_step_two_subtitle' => esc_html__( 'We need a few information to complete this!', 'kiwi-social-share' )
			);
			wp_localize_script( $this->_token . '-admin', 'kiwi_locale', $kiwi_locale );
			wp_enqueue_script( $this->_token . '-admin' );
		}
	} // End admin_enqueue_scripts ()

	/**
	 * Load JS and CSS on the post/page edit pages only.
	 */
	public function admin_print_styles_post_only() {

		// Enqueue metabox CSS file.
		wp_enqueue_style( $this->_token . '-post-only', esc_url( $this->assets_url ) . 'css/metabox' . $this->script_suffix . '.css', array(), $this->_version );

		// Enqueue the WP Media scripts.
		wp_enqueue_media();

		// Enqueue metabox JS file.
		wp_enqueue_script( $this->_token . '-post-only', esc_url( $this->assets_url ) . 'js/metabox' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );

		// Localize the scripts.
		wp_localize_script(
			$this->_token . '-post-only',
			'kiwiMediaSelects',
			array(
				'choose' => esc_html__( 'Choose Image', 'kiwi-social-share' ),
				'update' => esc_html__( 'Use Image', 'kiwi-social-share' ),
				'delete' => esc_html__( 'Delete Image', 'kiwi-social-share' ),
			)
		);

	}

	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'kiwi-social-share', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain() {
		$domain = 'kiwi-social-share';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Kiwi_Social_Share Instance
	 *
	 * Ensures only one instance of Kiwi_Social_Share is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see   Kiwi_Social_Share()
	 * @return Main Kiwi_Social_Share instance
	 */
	public static function instance( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'kiwi-social-share' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'kiwi-social-share' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install() {
		$this->_log_version_number();
		$options = get_option( 'kiwi_general_settings', array() );
		if ( empty( $options ) ) {
			Kiwi_Social_Share_Importer::get_instance();
		}
	} // End install ()

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number() {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
