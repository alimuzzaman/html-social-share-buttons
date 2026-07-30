#!/usr/bin/env php
<?php

$root = dirname( __DIR__ );
$compatibilityFiles = array(
	'src/Compatibility/Legacy/Global/actions.php',
	'src/Compatibility/Legacy/Global/filters.php',
	'src/Compatibility/Legacy/Global/interfaces.php',
	'src/Compatibility/Legacy/Global/iconsets.php',
	'src/Compatibility/Legacy/Global/shortcode.php',
	'src/Compatibility/Legacy/Global/widget.php',
	'src/Compatibility/Legacy/Runtime/SocialShareAdapter.php',
);
$rootShims = array(
	'actions.php'    => 'src/Compatibility/Legacy/Global/actions.php',
	'filters.php'    => 'src/Compatibility/Legacy/Global/filters.php',
	'interfaces.php' => 'src/Compatibility/Legacy/Global/interfaces.php',
	'iconsets.php'   => 'src/Compatibility/Legacy/Global/iconsets.php',
	'shortcode.php'  => 'src/Compatibility/Legacy/Global/shortcode.php',
	'widget.php'     => 'src/Compatibility/Legacy/Global/widget.php',
);

$failures = array();
foreach ( $compatibilityFiles as $file ) {
	if ( ! is_file( $root . '/' . $file ) ) {
		$failures[] = 'Missing compatibility frontend module: ' . $file;
	}
}
foreach ( $rootShims as $file => $target ) {
	$contents = is_file( $root . '/' . $file )
		? (string) file_get_contents( $root . '/' . $file )
		: '';
	if (
		false === strpos( $contents, $target ) ||
		preg_match( '/\\b(?:class|interface|trait|function)\\s+[A-Za-z_]/', $contents )
	) {
		$failures[] = 'Historical root path is not a compatibility-only shim: ' . $file;
	}
}

if ( ! empty( $failures ) ) {
	echo implode( "\n", $failures ) . "\n";
	exit( 1 );
}

printf(
	"Frontend compatibility isolation passed: %d modules checked.\n",
	count( $compatibilityFiles )
);
