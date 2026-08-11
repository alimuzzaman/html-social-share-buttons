#!/usr/bin/env php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\Definition\BuiltInNetworkProvider;

$expected = array(
	'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
	'x'         => 'https://x.com/intent/tweet?url=%%permalink%%&text=%%title%%',
	'linkedin'  => 'https://www.linkedin.com/sharing/share-offsite/?url=%%permalink%%',
	'pinterest' => 'https://www.pinterest.com/pin/create/button/?url=%%permalink%%&media=%%imageurl%%&description=%%title%%',
	'telegram'  => 'https://t.me/share/url?url=%%permalink%%&text=%%title%%',
	'bluesky'   => 'https://bsky.app/intent/compose?text=%%title%%%0A%%permalink%%',
	'mail'      => 'mailto:?subject=%%title%%&body=%%permalink%%',
);

$templates = array();
foreach ( ( new BuiltInNetworkProvider() )->createRegistry()->all() as $network ) {
	$templates[ $network->id() ] = $network->defaultShareTemplate();
}
if ( $templates !== $expected ) {
	echo "Share template contract failed.\n";
	exit( 1 );
}


$legacyApi = (string) file_get_contents( __DIR__ . '/../src/Compatibility/Legacy/Api/globals.php' );
if ( false === strpos( $legacyApi, 'function zm_sh_get_share_template' ) ||
	false === strpos( $legacyApi, 'LegacyHooks::shareTemplate' )
) {
	echo "Legacy share template bridge contract failed.\n";
	exit( 1 );
}

foreach ( glob( __DIR__ . '/../resources/iconsets/*.php' ) as $manifest ) {
	$definition = require $manifest;
	if (
		! isset( $definition['icons'] ) ||
		! isset( $definition['icons']['telegram'] ) ||
		! isset( $definition['icons']['bluesky'] )
	) {
		fwrite( STDERR, sprintf( "New platform IDs are missing from %s.\n", $manifest ) );
		exit( 1 );
	}
}

$icon_asset_directories = array(
	'iconset/default/square',
	'iconset/flat/square',
	'iconset/flat/circle',
	'iconset/long_shadow/square',
	'iconset/long_shadow/circle',
	'iconset/prajin/square',
	'iconset/prajin/circle',
);

foreach ( $icon_asset_directories as $directory ) {
	foreach ( array( 'telegram', 'bluesky' ) as $platform ) {
		if ( ! file_exists( __DIR__ . '/../' . $directory . '/' . $platform . '.svg' ) ) {
			exit( esc_html( sprintf( 'Missing %s asset in %s.\n', $platform, $directory ) ) );
			exit( 1 );
		}
	}
}

foreach ( array( 'telegram', 'bluesky' ) as $platform ) {
	$icon_style_contracts = array(
		"iconset/default/square/{$platform}.svg"     => 'stroke-dasharray="6 4"',
		"iconset/flat/square/{$platform}.svg"       => '<rect width="128" height="128"',
		"iconset/flat/circle/{$platform}.svg"       => '<circle cx="64" cy="64" r="64"',
		"iconset/long_shadow/square/{$platform}.svg" => 'transform="translate(-2 2)"',
		"iconset/long_shadow/circle/{$platform}.svg" => 'transform="translate(-2 2)"',
		"iconset/prajin/square/{$platform}.svg"      => 'transform="translate(1 1)"',
		"iconset/prajin/circle/{$platform}.svg"      => '<circle cx="64" cy="64" r="64"',
	);

	foreach ( $icon_style_contracts as $asset => $expected_markup ) {
		$contents = implode( '', file( __DIR__ . '/../' . $asset ) );
		if ( false === strpos( $contents, $expected_markup ) ) {
			exit( esc_html( sprintf( 'Set-specific icon treatment is missing from %s.\n', $asset ) ) );
			exit( 1 );
		}
	}

	foreach ( array( 'long_shadow/square', 'long_shadow/circle', 'prajin/square' ) as $iconset_shape ) {
		$asset    = "iconset/{$iconset_shape}/{$platform}.svg";
		$contents = implode( '', file( __DIR__ . '/../' . $asset ) );
		if ( false !== strpos( $contents, '<use href="#clip"' ) ) {
			exit( esc_html( sprintf( 'Visible background must not reference a clipPath in %s.\n', $asset ) ) );
			exit( 1 );
		}
	}

	$prajin_circle = implode( '', file( __DIR__ . '/../iconset/prajin/circle/' . $platform . '.svg' ) );
	if ( false !== strpos( $prajin_circle, 'id="shadow"' ) || false !== strpos( $prajin_circle, '<filter' ) ) {
		exit( esc_html( sprintf( 'Prajin circle %s asset must remain unshadowed.\n', $platform ) ) );
		exit( 1 );
	}
}

echo "Share template contract passed.\n";
