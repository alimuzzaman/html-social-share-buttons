<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Kiwi_Social_Share_Autoloader
 */
class Kiwi_Social_Share_Autoloader {
	/**
	 * Kiwi_Social_Share_Autoloader constructor.
	 */
	public function __construct() {
		spl_autoload_register( array( $this, 'load' ) );
	}

	/**
	 * @param $class
	 */
	public function load( $class ) {
		/**
		 * All classes are prefixed with Kiwi_
		 */
		$parts = explode( '_', $class );
		$bind  = implode( '-', $parts );

		if ( $parts[0] == 'Kiwi' ) {
			/**
			 * Load interfaces
			 */
			if ( in_array( 'Interface', $parts ) && isset( $parts[2] ) ) {
				$path = KIWI_SOCIAL_SHARE_BASE . 'includes/interface/class-' . strtolower( $bind ) . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}

			/**
			 * Load Views
			 */
			if ( in_array( 'View', $parts ) && isset( $parts[2] ) ) {
				$path = KIWI_SOCIAL_SHARE_BASE . 'includes/frontend/social-bars/class-' . strtolower( $bind ) . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}

			/**
			 * Load Buttons
			 */
			if ( in_array( 'Button', $parts ) && isset( $parts[2] ) ) {
				$path = KIWI_SOCIAL_SHARE_BASE . 'includes/frontend/social-buttons/class-' . strtolower( $bind ) . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}

			/**
			 * Load Buttons
			 */
			if ( in_array( 'Helper', $parts ) && ! in_array( 'EDD', $parts ) && isset( $parts[2] ) ) {
				$path = KIWI_SOCIAL_SHARE_BASE . 'includes/lib/helpers/class-' . strtolower( $bind ) . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}


			/*
			 * Core library autoload.
			 */
			if ( isset( $parts[2] ) ) {
				$path = KIWI_SOCIAL_SHARE_BASE . 'includes/lib/class-' . strtolower( $bind ) . '.php';

				if ( file_exists( $path ) ) {
					require_once $path;
				}
			}
		}
	}
}

$autoloader = new Kiwi_Social_Share_Autoloader();
