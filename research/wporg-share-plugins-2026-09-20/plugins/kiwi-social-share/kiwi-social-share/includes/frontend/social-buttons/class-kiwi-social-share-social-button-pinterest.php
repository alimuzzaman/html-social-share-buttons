<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button_Pinterest
 */
final class Kiwi_Social_Share_Social_Button_Pinterest extends Kiwi_Social_Share_Social_Button implements Kiwi_Social_Share_Interface_Social {
	/**
	 * Kiwi_Social_Share_Social_Button_Pinterest constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->platform = 'pinterest';
		$this->url      = $this->build_url();
		$this->api_url  = 'http://api.pinterest.com/v1/urls/count.json?callback%20&url=' . rawurlencode( $this->get_current_page_url( $this->post_id ) );
	}

	/**
	 * @return string
	 */
	public function build_url() {
		$media        = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ) );
		$custom_image = get_post_meta( get_the_ID(), 'kiwi_social-media-pinterest-image', true );
		$custom_text  = get_post_meta( get_the_ID(), 'kiwi_social-media-pinterest-description', true );

		if ( ! empty( $custom_image ) ) {
			$media = $custom_image;
		}

		$desc = strip_tags( $this->get_excerpt_by_id( get_the_ID() ) );
		if ( 'fp' == $this->post_id ) {
			$desc = get_bloginfo( 'description' );
		}

		if ( ! empty( $custom_text ) ) {
			$desc = $custom_text;
		}

		$url = $this->get_current_page_url( $this->post_id );
		/* start-pro-version */
		if ( $this->short_url !== NULL ) {
			$url = $this->short_url;
		};

		/* end-pro-version */
		
		$str = 'https://pinterest.com/pin/create/button/?url=' . rawurlencode( $url ) . '&description=' . urlencode( $desc );

		if ( ! empty( $media ) ) {
			$str .= '&media=' . $media;
		}

		return $str;
	}

	/**
	 * @return string
	 */
	public function generate_output() {
		return '<a data-class="popup" data-network="' . esc_attr( $this->platform ) . '" class="' . esc_attr( $this->generate_anchor_class() ) . '" href="' . esc_url( $this->url ) . '" target="_blank" rel="nofollow">' . $this->generate_anchor_icon() . ' ' . $this->build_shared_count() . '</a>';
	}

	/**
	 * @param $response
	 */
	/**
	 * @param $response
	 *
	 * @return bool
	 */
	public function parse_api_response( $response ) {
		$response = preg_replace( '/^receiveCount\((.*)\)$/', '\\1', $response['body'] );
		$response = json_decode( $response, true );

		if ( empty( $response['count'] ) ) {
			return false;
		}

		return $response['count'];
	}

}