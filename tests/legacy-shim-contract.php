#!/usr/bin/env php
<?php

$root = dirname( __DIR__ );
$mappings = array(
	'actions.php'               => 'src/Compatibility/Legacy/Global/actions.php',
	'block-integration.php'     => 'src/Compatibility/Legacy/Global/block.php',
	'elementor-integration.php' => 'src/Compatibility/Legacy/Global/elementor.php',
	'filters.php'               => 'src/Compatibility/Legacy/Global/filters.php',
	'form.php'                  => 'src/Compatibility/Legacy/Global/form.php',
	'function.php'              => 'src/Compatibility/Legacy/Global/functions.php',
	'iconsets.php'              => 'src/Compatibility/Legacy/Global/iconsets.php',
	'interfaces.php'            => 'src/Compatibility/Legacy/Global/interfaces.php',
	'metabox.php'               => 'src/Compatibility/Legacy/Global/metabox.php',
	'schemas.php'               => 'src/Compatibility/Legacy/Global/schemas.php',
	'settings_page.php'         => 'src/Compatibility/Legacy/Global/settings-page.php',
	'share-templates.php'       => 'src/Compatibility/Legacy/Global/share-templates.php',
	'shortcode.php'             => 'src/Compatibility/Legacy/Global/shortcode.php',
	'vc-integration.php'        => 'src/Compatibility/Legacy/Global/wpbakery.php',
	'widget.php'                => 'src/Compatibility/Legacy/Global/widget.php',
);
$failures = array();

foreach ( $mappings as $shim => $target ) {
	$shimPath = $root . '/' . $shim;
	$targetPath = $root . '/' . $target;
	if ( ! is_file( $shimPath ) ) {
		$failures[] = 'Missing historical shim: ' . $shim;
		continue;
	}
	if ( ! is_file( $targetPath ) ) {
		$failures[] = 'Missing compatibility target: ' . $target;
		continue;
	}

	$contents = (string) file_get_contents( $shimPath );
	if ( false === strpos( $contents, $target ) ) {
		$failures[] = $shim . ' does not delegate to ' . $target;
	}
	if (
		preg_match( '/\\b(?:class|interface|trait|function)\\s+[A-Za-z_]/', $contents ) ||
		false !== strpos( $contents, 'add_action(' ) ||
		false !== strpos( $contents, 'add_filter(' )
	) {
		$failures[] = $shim . ' contains implementation instead of a compatibility-only include.';
	}
}

if ( $failures ) {
	echo "Legacy shim contract failed:\n";
	foreach ( $failures as $failure ) {
		echo ' - ' . $failure . "\n";
	}
	exit( 1 );
}

printf( "Legacy shim contract passed: %d historical PHP paths delegate only to Compatibility/Legacy.\n", count( $mappings ) );
