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
	exit( 'WP_TESTS_DIR must point to the WordPress test library. Run this suite through Sandbox.\n' );
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

require $tests_dir . '/includes/bootstrap.php';
