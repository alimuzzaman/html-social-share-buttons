#!/usr/bin/env php
<?php
define( 'ABSPATH', __DIR__ . '/../' );

$template_overrides = array();

function apply_filters( $hook, $value ) {
	global $template_overrides;

	if ( 'zm_sh_share_templates' === $hook && $template_overrides ) {
		return array_merge( $value, $template_overrides );
	}

	return $value;
}

require_once __DIR__ . '/../share-templates.php';

$expected = array(
	'facebook'  => 'https://www.facebook.com/sharer/sharer.php?u=%%permalink%%',
	'x'         => 'https://x.com/intent/tweet?url=%%permalink%%&text=%%title%%',
	'linkedin'  => 'https://www.linkedin.com/sharing/share-offsite/?url=%%permalink%%',
	'pinterest' => 'https://www.pinterest.com/pin/create/button/?url=%%permalink%%&media=%%imageurl%%&description=%%title%%',
	'mail'      => 'mailto:?subject=%%title%%&body=%%permalink%%',
);

if ( zm_sh_get_share_templates() !== $expected ) {
	echo "Share template contract failed.\n";
	exit( 1 );
}

$template_overrides['facebook'] = 'https://example.com/share?url=%%permalink%%';
if ( zm_sh_get_share_template( 'facebook' ) !== $template_overrides['facebook'] ) {
	echo "Share template filter contract failed.\n";
	exit( 1 );
}

$iconset_sources = glob( __DIR__ . '/../iconset/*/ssb.php' );
foreach ( $iconset_sources as $source ) {
	$contents = file_get_contents( $source );
	if ( false !== strpos( $contents, 'sharer.php' ) || false !== strpos( $contents, 'shareArticle' ) ) {
		echo "Legacy platform URL remains in {$source}.\n";
		exit( 1 );
	}
}

echo "Share template contract passed.\n";
