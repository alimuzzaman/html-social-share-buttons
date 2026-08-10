#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
	define( 'ABSPATH', __DIR__ . '/../' );
}

$source = implode( '', file( __DIR__ . '/../src/Compatibility/Legacy/Integration/BlockAdapter.php' ) );
$script = implode( '', file( __DIR__ . '/../src/js/compatibility/legacy/block/register.js' ) );
$metadata = json_decode( (string) file_get_contents( __DIR__ . '/../block.json' ), true );

foreach ( array( "register_block_type( \$metadataPath", 'register_block_type_from_metadata', 'render_callback', 'renderCanonical', 'zm_sh_get_builder_iconset(', "empty( \$attributes['icons'] )", 'iconsetAssets', 'builderIconSetAssets', 'wp_set_script_translations' ) as $needle ) {
	if ( false === strpos( $source, $needle ) ) {
		exit( esc_html( sprintf( 'Block integration contract failed: %s\n', $needle ) ) );
		exit( 1 );
	}
}

if ( false !== strpos( $source, 'zm_sh_shortcode_cb' ) ) {
	echo "Block integration contract failed: shortcode callback remains a renderer dependency.\n";
	exit( 1 );
}

if ( ! is_array( $metadata ) || 'html-social-share/social-share' !== $metadata['name'] || 'zm-sh-social-share-block' !== $metadata['editorScript'] || empty( $metadata['attributes'] ) ) {
	echo "Block integration contract failed: canonical block metadata.\n";
	exit( 1 );
}

if ( false === strpos( $script, 'blocks.registerBlockType( metadata.name' ) || false === strpos( $script, "import metadata from '../../../../../block.json'" ) || false === strpos( $script, 'selected.length === 1' ) || false === strpos( $script, 'Inherit from plugin settings' ) || false === strpos( $script, 'zm-sh-block-preview__icons' ) || false === strpos( $script, 'supportedTypes' ) ) {
	echo "Block integration contract failed: JavaScript registration.\n";
	exit( 1 );
}

echo "Block integration contract passed.\n";
