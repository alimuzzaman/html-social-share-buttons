<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button_WhatsApp
 */
final class Kiwi_Social_Share_Social_Button_WhatsApp extends Kiwi_Social_Share_Social_Button implements Kiwi_Social_Share_Interface_Social {
	/**
	 * Kiwi_Social_Share_Social_Button_WhatsApp constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->platform = 'whatsapp';
		$this->url      = $this->build_url();
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
		if ( $this->short_url !== null ) {
			$url = $this->short_url;
		};

		/* end-pro-version */

		return 'https://wa.me/?text=' . esc_html__( 'Look at this', 'kiwi-social-share' ) . ': ' . urlencode( $desc ) . ' - ' . rawurlencode( $url );
		
	}

	/**
	 * @return string
	 */
	public function generate_output() {		
		return '<a data-class="popup" class="' . esc_attr( $this->generate_anchor_class() ) . '" data-network="' . esc_attr( $this->platform ) . '" href="' . esc_url( $this->url ) . '" target="_blank" rel="nofollow">' . $this->generate_anchor_icon() . ' ' . $this->build_shared_count() . '</a>';
	}
}