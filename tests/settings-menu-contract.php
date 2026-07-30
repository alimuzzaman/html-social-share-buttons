#!/usr/bin/env php
<?php

if ( ! defined( 'ABSPATH' ) ) {
	if ( 'cli' !== PHP_SAPI ) {
		exit;
	}
}

$settings_page = implode(
	'',
	file( __DIR__ . '/../src/Compatibility/Legacy/Global/settings-page.php' )
);
$settings_assets = implode(
	'',
	file( __DIR__ . '/../src/Compatibility/Legacy/Admin/LegacySettingsAssetEnqueuer.php' )
);
$plugin_bootstrap = implode(
	'',
	file( __DIR__ . '/../src/Compatibility/Legacy/Global/bootstrap.php' )
);

if ( ! preg_match( "/add_submenu_page\\(\\s*'options-general\\.php'/", $settings_page ) ) {
	echo "Settings submenu contract failed.\n";
	exit( 1 );
}

if ( false === strpos( $plugin_bootstrap, 'options-general.php?page=zm_shbt_opt' ) ) {
	echo "Plugins page settings link contract failed.\n";
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
