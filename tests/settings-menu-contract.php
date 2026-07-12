#!/usr/bin/env php
<?php

$settings_page = file_get_contents( __DIR__ . '/../settings_page.php' );
$plugin_bootstrap = file_get_contents( __DIR__ . '/../html-social-share.php' );

if ( false === strpos( $settings_page, "add_submenu_page('options-general.php'" ) ) {
	echo "Settings submenu contract failed.\n";
	exit( 1 );
}

if ( false === strpos( $plugin_bootstrap, 'options-general.php?page=zm_shbt_opt' ) ) {
	echo "Plugins page settings link contract failed.\n";
	exit( 1 );
}

if ( false === strpos( $settings_page, "__DIR__ . '/assets/admin.css'" ) || false === strpos( $settings_page, 'filemtime( $admin_style_path )' ) ) {
	echo "Settings stylesheet cache version contract failed.\n";
	exit( 1 );
}

echo "Settings menu contract passed.\n";
