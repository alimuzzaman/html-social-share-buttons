#!/usr/bin/env php
<?php

$root = dirname( __DIR__ );
$productionPhp = array(
	$root . '/src/Compatibility/Legacy/Global/settings-page.php',
	$root . '/src/Compatibility/Legacy/Admin/LegacySettingsAssetEnqueuer.php',
	$root . '/src/Compatibility/Legacy/Integration/BlockAdapter.php',
	$root . '/src/Compatibility/Legacy/Integration/WpBakeryAdapter.php',
);
$requiredBuildFiles = array(
	'build/admin-react.js',
	'build/admin-react.asset.php',
	'build/social-share.js',
	'build/social-share.asset.php',
	'build/vc-scripts.js',
	'build/vc-scripts.asset.php',
);
$failures = array();

foreach ( $requiredBuildFiles as $relativePath ) {
	if ( ! is_file( $root . '/' . $relativePath ) ) {
		$failures[] = 'Missing compiled runtime asset: ' . $relativePath;
	}
}

$settingsRuntime = implode(
	"\n",
	array_map(
		function ( $path ) {
			return (string) file_get_contents( $path );
		},
		array_slice( $productionPhp, 0, 2 )
	)
);
$blockIntegration = (string) file_get_contents( $root . '/src/Compatibility/Legacy/Integration/BlockAdapter.php' );
$vcIntegration = (string) file_get_contents(
	$root . '/src/Compatibility/Legacy/Admin/LegacySettingsAssetEnqueuer.php'
);
$package = (string) file_get_contents( $root . '/package.json' );
$distIgnore = (string) file_get_contents( $root . '/.distignore' );

foreach (
	array(
		'build/admin-react.js',
		'build/vc-scripts.js',
	) as $requiredPath
) {
	if ( false === strpos( $settingsRuntime, $requiredPath ) ) {
		$failures[] = 'Settings runtime does not enqueue ' . $requiredPath;
	}
}

if ( false === strpos( $blockIntegration, 'build/social-share.js' ) ) {
	$failures[] = 'Block runtime does not enqueue build/social-share.js';
}

foreach ( array( 'jquery', 'wp-components', 'wp-element' ) as $dependency ) {
	if ( false === strpos( $settingsRuntime, "'$dependency'" ) ) {
		$failures[] = 'Settings runtime is missing required dependency: ' . $dependency;
	}
}

foreach ( array( 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-block-editor', 'wp-components' ) as $dependency ) {
	if ( false === strpos( $blockIntegration, "'$dependency'" ) ) {
		$failures[] = 'Block runtime is missing required dependency: ' . $dependency;
	}
}

if ( false === strpos( $vcIntegration, 'build/vc-scripts.js' ) ) {
	$failures[] = 'WPBakery runtime does not enqueue build/vc-scripts.js';
}

foreach ( $productionPhp as $path ) {
	$contents = (string) file_get_contents( $path );
	if ( preg_match( "#(?:assets|src)/[^'\"\\s]+\\.js#", $contents, $matches ) ) {
		$failures[] = basename( $path ) . ' loads JavaScript source at runtime: ' . $matches[0];
	}
}

foreach (
	array(
		'src/js/admin-react.js',
		'src/js/social-share.js',
		'src/js/vc-scripts.js',
	) as $entry
) {
	if ( false === strpos( $package, $entry ) ) {
		$failures[] = 'Build script is missing entry: ' . $entry;
	}
}

if ( false === strpos( $distIgnore, "src/js/\n" ) ) {
	$failures[] = 'Distribution must exclude build-time JavaScript source.';
}

$moduleCount = 0;
$legacyTokens = array(
	'zm_sh',
	'zm-sh',
	'zmSh',
	'html-social-share/social-share',
	'twitter',
	'long_shadow',
);
$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $root . '/src/js', FilesystemIterator::SKIP_DOTS )
);
foreach ( $iterator as $file ) {
	if ( 'js' !== strtolower( $file->getExtension() ) ) {
		continue;
	}
	$moduleCount++;
	$contents = (string) file_get_contents( $file->getPathname() );
	if (
		false === strpos(
			$file->getPathname(),
			DIRECTORY_SEPARATOR . 'compatibility' . DIRECTORY_SEPARATOR . 'legacy' . DIRECTORY_SEPARATOR
		)
	) {
		foreach ( $legacyTokens as $legacyToken ) {
			if ( false !== stripos( $contents, $legacyToken ) ) {
				$failures[] = 'New JavaScript contains a legacy token: ' .
					str_replace( $root . '/', '', $file->getPathname() ) . ' (' . $legacyToken . ')';
			}
		}
	}
	if (
		false !== strpos( $contents, 'import ' ) ||
		false !== strpos( $contents, 'export ' )
	) {
		continue;
	}

	// Leaf modules may expose behavior only through a bundled side effect.
	if ( false === strpos( $file->getPathname(), DIRECTORY_SEPARATOR . 'block' . DIRECTORY_SEPARATOR ) &&
		false === strpos( $file->getPathname(), DIRECTORY_SEPARATOR . 'wpbakery' . DIRECTORY_SEPARATOR ) ) {
		$failures[] = 'JavaScript source is not a build-time module: ' . str_replace( $root . '/', '', $file->getPathname() );
	}
}

if ( $moduleCount < 8 ) {
	$failures[] = 'Expected modular JavaScript sources; found only ' . $moduleCount . ' module(s).';
}

if ( $failures ) {
	echo "JavaScript build boundary failed:\n";
	foreach ( $failures as $failure ) {
		echo ' - ' . $failure . "\n";
	}
	exit( 1 );
}

printf( "JavaScript build boundary passed: %d source modules, 3 runtime bundles.\n", $moduleCount );
