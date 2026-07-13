<?php

$plugin_file = dirname( __DIR__, 2 ) . '/html-social-share.php';
$tests_dir   = getenv( 'WP_TESTS_DIR' );

if ( ! is_string( $tests_dir ) || ! is_dir( $tests_dir . '/includes' ) ) {
	fwrite( STDERR, "WP_TESTS_DIR must point to the WordPress test library. Run this suite through Sandbox.\n" );
	exit( 1 );
}

if ( ! function_exists( 'tests_add_filter' ) ) {
	require_once $tests_dir . '/includes/functions.php';
}

tests_add_filter(
	'muplugins_loaded',
	static function () use ( $plugin_file ) {
		require $plugin_file;
	}
);

require $tests_dir . '/includes/bootstrap.php';
