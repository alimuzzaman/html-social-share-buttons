#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
	define( 'ABSPATH', __DIR__ . '/../' );
}

$source = implode( '', file( __DIR__ . '/../block-integration.php' ) );
$script = implode( '', file( __DIR__ . '/../blocks/social-share.js' ) );

foreach ( array( "register_block_type(\n\t\t'html-social-share/social-share'", 'render_callback', 'zm_sh_shortcode_cb', 'zm_sh_get_builder_iconset( isset( $attributes', "'iconset'      => array( 'type' => 'string', 'default' => 'inherit' )", "empty( \$attributes['icons'] )", 'iconsetAssets', 'zm_sh_get_builder_iconset_assets' ) as $needle ) {
	if ( false === strpos( $source, $needle ) ) {
		exit( esc_html( sprintf( 'Block integration contract failed: %s\n', $needle ) ) );
		exit( 1 );
	}
}

if ( false === strpos( $script, "blocks.registerBlockType( 'html-social-share/social-share'" ) || false === strpos( $script, 'selected.length === 1' ) || false === strpos( $script, 'Inherit from plugin settings' ) || false === strpos( $script, 'zm-sh-block-preview__icons' ) || false === strpos( $script, 'supportedTypes' ) ) {
	echo "Block integration contract failed: JavaScript registration.\n";
	exit( 1 );
}

echo "Block integration contract passed.\n";
