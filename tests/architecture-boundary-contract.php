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
	'Application'    => array( 'Infrastructure\\', 'Presentation\\', 'Bootstrap\\', 'Compatibility\\' ),
	'Infrastructure' => array( 'Bootstrap\\', 'Compatibility\\' ),
	'Bootstrap'      => array( 'Compatibility\\' ),
);
$settingsStorageOwners = array(
	$sourceDir . '/Infrastructure/WordPress/Settings/OptionSettingsRepository.php',
	$sourceDir . '/Infrastructure/WordPress/Migration/WordPressMigrationStateStore.php',
);
$legacyIdentifierOwners = array(
	$sourceDir . '/Bootstrap/PluginConfig.php',
);
$requiredRenderingFiles = array(
	$sourceDir . '/Presentation/Rendering/RenderFacade.php',
	$sourceDir . '/Presentation/Rendering/RenderOutcome.php',
	$sourceDir . '/Presentation/Rendering/RenderRequestMapper.php',
	$sourceDir . '/Infrastructure/WordPress/Rendering/HookedShareUrlResolver.php',
);
$forbiddenApplicationRenderingFiles = array(
	$sourceDir . '/Application/Rendering/RenderFacade.php',
	$sourceDir . '/Application/Rendering/RenderOutcome.php',
	$sourceDir . '/Application/Rendering/RenderRequestMapper.php',
	$sourceDir . '/Application/Rendering/HookedShareUrlResolver.php',
);

foreach ( $requiredRenderingFiles as $path ) {
	if ( ! is_file( $path ) ) {
		$failures[] = str_replace( $root . '/', '', $path ) . ': canonical rendering file is missing';
	}
}
foreach ( $forbiddenApplicationRenderingFiles as $path ) {
	if ( is_file( $path ) ) {
		$failures[] = str_replace( $root . '/', '', $path ) . ': presentation/infrastructure rendering leaked into Application';
	}
}

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
		if (
			false !== stripos( $contents, $token ) &&
			! in_array( $path, $legacyIdentifierOwners, true )
		) {
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

$entrypoint = (string) file_get_contents( $root . '/html-social-share.php' );
foreach ( array( 'PluginFactory', '$hssb_plugin->boot()', 'LegacyApiRegistrar::register' ) as $required ) {
	if ( false === strpos( $entrypoint, $required ) ) {
		$failures[] = 'html-social-share.php: missing canonical bootstrap step ' . $required;
	}
}
foreach ( array( 'LegacyRuntime', 'Compatibility/Legacy/Global', 'Compatibility/Legacy/Bootstrap' ) as $forbiddenEntrypoint ) {
	if ( false !== strpos( $entrypoint, $forbiddenEntrypoint ) ) {
		$failures[] = 'html-social-share.php: obsolete compatibility bootstrap dependency ' . $forbiddenEntrypoint;
	}
}
if ( false !== strpos( $entrypoint, 'LegacyApiRegistrar::register' ) &&
	strrpos( $entrypoint, '$hssb_plugin->boot()' ) > strrpos( $entrypoint, 'LegacyApiRegistrar::register' )
) {
	$failures[] = 'html-social-share.php: canonical kernel must boot before legacy runtime aliases register';
}

$obsoleteRootFiles = array(
	'actions.php', 'block-integration.php', 'elementor-integration.php', 'filters.php',
	'form.php', 'function.php', 'iconsets.php', 'interfaces.php', 'metabox.php',
	'schemas.php', 'settings_page.php', 'share-templates.php', 'shortcode.php',
	'vc-integration.php', 'widget.php',
);
foreach ( $obsoleteRootFiles as $obsoleteRootFile ) {
	if ( is_file( $root . '/' . $obsoleteRootFile ) ) {
		$failures[] = 'obsolete root forwarding file remains: ' . $obsoleteRootFile;
	}
}

$legacyRoot = $sourceDir . '/Compatibility/Legacy';
if ( is_dir( $legacyRoot ) ) {
	$allowedLegacyDirectories = array( 'Api', 'Settings' );
	foreach ( new DirectoryIterator( $legacyRoot ) as $legacyEntry ) {
		if ( $legacyEntry->isDot() ) {
			continue;
		}
		if ( ! $legacyEntry->isDir() || ! in_array( $legacyEntry->getFilename(), $allowedLegacyDirectories, true ) ) {
			$failures[] = 'unsupported legacy implementation remains: ' .
				str_replace( $root . '/', '', $legacyEntry->getPathname() );
		}
	}
}

$presentationRoot = $sourceDir . '/Presentation';
if ( is_dir( $presentationRoot ) ) {
	$presentationIterator = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator( $presentationRoot, FilesystemIterator::SKIP_DOTS )
	);
	foreach ( $presentationIterator as $presentationFile ) {
		if ( 'php' !== strtolower( $presentationFile->getExtension() ) ) {
			continue;
		}
		$presentationSource = (string) file_get_contents( $presentationFile->getPathname() );
		foreach ( array( 'Compatibility\\Legacy', 'LegacyRuntime', 'LegacyRenderFacade', 'LegacyHtmlRenderer', 'zm_sh_shortcode_cb', 'global $', '$GLOBALS' ) as $forbiddenPresentationToken ) {
			if ( false !== strpos( $presentationSource, $forbiddenPresentationToken ) ) {
				$failures[] = str_replace( $root . '/', '', $presentationFile->getPathname() ) .
					': presentation depends on legacy runtime token ' . $forbiddenPresentationToken;
			}
		}
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
