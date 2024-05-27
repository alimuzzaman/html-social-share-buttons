<?php
/*
Plugin Name: Html Social share buttons
Plugin URI: http://wordpress.org/plugins/html-social-share-buttons/
Description: Html share button. It show lite share button only with html. It's not using any javascript whats another do. It's load only extra 10-11 kb total on your site.
Author: Alimuzzaman Alim
Version: 3.0.0
Author URI: https://alim.dev
Text Domain: zm-sh
Domain Path: /languages
*/

// Prevent direct file access
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Include the main plugin class file
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Define plugin constants
define( 'HSSB_VERSION', '3.0.0' );
define( 'HSSB_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'HSSB_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'HSSB_ASSETS_URL', HSSB_PLUGIN_URL . 'assets/' );
define( 'HSSB_ICONS_URL',  HSSB_ASSETS_URL . 'icons/' );

// Initialize the plugin
$hssb_plugin_manager = new HSSB\PluginManager();
$hssb_plugin_manager->init();