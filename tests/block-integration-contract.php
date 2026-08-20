#!/usr/bin/env php
<?php

require_once __DIR__ . '/cli-helpers.php';

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
	define( 'ABSPATH', __DIR__ . '/../' );
}

$source = implode(
	'',
	file( __DIR__ . '/../src/Presentation/Integration/Block/BlockRegistrar.php' )
);
$script = implode( '', file( __DIR__ . '/../src/js/blocks/social-share/register.js' ) );
$metadata = json_decode( (string) file_get_contents( __DIR__ . '/../block.json' ), true );
$socialLinksScript = implode( '', file( __DIR__ . '/../src/js/blocks/social-links/register.js' ) );
$socialLinksMetadata = json_decode(
	(string) file_get_contents( __DIR__ . '/../blocks/social-links/block.json' ),
	true
);

foreach ( array( 'register_block_type_from_metadata', 'render_callback', 'renderShareBlock', 'renderSocialLinksBlock', 'renderOutcome', 'builderIconSetAssets', 'iconsetAssets', 'hssbShareBlock', 'hssbSocialLinksBlock', 'blocks/social-links/block.json', 'wp_set_script_translations' ) as $needle ) {
	if ( false === strpos( $source, $needle ) ) {
		exit( esc_html( sprintf( 'Block integration contract failed: %s\n', $needle ) ) );
		exit( 1 );
	}
}

if ( false !== strpos( $source, 'zm_sh_shortcode_cb' ) || false !== strpos( $source, 'LegacyRuntime' ) ) {
	echo "Block integration contract failed: compatibility renderer dependency remains.\n";
	exit( 1 );
}

if ( ! is_array( $metadata ) || 'html-social-share/social-share' !== $metadata['name'] || 'zm-sh-social-share-block' !== $metadata['editorScript'] || empty( $metadata['attributes'] ) ) {
	echo "Block integration contract failed: canonical block metadata.\n";
	exit( 1 );
}

if ( 3 !== $metadata['apiVersion'] ) {
	echo "Block integration contract failed: maintained share block must use Block API v3.\n";
	exit( 1 );
}

if ( ! isset( $metadata['attributes']['profile_links_mode'] ) || 'inherit' !== $metadata['attributes']['profile_links_mode']['default'] ) {
	echo "Block integration contract failed: share block profile-link mode metadata.\n";
	exit( 1 );
}

if ( false === strpos( $script, 'blocks.registerBlockType( metadata.name' ) || false === strpos( $script, "import metadata from '../../../../block.json'" ) || false === strpos( $script, 'selected.length === 1' ) || false === strpos( $script, 'Inherit from plugin settings' ) || false === strpos( $script, 'hssb-block-preview__icons' ) || false === strpos( $script, 'supportedTypes' ) || false === strpos( $script, "editorData( 'hssbShareBlock' )" ) || false === strpos( $script, 'blockEditor.useBlockProps' ) || false === strpos( $script, 'legacyIconsets' ) ) {
	echo "Block integration contract failed: JavaScript registration.\n";
	exit( 1 );
}

foreach ( array( 'supportedBlockApiVersion', "version_compare( (string) \$wordpressVersion, '6.3', '>=' )", "'apiVersion'" ) as $needle ) {
	if ( false === strpos( $source, $needle ) ) {
		echo 'Block integration contract failed: supported-version API fallback (' . $needle . ").\n";
		exit( 1 );
	}
}

if ( false === strpos( $script, 'profile_links_mode' ) || false === strpos( $script, 'Hide profile links in this block' ) ) {
	echo "Block integration contract failed: share block profile-link editor control.\n";
	exit( 1 );
}

foreach ( array( 'Profile links after share buttons', 'selectedProfiles', 'zmshbt-profile-separator', 'hssb-block-preview__profile-icon' ) as $needle ) {
	if ( false === strpos( $script, $needle ) ) {
		echo 'Block integration contract failed: mixed-link preview (' . $needle . ").\n";
		exit( 1 );
	}
}

if (
	! is_array( $socialLinksMetadata ) ||
	'html-social-share/social-links' !== $socialLinksMetadata['name'] ||
	'zm-sh-social-links-block' !== $socialLinksMetadata['editorScript'] ||
	3 !== $socialLinksMetadata['apiVersion'] ||
	array( 'title', 'iconset', 'iconset_type', 'profile_links_mode', 'profile_links' ) !== array_keys( $socialLinksMetadata['attributes'] )
) {
	echo "Block integration contract failed: social links block metadata.\n";
	exit( 1 );
}

foreach (
	array(
		'blocks.registerBlockType( metadata.name',
		"import metadata from '../../../../blocks/social-links/block.json'",
		"editorData( 'hssbSocialLinksBlock' )",
		'profile_links_mode',
		'profileLinksForPreview',
		'hssb-social-links-block-preview__icons',
		'blockEditor.useBlockProps',
		'legacyIconsets',
		'save() {',
		'return null;',
	) as $needle
) {
	if ( false === strpos( $socialLinksScript, $needle ) ) {
		echo 'Block integration contract failed: social links editor implementation (' . $needle . ").\n";
		exit( 1 );
	}
}

if ( false !== strpos( $socialLinksScript, 'zm_sh_shortcode_cb' ) ) {
	echo "Block integration contract failed: social links editor depends on the shortcode callback.\n";
	exit( 1 );
}

echo "Block integration contract passed.\n";
