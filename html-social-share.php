<?php
/*
Plugin Name: Html Social share buttons
Plugin URI: http://wordpress.org/plugins/html-social-share-buttons/
Description: Lightweight HTML and CSS social share buttons. Settings and block editing use WordPress JavaScript.
Author: Alimuzzaman Alim
Version: 2.2.6
Author URI: https://alim.dev
Text Domain: html-social-share-buttons
Domain Path: /languages
Requires at least: 5.3
Requires PHP: 7.0
License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hssb_autoload = __DIR__ . '/vendor/autoload.php';
if ( ! is_readable( $hssb_autoload ) ) {
	if ( ! function_exists( 'hssb_missing_autoloader_message' ) ) {
		function hssb_missing_autoloader_message() {
			return __( 'HTML Social Share Buttons is missing its required production dependencies. Install a packaged release, or run Composer before activating this source checkout.', 'html-social-share-buttons' );
		}
	}

	if ( ! function_exists( 'hssb_fail_missing_autoloader_activation' ) ) {
		function hssb_fail_missing_autoloader_activation() {
			wp_die(
				esc_html( hssb_missing_autoloader_message() ),
				esc_html__( 'HTML Social Share Buttons cannot be activated', 'html-social-share-buttons' ),
				array( 'back_link' => true )
			);
		}
	}

	if ( ! function_exists( 'hssb_missing_autoloader_admin_notice' ) ) {
		function hssb_missing_autoloader_admin_notice() {
			echo '<div class="notice notice-error"><p>' .
				esc_html( hssb_missing_autoloader_message() ) .
				'</p></div>';
		}
	}

	register_activation_hook( __FILE__, 'hssb_fail_missing_autoloader_activation' );
	add_action( 'admin_notices', 'hssb_missing_autoloader_admin_notice' );
	add_action( 'network_admin_notices', 'hssb_missing_autoloader_admin_notice' );
	unset( $hssb_autoload );

	return;
}
require_once $hssb_autoload;
unset( $hssb_autoload );

if ( class_exists( '\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime' ) ) {
	\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::boot( __DIR__ );
	require_once __DIR__ . '/src/Compatibility/Legacy/Global/bootstrap.php';
}
