<?php

namespace Mediavine\Grow;

use Social_Pug;

/**
 * Class Settings_API handles the API for changing Hubbub Settings
 */
class Settings_API {

	/** @var string Added to endpoints for REST API. */
	const API_NAMESPACE = Social_Pug::API_NAMESPACE;

	/** @var Settings_API singleton */
	public static $instance;

	/** @var array Settings registered through the API */
	private $registered_settings = [];

	/**
	 * Makes sure class is only instantiated once and runs init.
	 *
	 * @return self Instantiated class.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Register routes
	 */
	public function init() {
		// Add REST API endpoints.
		add_action( 'rest_api_init', [ $this, 'register_routes' ] );
	}

	/**
	 * Add REST API routes for feature flags to WordPress.
	 */
	public function register_routes() {
		// Prebuild the permissions callback.
		$admin_permission = function () {
			return current_user_can( 'manage_options' );
		};

		register_rest_route(
			self::API_NAMESPACE, '/settings/tool/(?P<slug>\S+)', [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'permission_callback' => $admin_permission,
					'args'                => \Mediavine\Grow\API\V1\SettingsArgs\get_tool_settings(),
					'callback'            => [ $this, 'get_tool_settings' ],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'permission_callback' => $admin_permission,
					'args'                => \Mediavine\Grow\API\V1\SettingsArgs\put_tool_settings(),
					'callback'            => [ $this, 'put_tool_settings' ],
				],
				'schema' => function () {
					return self::build_api_schema( 'Grow Social Setting Schema', 'get_tool_settings' );
				},
			]
		);

		register_rest_route(
			self::API_NAMESPACE, '/settings/general', [
				[
					'methods'             => \WP_REST_Server::READABLE,
					'permission_callback' => $admin_permission,
					'args'                => \Mediavine\Grow\API\V1\SettingsArgs\get_general_settings(),
					'callback'            => [ $this, 'get_general_settings' ],
				],
				[
					'methods'             => \WP_REST_Server::EDITABLE,
					'permission_callback' => $admin_permission,
					'args'                => \Mediavine\Grow\API\V1\SettingsArgs\put_general_settings(),
					'callback'            => [ $this, 'put_general_settings' ],
				],
				'schema' => function () {
					return self::build_api_schema( 'Grow Social Setting Schema', 'get_general_settings' );
				},
			]
		);
	}

	/**
	 * Put Settings for a given tool
	 *
	 * Mapped to the REST API.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function put_tool_settings( $request ) {
		if ( ! $request['slug'] ) {
			return new \WP_Error( 'missing slug', 'The Request is missing a setting API slug', [ 'status' => \WP_Http::BAD_REQUEST ] );
		}
		if ( ! array_key_exists( $request['slug'], $this->registered_settings ) ) {
			return new \WP_Error( 'no-setting', 'Setting Not Found', [ 'status' => \WP_Http::NOT_FOUND ] );
		}
		$setting = $this->registered_settings[ $request['slug'] ];

		return $this->put_settings( $setting, $request );
	}
		/**
		 * Get settings for a tool.
		 *
		 * Mapped to the REST API.
		 *
		 * @param \WP_REST_Request $request
		 * @return \WP_REST_Response|\WP_Error
		 */
		public function get_tool_settings( $request ) {
			if ( ! $request['slug'] ) {
				return new \WP_Error( 'missing slug', 'The Request is missing a setting API slug', [ 'status' => \WP_Http::BAD_REQUEST ] );
			}
			if ( ! array_key_exists( $request['slug'], $this->registered_settings ) ) {
				return new \WP_Error( 'no-setting', 'Setting Not Found', [ 'status' => \WP_Http::NOT_FOUND ] );
			}
			$setting = $this->registered_settings[ $request['slug'] ];

			return $this->get_settings( $setting, $request );
		}

	/**
	 * Put Settings for General Settings
	 *
	 * Mapped to the REST API.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function put_general_settings( $request ) {
		$slug = 'general';
		if ( ! array_key_exists( $slug, $this->registered_settings ) ) {
			return new \WP_Error( 'no-setting', 'Setting Not Found', [ 'status' => \WP_Http::NOT_FOUND ] );
		}
		$setting = $this->registered_settings[ $slug ];

		return $this->put_settings( $setting, $request );
	}
	/**
	 * Get settings for general settings
	 *
	 * Mapped to the REST API.
	 *
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_general_settings( $request ) {
		$slug = 'general';
		if ( ! array_key_exists( $slug, $this->registered_settings ) ) {
			return new \WP_Error( 'no-setting', 'Setting Not Found', [ 'status' => \WP_Http::NOT_FOUND ] );
		}
		$setting = $this->registered_settings[ $slug ];

		return $this->get_settings( $setting, $request );
	}

	/**
	 * Put Settings for a setting with API
	 *
	 * Mapped to the REST API.
	 *
	 * @param Has_Settings_API $setting
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function put_settings( $setting, $request ) {

		$is_options_sanitized = $request->sanitize_params();
		if ( is_wp_error( $is_options_sanitized ) ) {
			return $is_options_sanitized;
		}
		$values          = $request->get_json_params();
		$updated_options = $setting->update_settings( $this->key_network_array( $values ) );

		if ( ! $updated_options ) {
			return new \WP_Error( 'bad-update', 'There was an error while updating options' );
		}

		return new \WP_REST_Response( $values );
	}

	/**
	 * Get settings for a tool.
	 *
	 * Mapped to the REST API.
	 *
	 * @param Has_Settings_API $setting
	 * @param \WP_REST_Request $request
	 * @return \WP_REST_Response|\WP_Error
	 */
	public function get_settings( $setting, $request ) {
		$is_options_sanitized = $request->sanitize_params();
		if ( is_wp_error( $is_options_sanitized ) ) {
			return $is_options_sanitized;
		}
		return new \WP_REST_Response( $setting->get_settings() );
	}

	/**
	 * Build schema for an endpoint.
	 *
	 * @param string $title
	 * @param string $properties_callback
	 * @return array
	 */
	public static function build_api_schema( $title, $properties_callback ) {
		return [
			'$schema'    => 'http://json-schema.org/draft-04/schema#',
			'title'      => $title, // Identity of the resource.
			'type'       => 'object',
			'properties' => call_user_func( '\Mediavine\Grow\API\V1\SettingsSchema\\' . $properties_callback ),
		];
	}

	/**
	 * Turns the 'networks' property on a passed settings array from an keyed array to a numerically indexed array
	 * @param array $values Settings array
	 *
	 * @return array
	 */
	public function index_network_array( $values ) {
		if ( ! array_key_exists( 'networks', $values ) ) {
			return $values;
		}
		$indexed_networks = [];
		foreach ( $values['networks'] as $slug => $data ) {
			$indexed_networks[] = [
				'slug'  => $slug,
				'label' => $data['label'],
			];
		}
		$values['networks'] = $indexed_networks;
		return $values;
	}

	/**
	 * Turns the 'networks' property on a passed settings array from an indexed array to a keyed array based on the slug
	 * @param array $values Settings array
	 *
	 * @return array
	 */
	public function key_network_array( $values ) {
		if ( ! array_key_exists( 'networks', $values ) ) {
			return $values;
		}
		$keyed_networks = [];
		foreach ( $values['networks'] as $network ) {
			$keyed_networks[ $network['slug'] ] = [ 'label' => $network['label'] ];
		}
		$values['networks'] = $keyed_networks;
		return $values;
	}

	/**
	 * Register a setting so that the Settings API can handle it.=
	 * @param Has_Settings_API $setting Object that implements the Has_Settings_API interface
	 *
	 * @return bool Whether or not the setting was sucessfully registered
	 */
	public function register_setting( $setting ) {
		if ( ! $setting instanceof Has_Settings_API ) {
			return false;
		}
		$this->registered_settings[ $setting->get_api_slug() ] = $setting;
		return true;
	}

	/**
	 * Gets the registered settings
	 * @return array Settings registered with the class
	 */
	public function get_registered_settings() {
		return $this->registered_settings;
	}
}
