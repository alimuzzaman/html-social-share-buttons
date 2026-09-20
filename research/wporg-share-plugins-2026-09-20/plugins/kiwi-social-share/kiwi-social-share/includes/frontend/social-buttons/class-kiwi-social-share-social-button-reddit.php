<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button_Reddit
 */
final class Kiwi_Social_Share_Social_Button_Reddit extends Kiwi_Social_Share_Social_Button implements Kiwi_Social_Share_Interface_Social {
	/**
	 * Kiwi_Social_Share_Social_Button_Reddit constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->platform = 'reddit';
		$this->url      = $this->build_url();
		$this->api_url  = 'https://www.reddit.com/api/info.json?url=' . rawurlencode( $this->get_current_page_url( $this->post_id ) );
	}

	/**
	 * @return string
	 */
	public function build_url() {
		$url = $this->get_current_page_url( $this->post_id );
		/* start-pro-version */
		if ( $this->short_url !== NULL ) {
			$url = $this->short_url;
		};
		/* end-pro-version */
		return 'https://reddit.com/submit?url=' . rawurlencode( $url );
	}

	/**
	 * @return string
	 */
	public function generate_output() {
		return '<a data-class="popup" data-network="' . esc_attr( $this->platform ) . '" class="' . esc_attr( $this->generate_anchor_class() ) . '" href="' . esc_url( $this->url ) . '" target="_blank" rel="nofollow">' . $this->generate_anchor_icon() . ' ' . $this->build_shared_count() . '</a>';
	}

	/**
	 * @param $response
	 *
	 * @return bool|int
	 */
	public function parse_api_response( $response ) {
		$response = json_decode( $response['body'], true );

		if ( isset( $response ) && isset( $response['data'] ) && ! empty( $response['data']['children'] ) ) {
			return count( $response['data']['children'] );
		}

		return false;
	}
}