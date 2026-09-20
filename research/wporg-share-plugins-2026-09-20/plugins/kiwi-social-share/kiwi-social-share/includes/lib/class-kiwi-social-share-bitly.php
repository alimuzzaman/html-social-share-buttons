<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Class Kiwi_Social_Share_Bitly
 */
class Kiwi_Social_Share_Bitly {
	/**
	 * The single instance of Kiwi_Social_Share_Bitly
	 *
	 * @var    object
	 * @access   private
	 * @since    2.0.0
	 */
	private static $_instance = null;

	/**
	 * @var string
	 * @access private
	 * @since  2.0.0
	 */
	private $access_token = null;

	/**
	 * @var string
	 * @access private
	 * @since
	 */
	private $access_login = null;

	/**
	 * @var string
	 * @access private
	 * @since  2.0.0
	 */
	private $api_url = 'https://api-ssl.bitly.com/v3/shorten?';

	/**
	 * Kiwi_Social_Share_Bitly constructor.
	 *
	 * @param string $access_token
	 */
	public function __construct( $access_token, $access_login ) {
		$this->access_token = $access_token;
		$this->access_login = $access_login;
	}

	/**
	 * @param $url
	 *
	 * @return bool
	 */
	public function shorten_url( $url ) {
		$parsed = parse_url( $url );

		if ( ! empty( $parsed['host'] ) && $parsed['host'] === 'localhost' ) {
			return $url;
		}

		if ( empty( $parsed['scheme'] ) ) {
			$url = 'http://' . ltrim( $url, '/' );
		}

		if ( empty( $this->access_token ) ) {
			return $url;
		}

		$query = array(
			'login'   => $this->access_login,
			'apiKey'  => $this->access_token,
			'longUrl' => $url,
			'format'  => 'json',
		);

		$remote_url = $this->api_url . http_build_query( $query );

		$response = wp_remote_get( $remote_url );

		if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		if ( empty( $response['body'] ) ) {
			return false;
		}

		$response = json_decode( $response['body'] );

		if ( empty( $response->data ) ) {
			return false;
		}

		return $response->data->url;
	}

	/**
	 * Main Kiwi_Social_Share_Bitly Instance
	 *
	 * Ensures only one instance of Kiwi_Social_Share_Bitly is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see   Kiwi_Social_Share()
	 * @return Main Kiwi_Social_Share_Bitly instance
	 */
	public static function instance( $access_token = '', $access_login = '' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $access_token, $access_login );
		}

		return self::$_instance;
	} // End instance ()
}