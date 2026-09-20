<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button_LinkedIn
 */
final class Kiwi_Social_Share_Social_Button_LinkedIn extends Kiwi_Social_Share_Social_Button implements Kiwi_Social_Share_Interface_Social {
	/**
	 * Kiwi_Social_Share_Social_Button_LinkedIn constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->platform = 'linkedin';
		$this->url      = $this->build_url();
		$this->api_url  = 'http://www.linkedin.com/countserv/count/share?url=' . rawurlencode( $this->get_current_page_url( $this->post_id ) ) . '&format=json';
	}

	/**
	 * @return string
	 */
	public function build_url() {
		$desc = strip_tags( get_the_title() );
		if ( 'fp' == $this->post_id ) {
			$desc = get_bloginfo( 'description' );
		}

		$url = $this->get_current_page_url( $this->post_id );
		/* start-pro-version */
		if ( $this->short_url !== NULL ) {
			$url = $this->short_url;
		};

		/* end-pro-version */

		return 'https://linkedin.com/shareArticle?mini=true&url=' . rawurlencode( $url ) . '&title=' . urlencode( $desc );
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
	 * @return bool
	 */
	public function parse_api_response( $response ) {
		$response = json_decode( $response['body'], true );

		if ( empty( $response['count'] ) ) {
			return false;
		}

		return $response['count'];
	}
}