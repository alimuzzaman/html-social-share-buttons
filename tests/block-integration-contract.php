#!/usr/bin/env php
<?php
if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
	define( 'ABSPATH', __DIR__ . '/../' );
}

$source = file_get_contents( __DIR__ . '/../block-integration.php' );
$script = file_get_contents( __DIR__ . '/../blocks/social-share.js' );

foreach ( array( "register_block_type(\n\t\t'html-social-share/social-share'", 'render_callback', 'zm_sh_shortcode_cb', 'zm_sh_get_builder_iconset( isset( $attributes', "'iconset'      => array( 'type' => 'string', 'default' => 'inherit' )", "empty( \$attributes['icons'] )" ) as $needle ) {
	if ( false === strpos( $source, $needle ) ) {
		echo "Block integration contract failed: {$needle}\n";
		exit( 1 );
	}
}

if ( false === strpos( $script, "blocks.registerBlockType( 'html-social-share/social-share'" ) || false === strpos( $script, 'selected.length === 1' ) || false === strpos( $script, 'Inherit from plugin settings' ) ) {
	echo "Block integration contract failed: JavaScript registration.\n";
	exit( 1 );
}

echo "Block integration contract passed.\n";
