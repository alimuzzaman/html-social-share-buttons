#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';

$root = dirname( __DIR__ );
$payloadSource = (string) file_get_contents(
	$root . '/src/Compatibility/Legacy/Admin/LegacySettingsAssetEnqueuer.php'
);
$localizedSources = array(
	$root . '/src/js/compatibility/legacy/admin/app.js',
	$root . '/src/js/compatibility/legacy/admin/components.js',
	$root . '/src/js/compatibility/legacy/admin/settings-renderer.js',
	$root . '/src/js/compatibility/legacy/admin/template-editor-behavior.js',
	$root . '/src/js/admin/profile-links.js',
);
$keys = array();

foreach ( $localizedSources as $path ) {
	$source = (string) file_get_contents( $path );
	preg_match_all( "/text\\(\s*'([A-Za-z0-9]+)'/", $source, $matches );
	foreach ( $matches[1] as $key ) {
		$keys[ $key ] = true;
	}
}

foreach ( array_keys( $keys ) as $key ) {
	if ( false === strpos( $payloadSource, "'" . $key . "' => __(" ) ) {
		echo 'JavaScript localization contract failed: missing PHP translation for ' . esc_html( $key ) . ".\n";
		exit( 1 );
	}
}

$blockSource = (string) file_get_contents(
	$root . '/src/js/compatibility/legacy/block/register.js'
);
if (
	false === strpos( $blockSource, "const __ = i18n.__" ) ||
	false === strpos( $blockSource, "'html-social-share-buttons'" ) ||
	false === strpos( $payloadSource, 'interfaceStrings()' )
) {
	echo "JavaScript localization contract failed: block or settings translation boundary.\n";
	exit( 1 );
}

echo 'JavaScript localization contract passed: ' . count( $keys ) . " settings strings mapped through WordPress translations.\n";
