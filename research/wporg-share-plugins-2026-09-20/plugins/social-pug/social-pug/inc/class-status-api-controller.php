<?php

namespace Mediavine\Grow;

use Mediavine\Grow\API\V1\Status_Schema;
use Social_Pug;
use WP_REST_Controller;
use WP_REST_Response;
use WP_REST_Server;

/**
 * Controller for the Grow Social status resource.
 *
 * @internal
 */
class Status_API_Controller extends WP_REST_Controller {

	/** @var Social_Pug */
	private $plugin_instance;

	/** @var WP_REST_Server */
	private $wp_rest_server;

	/**
	 * Setup the instance.
	 *
	 * @param WP_REST_Server $wp_rest_server Server object.
	 * @param Social_Pug     $addon_class Primary addon class.
	 */
	public function __construct( WP_REST_Server $wp_rest_server, Social_Pug $addon_class ) {
		$this->plugin_instance = $addon_class;
		$this->wp_rest_server  = $wp_rest_server;
	}

	/**
	 * Get the addon status.
	 *
	 * @return WP_REST_Response
	 */
	public function get_status() : WP_REST_Response {
		$data = [
			'is_pro'  => $this->plugin_instance->is_pro(),
			'version' => $this->plugin_instance->get_version(),
		];
		return new WP_REST_Response( $data, 200 );
	}

	/**
	 * Registers the routes for the objects of the controller.
	 */
	public function register_routes() : void {
		$status_schema = new Status_Schema();
		$this->wp_rest_server->register_route(
			Social_Pug::API_NAMESPACE,
			'/' . Social_Pug::API_NAMESPACE . '/status',
			[
				[
					'args'                => [],
					'callback'            => [ $this, 'get_status' ],
					'methods'             => WP_REST_Server::READABLE,
					'permission_callback' => '__return_true',
				],
				'schema' => [ $status_schema, 'json_schema' ],
			]
		);
	}
}
