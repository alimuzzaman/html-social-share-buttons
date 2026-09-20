<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Kiwi_Social_Share_View_Floating_Bar extends Kiwi_Social_Share_View implements Kiwi_Social_Share_Interface_Frontend {
	/**
	 * Kiwi_Social_Share_Floating_Bar constructor.
	 */
	public function __construct() {
		parent::__construct();
		$enable = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel', false );

		if ( ! $enable ) {
			return;
		}

		if ( empty( $this->networks['floating_bar'] ) ) {
			return;
		}

		$this->position          = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel_location', 'left' );
		$this->container_class[] = $this->position;

		// render on the front
		add_action( 'wp_footer', array( $this, 'display_bar' ) );
	}

	/**
	 * @param string $content
	 *
	 * @return mixed|void
	 */
	public function display_bar( $content = '' ) {
		$this->generate_frontend_bar();
	}

	/**
	 *
	 */
	public function set_options() {
		$this->container_class[]  = Kiwi_Social_Share_Helper::get_setting_value( 'button_shape_floating', 'rect' );
		$floating_panel_posttypes = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel_posttypes', 'all' );
		if ( $floating_panel_posttypes === 'all' ) {
			$this->post_types = 'all';
		} else {
			$post_types       = Kiwi_Social_Share_Helper::get_setting_value( 'floating_panel_posttypes_list', array() );
			$this->post_types = array_merge( $this->post_types, $post_types );
		};
	}

	/**
	 * Render the display bar
	 *
	 * Allow theme customization for the floating bar using the following filters:
	 *  * kiwi_before_floating_bar
	 *  * kiwi_before_first_floating_item
	 *  * kiwi_after_last_floating_item
	 *  * kiwi_after_floating_bar
	 *  * kiwi_floating_bar_list_custom_class
	 */
	public function generate_frontend_bar() {

		if (post_password_required()) {
			return;
		}

		/* start-pro-version */
		if ( is_array( $this->post_types ) && ! in_array( get_post_type(), $this->post_types ) ) {
			return false;
		}
		/* end-pro-version */

		$kiwi = Kiwi_Social_Share::instance();
		wp_enqueue_style( $kiwi->_token . '-frontend' );
		wp_enqueue_script( $kiwi->_token . '-frontend' );

		$output = '';
		$class  = implode( ' ', $this->container_class );
		$class  .= ' ' . apply_filters( 'kiwi_floating_bar_list_custom_class', '' );
		$output .= wp_kses_post( apply_filters( 'kiwi_before_floating_bar', '' ) );

		$tracking = '';
		if ( $this->tracking ) {
			$tracking = ' data-tracking="true" data-tracking-container="floating-bar" ';
		}

		$output .= '<ul class="kiwi-floating-bar ' . esc_attr( $class ) . '"' . $tracking . '>';
		$output .= wp_kses_post( apply_filters( 'kiwi_before_first_floating_bar_item', '' ) );

		$network_labels = array(
			'facebook'    => esc_html__( 'Facebook', 'kiwi-social-share' ),
			'twitter'     => esc_html__( 'Twitter', 'kiwi-social-share' ),
			'email'       => esc_html__( 'Email', 'kiwi-social-share' ),
			'linkedin'    => esc_html__( 'LinkedIn', 'kiwi-social-share' ),
			'fintel'      => esc_html__( 'Fintel', 'kiwi-social-share' ),
			'mix'         => esc_html__( 'Mix', 'kiwi-social-share' ),
			'pinterest'   => esc_html__( 'Pinterest', 'kiwi-social-share' ),
			'reddit'      => esc_html__( 'Reddit', 'kiwi-social-share' ),
			'skype'       => esc_html__( 'Skype', 'kiwi-social-share' ),
			'telegram'    => esc_html__( 'Telegram', 'kiwi-social-share' ),
			'whatsapp'    => esc_html__( 'WhatsApp', 'kiwi-social-share' ),
		);

		foreach ( $this->networks['floating_bar'] as $network ) {
			$class = $this->generate_class_name( $network );
			if ( ! class_exists( $class ) ) {
				continue;
			}

			$t    = new $class();
			$html = $t->generate_output();
			if ( ! empty( $html ) ) {
				$output .= '<li>';
				/**
				 * Social buttons will implement this interface :
				 * Kiwi_Social_Share_Interface_Social::generate_output
				 */
				$output .= $html;
				$output .= '<a data-class="popup" class="kiwi-nw-' . esc_attr( $network ) . ' network-label" data-network="' . esc_attr( $network ) . '" href="' . esc_url( $t->url ) . '">' . $network_labels[ $network ] . '</a>';
				$output .= '</li>';
			}

		}

		$output .= wp_kses_post( apply_filters( 'kiwi_after_last_floating_bar_item', '' ) );
		$output .= '</ul>';

		$output .= wp_kses_post( apply_filters( 'kiwi_after_floating_bar', '' ) );

		echo wp_kses_post( $output );
	}


}