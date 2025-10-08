<?php
/**
 * PHPUnit bootstrap file for HTML Social Share Buttons
 */

// Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// WordPress tests directory
$_tests_dir = getenv('WP_TESTS_DIR');

if (!$_tests_dir) {
    $_tests_dir = rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';
}

// Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file
if (!defined('WP_TESTS_PHPUNIT_POLYFILLS_PATH')) {
    define(
        'WP_TESTS_PHPUNIT_POLYFILLS_PATH',
        dirname(__DIR__) . '/vendor/yoast/phpunit-polyfills'
    );
}

// Give access to tests_add_filter()
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested
 */
function _manually_load_plugin() {
    // Define plugin constants
    if (!defined('HTML_SOCIAL_SHARE_VERSION')) {
        define('HTML_SOCIAL_SHARE_VERSION', '3.0.0');
    }
    
    if (!defined('HTML_SOCIAL_SHARE_DIR')) {
        define('HTML_SOCIAL_SHARE_DIR', dirname(__DIR__) . '/');
    }
    
    if (!defined('HTML_SOCIAL_SHARE_URL')) {
        define('HTML_SOCIAL_SHARE_URL', 'http://localhost/wp-content/plugins/html-social-share-buttons/');
    }
    
    if (!defined('HTML_SOCIAL_SHARE_ICONSET_URL')) {
        define('HTML_SOCIAL_SHARE_ICONSET_URL', HTML_SOCIAL_SHARE_URL . 'assets/iconset/');
    }
    
    if (!defined('HTML_SOCIAL_SHARE_BUILD_URL')) {
        define('HTML_SOCIAL_SHARE_BUILD_URL', HTML_SOCIAL_SHARE_URL . 'build/');
    }
    
    if (!defined('HTML_SOCIAL_SHARE_ASSETS_URL')) {
        define('HTML_SOCIAL_SHARE_ASSETS_URL', HTML_SOCIAL_SHARE_URL . 'assets/');
    }
    
    require dirname(__DIR__) . '/html-social-share.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment
require $_tests_dir . '/includes/bootstrap.php';

// Load test fixtures
require __DIR__ . '/fixtures/expected-output.php';
