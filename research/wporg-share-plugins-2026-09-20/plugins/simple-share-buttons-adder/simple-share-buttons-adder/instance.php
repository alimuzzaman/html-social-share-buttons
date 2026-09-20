<?php
/**
 * Instantiates the Simple Share Buttons Adder plugin
 *
 * @package SimpleShareButtonsAdder
 */

namespace SimpleShareButtonsAdder;

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'SSBA_FILE', __FILE__ );
define( 'SSBA_ROOT', trailingslashit( dirname( __FILE__ ) ) );
define( 'SSBA_VERSION', '8.7.0' );
define( 'ASSET_PREFIX', strtolower( preg_replace( '/\B([A-Z])/', '-$1', __NAMESPACE__ ) ) );
define( 'META_PREFIX', strtolower( preg_replace( '/\B([A-Z])/', '_$1', __NAMESPACE__ ) ) );
define( 'DIR_PATH', dirname( SSBA_FILE ) . '/' );

global $simple_share_buttons_adder_plugin;

require_once __DIR__ . '/php/class-plugin-base.php';
require_once __DIR__ . '/php/class-plugin.php';

$simple_share_buttons_adder_plugin = new Plugin();

/**
 * Simple Share Buttons Adder Plugin Instance
 *
 * @return Plugin
 */
function get_plugin_instance() {
	global $simple_share_buttons_adder_plugin;
	return $simple_share_buttons_adder_plugin;
}
