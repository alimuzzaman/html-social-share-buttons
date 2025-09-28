<?php
/*
Plugin Name: HTML Social Share Buttons
Plugin URI: https://github.com/alimuzzaman/html-social-share-buttons
Description: Modern, lightweight social share buttons for WordPress with PSR-4 architecture
Author: Alimuzzaman Alim
Version: 3.0.0
Author URI: https://alim.dev
Text Domain: html-social-share
Domain Path: /languages
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('HTML_SOCIAL_SHARE_PLUGIN_FILE', __FILE__);
define('HTML_SOCIAL_SHARE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('HTML_SOCIAL_SHARE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Additional helper constants for assets and URLs
define('HTML_SOCIAL_SHARE_ASSETS_DIR', HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/');
define('HTML_SOCIAL_SHARE_ASSETS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/');
define('HTML_SOCIAL_SHARE_ICONSET_DIR', HTML_SOCIAL_SHARE_PLUGIN_DIR . 'assets/iconset/');
define('HTML_SOCIAL_SHARE_ICONSET_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/iconset/');
define('HTML_SOCIAL_SHARE_ADMIN_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/admin/');
define('HTML_SOCIAL_SHARE_CSS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/css/');
define('HTML_SOCIAL_SHARE_JS_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/js/');
define('HTML_SOCIAL_SHARE_IMAGES_URL', HTML_SOCIAL_SHARE_PLUGIN_URL . 'assets/images/');

// Ensure composer autoloader is loaded from the main plugin directory.
$autoload = __DIR__ . '/vendor/autoload.php';
if (! file_exists($autoload)) {
    html_social_share_register_autoload_notice($autoload);
    return;
}

require_once $autoload;

// Ensure our Bootstrap class can be autoloaded
if (! class_exists('\HtmlSocialShare\\Bootstrap')) {
    html_social_share_register_missing_classes_notice();
    return;
}

/**
 * Get the plugin service container.
 *
 * Lazily instantiates the Bootstrap class and returns a shared Container instance.
 * Assumes autoload and classes are already available (verified above).
 *
 * @return \HtmlSocialShare\Container
 */
function html_social_share_get_container(): \HtmlSocialShare\Container
{
    static $container = null;

    if ($container === null) {
        $bootstrap = new \HtmlSocialShare\Bootstrap(HTML_SOCIAL_SHARE_PLUGIN_FILE);
        $container = $bootstrap->getContainer();
    }
    return $container;
}

/**
 * Register a clear admin notice explaining how to install Composer deps when
 * vendor/autoload.php is missing. Separated into a function for testability.
 *
 * @param string $autoloadPath
 * @return void
 */
function html_social_share_register_autoload_notice(string $autoloadPath): void
{
    add_action('admin_notices', function() use ($autoloadPath) {
        $pluginDir = esc_html(HTML_SOCIAL_SHARE_PLUGIN_DIR);

        // Provide OS-aware instructions
        $os = PHP_OS_FAMILY ?? php_uname('s');
        if (strtoupper(substr($os, 0, 7)) === 'WINDOWS') {
            $instruction = 'Open PowerShell in the plugin directory and run: <code>composer install</code>';
        } else {
            $instruction = 'Open a terminal in the plugin directory and run: <code>composer install</code>';
        }

        echo '<div class="notice notice-error"><p><strong>HTML Social Share Buttons:</strong> Composer autoloader not found at <code>' . esc_html($autoloadPath) . '</code>. ' . $instruction . ' (<code>' . $pluginDir . '</code>).</p></div>';
    });

    if (function_exists('error_log')) {
        error_log('HTML Social Share: vendor/autoload.php not found at ' . $autoloadPath . '. Run composer install.');
    }
}

/**
 * Register a clear admin notice when classes cannot be autoloaded despite
 * the autoloader being present.
 *
 * @return void
 */
function html_social_share_register_missing_classes_notice(): void
{
    add_action('admin_notices', function() {
        $pluginDir = esc_html(HTML_SOCIAL_SHARE_PLUGIN_DIR);
        echo '<div class="notice notice-error"><p><strong>HTML Social Share Buttons:</strong> The autoloader exists but plugin classes could not be found. Try running <code>composer dump-autoload</code> or verify plugin files are intact in <code>' . $pluginDir . '</code>.</p></div>';
    });

    if (function_exists('error_log')) {
        error_log('HTML Social Share: Autoloader present but classes missing. Run composer dump-autoload.');
    }
}

// Ensure Bootstrap is instantiated (do not keep a local reference)
html_social_share_get_container();

// Bootstrap performs initialization and hook wiring; container is available