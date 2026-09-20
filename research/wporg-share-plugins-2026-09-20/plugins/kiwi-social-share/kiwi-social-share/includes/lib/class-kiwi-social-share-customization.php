<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Customization
 */
class Kiwi_Social_Share_Customization {
	/**
	 * @var array
	 */
	public $colors = array();

	/**
	 * Kiwi_Social_Share_Customization constructor.
	 */
	public function __construct() {
		$customization = $this->set_colors();
		if ( $customization['continue'] ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
		}
	}

	/**
	 * @return array
	 */
	public function set_colors() {
		$scheme = Kiwi_Social_Share_Helper::get_setting_value( 'styles_colors', 'original' );
		$colors = Kiwi_Social_Share_Helper::get_network_colors();

		$return = array(
			'scheme'   => 'original',
			'continue' => false,
		);

		switch ( $scheme ) {
			case 'custom':
				unset( $colors['monochrome'] );
				$this->_set_options_custom( $colors );
				$return = array(
					'scheme'   => 'custom',
					'continue' => true,
				);
				break;
			case 'monochrome':
				$this->_set_options_custom( $colors );
				$return = array(
					'scheme'   => 'monochrome',
					'continue' => true,
				);
				break;
			default:
				$this->_set_options_original();
				break;
		}

		return $return;
	}

	/**
	 * Reset the selector array
	 */
	public function _set_options_original() {
		$this->colors = array();
	}

	/**
	 * Set the social networks colors (for custom scheme)
	 *
	 * @param $options
	 */
	public function _set_options_custom( $options ) {
		$socials   = Kiwi_Social_Share_Helper::get_social_network_identities();
		$selectors = array();
		foreach ( $socials as $name => $prop ) {

			$selectors[ $name ] = array(
				'normal' => array(
					'background' => $options[ $name ]['background'],
					'color'      => $options[ $name ]['text'],
				),
				'hover'  => array(
					'background' => $options[ $name ]['hover_background'],
					'color'      => $options[ $name ]['hover_text'],
				),
			);
		};

		$this->colors = $selectors;
	}

	/**
	 * Set the social networks colors (for monochrome scheme)
	 *
	 * @param $options
	 */
	public function _set_options_monochrome( $options ) {
		$socials   = Kiwi_Social_Share_Helper::get_social_network_identities();
		$selectors = array();
		foreach ( $socials as $name => $prop ) {
			$selectors[ $name ] = array(
				'normal' => array(
					'background' => $options['background'],
					'color'      => $options['text'],
				),
				'hover'  => array(
					'background' => $options['hover_background'],
					'color'      => $options['hover_text'],
				),
			);
		}

		$this->colors = $selectors;
	}

	/**
	 * Create the CSS string for output
	 *
	 * @return mixed|string
	 */
	public function create_css() {
		/**
		 * Get an instance of the plugin so we can get the token
		 */
		$instance = Kiwi_Social_Share::instance();
		/**
		 * Don't recreate the CSS file every time we load the page,
		 */
		if ( false === ( $string = get_transient( $instance->_token . '_css_transient' ) ) ) {
			$string = '';
			/**
			 * In case the array is empty, we return an empty string
			 */
			if ( empty( $this->colors ) ) {
				return $string;
			}

			/**
			 * Build the CSS string for the normal and hover states
			 */
			foreach ( $this->colors as $selector => $prop ) {
				foreach ( $prop as $state => $values ) {
					if ( $state == 'normal' ) {
						$string .= '.kiwi-floating-bar a.kiwi-nw-' . esc_attr( $selector ) . ', .kiwi-article-bar a.kiwi-nw-' . esc_attr( $selector ) . '{';
						foreach ( $values as $k => $v ) {
							$string .= esc_attr( $k ) . ':' . esc_attr( $v ) . ';';
						}
						$string .= '}';
					} elseif ( $state == 'hover' ) {
						$string .= '.kiwi-floating-bar a.kiwi-nw-' . esc_attr( $selector ) . ':hover, .kiwi-article-bar a.kiwi-nw-' . esc_attr( $selector ) . ':hover{';
						foreach ( $values as $k => $v ) {
							$string .= esc_attr( $k ) . ':' . esc_attr( $v ) . ';';
						}
						$string .= '}';
					}
				}
			}

			/**
			 * Add a transient available 24 HOURS
			 */
			set_transient( $instance->_token . '_css_transient', $string, 24 * HOUR_IN_SECONDS );
		};

		return $string;
	}

	/**
	 * Enqueue the inline CSS string
	 */
	public function enqueue() {
		$instance = Kiwi_Social_Share::instance();
		$css      = $this->create_css();
		wp_add_inline_style( $instance->_token . '-frontend', $css );
	}
}