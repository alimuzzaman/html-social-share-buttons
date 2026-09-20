<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class Kiwi_Social_Share_View {
	/**
	 * @var array|mixed|string|void
	 */
	public $networks = array(
		'floating_bar' => array(),
		'article_bar'  => array(),
	);

	/**
	 * @var array
	 */
	public $container_class = array();

	/**
	 * @var string
	 */
	public $position = '';

	/**
	 * @var array
	 */
	public $post_types = array();

	/**
	 * @var bool
	 */
	public $tracking = false;

	/**
	 * Kiwi_Social_Share_View constructor.
	 */
	public function __construct() {
		$this->set_networks();
		$this->set_tracking();
		$this->set_options();
	}

	/**
	 * Get backend google analytics tracking settings
	 */
	private function set_tracking() {
		$tracking = Kiwi_Social_Share_Helper::get_setting_value( 'ga_tracking', '', 'kiwi_social_identities' );
		if ( ! empty( $tracking ) ) {
			$this->tracking = true;
		}
	}

	/**
	 * Get settings from backend and build the arrays
	 */
	private function set_networks() {
		$saved_order = Kiwi_Social_Share_Helper::get_setting_value( 'networks_ordering' );

		if ( empty( $saved_order ) ) {
			$this->networks['floating_bar'] = Kiwi_Social_Share_Helper::get_setting_value( 'networks_floating_bar', array() );
			$this->networks['article_bar']  = Kiwi_Social_Share_Helper::get_setting_value( 'networks_article_bar', array() );

			return;
		}

		$this->networks['floating_bar'] = array();
		$this->networks['article_bar']  = array();

		$floating_bar = Kiwi_Social_Share_Helper::get_setting_value( 'networks_floating_bar', array() );
		$article_bar  = Kiwi_Social_Share_Helper::get_setting_value( 'networks_article_bar', array() );

		$array_keys = explode( ',', $saved_order );
		foreach ( $array_keys as $k ) {
			if ( in_array( $k, $floating_bar ) ) {
				$this->networks['floating_bar'][] = $k;
			}

			if ( in_array( $k, $article_bar ) ) {
				$this->networks['article_bar'][] = $k;
			}
		}

	}

	/**
	 * Set options
	 */
	public function set_options() {
		$this->container_class[] = Kiwi_Social_Share_Helper::get_setting_value( 'button_shape', 'rect' );
		$share_buttons_posttypes = Kiwi_Social_Share_Helper::get_setting_value( 'share_buttons_posttypes', 'all' );
		if ( $share_buttons_posttypes === 'all' ) {
			$this->post_types = 'all';
		} else {
			$post_types       = Kiwi_Social_Share_Helper::get_setting_value( 'share_buttons_posttypes_list', array() );
			$this->post_types = array_merge( $this->post_types, $post_types );
		};
	}

	/**
	 * Generate a class name, so we can create the buttons
	 *
	 * @param $network
	 *
	 * @return string
	 */
	public function generate_class_name( $network ) {
		return 'Kiwi_Social_Share_Social_Button_' . $this->sanitize_network( $network );
	}

	/**
	 * Sanitize the network name so we can call it as needed
	 *
	 * @param string $str
	 *
	 * @return mixed
	 */
	private function sanitize_network( $str = '' ) {
		return str_replace( '-', '_', $str );
	}

}