<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Kiwi_Social_Share_Importer
 */
class Kiwi_Social_Share_Importer {
	/**
	 * @var
	 */
	private $old_settings;
	/**
	 * @var
	 */
	private $new_settings;

	/**
	 * @return mixed
	 */
	public function get_old_settings() {
		return $this->old_settings;
	}

	/**
	 * @param mixed $old_settings
	 */
	public function set_old_settings( $old_settings ) {
		$this->old_settings = $old_settings;
	}

	/**
	 * @return mixed
	 */
	public function get_new_settings() {
		return $this->new_settings;
	}

	/**
	 * @param mixed $new_settings
	 */
	public function set_new_settings( $new_settings ) {
		$this->new_settings = $new_settings;
	}

	/**
	 * Kiwi_Social_Share_Updater constructor.
	 */
	public function __construct() {
		$settings = $this->get_legacy_settings();
		if ( ! $settings ) {
			$arr = array(
				'networks_ordering'             => '',
				'networks_article_bar'          => array( 'facebook', 'twitter', 'pinterest', 'fintel', 'mix' ),
				'share_buttons'                 => 'on',
				'share_buttons_location'        => 'top',
				'button_shape'                  => 'rect',
				'share_buttons_posttypes'       => '',
				'floating_panel_posttypes'      => '',
				'share_buttons_posttypes_list'  => array(),
				'floating_panel_posttypes_list' => array(),
			);

			update_option( 'kiwi_general_settings', $arr );

			return;
		}
		$this->set_old_settings( $settings );

		$new_settings = $this->build_settings_array();
		if ( ! $new_settings ) {
			return;
		}

		$this->set_new_settings( $new_settings );
		$this->add_backup_options();

		update_option( 'kiwi_general_settings', $new_settings );

	}

	/**
	 * @since {VERSION}
	 */
	public function add_backup_options() {
		$sanitized = array();

		foreach ( $this->get_new_settings() as $k => $v ) {

			if ( is_string( $v ) ) {
				$sanitized[ $k ] = sanitize_text_field( $v );
			}

			if ( is_array( $v ) ) {
				foreach ( $v as $item ) {
					$sanitized[ $k ][] = sanitize_text_field( $item );
				}
			}
		}

		update_option( 'kiwi_backup_option', $sanitized );
	}

	/**
	 * @return bool|mixed
	 * @since {VERSION}
	 */
	public function get_legacy_settings() {
		$legacy = get_option( 'kiwi_settings' );

		if ( ! $legacy ) {
			return false;
		}

		return $legacy;
	}


	/**
	 * @return array|bool
	 * @since {VERSION}
	 */
	public function build_settings_array() {
		$arr = array(
			'networks_ordering'             => $this->check_sorting_option(),
			'networks_article_bar'          => $this->check_social_networks(),
			'share_buttons'                 => $this->check_share_buttons(),
			'share_buttons_location'        => $this->check_share_buttons_location(),
			'button_shape'                  => $this->check_button_shape(),
			'share_buttons_posttypes_list'  => $this->check_enable_posts(),
			'floating_panel_posttypes_list' => array(),
		);

		$arr = array_filter( $arr );

		if ( empty( $arr ) ) {
			return false;
		}

		return $arr;
	}

	/**
	 * @return array|bool
	 */
	public function check_enable_posts() {
		$settings = $this->get_old_settings();
		$arr      = array();
		if ( ! empty( $settings['kiwi-enable-on-posts'] ) ) {
			$arr[] = 'posts';
		}
		if ( ! empty( $settings['kiwi-enable-on-pages'] ) ) {
			$arr[] = 'pages';
		}
		if ( ! empty( $arr ) ) {
			return $arr;
		}

		return false;
	}


	/**
	 * @return bool|string
	 */
	public function check_button_shape() {
		$settings = $this->get_old_settings();
		if ( ! empty( $settings['kiwi-design-choose-layout'] ) ) {
			switch ( $settings['kiwi-design-choose-layout'] ) {
				case 'kiwi-leaf-style':
					$style = 'leaf';
					break;
				case 'kiwi-shift-style':
					$style = 'shift';
					break;
				case 'kiwi-pills-style':
					$style = 'pill';
					break;
				default:
					$style = 'rect';
					break;
			}

			return $style;
		}

		return false;
	}

	/**
	 * @return bool|string
	 */
	public function check_share_buttons_location() {
		$settings = $this->get_old_settings();
		if ( ! empty( $settings['kiwi-enable-share-position'] ) ) {

			switch ( $settings['kiwi-enable-share-position'] ) {
				case 'before-posts':
					$location = 'top';
					break;
				case 'after-posts':
					$location = 'bottom';
					break;
				case 'before_and_after_posts':
					$location = 'both';
					break;
				default:
					$location = 'bottom';
					break;
			}

			return $location;
		}

		return false;

	}

	/**
	 * @return bool|string
	 */
	public function check_share_buttons() {
		$settings = $this->get_old_settings();
		if ( ! empty( $settings['kiwi-enable-on-posts'] ) || ! empty( $settings['kiwi-enable-on-pages'] ) ) {
			return 'on';
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function check_sorting_option() {
		$settings = $this->get_old_settings();
		if ( ! empty( $settings['general-settings-order'] ) ) {
			$ordering = explode( ',', $settings['general-settings-order'] );
			$t        = array();
			foreach ( $ordering as $item ) {
				if ( $item === 'kiwi-enable-on-posts' || $item === 'kiwi-enable-on-pages' ) {
					continue;
				}
				$item = str_replace( 'kiwi-enable-', '', $item );
				$t[]  = $item;
			}

			$extra = array(
				'whatsapp',
				'telegram',
				'skype'
			);

			$t = array_merge( $t, $extra );

			return implode( ',', $t );
		}

		return '';
	}

	/**
	 * @return array
	 */
	public function check_social_networks() {
		$settings  = $this->get_old_settings();
		$return    = array();
		$available = array( 'facebook', 'twitter', 'pinterest', 'linkedin', 'reddit', 'email' );

		foreach ( $available as $social ) {
			if ( ! empty( $settings[ 'kiwi-enable-' . $social ] ) ) {
				$return[] = $social;
			}
		}

		return $return;
	}

	/**
	 * @return Kiwi_Social_Share_Importer
	 */
	public static function get_instance() {
		static $inst;
		if ( ! $inst ) {
			$inst = new Kiwi_Social_Share_Importer;
		}

		return $inst;
	}
}