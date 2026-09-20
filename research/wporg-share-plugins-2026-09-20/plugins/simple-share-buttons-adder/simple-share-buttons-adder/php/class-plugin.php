<?php
/**
 * Bootstraps the Simple Share Buttons Adder plugin.
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

/**
 * Main plugin bootstrap file.
 */
class Plugin extends Plugin_Base {

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
		parent::__construct();

		// Globals.
		$class_ssba       = new Simple_Share_Buttons_Adder( $this );
		$database         = new Database( $this, $class_ssba );
		$forms            = new Forms( $this );
		$forms_validation = new Forms_Validation();
		$widget_class     = new Widget();
		$admin_panel      = new Admin_Panel( $this, $class_ssba, $forms, $forms_validation, $widget_class );

		// Initiate classes.
		$classes = array(
			$class_ssba,
			$database,
			$admin_panel,
			$widget_class,
			$forms,
			new Styles( $this, $class_ssba ),
			new Admin_Bits( $this, $class_ssba, $database, $admin_panel ),
			new Buttons( $this, $class_ssba, $admin_panel ),
		);

		// Add classes doc hooks.
		foreach ( $classes as $instance ) {
			$this->add_doc_hooks( $instance );
		}
	}

	/**
	 * Register assets.
	 *
	 * @action wp_enqueue_scripts
	 */
	public function register_assets() {
		$propertyid = get_option( 'ssba_property_id' );

		wp_register_script(
			ASSET_PREFIX . "-ssba",
			"/wp-content/plugins/simple-share-buttons-adder/js/ssba.js",
			array( 'jquery' ),
			filemtime( DIR_PATH . "js/ssba.js" ),
			true
		);

		wp_register_style(
			ASSET_PREFIX . "-indie",
			'//fonts.googleapis.com/css?family=Indie+Flower',
			array(),
			SSBA_VERSION
		);

		wp_register_style(
			ASSET_PREFIX . "-reenie",
			'//fonts.googleapis.com/css?family=Reenie+Beanie',
			array(),
			SSBA_VERSION
		);

		wp_register_style(
			ASSET_PREFIX . "-ssba",
			"/wp-content/plugins/simple-share-buttons-adder/css/ssba.css",
			array(),
			filemtime( DIR_PATH . "css/ssba.css" )
		);

		wp_register_style(
			ASSET_PREFIX . "-font-awesome",
			'//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
			array(),
			SSBA_VERSION
		);

		if ( false === empty( $propertyid ) ) {
			wp_register_script(
				ASSET_PREFIX . "-mu",
				"//platform-api.sharethis.com/js/sharethis.js#property={$propertyid}&product=gdpr-compliance-tool-v2&source=simple-share-buttons-adder-wordpress",
				null,
				SSBA_VERSION,
				false
			);
		}
	}

	/**
	 * Register admin scripts/styles.
	 *
	 * @action admin_enqueue_scripts
	 */
	public function register_admin_assets() {
		wp_register_script(
			ASSET_PREFIX . "-admin",
			"/wp-content/plugins/simple-share-buttons-adder/js/admin.js",
			array( 'jquery', 'jquery-ui-sortable', 'wp-util' ),
			filemtime( DIR_PATH . "js/admin.js" ),
			false
		);
		wp_register_script(
			ASSET_PREFIX . "-bootstrap-js",
			"/wp-content/plugins/simple-share-buttons-adder/js/extras/bootstrap.js",
			array(),
			filemtime( DIR_PATH . "js/extras/bootstrap.js" ),
			false
		);
		wp_register_script(
			ASSET_PREFIX . "-colorpicker",
			"/wp-content/plugins/simple-share-buttons-adder/js/extras/colorpicker.js",
			array(),
			filemtime( DIR_PATH . "js/extras/colorpicker.js" ),
			false
		);
		wp_register_script(
			ASSET_PREFIX . "-switch",
			"/wp-content/plugins/simple-share-buttons-adder/js/extras/switch.js",
			array(),
			filemtime( DIR_PATH . "js/extras/switch.js" ),
			false
		);

		wp_register_style(
			ASSET_PREFIX . "-admin",
			"/wp-content/plugins/simple-share-buttons-adder/css/admin.css",
			false,
			time()
		);

		wp_register_style(
			ASSET_PREFIX . "-readable",
			"/wp-content/plugins/simple-share-buttons-adder/css/readable.css",
			array(),
			filemtime( DIR_PATH . "css/readable.css" )
		);

		wp_register_style(
			ASSET_PREFIX . "-colorpicker",
			"/wp-content/plugins/simple-share-buttons-adder/css/colorpicker.css",
			array(),
			filemtime( DIR_PATH . "css/colorpicker.css" )
		);

		wp_register_style(
			ASSET_PREFIX . "-switch",
			"/wp-content/plugins/simple-share-buttons-adder/css/switch.css",
			array(),
			filemtime( DIR_PATH . "css/switch.css" )
		);

		wp_register_style(
			ASSET_PREFIX . "-admin-theme",
			"/wp-content/plugins/simple-share-buttons-adder/css/admin-theme.css",
			ASSET_PREFIX . "-font-awesome",
			filemtime( DIR_PATH . "css/admin-theme.css" )
		);

		wp_register_style(
			ASSET_PREFIX . "-font-awesome",
			'//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
			array(),
			SSBA_VERSION
		);

		wp_register_style(
			ASSET_PREFIX . "-styles",
			"/wp-content/plugins/simple-share-buttons-adder/css/style.css",
			array(),
			filemtime( DIR_PATH . "css/style.css" )
		);

		wp_register_style(
			ASSET_PREFIX . "-indie",
			'//fonts.googleapis.com/css?family=Indie+Flower',
			array(),
			SSBA_VERSION
		);

		wp_register_style(
			ASSET_PREFIX . "-reenie",
			'//fonts.googleapis.com/css?family=Reenie+Beanie',
			array(),
			SSBA_VERSION
		);
	}
}
