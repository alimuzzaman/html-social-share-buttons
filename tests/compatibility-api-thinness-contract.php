<?php

/**
 * Static guard for the narrow legacy boundary. Compatibility may expose old
 * names and storage shapes, but it must never become a second runtime.
 */

$root = dirname( __DIR__ );
$files = array(
	$root . '/src/Compatibility/Legacy/Api/LegacyApi.php',
	$root . '/src/Compatibility/Legacy/Api/LegacyApiRegistrar.php',
	$root . '/src/Compatibility/Legacy/Api/LegacyConstants.php',
	$root . '/src/Compatibility/Legacy/Api/LegacyHooks.php',
	$root . '/src/Compatibility/Legacy/Api/LegacyOptionCodec.php',
	$root . '/src/Compatibility/Legacy/Api/LegacyIconSetAdapter.php',
	$root . '/src/Compatibility/Legacy/Api/LegacyElementorWidgetFactory.php',
	$root . '/src/Compatibility/Legacy/Api/LegacySchemaRegistry.php',
	$root . '/src/Compatibility/Legacy/Api/globals.php',
);
$forbidden = array(
	'LegacyRuntime',
	'PluginFactory',
	'LegacyRenderFacade',
	'LegacyHtmlRenderer',
	'add_action(',
	'add_filter(',
	'add_shortcode(',
	'register_block_type(',
	'wp_enqueue_',
);

foreach ( $files as $file ) {
	if ( ! is_file( $file ) ) {
		fwrite( STDERR, "Missing compatibility API file: {$file}\n" );
		exit( 1 );
	}
	$source = (string) file_get_contents( $file );
	$needles = $forbidden;
	if ( false !== strpos( $file, '/LegacyHooks.php' ) ) {
		/* This one bridge may subscribe to canonical extension filters only. */
		$needles = array_diff( $needles, array( 'add_filter(' ) );
	}
	foreach ( $needles as $needle ) {
		if ( false !== strpos( $source, $needle ) ) {
			fwrite( STDERR, "Compatibility API must not own runtime work ({$needle}) in {$file}.\n" );
			exit( 1 );
		}
	}
}

$legacyRoot = $root . '/src/Compatibility/Legacy';
$allowedDirectories = array( 'Api', 'Settings' );
foreach ( new DirectoryIterator( $legacyRoot ) as $entry ) {
	if ( $entry->isDot() ) {
		continue;
	}
	if ( ! $entry->isDir() || ! in_array( $entry->getFilename(), $allowedDirectories, true ) ) {
		fwrite( STDERR, 'Compatibility contains non-boundary implementation: ' . $entry->getPathname() . "\n" );
		exit( 1 );
	}
}

$hooks = (string) file_get_contents( $root . '/src/Compatibility/Legacy/Api/LegacyHooks.php' );
foreach ( array( 'ExtensionHooks::SHARE_TEMPLATES', 'ExtensionHooks::SHARE_TEMPLATE', 'ExtensionHooks::SHARE_TITLE', 'ExtensionHooks::SHARE_URL' ) as $hook ) {
	if ( false === strpos( $hooks, $hook ) ) {
		fwrite( STDERR, "Legacy extension hook bridge is missing {$hook}.\n" );
		exit( 1 );
	}
}

$globals = (string) file_get_contents( $root . '/src/Compatibility/Legacy/Api/globals.php' );
foreach ( array( 'function zm_sh_btn( $options )', 'function zm_sh_shortcode_cb( $attributes )', 'class zm_social_share', 'class zm_sh_iconset', 'interface interface_iconset' ) as $surface ) {
	if ( false === strpos( $globals, $surface ) ) {
		fwrite( STDERR, "Missing legacy API surface: {$surface}\n" );
		exit( 1 );
	}
}

foreach ( array( 'get_option(', 'update_option(', 'wp_enqueue_', '<div', '<a ', 'new PluginFactory', 'new Plugin' ) as $token ) {
	if ( false !== strpos( $globals, $token ) ) {
		fwrite( STDERR, 'Legacy globals own implementation token: ' . $token . "\n" );
		exit( 1 );
	}
}

$registrar = (string) file_get_contents( $root . '/src/Compatibility/Legacy/Api/LegacyApiRegistrar.php' );
foreach ( array( 'LegacyApi::register( $plugin )', 'require_once __DIR__ . \'/globals.php\';' ) as $required ) {
	if ( false === strpos( $registrar, $required ) ) {
		fwrite( STDERR, 'Legacy registrar is missing boundary step: ' . $required . "\n" );
		exit( 1 );
	}
}
foreach ( array( 'PluginFactory', '->boot(', 'LegacyRuntime' ) as $token ) {
	if ( false !== strpos( $registrar, $token ) ) {
		fwrite( STDERR, 'Legacy registrar owns canonical boot work: ' . $token . "\n" );
		exit( 1 );
	}
}

$constants = (string) file_get_contents( $root . '/src/Compatibility/Legacy/Api/LegacyConstants.php' );
foreach ( array( 'HSSB_PLUGIN_DIR', 'HSSB_PLUGIN_URL', 'self::alias' ) as $required ) {
	if ( false === strpos( $constants, $required ) ) {
		fwrite( STDERR, 'Legacy constants do not alias canonical values: ' . $required . "\n" );
		exit( 1 );
	}
}

echo "Compatibility API thinness contract passed.\n";
