<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_View_Shortcode_Bar
 */
final class Kiwi_Social_Share_View_Shortcode_Bar extends Kiwi_Social_Share_View implements Kiwi_Social_Share_Interface_Frontend {
	public function __construct( $networks = array(), $style = 'center', $icons = 'rect' ) {
		parent::__construct();

		if ( ! empty( $networks ) ) {
			$this->networks['shortcode_bar'] = $networks;
		}

		$this->container_class = array( $icons );

		switch ( $style ) {
			case 'fit':
				$this->container_class[] = 'kiwi-article-bar-fit';
				break;
			default:
				$this->container_class[] = 'kiwi-article-bar-center';
				break;
		}

		$kiwi = Kiwi_Social_Share::instance();
		wp_enqueue_style( $kiwi->_token . '-frontend' );
		wp_enqueue_script( $kiwi->_token . '-frontend' );
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
	 * Render the display bar
	 *
	 */
	public function generate_frontend_bar() {

		if (post_password_required()) {
			return;
		}

		$output   = '';
		$class    = implode( ' ', $this->container_class );
		$tracking = '';
		if ( $this->tracking ) {
			$tracking = ' data-tracking="true" data-tracking-container="shortcode-bar" ';
		}
		$output .= '<ul class="kiwi-article-bar ' . esc_attr( $class ) . '"' . $tracking . '>';
		if ( ! empty( $this->networks['shortcode_bar'] ) ) {
			foreach ( (array) $this->networks['shortcode_bar'] as $network ) {
				$class = $this->generate_class_name( $network );
				if ( ! class_exists( $class ) ) {
					continue;
				}
				$t = new $class();
				$t = $t->generate_output();
				if ( ! empty( $t ) ) {
					$output .= '<li>';
					/**
					 * Social buttons will implement this interface :
					 * Kiwi_Social_Share_Interface_Social::generate_output
					 */
					$output .= $t;
					$output .= '</li>';
				}
			}
		}
		$output .= '</ul>';

		return wp_kses_post( $output );
	}

}
