#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';

$root = dirname( __DIR__ );
$payloadSource = (string) file_get_contents(
	$root . '/src/Presentation/Admin/SettingsPayloadBuilder.php'
);
$localizedSources = array(
	$root . '/src/js/admin/settings/app.js',
	$root . '/src/js/admin/settings/components.js',
	$root . '/src/js/admin/settings/settings-renderer.js',
	$root . '/src/js/admin/settings/template-editor-behavior.js',
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
	$translatedKeyPattern = "/'" . preg_quote( $key, '/' ) . "'\\s*=>\\s*__\\s*\\(/";
	if ( 1 !== preg_match( $translatedKeyPattern, $payloadSource ) ) {
		echo 'JavaScript localization contract failed: missing PHP translation for ' . esc_html( $key ) . ".\n";
		exit( 1 );
	}
}

$blockSource = (string) file_get_contents( $root . '/src/js/blocks/social-share/register.js' );
$socialLinksBlockSource = (string) file_get_contents(
	$root . '/src/js/blocks/social-links/register.js'
);
if (
	false === strpos( $blockSource, "const __ = i18n.__" ) ||
	false === strpos( $blockSource, "'html-social-share-buttons'" ) ||
	false === strpos( $socialLinksBlockSource, "const __ = i18n.__" ) ||
	false === strpos( $socialLinksBlockSource, "'html-social-share-buttons'" ) ||
	false === strpos( $payloadSource, 'interfaceStrings()' )
) {
	echo "JavaScript localization contract failed: block or settings translation boundary.\n";
	exit( 1 );
}

echo 'JavaScript localization contract passed: ' . count( $keys ) . " settings strings mapped through WordPress translations.\n";
