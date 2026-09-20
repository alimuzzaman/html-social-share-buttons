<?php
namespace Mediavine\Grow;

/**
 * Data syncing.
 */
class Data_Sync extends \Social_Pug {

	/** @var null  */
	private static $instance = null;

	/** @var bool  */
	private static $data_sync_run = false;

	/**
	 * Makes sure class is only instantiated once.
	 *
	 * @return object Instantiated class
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}
		return self::$instance;
	}

	/**
	 * Hooks to be run on class instantiation.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'mv_grow_plugin_activated', [ $this, 'sync_data' ], 10 );
		add_action( 'mv_grow_plugin_activated', [ $this, 'schedule_data_sync' ], 20 );
		add_action( 'mv_grow_plugin_updated', [ $this, 'sync_data' ], 20 );
		add_action( 'mv_grow_sync_data', [ $this, 'sync_data' ] );
		add_action( 'mv_grow_plugin_deactivated', [ $this, 'sync_data' ], 10 );
		add_action( 'mv_grow_plugin_deactivated', [ $this, 'remove_scheduled_data_sync' ], 20 );
	}

	/**
	 * Schedules data sync to run every 12 hours.
	 *
	 * @return void
	 */
	public function schedule_data_sync() {
		// Make sure who don't already have something scheduled
		if ( ! wp_next_scheduled( 'mv_grow_sync_data' ) ) {
			// Because we are also running data sync on every activation,
			// we wait 12 hours before running the first scheduled event
			// TURNED OFF SYNC: 2.19.0 wp_schedule_event( time() + HOUR_IN_SECONDS * 12, 'twicedaily', 'mv_grow_sync_data' );
		}
	}

	/**
	 * Removes Schedules data sync event.
	 *
	 * @return void
	 */
	public function remove_scheduled_data_sync() {
		wp_clear_scheduled_hook( 'mv_grow_sync_data' );
	}

	/**
	 * Determines whether MCP is enabled.
	 *
	 * @return boolean True if enabled
	 */
	public function is_mcp_enabled() {
		if (
			(
				class_exists( 'Mediavine\\MCP\\MV_Control_Panel' ) ||
				class_exists( 'MV_Control_Panel' ) || // Renamed to Mediavine\MCP\MV_Control_Panel in MCP v2.9.0.
				class_exists( 'MVCP' ) // Renamed to MV_Control_Panel in MCP v1.9.0.
			) && $this->bc_mcp_site_id() // Option renamed to mcp_site_id in MCP v2.9.0.
		) {
			return true;
		}

		return false;
	}

	/**
	 * Backwards-compatible way of determining the current MCP site ID.
	 *
	 * @return string|false MCP Site ID, if it exists as a valid WordPress option, or a boolean false on failure.
	 */
	private function bc_mcp_site_id() {
		$mcp_version = get_option('mcp_version');
		$option_key  = is_string( $mcp_version ) && version_compare( $mcp_version, '2.9.0') >= 0 ? 'mcp_site_id' : 'MVCP_site_id';
		return get_option( $option_key, false );
	}

	/**
	 * Get MCP site id.
	 *
	 * @return  string|null|false  Site id if exists and MCP active; false if doesn't exist; null if MCP not active
	 */
	public function get_mcp_site_id() {
		$mcp_site_id = null;
		if ( $this->is_mcp_enabled() ) {
			$mcp_site_id = $this->bc_mcp_site_id(); // Option renamed to mcp_site_id in MCP v2.9.0.
		}

		return $mcp_site_id;
	}

	/**
	 * Syncs site and plugin data with Mediavine Product Data Sync API.
	 *
	 * @return void
	 */
	public function sync_data() {
		return; // Added 2.19.0 to stop syncing data
		// Check if the data sync has already run
		if ( $this::$data_sync_run ) {
			return;
		}

		global $wp_version;

		// Find what action was run
		$action_run = null;
		if ( did_action( 'mv_grow_sync_data' ) ) {
			$action_run = 'scheduled_event';
		}
		if ( empty( $action_run ) && did_action( 'mv_grow_plugin_updated' ) ) {
			$action_run = 'plugin_updated';
		}
		if ( empty( $action_run ) && did_action( 'mv_grow_plugin_activated' ) ) {
			$action_run = 'plugin_activated';
		}
		if ( empty( $action_run ) && did_action( 'mv_grow_plugin_deactivated' ) ) {
			$action_run = 'plugin_deactivated';
		}

		// Get site domain and remove www if exists
		$site_domain = false;
		if ( isset( $_SERVER['HTTP_HOST'] ) ) { // Input var okay
			$site_domain = wp_unslash( $_SERVER['HTTP_HOST'] ); // Input var okay; sanitization okay.
			if ( strpos( $site_domain, 'www.' ) === 0 ) {
				$site_domain = substr( $site_domain, 4 );
			}
		}

		// Get admin email addresses
		$admins       = get_users( [ 'role__in' => [ 'administrator' ] ] );
		$admin_emails = wp_list_pluck( $admins, 'user_email' );

		// Build data array for sync
		$data_to_send = [
			'action_run'        => $action_run,
			'php_version'       => PHP_VERSION,
			'wp_version'        => $wp_version,
			// We quickly want to pull the db version, which is not easily pulled from $wpdb
			// phpcs:disable
			'mysql_version'     => mysqli_get_client_info(),
			// phpcs:enable
			'email'             => wp_get_current_user()->user_email,
			'grow_license'      => get_option( 'mv_grow_license', false ),
			'grow_version'      => $this::$VERSION,
			'plugin_name'       => 'Grow Social Pro by Mediavine',
			'plugin_slug'       => 'mediavine-grow',
			'plugin_dir'        => dirname( plugin_basename( mv_grow_get_activation_path() ) ),
			'site_url'          => site_url(),
			'site_domain'       => $site_domain,
			'site_admin_emails' => $admin_emails,
			'mcp_status'        => $this->is_mcp_enabled(),
			'mcp_site_id'       => $this->get_mcp_site_id(),
		];

		$data_sync = wp_remote_post(
			'https://product-data-sync.herokuapp.com/record',
			[
				'headers' => [
					'Content-Type' => 'application/json; charset=utf-8',
				],
				'body'    => wp_json_encode( $data_to_send ),
			]
		);

		// Prevent duplicate data syncs from running
		if ( ! empty( $data_sync ) ) {
			$this::$data_sync_run = true;
		}
	}
}
