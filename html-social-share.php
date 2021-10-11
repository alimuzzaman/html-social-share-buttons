<?php
/*
Plugin Name: Html Social share buttons
Plugin URI: http://wordpress.org/plugins/html-social-share-buttons/
Description: Html share button. It show lite share button only with html. It's not using any javascript whats anothers do. It's load only extra 10-11 kb total on your site.
Author: Alimuzzaman Alim
Version: 3.0.0
Author URI: https://alim.dev
Text Domain: zm-sh
Domain Path: /languages
*/

// Iconset dir where to search for iconsets.
define("hssb_dir", plugin_dir_path(__FILE__));
//define("hssb_url_iconset", hssb_dir . "iconset");

define("hssb_url", plugin_dir_url(__FILE__));
define("hssb_url_iconset", hssb_url . "iconset/");
define("hssb_url_assets", hssb_url . "assets/");
define("hssb_url_assets_img", hssb_url_assets . "image/");

$loader = require __DIR__ . '/vendor/autoload.php';

new HSSB\HSSB();

