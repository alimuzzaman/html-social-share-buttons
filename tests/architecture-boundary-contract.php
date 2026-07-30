#!/usr/bin/env php
<?php

$root      = dirname( __DIR__ );
$sourceDir = $root . '/src';
$forbidden = array(
	'zm_sh',
	'zm-sh',
);
$failures  = array();
$layerDependencies = array(
	'Domain'         => array( 'Application\\', 'Infrastructure\\', 'Bootstrap\\', 'Compatibility\\' ),
	'Application'    => array( 'Infrastructure\\', 'Bootstrap\\', 'Compatibility\\' ),
	'Infrastructure' => array( 'Bootstrap\\', 'Compatibility\\' ),
	'Bootstrap'      => array( 'Compatibility\\' ),
);
$settingsStorageOwners = array(
	$sourceDir . '/Infrastructure/WordPress/Settings/OptionSettingsRepository.php',
	$sourceDir . '/Infrastructure/WordPress/Migration/WordPressMigrationStateStore.php',
);

$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator( $sourceDir, FilesystemIterator::SKIP_DOTS )
);

foreach ( $iterator as $file ) {
	if ( 'php' !== strtolower( $file->getExtension() ) ) {
		continue;
	}

	$path = $file->getPathname();
	if ( false !== strpos( $path, DIRECTORY_SEPARATOR . 'Compatibility' . DIRECTORY_SEPARATOR . 'Legacy' . DIRECTORY_SEPARATOR ) ) {
		continue;
	}

	$contents = (string) file_get_contents( $path );
	foreach ( $forbidden as $token ) {
		if ( false !== stripos( $contents, $token ) ) {
			$failures[] = str_replace( $root . '/', '', $path ) . ': legacy token ' . $token;
		}
	}

	$relative = str_replace( $sourceDir . DIRECTORY_SEPARATOR, '', $path );
	$layer = strtok( $relative, DIRECTORY_SEPARATOR );
	if ( isset( $layerDependencies[ $layer ] ) ) {
		foreach ( $layerDependencies[ $layer ] as $forbiddenLayer ) {
			if (
				false !== strpos(
					$contents,
					'Alimuzzaman\\HtmlSocialShareButtons\\' . $forbiddenLayer
				)
			) {
				$failures[] = str_replace( $root . '/', '', $path ) .
					': invalid dependency on ' . rtrim( $forbiddenLayer, '\\' );
			}
		}
	}

	if (
		preg_match( '/\\b(?:get_option|update_option|add_option|delete_option)\\s*\\(/', $contents ) &&
		! in_array( $path, $settingsStorageOwners, true )
	) {
		$failures[] = str_replace( $root . '/', '', $path ) .
			': direct option storage access outside an approved repository';
	}

	if (
		( 'Domain' === $layer || 'Application' === $layer ) &&
		preg_match( '/\\b(?:add_action|add_filter|do_action|apply_filters)\\s*\\(/', $contents )
	) {
		$failures[] = str_replace( $root . '/', '', $path ) .
			': WordPress hooks are not allowed in the domain/application layers';
	}
}

if ( $failures ) {
	echo "New-core compatibility boundary failed:\n";
	foreach ( $failures as $failure ) {
		echo ' - ' . $failure . "\n";
	}
	exit( 1 );
}

echo "New-core compatibility boundary passed.\n";
