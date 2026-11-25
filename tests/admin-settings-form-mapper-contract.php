#!/usr/bin/env php
<?php

$root = dirname( __DIR__ );
$autoload = $root . '/vendor/autoload.php';
if ( ! is_file( $autoload ) ) {
	echo "Composer autoloader is required.\n";
	exit( 1 );
}
require_once $autoload;

if ( ! function_exists( 'sanitize_key' ) ) {
	function sanitize_key( $key ) {
		$key = strtolower( (string) $key );

		return preg_replace( '/[^a-z0-9_\-]/', '', $key );
	}
}

use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Placement;
use Alimuzzaman\HtmlSocialShareButtons\Domain\Settings\Settings;
use Alimuzzaman\HtmlSocialShareButtons\Infrastructure\WordPress\Settings\OptionSettingsRequestMapper;

$mapper = new OptionSettingsRequestMapper();
$input = array(
	'title' => ' <b>Settings title</b> ',
	'iconset' => 'flat',
	'show_in' => array( 'show_left' => '1' ),
	'icons' => array( 'facebook' => '1', 'twitter' => '1' ),
	'use_port' => '1',
	'show_for_current_user' => '0',
	'show_for_logged_in_user' => '1',
	'show_for_logged_out_user' => '0',
);
$canonical = $mapper->toCanonical( $input );
if ( true !== $canonical['placements'][ Placement::LEFT ] || '1' !== $canonical['networks']['x'] ) {
	echo "Settings form mapper canonical mapping failed.\n";
	exit( 1 );
}

$settings = new Settings(
	'Settings title',
	'flat',
	'square',
	array(
		Placement::LEFT => true,
		Placement::RIGHT => false,
		Placement::BEFORE_CONTENT => false,
		Placement::AFTER_CONTENT => false,
	),
	array(
		Placement::LEFT => 'square',
		Placement::RIGHT => 'square',
		Placement::BEFORE_CONTENT => 'square',
		Placement::AFTER_CONTENT => 'square',
	),
	array(),
	array(),
	'',
	false,
	false,
	true,
	false,
	array(),
	array(),
	false,
	true,
	false
);
$stored = $mapper->toStoredSubmission( $settings, $input );
$expected = array(
	'title' => 'Settings title',
	'iconset' => 'flat',
	'show_in' => array( 'show_left' => '1' ),
	'icons' => array( 'facebook' => '1', 'twitter' => '1' ),
	'use_port' => true,
	'show_for_current_user' => false,
	'show_for_logged_in_user' => true,
	'show_for_logged_out_user' => false,
);
if ( $expected !== $stored ) {
	echo "Settings form mapper stored-shape contract failed.\n";
	exit( 1 );
}

// Admin replacement must ignore even forged retired fields, preserving opaque values.
foreach ( array( array(), array( 'g_analytics' => false, 'use_port' => null ), array( 'g_analytics' => array( 'legacy' ), 'use_port' => 'false' ) ) as $original ) {
	$original['extension'] = array( 'opaque' => 'keep' );
	$replacement = $mapper->toStoredReplacement( $settings, $input + array( 'g_analytics' => '1' ), $original );
	foreach ( array( 'g_analytics', 'use_port', 'extension' ) as $key ) {
		if ( array_key_exists( $key, $original ) !== array_key_exists( $key, $replacement ) ||
			( array_key_exists( $key, $original ) && $original[ $key ] !== $replacement[ $key ] ) ) {
			echo "Retired admin setting preservation failed.\n";
			exit( 1 );
		}
	}
}

// Non-boolean iconset_type and malformed literal/icons inputs must sanitize cleanly.
$hardenedLegacy = $mapper->toStoredSubmission(
	$settings,
	array(
		'iconset_type' => 'circle',
		'show_left'    => '<script>alert(1)</script>',
		'icons'        => array(
			'facebook' => '1',
			'evil'     => '<svg>',
		),
	)
);
if (
	array(
		'iconset_type' => 'circle',
		'show_left'    => 'square',
		'icons'        => array( 'facebook' => '1' ),
	) !== $hardenedLegacy
) {
	echo "Legacy form mapper hardening failed.\n";
	exit( 1 );
}

$hardenedAdmin = $mapper->toStoredReplacement(
	$settings,
	array(
		'iconset'          => 'evil-pack',
		'iconset_type'     => 'diamond',
		'show_left'        => 'diamond',
		'show_in'          => array( 'show_left' => '1', 'forged_slot' => '1' ),
		'icons'            => array( 'facebook' => '1', 'evil' => '1' ),
		'forged_top_level' => '1',
	),
	array(
		'extension_state' => 'keep',
		'icons'           => array( 'extension-net' => 'custom' ),
		'show_in'         => array( 'extension-slot' => '1' ),
	)
);
$expectedAdmin = array(
	'extension_state' => 'keep',
	'icons'           => array( 'extension-net' => 'custom', 'facebook' => '1' ),
	'show_in'         => array( 'extension-slot' => '1', 'show_left' => '1' ),
	'iconset'         => 'flat',
	'iconset_type'    => 'square',
	'show_left'       => 'square',
);
if ( $expectedAdmin !== $hardenedAdmin ) {
	echo "Admin form mapper strict hardening failed.\n";
	exit( 1 );
}

echo "Canonical admin settings form mapper contract passed.\n";
