<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package Html_Social_Share_Buttons
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
$_phpunit_polyfills_path = getenv( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH' );
if ( false !== $_phpunit_polyfills_path ) {
	define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_phpunit_polyfills_path );
}

// Check if WordPress test environment is available
if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
	echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	
	// Try to set up a minimal test environment for utility testing
	if ( ! function_exists( 'tests_add_filter' ) ) {
		/**
		 * Mock tests_add_filter function for standalone utility testing
		 */
		function tests_add_filter( $hook, $callback ) {
			// For utility testing, we don't need WordPress hooks
			return true;
		}
	}
} else {
	// Give access to tests_add_filter() function.
	require_once "{$_tests_dir}/includes/functions.php";
}

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	// Load the main plugin file
	$plugin_file = dirname( dirname( __FILE__ ) ) . '/html-social-share.php';
	if ( file_exists( $plugin_file ) ) {
		require $plugin_file;
	}
	
	// Also load the bootstrap for utility classes
	$bootstrap_file = dirname( dirname( __FILE__ ) ) . '/src/bootstrap.php';
	if ( file_exists( $bootstrap_file ) ) {
		require_once $bootstrap_file;
	}
}

// Add the plugin loading hook
if ( function_exists( 'tests_add_filter' ) ) {
	tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );
} else {
	// For standalone testing, load immediately
	_manually_load_plugin();
}

// Start up the WP testing environment if available.
if ( file_exists( "{$_tests_dir}/includes/bootstrap.php" ) ) {
	require "{$_tests_dir}/includes/bootstrap.php";
} else {
	// For standalone utility testing, define minimal WordPress constants
	if ( ! defined( 'ABSPATH' ) ) {
		define( 'ABSPATH', dirname( dirname( __FILE__ ) ) . '/' );
	}
	if ( ! defined( 'WP_DEBUG' ) ) {
		define( 'WP_DEBUG', true );
	}
}
