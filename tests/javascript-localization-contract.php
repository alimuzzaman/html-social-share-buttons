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
$sharedBlockSource = (string) file_get_contents(
	$root . '/src/js/blocks/shared/networks.js'
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

$pot = (string) file_get_contents(
	$root . '/languages/html-social-share-buttons.pot'
);
if ( is_file( $root . '/languages/zm-sh.pot' ) ) {
	echo "JavaScript localization contract failed: obsolete legacy POT is still distributed.\n";
	exit( 1 );
}
foreach ( array( 'settings_page.php:', 'form.php:', 'metabox.php:', 'share-templates.php:' ) as $deletedReference ) {
	if ( false !== strpos( $pot, $deletedReference ) ) {
		echo 'JavaScript localization contract failed: stale POT reference ' . $deletedReference . "\n";
		exit( 1 );
	}
}

$sharedBlockStrings = array( 'Square', 'Circle' );
preg_match_all( "/__\(\s*['\"]([^'\"]+)['\"]\s*,/", $sharedBlockSource, $sharedMatches );
$sharedBlockStrings = array_merge( $sharedBlockStrings, $sharedMatches[1] );
$entryBlockStrings = array();
foreach ( array( 'social-share' => $blockSource, 'social-links' => $socialLinksBlockSource ) as $entry => $source ) {
	preg_match_all( "/__\(\s*['\"]([^'\"]+)['\"]\s*,/", $source, $matches );
	$entryBlockStrings[ $entry ] = array_values(
		array_unique( array_merge( $sharedBlockStrings, $matches[1] ) )
	);
}
$blockStrings = array_values( array_unique( array_merge( $entryBlockStrings['social-share'], $entryBlockStrings['social-links'] ) ) );
foreach ( $blockStrings as $message ) {
	if ( false === strpos( $pot, 'msgid "' . addcslashes( $message, "\\\"" ) . '"' ) ) {
		echo 'JavaScript localization contract failed: POT is missing ' . esc_html( $message ) . ".\n";
		exit( 1 );
	}
}

foreach ( array( 'social-share', 'social-links' ) as $entry ) {
	$translationPath = $root . '/languages/html-social-share-buttons-fr_FR-' . md5( 'build/' . $entry . '.js' ) . '.json';
	$catalog = is_file( $translationPath )
		? json_decode( (string) file_get_contents( $translationPath ), true )
		: null;
	$messages = is_array( $catalog ) && isset( $catalog['locale_data']['messages'] )
		? $catalog['locale_data']['messages']
		: array();
	foreach ( $entryBlockStrings[ $entry ] as $message ) {
		if ( ! isset( $messages[ $message ][0] ) || '' === $messages[ $message ][0] ) {
			echo 'JavaScript localization contract failed: ' . $entry . ' French catalog is missing ' . esc_html( $message ) . ".\n";
			exit( 1 );
		}
	}
}

echo 'JavaScript localization contract passed: ' . count( $keys ) . " settings strings mapped through WordPress translations.\n";
