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

echo "Canonical admin settings form mapper contract passed.\n";
