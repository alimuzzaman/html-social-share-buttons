<?php
namespace Mediavine\Grow;

class Icons {

	/** @var Icons */
	private static $instance = null;

	/** @var Icon[] Array of Icon classes registered with the plugin */
	private $icons = [];

	/**
	 * Get instance of Class.
	 *
	 * @return Icons
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
			self::$instance->init();
		}

		return self::$instance;
	}

	/**
	 * Register generic icons on initialization.
	 */
	public function init() {
		$this->register_generic();
	}

	/**
	 * Register generic icons not associated with any networks.
	 */
	private function register_generic() {
		$utility = [

			'share'  => [
				'paths'  => [ 'M20.8 20.8q1.984 0 3.392 1.376t1.408 3.424q0 1.984-1.408 3.392t-3.392 1.408-3.392-1.408-1.408-3.392q0-0.192 0.032-0.448t0.032-0.384l-8.32-4.992q-1.344 1.024-2.944 1.024-1.984 0-3.392-1.408t-1.408-3.392 1.408-3.392 3.392-1.408q1.728 0 2.944 0.96l8.32-4.992q0-0.128-0.032-0.384t-0.032-0.384q0-1.984 1.408-3.392t3.392-1.408 3.392 1.376 1.408 3.424q0 1.984-1.408 3.392t-3.392 1.408q-1.664 0-2.88-1.024l-8.384 4.992q0.064 0.256 0.064 0.832 0 0.512-0.064 0.768l8.384 4.992q1.152-0.96 2.88-0.96z' ],
				'width'  => 26,
				'height' => 32,
			],
			'cancel' => [
				'paths'  => [ 'M23.168 23.616q0 0.704-0.48 1.216l-2.432 2.432q-0.512 0.48-1.216 0.48t-1.216-0.48l-5.248-5.28-5.248 5.28q-0.512 0.48-1.216 0.48t-1.216-0.48l-2.432-2.432q-0.512-0.512-0.512-1.216t0.512-1.216l5.248-5.248-5.248-5.248q-0.512-0.512-0.512-1.216t0.512-1.216l2.432-2.432q0.512-0.512 1.216-0.512t1.216 0.512l5.248 5.248 5.248-5.248q0.512-0.512 1.216-0.512t1.216 0.512l2.432 2.432q0.48 0.48 0.48 1.216t-0.48 1.216l-5.248 5.248 5.248 5.248q0.48 0.48 0.48 1.216z' ],
				'width'  => 25,
				'height' => 32,
			],
			'ok'     => [
				'paths'  => [ 'M31.4,3.4l-2.8-2.8C28.2,0.2,27.7,0,27.2,0c-0.5,0-1,0.2-1.4,0.6L12.2,14.2L6.2,8.1C5.8,7.7,5.3,7.5,4.8,7.5 s-1,0.2-1.4,0.6l-2.8,2.8C0.2,11.3,0,11.7,0,12.3c0,0.5,0.2,1,0.6,1.4L8,21.1l2.8,2.8c0.4,0.4,0.9,0.6,1.4,0.6c0.5,0,1-0.2,1.4-0.6 l2.8-2.8L31.4,6.2C31.8,5.8,32,5.3,32,4.8C32,4.2,31.8,3.8,31.4,3.4z' ],
				'width'  => 32,
				'height' => 25,
			],
			'pencil' => [
				'paths'  => [ 'M31.3,24.5c0.5-0.5,0.7-1.1,0.7-1.8s-0.2-1.3-0.7-1.8l-19-19c-0.5-0.5-1.2-0.9-2-1.3C9.4,0.2,8.6,0,7.8,0H0 v7.8c0,0.8,0.2,1.6,0.6,2.5s0.8,1.5,1.3,2l19,19c0.5,0.5,1.1,0.7,1.8,0.7c0.7,0,1.3-0.2,1.8-0.7L31.3,24.5z M4.8,10.3L7,8.2 l14.4,14.4l-2.1,2.1L4.8,10.3z M7.8,3.5c0.3,0,0.7,0.1,1.1,0.3C8.7,4,8.4,4.2,8,4.6C7.7,5,7.1,5.5,6.4,6.3S5,7.6,4.5,8.1L3.8,8.9 C3.6,8.4,3.5,8,3.5,7.8V5.2l1.7-1.7H7.8z M10.3,4.8l14.4,14.4l-2.1,2.1L8.2,7L10.3,4.8z M25.9,20.4l2.2,2.2l-5.5,5.5L20.4,26 L25.9,20.4z' ],
				'width'  => 32,
				'height' => 32,
			],
		];
		foreach ( $utility as $slug => $data ) {
			$this->register_icon( new Icon( $slug, $data ) );
		}
	}

	/**
	 * Register an Icon class instance with this class.
	 *
	 * @param Icon $icon
	 * @return bool True if the icon is successfully register, False if it fails because an Icon with that slug exists
	 */
	public function register_icon( Icon $icon ) {
		if ( array_key_exists( $icon->get_slug(), $this->icons ) ) {
			error_log( 'Hubbub Error: Attempting to add icon that already exists ' . $icon->get_slug() ); // @codingStandardsIgnoreLine

			return false;
		}
		$this->icons[ $icon->get_slug() ] = $icon;

		return true;
	}

	/**
	 * Get an Icon class instance with the given slug.
	 *
	 * @param string $slug Icon to get
	 * @return Icon|null
	 */
	public function get( $slug ) {
		return isset( $this->icons[ $slug ] ) ? $this->icons[ $slug ] : null;
	}

	/**
	 * Get an array of icons from an array of slugs
	 *
	 * @param array $slugs
	 * @return Icons[] Icons array corresponding to passed slugs
	 */
	public function get_many( $slugs ) {
		return array_map( [ $this, 'get' ], $slugs );
	}
}
