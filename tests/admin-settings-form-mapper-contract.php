#!/usr/bin/env php
<?php

$root = dirname( __DIR__ );
$autoload = $root . '/vendor/autoload.php';
if ( ! is_file( $autoload ) ) {
	echo "Composer autoloader is required.\n";
	exit( 1 );
}
require_once $autoload;

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
	false
);
$stored = $mapper->toStoredSubmission( $settings, $input );
$expected = array(
	'title' => 'Settings title',
	'iconset' => 'flat',
	'show_in' => array( 'show_left' => '1' ),
	'icons' => array( 'facebook' => '1', 'twitter' => '1' ),
	'use_port' => true,
);
if ( $expected !== $stored ) {
	echo "Settings form mapper stored-shape contract failed.\n";
	exit( 1 );
}

echo "Canonical admin settings form mapper contract passed.\n";
