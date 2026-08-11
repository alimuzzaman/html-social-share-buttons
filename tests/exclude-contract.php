#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';
require_once __DIR__ . '/../vendor/autoload.php';

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
	define( 'ABSPATH', __DIR__ . '/../' );
}

use Alimuzzaman\HtmlSocialShareButtons\Application\Content\ExcludedContentPolicy;

$post = (object) array(
	'ID' => 42,
	'post_name' => 'sample-page',
	'post_title' => 'Sample Page',
);

$cases = array(
	'42' => true,
	' 7, 42, 99 ' => true,
	'sample-page' => true,
	'SAMPLE-PAGE' => true,
	'Sample Page' => true,
	'sample page' => true,
	'7,about,Contact' => false,
	'' => false,
);

foreach ( $cases as $value => $expected ) {
	$policy = new ExcludedContentPolicy();
	if ( $policy->matches( $post->ID, $post->post_name, $post->post_title, $value ) !== $expected ) {
		exit( esc_html( sprintf( 'Exclude contract failed for: %s\n', $value ) ) );
		exit( 1 );
	}
}

$identifiers = ( new ExcludedContentPolicy() )->identifiers( ' 42, about, Sample Page, ' );
if ( $identifiers !== array( '42', 'about', 'Sample Page' ) ) {
	echo "Exclude parsing contract failed.\n";
	exit( 1 );
}

echo "Exclude contract passed.\n";
