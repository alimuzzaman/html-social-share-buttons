<?php
/**
 * Plugin Name: HTML Social Share Buttons
 * Plugin URI: https://yourwebsite.com/html-social-share-buttons
 * Description: This is a WordPress plugin that provides social share buttons.
 * Version: 3.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL2
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