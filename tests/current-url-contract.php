#!/usr/bin/env php
<?php
define( 'ABSPATH', __DIR__ . '/../' );

function wp_unslash( $value ) {
	return $value;
}

function home_url( $path = '' ) {
	return 'http://localhost:8211' . $path;
}

function esc_url_raw( $url ) {
	return $url;
}

$_SERVER['REQUEST_URI'] = '/privacy-policy/?preview=true';

require_once __DIR__ . '/../function.php';

$actual = zm_sh_curentPageURL();
$expected = 'http://localhost:8211/privacy-policy/?preview=true';

if ( $actual !== $expected ) {
	echo "Current URL contract failed. Expected {$expected}; got {$actual}.\n";
	exit( 1 );
}

echo "Current URL contract passed.\n";
