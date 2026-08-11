#!/usr/bin/env php
<?php

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
}

$settings_page = implode(
	'',
	file( __DIR__ . '/../src/Presentation/Admin/SettingsPageController.php' )
);
$settings_assets = implode(
	'',
	file( __DIR__ . '/../src/Presentation/Admin/SettingsAssetEnqueuer.php' )
);
$plugin_bootstrap = implode(
	'',
	file( __DIR__ . '/../html-social-share.php' )
);

if ( ! preg_match( "/add_submenu_page\\(\\s*'options-general\\.php'/", $settings_page ) ) {
	echo "Settings submenu contract failed.\n";
	exit( 1 );
}

if ( false === strpos( $plugin_bootstrap, 'PluginFactory' ) || false === strpos( $plugin_bootstrap, 'LegacyApiRegistrar::register' ) ) {
	echo "Canonical plugin bootstrap contract failed.\n";
	exit( 1 );
}

if (
	false === strpos( $settings_assets, "'/assets/admin.css'" ) ||
	false === strpos( $settings_assets, 'filemtime( $adminStylePath )' )
) {
	echo "Settings stylesheet cache version contract failed.\n";
	exit( 1 );
}

echo "Settings menu contract passed.\n";
