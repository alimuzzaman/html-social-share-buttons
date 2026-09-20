<?php
namespace Mediavine\Grow;

use InvalidArgumentException;
use RuntimeException;
use WP_Error;
use WP_HTTP_Requests_Response;

/**
 * Handler for license and activation requirements.
 */
class ActivationLite extends \Social_Pug {

	public const ITEM_ID = 15370151;

	public const MARKETPLACE_API_BASE_URL = 'https://morehubbub.com';

	public const LICENSE_STATUS_INACTIVE = 'inactive';

	public const LICENSE_STATUS_INVALID = 'invalid';

	public const LICENSE_STATUS_DISABLED = 'disabled';

	public const LICENSE_STATUS_EXPIRED = 'expired';

	public const LICENSE_STATUS_VALID = 'valid';

	public const OPTION_LICENSE_STATUS = 'mv_grow_license_status';

	public const OPTION_LICENSE_STATUS_DATE = 'mv_grow_license_status_date';

	public const OPTION_LICENSE_TIER = 'mv_grow_license_tier';

	/** @var null  */
	private static $instance = null;

	/**
	 * Make a request to the marketplace API.
	 *
	 * @param array $query Query string to pass as part of the request.
	 * @return array
	 * @throws RuntimeException If an error is encountered during the request.
	 */
	private function api_request( array $query = [] ) : array {
		$query_string = http_build_query( $query );
		$url          = self::MARKETPLACE_API_BASE_URL;
		if ( ! empty( $query_string ) ) {
			$url .= false === strstr( $url, '?' ) ? '?' : '&';
			$url .= $query_string;
		}

		$request_result = wp_remote_get( $url );
		if ( ! is_array( $request_result ) ) {
			if ( $request_result instanceof WP_Error ) {
				throw new RuntimeException( $request_result->get_error_message() );
			} else {
				throw new RuntimeException( 'An unknown error occurred while calling the Hubbub license API.' );
			}
		}

		/** @var WP_HTTP_Requests_Response $requests_response */
		$requests_response = $request_result['http_response'];
		$requests_response->get_response_object()->throw_for_status();

		$result = json_decode( $requests_response->get_data(), true );
		if ( ! is_array( $result ) ) {
			throw new RuntimeException( 'Unexpected format returned from the Hubbub license API.' );
		}

		return $result;
	}

	/**
	 * Check the current Hubbub license status. Likely via CRON
	 * Mostly a duplication of validate_license except it isn't reliant on
	 * a settings update. I've duplicated it to keep a separation of concerns. 
	 * Can consolidate in a future update.
	 */
	public function check_license() {
		$license_status 	= get_option ( self::OPTION_LICENSE_STATUS ) ?? null;
		$license_key 		= get_option( 'mv_grow_license' );

		if ( null === $license_key ) : // No license key found
			return;
		else :
			// Check this license key
			try {
				$response = $this->api_request( [
					'edd_action' => 'check_license',
					'item_id'    => self::ITEM_ID,
					'license'    => $license_key,
					'url'        => get_site_url(),
				] );
			} catch ( \Exception $e ) {
				add_settings_error(
					'mv_grow_license',
					'mv_grow_license_request_error',
					__( 'An error was encountered while verifying the license key.', 'mediavine' )
				);
				$this->set_license_status( null );
				return;
			}

			$previous_license_status = get_option( self::OPTION_LICENSE_STATUS );

			$edd_license_status = $response['license'] ?? null;

			switch ( $edd_license_status) {
				case 'disabled':
					$this->set_license_status( self::LICENSE_STATUS_DISABLED );
					return;
				case 'expired':
					$this->set_license_status( self::LICENSE_STATUS_EXPIRED );
					return;
				case 'inactive':
				case 'site_inactive':
					$this->set_license_status( self::LICENSE_STATUS_INACTIVE );
					try {
						$activation_response = $this->api_request( [
							'edd_action' => 'activate_license',
							'item_id'    => self::ITEM_ID,
							'license'    => $license_key,
							'url'        => get_site_url(),
						] );
					} catch ( \Exception $e ) {
						add_settings_error(
							'mv_grow_license',
							'mv_grow_license_request_error',
							__( 'An error occurred while trying to activate this website for the Hubbub license key you entered.', 'mediavine' )
						);
						return;
					}
	
					if ( isset( $activation_response['error'] ) ) {
						update_option('hubbub_temp_site_activated_message', $response['error'] );
					} else {
						$this->set_license_status( self::LICENSE_STATUS_VALID ); // Sets license to active
						$this->set_license_tier( 'lite' ); // Sets the license tier hash
						if ( $previous_license_status != 'valid' ) : // If the license was anything except valid
							set_transient( 'hubbub_license_activated_on_this_website', true, 60 ); // shows woohoo message
						endif;
					}
					
					return;
				case 'invalid':
					$this->set_license_status( self::LICENSE_STATUS_INVALID );
					return;
				case 'valid':
					$this->set_license_status( self::LICENSE_STATUS_VALID );
					$this->set_license_tier( 'lite' ); // Sets the license tier hash
					if ( $previous_license_status != 'valid' ) : // If the license was anything except valid
						set_transient( 'hubbub_license_activated_on_this_website', true, 60 ); // shows woohoo message
					endif;
					return;
			}

			$this->set_license_status( null );
			add_settings_error(
				'mv_grow_license',
				'mv_grow_license_invalid',
				__( 'The Hubbub Lite license key could not be validated.', 'mediavine' )
			);

		endif;
		
	}

	/**
	 *
	 *
	 * @return Activation|\Social_Pug|null
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self;
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	public function init() {
		add_action( 'update_option_dpsp_settings', [ $this, 'manage_grow_license' ], 10, 2 );
		add_action( 'wp_loaded', [ $this, 'plugin_updated_check' ] );

		register_activation_hook( mv_grow_get_activation_path(), [ $this, 'plugin_activation' ] );
		register_deactivation_hook( mv_grow_get_activation_path(), [ $this, 'plugin_deactivation' ] );
	}

	/**
	 * Runs hook at plugin activation.
	 *
	 * The update hook will run a bit later through its own hook.
	 *
	 * @return void
	 */
	public function plugin_activation() {
		do_action( 'mv_grow_plugin_activated' );
	}

	/**
	 * Runs hook at plugin update.
	 *
	 * This runs after all plugins are loaded so it can run after update. It also performs a
	 * check based on version number, just in case someone updates in a non-conventional way.
	 * After completing hooks, Grow version number is updated in the db.
	 *
	 * @return void
	 */
	public function plugin_updated_check() {
		// Only progress if version has changed
		if ( get_option( 'mv_grow_version' ) === self::$VERSION ) {
			return;
		}

		do_action( 'mv_grow_plugin_updated' );
		update_option( 'mv_grow_version', self::$VERSION );
	}

	/**
	 * Runs hook at plugin deactivation.
	 *
	 * @return void
	 */
	public function plugin_deactivation() {
		do_action( 'mv_grow_plugin_deactivated' );
	}

	/**
	 * Get the license tier
	 * 
	 * @return string|false
	 */
	public function get_license_tier() {

		$license_tier = get_option( self::OPTION_LICENSE_TIER );

		if ( empty( $license_tier ) ) return false;

		return $license_tier;
	}

	/**
	 * Set the license tier
	 *
	 * @param string|null $tier The tier to set. (Should be "lite"). Null to delete. (Never used?)
	 */
	private function set_license_tier( ?string $tier ) : void {
		if ( null === $tier ) {
			delete_option( self::OPTION_LICENSE_TIER );
			return;
		}

		update_option( self::OPTION_LICENSE_TIER, $tier );
	}

	public function is_lite_registered() {
		
		$license_key = get_option( 'mv_grow_license' );
		if ( empty( $license_key ) ) return false;

		$license_tier = self::get_license_tier();
		if ( ! $license_tier || $license_tier != 'lite' ) return false;

		$license_status = get_option( self::OPTION_LICENSE_STATUS );

		if ( 
			empty( $license_status ) ||
			( $license_status != 'valid' &&
			$license_status != 'site_inactive' &&
			$license_status != 'inactive' )
		) return false;

		

		return true;
	}

	/**
	 * Set the license status.
	 *
	 * @param string|null $license_status Updated license status. Must be one of the LICENSE_STATUS_* constants. Null to delete.
	 * @throws InvalidArgumentException If $license_status is invalid.
	 */
	private function set_license_status( ?string $license_status ) : void {
		if ( null === $license_status ) {
			delete_option( self::OPTION_LICENSE_STATUS );
			delete_option( self::OPTION_LICENSE_STATUS_DATE );
			return;
		}

		$valid_statuses = [
			self::LICENSE_STATUS_DISABLED => self::LICENSE_STATUS_DISABLED,
			self::LICENSE_STATUS_EXPIRED => self::LICENSE_STATUS_EXPIRED,
			self::LICENSE_STATUS_INACTIVE => self::LICENSE_STATUS_INACTIVE,
			self::LICENSE_STATUS_INVALID => self::LICENSE_STATUS_INVALID,
			self::LICENSE_STATUS_VALID   => self::LICENSE_STATUS_VALID,
		];
		if ( ! array_key_exists( $license_status, $valid_statuses ) ) {
			throw new InvalidArgumentException( 'Invalid $license_status: ' . $license_status );
		}

		update_option( self::OPTION_LICENSE_STATUS, $license_status );
		update_option( self::OPTION_LICENSE_STATUS_DATE, time() );
	}

	/**
	 * Validate a software license as part of a settings update.
	 *
	 * @param array $old_values Original settings form values.
	 * @param array $new_values Updated settings form values.
	 */
	public function validate_license( $old_values, $new_values ) {
		$grow_license = $new_values['mv_grow_license'] ?? null;

		if ( empty( $grow_license ) || null === $grow_license ) return;

		$previous_license_status = get_option( self::OPTION_LICENSE_STATUS );

		try {
			$response = $this->api_request( [
				'edd_action' 		=> 'check_license',
				'item_id'    		=> self::ITEM_ID,
				'license'    		=> $grow_license,
				'url'        		=> get_site_url(),
				'hubbub_version' 	=> MV_GROW_VERSION,
			] );
		} catch ( \Exception $e ) {
			add_settings_error(
				'mv_grow_license',
				'mv_grow_license_request_error',
				__( 'An error was encountered while verifying the license.', 'mediavine' )
			);
			$this->set_license_status( null );
			return;
		}

		$license_valid = $response['license'] ?? null;
		switch ( $license_valid ) {
			case 'disabled':
				$this->set_license_status( self::LICENSE_STATUS_DISABLED );
				return;
			case 'expired':
				$this->set_license_status( self::LICENSE_STATUS_EXPIRED );
				return;
			case 'inactive':
			case 'site_inactive':
				$this->set_license_status( self::LICENSE_STATUS_INACTIVE ); // Sets license to inactive
				try {
					$activation_response = $this->api_request( [
						'edd_action' 		=> 'activate_license',
						'item_id'    		=> self::ITEM_ID,
						'license'   		=> $grow_license,
						'url'        		=> get_site_url(),
						'hubbub_version' 	=> MV_GROW_VERSION,
					] );
				} catch ( \Exception $e ) {
					add_settings_error(
						'mv_grow_license',
						'mv_grow_license_request_error',
						__( 'An error occurred while trying to activate this website for the Hubbub license key you entered.', 'mediavine' )
					);
					return;
				}

				if ( ! isset( $activation_response['error'] ) ) {
					$this->set_license_status( self::LICENSE_STATUS_VALID ); // Sets license to valid
					$this->set_license_tier( 'lite' ); // Sets the license tier hash
					set_transient( 'hubbub_license_activated_on_this_website', true, 60 ); // Shows woohoo message
				}
				
				return;
			case 'invalid':
				$this->set_license_status( self::LICENSE_STATUS_INVALID );
				return;
			case 'valid':
				$this->set_license_status( self::LICENSE_STATUS_VALID );
				$this->set_license_tier( 'lite' ); // Sets the license tier hash
				if ( $previous_license_status != 'valid' ) : // If the license was anything except valid
					set_transient( 'hubbub_license_activated_on_this_website', true, 60 ); // shows woohoo message
				endif;
				return;
		}

		$this->set_license_status( null );
		add_settings_error(
			'mv_grow_license',
			'mv_grow_license_invalid',
			__( 'The Hubbub Lite license could not be validated.', 'mediavine' )
		);
	}

}
