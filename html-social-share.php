<?php
/*
Plugin Name: Html Social share buttons
Plugin URI: http://wordpress.org/plugins/html-social-share-buttons/
Description: Lightweight HTML and CSS social share buttons. Settings and block editing use WordPress JavaScript.
Author: Alimuzzaman Alim
Version: 2.2.6
Author URI: https://alim.dev
Text Domain: html-social-share-buttons
Domain Path: /languages
Requires at least: 5.3
Requires PHP: 7.0
License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$hssb_autoload = __DIR__ . '/vendor/autoload.php';
if ( is_readable( $hssb_autoload ) ) {
	require_once $hssb_autoload;
}
unset( $hssb_autoload );

if ( class_exists( '\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime' ) ) {
	\Alimuzzaman\HtmlSocialShareButtons\Compatibility\Legacy\Bootstrap\LegacyRuntime::boot( __DIR__ );
	require_once __DIR__ . '/src/Compatibility/Legacy/Global/bootstrap.php';
}
