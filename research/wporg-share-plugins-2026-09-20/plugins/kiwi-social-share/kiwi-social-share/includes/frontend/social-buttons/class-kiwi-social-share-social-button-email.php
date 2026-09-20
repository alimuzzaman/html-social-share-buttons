<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Social_Button_Email
 */
final class Kiwi_Social_Share_Social_Button_Email extends Kiwi_Social_Share_Social_Button implements Kiwi_Social_Share_Interface_Social {
	/**
	 * Kiwi_Social_Share_Social_Button_Email constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->platform = 'email';
		$this->url      = $this->build_url();
	}

	/**
	 * @return string
	 */
	public function build_url() {
		$url = $this->get_current_page_url( $this->post_id );
		/* start-pro-version */
		if ( $this->short_url !== NULL && $this->short_url != false) {
			$url = $this->short_url;
		};

		/* end-pro-version */

		return 'mailto:?subject=' . esc_attr( get_the_title() ) . '&body=' . rawurlencode( $url );
	}

	/**
	 * @return string
	 */
	public function generate_output() {
		return '<a class="' . esc_attr( $this->generate_anchor_class() ) . '" data-network="' . esc_attr( $this->platform ) . '" href="' . esc_url( $this->url ) . '"  rel="nofollow">' . $this->generate_anchor_icon( 'envelope' ) . ' ' . $this->build_shared_count() . '</a>';
	}
}