<?php

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
}

$plugin_file = dirname( __DIR__, 2 ) . '/html-social-share.php';
$autoload    = dirname( __DIR__, 2 ) . '/vendor/autoload.php';
$tests_dir   = getenv( 'WP_TESTS_DIR' );

if ( is_file( $autoload ) ) {
	require_once $autoload;
}

if ( ! isset( $_SERVER['REQUEST_URI'] ) ) {
	$_SERVER['REQUEST_URI'] = '/';
}

if ( ! is_string( $tests_dir ) || ! is_dir( $tests_dir . '/includes' ) ) {
	$message = "WP_TESTS_DIR must point to the WordPress test library. Run this suite through Sandbox.\n";
	if ( defined( 'STDERR' ) ) {
		fwrite( STDERR, $message );
	} else {
		echo $message; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- fixed CLI diagnostic.
	}
	exit( 1 );
}

if ( ! function_exists( 'tests_add_filter' ) ) {
	require_once $tests_dir . '/includes/functions.php';
}

tests_add_filter(
	'muplugins_loaded',
	static function () use ( $plugin_file ) {
		global $zm_sh_default_options;

		require $plugin_file;
	}
);

tests_add_filter(
	'plugins_loaded',
	static function () {
		add_action(
			'zm_sh_add_iconset',
			static function () {
				$GLOBALS['hssb_test_plugins_loaded_iconset_hook'] = true;
			}
		);
	}
);

require $tests_dir . '/includes/bootstrap.php';
