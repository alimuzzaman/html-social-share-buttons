<?php
namespace Mediavine\Grow\Tools;

use Mediavine\Grow\Settings;
use Mediavine\Grow\Has_Settings_API;

abstract class Tool implements Has_Settings_API {

	/**
	 * @var string $slug Unique identifier for this tool e.g. floating-sidebar
	 */
	public $slug;

	/**
	 * @var string $api_slug Unique identifier for this tool in API route contexts
	 */
	public $api_slug;

	/**
	 * @var bool $active If this tool is active or not
	 */
	public $active = false;


	/**
	 * @var string $name Displayed Name for this tool e.g. Floating Sidebar
	 */
	protected $name;

	/**
	 * @var string $type What type of tool this is
	 */
	protected $type;

	/**
	 * @var string $img Path to image for this tool
	 */
	protected $img;

	/**
	 * @var string $admin_page URL of WordPress admin page for this tool
	 */
	protected $admin_page;

	/**
	 * @var array $settings Settings for tool from wp_options table
	 */
	protected $settings = [];

	/**
	 * @var int $default_mobile_breakpoint Default pixel width for viewport to be considered mobile
	 */
	protected $default_mobile_breakpoint = 720;

	/**
	 * @var string $settings_slug WordPress setting for this tool
	 */
	protected $settings_slug = '';

       /**
        * @var array $settings_sanitization Array of the settings to sanitize and the setting value type to sanitize.
        */
       protected $settings_sanitization = array();

	/**
	 * Construct action to run child init method
	 */
	public function __construct() {
		$this->init();
		$this->load_settings();
		$this->sanitize_settings_setup();
	}

	/**
	 * Sets up the hook so that Hubbub options are sanitized before they are updated.
	 */
	private function sanitize_settings_setup() {
               if ( empty( $this->settings_sanitization ) || empty( $this->settings_slug ) ) {
			return;
		}
		add_filter( 'pre_update_option_' . $this->settings_slug, array( $this, 'sanitize_settings' ), 10, 1 );
	}

	/**
	 * Sanitizes the settings whenever they are updated.
	 *
	 * @param array  $input Array of settings to sanitize.
	 * @param string $recursive If anything but "false", sanitize nested arrays. Default is "false".
	 *
	 * @return array Sanitized input array.
	 */
	public function sanitize_settings( $input, $recursive = 'false' ) {
               if ( empty( $this->settings_sanitization ) ) {
			return $input;
		}

        $settings_to_sanitize = 'false' !== $recursive && ! empty( $this->settings_sanitization[ $recursive ] ) ? $this->settings_sanitization[ $recursive ] : $this->settings_sanitization;
		
		/**
		 * Sanitize each value in the input array by type.
		 * If a value is a key-value array, sanitize its contents as well.
		 * Each original key is preserved in the sanitized array.
		**/
		foreach ( $settings_to_sanitize as $key => $value_type ) {
			if ( isset( $input[ $key ] ) ) {
				switch ( $value_type ) {
					case 'text':
						$input[ $key ] = sanitize_text_field( $input[ $key ] );
						break;
					case 'color':
						$input[ $key ] = sanitize_hex_color( $input[ $key ] );
						break;
					case 'number':
						$safe_input    = intval( $input[ $key ] );
						$input[ $key ] = $safe_input ? $safe_input : '';
						break;
					case 'post_content':
						$input[ $key ] = wp_kses_post( $input[ $key ] );
						break;
					default:
						if ( is_array( $input[ $key ] ) ) {
							$input[ $key ] = $this->sanitize_settings( $input[ $key ], $key );
						}
						break;
				}
			}
		}

		return $input;
	}

	abstract public function init();

	/**
	 * Load settings into class.
	 */
	private function load_settings() {
		$location_settings = ! empty( $this->settings_slug ) ? Settings::get_setting( $this->settings_slug, [] ) : [];
		$this->settings    = apply_filters( 'dpsp_get_location_settings', $location_settings, $this->slug );
		$this->active      = ! empty( $location_settings['active'] );
		return true;
	}

	/**
	 * Get a property from this tool.
	 *
	 * @param string $key Property key to get value for.
	 * @return mixed Value of the property or false if it doesn't exist
	 */
	public function get_property( $key = '' ) {
		if ( empty( $key ) ) {
			return false;
		}
		if ( property_exists( $this, $key ) ) {
			return $this->{$key};
		}
		return false;
	}

	/**
	 * Get the name for this tool.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->get_property( 'name' );
	}

	/**
	 * Get the slug for this tool.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->get_property( 'slug' );
	}

	/**
	 * Get the path to the image for this tool.
	 *
	 * @return string
	 */
	public function get_img() {
		return $this->get_property( 'img' );
	}

	/**
	 * Get the type of this tool.
	 *
	 * @return string
	 */
	public function get_type() {
		return $this->get_property( 'type' );
	}

	/**
	 * Get the Admin Page URL for the tool.
	 *
	 * @return string
	 */
	public function get_admin_page() {
		return $this->get_property( 'admin_page' );
	}

	/**
	 * Get the WordPress settings slug.
	 *
	 * @return string
	 */
	public function get_settings_slug() {
		return $this->get_property( 'settings_slug' );
	}

	/**
	 * Whether or not the tool is active.
	 *
	 * @return bool
	 */
	public function is_active() {
		return $this->get_property( 'active' );
	}

	/**
	 * Get the settings for this tool.
	 *
	 * @return array
	 */
	public function get_settings() {
		return $this->settings;
	}

	/**
	 * Get the settings for this tool.
	 *
	 * @param array $settings Settings to update;
	 * @return boolean
	 */
	public function update_settings( $settings ) {
		$this->settings = $settings;
		if ( $settings['active'] ) {
			$this->active = true;
		} else {
			$this->active = false;
		}
		return update_option( $this->get_settings_slug(), $settings );
	}

	/**
	 * Get the slug for the api route
	 *
	 * @return string
	 */
	public function get_api_slug() {
		return $this->api_slug ? $this->api_slug : $this->slug;
	}
}
